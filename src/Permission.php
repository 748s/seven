<?php

namespace Seven;

class Permission
{
    public function isLoggedIn()
    {
        return (isset($_SESSION['userId']) && $_SESSION['userId']);
    }
}
