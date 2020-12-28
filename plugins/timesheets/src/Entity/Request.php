<?php


namespace Ideabubble\Timesheets\Entity;


use Ideabubble\Timesheets\Dto\RequestSearchDto;
use Ideabubble\Timesheets\Entity\Event\RequestCreated;
use Ideabubble\Timesheets\Entity\Event\RequestStatusUpdated;

class Request
{
    use EventTrait;
    
    private $id;
    private $staffId;
    private $scheduleId;
    private $todoId;
    private $timesheetId;
    private $description;
    /**
     * @var Period
     */
    private $period;
    /**
     * @var Type
     */
    private $type;
    /**
     * @var Status
     */
    private $status;
    
    private $deleted;
    
    private $departmentId;
    
    private $businessId;
    
    private $createdAt;
    private $staffUpdatedAt;
    private $managerUpdatedAt;
    
    public function __construct(RequestSearchDto $dto)
    {
        $this->id = $dto->id;
        $this->staffId = $dto->staffId;
        $this->departmentId = $dto->departmentId;
        $this->businessId = $dto->businessId;
        $this->timesheetId = $dto->timesheetId;
        $this->todoId = $dto->todoId;
        $this->scheduleId = $dto->scheduleId;
        $this->period = new Period($dto->startDate, $dto->endDate, $dto->duration);
        $this->type = new Type($dto->type);
        $this->status = $dto->status;
        $this->createdAt = date('Y-m-d H:i:s');
        $this->staffUpdatedAt = date('Y-m-d H:i:s');
        $this->deleted = 0;
        $this->description = $dto->description;
        $this->recordEvent(new RequestCreated($this->id));
    }
    
    public function getStatus()
    {
        return $this->status->getValue();
    }

    public function setStatus(Status $status)
    {
        $this->status = $status;
        $this->recordEvent(new RequestStatusUpdated($this->id));
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
    
    public function getDepartmentId()
    {
        return $this->departmentId;
    }
    
    /**
     * @param mixed $departmentId
     */
    public function setDepartmentId($departmentId)
    {
        $this->departmentId = $departmentId;
    }
    
    
    
    /**
     * @return mixed
     */
    public function getBusinessId()
    {
        return $this->businessId;
    }

    /**
     * @param mixed $staffId
     */
    public function setStaffId($staffId)
    {
        $this->staffId = $staffId;
    }
    
    /**
     * @return Period
     */
    public function getPeriod(): Period
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
     * @return string
     */
    public function getType()
    {
        return $this->type->getValue();
    }
    
    /**
     * @param Type $type
     */
    public function setType(Type $type)
    {
        $this->type = $type;
    }
    
    public function setStaffUpdated()
    {
        $this->staffUpdatedAt = date('Y-m-d H:i:s');
    }
    
    public function setManagerUpdated()
    {
        $this->managerUpdatedAt = date('Y-m-d H:i:s');
    }
    
    /**
     * @return string
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }
    
    /**
     * @return string
     */
    public function getStaffUpdatedAt()
    {
        return $this->staffUpdatedAt;
    }
    
    /**
     * @return string
     */
    public function getManagerUpdatedAt()
    {
        return $this->managerUpdatedAt;
    }
    
    /**
     * @return int
     */
    public function getDeleted()
    {
        return $this->deleted;
    }
    
    /**
     * @param int $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }
    
    /**
     * @return mixed
     */
    public function getScheduleId()
    {
        return $this->scheduleId;
    }
    
    /**
     * @param mixed $scheduleId
     */
    public function setScheduleId($scheduleId)
    {
        $this->scheduleId = $scheduleId;
    }
    
    /**
     * @return mixed
     */
    public function getTodoId()
    {
        return $this->todoId;
    }
    
    /**
     * @param mixed $todoId
     */
    public function setTodoId($todoId)
    {
        $this->todoId = $todoId;
    }
    
    /**
     * @return mixed
     */
    public function getTimesheetId()
    {
        return $this->timesheetId;
    }
    
    /**
     * @param mixed $timesheetId
     */
    public function setTimesheetId($timesheetId)
    {
        $this->timesheetId = $timesheetId;
    }
    
    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }
    
}