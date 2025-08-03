<?php

/**
 * Abstract base class for all Data Transfer Objects
 * Provides common functionality for data conversion and export
 */

namespace MEC_AmicronSchnittstelle\DTO;

use InvalidArgumentException;

abstract class AbstractDTO
{
    /**
     * Converts the DTO to an array
     *
     * @return array
     */
    abstract public function toArray(): array;

    /**
     * Converts the DTO to a JSON string
     *
     * @param int|null $options JSON encoding options
     * @return string JSON representation of the DTO
     */
    public function toJson(int $options = null): string
    {
        if ($options === null) {
            $options = JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE;
        }

        return json_encode($this->toArray(), $options);
    }

    /**
     * Converts the DTO to a CSV line (without header)
     *
     * @param string $delimiter The delimiter to use (default: ',')
     * @param string $enclosure The enclosure to use (default: '"')
     * @param string $escapeChar The escape character to use (default: '\')
     * @return string CSV representation of the DTO (single line)
     */
    public function toCsv(string $delimiter = ',', string $enclosure = '"', string $escapeChar = '\\'): string
    {
        $data = $this->toArray();
        $output = fopen('php://temp', 'r+');

        // Only write data row
        fputcsv($output, array_values($data), $delimiter, $enclosure, $escapeChar);

        rewind($output);
        $csv = stream_get_contents($output);
        fclose($output);

        return $csv;
    }

    /**
     * Exports the DTO to a specific format
     *
     * @param string $format The format to export to ('json', 'csv', 'array')
     * @param array $options Format-specific options
     * @return mixed The exported data
     * @throws InvalidArgumentException If the format is not supported
     */
    public function exportAs(string $format, array $options = [])
    {
        switch (strtolower($format)) {
            case 'json':
                $jsonOptions = $options['options'] ?? null;
                return $this->toJson($jsonOptions);

            case 'csv':
                $delimiter = $options['delimiter'] ?? ',';
                $enclosure = $options['enclosure'] ?? '"';
                $escapeChar = $options['escape_char'] ?? '\\';
                return $this->toCsv($delimiter, $enclosure, $escapeChar);

            case 'array':
                return $this->toArray();

            default:
                throw new InvalidArgumentException("Unsupported export format: $format");
        }
    }


    /**
     * Returns a formatted representation of the DTO
     * IMPORTANT: This method only returns the string, it does NOT output anything directly
     * to avoid "headers already sent" errors
     *
     * @param string $format The format to use ('json', 'csv', 'array')
     * @param array $options Format-specific options
     * @return string The formatted string
     */
    public function getFormattedOutput(string $format = 'json', array $options = []): string
    {
        $output = $this->exportAs($format, $options);

        if ($format === 'array') {
            $output = print_r($output, true);
        }

        return $output;
    }
}
