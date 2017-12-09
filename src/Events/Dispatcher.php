<?php

namespace Glacier\Events;

class Dispatcher
{
    protected $listeners;
    protected $wildcards;
    protected $events;
    
    public function __construct()
    {
        $this->listeners = array();
        $this->wildcards = array();
    }
    
    public function listen($events, $listener)
    {
        foreach ((array) $events as $event) {
            if (strpos($event, '*')!==false) {
                $this->setupWildcardListen($event, $listener);
            } else {
                $this->listeners[$event][] = $this->makeListener($listener);
            }
        }
        
    }
    
    protected function setupWildcardListen($event, $listener)
    {
        $this->wildcards[$event][] = $this->makeListener($listener, true);
    }
    
    public function makeListener($classname)
    {
        if (class_exists($classname))
            return (new $classname);
    }
    
    public function fire($event)
    {        
        $wildcards = $this->getWildcardListeners($event);
        $listeners = array_merge($wildcards, $this->getListeners($event));
              
        $handlerResult = true;
        foreach($listeners as $listener) {
            $handlerResult = $handlerResult && $listener->handle($event);
            if (!$handlerResult)
                break;
        }
        
        return $this;
    }
    
    public function getListeners($event)
    {
        $result = [];
        $name = $event->name;
        if (isset($this->listeners[$name])) {
            foreach($this->listeners[$name] as $e)
                    $result[] = $e;
        }
        return $result;
    }
    
    public function getWildcardListeners($event)
    {
        $result = [];
        $eventName = $event->name;
        
        foreach($this->wildcards as $name=>$items) {
            if (fnmatch($name, $eventName)) {
                foreach($this->wildcards[$name] as $e)
                    $result[] = $e;
            }
        }
        
        return $result;
    }
    
    public function hasListeners($event)
    {
        $name = $event->name;
        return isset($this->listeners[$name]) ? count($this->listeners[$name])>0 : false; 
    }
    
}