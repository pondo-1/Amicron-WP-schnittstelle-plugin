<?php

namespace MEC_AmicronSchnittstelle\Init;

use MEC_AmicronSchnittstelle\Log\LogManager;
use MEC_AmicronSchnittstelle\Core\RequestParser;
use MEC_AmicronSchnittstelle\Core\ResponseHandler;
use MEC_AmicronSchnittstelle\Actions\ReadVersionAction;
use MEC_AmicronSchnittstelle\Actions\ReadLanguagesAction;
use MEC_AmicronSchnittstelle\Actions\ReadCategoriesAction;
use MEC_AmicronSchnittstelle\Actions\ReadManufacturersAction;
use MEC_AmicronSchnittstelle\Actions\ReadShopDataAction;
use MEC_AmicronSchnittstelle\Actions\WriteArtikelAction;
use Exception;

class ApiHandler
{
    private $logger;
    private $summaryLogger;

    public function __construct()
    {
        $this->logger = LogManager::getDefaultLogger();
        $this->summaryLogger = LogManager::getSummaryLogger();
    }

    /**
     * Handle API requests
     */
    public function handle_request()
    {
        try {
            // Initialize components
            $requestParser = new RequestParser($this->logger);
            $responseHandler = new ResponseHandler($this->logger);

            // Log request
            $requestParser->logHttpRequest();
            $requestData = $requestParser->parseRequestData();

            // Set headers
            $responseHandler->setHeaders();

            // Get action
            $action = isset($_POST['action']) ? $_POST['action'] : (isset($_GET['action']) ? $_GET['action'] : '');

            // Summary log: method | action
            $this->summaryLogger->info(sprintf(
                "%s | %s",
                $_SERVER['REQUEST_METHOD'] ?? 'UNKNOWN',
                $action
            ));

            // Log all key-value pairs in $requestData if present, except for read_shopdata and read_languages
            if (!in_array($action, ['read_shopdata', 'read_languages', 'read_categories', 'write_categorie ', 'read_hersteller', 'write_hersteller'])) {
                if (!empty($requestData) && is_array($requestData)) {
                    foreach ($requestData as $key => $value) {
                        if ($key === "ExportModus" || strpos($key, "Artikel_") === 0) {
                            // Ensure value is valid UTF-8 and properly log special characters
                            $safeValue = $value;
                            if (is_string($safeValue)) {
                                // Try to detect encoding and convert to UTF-8 if needed
                                if (!mb_check_encoding($safeValue, 'UTF-8')) {
                                    $encoding = mb_detect_encoding($safeValue, ['ISO-8859-1', 'Windows-1252', 'UTF-8'], true);
                                    if ($encoding && $encoding !== 'UTF-8') {
                                        $safeValue = mb_convert_encoding($safeValue, 'UTF-8', $encoding);
                                    } else {
                                        $safeValue = mb_convert_encoding($safeValue, 'UTF-8', 'auto');
                                    }
                                }
                                // If still not valid UTF-8, show as hex
                                if (!mb_check_encoding($safeValue, 'UTF-8')) {
                                    $safeValue = '[binary: ' . bin2hex($value) . ']';
                                }
                            }
                            // Encode special characters for logging, preserving German letters
                            // if (is_string($safeValue)) {
                            //     $safeValue = json_encode($safeValue, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                            // }
                            $this->summaryLogger->info($key . ": " . print_r($safeValue, true));
                        }
                    }
                }
            }

            // Process action
            $response = $this->process_action($action, $requestData);

            // Send response
            $responseHandler->sendResponse($response);
        } catch (Exception $e) {
            $this->logger->error("Error processing action: " . $e->getMessage());
            $responseHandler = new ResponseHandler($this->logger);
            $responseHandler->sendError("Internal server error: " . $e->getMessage());
        }
    }

    /**
     * Process API actions
     */
    private function process_action($action, $requestData)
    {
        $version_major = MEC_AMICRON_SCHNITTSTELLE_VERSION;
        $version_minor = 1;
        $default_charset = "Default_Charset";

        switch ($action) {
            case 'read_version':
                $actionHandler = new ReadVersionAction($this->logger, $version_major, $version_minor, $default_charset);
                return $actionHandler->execute();

            case 'read_languages':
                $actionHandler = new ReadLanguagesAction($this->logger);
                return $actionHandler->execute();

            case 'read_categories':
                $actionHandler = new ReadCategoriesAction($this->logger);
                return $actionHandler->execute();

            case 'read_hersteller':
                $actionHandler = new ReadManufacturersAction($this->logger);
                return $actionHandler->execute();

            case 'read_shopdata':
                $actionHandler = new ReadShopDataAction($this->logger);
                return $actionHandler->execute();

            case 'write_artikel':
                // Enhanced logging for write_artikel requests
                $this->logger->info("=== WRITE_ARTIKEL REQUEST RECEIVED ===");
                $this->logger->info("Request timestamp: " . date('Y-m-d H:i:s'));
                $this->logger->info("Client IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'unknown'));
                $this->logger->info("User Agent: " . ($_SERVER['HTTP_USER_AGENT'] ?? 'unknown'));
                if (!empty($_SERVER['HTTP_REFERER'])) {
                    $this->logger->info("Referer: " . $_SERVER['HTTP_REFERER']);
                }

                $actionHandler = new WriteArtikelAction($this->logger);
                return $actionHandler->execute($requestData);

            case 'write_categorie':
            case 'add_artikel_image':
            case 'write_hersteller':
                $this->logger->warning("nyi: $action");
                throw new Exception("Not yet implemented: $action");

            default:
                $this->logger->warning("unknown action: $action");
                throw new Exception("Unknown action: $action");
        }
    }
}
