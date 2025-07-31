<?php

namespace MEC_AmicronSchnittstelle\Core;

use MEC_AmicronSchnittstelle\Core\Logger;

class LogManager
{
    /** @var Logger|null */
    private static $logger = null;
    private static $summaryLogger = null;

    /**
     * Returns the shared Logger instance.
     *
     * @return Logger
     */
    public static function getDefaultLogger(): Logger
    {
        if (self::$logger === null) {
            $logDir = MEC_AMICRON_SCHNITTSTELLE_PLUGIN_PATH . 'src/logs';
            if (!file_exists($logDir)) {
                wp_mkdir_p($logDir);
            }
            self::$logger = new Logger($logDir . '/logs.txt', 'info');
        }
        return self::$logger;
    }
    public static function getSummaryLogger(): Logger
    {
        if (self::$summaryLogger === null) {
            $logDir = MEC_AMICRON_SCHNITTSTELLE_PLUGIN_PATH . 'src/logs';
            if (!file_exists($logDir)) {
                wp_mkdir_p($logDir);
            }
            self::$summaryLogger = new Logger($logDir . '/summary_logs.txt', 'info');
        }
        return self::$summaryLogger;
    }
}
