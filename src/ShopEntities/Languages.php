<?php

namespace MEC_AmicronSchnittstelle\ShopEntities;

use SimpleXMLElement;

class Languages
{
    private $languages = [];

    // Setter-Methoden
    public function addLanguage($id, $name)
    {
        $this->languages[] = ['ID' => $id, 'NAME' => $name];
    }

    // Methode zur Generierung des XML
    public function generateXML()
    {
        $xml = new SimpleXMLElement('<LANGUAGES/>');

        foreach ($this->languages as $language) {
            $languageDataNode = $xml->addChild('LANGUAGES_DATA');
            $languageDataNode->addChild('ID', $language['ID']);
            $languageDataNode->addChild('NAME', $language['NAME']);
        }

        return $xml->asXML();
    }
}
