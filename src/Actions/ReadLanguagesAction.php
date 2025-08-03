<?php

namespace MEC_AmicronSchnittstelle\Actions;

use MEC_AmicronSchnittstelle\Log\Logger;
use MEC_AmicronSchnittstelle\ShopEntities\Languages;


class ReadLanguagesAction extends AbstractAction
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
        $languages = new Languages();
        $languages->addLanguage(1, 'Deutsch');
        $xmlString = $languages->generateXML();

        $this->logger->info('ReadLanguages' . $xmlString);
        return $xmlString;
    }
}
