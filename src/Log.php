<?php

namespace Seven;

/**
 * Log
 *
 * @author Nick Wakeman <nick.wakeman@gmail.com>
 * @since  2016-10-08
 */
class Log
{
    protected $config;

    public function __construct()
    {
        global $config;
        $this->config = $config;
    }

    public function logError($type, $message, $file, $line)
    {
        global $db;
        $db->put($this->config->log->errors, [
            'type'          => $type,
            'message'       => $message,
            'file'          => $file,
            'line'          => $line,
            'uri'           => (isset($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : null,
            'post'          => (count($_POST)) ? 1 : 0,
            'userId'       => (isset($_SESSION['user_id'])) ? $_SESSION['user_id'] : 0,
            'ipIddress'    => (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : null,
            'userAgent'    => (isset($_SERVER['HTTP_USER_AGENT'])) ? $_SERVER['HTTP_USER_AGENT'] : null,
            'referer'       => (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : null
        ]);

        // if config->environment == 'development': also print errors to screen
        if ('development' === $this->config->environment) {
            return false;
        }
    }

    public function logRequest()
    {
        global $db;
        global $seven;
        $db->put($this->config->log->requests, [
            'uri'           => (isset($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : null,
            'post'          => (count($_POST)) ? 1 : 0,
            'userId'       => (isset($_SESSION['user_id'])) ? $_SESSION['user_id'] : 0,
            'ipAddress'    => (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : null,
            'userAgent'    => (isset($_SERVER['HTTP_USER_AGENT'])) ? $_SERVER['HTTP_USER_AGENT'] : null,
            'referer'       => (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : null,
            'responseTime' => $seven['endTime'] - $seven['startTime'],
            'numQueries'   => $seven['numQueries']
        ]);
    }
}
