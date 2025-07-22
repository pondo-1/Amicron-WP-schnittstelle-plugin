# MEC Shop WordPress Plugin

Convert your standalone MEC Shop E-Commerce API into a WordPress plugin.

## Features

✅ **Same API Functionality** - All original endpoints work exactly the same
✅ **WordPress Integration** - Seamless integration with WordPress admin
✅ **WooCommerce Compatible** - Can sync with WooCommerce products
✅ **Admin Interface** - Easy configuration and testing through WordPress admin
✅ **Export Testing** - Built-in export testing functionality
✅ **Custom Endpoints** - Clean URLs like `/mec-shop-api/`

## Installation

1. Copy your entire `mec-shop-main` folder to `wp-content/plugins/`
2. Rename the folder to `mec-shop`
3. Activate the plugin in WordPress admin
4. **Test both access methods:**
   - Direct: `https://yoursite.com/wp-content/plugins/mec-shop/woo.php?action=read_version`
   - Endpoint: `https://yoursite.com/mec-shop-api/?action=read_version`

## Plugin Structure

```
mec-shop/
├── mec-shop-plugin.php     # Main plugin file (WordPress integration)
├── woo.php                 # Direct script access (for external apps)
├── src/                    # All your original source code
│   ├── woo_standalone.php  # Fallback for non-WordPress environments
│   ├── actions/
│   ├── dto/
│   ├── exporters/
│   └── config/
└── README-PLUGIN.md        # This file
```

## API Endpoints

The plugin provides two ways to access the API:

### Method 1: Direct Script Access (Recommended for External Apps)

For external applications that need to call a specific PHP script:

```
https://yoursite.com/wp-content/plugins/mec-shop/woo.php
```

This method:

- ✅ **Direct compatibility** with existing applications
- ✅ **Same URL pattern** as standalone version
- ✅ **Automatic fallback** to standalone mode if WordPress fails
- ✅ **No URL rewriting** required

### Method 2: WordPress Endpoint (Recommended for WordPress Integration)

```
https://yoursite.com/mec-shop-api/
```

This method:

- ✅ **WordPress integrated** with admin interface
- ✅ **Clean URLs** using WordPress rewrite rules
- ✅ **WordPress security** features enabled

### Available Actions (Both Methods)

| Action            | Description             | Direct Script URL                                             | WordPress Endpoint URL                  |
| ----------------- | ----------------------- | ------------------------------------------------------------- | --------------------------------------- |
| `read_version`    | Get system version      | `/wp-content/plugins/mec-shop/woo.php?action=read_version`    | `/mec-shop-api/?action=read_version`    |
| `read_languages`  | Get available languages | `/wp-content/plugins/mec-shop/woo.php?action=read_languages`  | `/mec-shop-api/?action=read_languages`  |
| `read_categories` | Get product categories  | `/wp-content/plugins/mec-shop/woo.php?action=read_categories` | `/mec-shop-api/?action=read_categories` |
| `read_hersteller` | Get manufacturers       | `/wp-content/plugins/mec-shop/woo.php?action=read_hersteller` | `/mec-shop-api/?action=read_hersteller` |
| `read_shopdata`   | Get shop configuration  | `/wp-content/plugins/mec-shop/woo.php?action=read_shopdata`   | `/mec-shop-api/?action=read_shopdata`   |
| `write_artikel`   | Create/update articles  | `/wp-content/plugins/mec-shop/woo.php` (POST with data)       | `/mec-shop-api/` (POST with data)       |

## WordPress Admin

After activation, you'll find "MEC Shop" in your WordPress admin menu with:

1. **Dashboard** - Overview and API information
2. **Settings** - Configure API key and export directories
3. **Export Test** - Test export functionality directly in admin

## Advantages of WordPress Plugin vs Standalone

### ✅ WordPress Plugin Benefits:

- **Easy Installation** - Just activate like any WordPress plugin
- **WordPress Security** - Leverages WordPress user authentication
- **Admin Interface** - Professional admin pages for configuration
- **File Management** - Uses WordPress uploads directory
- **Error Handling** - WordPress-style error handling and logging
- **Updates** - Can be updated through WordPress plugin system
- **Integration** - Easy integration with WooCommerce and other plugins

### ⚖️ Standalone Benefits:

- **Lighter** - No WordPress overhead
- **Standalone** - Can run on any PHP server
- **Direct Access** - Direct file access to `woo.php`

## Usage Examples

### Direct Script Access (for External Apps)

```bash
# Basic API call
curl "https://yoursite.com/wp-content/plugins/mec-shop/woo.php?action=read_version"

# Creating an Article (POST)
curl -X POST "https://yoursite.com/wp-content/plugins/mec-shop/woo.php" \
  -d "action=write_artikel" \
  -d "Artikel_ID=123" \
  -d "Artikel_Artikelnr=TEST001" \
  -d "Artikel_Bezeichnung1=Test Product" \
  -d "Artikel_Preis=29.99"
```

### WordPress Endpoint Access

```bash
# Basic API call
curl "https://yoursite.com/mec-shop-api/?action=read_version"

# Creating an Article (POST)
curl -X POST "https://yoursite.com/mec-shop-api/" \
  -d "action=write_artikel" \
  -d "Artikel_ID=123" \
  -d "Artikel_Artikelnr=TEST001" \
  -d "Artikel_Bezeichnung1=Test Product" \
  -d "Artikel_Preis=29.99"
```

## WooCommerce Integration

The plugin can integrate with WooCommerce to:

- Import products from external systems via API
- Export WooCommerce products in XML/JSON/Excel formats
- Sync product data between systems
- Provide external API access to WooCommerce data

## File Locations

### Logs

- WordPress: `/wp-content/uploads/mec-shop-logs/`
- Standalone: `/src/logfile.txt`

### Exports

- WordPress: `/wp-content/uploads/mec-shop-exports/`
- Standalone: `/src/exporters/test_exports/`

## Migration from Standalone

### Option 1: Minimal Changes (Recommended)

If your external application currently calls:

```
https://yoursite.com/path/to/woo.php?action=read_version
```

Simply change to:

```
https://yoursite.com/wp-content/plugins/mec-shop/woo.php?action=read_version
```

### Option 2: Modern WordPress Integration

To migrate to WordPress endpoints:

1. **Update base URL** - Change from `/path/to/woo.php` to `/mec-shop-api/`
2. **Keep your existing API calls** - URLs change but functionality remains identical
3. **Move exports** - Exports now go to WordPress uploads directory
4. **Use admin interface** - Configure through WordPress admin instead of direct file editing

## Security

The WordPress plugin adds these security features:

- WordPress nonce verification (optional)
- User capability checks for admin functions
- WordPress sanitization for all inputs
- Secure file uploads using WordPress functions

## Conclusion

Converting to a WordPress plugin gives you:

- ✅ **Same functionality** as standalone version
- ✅ **Better integration** with WordPress ecosystem
- ✅ **Professional admin interface**
- ✅ **Easier maintenance** and updates
- ✅ **WooCommerce compatibility**

The plugin maintains 100% API compatibility while adding WordPress-specific benefits!
