<?php

namespace MEC_AmicronSchnittstelle;

use MEC_AmicronSchnittstelle\Log\LogManager;
use MEC_AmicronSchnittstelle\Core\RequestParser;
use MEC_AmicronSchnittstelle\Core\ResponseHandler;
use MEC_AmicronSchnittstelle\Actions\ReadVersionAction;
use MEC_AmicronSchnittstelle\Actions\ReadLanguagesAction;
use MEC_AmicronSchnittstelle\Actions\ReadCategoriesAction;
use MEC_AmicronSchnittstelle\Actions\ReadManufacturersAction;
use MEC_AmicronSchnittstelle\Actions\ReadShopDataAction;
use MEC_AmicronSchnittstelle\Actions\WriteArtikelAction;

/**
 * Main plugin class
 */
class Plugin
{
    private $logger;

    public function __construct()
    {
        // Set up autoloader
        spl_autoload_register(function ($class_name) {
            $namespace = 'MEC_AmicronSchnittstelle\\';
            if (strpos($class_name, $namespace) !== false) {
                $class_name = str_replace($namespace, '', $class_name);
                $file = MEC_AMICRON_SCHNITTSTELLE_PLUGIN_PATH . 'src/' . str_replace('\\', '/', $class_name) . '.php';
                if (file_exists($file)) {
                    require_once $file;
                } else {
                    error_log("Failed to load file: " . $file);
                }
            }
        });



        // Add WordPress hooks
        add_action('init', [$this, 'init']);
        add_action('wp_ajax_nopriv_mec_shop_api', [$this, 'handle_api_request']);
        add_action('wp_ajax_mec_shop_api', [$this, 'handle_api_request']);
        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('init', [$this, 'add_rewrite_rules']);
        add_action('template_redirect', [$this, 'handle_custom_endpoint']);
    }

    public function init()
    {
        // Any initialization code
    }

    public function handle_api_request()
    {
        try {
            $requestParser = new RequestParser($this->logger);
            $responseHandler = new ResponseHandler($this->logger);

            $requestParser->logHttpRequest();
            $requestData = $requestParser->parseRequestData();
            $responseHandler->setHeaders();

            $action = $_POST['action'] ?? $_GET['action'] ?? '';
            $this->logger->info("Action: $action");

            $response = $this->process_action($action, $requestData);
            $responseHandler->sendResponse($response);
        } catch (\Exception $e) {
            $this->logger->error("Error: " . $e->getMessage());
            $responseHandler = new ResponseHandler($this->logger);
            $responseHandler->sendError("Internal server error: " . $e->getMessage());
        }
    }

    private function process_action($action, $requestData)
    {
        switch ($action) {
            case 'read_version':
                return (new ReadVersionAction($this->logger))->execute();
            case 'read_languages':
                return (new ReadLanguagesAction($this->logger))->execute();
            case 'read_categories':
                return (new ReadCategoriesAction($this->logger))->execute();
            case 'read_hersteller':
                return (new ReadManufacturersAction($this->logger))->execute();
            case 'read_shopdata':
                return (new ReadShopDataAction($this->logger))->execute();
            case 'write_artikel':
                return (new WriteArtikelAction($this->logger))->execute($requestData);
            default:
                throw new \Exception("Unknown action: $action");
        }
    }

    // Add other methods as needed...
}
