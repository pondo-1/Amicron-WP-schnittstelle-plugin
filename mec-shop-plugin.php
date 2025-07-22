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
define('MEC_SHOP_VERSION', '14.1');
define('MEC_SHOP_PLUGIN_URL', plugin_dir_url(__FILE__));
define('MEC_SHOP_PLUGIN_PATH', plugin_dir_path(__FILE__));

/**
 * Main MEC Shop Plugin Class
 */
class MecShopPlugin
{

    private $logger;

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
        $this->load_dependencies();

        // Initialize logger
        $this->init_logger();
    }

    /**
     * Load all required files
     */
    private function load_dependencies()
    {
        // Load core classes
        require_once MEC_SHOP_PLUGIN_PATH . 'src/logger.php';
        require_once MEC_SHOP_PLUGIN_PATH . 'src/RequestParser.php';
        require_once MEC_SHOP_PLUGIN_PATH . 'src/ResponseHandler.php';

        // Load action classes
        require_once MEC_SHOP_PLUGIN_PATH . 'src/actions/AbstractAction.php';
        require_once MEC_SHOP_PLUGIN_PATH . 'src/actions/ReadVersionAction.php';
        require_once MEC_SHOP_PLUGIN_PATH . 'src/actions/ReadLanguagesAction.php';
        require_once MEC_SHOP_PLUGIN_PATH . 'src/actions/ReadCategoriesAction.php';
        require_once MEC_SHOP_PLUGIN_PATH . 'src/actions/ReadManufacturersAction.php';
        require_once MEC_SHOP_PLUGIN_PATH . 'src/actions/ReadShopDataAction.php';
        require_once MEC_SHOP_PLUGIN_PATH . 'src/actions/WriteArtikelAction.php';

        // Load DTO classes
        require_once MEC_SHOP_PLUGIN_PATH . 'src/dto/AbstractDTO.php';
        require_once MEC_SHOP_PLUGIN_PATH . 'src/dto/ArticleDTO.php';

        // Load exporters
        require_once MEC_SHOP_PLUGIN_PATH . 'src/exporters/AbstractExporter.php';
        require_once MEC_SHOP_PLUGIN_PATH . 'src/exporters/JsonExporter.php';
        require_once MEC_SHOP_PLUGIN_PATH . 'src/exporters/XmlExporter.php';
        require_once MEC_SHOP_PLUGIN_PATH . 'src/exporters/ExcelExporter.php';
        require_once MEC_SHOP_PLUGIN_PATH . 'src/exporters/FileWriter.php';

        // Load config
        require_once MEC_SHOP_PLUGIN_PATH . 'src/config/FieldMappingConfig.php';
    }

    /**
     * Initialize logger with WordPress uploads directory
     */
    private function init_logger()
    {
        $upload_dir = wp_upload_dir();
        $log_dir = $upload_dir['basedir'] . '/mec-shop-logs';

        if (!file_exists($log_dir)) {
            wp_mkdir_p($log_dir);
        }

        $this->logger = new Logger($log_dir . '/mec-shop.log', 'info');
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
            $this->handle_api_request();
            exit;
        }
    }

    /**
     * Handle API requests (main functionality from woo.php)
     */
    public function handle_api_request()
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
            $this->logger->info("Action: $action");

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
        $version_major = MEC_SHOP_VERSION;
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

    /**
     * Add admin menu
     */
    public function add_admin_menu()
    {
        add_menu_page(
            'MEC Shop API',
            'MEC Shop',
            'manage_options',
            'mec-shop',
            array($this, 'admin_page'),
            'dashicons-store',
            30
        );

        add_submenu_page(
            'mec-shop',
            'API Settings',
            'Settings',
            'manage_options',
            'mec-shop-settings',
            array($this, 'settings_page')
        );

        add_submenu_page(
            'mec-shop',
            'Export Test',
            'Export Test',
            'manage_options',
            'mec-shop-export-test',
            array($this, 'export_test_page')
        );
    }

    /**
     * Admin page content
     */
    public function admin_page()
    {
?>
        <div class="wrap">
            <h1>MEC Shop E-Commerce API</h1>

            <div class="card">
                <h2>API Endpoints</h2>
                <p>Your API is available at:</p>
                <code><?php echo home_url('/mec-shop-api/'); ?></code>

                <h3>Available Actions:</h3>
                <ul>
                    <li><strong>read_version</strong> - Get system version</li>
                    <li><strong>read_languages</strong> - Get available languages</li>
                    <li><strong>read_categories</strong> - Get product categories</li>
                    <li><strong>read_hersteller</strong> - Get manufacturers</li>
                    <li><strong>read_shopdata</strong> - Get shop configuration</li>
                    <li><strong>write_artikel</strong> - Create/update articles</li>
                </ul>

                <h3>Example Usage:</h3>
                <code><?php echo home_url('/mec-shop-api/?action=read_version'); ?></code>
            </div>

            <div class="card">
                <h2>Integration with WooCommerce</h2>
                <p>This plugin can integrate with WooCommerce to:</p>
                <ul>
                    <li>Sync products from external systems</li>
                    <li>Export WooCommerce data in multiple formats</li>
                    <li>Provide API access to product data</li>
                </ul>
            </div>
        </div>
    <?php
    }

    /**
     * Settings page
     */
    public function settings_page()
    {
    ?>
        <div class="wrap">
            <h1>MEC Shop Settings</h1>
            <form method="post" action="options.php">
                <?php
                settings_fields('mec_shop_settings');
                do_settings_sections('mec_shop_settings');
                ?>
                <table class="form-table">
                    <tr>
                        <th scope="row">API Key</th>
                        <td>
                            <input type="text" name="mec_shop_api_key" value="<?php echo esc_attr(get_option('mec_shop_api_key')); ?>" class="regular-text" />
                            <p class="description">Optional API key for authentication</p>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Export Directory</th>
                        <td>
                            <input type="text" name="mec_shop_export_dir" value="<?php echo esc_attr(get_option('mec_shop_export_dir', 'mec-shop-exports')); ?>" class="regular-text" />
                            <p class="description">Directory name in uploads folder for exports</p>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
    <?php
    }

    /**
     * Export test page
     */
    public function export_test_page()
    {
        if (isset($_POST['run_test'])) {
            $this->run_export_test();
        }

    ?>
        <div class="wrap">
            <h1>Export Test</h1>

            <form method="post">
                <input type="hidden" name="run_test" value="1" />
                <?php submit_button('Run Export Test'); ?>
            </form>

            <?php if (isset($_POST['run_test'])): ?>
                <div class="notice notice-success">
                    <p>Export test completed! Check the results above.</p>
                </div>
            <?php endif; ?>
        </div>
<?php
    }

    /**
     * Run export test
     */
    private function run_export_test()
    {
        echo '<h2>Export Test Results</h2>';

        // Test data
        $testData = [
            'Artikel_ID' => 12345,
            'Artikel_Artikelnr' => 'WP-TEST-001',
            'Artikel_Bezeichnung1' => 'WordPress Test Artikel',
            'Artikel_Text1' => 'Dies ist ein Test-Artikel für WordPress Plugin Export-Funktionalität',
            'Artikel_Preis' => 29.99,
            'Artikel_Steuersatz' => 19.0,
            'Artikel_Status' => 1,
            'Artikel_Gewicht' => 1.5,
            'Artikel_Menge' => 50,
            'Feld_LFDNR' => 'WP-LFD001',
            'Feld_ARTIKELNR' => 'WP-TEST-001',
            'Feld_HSNAME' => 'WordPress Test Hersteller'
        ];

        try {
            // Create ArticleDTO
            $articleDto = ArticleDTO::fromArray($testData);

            // Test JSON Export
            echo '<h3>JSON Export</h3>';
            $jsonExporter = new JsonExporter();
            $jsonContent = $jsonExporter->export($articleDto);
            echo '<pre>' . htmlspecialchars($jsonContent) . '</pre>';

            // Test XML Export
            echo '<h3>XML Export</h3>';
            $xmlExporter = new XmlExporter('article');
            $xmlContent = $xmlExporter->export($articleDto);
            echo '<pre>' . htmlspecialchars($xmlContent) . '</pre>';

            echo '<p><strong>Export test completed successfully!</strong></p>';
        } catch (Exception $e) {
            echo '<div class="notice notice-error"><p>Export test failed: ' . esc_html($e->getMessage()) . '</p></div>';
        }
    }
}

/**
 * Initialize the plugin
 */
function mec_shop_init()
{
    global $mec_shop_plugin_instance;
    $mec_shop_plugin_instance = new MecShopPlugin();
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
    $mec_dir = $upload_dir['basedir'] . '/mec-shop-exports';
    if (!file_exists($mec_dir)) {
        wp_mkdir_p($mec_dir);
    }
});

/**
 * Deactivation hook
 */
register_deactivation_hook(__FILE__, function () {
    // Flush rewrite rules
    flush_rewrite_rules();
});
