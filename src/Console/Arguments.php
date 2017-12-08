<?php

namespace Glacier\Console;


class Arguments
{
    public $args;
    public $parameters;
    public $options;
    public $optionDefinitions;
    public $parameterNames;
    public $signature;

    public function __construct($arguments)
    {
        $this->args = $arguments;
        $this->reset();
        $this->signature = '{command} {arguments}';
    }

    protected function reset()
    {
        $this->parameters = array();
        $this->parameterNames = array();
        $this->options = array();
        $this->optionDefinitions = array();
        $this->signature = '{command} {arguments}';
        return $this;
    }

    public function defineOption($short, $long, $expectsValue, $defaultValue)
    {
        if ($this->getOptionDefinition($short)===false && $this->getOptionDefinition($long)===false)
            $this->optionDefinitions[] = OptionDefinition::create($short, $long, $expectsValue, $defaultValue);
        
        $this->parseSignature();
        return $this;
    }
    
    protected function parseSignature()
    {
        if (preg_match_all('/\{(\w+)\}/', $this->signature, $m)!==false) {
            $this->parameterNames = $m[1];
        }
        return $this;
    }

    public function getOptionDefinition($name)
    {
        foreach($this->optionDefinitions as $def) {
            if ($def->short == $name || $def->long == $name)
                return $def;
        }
        return false;
    }

    public function parse()
    {

        for($i = 1; $i < count($this->args); $i++) {
            $value = $this->args[$i];
            $bIsFlag = (substr($value, 0, 1)=='-');
            $bIsLongFlag = $bIsFlag && (substr($value, 0, 2)=='--');
            $bHasInlineValue = false;

            if ($bIsFlag) {
                $flagValue = null;
                
                $bHasInlineValue = $this->hasInlineValue($value);
                if ($bIsLongFlag) {
                    $name = substr($value, 2);
                    if (strpos($name, '=')!==false) {
                        list($name,$flagValue) = explode('=',$name,2);
                    } else {
                        $def = $this->getOptionDefinition($name);
                        if ($def !== false && $def->expectsValue) {
                            $flagValue = $this->args[$i+1];
                            $i++;
                        } else {
                            $flagValue = true;
                        }
                    }
                } else {
                    $name = substr($value, 1);
                    if (strlen($name)>1) {
                        for($j = 0; $j < strlen($name); $j++) {
                            $flag = $name[$j];
                            $optdef = $this->getOptionDefinition($flag);
                            $this->options[] = Option::create($flag, $flagValue, $optdef);
                        }
                        continue;
                    }
                }

                $def = $this->getOptionDefinition($name);
                if ($def !== false && $def->expectsValue && !$bHasInlineValue && $i+1 < count($this->args)) {
                    $flagValue = $this->args[$i+1];
                    $i++;
                }

                if (is_null($flagValue))
                    $flagValue = $def->defaultValue;
                //echo 'found flag: '.$name.'; value = '.$flagValue.PHP_EOL;
                $this->options[] = Option::create($name, $flagValue, $def);
            } else {
                //echo 'found param: '.$value.PHP_EOL;
                $this->parameters[] = Parameter::create($value);
            }

        }
        return $this;
    }

    protected function hasInlineValue($str)
    {
        return (strpos($str,'=')!==false);
    }

    public function hasOption($name)
    {
        if (is_array($name)) {
            foreach($name as $n) {
                foreach($this->options as $opt)
                    if ($opt->name == $n)
                        return true;
            }
        } else {
            foreach($this->options as $opt)
                if ($opt->name == $name)
                    return true;
        }

        return false;
    }

    public function getOption($name, $default = false)
    {
        if (is_array($name)) {
            foreach($name as $n) {
                foreach($this->options as $opt)
                    if ($opt->name == $n)
                        return $opt;
            }
        } else {
            foreach($this->options as $opt)
                if ($opt->name == $name)
                    return $opt;
        }
        return $default;
    }

    protected function getNamedParameterIndex($name)
    {
        foreach($this->parameterNames as $index=>$param) 
            if ($param == $name)
                return $index;
            
        return false;
    }
    
    public function parameter($name)
    {
        if (is_numeric($name))
            return $this->parameters[$name];

        $index = $this->getNamedParameterIndex($name);
        if ($index !== false)
            return $this->parameters[$index];
        
        return false;
    }
}