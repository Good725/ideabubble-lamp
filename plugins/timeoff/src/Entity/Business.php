<?php


namespace Ideabubble\Timeoff\Entity;


class Business
{
    private $id;
    private $name;
    
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
    
}