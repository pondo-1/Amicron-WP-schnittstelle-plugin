<?php

namespace MEC_AmicronSchnittstelle\ShopEntities;

class Manufacturers
{
    private $manufacturers = [];

    public function __construct()
    {
        // Initialize with WooCommerce manufacturers/brands if using a brands plugin
        $this->loadManufacturers();
    }

    private function loadManufacturers()
    {
        // Example using WooCommerce brands plugin taxonomy
        // Adjust the taxonomy based on your brand/manufacturer plugin
        $taxonomy = 'product_brand'; // or your specific taxonomy

        if (taxonomy_exists($taxonomy)) {
            $args = array(
                'taxonomy'   => $taxonomy,
                'orderby'    => 'name',
                'hide_empty' => false,
            );

            $manufacturers = get_terms($args);

            if (!empty($manufacturers) && !is_wp_error($manufacturers)) {
                foreach ($manufacturers as $manufacturer) {
                    $this->addManufacturer(
                        $manufacturer->term_id,
                        $manufacturer->name,
                        1 // Default language ID
                    );
                }
            }
        }
    }

    public function addManufacturer($id, $name, $languageId)
    {
        if (!isset($this->manufacturers[$id])) {
            $this->manufacturers[$id] = [];
        }
        $this->manufacturers[$id][$languageId] = $name;
    }

    public function generateXML()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<HERSTELLER>' . "\n";

        foreach ($this->manufacturers as $id => $languages) {
            foreach ($languages as $languageId => $name) {
                $xml .= "  <HERSTELLER_EINTRAG>\n";
                $xml .= "    <HERSTELLER_ID>$id</HERSTELLER_ID>\n";
                $xml .= "    <SPRACH_ID>$languageId</SPRACH_ID>\n";
                $xml .= "    <HERSTELLER_NAME>" . htmlspecialchars($name) . "</HERSTELLER_NAME>\n";
                $xml .= "  </HERSTELLER_EINTRAG>\n";
            }
        }

        $xml .= '</HERSTELLER>';
        return $xml;
    }
}
