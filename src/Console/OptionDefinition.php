<?php

namespace Glacier\Console;

class OptionDefinition
{
    public $short;
    public $long;
    public $expectsValue;
    public $defaultValue;

    public static function create($short, $long, $expectsValue, $defaultValue = true)
    {
        $o = new static;
        $o->short = $short;
        $o->long = $long;
        $o->expectsValue = $expectsValue;
        $o->defaultValue = $defaultValue;
        return $o;
    }

}