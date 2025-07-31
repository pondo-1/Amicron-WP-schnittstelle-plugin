<?php

/**
 * Configuration class for field name mappings in exports
 * Allows customization of field names for different export formats
 * 
 * @author Stefan Witt <stefan.witt@rathje-design.de>
 */
class FieldMappingConfig {
    private $mappings = [];
    private $configPath;
    
    public function __construct($configPath = null) {
        $this->configPath = $configPath ?: __DIR__ . '/field_mappings.json';
        $this->loadMappings();
    }
    
    /**
     * Load field mappings from configuration file
     */
    private function loadMappings() {
        if (file_exists($this->configPath)) {
            $content = file_get_contents($this->configPath);
            $decoded = json_decode($content, true);
            
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $this->mappings = $decoded;
            } else {
                error_log("Error loading field mappings from {$this->configPath}: " . json_last_error_msg());
            }
        }
    }
    
    /**
     * Get mapped field name for export
     * 
     * @param string $constantName The field constant name (e.g., 'FIELD_ID')
     * @param string $exportType The export type (e.g., 'xml', 'json', 'excel')
     * @return string The mapped field name or the constant name if no mapping exists
     */
    public function getMappedFieldName($constantName, $exportType = 'default') {
        // Check for export-type-specific mapping first
        if (isset($this->mappings[$exportType][$constantName])) {
            return $this->mappings[$exportType][$constantName];
        }
        
        // Fall back to default mapping
        if (isset($this->mappings['default'][$constantName])) {
            return $this->mappings['default'][$constantName];
        }
        
        // Fall back to constant name if no mapping exists
        return $constantName;
    }
    
    /**
     * Get all mapped field names for a specific export type
     * 
     * @param string $exportType The export type
     * @return array Array of mapped field names
     */
    public function getAllMappedFields($exportType = 'default') {
        $result = [];
        
        // Start with default mappings
        if (isset($this->mappings['default'])) {
            $result = $this->mappings['default'];
        }
        
        // Override with export-type-specific mappings
        if ($exportType !== 'default' && isset($this->mappings[$exportType])) {
            $result = array_merge($result, $this->mappings[$exportType]);
        }
        
        return $result;
    }
    
    /**
     * Check if mappings are loaded
     * 
     * @return bool
     */
    public function hasMappings() {
        return !empty($this->mappings);
    }
    
    /**
     * Get the configuration file path
     * 
     * @return string
     */
    public function getConfigPath() {
        return $this->configPath;
    }
}