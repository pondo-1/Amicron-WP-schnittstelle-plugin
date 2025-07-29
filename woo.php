<?php

/**
 * Direct Script Access
 * This file provides direct access to the MEC Shop API for external applications
 * that need to call a specific PHP script rather than using WordPress endpoints.
 * 
 * @author Stefan Witt <stefan.witt@rathje-design.de>
 */

// Check if we're being called from within WordPress
if (!defined('ABSPATH')) {
    // Not in WordPress - need to bootstrap WordPress to access plugin functionality

    // Find WordPress root directory
    $wp_root = dirname(__FILE__);
    while (!file_exists($wp_root . '/wp-config.php') && $wp_root !== '/') {
        $wp_root = dirname($wp_root);
    }

    if (file_exists($wp_root . '/wp-config.php')) {
        // Bootstrap WordPress
        define('WP_USE_THEMES', false);
        require_once($wp_root . '/wp-load.php');
    } else {
        // Fallback to standalone mode if WordPress not found
        require_once 'src/woo_standalone.php';
        exit;
    }
}

// Now we're in WordPress context - use the plugin functionality
if (class_exists('MecAmicronSchnittstelle')) {
    // Get the plugin instance and handle the request
    global $mec_shop_plugin_instance;
    if (!$mec_shop_plugin_instance) {
        $mec_shop_plugin_instance = new MecAmicronSchnittstelle();
    }

    // Handle the API request directly
    $mec_shop_plugin_instance->handle_api_request();
} else {
    // Plugin not active - show error
    http_response_code(503);
    header('Content-Type: application/json');
    echo json_encode([
        'error' => 'MEC Shop Plugin not active',
        'message' => 'Please activate the MEC Shop plugin in WordPress admin'
    ]);
}
