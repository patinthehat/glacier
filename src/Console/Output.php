<?php

namespace Glacier\Console;

class Output
{
    public function __construct()
    {
        //
    }
    
    public function write($data)
    {
        echo $data;
        return $this;
    }
    
    public function writeln($data)
    {
        return $this->write($data . PHP_EOL);
    }
    
}
