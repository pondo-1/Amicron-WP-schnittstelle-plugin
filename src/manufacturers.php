<?php
/**
 * @author Stefan Witt <stefan.witt@rathje-design.de>
 */

class Manufacturers {
    private $manufacturers = [];

    public function addManufacturer($id, $name) {
        $this->manufacturers[] = [
            'ID' => $id,
            'NAME' => $name
        ];
    }

    public function generateXML() {
        $xml = new SimpleXMLElement('<MANUFACTURERS/>');

        foreach ($this->manufacturers as $manufacturer) {
            $manufacturerDataNode = $xml->addChild('MANUFACTURERS_DATA');
            $manufacturerDataNode->addChild('ID', $manufacturer['ID']);
            $manufacturerDataNode->addChild('NAME', $manufacturer['NAME']);
        }

        return $xml->asXML();
    }
}

// Beispielverwendung
// $manufacturers = new Manufacturers();
// $manufacturers->addManufacturer(1, 'Manufacturer A');
// $manufacturers->addManufacturer(2, 'Manufacturer B');

// $xmlString = $manufacturers->generateXML();
// header('Content-Type: application/xml');
// echo $xmlString;
?>
