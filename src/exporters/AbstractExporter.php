<?php
require_once __DIR__ . '/../dto/AbstractDTO.php';

/**
 * Abstract base class for different export formats
 * 
 * @author Stefan Witt <stefan.witt@rathje-design.de>
 */
abstract class AbstractExporter {
    
    /**
     * Exports the given DTO to the specific format
     *
     * @param AbstractDTO $dto The DTO to export
     * @return string The exported content
     */
    abstract public function export(AbstractDTO $dto): string;
    
    /**
     * Gets the file extension for this export format
     *
     * @return string The file extension (without dot)
     */
    abstract public function getFileExtension(): string;
    
    /**
     * Gets the MIME type for this export format
     *
     * @return string The MIME type
     */
    abstract public function getMimeType(): string;
    
    /**
     * Validates if the DTO can be exported by this exporter
     *
     * @param AbstractDTO $dto The DTO to validate
     * @return bool True if exportable, false otherwise
     */
    public function canExport(AbstractDTO $dto): bool {
        return true; // Default implementation accepts all DTOs
    }
    
    /**
     * Prepares the DTO data for export
     * Override this method to customize data preparation
     *
     * @param AbstractDTO $dto The DTO to prepare
     * @return array The prepared data array
     */
    protected function prepareData(AbstractDTO $dto): array {
        if (method_exists($dto, 'toArray')) {
            return $dto->toArray();
        }
        
        // Fallback: use object properties
        return get_object_vars($dto);
    }
}