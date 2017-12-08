<?php

namespace Glacier\Support;

class DataStore
{
    protected $data;

    public function __construct()
    {
        $this->data = array();
    }

    public function __get($name)
    {
        return $this->data[$name];
    }

    public function __set($name, $value)
    {
        $this->data[$name] = $value;
    }

    public function __isset($name)
    {
        return isset($this->data[$name]);
    }
}