<?php
require_once __DIR__ . '/../dto/AbstractDTO.php';
require_once __DIR__ . '/AbstractExporter.php';

/**
 * File writer class for saving exported DTOs to files
 * 
 * @author Stefan Witt <stefan.witt@rathje-design.de>
 */
class FileWriter {
    
    private $baseDirectory;
    private $createDirectories;
    
    /**
     * Constructor
     *
     * @param string $baseDirectory Base directory for saving files
     * @param bool $createDirectories Whether to create directories if they don't exist
     */
    public function __construct(string $baseDirectory = './exports', bool $createDirectories = true) {
        $this->baseDirectory = rtrim($baseDirectory, '/\\');
        $this->createDirectories = $createDirectories;
    }

    /**
     * Saves the DTO to a file using the specified exporter
     *
     * @param AbstractDTO $dto The DTO to save
     * @param AbstractExporter $exporter The exporter to use
     * @param string|null $filename Optional custom filename (without extension)
     * @param string|null $subdirectory Optional subdirectory
     * @return string|false Path to saved file or false on failure
     */
    public function saveToFile(AbstractDTO $dto, AbstractExporter $exporter, ?string $filename = null, ?string $subdirectory = null) {
        try {
            // Generate filename if not provided
            if ($filename === null) {
                $filename = $this->generateFilename($dto);
            }

            // Build full path
            $directory = $this->baseDirectory;
            if ($subdirectory !== null) {
                $directory .= '/' . trim($subdirectory, '/\\');
            }

            // Create directory if needed
            if ($this->createDirectories && !is_dir($directory)) {
                if (!mkdir($directory, 0755, true)) {
                    return false;
                }
            }

            // Generate full file path
            $extension = $exporter->getFileExtension();
            $filepath = $directory . '/' . $filename . '.' . $extension;

            // Export content
            $content = $exporter->export($dto);

            // Save to file
            $result = file_put_contents($filepath, $content);

            return $result !== false ? $filepath : false;

        } catch (Exception $e) {
            error_log("FileWriter error: " . $e->getMessage());
            return false;
        }
    }


  /**
   * Generates a filename for the given DTO
   *
   * This method creates meaningful filenames based on the DTO type and available data.
   * For ArticleDTO objects, it prioritizes LFDNR (internal article ID) for consistent
   * file naming. All generated filenames are cleaned to remove non-printable characters.
   *
   * @param AbstractDTO $dto The DTO object to generate a filename for
   * @return string Clean filename without extension
   *
   * @example
   * // For ArticleDTO with LFDNR
   * $dto = new ArticleDTO();
   * $dto->additionalFields['LFDNR'] = '13716';
   * $filename = $this->generateFilename($dto); // Returns: "13716"
   *
   */
   private function generateFilename(AbstractDTO $dto): string {
       $filename = 'export';

       // Try to get meaningful identifiers from the DTO
       if ($dto instanceof ArticleDTO) {
           $lfdnr = $dto->additionalFields['LFDNR'] ?? null;

           if ($lfdnr) {
               // Use only LFDNR for filename
               $filename = $this->cleanFilename($lfdnr);
           } elseif ($dto->id) {
               $filename = 'article-' . $dto->id;
           } else {
               // Fallback to timestamp if no LFDNR or ID available
               $filename = 'article-' . date('YmdHis');
           }
       } else {
           // Generic approach for other DTOs
           if (property_exists($dto, 'id') && $dto->id) {
               $filename = 'dto-' . $dto->id;
           } else {
               $filename = 'dto-' . uniqid();
           }
       }

       // Clean the final filename
       $filename = $this->cleanFilename($filename);

       return $filename;
   }

   /**
    * Removes only non-printable characters from filename
    *
    * @param string $filename The filename to clean
    * @return string Cleaned filename
    */
   private function cleanFilename(string $filename): string {
       // Remove only non-printable characters (ASCII < 32 and > 126)
       $clean = preg_replace('/[^\x20-\x7E]/', '', $filename);

       // Trim whitespace from beginning and end
       $clean = trim($clean);

       // If the result is empty, use a fallback
       if (empty($clean)) {
           $clean = 'export-' . date('YmdHis');
       }

       return $clean;
   }


    /**
     * Sets the base directory for file operations
     *
     * @param string $directory New base directory
     */
    public function setBaseDirectory(string $directory): void {
        $this->baseDirectory = rtrim($directory, '/\\');
    }

    /**
     * Gets the current base directory
     *
     * @return string Current base directory
     */
    public function getBaseDirectory(): string {
        return $this->baseDirectory;
    }

    /**
     * Checks if a file exists in the export directory
     *
     * @param string $filename Filename to check
     * @param string $extension File extension
     * @param string|null $subdirectory Optional subdirectory
     * @return bool True if file exists, false otherwise
     */
    public function fileExists(string $filename, string $extension, ?string $subdirectory = null): bool {
        $directory = $this->baseDirectory;
        if ($subdirectory !== null) {
            $directory .= '/' . trim($subdirectory, '/\\');
        }

        $filepath = $directory . '/' . $filename . '.' . $extension;
        return file_exists($filepath);
    }

    /**
     * Deletes a file from the export directory
     *
     * @param string $filename Filename to delete
     * @param string $extension File extension
     * @param string|null $subdirectory Optional subdirectory
     * @return bool True if file was deleted, false otherwise
     */
    public function deleteFile(string $filename, string $extension, ?string $subdirectory = null): bool {
        $directory = $this->baseDirectory;
        if ($subdirectory !== null) {
            $directory .= '/' . trim($subdirectory, '/\\');
        }
        
        $filepath = $directory . '/' . $filename . '.' . $extension;
        
        if (file_exists($filepath)) {
            return unlink($filepath);
        }
        
        return false;
    }
}