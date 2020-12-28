<?php


namespace Ideabubble\Timeoff;


interface EventDispatcher
{
    public function dispatchAll(array $events);
}