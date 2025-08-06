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
            <div class="amicron-logs-container">
                <div class="amicron-log-section">
                    <h3>Summary Logs</h3>
                    <div class="amicron-log-viewer">
                        <pre><?php
                                echo "<!-- Debugging summary log output -->";
                                $log_file = MEC_AMICRON_SCHNITTSTELLE_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Log' . DIRECTORY_SEPARATOR . 'summary_logs.txt';
                                echo "<!-- Log file path: " . esc_html($log_file) . " -->"; // Debug path
                                if (file_exists($log_file)) {
                                    echo "<!-- File exists -->";
                                    echo "<!-- File permissions: " . substr(sprintf('%o', fileperms($log_file)), -4) . " -->";
                                    $logs = file_get_contents($log_file);
                                    if ($logs === false) {
                                        echo '<em>Error reading log file. Check file permissions.</em>';
                                    } else {
                                        if (empty($logs)) {
                                            echo '<em>Log file exists but is empty.</em>';
                                        } else {
                                            echo "<!-- Content length: " . strlen($logs) . " bytes -->";
                                            $logs = mb_convert_encoding($logs, 'UTF-8', 'UTF-8');
                                            $logs = explode("\n", $logs);
                                            // Show last 1000 lines for performance
                                            $logs = array_slice($logs, -1000);
                                            echo esc_html(implode("\n", $logs));
                                        }
                                    }
                                } else {
                                    echo '<em>No summary log file found at: ' . esc_html($log_file) . '</em>';
                                }
                                ?>
                        </pre>
                    </div>
                </div>

                <div class="amicron-log-section">
                    <h3>Detailed Logs</h3>
                    <div class="amicron-log-viewer">
                        <pre><?php
                                echo "<!-- Debugging summary log output -->";
                                $log_file = MEC_AMICRON_SCHNITTSTELLE_PLUGIN_PATH . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'Log' . DIRECTORY_SEPARATOR . 'logs.txt';
                                echo "<!-- Log file path: " . esc_html($log_file) . " -->"; // Debug path
                                if (file_exists($log_file)) {
                                    echo "<!-- File exists -->";
                                    echo "<!-- File permissions: " . substr(sprintf('%o', fileperms($log_file)), -4) . " -->";
                                    $logs = file_get_contents($log_file);
                                    if ($logs === false) {
                                        echo '<em>Error reading log file. Check file permissions.</em>';
                                    } else {
                                        if (empty($logs)) {
                                            echo '<em>Log file exists but is empty.</em>';
                                        } else {
                                            echo "<!-- Content length: " . strlen($logs) . " bytes -->";
                                            $logs = mb_convert_encoding($logs, 'UTF-8', 'UTF-8');
                                            $logs = explode("\n", $logs);
                                            // Show last 1000 lines for performance
                                            $logs = array_slice($logs, -1000);
                                            echo esc_html(implode("\n", $logs));
                                        }
                                    }
                                } else {
                                    echo '<em>No summary log file found at: ' . esc_html($log_file) . '</em>';
                                }
                                ?>
                        </pre>
                    </div>
                </div>

                <style>
                    .amicron-logs-container {
                        margin: 20px 0;
                    }

                    .amicron-log-section {
                        margin-bottom: 30px;
                    }

                    .amicron-log-section h3 {
                        margin-bottom: 10px;
                    }

                    .amicron-log-viewer {
                        background: #f9f9f9;
                        border: 1px solid #e5e5e5;
                        border-radius: 4px;
                        padding: 15px;
                        max-height: 400px;
                        overflow-y: auto;
                    }

                    .amicron-log-viewer pre {
                        margin: 0;
                        font-family: Consolas, Monaco, 'Andale Mono', monospace;
                        font-size: 13px;
                        white-space: pre-wrap;
                        word-wrap: break-word;
                        color: #333;
                    }
                </style>
            </div>
        </div>
<?php
    }
}
