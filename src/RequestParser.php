<?php

/**
 * RequestParser Class
 * 
 * This class handles reading and parsing request data
 * based on Content-Type.
 * 
 * @author Stefan Witt <stefan.witt@rathje-design.de>
 */
class RequestParser
{
    private $logger;
    private $requestMethod;
    private $requestedUrl;
    private $queryString;
    private $contentType;
    private $requestBody;
    private $headers;
    private $parsedData = [];

    /**
     * Constructor
     * 
     * @param Logger $logger The logger for output
     */
    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
        $this->requestMethod = $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN';
        $this->requestedUrl = $_SERVER['REQUEST_URI'] ?? '';
        $this->queryString = $_SERVER['QUERY_STRING'] ?? '';
        $this->headers = $this->getHeaders();
        $this->contentType = $this->headers['Content-Type'] ?? '';
        $this->requestBody = file_get_contents('php://input');
    }

    /**
     * Gets all HTTP headers
     * 
     * @return array The HTTP headers
     */
    private function getHeaders()
    {
        if (function_exists('getallheaders')) {
            return getallheaders();
        }

        // Fallback for servers without getallheaders() function
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            } else if ($name == 'CONTENT_TYPE') {
                $headers['Content-Type'] = $value;
            } else if ($name == 'CONTENT_LENGTH') {
                $headers['Content-Length'] = $value;
            }
        }
        return $headers;
    }

    /**
     * Logs HTTP request information
     */
    public function logHttpRequest()
    {
        $this->logger->info("HTTP Request Method: {$this->requestMethod}");
        $this->logger->info("Requested URL: {$this->requestedUrl}");
        $this->logger->info("Query String: {$this->queryString}");
        $this->logger->info("Content-Type: {$this->contentType}");

        // Log all headers for detailed analysis
        $this->logger->info("=== ALL HTTP HEADERS ===");
        foreach ($this->headers as $header => $value) {
            $this->logger->info("Header - $header: $value");
        }
        $this->logger->info("=== END HEADERS ===");

        $this->logger->info("Request Body Length: " . strlen($this->requestBody) . " bytes");
        $this->logger->info("Request Body (raw): " . $this->requestBody);

        // Log GET parameters
        if (!empty($_GET)) {
            $this->logger->info("GET Parameters: " . json_encode($_GET, JSON_PRETTY_PRINT));
        }

        // Log POST data
        if (!empty($_POST)) {
            $this->logger->info("POST Data: " . json_encode($_POST, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } else {
            $this->logger->info("POST Data: (empty)");
        }

        // Log any additional server variables that might be useful
        $serverVars = [
            'HTTP_HOST',
            'SERVER_NAME',
            'REQUEST_TIME',
            'REQUEST_TIME_FLOAT',
            'HTTP_ACCEPT',
            'HTTP_ACCEPT_LANGUAGE',
            'HTTP_ACCEPT_ENCODING',
            'HTTP_CONNECTION',
            'HTTP_CACHE_CONTROL'
        ];

        $this->logger->info("=== ADDITIONAL SERVER INFO ===");
        foreach ($serverVars as $var) {
            if (isset($_SERVER[$var])) {
                $this->logger->info("$var: " . $_SERVER[$var]);
            }
        }
        $this->logger->info("=== END SERVER INFO ===");
    }

    /**
     * Parses request data based on Content-Type
     * 
     * @return array The parsed data
     */
    public function parseRequestData()
    {
        // First use POST data directly
        $this->parsedData = $_POST;

        // If POST data is empty or Content-Type is not form-urlencoded,
        // try to extract data from the request body
        if (empty($this->parsedData) || strpos($this->contentType, 'application/x-www-form-urlencoded') === false) {
            if (strpos($this->contentType, 'application/json') !== false) {
                $this->parseJsonData();
            } elseif (strpos($this->contentType, 'application/x-www-form-urlencoded') !== false) {
                $this->parseFormUrlEncodedData();
            } elseif (strpos($this->contentType, 'multipart/form-data') !== false) {
                $this->parseMultipartFormData();
            } elseif (strpos($this->contentType, 'text/xml') !== false || strpos($this->contentType, 'application/xml') !== false) {
                $this->parseXmlData();
            } else {
                $this->parseUnknownContentType();
            }
        }

        // Log the processed data
        $this->logger->info('Processed request data: ' . json_encode(array_keys($this->parsedData)));

        // If we parsed data from the body and $_POST is empty,
        // add the parsed data to $_POST
        if (empty($_POST) && !empty($this->parsedData)) {
            $_POST = array_merge($_POST, $this->parsedData);
            $this->logger->info("Updated POST data with parsed body: " . json_encode($_POST));
        }

        return $this->parsedData;
    }

    /**
     * Parses JSON data from request body
     */
    private function parseJsonData()
    {
        $jsonData = json_decode($this->requestBody, true);
        if ($jsonData !== null) {
            $this->parsedData = $jsonData;
            $this->logger->info('Parsed JSON data successfully');
        } else {
            $this->logger->error('Failed to parse JSON data: ' . json_last_error_msg());
        }
    }

    /**
     * Parses form URL encoded data from request body
     */
    private function parseFormUrlEncodedData()
    {
        parse_str($this->requestBody, $parsedData);
        if (!empty($parsedData)) {
            $this->parsedData = $parsedData;
            $this->logger->info('Parsed form data successfully');
        }
    }

    /**
     * Handles multipart form data
     */
    private function parseMultipartFormData()
    {
        // Multipart data should already be available in $_POST and $_FILES
        $this->logger->info('Multipart form data detected, using $_POST data');

        // Use the data already available in $_POST
        $this->parsedData = $_POST;

        // Log the available data
        $this->logger->info('Available POST data: ' . json_encode(array_keys($this->parsedData)));
    }

    /**
     * Parses XML data from request body
     */
    private function parseXmlData()
    {
        try {
            $xml = new SimpleXMLElement($this->requestBody);
            $this->parsedData = json_decode(json_encode($xml), true);
            $this->logger->info('Parsed XML data successfully');
        } catch (Exception $e) {
            $this->logger->error('Failed to parse XML data: ' . $e->getMessage());
        }
    }

    /**
     * Attempts to parse unknown content type data
     */
    private function parseUnknownContentType()
    {
        // Try to parse as form data anyway
        parse_str($this->requestBody, $parsedData);
        if (!empty($parsedData)) {
            $this->parsedData = $parsedData;
            $this->logger->info('Attempted to parse unknown content type as form data');
        } else {
            $this->logger->warning('Could not determine how to parse request body with content type: ' . $this->contentType);
        }
    }

    /**
     * Returns the parsed data
     * 
     * @return array The parsed data
     */
    public function getParsedData()
    {
        return $this->parsedData;
    }

    /**
     * Returns the value of a specific parameter
     * 
     * @param string $key The parameter key
     * @param mixed $default The default value if the parameter doesn't exist
     * @return mixed The parameter value or the default value
     */
    public function getParam($key, $default = null)
    {
        return $this->parsedData[$key] ?? $default;
    }

    /**
     * Returns the Content-Type
     * 
     * @return string The Content-Type
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Returns the request body
     * 
     * @return string The request body
     */
    public function getRequestBody()
    {
        return $this->requestBody;
    }

    /**
     * Extracts and logs specific key values from the data
     *
     * @param int $maxLength Maximum length for values (default: 40)
     */
    public function dumpData($maxLength = 40)
    {
        // Log all keys in the data array
        if (is_array($this->parsedData)) {
            $allKeys = array_keys($this->parsedData);
            $this->logger->info('All available keys in data: ' . json_encode($allKeys));

            // Iterate through all keys and log the associated values
            foreach ($this->parsedData as $key => $value) {
                // Convert arrays and objects to JSON strings for better readability
                if (is_array($value) || is_object($value)) {
                    $valueStr = json_encode($value, JSON_UNESCAPED_UNICODE);
                } else {
                    $valueStr = (string)$value;
                }

                // Truncate very long values - apply strict limit
                if (mb_strlen($valueStr) > $maxLength) {
                    $valueStr = mb_substr($valueStr, 0, $maxLength - 3) . '...';
                }

                $this->logger->info("Key: '$key' => Value: '$valueStr'");
            }
        } else {
            $this->logger->warning('Data is not an array, cannot extract keys');
        }

        // Optional: If you want to recursively find all keys in nested arrays
        if (is_array($this->parsedData)) {
            $allNestedKeys = $this->findAllKeysRecursively($this->parsedData);
            $this->logger->info('All nested keys found: ' . json_encode($allNestedKeys));
        }
    }

    /**
     * Finds all keys in a nested array recursively
     *
     * @param array $array The array to search
     * @return array List of all found keys
     */
    private function findAllKeysRecursively($array)
    {
        $keys = [];

        foreach ($array as $key => $value) {
            $keys[] = $key;

            if (is_array($value)) {
                $nestedKeys = $this->findAllKeysRecursively($value);
                foreach ($nestedKeys as $nestedKey) {
                    $keys[] = $key . '.' . $nestedKey; // Dot notation for nested keys
                }
            }
        }

        return $keys;
    }
}
