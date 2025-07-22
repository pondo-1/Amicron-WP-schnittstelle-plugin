<?php
require_once __DIR__ . '/AbstractExporter.php';

/**
 * JSON exporter implementation
 * 
 * @author Stefan Witt <stefan.witt@rathje-design.de>
 */
class JsonExporter extends AbstractExporter {
    private $dataDumper;
    private $prettyPrint;
    private $unescapedUnicode;

    //$this->dataDumper = new DataDumper($logger);
    
    /**
     * Constructor
     *
     * @param bool $prettyPrint Enable pretty printing
     * @param bool $unescapedUnicode Enable unescaped unicode
     */
    public function __construct(bool $prettyPrint = true, bool $unescapedUnicode = true) {
        $this->prettyPrint = $prettyPrint;
        $this->unescapedUnicode = $unescapedUnicode;
    }
    
    /**
     * Exports the given DTO to JSON format
     *
     * @param AbstractDTO $dto The DTO to export
     * @return string The JSON content
     */
    public function export(AbstractDTO $dto): string {
        // Use JSON-specific field mappings
        $data = ($dto instanceof ArticleDTO) ? $dto->toArray('json') : $this->prepareData($dto);

        // Filter the data using the same logic as XmlExporter
        $filteredData = $this->filterData($data);

        $flags = 0;
        if ($this->prettyPrint) {
            $flags |= JSON_PRETTY_PRINT;
        }
        if ($this->unescapedUnicode) {
            $flags |= JSON_UNESCAPED_UNICODE;
        }

        return json_encode($filteredData, $flags);
    }
    
    /**
     * Filters data to exclude empty values
     *
     * @param array $data
     * @return array
     */
    private function filterData(array $data): array {
        $filtered = [];
        foreach ($data as $key => $value) {
            if ($this->hasValue($value)) {
                if (is_array($value)) {
                    $filtered[$key] = $this->filterData($value);
                } else {
                    $filtered[$key] = $value;
                }
            }
        }
        return $filtered;
    }

    /**
     * Gets the file extension for JSON format
     *
     * @return string The file extension
     */
    public function getFileExtension(): string {
        return 'json';
    }

    /**
     * Gets the MIME type for JSON format
     *
     * @return string The MIME type
     */
    public function getMimeType(): string {
        return 'application/json';
    }


    /**
     * Checks if a value is considered "set" and should be exported
     * (Same logic as XmlExporter)
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