# MEC-Shop E-Commerce API System

A comprehensive PHP-based e-commerce API system for product data management with multi-format export capabilities.

## Key Features

### API Endpoints
- **ReadVersion** - System version information
- **ReadLanguages** - Available languages
- **ReadCategories** - Product categories
- **ReadManufacturers** - Manufacturer data
- **ReadShopData** - Shop configuration (tax rates, shipping status)
- **WriteArtikel** - Create/update articles

### Data Management
- **ArticleDTO** - Complete article data model (100+ fields)
- **Request/Response Handler** - HTTP request processing
- **Structured Logging** - Comprehensive activity tracking
- **Data Dumper** - Debug and development utilities

### Export System
- **XML Export** - Structured XML output with configurable field names
- **JSON Export** - API-friendly format with snake_case naming
- **Excel Export** - Spreadsheet format (SpreadsheetML) with user-friendly headers
- **File Writer** - Automated file creation and management

### Configuration
- **Field Mapping** - Customizable field names per export format
- **JSON Configuration** - Clear, readable settings file
- **Fallback System** - Robust default values and error handling

## Technical Stack

- **Pure PHP** - No framework dependencies
- **PSR-compliant** - Clean class structure
- **Constants-based** - Centralized field management
- **Modular Architecture** - Easily extensible design
- **Comprehensive Documentation** - Available in English and German

## Use Cases

- **Product Data Synchronization** between different systems
- **E-Commerce Platform Integration** via REST API
- **Data Export** for external systems and analytics
- **WooCommerce Integration** for online shops
- **Multi-format Data Distribution** with configurable field mappings

## Quick Start

1. Configure field mappings in `src/config/field_mappings.json`
2. Access API endpoints via `src/woo.php`
3. Use exporters for data output in desired format
4. Customize field names per export type as needed

## Project Structure

```
src/
├── actions/          # API endpoint implementations
├── dto/              # Data Transfer Objects
├── exporters/        # Export format handlers
├── config/           # Configuration files and documentation
└── utils/            # Utility classes
```

## Author

Stefan Witt <stefan.witt@rathje-design.de>

## Documentation

- Configuration Documentation: `src/config/README.md`
- 