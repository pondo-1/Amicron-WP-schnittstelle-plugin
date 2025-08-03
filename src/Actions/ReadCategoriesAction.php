<?php

namespace MEC_AmicronSchnittstelle\Actions;

use MEC_AmicronSchnittstelle\Log\Logger;
use MEC_AmicronSchnittstelle\ShopEntities\Categories;

class ReadCategoriesAction extends AbstractAction
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
        $categories = new Categories();
        // here you can add WP categories if you need to 
        //$categories->addCategoryName(1, 1, 'Category 1 - English');
        $xmlString = $categories->generateXML();
        $this->logger->info('ReadCategories' . $xmlString);
        return $xmlString;
    }
}
