<?php

namespace MEC_AmicronSchnittstelle\DTO;

use MEC_AmicronSchnittstelle\Config\FieldMappingConfig;

class ArticleDTO extends AbstractDTO
{
    // Field name constants
    const FIELD_ID = 'Artikel_ID';
    const FIELD_ARTICLE_NUMBER = 'Artikel_Artikelnr';
    const FIELD_NAME = 'Artikel_Bezeichnung1';
    const FIELD_DESCRIPTION = 'Artikel_Text1';
    const FIELD_SHORT_DESCRIPTION = 'Artikel_Kurztext1';
    const FIELD_PRICE = 'Artikel_Preis';
    const FIELD_TAX_RATE = 'Artikel_Steuersatz';
    const FIELD_STATUS = 'Artikel_Status';
    const FIELD_WEIGHT = 'Artikel_Gewicht';
    const FIELD_QUANTITY = 'Artikel_Menge';
    const FIELD_CATEGORY_ID = 'Artikel_KategorieID1';
    const FIELD_CATEGORY_ID2 = 'Artikel_KategorieID2';
    const FIELD_CATEGORY_ID3 = 'Artikel_KategorieID3';
    const FIELD_MANUFACTURER_ID = 'Hersteller_ID';
    const FIELD_MANUFACTURER_NAME = 'Feld_HSNAME';
    const FIELD_META_TITLE = 'Artikel_MetaTitle1';
    const FIELD_META_DESCRIPTION = 'Artikel_MetaDescription1';
    const FIELD_META_KEYWORDS = 'Artikel_MetaKeywords1';
    const FIELD_URL = 'Artikel_URL1';
    const FIELD_EAN = 'Artikel_EAN';
    const FIELD_MPN = 'Artikel_MPN';
    const FIELD_DELIVERY_STATUS = 'Artikel_Lieferstatus';
    const FIELD_DELIVERY_STATUS_TEXT = 'Artikel_Lieferstatustext';
    const FIELD_SHOW_ON_HOMEPAGE = 'Artikel_Startseite';
    const FIELD_TEXT_LANGUAGE = 'Artikel_TextLanguage1';
    const FIELD_DB_TEXT1 = 'Artikel_DBText1';
    const FIELD_DB_TEXT2 = 'Artikel_DBText2';
    const FIELD_DB_TEXT3 = 'Artikel_DBText3';
    const FIELD_DB_TEXT4 = 'Artikel_DBText4';
    const FIELD_DB_TEXT5 = 'Artikel_DBText5';
    const FIELD_DB_TEXT6 = 'Artikel_DBText6';
    const FIELD_DB_TEXT7 = 'Artikel_DBText7';
    const FIELD_DB_TEXT8 = 'Artikel_DBText8';
    const FIELD_RABATTGRUPPE = 'Artikel_Rabattgruppe';
    const FIELD_MENGENEINHEIT = 'Artikel_Mengeneinheit';
    const FIELD_LIEFERZEIT_TAGE = 'Artikel_LieferzeitTage';
    const FIELD_LIEFERZEIT_TEXT = 'Artikel_LieferzeitText';
    const FIELD_BILDDATEIEN = 'Artikel_Bilddateien';
    const FIELD_IMAGES_DATEINAME0 = 'artikel_imagesDateiname0';
    const FIELD_SKIP_IMAGES = 'SkipImages';
    const FIELD_EXPORT_MODUS = 'ExportModus';

    // Feld_ constants for additional fields
    const FIELD_LFDNR = 'Feld_LFDNR';
    const FIELD_ARTIKELNR = 'Feld_ARTIKELNR';
    const FIELD_BEMERKUNG = 'Feld_BEMERKUNG';
    const FIELD_GEAENDERTAM = 'Feld_GEAENDERTAM';
    const FIELD_UMSATZMENGE = 'Feld_UMSATZMENGE';
    const FIELD_UMSATZ = 'Feld_UMSATZ';
    const FIELD_PROVISION = 'Feld_PROVISION';
    const FIELD_GEWICHT_FELD = 'Feld_GEWICHT';
    const FIELD_BESTELLMENGE = 'Feld_BESTELLMENGE';
    const FIELD_LETZTERVERKAUF = 'Feld_LETZTERVERKAUF';
    const FIELD_BESTANDDATUM = 'Feld_BESTANDDATUM';
    const FIELD_BESTANDMINDEST = 'Feld_BESTANDMINDEST';
    const FIELD_VK1 = 'Feld_VK1';
    const FIELD_VK1BRUTTO = 'Feld_VK1BRUTTO';
    const FIELD_EKPREIS = 'Feld_EKPREIS';
    const FIELD_EKLETZTER = 'Feld_EKLETZTER';
    const FIELD_PREISEINHEIT = 'Feld_PREISEINHEIT';
    const FIELD_TEXT = 'Feld_TEXT';
    const FIELD_BILDANZEIGE = 'Feld_BILDANZEIGE';
    const FIELD_RABATTGRUPPE_FELD = 'Feld_RABATTGRUPPE';
    const FIELD_BEZEICHNUNG = 'Feld_BEZEICHNUNG';
    const FIELD_PASSIVERARTIKEL = 'Feld_PASSIVERARTIKEL';
    const FIELD_GEAENDERTVON = 'Feld_GEAENDERTVON';
    const FIELD_UMSATZFUEHREN = 'Feld_UMSATZFUEHREN';
    const FIELD_BESTANDFUEHREN = 'Feld_BESTANDFUEHREN';
    const FIELD_STEUERSATZ_FELD = 'Feld_STEUERSATZ';
    const FIELD_MENGENEINHEIT_FELD = 'Feld_MENGENEINHEIT';
    const FIELD_RESERVIERT = 'Feld_RESERVIERT';
    const FIELD_SHOPARTIKEL = 'Feld_SHOPARTIKEL';
    const FIELD_TEXT2 = 'Feld_TEXT2';
    const FIELD_TEXT3 = 'Feld_TEXT3';
    const FIELD_TEXT4 = 'Feld_TEXT4';
    const FIELD_TEXT5 = 'Feld_TEXT5';
    const FIELD_TEXT6 = 'Feld_TEXT6';
    const FIELD_TEXT7 = 'Feld_TEXT7';
    const FIELD_TEXT8 = 'Feld_TEXT8';
    const FIELD_LIEFERANTARTIKELNR = 'Feld_LIEFERANTARTIKELNR';
    const FIELD_GRUPPELFDNR = 'Feld_GRUPPELFDNR';
    const FIELD_LAGERLFDNR = 'Feld_LAGERLFDNR';
    const FIELD_LIEFERANTLFDNR = 'Feld_LIEFERANTLFDNR';
    const FIELD_EAN_FELD = 'Feld_EAN';
    const FIELD_EKMITTEL = 'Feld_EKMITTEL';
    const FIELD_SHOPID = 'Feld_SHOPID';
    const FIELD_SUCHARTIKELNR = 'Feld_SUCHARTIKELNR';
    const FIELD_ART = 'Feld_ART';
    const FIELD_LIEFERZEITTAGE_FELD = 'Feld_LIEFERZEITTAGE';
    const FIELD_LIEFERZEITTEXT_FELD = 'Feld_LIEFERZEITTEXT';
    const FIELD_VERPACKUNGSEINHEIT = 'Feld_VERPACKUNGSEINHEIT';
    const FIELD_BEZEICHNUNG2 = 'Feld_BEZEICHNUNG2';
    const FIELD_BEZEICHNUNG3 = 'Feld_BEZEICHNUNG3';
    const FIELD_BEZEICHNUNG4 = 'Feld_BEZEICHNUNG4';
    const FIELD_BEZEICHNUNG5 = 'Feld_BEZEICHNUNG5';
    const FIELD_BEZEICHNUNG6 = 'Feld_BEZEICHNUNG6';
    const FIELD_BEZEICHNUNG7 = 'Feld_BEZEICHNUNG7';
    const FIELD_BEZEICHNUNG8 = 'Feld_BEZEICHNUNG8';
    const FIELD_SHOPLIEFERSTATUS = 'Feld_SHOPLIEFERSTATUS';
    const FIELD_SHOPLIEFERSTATUSTEXT = 'Feld_SHOPLIEFERSTATUSTEXT';
    const FIELD_GRUNDEINHEIT = 'Feld_GRUNDEINHEIT';
    const FIELD_MASSEINHEIT = 'Feld_MASSEINHEIT';
    const FIELD_GRUNDGEWICHT = 'Feld_GRUNDGEWICHT';
    const FIELD_BESTAND = 'Feld_BESTAND';
    const FIELD_VERFUEGBAR = 'Feld_clcVERFUEGBAR';
    const FIELD_PLUS = 'Feld_clcPLUS';
    const FIELD_GRUNDPREISNETTO = 'Feld_clcGRUNDPREISNETTO';
    const FIELD_GRUNDPREISBRUTTO = 'Feld_clcGRUNDPREISBRUTTO';
    const FIELD_LAGER = 'Feld_LAGER';
    const FIELD_LIEFERANTNR = 'Feld_LIEFERANTNR';
    const FIELD_HSNAME = 'Feld_HSNAME';

    // Group fields
    const FIELD_GRUPPE1 = 'Feld_Gruppe1';
    const FIELD_GRPART1 = 'Feld_GrpArt1';
    const FIELD_BEZEICHNUNG1_1 = 'Feld_Bezeichnung1_1';
    const FIELD_BEZEICHNUNG2_1 = 'Feld_Bezeichnung2_1';
    const FIELD_BEZEICHNUNG3_1 = 'Feld_Bezeichnung3_1';
    const FIELD_BEZEICHNUNG4_1 = 'Feld_Bezeichnung4_1';
    const FIELD_BEZEICHNUNG5_1 = 'Feld_Bezeichnung5_1';

    // Basic article data
    public $id;
    public $articleNumber;
    public $name;
    public $description;
    public $shortDescription;
    public $price;
    public $taxRate;
    public $status;
    public $weight;
    public $quantity;

    // Categories and manufacturers
    public $categoryId;
    public $categoryId2;
    public $categoryId3;
    public $manufacturerId;
    public $manufacturerName;

    // SEO and shop data
    public $metaTitle;
    public $metaDescription;
    public $metaKeywords;
    public $url;
    public $ean;
    public $mpn;
    public $deliveryStatus;
    public $deliveryStatusText;
    public $showOnHomepage;

    // Additional language and text fields
    public $textLanguage;
    public $dbText1;
    public $dbText2;
    public $dbText3;
    public $dbText4;
    public $dbText5;
    public $dbText6;
    public $dbText7;
    public $dbText8;

    // Additional article properties
    public $rabattgruppe;
    public $mengeneinheit;
    public $lieferzeitTage;
    public $lieferzeitText;
    public $bilddateien;
    public $imagesDateiname0;
    public $skipImages;

    // Additional properties for all new fields
    public $exportModus;
    public $lfdnr;
    public $artikelnrFeld;
    public $bemerkung;
    public $geaendertam;
    public $umsatzmenge;
    public $umsatz;
    public $provision;
    public $gewichtFeld;
    public $bestellmenge;
    public $letzterverkauf;
    public $bestanddatum;
    public $bestandmindest;
    public $vk1;
    public $vk1brutto;
    public $ekpreis;
    public $ekletzter;
    public $preiseinheit;
    public $textFeld;
    public $bildanzeige;
    public $rabattgruppeFeld;
    public $bezeichnungFeld;
    public $passiverartikel;
    public $geaendertvon;
    public $umsatzfuehren;
    public $bestandfuehren;
    public $steuersatzFeld;
    public $mengeneinheitFeld;
    public $reserviert;
    public $shopartikel;
    public $text2;
    public $text3;
    public $text4;
    public $text5;
    public $text6;
    public $text7;
    public $text8;
    public $lieferantartikelnr;
    public $gruppelfdnr;
    public $lagerlfdnr;
    public $lieferantlfdnr;
    public $eanFeld;
    public $ekmittel;
    public $shopid;
    public $suchartikelnr;
    public $art;
    public $lieferzeittage_feld;
    public $lieferzeittext_feld;
    public $verpackungseinheit;
    public $bezeichnung2;
    public $bezeichnung3;
    public $bezeichnung4;
    public $bezeichnung5;
    public $bezeichnung6;
    public $bezeichnung7;
    public $bezeichnung8;
    public $shoplieferstatus;
    public $shoplieferstatustext;
    public $grundeinheit;
    public $masseinheit;
    public $grundgewicht;
    public $bestand;
    public $verfuegbar;
    public $plus;
    public $grundpreisnetto;
    public $grundpreisbrutto;
    public $lager;
    public $lieferantnr;
    public $hsnameFeld;

    // Group fields
    public $gruppe1;
    public $grpart1;
    public $bezeichnung1_1;
    public $bezeichnung2_1;
    public $bezeichnung3_1;
    public $bezeichnung4_1;
    public $bezeichnung5_1;

    // Additional fields
    public $freeFields = [];
    public $additionalFields = [];

    /**
     * Creates an ArticleDTO from an array of article data
     *
     * @param array $data The article data
     * @return ArticleDTO
     */
    public static function fromArray(array $data): ArticleDTO
    {
        $dto = new self();

        // Basic article data
        $dto->id = $data[self::FIELD_ID] ?? 0;
        $dto->articleNumber = $data[self::FIELD_ARTICLE_NUMBER] ?? '';
        $dto->name = $data[self::FIELD_NAME] ?? '';
        $dto->description = $data[self::FIELD_DESCRIPTION] ?? '';
        $dto->shortDescription = $data[self::FIELD_SHORT_DESCRIPTION] ?? '';
        $dto->price = $data[self::FIELD_PRICE] ?? 0;
        $dto->taxRate = $data[self::FIELD_TAX_RATE] ?? 0;
        $dto->status = $data[self::FIELD_STATUS] ?? 0;
        $dto->weight = $data[self::FIELD_WEIGHT] ?? 0;
        $dto->quantity = $data[self::FIELD_QUANTITY] ?? 0;

        // Categories and manufacturers
        $dto->categoryId = $data[self::FIELD_CATEGORY_ID] ?? 0;
        $dto->categoryId2 = $data[self::FIELD_CATEGORY_ID2] ?? 0;
        $dto->categoryId3 = $data[self::FIELD_CATEGORY_ID3] ?? 0;
        $dto->manufacturerId = $data[self::FIELD_MANUFACTURER_ID] ?? 0;
        $dto->manufacturerName = $data[self::FIELD_MANUFACTURER_NAME] ?? '';

        // SEO and shop data
        $dto->metaTitle = $data[self::FIELD_META_TITLE] ?? '';
        $dto->metaDescription = $data[self::FIELD_META_DESCRIPTION] ?? '';
        $dto->metaKeywords = $data[self::FIELD_META_KEYWORDS] ?? '';
        $dto->url = $data[self::FIELD_URL] ?? '';
        $dto->ean = $data[self::FIELD_EAN] ?? '';
        $dto->mpn = $data[self::FIELD_MPN] ?? '';
        $dto->deliveryStatus = $data[self::FIELD_DELIVERY_STATUS] ?? '';
        $dto->deliveryStatusText = $data[self::FIELD_DELIVERY_STATUS_TEXT] ?? '';
        $dto->showOnHomepage = $data[self::FIELD_SHOW_ON_HOMEPAGE] ?? 0;

        // Additional language and text fields
        $dto->textLanguage = $data[self::FIELD_TEXT_LANGUAGE] ?? '';
        $dto->dbText1 = $data[self::FIELD_DB_TEXT1] ?? '';
        $dto->dbText2 = $data[self::FIELD_DB_TEXT2] ?? '';
        $dto->dbText3 = $data[self::FIELD_DB_TEXT3] ?? '';
        $dto->dbText4 = $data[self::FIELD_DB_TEXT4] ?? '';
        $dto->dbText5 = $data[self::FIELD_DB_TEXT5] ?? '';
        $dto->dbText6 = $data[self::FIELD_DB_TEXT6] ?? '';
        $dto->dbText7 = $data[self::FIELD_DB_TEXT7] ?? '';
        $dto->dbText8 = $data[self::FIELD_DB_TEXT8] ?? '';

        // Additional article properties
        $dto->rabattgruppe = $data[self::FIELD_RABATTGRUPPE] ?? '';
        $dto->mengeneinheit = $data[self::FIELD_MENGENEINHEIT] ?? '';
        $dto->lieferzeitTage = $data[self::FIELD_LIEFERZEIT_TAGE] ?? 0;
        $dto->lieferzeitText = $data[self::FIELD_LIEFERZEIT_TEXT] ?? '';
        $dto->bilddateien = $data[self::FIELD_BILDDATEIEN] ?? 0;
        $dto->imagesDateiname0 = $data[self::FIELD_IMAGES_DATEINAME0] ?? '';
        $dto->skipImages = $data[self::FIELD_SKIP_IMAGES] ?? 0;

        // Additional new fields
        $dto->exportModus = $data[self::FIELD_EXPORT_MODUS] ?? '';
        $dto->lfdnr = $data[self::FIELD_LFDNR] ?? '';
        $dto->artikelnrFeld = $data[self::FIELD_ARTIKELNR] ?? '';
        $dto->bemerkung = $data[self::FIELD_BEMERKUNG] ?? '';
        $dto->geaendertam = $data[self::FIELD_GEAENDERTAM] ?? '';
        $dto->umsatzmenge = $data[self::FIELD_UMSATZMENGE] ?? 0;
        $dto->umsatz = $data[self::FIELD_UMSATZ] ?? 0;
        $dto->provision = $data[self::FIELD_PROVISION] ?? 0;
        $dto->gewichtFeld = $data[self::FIELD_GEWICHT_FELD] ?? 0;
        $dto->bestellmenge = $data[self::FIELD_BESTELLMENGE] ?? 0;
        $dto->letzterverkauf = $data[self::FIELD_LETZTERVERKAUF] ?? '';
        $dto->bestanddatum = $data[self::FIELD_BESTANDDATUM] ?? '';
        $dto->bestandmindest = $data[self::FIELD_BESTANDMINDEST] ?? 0;
        $dto->vk1 = $data[self::FIELD_VK1] ?? 0;
        $dto->vk1brutto = $data[self::FIELD_VK1BRUTTO] ?? 0;
        $dto->ekpreis = $data[self::FIELD_EKPREIS] ?? 0;
        $dto->ekletzter = $data[self::FIELD_EKLETZTER] ?? 0;
        $dto->preiseinheit = $data[self::FIELD_PREISEINHEIT] ?? 0;
        $dto->textFeld = $data[self::FIELD_TEXT] ?? '';
        $dto->bildanzeige = $data[self::FIELD_BILDANZEIGE] ?? 0;
        $dto->rabattgruppeFeld = $data[self::FIELD_RABATTGRUPPE_FELD] ?? '';
        $dto->bezeichnungFeld = $data[self::FIELD_BEZEICHNUNG] ?? '';
        $dto->passiverartikel = $data[self::FIELD_PASSIVERARTIKEL] ?? '';
        $dto->geaendertvon = $data[self::FIELD_GEAENDERTVON] ?? '';
        $dto->umsatzfuehren = $data[self::FIELD_UMSATZFUEHREN] ?? '';
        $dto->bestandfuehren = $data[self::FIELD_BESTANDFUEHREN] ?? '';
        $dto->steuersatzFeld = $data[self::FIELD_STEUERSATZ_FELD] ?? 0;
        $dto->mengeneinheitFeld = $data[self::FIELD_MENGENEINHEIT_FELD] ?? '';
        $dto->reserviert = $data[self::FIELD_RESERVIERT] ?? 0;
        $dto->shopartikel = $data[self::FIELD_SHOPARTIKEL] ?? '';
        $dto->text2 = $data[self::FIELD_TEXT2] ?? '';
        $dto->text3 = $data[self::FIELD_TEXT3] ?? '';
        $dto->text4 = $data[self::FIELD_TEXT4] ?? '';
        $dto->text5 = $data[self::FIELD_TEXT5] ?? '';
        $dto->text6 = $data[self::FIELD_TEXT6] ?? '';
        $dto->text7 = $data[self::FIELD_TEXT7] ?? '';
        $dto->text8 = $data[self::FIELD_TEXT8] ?? '';
        $dto->lieferantartikelnr = $data[self::FIELD_LIEFERANTARTIKELNR] ?? '';
        $dto->gruppelfdnr = $data[self::FIELD_GRUPPELFDNR] ?? '';
        $dto->lagerlfdnr = $data[self::FIELD_LAGERLFDNR] ?? '';
        $dto->lieferantlfdnr = $data[self::FIELD_LIEFERANTLFDNR] ?? '';
        $dto->eanFeld = $data[self::FIELD_EAN_FELD] ?? '';
        $dto->ekmittel = $data[self::FIELD_EKMITTEL] ?? 0;
        $dto->shopid = $data[self::FIELD_SHOPID] ?? '';
        $dto->suchartikelnr = $data[self::FIELD_SUCHARTIKELNR] ?? '';
        $dto->art = $data[self::FIELD_ART] ?? '';
        $dto->lieferzeittage_feld = $data[self::FIELD_LIEFERZEITTAGE_FELD] ?? 0;
        $dto->lieferzeittext_feld = $data[self::FIELD_LIEFERZEITTEXT_FELD] ?? '';
        $dto->verpackungseinheit = $data[self::FIELD_VERPACKUNGSEINHEIT] ?? 0;
        $dto->bezeichnung2 = $data[self::FIELD_BEZEICHNUNG2] ?? '';
        $dto->bezeichnung3 = $data[self::FIELD_BEZEICHNUNG3] ?? '';
        $dto->bezeichnung4 = $data[self::FIELD_BEZEICHNUNG4] ?? '';
        $dto->bezeichnung5 = $data[self::FIELD_BEZEICHNUNG5] ?? '';
        $dto->bezeichnung6 = $data[self::FIELD_BEZEICHNUNG6] ?? '';
        $dto->bezeichnung7 = $data[self::FIELD_BEZEICHNUNG7] ?? '';
        $dto->bezeichnung8 = $data[self::FIELD_BEZEICHNUNG8] ?? '';
        $dto->shoplieferstatus = $data[self::FIELD_SHOPLIEFERSTATUS] ?? '';
        $dto->shoplieferstatustext = $data[self::FIELD_SHOPLIEFERSTATUSTEXT] ?? '';
        $dto->grundeinheit = $data[self::FIELD_GRUNDEINHEIT] ?? 0;
        $dto->masseinheit = $data[self::FIELD_MASSEINHEIT] ?? '';
        $dto->grundgewicht = $data[self::FIELD_GRUNDGEWICHT] ?? 0;
        $dto->bestand = $data[self::FIELD_BESTAND] ?? 0;
        $dto->verfuegbar = $data[self::FIELD_VERFUEGBAR] ?? 0;
        $dto->plus = $data[self::FIELD_PLUS] ?? 0;
        $dto->grundpreisnetto = $data[self::FIELD_GRUNDPREISNETTO] ?? 0;
        $dto->grundpreisbrutto = $data[self::FIELD_GRUNDPREISBRUTTO] ?? 0;
        $dto->lager = $data[self::FIELD_LAGER] ?? '';
        $dto->lieferantnr = $data[self::FIELD_LIEFERANTNR] ?? '';
        $dto->hsnameFeld = $data[self::FIELD_HSNAME] ?? '';

        // Group fields
        $dto->gruppe1 = $data[self::FIELD_GRUPPE1] ?? '';
        $dto->grpart1 = $data[self::FIELD_GRPART1] ?? '';
        $dto->bezeichnung1_1 = $data[self::FIELD_BEZEICHNUNG1_1] ?? '';
        $dto->bezeichnung2_1 = $data[self::FIELD_BEZEICHNUNG2_1] ?? '';
        $dto->bezeichnung3_1 = $data[self::FIELD_BEZEICHNUNG3_1] ?? '';
        $dto->bezeichnung4_1 = $data[self::FIELD_BEZEICHNUNG4_1] ?? '';
        $dto->bezeichnung5_1 = $data[self::FIELD_BEZEICHNUNG5_1] ?? '';

        // Extract free fields
        for ($i = 1; $i <= 30; $i++) {
            $key = 'Artikel_Freifeld' . $i;
            if (isset($data[$key])) {
                $dto->freeFields[$i] = $data[$key];
            }
        }

        // Additional fields (all with prefix "Feld_")
        foreach ($data as $key => $value) {
            if (strpos($key, 'Feld_') === 0) {
                $fieldName = substr($key, 5); // Remove "Feld_"
                $dto->additionalFields[$fieldName] = $value;
            }
        }

        return $dto;
    }

    /**
     * Converts the DTO to an array with configurable field names
     *
     * @param string $exportType The export type for field name mapping (default, xml, json, excel)
     * @return array
     */
    public function toArray($exportType = 'default'): array
    {
        $fieldMapping = new FieldMappingConfig();

        // Get field mappings for the export type
        $mappings = $fieldMapping->getAllMappedFields($exportType);

        // Create result array with original constant names first
        $originalResult = $this->getOriginalArray();

        // Apply field name mappings
        $result = [];
        foreach ($originalResult as $constantName => $value) {
            $mappedName = $fieldMapping->getMappedFieldName($constantName, $exportType);
            $result[$mappedName] = $value;
        }

        return $result;
    }

    /**
     * Gets the original array with constant names as keys
     *
     * @return array
     */
    private function getOriginalArray(): array
    {
        $result = [
            'FIELD_ID' => $this->id,
            'FIELD_ARTICLE_NUMBER' => $this->articleNumber,
            'FIELD_NAME' => $this->name,
            'FIELD_DESCRIPTION' => $this->description,
            'FIELD_SHORT_DESCRIPTION' => $this->shortDescription,
            'FIELD_PRICE' => $this->price,
            'FIELD_TAX_RATE' => $this->taxRate,
            'FIELD_STATUS' => $this->status,
            'FIELD_WEIGHT' => $this->weight,
            'FIELD_QUANTITY' => $this->quantity,
            'FIELD_CATEGORY_ID' => $this->categoryId,
            'FIELD_CATEGORY_ID2' => $this->categoryId2,
            'FIELD_CATEGORY_ID3' => $this->categoryId3,
            'FIELD_MANUFACTURER_ID' => $this->manufacturerId,
            'FIELD_META_TITLE' => $this->metaTitle,
            'FIELD_META_DESCRIPTION' => $this->metaDescription,
            'FIELD_META_KEYWORDS' => $this->metaKeywords,
            'FIELD_URL' => $this->url,
            'FIELD_EAN' => $this->ean,
            'FIELD_MPN' => $this->mpn,
            'FIELD_DELIVERY_STATUS' => $this->deliveryStatus,
            'FIELD_DELIVERY_STATUS_TEXT' => $this->deliveryStatusText,
            'FIELD_SHOW_ON_HOMEPAGE' => $this->showOnHomepage,

            // Additional language and text fields
            'FIELD_TEXT_LANGUAGE' => $this->textLanguage,
            'FIELD_DB_TEXT1' => $this->dbText1,
            'FIELD_DB_TEXT2' => $this->dbText2,
            'FIELD_DB_TEXT3' => $this->dbText3,
            'FIELD_DB_TEXT4' => $this->dbText4,
            'FIELD_DB_TEXT5' => $this->dbText5,
            'FIELD_DB_TEXT6' => $this->dbText6,
            'FIELD_DB_TEXT7' => $this->dbText7,
            'FIELD_DB_TEXT8' => $this->dbText8,

            // Additional article properties
            'FIELD_RABATTGRUPPE' => $this->rabattgruppe,
            'FIELD_MENGENEINHEIT' => $this->mengeneinheit,
            'FIELD_LIEFERZEIT_TAGE' => $this->lieferzeitTage,
            'FIELD_LIEFERZEIT_TEXT' => $this->lieferzeitText,
            'FIELD_BILDDATEIEN' => $this->bilddateien,
            'FIELD_IMAGES_DATEINAME0' => $this->imagesDateiname0,
            'FIELD_SKIP_IMAGES' => $this->skipImages,

            // Additional new fields
            'FIELD_EXPORT_MODUS' => $this->exportModus,
            'FIELD_LFDNR' => $this->lfdnr,
            'FIELD_ARTIKELNR' => $this->artikelnrFeld,
            'FIELD_BEMERKUNG' => $this->bemerkung,
            'FIELD_GEAENDERTAM' => $this->geaendertam,
            'FIELD_UMSATZMENGE' => $this->umsatzmenge,
            'FIELD_UMSATZ' => $this->umsatz,
            'FIELD_PROVISION' => $this->provision,
            'FIELD_GEWICHT_FELD' => $this->gewichtFeld,
            'FIELD_BESTELLMENGE' => $this->bestellmenge,
            'FIELD_LETZTERVERKAUF' => $this->letzterverkauf,
            'FIELD_BESTANDDATUM' => $this->bestanddatum,
            'FIELD_BESTANDMINDEST' => $this->bestandmindest,
            'FIELD_VK1' => $this->vk1,
            'FIELD_VK1BRUTTO' => $this->vk1brutto,
            'FIELD_EKPREIS' => $this->ekpreis,
            'FIELD_EKLETZTER' => $this->ekletzter,
            'FIELD_PREISEINHEIT' => $this->preiseinheit,
            'FIELD_TEXT' => $this->textFeld,
            'FIELD_BILDANZEIGE' => $this->bildanzeige,
            'FIELD_RABATTGRUPPE_FELD' => $this->rabattgruppeFeld,
            'FIELD_BEZEICHNUNG' => $this->bezeichnungFeld,
            'FIELD_PASSIVERARTIKEL' => $this->passiverartikel,
            'FIELD_GEAENDERTVON' => $this->geaendertvon,
            'FIELD_UMSATZFUEHREN' => $this->umsatzfuehren,
            'FIELD_BESTANDFUEHREN' => $this->bestandfuehren,
            'FIELD_STEUERSATZ_FELD' => $this->steuersatzFeld,
            'FIELD_MENGENEINHEIT_FELD' => $this->mengeneinheitFeld,
            'FIELD_RESERVIERT' => $this->reserviert,
            'FIELD_SHOPARTIKEL' => $this->shopartikel,
            'FIELD_TEXT2' => $this->text2,
            'FIELD_TEXT3' => $this->text3,
            'FIELD_TEXT4' => $this->text4,
            'FIELD_TEXT5' => $this->text5,
            'FIELD_TEXT6' => $this->text6,
            'FIELD_TEXT7' => $this->text7,
            'FIELD_TEXT8' => $this->text8,
            'FIELD_LIEFERANTARTIKELNR' => $this->lieferantartikelnr,
            'FIELD_GRUPPELFDNR' => $this->gruppelfdnr,
            'FIELD_LAGERLFDNR' => $this->lagerlfdnr,
            'FIELD_LIEFERANTLFDNR' => $this->lieferantlfdnr,
            'FIELD_EAN_FELD' => $this->eanFeld,
            'FIELD_EKMITTEL' => $this->ekmittel,
            'FIELD_SHOPID' => $this->shopid,
            'FIELD_SUCHARTIKELNR' => $this->suchartikelnr,
            'FIELD_ART' => $this->art,
            'FIELD_LIEFERZEITTAGE_FELD' => $this->lieferzeittage_feld,
            'FIELD_LIEFERZEITTEXT_FELD' => $this->lieferzeittext_feld,
            'FIELD_VERPACKUNGSEINHEIT' => $this->verpackungseinheit,
            'FIELD_BEZEICHNUNG2' => $this->bezeichnung2,
            'FIELD_BEZEICHNUNG3' => $this->bezeichnung3,
            'FIELD_BEZEICHNUNG4' => $this->bezeichnung4,
            'FIELD_BEZEICHNUNG5' => $this->bezeichnung5,
            'FIELD_BEZEICHNUNG6' => $this->bezeichnung6,
            'FIELD_BEZEICHNUNG7' => $this->bezeichnung7,
            'FIELD_BEZEICHNUNG8' => $this->bezeichnung8,
            'FIELD_SHOPLIEFERSTATUS' => $this->shoplieferstatus,
            'FIELD_SHOPLIEFERSTATUSTEXT' => $this->shoplieferstatustext,
            'FIELD_GRUNDEINHEIT' => $this->grundeinheit,
            'FIELD_MASSEINHEIT' => $this->masseinheit,
            'FIELD_GRUNDGEWICHT' => $this->grundgewicht,
            'FIELD_BESTAND' => $this->bestand,
            'FIELD_VERFUEGBAR' => $this->verfuegbar,
            'FIELD_PLUS' => $this->plus,
            'FIELD_GRUNDPREISNETTO' => $this->grundpreisnetto,
            'FIELD_GRUNDPREISBRUTTO' => $this->grundpreisbrutto,
            'FIELD_LAGER' => $this->lager,
            'FIELD_LIEFERANTNR' => $this->lieferantnr,
            'FIELD_HSNAME' => $this->hsnameFeld,

            // Group fields
            'FIELD_GRUPPE1' => $this->gruppe1,
            'FIELD_GRPART1' => $this->grpart1,
            'FIELD_BEZEICHNUNG1_1' => $this->bezeichnung1_1,
            'FIELD_BEZEICHNUNG2_1' => $this->bezeichnung2_1,
            'FIELD_BEZEICHNUNG3_1' => $this->bezeichnung3_1,
            'FIELD_BEZEICHNUNG4_1' => $this->bezeichnung4_1,
            'FIELD_BEZEICHNUNG5_1' => $this->bezeichnung5_1,
        ];

        // Add free fields
        foreach ($this->freeFields as $index => $value) {
            $result['Artikel_Freifeld' . $index] = $value;
        }

        // Add additional fields
        foreach ($this->additionalFields as $key => $value) {
            $result['Feld_' . $key] = $value;
        }

        return $result;
    }

    /**
     * Returns a readable summary of the article
     *
     * @return string
     */
    public function getSummary(): string
    {
        return sprintf(
            "Article #%s: %s (Price: %.2f, Stock: %d)",
            $this->articleNumber,
            $this->name,
            $this->price,
            $this->quantity
        );
    }
}
