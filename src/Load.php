<?php

namespace Seven;

use Exception;
use ReflectionClass;

/**
 * Load - a collection of static functions to quickly, reliably, and informatively
 *  (i.e. throwing Exceptions when helpful) load data or files of one form or another
 *
 * @author Nick Wakeman <nick.wakeman@gmail.com>
 * @since  2016-10-08
 */
class Load
{
    static function json($fileName, $asArray = false)
    {
        $json = json_decode(file_get_contents($fileName), $asArray);
        if (json_last_error()) {
            Throw new Exception("There is an error in your JSON syntax for file $fileName");
        }

        return $json;
    }

    static function extensionOrSeven($className, $arguments = [])
    {
        $className = (class_exists('\App\Extension\\' . $className)) ?
            "\App\Extension\\$className" : "\Seven\\$className"
        ;
        $reflector = new ReflectionClass($className);
        return $reflector->newInstanceArgs($arguments);
    }
}
