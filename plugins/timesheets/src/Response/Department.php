<?php


namespace Ideabubble\Timesheets\Response;


class Department
{
    public $id;
    public $name;
    
    public function __construct(\Ideabubble\Timesheets\Entity\Department $department)
    {
        $this->id = $department->getId();
        $this->name = $department->getName();
    }
}