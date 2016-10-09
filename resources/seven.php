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
$errorHandler = Load::extensionOrSeven('ErrorHandler');

// Configure the timezone
date_default_timezone_set($config->timezone);

// Setup the Database Connection
$db = new Arm($config->database);

// Start the Session
session_start();

// Route and Execute the request
$router = Load::extensionOrSeven('Router');
$router->route();

// Shutdown script
$seven['numQueries'] = $db->getNumQueries();
$seven['endTime'] = microtime(true);

// That's it!
