<?php

namespace Seven;

use Exception;
use InvalidArgumentException;

/**
 * ErrorHandler
 *
 * @author Nick Wakeman <nick.wakeman@gmail.com>
 * @since  2016-10-08
 */
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

        if (!isset($this->config->environment)) {
            Throw new Exception("You must declare the 'environment' property in config.json");
        } else {
            switch (strtolower($this->config->environment)) {
                case 'development':
                    $errorReporting = -1;
                    $displayErrors = true;
                break;
                case 'staging':
                case 'production':
                    $errorReporting = E_WARNING;
                    $displayErrors = false;
                break;
                default:
                    Throw new InvalidArgumentException("config->environment must be 'development', 'staging', or 'production'");
                break;
            }
        }

        ini_set('error_reporting', $errorReporting);
        ini_set('display_errors', $displayErrors);
        if ($this->config->log->errors) {
            set_error_handler([$this->log, 'logError'], $errorReporting);
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
