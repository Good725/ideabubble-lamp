<?php


namespace Ideabubble\Timesheets;


use Ideabubble\Timesheets\Dto\TimesheetDto;
use Ideabubble\Timesheets\Entity\Status;
use Ideabubble\Timesheets\Entity\Timesheet;

class TimesheetService
{
    /**
     * @var TimesheetRepository
     */
    private $timesheetRepository;
    
    public function __construct(TimesheetRepository $timesheetRepository)
    {
        $this->timesheetRepository = $timesheetRepository;
    }
    
    
    
    
    /**
     * @param TimesheetDto $dto
     * @return Timesheet[]
     */
    public function findAll(TimesheetDto $dto)
    {
        return $this->timesheetRepository->findAll($dto);
    }
    
    public function count(TimesheetDto $dto)
    {
        return $this->timesheetRepository->count($dto);
    }
    
    public function findOne(TimesheetDto $dto)
    {
        $items = $this->timesheetRepository->findAll($dto);
        if (count($items) > 0) {
            return $items[0];
        } else {
            return null;
        }
    }
    
    public function findById($id)
    {
        $dto = new TimesheetDto();
        $dto->id = $id;
        return $this->findOne($dto);
    }
    
    public function submit(Timesheet $timesheet)
    {
        if (in_array($timesheet->getStatus()->getValue(), array(Status::OPEN, Status::DECLINED, Status::READY))) {
            $timesheet->setStatus(new Status(Status::PENDING));
            $id = $timesheet->getId();
            $this->update($timesheet);
            $this->sendSubmitAlert($id);
        }
    }

    public static function minute_to_hour($minutes)
    {
        $hours = floor($minutes / 60);
        $minutes = $minutes % 60;
        return $hours . 'h ' . $minutes . 'm';
    }

    public function sendSubmitAlert($id)
    {
        $timesheet = $this->findById($id);
        $duration = \DB::select(\DB::expr("sum(duration) as total"))
            ->from('plugin_timesheets_requests')
            ->where('timesheet_id', '=', $id)
            ->and_where('deleted', '=', 0)
            ->execute()
            ->get('total');
        $recipients = array();
        $params = array();
        $params['comment'] = $timesheet->getNote();
        $params['period'] = date('d/M/Y', strtotime($timesheet->getPeriod()->getStartDate())) . ' - ' . date('d/M/Y', strtotime($timesheet->getPeriod()->getEndDate()));
        $params['duration'] = self::minute_to_hour($duration);
        $dispatcher = timesheets_event_dispatcher();
        $staffService = new \Ideabubble\Timesheets\StaffService(new \Ideabubble\Timesheets\Kohana\KohanaStaffRepository($dispatcher));
        $departmentRepository = new \Ideabubble\Timesheets\Kohana\KohanaDepartmentRepository();
        $params['name'] = $staffService->findById($timesheet->getStaffId())->getName();
        $dep = new DepartmentService($departmentRepository);
        $params['department'] = $dep->findById($timesheet->getDepartmentId())->getName();
        $recipients[] = array(
            'target_type' => 'CMS_CONTACT3',
            'target' => $timesheet->getStaffId()
        );

        /*//disabled for now
         * if ($timesheet->getDepartmentId()) {
            $managers = \Model_Contacts3::get_child_related_contacts($timesheet->getDepartmentId(), 'manager');
            foreach ($managers as $manager_id) {
                $recipients[] = array(
                    'target_type' => 'CMS_CONTACT3',
                    'target' => $manager_id
                );
            }
        }*/
        $mm = new \Model_Messaging();
        $mm->send_template(
            'timesheet-request-created',
            null,
            null,
            $recipients,
            $params
        );
    }

    public function approve(Timesheet $timesheet)
    {
        $timesheet->setStatus(new Status(Status::APPROVED));
        $this->update($timesheet);
    }

    public function reject(Timesheet $timesheet)
    {
        $timesheet->setStatus(new Status(Status::DECLINED));
        $this->update($timesheet);
    }
    
    public function update(Timesheet $timesheet)
    {
        return $this->timesheetRepository->update($timesheet);
    }
    
    public function insert(Timesheet $timesheet)
    {
        return $this->timesheetRepository->insert($timesheet);
    }
    
}