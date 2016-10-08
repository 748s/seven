<?php

namespace Seven;

/**
 * Permission
 *
 * @author Nick Wakeman <nick.wakeman@gmail.com>
 * @since  2016-10-08
 */
class Permission
{
    public function isLoggedIn()
    {
        return (isset($_SESSION['userId']) && $_SESSION['userId']);
    }
}
