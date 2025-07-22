<?php

/**
 * MEC Shop API - Standalone Fallback
 * This file provides the original standalone functionality when WordPress is not available
 * 
 * @author Stefan Witt <stefan.witt@rathje-design.de>
 */

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'logger.php';
require_once 'RequestParser.php';
require_once 'ResponseHandler.php';

// Load all action classes
require_once 'actions/AbstractAction.php';
require_once 'actions/ReadVersionAction.php';
require_once 'actions/ReadLanguagesAction.php';
require_once 'actions/ReadCategoriesAction.php';
require_once 'actions/ReadManufacturersAction.php';
require_once 'actions/ReadShopDataAction.php';
require_once 'actions/WriteArtikelAction.php';

// IMPORTANT: these lines are required, do not change!
$version_major = 14;
$version_minor = 1;
$default_charset = "Default_Charset";
$datum = "June 2025";

file_put_contents('request_log.txt', "request ", FILE_APPEND);
$logger = new Logger(__DIR__ . '/logfile.txt', 'info');

try {
    $logger->info('start next request (standalone mode)');
} catch (Exception $e) {
    error_log('Logging error: ' . $e->getMessage());
}

// Initialize the RequestParser and ResponseHandler
$requestParser = new RequestParser($logger);
$responseHandler = new ResponseHandler($logger);
$requestParser->logHttpRequest();
$requestData = $requestParser->parseRequestData();

// Set headers through the ResponseHandler
$responseHandler->setHeaders();

$action = (isset($_POST['action']) ? $_POST['action'] : $_GET['action']);
$logger->info("Action: $action");

// Process the action
try {
    switch ($action) {
        case 'read_version':
            $actionHandler = new ReadVersionAction($logger, $version_major, $version_minor, $default_charset);
            $response = $actionHandler->execute();
            break;

        case 'read_languages':
            $actionHandler = new ReadLanguagesAction($logger);
            $response = $actionHandler->execute();
            break;

        case 'read_categories':
            $actionHandler = new ReadCategoriesAction($logger);
            $response = $actionHandler->execute();
            break;

        case 'read_hersteller':
            $actionHandler = new ReadManufacturersAction($logger);
            $response = $actionHandler->execute();
            break;

        case 'read_shopdata':
            $actionHandler = new ReadShopDataAction($logger);
            $response = $actionHandler->execute();
            break;

        case 'write_artikel':
            $actionHandler = new WriteArtikelAction($logger);
            $response = $actionHandler->execute($requestData);
            break;

        case 'write_categorie':
        case 'add_artikel_image':
        case 'write_hersteller':
            $logger->warning("nyi: $action");
            throw new Exception("Not yet implemented: $action");

        default:
            $logger->warning("unknown action: $action");
            throw new Exception("Unknown action: $action");
    }

    $responseHandler->sendResponse($response);
} catch (Exception $e) {
    $logger->error("Error processing action: " . $e->getMessage());
    $responseHandler->sendError("Internal server error: " . $e->getMessage());
}
