<?php


namespace Ideabubble\Timeoff\Entity;


class Schedule
{
    private $id;
    private $name;
    private $trainerId;
    
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
    public function getTrainerId()
    {
        return $this->trainerId;
    }
    
    
    
    
}