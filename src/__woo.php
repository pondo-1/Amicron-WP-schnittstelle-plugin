<?php
/**
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
    $logger->info('start next request');
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
            $logger->warning("nyi: $action");
            $responseHandler->sendError("Not yet implemented: $action");
            break;
        case 'add_artikel_image':
            $logger->warning("nyi: $action");
            $responseHandler->sendError("Not yet implemented: $action");
            break;

        case 'write_hersteller':
            $logger->warning("nyi: $action");
            $responseHandler->sendError("Not yet implemented: $action");
            break;

        default:
            $logger->warning("default unknown action: $action");
            $responseHandler->sendError("Unknown action: $action");
            break;
    }

    // Send the response
    $responseHandler->sendResponse($response);

} catch (Exception $e) {
    $logger->error("Error processing action: " . $e->getMessage());
    $responseHandler->sendError("Internal server error: " . $e->getMessage());
}

/**
 * Extracts and logs specific key values from the data
 *
 * @param Logger $logger The logger for output
 * @param array $data The data to examine
 * @param int $maxLength Maximum length for values (default: 40)
 */
function dumpData($logger, $data, $maxLength = 40) {
    // Log all keys in the data array
    if (is_array($data)) {
        $allKeys = array_keys($data);
        $logger->info('All available keys in data: ' . json_encode($allKeys));

        // Iterate through all keys and log the associated values
        foreach ($data as $key => $value) {
            // Convert arrays and objects to JSON strings for better readability
            if (is_array($value) || is_object($value)) {
                $valueStr = json_encode($value, JSON_UNESCAPED_UNICODE);
            } else {
                $valueStr = (string)$value;
            }

            // Shorten very long values - apply strict limit
            if (mb_strlen($valueStr) > $maxLength) {
                $valueStr = mb_substr($valueStr, 0, $maxLength - 3) . '...';
            }

            $logger->info("Key: '$key' => Value: '$valueStr'");
        }
    } else {
        $logger->warning('Data is not an array, cannot extract keys');
    }

    // Optional: If you want to recursively find all keys in nested arrays
    if (is_array($data)) {
        $allNestedKeys = findAllKeysRecursively($data);
        $logger->info('All nested keys found: ' . json_encode($allNestedKeys));
    }
}

/**
 * Finds all keys in a nested array recursively
 *
 * @param array $array The array to search
 * @return array List of all found keys
 */
function findAllKeysRecursively($array) {
    $keys = [];

    foreach ($array as $key => $value) {
        $keys[] = $key;

        if (is_array($value)) {
            $nestedKeys = findAllKeysRecursively($value);
            foreach ($nestedKeys as $nestedKey) {
                $keys[] = $key . '.' . $nestedKey; // Dot notation for nested keys
            }
        }
    }

    return $keys;
}
?>