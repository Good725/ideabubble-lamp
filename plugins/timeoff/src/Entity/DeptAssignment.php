<?php


namespace Ideabubble\Timeoff\Entity;


use Ideabubble\Timeoff\Exception;

class DeptAssignment
{
    private $departmentId;
    private $staffId;
    private $role;
    private $position;
    
    public function __construct($departmentId, $staffId, $role, $position)
    {
        $this->departmentId = $departmentId;
        $this->staffId = $staffId;
        if (!in_array($role, ['staff', 'member'])) {
            throw new Exception('Incorrect department role: ' . $role);
        }
        $this->role = $role;
        $this->position = $position;
    }
    
    /**
     * @return mixed
     */
    public function getDepartmentId()
    {
        return $this->departmentId;
    }
    
    /**
     * @return mixed
     */
    public function getStaffId()
    {
        return $this->staffId;
    }
    
    /**
     * @return mixed
     */
    public function getRole()
    {
        return $this->role;
    }
    
    public function getPosition()
    {
        return $this->position;
    }
    
    
}