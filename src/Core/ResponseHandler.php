<?php

namespace MEC_AmicronSchnittstelle\Core;

use MEC_AmicronSchnittstelle\Log\Logger;
use MEC_AmicronSchnittstelle\Log\LogManager;

/**
 * ResponseHandler Class
 *
 * This class handles HTTP response formatting and headers
 * for the API responses.
 */
class ResponseHandler
{
    private $logger;
    private $contentType;
    private $disableCache;
    private $fullHeader;

    /**
     * Constructor
     *
     * @param Logger $logger The logger for output
     * @param string $contentType Default content type for responses
     */
    public function __construct(Logger $logger, $contentType = 'text/xml; charset=utf-8')
    {
        $this->logger = $logger;
        $this->contentType = $contentType;
        $this->disableCache = !(isset($_GET['NoHeader']) && $_GET['NoHeader'] == "Y");
        $this->fullHeader = isset($_GET['FullHeader']) && $_GET['FullHeader'] == "Y";
    }

    /**
     * Sets appropriate headers for the response
     */
    public function setHeaders()
    {
        if ($this->disableCache) {
            // Set cache prevention headers
            header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");  // always changed
            header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
            header("Pragma: no-cache"); // HTTP/1.0

            if ($this->fullHeader) {
                header("Content-type: text/xml");
            }
        }

        // Always set content type
        header("Content-Type: " . $this->contentType);
    }

    /**
     * Sends a response with appropriate headers
     *
     * @param string $content The content to send
     * @param int $statusCode HTTP status code (default: 200)
     */
    public function sendResponse($content, $statusCode = 200)
    {
        // Log detailed response information
        $this->logger->info("=== RESPONSE BEING SENT ===");
        $this->logger->info("Status Code: " . $statusCode);
        $this->logger->info("Content Type: " . $this->contentType);
        $this->logger->info("Content Length: " . strlen($content) . " bytes");
        $this->logger->info("Response Content: " . $content);
        $this->logger->info("Timestamp: " . date('Y-m-d H:i:s'));
        $this->logger->info("=== END RESPONSE ===");

        http_response_code($statusCode);
        $this->setHeaders();
        echo $content;
        exit;
    }

    /**
     * Sends an error response
     *
     * @param string $message Error message
     * @param int $statusCode HTTP status code (default: 501)
     */
    public function sendError($message = "501 Not Implemented", $statusCode = 501)
    {
        $this->logger->warning("Error response: $message");
        $this->sendResponse($message, $statusCode);
    }

    /**
     * Creates and sends an XML status response
     *
     * @param string $message Status message
     * @param string $mode Status mode (Updated, Inserted, Error, etc.)
     * @param string|int $id ID of the affected record
     * @param int $statusCode HTTP status code (default: 200)
     */
    public function sendXmlStatus($message, $mode, $id, $statusCode = 200)
    {
        $xml = new \SimpleXMLElement('<STATUS/>');
        $statusData = $xml->addChild('STATUS_DATA');
        $statusData->addChild('MESSAGE', $message);
        $statusData->addChild('MODE', $mode);
        $statusData->addChild('ID', $id);

        $this->sendResponse($xml->asXML(), $statusCode);
    }
}
