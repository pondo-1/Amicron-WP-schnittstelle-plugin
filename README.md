# Amicron Export Schnittstelle Plugin

Ein WordPress-Plugin für den Datenaustausch zwischen WordPress/WooCommerce und Amicron-Systemen.

## Funktionen

- Produktdatensynchronisation mit Amicron
- **Automatische Synchronisation über Produkt-SKU (Artikel_Artikelnr in Amicron entspricht der SKU im WooCommerce-Produkt)**
- Konfigurierbare Feldabbildungen
- Automatisierte WooCommerce-Produktaktualisierungen
- Protokollierungssystem zur Nachverfolgung von Operationen

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
