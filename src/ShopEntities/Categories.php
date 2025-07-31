<?php

namespace MEC_AmicronSchnittstelle\ShopEntities;

class Categories
{
    private $categories = [];

    public function __construct()
    {
        // Initialize with WooCommerce categories if needed
        $this->loadWooCommerceCategories();
    }

    private function loadWooCommerceCategories()
    {
        // Get WooCommerce product categories
        $args = array(
            'taxonomy'   => 'product_cat',
            'orderby'    => 'name',
            'hide_empty' => false,
        );

        $product_categories = get_terms($args);

        if (!empty($product_categories) && !is_wp_error($product_categories)) {
            foreach ($product_categories as $category) {
                $this->addCategoryName(
                    $category->term_id,      // Category ID
                    1,                       // Language ID (default to 1 for now)
                    $category->name          // Category Name
                );
            }
        }
    }

    public function addCategoryName($categoryId, $languageId, $name)
    {
        if (!isset($this->categories[$categoryId])) {
            $this->categories[$categoryId] = [];
        }
        $this->categories[$categoryId][$languageId] = $name;
    }

    public function generateXML()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<KATEGORIEN>' . "\n";

        foreach ($this->categories as $categoryId => $languages) {
            foreach ($languages as $languageId => $name) {
                $xml .= "  <KATEGORIE>\n";
                $xml .= "    <KAT_ID>$categoryId</KAT_ID>\n";
                $xml .= "    <SPRACH_ID>$languageId</SPRACH_ID>\n";
                $xml .= "    <KAT_NAME>" . htmlspecialchars($name) . "</KAT_NAME>\n";
                $xml .= "  </KATEGORIE>\n";
            }
        }

        $xml .= '</KATEGORIEN>';
        return $xml;
    }
}
