<?php

namespace Glacier\Console;


class Option
{
    public $name;
    public $value;
    public $definition;

    public static function create($name,$value, $definition = false)
    {
        $o = new static;
        $o->name = $name;
        $o->value = $value;
        $o->definition = $definition;
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