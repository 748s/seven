<?php

namespace Seven\Argument;

use InvalidArgumentException;
use Seven\Argument;

/**
 * ArgumentById
 *
 * @author Nick Wakeman <nick.wakeman@gmail.com>
 * @since  2016-10-08
 */
class ArgumentById extends Argument
{
    public function __construct($tableName, $argument)
    {
        global $db;
        parent::__construct($argument);
        if (!$db->existsById($tableName, $argument)) {
            Throw new InvalidArgumentException();
        }
    }
}
