<?php

namespace Glacier\Events;

abstract class EventListener
{
    public static $events;
    
    public function __construct()
    {
        if (empty(static::$events) || (!is_string(static::$events) && !is_array(static::$events)))
            throw new \Exception(__CLASS__.' must define static property $events as string|string[].');
    }
    
    abstract public function handle(Event $event);
}
