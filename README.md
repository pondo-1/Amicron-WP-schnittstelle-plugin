# Amicron Export Schnittstelle Plugin

Ein WordPress-Plugin für den Datenaustausch zwischen WordPress/WooCommerce und Amicron-Systemen.

## Funktionen

- Produktdatensynchronisation mit Amicron
- **Automatische Synchronisation über Produkt-SKU (Artikel_Artikelnr in Amicron entspricht der SKU im WooCommerce-Produkt)**
  Was wird tatsächlich durch den Amicron-Export synchronisiert: Siehe unten.
- Konfigurierbare Feldabbildungen
- Automatisierte WooCommerce-Produktaktualisierungen
- Protokollierungssystem zur Nachverfolgung von Operationen

### Amicron Shop Export -> WC Product Update

    S - Simple
    V - Variable
    A - Variant(defined by Attribute from Variable)

    o: Die Eigenschaft gehört zu diesem Produkttyp
    x: Die Eigenschaft gehört zu diesem Produkttyp nicht

    S:V:A:WC Product          : Amicron Export

    o:o:x:name                : Artikel_DBText1
    o:o:x:description         : Artikel_DBText4
    o:o:x:short_description   : Artikel_DBText8
    o:x:o:regualr_price       : Artikel_Preis
    o:x:o:stock_quantity      : Artikel_Menge

    --Produktinformationen (sollen durch den Export nicht verändert werden; Beziehungen zwischen Variablen und Varianten werden nicht aktualisiert)
    x:o:x:option_name         : 3. Element von Artikel_Freifeld6

    x:x:o:parent SKU          : Artikel_Freifeld6
    x:x:o:attribute_value     : letzte Line von Artikel_DBText4

    --taxonomy  (muss noch weiter bearbeiten werden)
    o:o:x:product_cat         : Artikel_Freifeld17
    o:o:x:product_brand       : Artikel_Freifeld16

    --meta field
    o:o:x:amicron_artikelID   : Artikel_ID
    o:o:x:compatible          : Artikel_Kurztext1

Nach dem Export aus Amicron wird das Metafeld **"compatible"** im Produkt gespeichert und im Frontend angezeigt. Es wird jedoch nicht für Produktfilter verwendet.
**Die Filterfunktion für "Compatible mit" wird nicht automatisch aktualisiert.** Eine Aktualisierung ist nur auf Anfrage möglich.

## Verzeichnisstruktur

```
├── src/
│   ├── Actions/         # Aktionshandler für verschiedene Datenoperationen
│   ├── Config/          # Konfigurationsdateien und Feldabbildungen
│   ├── Core/            # Kernfunktionalität
│   ├── DTO/            # Datenübertragungsobjekte
│   ├── Exporters/      # Exportfunktionalität (JSON, XML, Excel)
│   ├── Init/           # Plugin-Initialisierung und Admin-Einstellungen
│   ├── Log/            # Protokollierungsfunktionalität
│   ├── ShopEntities/   # Shop-bezogene Entitäts-Handler
│   ├── Utils/          # Hilfsfunktionen
│   └── Woo/            # WooCommerce-spezifische Funktionalität
```

## Installation

1. Laden Sie die Plugin-Dateien in den Ordner `/wp-content/plugins/` hoch
2. Aktivieren Sie das Plugin über den 'Plugins'-Bildschirm in WordPress
3. Konfigurieren Sie die Plugin-Einstellungen über das WordPress-Admin-Panel

## Konfiguration

Das Plugin kann über folgende Wege konfiguriert werden:

1. Feldabbildungen in `src/Config/field_mappings.json`
2. WooCommerce-Abbildungen in `src/Woo/amicron_woo_mapping.json`
3. WordPress-Admin-Einstellungsseite

## Exportformate

Das Plugin unterstützt folgende Exportformate:

- JSON
- XML
- Excel

Beispielexporte finden Sie im Verzeichnis `src/Exporters/test_exports/`.

## Protokollierung

Das Plugin enthält ein umfassendes Protokollierungssystem zur Nachverfolgung von Operationen und Fehlerbehebung. Die Protokolle können über die WordPress-Admin-Oberfläche eingesehen werden.

### Log

Man kann die Log einfach in www.yourwebsite.com/wp-admin/admin.php?page=amicron-schnittstelle ansehen.

## Entwicklung

### Hauptkomponenten

- **RequestParser**: Verarbeitet eingehende Anfragen
- **ResponseHandler**: Verwaltet API-Antworten
- **WooProductUpdater**: Handhabt WooCommerce-Produktaktualisierungen
- **AmicronToWooProductMapper**: Bildet Amicron-Daten auf WooCommerce-Format ab

### Hinzufügen neuer Funktionen

1. Erstellen Sie entsprechende Action-Klassen in `src/Actions/`
2. Aktualisieren Sie bei Bedarf die Feldabbildungen
3. Implementieren Sie notwendige DTO-Klassen
4. Fügen Sie bei Bedarf entsprechende Exporter-Funktionalität hinzu

## Lizenz

Dieses Plugin ist proprietäre Software. Alle Rechte vorbehalten.

## Support

Für Support-Anfragen wenden Sie sich bitte an die Plugin-Betreuer.
