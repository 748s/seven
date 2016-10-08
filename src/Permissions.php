<?php

namespace Seven;

class Permissions
{
    public function isLoggedIn()
    {
        return (isset($_SESSION['user_id']) && $_SESSION['user_id']);
    }
}
