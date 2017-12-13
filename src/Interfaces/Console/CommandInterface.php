<?php

namespace Glacier\Interfaces\Console;

interface CommandInterface
{
    public function execute();
    public function getName();
}