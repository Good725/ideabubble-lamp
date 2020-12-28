<?php


namespace Ideabubble\Timeoff\Entity;


class ScheduleEvent
{
    private $id;
    private $name;
    private $datetimeStart;
    private $datetimeEnd;
    
    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * @return mixed
     */
    public function getDatetimeStart()
    {
        return $this->datetimeStart;
    }
    
    /**
     * @return mixed
     */
    public function getDatetimeEnd()
    {
        return $this->datetimeEnd;
    }
    
    
}