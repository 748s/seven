<?php

namespace Seven;

use Seven\Logger;

class ErrorHandler
{
    protected $config;
    protected $logger;

    public function __construct()
    {
        global $config;
        $this->config = $config;
        if (
            $this->config->errorHandler->logErrors ||
            $this->config->appRequests->logRequests
        ) {
            $this->logger = new Logger();
        }
    }

    public function configure()
    {
        ini_set('display_errors', $this->config->errorHandler->display_errors);
        ini_set('error_reporting', $this->config->errorHandler->error_reporting);
        if ($this->config->errorHandler->logErrors) {
            set_error_handler([$this->logger, 'logError'], $this->config->errorHandler->error_reporting);
        }
        register_shutdown_function([$this, 'handleShutdown']);
    }

    public function handleShutdown()
    {
        global $seven;
        if (!$seven['endTime']) {
            $error = error_get_last();
            if ($this->config->errorHandler->logErrors) {
                $this->logger->logError($error['type'], $error['message'], $error['file'], $error['line']);
            }
        } elseif ($this->config->appRequests->logRequests) {
            $this->logger->logRequest();
        }
    }
}
