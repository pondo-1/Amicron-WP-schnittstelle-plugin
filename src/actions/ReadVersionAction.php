<?php
/**
 * @author Stefan Witt <stefan.witt@rathje-design.de>
 */
require_once 'AbstractAction.php';

class ReadVersionAction extends AbstractAction {
    private $versionMajor;
    private $versionMinor;
    private $defaultCharset;

    /**
     * Constructor
     *
     * @param Logger $logger The logger instance
     * @param int $versionMajor Major version number
     * @param int $versionMinor Minor version number
     * @param string $defaultCharset Default charset
     */
    public function __construct(Logger $logger, $versionMajor, $versionMinor, $defaultCharset) {
        parent::__construct($logger);
        $this->versionMajor = $versionMajor;
        $this->versionMinor = $versionMinor;
        $this->defaultCharset = $defaultCharset;
    }

    /**
     * Execute the action
     *
     * @param array $requestData The request data (not used in this action)
     * @return string XML response
     */
    public function execute($requestData = []) {
        $versionString = $this->getVersionXML();
        $this->logger->info('ReadVersion' . $versionString);
        return $versionString;
    }

    /**
     * Generate version XML
     *
     * @return string XML string
     */
    private function getVersionXML() {
        // Create a new XML document
        $xml = new DOMDocument('1.0', 'UTF-8');

        // Create the main nodes
        $status = $xml->createElement('STATUS');
        $statusData = $xml->createElement('STATUS_DATA');

        // Add the individual elements
        $scriptVersionMajor = $xml->createElement('SCRIPT_VERSION_MAJOR', (int)$this->versionMajor);
        $scriptVersionMinor = $xml->createElement('SCRIPT_VERSION_MINOR', (int)$this->versionMinor);
        $scriptDefaultCharset = $xml->createElement('SCRIPT_DEFAULTCHARSET',
            htmlspecialchars($this->defaultCharset, ENT_XML1, 'UTF-8'));

        // Attach the elements to STATUS_DATA
        $statusData->appendChild($scriptVersionMajor);
        $statusData->appendChild($scriptVersionMinor);
        $statusData->appendChild($scriptDefaultCharset);

        // Attach STATUS_DATA to STATUS
        $status->appendChild($statusData);

        // Attach STATUS to the document
        $xml->appendChild($status);

        // Return the XML document as a string
        return $xml->saveXML();
    }
}
?>