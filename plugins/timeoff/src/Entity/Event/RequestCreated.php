<?php


namespace Ideabubble\Timeoff\Entity\Event;


class RequestCreated
{
    public $requestId;
    
    public function __construct($requestId)
    {
        $this->requestId = $requestId;
    }
}