<?php


namespace Ideabubble\Timeoff\Entity\Event;


class RequestStatusUpdated
{
    public $requestId;
    
    public function __construct($requestId)
    {
        $this->requestId = $requestId;
    }
}