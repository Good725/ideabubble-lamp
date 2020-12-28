<?php


namespace Ideabubble\Timesheets\Entity\Event;


class RequestStatusUpdated
{
    public $requestId;
    
    public function __construct($requestId)
    {
        $this->requestId = $requestId;
    }
}