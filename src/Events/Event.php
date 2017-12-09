<?php

namespace Glacier\Events;

class Event
{
    public $name;
    
    public function __construct()
    {
        if (!isset($this->name) || empty($this->name) || !is_string($this->name))
            $this->name = $this->generateEventName();
    }
    
    public static function create()
    {
        $e = new static;
        return $e;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function __get($name)
    {
        //proxy requests for protected properties
        if (property_exists($this, $name))
            return $this->$name;
    }
    
    protected function generateEventName()
    {
        if (preg_match_all('/([A-Z][a-z0-9_]+)/', static::class, $m)!==false)
            return strtolower(implode('.', $m[1]));
        return static::class;
    }
    
}
