<?php

namespace MEC_AmicronSchnittstelle\Woo;

use Exception;
use WC_Product_Variable;
use WC_Product_Simple;
use WC_Product_Attribute;

class WooProductUpdater
{
    /**
     * Update or create a WooCommerce product using SKU as identifier
     *
     * @param array $productData Array containing product data
     * @return int|WP_Error Product ID on success, WP_Error on failure
     */

    public function execute($requestData = []) {}
    public function updateProductBySku(array $productData)
    {
        try {
            // Get product ID by SKU
            $product_id = wc_get_product_id_by_sku($productData['sku']);

            // Get or create product object
            $product = $product_id ? wc_get_product($product_id) : null;

            if (!$product) {
                // Create new product based on product type
                $product = $productData['product_type'] === 'variable'
                    ? new WC_Product_Variable()
                    : new WC_Product_Simple();

                $product->set_sku($productData['sku']);
            }

            // Update basic product data
            $this->updateBasicProductData($product, $productData);

            // Update taxonomies
            if (!empty($productData['taxonomies'])) {
                $this->updateProductTaxonomies($product, $productData['taxonomies']);
            }

            // Update meta fields
            if (!empty($productData['meta_fields'])) {
                $this->updateProductMetaFields($product, $productData['meta_fields']);
            }

            // Save the product
            $product_id = $product->save();

            // Handle product options/variations if it's a variable product
            if ($productData['product_type'] === 'variable' && !empty($productData['product_info']['option'])) {
                $this->handleProductAttributes($product, $productData['product_info']['option']);
            }

            return $product_id;
        } catch (Exception $e) {
            return new \WP_Error('product_update_error', $e->getMessage());
        }
    }

    /**
     * Update basic product data
     */
    private function updateBasicProductData($product, $data)
    {
        // Set basic product data
        $product->set_name($data['name']);
        $product->set_status('publish');

        if (isset($data['description'])) {
            $product->set_description($data['description']);
        }

        if (isset($data['short_description'])) {
            $product->set_short_description($data['short_description']);
        }

        // Price handling
        if (!empty($data['regular_price'])) {
            $product->set_regular_price($data['regular_price']);
        }
        // if (!empty($data['price'])) {
        //     $product->set_price($data['price']);
        // }

        // Stock handling
        if (isset($data['manage_stock'])) {
            $product->set_manage_stock($data['manage_stock']);

            if ($data['manage_stock']) {
                if (isset($data['stock_quantity'])) {
                    $product->set_stock_quantity($data['stock_quantity']);
                }
                if (!empty($data['stock_status'])) {
                    $product->set_stock_status($data['stock_status']);
                }
            }
        }
    }

    /**
     * Update product taxonomies
     */
    private function updateProductTaxonomies($product, $taxonomies)
    {
        foreach ($taxonomies as $taxonomy => $terms) {
            if (!empty($terms)) {
                $term_ids = [];

                // Handle single term or array of terms
                $terms_array = is_array($terms) ? $terms : [$terms];

                foreach ($terms_array as $term_name) {
                    // Get or create term
                    $term = get_term_by('name', $term_name, $taxonomy);
                    if (!$term) {
                        $result = wp_insert_term($term_name, $taxonomy);
                        if (!is_wp_error($result)) {
                            $term_ids[] = $result['term_id'];
                        }
                    } else {
                        $term_ids[] = $term->term_id;
                    }
                }

                if (!empty($term_ids)) {
                    wp_set_object_terms($product->get_id(), $term_ids, $taxonomy);
                }
            }
        }
    }

    /**
     * Update product meta fields
     */
    private function updateProductMetaFields($product, $meta_fields)
    {
        foreach ($meta_fields as $meta_key => $meta_value) {
            $product->update_meta_data($meta_key, $meta_value);
        }
    }

    /**
     * Handle product attributes for variable products
     */
    private function handleProductAttributes($product, $option)
    {
        if (empty($option['name'])) {
            return;
        }

        $attribute = new \WC_Product_Attribute();
        $attribute->set_name($option['name']);
        $attribute->set_visible(true);
        $attribute->set_variation(true);

        // If values are provided for the attribute
        if (!empty($option['values'])) {
            $attribute->set_options($option['values']);
        }

        $product->set_attributes(array($attribute));
    }
}
