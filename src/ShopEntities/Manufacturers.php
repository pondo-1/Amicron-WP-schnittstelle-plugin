<?php

namespace MEC_AmicronSchnittstelle\ShopEntities;

use SimpleXMLElement;

class Manufacturers
{
    private $manufacturers = [];

    public function __construct()
    {
        // Initialize with WooCommerce manufacturers/brands if using a brands plugin
        // $this->loadManufacturers();
    }

    // private function loadManufacturers()
    // {
    //     // Example using WooCommerce brands plugin taxonomy
    //     // Adjust the taxonomy based on your brand/manufacturer plugin
    //     $taxonomy = 'product_brand'; // or your specific taxonomy

    //     if (taxonomy_exists($taxonomy)) {
    //         $args = array(
    //             'taxonomy'   => $taxonomy,
    //             'orderby'    => 'name',
    //             'hide_empty' => false,
    //         );

    //         $manufacturers = get_terms($args);

    //         if (!empty($manufacturers) && !is_wp_error($manufacturers)) {
    //             foreach ($manufacturers as $manufacturer) {
    //                 $this->addManufacturer(
    //                     $manufacturer->term_id,
    //                     $manufacturer->name,
    //                     1 // Default language ID
    //                 );
    //             }
    //         }
    //     }
    // }

    public function addManufacturer($id, $name)
    {
        $this->manufacturers[] = [
            'ID' => $id,
            'NAME' => $name
        ];
    }

    public function generateXML()
    {
        $xml = new SimpleXMLElement('<MANUFACTURERS/>');

        foreach ($this->manufacturers as $manufacturer) {
            $manufacturerDataNode = $xml->addChild('MANUFACTURERS_DATA');
            $manufacturerDataNode->addChild('ID', $manufacturer['ID']);
            $manufacturerDataNode->addChild('NAME', $manufacturer['NAME']);
        }

        return $xml->asXML();
    }
}
