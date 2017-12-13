<?php

namespace Glacier\Events;

use Glacer\Events\Event;

class MultipleEventListener extends EventListener
{    
    public function __construct()
    {
        if (empty(static::$events) || !is_array(static::$events))
            throw new \Exception(__CLASS__.' must define static property $events as string[].');
    }
 
    public function handle($event, $payload = null)
    {
        $methodName = preg_replace('/[-.]/', '_', $event->name);
        $methodName = str_replace('*', 'all_events', $methodName);
        
        
        if (!method_exists($this, $methodName))
            if ($this->ignoreMissingHandlers) {
                return false;
            } else {
                throw new \Exception('Unhandled event: '.$methodName);
            }
        
        $this->$methodName($event, $payload);
        return true;
    }
}
