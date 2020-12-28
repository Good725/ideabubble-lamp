<?php
spl_autoload_register(function($className) {
    $namespace = 'Ideabubble\Timeoff\\';
    if (substr($className, 0, strlen($namespace)) == $namespace) {
        $localName = substr($className, strlen($namespace));
        $filename = dirname(__DIR__) . '/src/' . str_replace('\\', DIRECTORY_SEPARATOR, $localName) . '.php';
        include $filename;
    }
});

/**
 * @return \Ideabubble\Timeoff\Kohana\KohanaEventDispatcher
 */
function timeoff_event_dispatcher()
{
    $events = [
        \Ideabubble\Timeoff\Entity\Event\RequestCreated::class => [
            Model_Requestcreated::class
        ],
        \Ideabubble\Timeoff\Entity\Event\RequestStatusUpdated::class => [
            Model_Requeststatusupdated::class
        ],
        
    ];
    $dispatcher = new \Ideabubble\Timeoff\Kohana\KohanaEventDispatcher($events);
    return $dispatcher;
}

