<?php

namespace MEC_AmicronSchnittstelle\ShopEntities;

use SimpleXMLElement;

class Categories
{
    private $categories = [];

    public function __construct()
    {
        // Initialize with WooCommerce categories if needed
        // $this->loadWooCommerceCategories();
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

    public function addCategory($id, $parentId, $names, $bild)
    {
        $this->categories[] = [
            'ID' => $id,
            'PARENT_ID' => $parentId,
            'NAMES' => $names,
            'BILD' => $bild
        ];
    }

    public function addCategoryName($categoryId, $languageId, $name)
    {
        foreach ($this->categories as &$category) {
            if ($category['ID'] == $categoryId) {
                $category['NAMES'][] = [
                    'LANGUAGEID' => $languageId,
                    'NAME' => $name
                ];
                break;
            }
        }
    }

    public function generateXML()
    {
        $xml = new SimpleXMLElement('<CATEGORIES/>');

        foreach ($this->categories as $category) {
            $categoryDataNode = $xml->addChild('CATEGORIES_DATA');
            $categoryDataNode->addChild('ID', $category['ID']);
            $categoryDataNode->addChild('PARENT_ID', $category['PARENT_ID']);

            $namesNode = $categoryDataNode->addChild('NAMES');
            foreach ($category['NAMES'] as $nameEntry) {
                $nameEntryNode = $namesNode->addChild('NAMEENTRY');
                $nameEntryNode->addChild('LANGUAGEID', $nameEntry['LANGUAGEID']);
                $nameEntryNode->addChild('NAME', $nameEntry['NAME']);
            }

            $categoryDataNode->addChild('BILD', $category['BILD']);
        }

        return $xml->asXML();
    }
}
