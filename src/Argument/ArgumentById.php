<?php

namespace Seven\Argument;

use InvalidArgumentException;
use Seven\Argument;

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
