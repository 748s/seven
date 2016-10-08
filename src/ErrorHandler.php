<?php

namespace Seven;

use Seven\Logger;

class ErrorHandler
{
    protected $config;
    protected $log;

    public function __construct()
    {
        global $config;
        $this->config = $config;
        if (
            $this->config->log->errors ||
            $this->config->log->requests
        ) {
            $this->log = new Log();
        }
    }

    public function configure()
    {
        ini_set('display_errors', $this->config->errorHandler->display_errors);
        ini_set('error_reporting', $this->config->errorHandler->error_reporting);
        if ($this->config->log->errors) {
            set_error_handler([$this->log, 'logError'], $this->config->errorHandler->error_reporting);
        }
        register_shutdown_function([$this, 'handleShutdown']);
    }

    public function handleShutdown()
    {
        global $seven;
        if (!$seven['endTime']) {
            $error = error_get_last();
            if ($this->config->log->errors) {
                $this->log->logError($error['type'], $error['message'], $error['file'], $error['line']);
            }
        } elseif ($this->config->log->requests) {
            $this->log->logRequest();
        }
    }
}
