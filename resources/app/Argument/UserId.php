<?php

namespace App\Argument;

use Seven\Argument\ArgumentById;

/**
 * UserId
 *
 * @author Nick Wakeman <nick.wakeman@gmail.com>
 * @since  2016-10-08
 */
class UserID extends ArgumentById
{
    public function __construct($argument)
    {
        parent::__construct('user', $argument);
    }
}
