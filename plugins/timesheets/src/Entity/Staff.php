<?php


namespace Ideabubble\Timesheets\Entity;


class Staff
{
    private $id;
    private $firstName;
    private $lastName;
    private $userId;
    private $roleId;
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
     * verify that current user can manage staff. return true if can manage user in at least one department
     * @param Staff $staff
     * @return bool
     */
    public function canManageStaff(Staff $staff)
    {
        $staffAssignments = $staff->getAssignments();
        foreach ($staffAssignments as $item) {
            if ($this->isManagerOf($item->getDepartmentId())) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * @param DeptAssignment[] $assignments
     */
    public function setAssignments(array $assignments)
    {
        $this->assignments = $assignments;
    }
    
    /**
     * @return mixed
     */
    public function getRoleId()
    {
        return $this->roleId;
    }
    
    
    
    
    
}