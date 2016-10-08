<?php

namespace Seven;

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
        $db->put(
            $this->config->log->errors,
            [
                'type'          => $type,
                'message'       => $message,
                'file'          => $file,
                'line'          => $line,
                'uri'           => (isset($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : '',
                'post'          => (count($_POST)) ? 1 : 0,
                'userId'       => (isset($_SESSION['user_id'])) ? $_SESSION['user_id'] : 0,
                'ipIddress'    => (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : '',
                'userAgent'    => (isset($_SERVER['HTTP_USER_AGENT'])) ? $_SERVER['HTTP_USER_AGENT'] : '',
                'referer'       => (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : ''
            ]
        );
    }

    public function logRequest()
    {
        global $db;
        global $seven;
        $db->put(
            $this->config->log->requests,
            [
                'uri'           => (isset($_SERVER['REQUEST_URI'])) ? $_SERVER['REQUEST_URI'] : '',
                'post'          => (count($_POST)) ? 1 : 0,
                'userId'       => (isset($_SESSION['user_id'])) ? $_SESSION['user_id'] : 0,
                'ipAddress'    => (isset($_SERVER['REMOTE_ADDR'])) ? $_SERVER['REMOTE_ADDR'] : '',
                'userAgent'    => (isset($_SERVER['HTTP_USER_AGENT'])) ? $_SERVER['HTTP_USER_AGENT'] : '',
                'referer'       => (isset($_SERVER['HTTP_REFERER'])) ? $_SERVER['HTTP_REFERER'] : '',
                'responseTime' => $seven['endTime'] - $seven['startTime'],
                'numQueries'   => $seven['numQueries']
            ]
        );
    }
}
/*
CREATE TABLE error(
    errorId INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    type TINYTEXT,
    message TEXT,
    file TINYTEXT,
    line INT,
    uri TINYTEXT,
    post INT(1),
    userId INT,
    ipAddress VARCHAR(50),
    userAgent TEXT,
    referer TINYTEXT,
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
*/
/*
CREATE TABLE request(
    requestId INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    uri TINYTEXT,
    post INT(1),
    userId INT,
    ipAddress VARCHAR(50),
    userAgent TEXT,
    referer TINYTEXT,
    responseTime VARCHAR(25),
    numQueries INT,
    created DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
*/

