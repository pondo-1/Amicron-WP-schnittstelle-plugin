<?php

namespace MEC_AmicronSchnittstelle\Actions;

use MEC_AmicronSchnittstelle\ShopEntities\ShopData;
use MEC_AmicronSchnittstelle\Log\Logger;

class ReadShopDataAction extends AbstractAction
{
    /**
     * Execute the action
     *
     * @param array $requestData The request data (not used in this action)
     * @return string XML response
     */

    public function __construct(Logger $logger)
    {
        parent::__construct($logger);
    }
    public function execute($requestData = [])
    {
        $shopData = new ShopData();
        $shopData->addTaxRate(1, 19.0);
        $shopData->addTaxRate(2, 7.0);
        $shopData->addShippingStatus(1, 1, 'Standard Shipping');
        $shopData->addShippingStatus(2, 2, 'Express Shipping');
        $shopData->addCustomerStatus(1, 1, 'Regular Customer');
        $shopData->addCustomerStatus(2, 2, 'Premium Customer');

        $xmlString = $shopData->generateXML();
        $this->logger->info('ReadShopData' . $xmlString);
        return $xmlString;
    }
}
