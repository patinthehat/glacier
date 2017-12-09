<?php

namespace Glacier\Console;

use Glacier\Console\CommandInterface;

abstract class Command implements CommandInterface
{
    //public $app;
    public $name = '';
    
    public function __construct()
    {
        //
    }
    
    public function getName()
    {
        if (strlen($this->name)==0) {
            $name = preg_replace('/Command$/', '', static::class);
            if (preg_match_all('/([A-Z][a-z]+)/', $name, $m)!==false)
                return strtolower(implode('-',$m[0]));
        }
        return $this->name;
    }
    
    abstract public function execute();
}