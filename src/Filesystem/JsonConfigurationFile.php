<?php

namespace Glacier\Filesystem;

use Glacier\Filesystem\ConfigurationFile;

class JsonConfigurationFile extends ConfigurationFile
{
    public function initialize()
    {
        $data = '{}';
        $this->data = json_decode($data);
        return $this;
    }
   
    public function load($filename = false)
    {
        if ($filename !== false)
            $this->filename = $filename;    

        if (file_exists($this->filename)) {
            $data = file_get_contents($this->filename);
        } else {
            $data = '{}';
        }
        
        $this->data = json_decode($data);
        return ($this->data !== false);
    }
    
    public function save()
    {
        $json = json_encode($this->data, JSON_PRETTY_PRINT);
        file_put_contents($this->filename, $json);
        return $this;
    }
    
}
