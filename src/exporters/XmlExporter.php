<?php
require_once __DIR__ . '/AbstractExporter.php';

/**
 * XML exporter implementation
 * 
 * @author Stefan Witt <stefan.witt@rathje-design.de>
 */
class XmlExporter extends AbstractExporter {
    
    private $rootElementName;
    private $formatOutput;
    
    /**
     * Constructor
     *
     * @param string $rootElementName Name of the root XML element
     * @param bool $formatOutput Enable formatted output
     */
    public function __construct(string $rootElementName = 'data', bool $formatOutput = true) {
        $this->rootElementName = $rootElementName;
        $this->formatOutput = $formatOutput;
    }
    
    /**
     * Exports the given DTO to XML format
     *
     * @param AbstractDTO $dto The DTO to export
     * @return string The XML content
     */
    public function export(AbstractDTO $dto): string {
        // Use XML-specific field mappings
        $data = ($dto instanceof ArticleDTO) ? $dto->toArray('xml') : $this->prepareData($dto);
        
        // Create XML document
        $xml = new DOMDocument('1.0', 'UTF-8');
        $xml->formatOutput = $this->formatOutput;
        
        // Create root element
        $rootElement = $xml->createElement($this->rootElementName);
        $xml->appendChild($rootElement);
        
        // Convert array to XML
        $this->arrayToXml($data, $rootElement, $xml);
        
        return $xml->saveXML();
    }
    
    /**
     * Gets the file extension for XML format
     *
     * @return string The file extension
     */
    public function getFileExtension(): string {
        return 'xml';
    }
    
    /**
     * Gets the MIME type for XML format
     *
     * @return string The MIME type
     */
    public function getMimeType(): string {
        return 'application/xml';
    }
    
    /**
     * Converts array to XML recursively
     *
     * @param array $array Source array
     * @param DOMElement $parentElement Parent XML element
     * @param DOMDocument $xml XML document
     */
    private function arrayToXml(array $array, DOMElement $parentElement, DOMDocument $xml): void {
        foreach ($array as $key => $value) {
            // Only export if value is set and not empty
            if (!$this->hasValue($value)) {
                continue;
            }

            // Handle numeric keys by creating generic element names
            if (is_numeric($key)) {
                $elementName = 'item';
            } else {
                // Sanitize element name
                $elementName = $this->sanitizeElementName($key);
            }

            if (is_array($value)) {
                $element = $xml->createElement($elementName);
                $parentElement->appendChild($element);
                $this->arrayToXml($value, $element, $xml);
            } else {
                $element = $xml->createElement($elementName);
                $element->appendChild($xml->createTextNode($this->sanitizeValue($value)));
                $parentElement->appendChild($element);
            }
        }
    }

    /**
     * Sanitizes element names for XML compatibility
     *
     * @param string $name Element name to sanitize
     * @return string Sanitized element name
     */
    private function sanitizeElementName(string $name): string {
        // Remove invalid characters and ensure it starts with a letter or underscore
        $name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $name);
        if (preg_match('/^[0-9]/', $name)) {
            $name = '_' . $name;
        }
        return $name;
    }

    /**
     * Sanitizes values for XML content
     *
     * @param mixed $value Value to sanitize
     * @return string Sanitized value
     */
    private function sanitizeValue($value): string {
        if ($value === null) {
            return '';
        }
        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_XML1, 'UTF-8');
    }

    /**
     * Checks if a value is considered "set" and should be exported
     *
     * @param mixed $value
     * @return bool
     */
    private function hasValue($value): bool {
        // Null values are not exported
        if ($value === null) {
            return false;
        }

        // Empty strings are not exported
        if ($value === '') {
            return false;
        }

        // Arrays: only export if not empty
        if (is_array($value)) {
            return !empty($value);
        }

        // For numbers: export 0 as it's a valid value
        if (is_numeric($value)) {
            return true;
        }

        // For booleans: always export
        if (is_bool($value)) {
            return true;
        }

        // For strings: export if not empty after trimming
        if (is_string($value)) {
            return trim($value) !== '';
        }

        // Default: export the value
        return true;
    }
}