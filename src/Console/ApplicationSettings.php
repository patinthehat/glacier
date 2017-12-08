<?php

namespace Glacier\Console;

use Glacier\Console\ApplicationData;

class ApplicationSettings extends ApplicationData
{
    public function __construct(array $settings)
    {
        parent::__construct();

        foreach($settings as $key => $value)
        {
            $this->$key = $value;
        }
    }
}
