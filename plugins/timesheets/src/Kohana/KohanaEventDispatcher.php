<?php


namespace Ideabubble\Timesheets\Kohana;


use Ideabubble\Timesheets\EventDispatcher;

class KohanaEventDispatcher implements EventDispatcher
{
    private $listeners;
    
    public function __construct($listeners = [])
    {
        $this->listeners = $listeners;
    }
    
    public function dispatchAll(array $events)
    {
        foreach ($events as $event) {
            $this->dispatch($event);
        }
    }
    
    private function dispatch($event)
    {
        $eventName = get_class($event);
        if (array_key_exists($eventName, $this->listeners)) {
            foreach ($this->listeners[$eventName] as $listenerClass) {
                $listener = new $listenerClass();
                $listener->handle($event);
            }
        }
    }
    
}