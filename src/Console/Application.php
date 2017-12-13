<?php

namespace Glacier\Console;

use Glacier\Console\Arguments;
use Glacier\Console\Output;
use Glacier\Console\ApplicationSettings;
use Glacier\Filesystem\JsonConfigurationFile;
use Glacier\Events\Dispatcher;
use Glacier\Interfaces\Console\CommandInterface;

class Application
{
    public $commandSupport = true;
    public $configFileSupport = true;

    /**
     *
     * @var Application
     */
    protected static $instance;

    protected $arguments;
    protected $settings;
    protected $name;
    protected $output;
    protected $commands;
    protected $defaultCommandIndex = -1;
    protected $configFile;
    protected $dispatcher;

    public static function instance()
    {
        return static::$instance;
    }

    protected function init(array $args, $parseArguments)
    {
        $this->dispatcher = new Dispatcher;
        $this->configFile = false;
        $this->commands = [];
        $this->output = new Output;
        $this->name = (isset($args[0]) ? basename($args[0], '.php') : 'app');
        $this->arguments = new Arguments($args);
        if ($parseArguments)
            $this->arguments->parse();

        $this->initSettings();
        $this->loadConfigurationFile();

        return $this;
    }

    public function event($event)
    {
        return $this->dispatcher->fire($event);
    }

    public function registerCommand(Command $cmd)
    {
        if (empty(trim($cmd->name)))
            $cmd->name = generate_command_name(get_class($cmd));

        if (!$this->hasCommand($cmd->name)) {
            if ($cmd->name == 'default')
                $cmd->default = true;

            $this->commands[] = $cmd;
        }
        return $this;
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

    public function __construct(array $args, $parseArguments = true, $defaultCommand = null, $automaticallyRun = false, $multipleCommandSupport = true)
    {
        if (!isset(static::$instance))
            static::$instance = $this;

        $this->init($args, $parseArguments);

        if (is_object($defaultCommand) && $defaultCommand instanceof CommandInterface) {
            $defaultCommand->default = true;
            $this->registerCommand($defaultCommand);
        }

        if (is_array($defaultCommand)) {
            foreach($defaultCommand as $cmd) {
                if (is_string($cmd))
                    $cmd = new $cmd;
                $cmd->default = true;
                $this->registerCommand($cmd);
            }
        }

        $this->initializeDefaultCommands();

        $this->commandSupport = $multipleCommandSupport;

        if ($automaticallyRun)
            $this->run();
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
        if ($name == 'events')
            $name = 'dispatcher';

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
        $this->initializeDefaultCommands();

        //multiple command support disabled
        if (!$this->commandSupport)
            return $this->executeDefaultCommands();

        //multiple command support enabled
        if (count($this->arguments()->parameters)==0)
            return $this->executeDefaultCommands();

        $commandName = $this->arguments()->parameters[0];
        if ($this->hasCommand($commandName))
            return $this->command($commandName)->execute();

        throw new \Exception('Command not found.');

        return false;
    }

    protected function initializeDefaultCommands()
    {
        foreach($this->getDefaultCommands() as $cmd) {
            if (!$cmd->initialized && method_exists($cmd, 'initialize')) {
                $cmd->initialize();
                $cmd->initialized = true;
            }
        }

        return $this;
    }

    protected function executeDefaultCommands()
    {
        foreach($this->getDefaultCommands() as $cmd)
            $cmd->execute();

        return true;
    }

    protected function isDefaultCommand($command)
    {
        return (
                $command->name == 'default' ||
                $command->default ||
                $command instanceof DefaultCommand
        );
    }

    protected function getDefaultCommands()
    {
        $result = [];
        foreach($this->commands as $command) {
            if ($this->isDefaultCommand($command))
                $result[] = $command;
        }
        return $result;
    }

    public function configurationFileName()
    {
        return $this->name . ".json";
    }

    public function loadConfigurationFile()
    {
        $this->configFile = new JsonConfigurationFile($this->configurationFileName());

        if (file_exists($this->configurationFileName())) {
            $this->configFile->load();
        }

        return $this;
    }

    public function config($name = false)
    {
        if ($name == false)
            return $this->configFile;

        if (property_exists($this->configFile->getData(), $name))
            return $this->configFile->getData()->$name;

        return false;
    }


}