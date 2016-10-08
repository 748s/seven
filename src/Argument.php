<?php

namespace Seven;

abstract class Argument
{
    protected $argument;

    public function __construct($argument)
    {
        $this->argument = $argument;
    }

    public function __toString()
    {
        return $this->argument;
    }
}
