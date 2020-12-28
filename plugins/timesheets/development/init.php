<?php
spl_autoload_register(function($className) {
    $namespace = 'Ideabubble\Timesheets\\';
    if (substr($className, 0, strlen($namespace)) == $namespace) {
        $localName = substr($className, strlen($namespace));
        $filename = dirname(__DIR__) . '/src/' . str_replace('\\', DIRECTORY_SEPARATOR, $localName) . '.php';
        include $filename;
    }
});

/**
 * @return \Ideabubble\Timesheets\Kohana\KohanaEventDispatcher
 */
function timesheets_event_dispatcher()
{
    $events = [
    ];
    $dispatcher = new \Ideabubble\Timesheets\Kohana\KohanaEventDispatcher($events);
    return $dispatcher;
}


