# Field Name Configuration for Exports

This documentation describes the usage of the configurable field name mapping system for ArticleDTO exports.

## Overview

The system allows using different field names for various export formats while internally always using the same constants. This improves readability of exported data and enables format-specific customizations.

## Configuration File

Field name mappings are configured in the `field_mappings.json` file:

```
src/config/field_mappings.json
```

### Configuration File Structure

```json
{
  "default": {
    "FIELD_ID": "Artikel_ID",
    "FIELD_ARTICLE_NUMBER": "Artikel_Artikelnr",
    "FIELD_NAME": "Artikel_Bezeichnung1",
    "..."
  },
  "xml": {
    "FIELD_ID": "id",
    "FIELD_ARTICLE_NUMBER": "articleNumber",
    "FIELD_NAME": "name",
    "..."
  },
  "json": {
    "FIELD_ID": "id",
    "FIELD_ARTICLE_NUMBER": "article_number",
    "FIELD_NAME": "name",
    "..."
  },
  "excel": {
    "FIELD_ID": "ID",
    "FIELD_ARTICLE_NUMBER": "Article Number",
    "FIELD_NAME": "Product Name",
    "..."
  }
}
```

### Export Types

| Export Type | Description | Example Field Name |
|-------------|-------------|-------------------|
| `default` | Original constant values | `Artikel_ID` |
| `xml` | Short, XML-friendly names | `id` |
| `json` | Snake_case for JSON APIs | `article_number` |
| `excel` | User-friendly English names | `Article Number` |

## Code Usage

### ArticleDTO Export

```php
// Standard export (uses 'default' mappings)
$data = $article->toArray();
// Result: ["Artikel_ID" => 123, "Artikel_Artikelnr" => "10041", ...]

// XML export
$data = $article->toArray('xml');
// Result: ["id" => 123, "articleNumber" => "10041", ...]

// JSON export
$data = $article->toArray('json');
// Result: ["id" => 123, "article_number" => "10041", ...]

// Excel export
$data = $article->toArray('excel');
// Result: ["ID" => 123, "Article Number" => "10041", ...]
```

### Exporter Integration

The exporters automatically use the corresponding mappings:

```php
// XML Exporter automatically uses 'xml' mappings
$xmlExporter = new XmlExporter();
$xmlContent = $xmlExporter->export($article);
// Generates: <id>123</id><articleNumber>10041</articleNumber>

// JSON Exporter automatically uses 'json' mappings
$jsonExporter = new JsonExporter();
$jsonContent = $jsonExporter->export($article);
// Generates: {"id": 123, "article_number": "10041"}

// Excel Exporter automatically uses 'excel' mappings
$excelExporter = new ExcelExporter();
$excelContent = $excelExporter->export($article);
// Generates Excel with columns: "ID", "Article Number"
```

## FieldMappingConfig Class

### Instantiation

```php
// Use standard configuration file
$config = new FieldMappingConfig();

// Use custom configuration file
$config = new FieldMappingConfig('/path/to/custom/mappings.json');
```

### Methods

#### getMappedFieldName()

Returns the mapped field name for an export type:

```php
$config = new FieldMappingConfig();

// Get XML field name
$xmlName = $config->getMappedFieldName('FIELD_ID', 'xml');
// Result: "id"

// Get JSON field name
$jsonName = $config->getMappedFieldName('FIELD_ARTICLE_NUMBER', 'json');
// Result: "article_number"

// Fallback to default when no mapping exists
$defaultName = $config->getMappedFieldName('FIELD_UNKNOWN', 'xml');
// Result: "FIELD_UNKNOWN" (constant name as fallback)
```

#### getAllMappedFields()

Returns all field name mappings for an export type:

```php
$config = new FieldMappingConfig();

// Get all XML mappings
$xmlMappings = $config->getAllMappedFields('xml');
// Result: ["FIELD_ID" => "id", "FIELD_ARTICLE_NUMBER" => "articleNumber", ...]

// Get all default mappings
$defaultMappings = $config->getAllMappedFields('default');
// Result: ["FIELD_ID" => "Artikel_ID", "FIELD_ARTICLE_NUMBER" => "Artikel_Artikelnr", ...]
```

#### hasMappings()

Checks if mappings were loaded:

```php
$config = new FieldMappingConfig();

if ($config->hasMappings()) {
    echo "Configuration loaded successfully";
} else {
    echo "No configuration found, using fallback values";
}
```

## Customizing Configuration

### Adding New Export Types

To add a new export type, add a new section to `field_mappings.json`:

```json
{
  "default": { ... },
  "xml": { ... },
  "json": { ... },
  "excel": { ... },
  "csv": {
    "FIELD_ID": "ID",
    "FIELD_ARTICLE_NUMBER": "Article Number",
    "FIELD_NAME": "Product Name",
    "FIELD_PRICE": "Price (EUR)"
  }
}
```

Usage:

```php
$data = $article->toArray('csv');
// Result: ["ID" => 123, "Article Number" => "10041", "Product Name" => "..."]
```

### Modifying Existing Mappings

Edit the corresponding values in `field_mappings.json`:

```json
{
  "xml": {
    "FIELD_ID": "productId",           // Changed from "id" to "productId"
    "FIELD_ARTICLE_NUMBER": "sku",    // Changed from "articleNumber" to "sku"
    "FIELD_NAME": "title"             // Changed from "name" to "title"
  }
}
```

### Fallback Behavior

The system uses a hierarchy for field name resolution:

1. **Export-specific mapping**: First looks for a mapping for the specific export type
2. **Default mapping**: If not found, uses the default mapping
3. **Constant name**: If no mapping exists at all, uses the constant name itself

```php
// Example: FIELD_NEW_FIELD exists only in default, not in xml
$config = new FieldMappingConfig();

// XML export: Uses default mapping as fallback
$xmlName = $config->getMappedFieldName('FIELD_NEW_FIELD', 'xml');
// Result: Value from default mapping

// Unknown field: Uses constant name as fallback
$unknownName = $config->getMappedFieldName('FIELD_UNKNOWN', 'xml');
// Result: "FIELD_UNKNOWN"
```

## Error Handling

### Invalid JSON File

When `field_mappings.json` contains invalid JSON:

```php
$config = new FieldMappingConfig();

// Error is written to error_log
// Fallback: Constant names are used
$name = $config->getMappedFieldName('FIELD_ID', 'xml');
// Result: "FIELD_ID" (fallback)
```

### Missing Configuration File

When `field_mappings.json` doesn't exist:

```php
$config = new FieldMappingConfig();

// hasMappings() returns false
if (!$config->hasMappings()) {
    // All mappings use constant names as fallback
}
```

## Best Practices

### 1. Define Complete Mappings

Define all relevant fields for each export type:

```json
{
  "xml": {
    "FIELD_ID": "id",
    "FIELD_ARTICLE_NUMBER": "articleNumber",
    "FIELD_NAME": "name"
    // Define all fields you want to export
  }
}
```

### 2. Consistent Naming Conventions

Use consistent naming conventions per export type:

- **XML**: camelCase (`articleNumber`, `deliveryStatus`)
- **JSON**: snake_case (`article_number`, `delivery_status`)
- **Excel**: User-friendly English names (`Article Number`, `Delivery Status`)

### 3. Backup Configuration

Create backups of `field_mappings.json` before changes:

```bash
cp field_mappings.json field_mappings.json.backup
```

### 4. Validation After Changes

Test exports after configuration changes:

```php
// Perform test export
$article = ArticleDTO::fromArray($testData);
$xmlData = $article->toArray('xml');
$jsonData = $article->toArray('json');
$excelData = $article->toArray('excel');

// Check field names
var_dump(array_keys($xmlData));
var_dump(array_keys($jsonData));
var_dump(array_keys($excelData));
```

## Examples

### Complete Application Example

```php
<?php
require_once 'src/dto/ArticleDTO.php';
require_once 'src/exporters/XmlExporter.php';
require_once 'src/exporters/JsonExporter.php';
require_once 'src/exporters/ExcelExporter.php';

// Article data
$articleData = [
    'Artikel_ID' => 123,
    'Artikel_Artikelnr' => '10041',
    'Artikel_Bezeichnung1' => 'Test Article',
    'Artikel_Preis' => 44.54
];

// Create ArticleDTO
$article = ArticleDTO::fromArray($articleData);

// Different export formats with configurable field names
$xmlExporter = new XmlExporter();
$xmlContent = $xmlExporter->export($article);
// XML uses short names: <id>123</id><articleNumber>10041</articleNumber>

$jsonExporter = new JsonExporter();
$jsonContent = $jsonExporter->export($article);
// JSON uses snake_case: {"id": 123, "article_number": "10041"}

$excelExporter = new ExcelExporter();
$excelContent = $excelExporter->export($article);
// Excel uses English names: columns "ID", "Article Number"
```

### Custom Export Types

```php
// Create configuration for API export
$apiMappings = [
    'api' => [
        'FIELD_ID' => 'product_id',
        'FIELD_ARTICLE_NUMBER' => 'sku',
        'FIELD_NAME' => 'product_name',
        'FIELD_PRICE' => 'price_euro'
    ]
];

// Add to field_mappings.json or use programmatically
$article = ArticleDTO::fromArray($articleData);
$apiData = $article->toArray('api');
// Result: ["product_id" => 123, "sku" => "10041", ...]
```

## Implementation Details

### Class Structure

```php
class FieldMappingConfig {
    private $mappings = [];
    private $configPath;
    
    public function __construct($configPath = null)
    public function getMappedFieldName($constantName, $exportType = 'default')
    public function getAllMappedFields($exportType = 'default')
    public function hasMappings()
    public function getConfigPath()
}
```

### ArticleDTO Integration

The ArticleDTO class has been extended with:

```php
public function toArray($exportType = 'default'): array
private function getOriginalArray(): array
```

### Exporter Integration

All exporters (XML, JSON, Excel) automatically detect ArticleDTO instances and use the appropriate field mappings:

```php
// In each exporter's export() method:
$data = ($dto instanceof ArticleDTO) ? $dto->toArray('exportType') : $this->prepareData($dto);
```

This ensures backward compatibility while providing the new field mapping functionality.