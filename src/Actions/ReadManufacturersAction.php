<?php

require_once 'AbstractAction.php';
require_once __DIR__ . '/../manufacturers.php';

class ReadManufacturersAction extends AbstractAction
{
    /**
     * Execute the action
     *
     * @param array $requestData The request data (not used in this action)
     * @return string XML response
     */
    public function execute($requestData = [])
    {
        $manufacturers = new Manufacturers();
        //$manufacturers->addManufacturer(1, 'BMW');

        $xmlString = $manufacturers->generateXML();
        $this->logger->info('ReadManufacturers' . $xmlString);
        return $xmlString;
    }
}
