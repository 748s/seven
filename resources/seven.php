<?php

/**
 * Seven - the bootstrapping script
 *
 * @author Nick Wakeman <nick.wakeman@gmail.com>
 * @since  2016-10-08
 */

// Start the script
$seven = [
    'startTime'         => microtime(true),
    'endTime'           => null,
    'numQueries'        => 0
];

// Get the Autoloader
require_once('./vendor/autoload.php');

use Seven\Load;
use Arm\Arm;

// Get the config file
$config = Load::json('./config.json');

// Configure Error Handling
$errorClass = Load::extensionOrSeven('ErrorHandler');
$errorHandler = new $errorClass();
$errorHandler->configure();

// Configure the timezone
date_default_timezone_set($config->timezone);

// Setup the Database Connection
$db = new Arm($config->database);

// Start the Session
session_start();

// Route and Execute the request
$routerClass = Load::extensionOrSeven('Router');
$Router = new $routerClass();
$Router->route();

// Shutdown script
$seven['numQueries'] = $db->getNumQueries();
$seven['endTime'] = microtime(true);

// That's it!
