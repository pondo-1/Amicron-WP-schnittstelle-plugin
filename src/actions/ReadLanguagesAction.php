<?php
/**
 * @author Stefan Witt <stefan.witt@rathje-design.de>
 */
require_once 'AbstractAction.php';
require_once __DIR__ . '/../languages.php';

class ReadLanguagesAction extends AbstractAction {
    /**
     * Execute the action
     *
     * @param array $requestData The request data (not used in this action)
     * @return string XML response
     */
    public function execute($requestData = []) {
        $languages = new Languages();
        $languages->addLanguage(1, 'Deutsch');
        $xmlString = $languages->generateXML();

        $this->logger->info('ReadLanguages' . $xmlString);
        return $xmlString;
    }
}
?>