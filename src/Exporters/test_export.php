<?php
/**
 * Test script for export functionality
 * 
 * @author Stefan Witt <stefan.witt@rathje-design.de>
 */
require_once __DIR__ . '/../dto/ArticleDTO.php';
require_once __DIR__ . '/JsonExporter.php';
require_once __DIR__ . '/XmlExporter.php';
require_once __DIR__ . '/FileWriter.php';

// Create test data
$testData = [
    'Artikel_ID' => 12345,
    'Artikel_Artikelnr' => 'ART001',
    'Artikel_Bezeichnung1' => 'Test Artikel',
    'Artikel_Text1' => 'Dies ist ein Test-Artikel für die Export-Funktionalität',
    'Artikel_Preis' => 19.99,
    'Artikel_Steuersatz' => 19.0,
    'Artikel_Status' => 1,
    'Artikel_Gewicht' => 0.5,
    'Artikel_Menge' => 100,
    'Feld_LFDNR' => 'LFD001',
    'Feld_ARTIKELNR' => 'ART001',
    'Feld_HSNAME' => 'Test Hersteller'
];

// Create ArticleDTO
$articleDto = ArticleDTO::fromArray($testData);

// Test JSON Export
echo "=== JSON Export Test ===\n";
$jsonExporter = new JsonExporter();
$jsonContent = $jsonExporter->export($articleDto);
echo "JSON Content:\n" . $jsonContent . "\n\n";

// Test XML Export
echo "=== XML Export Test ===\n";
$xmlExporter = new XmlExporter('article');
$xmlContent = $xmlExporter->export($articleDto);
echo "XML Content:\n" . $xmlContent . "\n\n";

// Test FileWriter
echo "=== FileWriter Test ===\n";
$fileWriter = new FileWriter('./test_exports');

// Save JSON file
$jsonFile = $fileWriter->saveToFile($articleDto, $jsonExporter);
echo "JSON file saved to: " . ($jsonFile ?: 'FAILED') . "\n";

// Save XML file
$xmlFile = $fileWriter->saveToFile($articleDto, $xmlExporter);
echo "XML file saved to: " . ($xmlFile ?: 'FAILED') . "\n";

// Test with custom filename
$customJsonFile = $fileWriter->saveToFile($articleDto, $jsonExporter, 'custom_export');
echo "Custom JSON file saved to: " . ($customJsonFile ?: 'FAILED') . "\n";

// Test with subdirectory
$subdirXmlFile = $fileWriter->saveToFile($articleDto, $xmlExporter, null, 'articles');
echo "Subdirectory XML file saved to: " . ($subdirXmlFile ?: 'FAILED') . "\n";

echo "\nTest completed!\n";