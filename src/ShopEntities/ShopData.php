<?php

namespace MEC_AmicronSchnittstelle\ShopEntities;

use SimpleXMLElement;

class ShopData
{
    private $taxRates = [];
    private $shippingStatus = [];
    private $customersStatus = [];

    // Setter-Methoden
    public function addTaxRate($id, $rate)
    {
        $this->taxRates[] = ['ID' => $id, 'RATE' => $rate];
    }

    public function addShippingStatus($id, $languageId, $name)
    {
        $this->shippingStatus[] = ['ID' => $id, 'LANGUAGEID' => $languageId, 'NAME' => $name];
    }

    public function addCustomerStatus($id, $languageId, $name)
    {
        $this->customersStatus[] = ['ID' => $id, 'LANGUAGEID' => $languageId, 'NAME' => $name];
    }

    // Methode zur Generierung des XML
    public function generateXML()
    {
        $xml = new SimpleXMLElement('<SHOPDATA/>');

        // TAXRATES
        $taxRatesNode = $xml->addChild('TAXRATES');
        foreach ($this->taxRates as $tax) {
            $taxNode = $taxRatesNode->addChild('TAX');
            $taxNode->addChild('ID', $tax['ID']);
            $taxNode->addChild('RATE', $tax['RATE']);
        }

        // SHIPPINGSTATUS
        $shippingStatusNode = $xml->addChild('SHIPPINGSTATUS');
        foreach ($this->shippingStatus as $status) {
            $statusNode = $shippingStatusNode->addChild('SHIPPINGSTATUS_DATA');
            $statusNode->addChild('ID', $status['ID']);
            $statusNode->addChild('LANGUAGEID', $status['LANGUAGEID']);
            $statusNode->addChild('NAME', $status['NAME']);
        }

        // CUSTOMERSSTATUS
        $customersStatusNode = $xml->addChild('CUSTOMERSSTATUS');
        foreach ($this->customersStatus as $status) {
            $statusNode = $customersStatusNode->addChild('CUSTOMERSSTATUS_DATA');
            $statusNode->addChild('ID', $status['ID']);
            $statusNode->addChild('LANGUAGEID', $status['LANGUAGEID']);
            $statusNode->addChild('NAME', $status['NAME']);
        }

        return $xml->asXML();
    }
}

// Beispielverwendung
// $shopData = new ShopData();
// $shopData->addTaxRate(1, 19.0);
// $shopData->addTaxRate(2, 7.0);
// $shopData->addShippingStatus(1, 1, 'Standard Shipping');
// $shopData->addShippingStatus(2, 2, 'Express Shipping');
// $shopData->addCustomerStatus(1, 1, 'Regular Customer');
// $shopData->addCustomerStatus(2, 2, 'Premium Customer');

// $xmlString = $shopData->generateXML();
// echo $xmlString;
