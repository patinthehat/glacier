<?php

namespace Glacier\Events;

abstract class EventListener
{
    public function __construct()
    {
        //
    }
    
    abstract public function handle(Event $event);
}
