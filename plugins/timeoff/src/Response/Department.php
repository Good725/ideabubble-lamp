<?php


namespace Ideabubble\Timeoff\Response;


class Department
{
    public $id;
    public $name;
    
    public function __construct(\Ideabubble\Timeoff\Entity\Department $department)
    {
        $this->id = $department->getId();
        $this->name = $department->getName();
    }
}