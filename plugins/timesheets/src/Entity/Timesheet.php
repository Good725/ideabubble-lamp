<?php


namespace Ideabubble\Timesheets\Entity;


class Timesheet
{
    use EventTrait;
    private $id;
    private $staffId;
    private $reviewerId;
    private $departmentId;
    /**
     * @var Status
     */
    private $status;
    /**
     * @var Period
     */
    private $period;
    private $note;
    
    
    public function __construct($id, $staffId, $departmentId, Period $period)
    {
        $this->id = $id;
        $this->staffId = $staffId;
        $this->departmentId = $departmentId;
        $this->period = $period;
        $this->reviewerId = 0;
        $this->status = new Status(Status::OPEN);
        $this->note = '';
    }
    
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
    public function getStaffId()
    {
        return $this->staffId;
    }
    
    /**
     * @return mixed
     */
    public function getDepartmentId()
    {
        return $this->departmentId;
    }
    
    
    
    /**
     * @return Status
     */
    public function getStatus()
    {
        return $this->status;
    }
    
    /**
     * @param Status $status
     */
    public function setStatus(Status $status)
    {
        $this->status = $status;
    }
    
    /**
     * @return Period
     */
    public function getPeriod()
    {
        return $this->period;
    }
    
    /**
     * @param Period $period
     */
    public function setPeriod(Period $period)
    {
        $this->period = $period;
    }
    
    /**
     * @return mixed
     */
    public function getNote()
    {
        return $this->note;
    }
    
    /**
     * @param mixed $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }
    
    /**
     * @return mixed
     */
    public function getReviewerId()
    {
        return $this->reviewerId;
    }
    
    /**
     * @param mixed $reviewerId
     */
    public function setReviewerId($reviewerId)
    {
        $this->reviewerId = $reviewerId;
    }
    
    
    
    
    
    
}