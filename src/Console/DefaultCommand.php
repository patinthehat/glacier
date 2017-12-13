<?php

namespace Glacier\Console;

use Glacier\Interfaces\Console\DefaultCommandInterface;
use Glacier\Console\Command;

abstract class DefaultCommand extends Command implements DefaultCommandInterface
{
    public $default = true;
    public $initialized = false;

    abstract public function execute();
}
