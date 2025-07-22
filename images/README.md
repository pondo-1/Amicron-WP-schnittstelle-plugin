# Images Directory

This directory contains article images uploaded through the MEC Shop API.

## Structure

- Images are saved with timestamps and unique IDs
- Filename format: `{field_key}_{timestamp}_{unique_id}.{extension}`
- Example: `artikel_image_2025-07-17_14-30-45_64b8f123456.jpg`

## Security

- Protected by `.htaccess` file
- Only image files are accessible
- PHP execution is disabled
- Directory listing is disabled

## Supported Formats

- JPG/JPEG
- PNG
- GIF
- BMP
- WebP
- SVG

## File Management

- Files are automatically created when images are uploaded via write_artikel action
- Old files should be cleaned up periodically to manage disk space
- Each image is logged with its full path in the application logs

## Access

- Images are accessible via direct URL: `https://yoursite.com/wp-content/plugins/mec-shop/images/filename.jpg`
- Access is restricted to image files only for security
