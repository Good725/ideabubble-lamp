<?php


namespace Ideabubble\Timesheets;


interface EventDispatcher
{
    public function dispatchAll(array $events);
}