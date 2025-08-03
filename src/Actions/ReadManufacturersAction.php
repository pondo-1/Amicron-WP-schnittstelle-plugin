<?php

namespace MEC_AmicronSchnittstelle\Actions;

use MEC_AmicronSchnittstelle\Log\Logger;
use MEC_AmicronSchnittstelle\ShopEntities\Manufacturers;

class ReadManufacturersAction extends AbstractAction
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
        $manufacturers = new Manufacturers();
        //$manufacturers->addManufacturer(1, 'BMW');

        $xmlString = $manufacturers->generateXML();
        $this->logger->info('ReadManufacturers' . $xmlString);
        return $xmlString;
    }
}
