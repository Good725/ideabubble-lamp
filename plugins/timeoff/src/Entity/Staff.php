<?php


namespace Ideabubble\Timeoff\Entity;


class Staff
{
    private $id;
    private $firstName;
    private $lastName;
    private $userId;
    /**
     * @var DeptAssignment[]
     */
    private $assignments = [];
    
    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
    
    public function getUserId()
    {
        return $this->userId;
    }
    
    /**
     * @return string
     */
    public function getName()
    {
        return $this->firstName . ' ' . $this->lastName;
    }
    
    /**
     * @return DeptAssignment[]
     */
    public function getAssignments()
    {
        return $this->assignments;
    }
    
    public function isMemberOf($departmentId)
    {
        if (count($this->assignments) > 0) {
            foreach ($this->assignments as $item) {
                if ($item->getDepartmentId() == $departmentId) {
                    return true;
                }
            }
        }
        return false;
    }
    
    public function isManagerOf($departmentId)
    {
        if (count($this->assignments) > 0) {
            foreach ($this->assignments as $item) {
                if ($item->getDepartmentId() == $departmentId && $item->getRole() == 'manager') {
                    return true;
                }
            }
        }
        return false;
    }
    
    public function positionIn($departmentId)
    {
        if (count($this->assignments) > 0) {
            foreach ($this->assignments as $item) {
                if ($item->getDepartmentId() == $departmentId) {
                    return $item->getPosition();
                }
            }
        }
        return null;
        
    }
    
    /**
     * @param DeptAssignment[] $assignments
     */
    public function setAssignments(array $assignments)
    {
        $this->assignments = $assignments;
    }
    
    
    
}