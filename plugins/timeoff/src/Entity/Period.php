<?php


namespace Ideabubble\Timeoff\Entity;


use Ideabubble\Timeoff\Exception;

class Period
{
    private $startDate;
    private $endDate;
    private $duration;
    
    const HOUR = 60*60;
    
    public function __construct($startDate, $endDate, $duration)
    {
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->duration = $duration;
    }
    
    public function getStartDate()
    {
        return $this->startDate;
    }
    
    public function getEndDate()
    {
        return $this->endDate;
    }
    
    public function getDuration()
    {
        return $this->duration;
    }
    
    
    
}