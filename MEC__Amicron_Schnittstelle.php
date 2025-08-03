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
    public function __construct()
    {
        // Register activation and deactivation hooks
        register_activation_hook(__FILE__, [$this, 'activate']);
        register_deactivation_hook(__FILE__, [$this, 'deactivate']);

        add_action('init', array($this, 'init'));
        // add_action('wp_ajax_nopriv_mec_shop_api', array($this, 'handle_api_request'));
        // add_action('wp_ajax_mec_shop_api', array($this, 'handle_api_request'));
    }

    /**
     * Initialize the plugin
     */
    public function init()
    {
        // Load required files
        $this->__autoload();

        new MEC_AmicronSchnittstelle\Init\AdminOptionPage();
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
    /**
     * Plugin activation handler
     */
    public function activate()
    {
        // Flush rewrite rules
        flush_rewrite_rules();

        // Create upload directory
        $upload_dir = wp_upload_dir();
        $amicron_upload_dir = $upload_dir['basedir'] . '/amicron-uploads';
        if (!file_exists($amicron_upload_dir)) {
            wp_mkdir_p($amicron_upload_dir);
        }
    }

    /**
     * Plugin deactivation handler
     */
    public function deactivate()
    {
        // Flush rewrite rules
        flush_rewrite_rules();
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
