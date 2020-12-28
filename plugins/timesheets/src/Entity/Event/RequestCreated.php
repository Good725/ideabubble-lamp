<?php


namespace Ideabubble\Timesheets\Entity\Event;


class RequestCreated
{
    public $requestId;
    
    public function __construct($requestId)
    {
        $this->requestId = $requestId;
    }
}