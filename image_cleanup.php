<?php

/**
 * Image Cleanup Utility for MEC Shop Plugin
 * 
 * This utility helps manage the images directory by cleaning up old files
 * and providing statistics about image storage.
 */

class ImageCleanup
{
    private $imagesDir;
    private $logger;

    public function __construct()
    {
        // Determine images directory path
        if (defined('MEC_AMICRON_SCHNITTSTELLE_PLUGIN_PATH')) {
            $this->imagesDir = MEC_AMICRON_SCHNITTSTELLE_PLUGIN_PATH . '/images';
        } else {
            $this->imagesDir = dirname(__DIR__) . '/images';
        }

        // Simple logging
        $this->logger = function ($message) {
            echo date('Y-m-d H:i:s') . " - " . $message . "\n";
        };
    }

    /**
     * Get statistics about the images directory
     */
    public function getStatistics()
    {
        if (!is_dir($this->imagesDir)) {
            call_user_func($this->logger, "Images directory does not exist: " . $this->imagesDir);
            return false;
        }

        $files = glob($this->imagesDir . '/*');
        $totalFiles = 0;
        $totalSize = 0;
        $fileTypes = [];

        foreach ($files as $file) {
            if (is_file($file) && !in_array(basename($file), ['.htaccess', 'README.md'])) {
                $totalFiles++;
                $totalSize += filesize($file);

                $ext = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                $fileTypes[$ext] = ($fileTypes[$ext] ?? 0) + 1;
            }
        }

        return [
            'total_files' => $totalFiles,
            'total_size' => $totalSize,
            'total_size_formatted' => $this->formatBytes($totalSize),
            'file_types' => $fileTypes,
            'directory' => $this->imagesDir
        ];
    }

    /**
     * Clean up old files (older than specified days)
     */
    public function cleanupOldFiles($daysOld = 30)
    {
        if (!is_dir($this->imagesDir)) {
            call_user_func($this->logger, "Images directory does not exist: " . $this->imagesDir);
            return false;
        }

        $cutoffTime = time() - ($daysOld * 24 * 60 * 60);
        $files = glob($this->imagesDir . '/*');
        $deletedFiles = 0;
        $deletedSize = 0;

        foreach ($files as $file) {
            if (is_file($file) && !in_array(basename($file), ['.htaccess', 'README.md'])) {
                if (filemtime($file) < $cutoffTime) {
                    $fileSize = filesize($file);
                    if (unlink($file)) {
                        $deletedFiles++;
                        $deletedSize += $fileSize;
                        call_user_func($this->logger, "Deleted: " . basename($file));
                    }
                }
            }
        }

        call_user_func($this->logger, "Cleanup completed: {$deletedFiles} files deleted, " . $this->formatBytes($deletedSize) . " freed");
        return ['deleted_files' => $deletedFiles, 'deleted_size' => $deletedSize];
    }

    /**
     * List all image files with details
     */
    public function listFiles()
    {
        if (!is_dir($this->imagesDir)) {
            call_user_func($this->logger, "Images directory does not exist: " . $this->imagesDir);
            return false;
        }

        $files = glob($this->imagesDir . '/*');
        $imageFiles = [];

        foreach ($files as $file) {
            if (is_file($file) && !in_array(basename($file), ['.htaccess', 'README.md'])) {
                $imageFiles[] = [
                    'filename' => basename($file),
                    'size' => filesize($file),
                    'size_formatted' => $this->formatBytes(filesize($file)),
                    'modified' => date('Y-m-d H:i:s', filemtime($file)),
                    'extension' => pathinfo($file, PATHINFO_EXTENSION)
                ];
            }
        }

        // Sort by modification time (newest first)
        usort($imageFiles, function ($a, $b) {
            return strtotime($b['modified']) - strtotime($a['modified']);
        });

        return $imageFiles;
    }

    /**
     * Format bytes to human readable format
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, $precision) . ' ' . $units[$i];
    }
}

// Usage examples if run directly
if (php_sapi_name() === 'cli' || !isset($_SERVER['HTTP_HOST'])) {
    echo "MEC Shop Image Cleanup Utility\n";
    echo "==============================\n\n";

    $cleanup = new ImageCleanup();

    // Show statistics
    echo "Current Statistics:\n";
    echo "-------------------\n";
    $stats = $cleanup->getStatistics();
    if ($stats) {
        echo "Directory: " . $stats['directory'] . "\n";
        echo "Total Files: " . $stats['total_files'] . "\n";
        echo "Total Size: " . $stats['total_size_formatted'] . "\n";
        echo "File Types: " . json_encode($stats['file_types']) . "\n\n";
    }

    // List recent files
    echo "Recent Files:\n";
    echo "-------------\n";
    $files = $cleanup->listFiles();
    if ($files) {
        foreach (array_slice($files, 0, 10) as $file) {
            echo "{$file['filename']} - {$file['size_formatted']} - {$file['modified']}\n";
        }
        if (count($files) > 10) {
            echo "... and " . (count($files) - 10) . " more files\n";
        }
    }

    echo "\nTo clean up files older than 30 days, run:\n";
    echo "php image_cleanup.php cleanup\n";

    if (isset($argv[1]) && $argv[1] === 'cleanup') {
        echo "\nCleaning up old files...\n";
        $cleanup->cleanupOldFiles(30);
    }
}
