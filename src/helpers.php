<?php

use Glacier\Console\Application;

if (!function_exists('app')) {

    function app(): Application
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

if (!function_exists('setting')) {
    function setting($name)
    {
        return app()->setting($name);
    }
}


if (!function_exists('command_name')) {
    function command_name($className)
    {
        $name = $className;
        $name = preg_replace('/Command$/', '', $name);
        if (preg_match_all('/([A-Z][a-z0-9_]+)/', $name, $m)!==false)
            return strtolower(implode('-',$m[0]));

        return strtolower($name);
    }
}


if (!function_exists('generate_command_name')) {

    function generate_command_name($class = null, $suffixLength = 6)
    {
        $result = 'command-';
        if (is_string($class) && strlen($class) > 0)
            $result .= '-'.command_name($class);
        $result .= '-'.substr(md5(rand(1,10000000)), 0, ($suffixLength > 0 ? $suffixLength : 6));

        //compress multiple dashes => single dash
        $result = preg_replace('/\-{2,}/', '-', $result);

        return $result;
    }
}