<?php

/**
 * Plugin Name: MEC Shop Amicron Schnittstelle 
 * Plugin URI: https://your-website.com/mec-shop
 * Description: A comprehensive PHP-based e-commerce API system for product data management with multi-format export capabilities.
 * Version: 1.1.2
 * Author: Diane 
 * License: GPL v2 or later
 * Text Domain: mec-shop
 * Domain Path: /languages
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('MEC_AMICRON_SCHNITTSTELLE_VERSION', '1.1.2');
define('MEC_AMICRON_SCHNITTSTELLE_PLUGIN_URL', plugin_dir_url(__FILE__));
define('MEC_AMICRON_SCHNITTSTELLE_PLUGIN_PATH', plugin_dir_path(__FILE__));



/**
 * Main MEC Shop Plugin Class
 */
class MecAmicronSchnittstelle
{

    private $logger;
    private $summaryLogger;

    public function __construct()
    {
        add_action('init', array($this, 'init'));
        add_action('wp_ajax_nopriv_mec_shop_api', array($this, 'handle_api_request'));
        add_action('wp_ajax_mec_shop_api', array($this, 'handle_api_request'));
        add_action('admin_menu', array($this, 'add_admin_menu'));

        // Handle custom endpoint
        add_action('init', array($this, 'add_rewrite_rules'));
        add_action('template_redirect', array($this, 'handle_custom_endpoint'));
    }

    /**
     * Initialize the plugin
     */
    public function init()
    {
        // Load required files
        $this->__autoload();

        new MEC__CreateProducts\Init\AdminOptionPage();

        // Initialize logger
        $this->logger = LogManager::getLogger();

    }


public function __autoload()
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
    }
        // Load core classes
        require_once MEC_AMICRON_SCHNITTSTELLE_PLUGIN_PATH . 'src/logs/Logger.php';

        require_once MEC_AMICRON_SCHNITTSTELLE_PLUGIN_PATH . 'src/RequestParser.php';
        require_once MEC_AMICRON_SCHNITTSTELLE_PLUGIN_PATH . 'src/ResponseHandler.php';

        // Load action classes
        require_once MEC_AMICRON_SCHNITTSTELLE_PLUGIN_PATH . 'src/actions/AbstractAction.php';
        require_once MEC_AMICRON_SCHNITTSTELLE_PLUGIN_PATH . 'src/actions/ReadVersionAction.php';
        require_once MEC_AMICRON_SCHNITTSTELLE_PLUGIN_PATH . 'src/actions/ReadLanguagesAction.php';
        require_once MEC_AMICRON_SCHNITTSTELLE_PLUGIN_PATH . 'src/actions/ReadCategoriesAction.php';
        require_once MEC_AMICRON_SCHNITTSTELLE_PLUGIN_PATH . 'src/actions/ReadManufacturersAction.php';
        require_once MEC_AMICRON_SCHNITTSTELLE_PLUGIN_PATH . 'src/actions/ReadShopDataAction.php';
        require_once MEC_AMICRON_SCHNITTSTELLE_PLUGIN_PATH . 'src/actions/WriteArtikelAction.php';

        // Load DTO classes
        require_once MEC_AMICRON_SCHNITTSTELLE_PLUGIN_PATH . 'src/dto/AbstractDTO.php';
        require_once MEC_AMICRON_SCHNITTSTELLE_PLUGIN_PATH . 'src/dto/ArticleDTO.php';

        // Load exporters
        require_once MEC_AMICRON_SCHNITTSTELLE_PLUGIN_PATH . 'src/exporters/AbstractExporter.php';
        require_once MEC_AMICRON_SCHNITTSTELLE_PLUGIN_PATH . 'src/exporters/JsonExporter.php';
        require_once MEC_AMICRON_SCHNITTSTELLE_PLUGIN_PATH . 'src/exporters/XmlExporter.php';
        require_once MEC_AMICRON_SCHNITTSTELLE_PLUGIN_PATH . 'src/exporters/ExcelExporter.php';
        require_once MEC_AMICRON_SCHNITTSTELLE_PLUGIN_PATH . 'src/exporters/FileWriter.php';

        // Load config
        require_once MEC_AMICRON_SCHNITTSTELLE_PLUGIN_PATH . 'src/config/FieldMappingConfig.php';
    }

    /**
     * Initialize logger with WordPress uploads directory
     */
    private function init_logger()
    {
        $log_dir = MEC_AMICRON_SCHNITTSTELLE_PLUGIN_PATH . 'src/logs';

        if (!file_exists($log_dir)) {
            wp_mkdir_p($log_dir);
        }

        // Use shared logger everywhere
        $this->logger = LogManager::getDefaultLogger();
        $this->summaryLogger = LogManager::getSummaryLogger();
        // You can also reuse $this->logger for summaries or create a second shared Logger via LogManager if needed
    }

    /**
     * Add rewrite rules for custom API endpoint
     */
    public function add_rewrite_rules()
    {
        add_rewrite_rule('^mec-shop-api/?', 'index.php?mec_shop_api=1', 'top');
        add_rewrite_tag('%mec_shop_api%', '([^&]+)');
    }

    /**
     * Handle custom endpoint requests
     */
    public function handle_custom_endpoint()
    {
        if (get_query_var('mec_shop_api')) {
            $apiHandler = new \MEC_AmicronSchnittstelle\Init\ApiHandler();
            $apiHandler->handle_request();
            exit;
        }
    }
    }

    /**
     * Add admin menu
     */
    public function add_admin_menu()
    {
        add_menu_page(
            'Amicron Schnittstelle',
            'Amicron Schnittstelle',
            'manage_options',
            'amicron-schnittstelle',
            array($this, 'admin_page'),
            'dashicons-store',
            30
        );
    }

    /**
     * Admin page content
     */
    public function admin_page()
    {
?>
        <div class="wrap">
            <h1>MEC Shop Amicron Schnittstelle</h1>
            <p>Welcome to the MEC Shop Amicron Schnittstelle plugin. Use the API to manage your product data.</p>
            <h2>Plugin Logs</h2>
            summary
            <div style="background: #f9f9f9; border: 1px solid #ccc; padding: 10px; max-height: 400px; overflow-y: auto; font-family: monospace; font-size: 13px;">
                <?php
                $log_file = MEC_AMICRON_SCHNITTSTELLE_PLUGIN_PATH . 'src/logs/summary_logs.txt';
                if (file_exists($log_file)) {
                    $logs = file($log_file);
                    // Show last 1000 lines for performance
                    $logs = array_slice($logs, -1000);
                    foreach ($logs as $line) {
                        echo esc_html($line) . "<br>";
                    }
                } else {
                    echo '<em>No log file found.</em>';
                }
                ?>
            </div>
            all
            <div style="background: #f9f9f9; border: 1px solid #ccc; padding: 10px; max-height: 400px; overflow-y: auto; font-family: monospace; font-size: 13px;">
                <?php
                $log_file = MEC_AMICRON_SCHNITTSTELLE_PLUGIN_PATH . 'src/logs/logs.txt';
                if (file_exists($log_file)) {
                    $logs = file($log_file);
                    // Show last 1000 lines for performance
                    $logs = array_slice($logs, -1000);
                    foreach ($logs as $line) {
                        echo esc_html($line) . "<br>";
                    }
                } else {
                    echo '<em>No log file found.</em>';
                }
                ?>
            </div>
        </div>
<?php
    }
}

/**
 * Initialize the plugin
 */
function mec_shop_init()
{
    global $mec_shop_plugin_instance;
    $mec_shop_plugin_instance = new MecAmicronSchnittstelle();
}
add_action('plugins_loaded', 'mec_shop_init');

/**
 * Activation hook
 */
register_activation_hook(__FILE__, function () {
    // Flush rewrite rules
    flush_rewrite_rules();

    // Create upload directory
    $upload_dir = wp_upload_dir();
    $amicron_upload_dir = $upload_dir['basedir'] . '/amicron-uploads';
    if (!file_exists($amicron_upload_dir)) {
        wp_mkdir_p($amicron_upload_dir);
    }
});

/**
 * Deactivation hook
 */
register_deactivation_hook(__FILE__, function () {
    // Flush rewrite rules
    flush_rewrite_rules();
});
