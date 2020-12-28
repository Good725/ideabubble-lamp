<?php


namespace Ideabubble\Timeoff\Entity;


use Ideabubble\Timeoff\Entity\Event\RequestCreated;
use Ideabubble\Timeoff\Entity\Event\RequestStatusUpdated;

class Request
{
    use EventTrait;
    
    private $id;
    private $staffId;
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
    
    private $departmentId;
    
    private $businessId;
    
    private $createdAt;
    private $staffUpdatedAt;
    private $managerUpdatedAt;
    
    public function __construct($id, $staffId, $departmentId, $businessId, Period $period, Type $type)
    {
        $this->id = $id;
        $this->staffId = $staffId;
        $this->departmentId = $departmentId;
        $this->businessId = $businessId;
        $this->period = $period;
        $this->type = $type;
        $this->status = new Status(Status::PENDING);
        $this->createdAt = date('Y-m-d H:i:s');
        $this->staffUpdatedAt = date('Y-m-d H:i:s');
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

    public function can_view()
    {
        if (!\Auth::instance()->has_access('timeoff_requests_edit')) {
            $contact = \Auth::instance()->get_contact();
            if ($this->staffId != $contact->id && $contact->get_role_in($this->departmentId) != 'manager') {
                return false;
            }
        }

        return true;
    }

    public function can_edit()
    {
        if (!\Auth::instance()->has_access('timeoff_requests_edit')) {
            $contact = \Auth::instance()->get_contact();

            if ($contact->get_role_in($this->departmentId) != 'manager') {
                if (!$this->can_view() || $this->status->getValue() != 'pending') {
                    return false;
                }
            }
        }

        return true;
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
    
    
    
}