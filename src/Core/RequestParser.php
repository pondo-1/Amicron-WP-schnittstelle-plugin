<?php

namespace MEC_AmicronSchnittstelle\Core;

use MEC_AmicronSchnittstelle\Log\Logger;
use MEC_AmicronSchnittstelle\Log\LogManager;

/**
 * RequestParser Class
 * 
 * This class handles reading and parsing request data
 * based on Content-Type.
 */
class RequestParser
{
    private $logger;
    private $summaryLogger;
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
        $this->summaryLogger = LogManager::getSummaryLogger();
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
        }
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

        return $this->parsedData;
    }

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

    private function parseFormUrlEncodedData()
    {
        parse_str($this->requestBody, $parsedData);
        if (!empty($parsedData)) {
            $this->parsedData = $parsedData;
            $this->logger->info('Parsed form data successfully');
        }
    }

    private function parseMultipartFormData()
    {
        // Multipart form data is already parsed by PHP in $_POST
        $this->logger->info('Using PHP parsed multipart form data');
    }

    private function parseXmlData()
    {
        try {
            $xml = simplexml_load_string($this->requestBody);
            if ($xml !== false) {
                $this->parsedData = json_decode(json_encode($xml), true);
                $this->logger->info('Parsed XML data successfully');
            } else {
                $this->logger->error('Failed to parse XML data');
            }
        } catch (\Exception $e) {
            $this->logger->error('Error parsing XML data: ' . $e->getMessage());
        }
    }

    private function parseUnknownContentType()
    {
        $this->logger->warning('Unknown content type: ' . $this->contentType);
        // Try to parse as JSON as fallback
        $this->parseJsonData();
    }
}
