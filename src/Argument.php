<?php

namespace Seven;

/**
 * Argument
 *
 * @author Nick Wakeman <nick.wakeman@gmail.com>
 * @since  2016-10-08
 */
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
