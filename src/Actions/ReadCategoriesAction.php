<?php

namespace MEC_AmicronSchnittstelle\Actions;

use MEC_AmicronSchnittstelle\Models\Categories;

class ReadCategoriesAction extends AbstractAction
{
    /**
     * Execute the action
     *
     * @param array $requestData The request data (not used in this action)
     * @return string XML response
     */
    public function execute($requestData = [])
    {
        $categories = new Categories();

        $xmlString = $categories->generateXML();
        $this->logger->info('ReadCategories' . $xmlString);
        return $xmlString;
    }
}
