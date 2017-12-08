<?php

namespace Glacier\Console;

use Glacier\Console\Arguments;
use Glacier\Console\Output;
use Glacier\Console\ApplicationSettings;

class Application
{
    public $commandSupport = true;
    
    protected $arguments;
    protected $settings;
    protected $name;
    protected $output;
    protected $commands;
    protected $defaultCommandIndex = -1;

    protected function init(array $args, $parseArguments)
    {
        $this->commands = [];
        $this->output = new Output;
        $this->name = (isset($args[0]) ? basename($args[0], '.php') : 'app');
        $this->arguments = new Arguments($args);
        if ($parseArguments)
            $this->arguments->parse();

        $this->initSettings();
        
        return $this;
    }
    
    public function registerCommand(Command $cmd)
    {
        if (!$this->hasCommand($cmd->name)) {
            $cmd->app = $this;
            $this->commands[] = $cmd;
            if ($cmd->name == 'default')
                $this->defaultCommandIndex = count($this->commands)-1;
        }
        return $this;
    }
    
    public function defaultCommand()
    {
        if (count($this->commands) == 1)
            return $this->commands[0];
        
        if (count($this->commands) > 1)
            return $this->command('default');
        
        return false;
    }

    public function command($name)
    {
        foreach($this->commands as $command)
            if ($command->getName() == $name)
                return $command;
        return false;
    }
    
    public function hasCommand($name)
    {
        foreach($this->commands as $command)
            if ($command->getName() == $name)
                return true;
            
        return false;
    }

    public function initSettings($settings = false)
    {
        if (!is_array($settings))
            $settings = [];
        
        foreach($this->arguments->optionDefinitions as $def) {
            if (is_string($def->long) && strlen($def->long) > 0) {
                $settings[$def->long] = $def->defaultValue;
            }
        }

        foreach($settings as $name=>&$value) {
            foreach($this->arguments->options as $option)
                if (is_object($option->definition) && $name == $option->definition->long)
                       $value = $option->value;
        }

        $this->settings = new ApplicationSettings($settings);

        return $this;
    }

    public function __construct(array $args, $parseArguments = true, Command $defaultCommand = null)
    {
        $this->init($args, $parseArguments);
        if (is_object($defaultCommand)) {
            $this->registerCommand($defaultCommand);
            $this->defaultCommandIndex = count($this->commands)-1;
        }
    }

    public function arguments()
    {
        return $this->arguments;
    }

    public function settings()
    {
        return $this->settings;
    }

    public function setting($name)
    {
        return $this->settings()->$name;
    }

    public function expectParameterCount($expected, $throwException = true, $errorMessage = 'Expecting %d parameters')
    {
        $actual = count($this->arguments->parameters);

        if ($actual != $expected) {
            if ($throwException)
                throw new \Exception(sprintf($errorMessage, $actual));

            return false;
        }

        return true;
    }
    
    public function getName()
    {
        return $this->name;
    }
    
    public function output($data = false)
    {
        if ($data !== false)
            return $this->output->write($data);
        
        return $this->output;
    }
    
    public function write($data)
    {
        return $this->output()->write($data);
    }
    
    public function __get($name)
    {
        return $this->$name;
    }
    
    public function registerSetting($name, $default)
    {
        $this->settings->$name = $default;
        return $this;
    }
        
    public function registerSettings(array $settings)
    {
        foreach($settings as $name=>$default) 
            $this->registerSetting($name, $default);
        
        return $this;
    }
    
    public function run()
    {
        if ($this->defaultCommandIndex == -1 && count($this->commands)==1)
            $this->defaultCommandIndex = 0;
        
        if ($this->commandSupport) {
            //multiple command support enabled
            if (count($this->arguments()->parameters)==0) {
                $commandName = 'default';   
            } else {            
                $commandName = $this->arguments()->parameters[0];
            }

            if ($this->hasCommand($commandName))
                return $this->command($commandName)->execute();
            
            if ($this->defaultCommandIndex > -1)
                return $this->commands[$this->defaultCommandIndex]->execute();
            
            if (count($this->commands)==1 || $this->hasCommand('default'))
                return $this->defaultCommand()->execute();
            
            return false;
        } else {
            //multiple command support disabled
            if ($this->defaultCommandIndex > -1)
                return $this->commands[$this->defaultCommandIndex]->execute();
            if (count($this->commands) > 0)
                return $this->commands[0]->execute();
        }
        
        return true;
    }

    
}