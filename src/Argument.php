<?php

namespace Seven;

class Argument
{
    protected $db;

    public function __construct()
    {
        global $db;
        $this->db = $db;
    }
}
