<?php

namespace Glacier\Console;

class Parameter
{
    public $value;

    public static function create($value)
    {
        $o = new static;
        $o->value = $value;
        return $o;
    }

    public function getValue()
    {
        return $this->value;
    }

    public function __toString()
    {
        return $this->getValue();
    }

}