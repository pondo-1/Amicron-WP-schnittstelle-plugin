<?php
/**
 * @author Stefan Witt <stefan.witt@rathje-design.de>
 */
require_once 'AbstractAction.php';
require_once __DIR__ . '/../categories.php';

class ReadCategoriesAction extends AbstractAction {
    /**
     * Execute the action
     *
     * @param array $requestData The request data (not used in this action)
     * @return string XML response
     */
    public function execute($requestData = []) {
        $categories = new Categories();
        //$categories->addCategoryName(1, 1, 'Category 1 - English');

        $xmlString = $categories->generateXML();
        $this->logger->info('ReadCategories' . $xmlString);
        return $xmlString;
    }
}
?>