<?php

namespace Glacier\Filesystem;

abstract class ConfigurationFile
{
    protected $filename;
    protected $data;
    
    public function __construct($filename)
    {
        $this->filename = $filename;
        $this->initialize();
    }
    
    public function getFilename()
    {
        return $this->filename;
    }
    
    public function getData()
    {
        return $this->data;
    }
    
    public function __set($name, $value)
    {
       $this->data->$name = $value;   
    }
    
    public function __get($name)
    {
        if (property_exists($this->data, $name))
            return $this->data->$name;
        
        return false;
    }

    
    abstract public function initialize();
    abstract public function load();
    abstract public function save();
    
}
