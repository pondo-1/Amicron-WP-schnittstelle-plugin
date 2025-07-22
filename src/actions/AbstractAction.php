<?php
/**
 * Abstract base class for all API actions
 * 
 * @author Stefan Witt <stefan.witt@rathje-design.de>
 */
abstract class AbstractAction {
    protected $logger;

    /**
     * Constructor
     *
     * @param Logger $logger The logger instance
     */
    public function __construct(Logger $logger) {
        $this->logger = $logger;
    }

    /**
     * Execute the action
     *
     * @param array $requestData The request data
     * @return string XML response
     */
    abstract public function execute($requestData = []);
}
?>