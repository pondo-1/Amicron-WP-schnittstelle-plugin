<?php
require_once __DIR__ . '/AbstractExporter.php';

/**
 * Simple Excel exporter implementation (without PhpSpreadsheet)
 * 
 * @author Stefan Witt <stefan.witt@rathje-design.de>
 */
class ExcelExporter extends AbstractExporter {
    private $includeHeaders;
    private $sheetName;
    private $logger;

    /**
     * Constructor
     *
     * @param string $sheetName Name of the Excel sheet
     * @param bool $includeHeaders Include column headers
     * @param mixed $logger Optional logger instance
     */
    public function __construct(string $sheetName = 'Articles', bool $includeHeaders = true, $logger = null) {
        $this->sheetName = $sheetName;
        $this->includeHeaders = $includeHeaders;
        $this->logger = $logger;
    }

    /**
     * Exports the given DTO to Excel format (SpreadsheetML)
     *
     * @param AbstractDTO $dto The DTO to export
     * @return string The Excel content as XML
     */
    public function export(AbstractDTO $dto): string {
        if ($this->logger) {
            $this->logger->info('ExcelExporter: Starting export for DTO');
        }

        // Use Excel-specific field mappings
        $data = ($dto instanceof ArticleDTO) ? $dto->toArray('excel') : $this->prepareData($dto);
        $filteredData = $this->filterDataLikeXml($data);
        $flatData = $this->flattenArray($filteredData);

        // Create SpreadsheetML format
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<Workbook xmlns="urn:schemas-microsoft-com:office:spreadsheet"' . "\n";
        $xml .= '    xmlns:o="urn:schemas-microsoft-com:office:office"' . "\n";
        $xml .= '    xmlns:x="urn:schemas-microsoft-com:office:excel"' . "\n";
        $xml .= '    xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"' . "\n";
        $xml .= '    xmlns:html="http://www.w3.org/TR/REC-html40">' . "\n";

        // Add DocumentProperties
        $xml .= '<DocumentProperties xmlns="urn:schemas-microsoft-com:office:office">' . "\n";
        $xml .= '<Title>Article Export</Title>' . "\n";
        $xml .= '<Author>MEC Shop</Author>' . "\n";
        $xml .= '<Created>' . date('Y-m-d\TH:i:s\Z') . '</Created>' . "\n";
        $xml .= '</DocumentProperties>' . "\n";

        // Add Styles
        $xml .= '<Styles>' . "\n";
        $xml .= '<Style ss:ID="Default" ss:Name="Normal">' . "\n";
        $xml .= '<Alignment ss:Vertical="Bottom"/>' . "\n";
        $xml .= '<Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#000000"/>' . "\n";
        $xml .= '</Style>' . "\n";
        $xml .= '<Style ss:ID="Header">' . "\n";
        $xml .= '<Font ss:FontName="Calibri" x:Family="Swiss" ss:Size="11" ss:Color="#000000" ss:Bold="1"/>' . "\n";
        $xml .= '<Interior ss:Color="#D9D9D9" ss:Pattern="Solid"/>' . "\n";
        $xml .= '</Style>' . "\n";
        $xml .= '</Styles>' . "\n";

        // Start Worksheet
        $xml .= '<Worksheet ss:Name="' . htmlspecialchars($this->sheetName) . '">' . "\n";
        $xml .= '<Table>' . "\n";

        if (!empty($flatData)) {
            // Header row
            if ($this->includeHeaders) {
                $xml .= '<Row>' . "\n";
                foreach (array_keys($flatData) as $header) {
                    $formattedHeader = $this->formatHeader($header);
                    $xml .= '<Cell ss:StyleID="Header"><Data ss:Type="String">' . htmlspecialchars($formattedHeader) . '</Data></Cell>' . "\n";
                }
                $xml .= '</Row>' . "\n";
            }

            // Data row
            $xml .= '<Row>' . "\n";
            foreach ($flatData as $value) {
                if (is_numeric($value) && !empty($value)) {
                    $xml .= '<Cell><Data ss:Type="Number">' . htmlspecialchars($value) . '</Data></Cell>' . "\n";
                } elseif (is_bool($value)) {
                    $xml .= '<Cell><Data ss:Type="String">' . ($value ? 'true' : 'false') . '</Data></Cell>' . "\n";
                } else {
                    $xml .= '<Cell><Data ss:Type="String">' . htmlspecialchars($value) . '</Data></Cell>' . "\n";
                }
            }
            $xml .= '</Row>' . "\n";
        }

        // Close Table and Worksheet
        $xml .= '</Table>' . "\n";
        $xml .= '</Worksheet>' . "\n";

        // Close Workbook
        $xml .= '</Workbook>' . "\n";

        if ($this->logger) {
            $this->logger->info('ExcelExporter: Export completed successfully');
        }

        return $xml;
    }

    /**
     * Gets the file extension for Excel format
     *
     * @return string The file extension
     */
    public function getFileExtension(): string {
        return 'xls';
    }

    /**
     * Gets the MIME type for Excel format
     *
     * @return string The MIME type
     */
    public function getMimeType(): string {
        return 'application/vnd.ms-excel';
    }

    /**
     * Flatten nested array to single level for Excel columns
     */
    private function flattenArray(array $array, string $prefix = ''): array {
        $result = [];

        foreach ($array as $key => $value) {
            $newKey = $prefix === '' ? $key : $prefix . '_' . $key;

            if (is_array($value)) {
                $result = array_merge($result, $this->flattenArray($value, $newKey));
            } else {
                $result[$newKey] = $value;
            }
        }

        return $result;
    }

    /**
     * Format header names for better readability
     */
    private function formatHeader(string $header): string {
        $formatted = preg_replace('/^(Feld_|Artikel_|clc)/', '', $header);
        $formatted = str_replace('_', ' ', $formatted);
        $formatted = ucwords(strtolower($formatted));
        return $formatted;
    }

    /**
     * Filters data using the same logic as XmlExporter
     */
    private function filterDataLikeXml(array $data): array {
        $filtered = [];

        foreach ($data as $key => $value) {
            if ($this->hasValue($value)) {
                if (is_array($value)) {
                    $filteredValue = $this->filterDataLikeXml($value);
                    if (!empty($filteredValue)) {
                        $filtered[$key] = $filteredValue;
                    }
                } else {
                    $filtered[$key] = $value;
                }
            }
        }

        return $filtered;
    }

    /**
     * Checks if a value is considered "set" and should be exported
     */
    private function hasValue($value): bool {
        if ($value === null) return false;
        if ($value === '') return false;
        if (is_array($value)) return !empty($value);
        if (is_numeric($value)) return true;
        if (is_bool($value)) return true;
        if (is_string($value)) return trim($value) !== '';
        return true;
    }

    /**
     * Sets the logger instance
     */
    public function setLogger($logger): void {
        $this->logger = $logger;
    }

    /**
     * Gets the current logger instance
     */
    public function getLogger() {
        return $this->logger;
    }
}
