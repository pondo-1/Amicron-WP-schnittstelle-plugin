<?php

namespace MEC_AmicronSchnittstelle\Actions;

use MEC_AmicronSchnittstelle\Log\LogManager;
use MEC_AmicronSchnittstelle\DTO\ArticleDTO;
use MEC_AmicronSchnittstelle\Utils\ArrayFormatter;
use MEC_AmicronSchnittstelle\Woo\WooProductUpdater;
use MEC_AmicronSchnittstelle\Exporters\JsonExporter;
use MEC_AmicronSchnittstelle\Exporters\XmlExporter;
use MEC_AmicronSchnittstelle\Exporters\ExcelExporter;
use MEC_AmicronSchnittstelle\Exporters\FileWriter;

use SimpleXMLElement;
use Exception;



class WriteArtikelAction extends AbstractAction
{
    private $dataDumper;

    public function __construct($logger)
    {
        parent::__construct($logger);
    }

    public function execute($requestData = [])
    {
        LogManager::getDefaultLogger()->info('Executing WriteArtikelAction with request data: ' . json_encode($requestData));
        $this->logger->info('WriteArtikel called with parsed request data');

        // Log detailed request information
        $this->logDetailedRequest($requestData);

        // Extract the most important parameters from the processed data
        $exportModus = isset($requestData['ExportModus']) ? $requestData['ExportModus'] : 'Overwrite';
        $artikelId = isset($requestData['Artikel_ID']) ? $requestData['Artikel_ID'] : null;
        $artikelNr = isset($requestData['Artikel_Artikelnr']) ? $requestData['Artikel_Artikelnr'] : '';

        // Instead of exporting files, just log the data processing
        $this->logger->info("Article processing completed - no XML files generated, all data logged above");

        // Determine message and mode based on parameters
        $message = 'OK'; // According to requirements, the message must be "OK"
        $mode = '';
        $id = 0;

        $wooProductMapper = new \MEC_AmicronSchnittstelle\Woo\AmicronToWooProductMapper();
        $mappedProductData = $wooProductMapper->mapToWooProduct($requestData);
        $readableProductData = ArrayFormatter::prettyPrint($mappedProductData, 4);
        $wooUpdater = new WooProductUpdater();
        if ($artikelId) {
            // Update existing article
            switch ($exportModus) {
                case 'Overwrite':
                    $result = $wooUpdater->updateProductBySku($mappedProductData);
                    if (is_wp_error($result)) {
                        // Handle error
                        $error_message = $result->get_error_message();
                    } else {
                        // Success - $result contains the product ID
                        LogManager::getSummaryLogger()->info("Update Success:\n" . $readableProductData);
                    }
                    // check if artikel exists, then update it
                    // if not exists, create a new one
                    $mode = "Updated";
                    break;
                case 'NoOverwrite':
                    // If the article already exists, skip it
                    $mode = "None";
                    break;
                case 'PriceOnly':
                case 'QuantityOnly':
                case 'PriceAndQuantityOnly':
                    $mode = "Updated";
                    break;
                default:
                    $message = "Error: Unknown ExportModus: $exportModus";
                    $mode = "Error";
                    break;
            }
            $id = $artikelId;
        } else {
            // Create new article
            $mode = "Inserted";
            $id = rand(1000, 9999); // Simulate a new ID
        }

        // Generate XML response
        $xmlString = $this->generateStatusXML($message, $mode, $id);

        // Log detailed response information
        $this->logDetailedResponse($xmlString, $message, $mode, $id);

        $this->logger->info('WriteArtikel response: ' . $xmlString);

        return $xmlString;
    }

    /**
     * Logs detailed request information to the logger
     *
     * @param array $requestData The parsed request data
     */
    private function logDetailedRequest($requestData)
    {
        $this->logger->info("=== WRITE_ARTIKEL REQUEST DETAILS ===");

        // Log raw request data
        $this->logger->info("Raw Request Data (JSON): " . json_encode($requestData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // Log HTTP method and headers
        $this->logger->info("HTTP Method: " . $_SERVER['REQUEST_METHOD']);

        // Log all POST data
        if (!empty($_POST)) {
            $this->logger->info("POST Data: " . json_encode($_POST, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }

        // Log all GET data
        if (!empty($_GET)) {
            $this->logger->info("GET Data: " . json_encode($_GET, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }

        // Log raw input if available
        $rawInput = file_get_contents('php://input');
        if (!empty($rawInput)) {
            $this->logger->info("Raw Input Body: " . $rawInput);
        }

        // Log specific article fields
        $this->logger->info("=== EXTRACTED ARTICLE FIELDS ===");
        //show all fields in the request data
        foreach ($requestData as $key => $value) {
            $this->logger->info("$key: " . $value);
        }

        $this->logger->info("=== END REQUEST DETAILS ===");
    }

    /**
     * Logs detailed response information to the logger
     *
     * @param string $xmlResponse The XML response that will be sent
     * @param string $message The status message
     * @param string $mode The processing mode
     * @param int $id The article ID
     */
    private function logDetailedResponse($xmlResponse, $message, $mode, $id)
    {
        $this->logger->info("=== WRITE_ARTIKEL RESPONSE DETAILS ===");
        $this->logger->info("Response Message: " . $message);
        $this->logger->info("Response Mode: " . $mode);
        $this->logger->info("Response ID: " . $id);
        $this->logger->info("Full XML Response: " . $xmlResponse);
        $this->logger->info("Response Length: " . strlen($xmlResponse) . " bytes");
        $this->logger->info("=== END RESPONSE DETAILS ===");
    }

    /**
     * Generates an XML status message for article processing
     *
     * @param string $message The status message
     * @param string $mode The processing mode (INSERT, UPDATE, SKIP, ERROR, etc.)
     * @param int $id The ID of the affected article
     * @return string The XML status message
     */
    private function generateStatusXML($message, $mode, $id)
    {
        $xml = new SimpleXMLElement('<STATUS/>');
        $statusData = $xml->addChild('STATUS_DATA');
        $statusData->addChild('MESSAGE', htmlspecialchars($message, ENT_XML1, 'UTF-8'));
        $statusData->addChild('MODE', htmlspecialchars($mode, ENT_XML1, 'UTF-8'));
        $statusData->addChild('ID', $id);

        return $xml->asXML();
    }

    /**
     * Saves image data to the plugin directory
     *
     * @param string $imageData The binary image data
     * @param string $key The field key (e.g., 'artikel_image')
     * @param string $extension The file extension (e.g., 'jpg', 'png')
     * @return string|false The full path to the saved file or false on failure
     */
    private function saveImageToPluginDirectory($imageData, $key, $extension)
    {
        try {
            // Determine the plugin directory path
            $pluginDir = $this->getPluginDirectory();

            // Create images directory within plugin directory
            $imagesDir = $pluginDir . '/images';
            if (!file_exists($imagesDir)) {
                if (!mkdir($imagesDir, 0755, true)) {
                    $this->logger->error("Failed to create images directory: $imagesDir");
                    return false;
                }
            }

            // Generate unique filename
            $timestamp = date('Y-m-d_H-i-s');
            $uniqueId = uniqid();
            $filename = "{$key}_{$timestamp}_{$uniqueId}.{$extension}";
            $fullPath = $imagesDir . '/' . $filename;

            // Save the image data
            if (file_put_contents($fullPath, $imageData) !== false) {
                $this->logger->info("Successfully saved image to: $fullPath");
                return $fullPath;
            } else {
                $this->logger->error("Failed to write image data to: $fullPath");
                return false;
            }
        } catch (Exception $e) {
            $this->logger->error("Error saving image: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Gets the plugin directory path
     *
     * @return string The plugin directory path
     */
    private function getPluginDirectory()
    {
        // Check if we're in WordPress environment
        if (defined('MEC_AMICRON_SCHNITTSTELLE_PLUGIN_PATH')) {
            return MEC_AMICRON_SCHNITTSTELLE_PLUGIN_PATH;
        }

        // Fallback for standalone mode - get parent directory of src/
        return dirname(__DIR__);
    }
}
