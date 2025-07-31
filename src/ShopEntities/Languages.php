<?php

namespace MEC_AmicronSchnittstelle\ShopEntities;

class Languages
{
    private $languages = [];

    public function __construct()
    {
        // Initialize with default language
        $this->addLanguage(1, 'English', 'en');

        // Add other languages from WordPress if WPML is active
        if (defined('ICL_LANGUAGE_CODE')) {
            $this->loadWPMLLanguages();
        }
    }

    private function loadWPMLLanguages()
    {
        global $sitepress;
        if ($sitepress) {
            $languages = $sitepress->get_active_languages();
            $id = 2; // Start from 2 since 1 is default English
            foreach ($languages as $lang) {
                if ($lang['code'] != 'en') { // Skip English as it's already added
                    $this->addLanguage($id, $lang['display_name'], $lang['code']);
                    $id++;
                }
            }
        }
    }

    public function addLanguage($id, $name, $code)
    {
        $this->languages[$id] = [
            'name' => $name,
            'code' => $code
        ];
    }

    public function generateXML()
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<SPRACHEN>' . "\n";

        foreach ($this->languages as $id => $language) {
            $xml .= "  <SPRACHE>\n";
            $xml .= "    <SPRACH_ID>$id</SPRACH_ID>\n";
            $xml .= "    <SPRACH_NAME>" . htmlspecialchars($language['name']) . "</SPRACH_NAME>\n";
            $xml .= "    <SPRACH_KENNUNG>" . htmlspecialchars($language['code']) . "</SPRACH_KENNUNG>\n";
            $xml .= "  </SPRACHE>\n";
        }

        $xml .= '</SPRACHEN>';
        return $xml;
    }
}
