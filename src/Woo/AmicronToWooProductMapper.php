<?php

namespace MEC_AmicronSchnittstelle\Woo;

use MEC_AmicronSchnittstelle\Log\LogManager;

class AmicronToWooProductMapper
{
    private $mappingConfig;
    private $logger;

    public function __construct()
    {
        $this->logger = LogManager::getSummaryLogger();
        $this->loadMappingConfig();
    }

    private function loadMappingConfig()
    {
        $configPath = dirname(__DIR__) . '/Woo/amicron_woo_mapping.json';
        if (file_exists($configPath)) {
            $content = file_get_contents($configPath);
            $this->mappingConfig = json_decode($content, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \Exception("Error parsing mapping configuration: " . json_last_error_msg());
            }
        } else {
            throw new \Exception("Mapping configuration file not found at: $configPath");
        }
    }

    /**
     * Convert Amicron article data to WooCommerce product data
     */
    public function mapToWooProduct(array $artikelData): array
    {
        $productData = [
            'manage_stock' => true, // Always true for Amicron products
        ];

        // Map direct fields
        foreach ($this->mappingConfig['direct_mappings'] as $wooField => $amicronField) {
            if (isset($artikelData[$amicronField])) {
                $productData[$wooField] = $artikelData[$amicronField];
            }
        }

        // Map product type (simple/variable)
        [
            $productData['type'],
            $productData['attributes']
        ]  = $this->determineProductType($artikelData);

        // Map taxonomies
        $productData['taxonomies'] = $this->mapTaxonomies($artikelData);


        // Map meta fields
        $productData['meta_data'] = $this->mapMetaFields($artikelData);

        // Map images
        $productData['images'] = $this->mapImages($artikelData);

        $this->logger->info("Mapped product data: " . json_encode($productData));

        return $productData;
    }

    private function determineProductType(array $artikelData)
    {
        $config = $this->mappingConfig['complex_fields']['type'];

        if ($artikelData[$config['source']] == '') {
            // Simple product with no additional info
            return ["simple", []];
        } elseif (str_ends_with($artikelData[$config['source']], 'M')) {
            // Variant Product with Parent SKU
            return ["variant", [
                'parent' => $artikelData[$config['source']],
                'options' => []????
            ]]; 


        } elseif (strpos($artikelData[$config['source']], ';') !== false) {
            // Variable product with multiple attributes
            $values = explode(';', $artikelData[$config['source']]);
            return ["variable", "attributes" => [
                'name' => $values[2],
                'options' => $this->mapOptions($artikelData),
                'visible' => true,
                'variation' => true
            ]];
        }
    }

    private function mapTaxonomies(array $artikelData): array
    {
        $taxonomies = [];
        foreach ($this->mappingConfig['taxonomies'] as $taxonomy => $field) {
            if (isset($artikelData[$field])) {
                $taxonomies[$taxonomy] = $artikelData[$field];
            }
        }
        return $taxonomies;
    }

    private function mapOptions(array $artikelData): array
    {
        $config = $this->mappingConfig['complex_fields']['attributes'];
        $options = explode($config['options_delimiter'], $artikelData[$config['options_source']]);

        return $options;
    }

    private function mapMetaFields(array $artikelData): array
    {
        $metaData = [];
        foreach ($this->mappingConfig['meta_fields'] as $metaKey => $field) {
            if (isset($artikelData[$field])) {
                $metaData[] = [
                    'key' => $metaKey,
                    'value' => $artikelData[$field]
                ];
            }
        }
        return $metaData;
    }

    private function mapImages(array $artikelData): array
    {
        $images = [];
        // Handle image mapping logic here
        // This might involve looking up image IDs in the media library
        // or processing image filenames from artikel_imagesDateiname0
        if (isset($artikelData['artikel_imagesDateiname0'])) {
            // TODO: Implement image handling logic
            $this->logger->info("Image mapping needed for: " . $artikelData['artikel_imagesDateiname0']);
        }
        return $images;
    }

    /**
     * Get mapped field from Amicron data
     */
    public function getAmicronField(string $wooField): ?string
    {
        return $this->mappingConfig['direct_mappings'][$wooField] ?? null;
    }

    /**
     * Check if a WooCommerce field has a mapping
     */
    public function hasMapping(string $wooField): bool
    {
        return isset($this->mappingConfig['direct_mappings'][$wooField]);
    }
}
