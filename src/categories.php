<?php
/**
 * @author Stefan Witt <stefan.witt@rathje-design.de>
 */

class Categories {
    private $categories = [];

    public function addCategory($id, $parentId, $names, $bild) {
        $this->categories[] = [
            'ID' => $id,
            'PARENT_ID' => $parentId,
            'NAMES' => $names,
            'BILD' => $bild
        ];
    }

    public function addCategoryName($categoryId, $languageId, $name) {
        foreach ($this->categories as &$category) {
            if ($category['ID'] == $categoryId) {
                $category['NAMES'][] = [
                    'LANGUAGEID' => $languageId,
                    'NAME' => $name
                ];
                break;
            }
        }
    }

    public function generateXML() {
        $xml = new SimpleXMLElement('<CATEGORIES/>');

        foreach ($this->categories as $category) {
            $categoryDataNode = $xml->addChild('CATEGORIES_DATA');
            $categoryDataNode->addChild('ID', $category['ID']);
            $categoryDataNode->addChild('PARENT_ID', $category['PARENT_ID']);
            
            $namesNode = $categoryDataNode->addChild('NAMES');
            foreach ($category['NAMES'] as $nameEntry) {
                $nameEntryNode = $namesNode->addChild('NAMEENTRY');
                $nameEntryNode->addChild('LANGUAGEID', $nameEntry['LANGUAGEID']);
                $nameEntryNode->addChild('NAME', $nameEntry['NAME']);
            }

            $categoryDataNode->addChild('BILD', $category['BILD']);
        }

        return $xml->asXML();
    }
}
