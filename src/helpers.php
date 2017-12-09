<?php

use Glacier\Console\Application;

if (!function_exists('app')) {
    
    function app()
    {
        return Application::instance();
    }
    
}


if (!function_exists('event')) {
    function event($event) 
    {
        return app()->event($event);
    }
}