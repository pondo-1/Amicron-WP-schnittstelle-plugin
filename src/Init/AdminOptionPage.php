<?php

namespace MEC_AmicronSchnittstelle\Init;

class AdminOptionPage
{

    public function __construct()
    {
        add_action('admin_menu', [$this, 'addAdminMenu']);
    }

    /**
     * Add admin menu
     */
    public function addAdminMenu()
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
                $log_file = MEC_AMICRON_SCHNITTSTELLE_PLUGIN_PATH . 'src/Log/summary_logs.txt';
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
                $log_file = MEC_AMICRON_SCHNITTSTELLE_PLUGIN_PATH . 'src/Log/logs.txt';
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
