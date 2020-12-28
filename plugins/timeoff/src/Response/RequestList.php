<?php


namespace Ideabubble\Timeoff\Response;


class RequestList
{
    /**
     * @var Request[]
     */
    public $items = [];
    public $total = 0;
    public $status;
    public $error;
}