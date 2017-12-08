<?php

namespace Glacier\Console;

interface CommandInterface
{
    public function execute();
    public function getName();
}