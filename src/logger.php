<?php
/**
 * @author Stefan Witt <stefan.witt@rathje-design.de>
 */

class Logger {
    private $logFile;

    private $minimumLogLevel;

    const LEVELS = [
        'info' => 1,
        'warning' => 2,
        'error' => 3
    ];

    public function __construct($logFile, $minimumLogLevel = 'info') {
        $this->logFile = $logFile;
        $this->minimumLogLevel = $minimumLogLevel;
    }

      public function log($message, $level = 'info') {
        // Prüfen, ob der aktuelle Level größer oder gleich dem minimalen Log-Level ist
        if (self::LEVELS[$level] >= self::LEVELS[$this->minimumLogLevel]) {
            // Set the timezone to your preference
            date_default_timezone_set('UTC');

            // Get the current time in the specified format
            $timestamp = date('y-m-d-H:i:s');

            // Format the log entry
            $logEntry = sprintf("%s [%s] %s\n", $timestamp, strtoupper($level), $message);

            // Write the log entry to the file
            file_put_contents($this->logFile, $logEntry, FILE_APPEND);
        }
    }

    public function info($message) {
        $this->log($message, 'info');
    }

    public function warning($message) {
        $this->log($message, 'warning');
    }

    public function error($message) {
        $this->log($message, 'error');
    }
}

// Beispielverwendung:
// $logger = new Logger('logfile.txt');
// $logger->info('This is an info message.');
// $logger->warning('This is a warning message.');
// $logger->error('This is an error message.');
