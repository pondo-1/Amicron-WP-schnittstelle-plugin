<?php

namespace MEC_AmicronSchnittstelle\Woo;

use MEC_AmicronSchnittstelle\Log\LogManager;

class AmicronToWooProductMapper
{
    private $mappingConfig;
    private $woo_productdata;
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
            $all = json_decode($content, true);
            $this->mappingConfig = $all["mapping_config"];
            $this->woo_productdata = $all["woo_product"];
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
        $productData = $this->woo_productdata;

        // Map direct fields
        foreach ($this->mappingConfig['direct'] as $wooField => $amicronField) {
            if (isset($artikelData[$amicronField])) {
                $productData[$wooField] = $artikelData[$amicronField];
            }
        }

        // Map product type (simple/variable)
        [$productData['product_type'], $productData['product_info']]  = $this->determineProductType($artikelData);

        // Map taxonomies
        $productData['taxonomies'] = $this->mapTaxonomies($artikelData);


        // Map meta fields
        $productData['meta_fields'] = $this->mapMetaFields($artikelData);

        // Map images
        // $productData['images'] = $this->mapImages($artikelData);


        return $productData;
    }

    private function determineProductType(array $artikelData)
    {
        $config = $this->mappingConfig['complex']['type'];

        if ($artikelData[$config['source']] == '') {
            // Simple product with no additional info
            return ["simple", []];
        } elseif (strpos($artikelData[$config['source']], ';') !== false) {
            // Variable product with available options
            $options = $this->determineOptions($artikelData);
            return ["variable", ["option" => $options]];
        } elseif (str_ends_with($artikelData[$config['source']], 'M')) {
            // Variant Product with Parent SKU
            $info = $this->determineVariantInfo($artikelData);
            return ["variant", ["attribute" => $info]];
        }
    }
    private function determineVariantInfo(array $artikelData)
    {
        $config = $this->mappingConfig['complex']['variant_info'];
        $variant_info = [
            "parent_sku" => $artikelData[$config['parent_sku']],
            "value" => end(explode("\n", $artikelData[$config['attribute_source']]))
        ];
        return $variant_info;
    }

    private function determineOptions(array $artikelData)
    {
        $config = $this->mappingConfig['complex']['attributes'];
        $options_info = [
            "name" => explode(";", $artikelData[$config["source"]])[2],
        ];
        return $options_info;
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


    private function mapMetaFields(array $artikelData): array
    {
        $metaData = [];
        foreach ($this->mappingConfig['meta_fields'] as $metaKey => $field) {
            if ($metaKey === 'compatible') {
                $metaData[$metaKey] = json_encode(preg_split('/;\r\n|\n/', rtrim($artikelData[$field], ';')));
            } else if (isset($artikelData[$field])) {
                $metaData[$metaKey] = $artikelData[$field];
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
        return $this->mappingConfig['direct'][$wooField] ?? null;
    }

    /**
     * Check if a WooCommerce field has a mapping
     */
    public function hasMapping(string $wooField): bool
    {
        return isset($this->mappingConfig['direct'][$wooField]);
    }
}
