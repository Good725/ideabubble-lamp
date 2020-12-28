<?php
use Ideabubble\Timesheets\Entity\Request;
use Ideabubble\Timesheets\Dto\RequestSearchDto;
use Ideabubble\Timesheets\Entity\Period;
use Ideabubble\Timesheets\Entity\Type;
use Ideabubble\Timesheets\RequestService;
use Ideabubble\Timesheets\Kohana\KohanaRequestRepository;
use Ideabubble\Timesheets\Entity\Note;
use Ideabubble\Timesheets\Dto\DeptSearchDto;
use Ideabubble\Timesheets\Entity\Ref;
use Ideabubble\Timesheets\Dto\ScheduleSearchDto;


class Controller_Api_Timesheets extends Controller_Api
{
    private $requestService;
    private $staffService;
    private $departmentService;
    private $detailsReport;
    private $configService;
    private $scheduleService;
    private $timesheetService;
    /**
     * @var \Ideabubble\Timesheets\Kohana\KohanaGenerator
     */
    private $gen;
    
    
    private function userId()
    {
        if (isset($_REQUEST['_user_id']) && strpos($_SERVER['HTTP_HOST'], 'websitecms.local') > 0) {
            return $_REQUEST['_user_id'];
        } else {
            $userId = Auth::instance()->get_user()['id'];
            $staff = $this->staffService->findByUserId($userId);
            return $staff->getId();
        }
        
    }
    
    
    public function __construct(\Request $request, \Response $response)
    {
        // would be nice to have dependency injection here, but its Kohana :)
        $dispatcher = timesheets_event_dispatcher();
        $this->gen = new \Ideabubble\Timesheets\Kohana\KohanaGenerator();
        $departmentRepository = new \Ideabubble\Timesheets\Kohana\KohanaDepartmentRepository();
        $this->scheduleService = new \Ideabubble\Timeoff\ScheduleService(new \Ideabubble\Timeoff\Kohana\KohanaScheduleRepository());
        $this->staffService = new \Ideabubble\Timesheets\StaffService(new \Ideabubble\Timesheets\Kohana\KohanaStaffRepository($dispatcher));
        $configService = new \Ideabubble\Timesheets\ConfigService(new \Ideabubble\Timesheets\Kohana\KohanaConfigRepository($dispatcher));
        $this->configService = $configService;
        $this->departmentService = new \Ideabubble\Timesheets\DepartmentService($departmentRepository);
        $timesheetRepository = new \Ideabubble\Timesheets\Kohana\KohanaTimesheetRepository($dispatcher);
        $this->timesheetService = new \Ideabubble\Timesheets\TimesheetService($timesheetRepository);
        $permissionManager = new \Ideabubble\Timesheets\Kohana\KohanaPermissionManager();
        $this->requestService = new RequestService(
            $permissionManager,
            $configService,
            new KohanaRequestRepository($dispatcher),
            new \Ideabubble\Timesheets\Kohana\KohanaNoteRepository($dispatcher),
            $this->staffService,
            $departmentRepository,
            $this->scheduleService,
            $this->timesheetService,
            new Model_Todos(),
            $this->gen
        );
        $this->detailsReport = new \Ideabubble\Timesheets\Kohana\DetailsReport($this->requestService, $this->staffService);
        
        
        parent::__construct($request, $response);
    }
    
    public function action_index()
    {
        $dto = new RequestSearchDto();
        if ($this->request->query('level') == 'contact') {
            if (!Auth::instance()->has_access('timesheets_edit')) {
                $userId = Auth::instance()->get_user()['id'];
                $staff = $this->staffService->findByUserId($userId);
                $dto->staffId = $staff->getId();
            } else {
                $dto->staffId = $this->request->query('staff_id');
            }
        }

        $dto->idList = $this->request->query('id_list') ? explode(',', $this->request->query('id_list')) : null;
        $dto->managerId = $this->request->query('manager_id');
        $dto->departmentId = $this->request->query('department_id') ? explode(',',$this->request->query('department_id')) : null;
        $dto->businessId = $this->request->query('business_id');
        $dto->timesheetId = $this->request->query('timesheet_id');
        $dto->status = $this->request->query('status') ? explode(',', $this->request->query('status')) : null;
        $dto->type = $this->request->query('type') ? explode(',', $this->request->query('type')) : null;
        $dto->startDate = $this->request->query('period_start_date');
        $dto->endDate = $this->request->query('period_end_date');
        $dto->orderBy = $this->request->query('order_by');
        $dto->orderDir = $this->request->query('order_dir');
        $dto->text = $this->request->query('text');
        $dto->offset = $this->request->query('offset');
        $dto->limit = $this->request->query('limit');
        $dto->datesMode = $this->request->query('dates_mode') ? $this->request->query('dates_mode') : RequestSearchDto::DATES_OVERLAP;
        $this->response_data = $this->requestService->worklogs($dto, $this->userId());
    }
    
    public function action_stats()
    {
        $ref = new Ref($this->request->query('level'), explode(',', $this->request->query('level_id')));
        $startDate = $this->request->query('period_start_date');
        $endDate = $this->request->query('period_end_date');
        $this->response_data = $this->requestService->stats($ref, $startDate, $endDate, $this->userId());
    }
    
    public function action_sumduration()
    {
        $dto = new RequestSearchDto();
        $dto->staffId = $this->request->query('staff_id');
        $dto->idList = $this->request->query('id_list') ? explode(',', $this->request->query('id_list')) : null;
        $dto->managerId = $this->request->query('manager_id');
        $dto->departmentId = $this->request->query('department_id');
        $dto->businessId = $this->request->query('business_id');
        $dto->status = $this->request->query('status') ? explode(',', $this->request->query('status')) : null;
        $dto->type = $this->request->query('type') ? explode(',', $this->request->query('type')) : null;
        $dto->startDate = $this->request->query('period_start_date');
        $dto->endDate = $this->request->query('period_end_date');
        $this->response_data = ['status'=>'success', 'duration'=>$this->requestService->sumDuration($dto)];
    }
    
    public function action_getrequest()
    {
        $dto = new RequestSearchDto();
        $dto->id = $this->request->query('id');
        $request = $this->requestService->findOne($dto);
        $response = $this->requestService->respRequest($request);
        $this->response_data = $response;
    }
    
    
    public function action_ismanager()
    {
        $staff = $this->staffService->findById($this->userId());
        $departmentId = $this->request->query('department_id');
        $this->response_data = ['is_manager' => $staff->isManagerOf($departmentId)];
    }
    
    public function action_duration()
    {
        $minutes = $this->requestService->getDuration($this->request->query('period_start_date'), $this->request->query('period_end_date'));
        $this->response_data = ['minutes' => $minutes, 'hours' => ceil($minutes / 60)];
    }
    
    public function action_submit()
    {
        $post = $this->request->post();
        try {
            $dto = new RequestSearchDto();

            $dto->id = @$post['id'] ?: $this->gen->nextId();
            $dto->staffId = @$post['staff_id'];
            $dto->departmentId = @$post['department_id'];
            // No department ID, get the contact's department
            if (!$dto->departmentId) {
                $departments = Model_Contacts3::get_parent_related_contacts($dto->staffId);
                $dto->departmentId = current($departments);
            }
            // Still no department ID, use a default
            if (!$dto->departmentId) {
                $dto->departmentId = 0;
            }
            $dto->businessId = 0;
            //$dto->timesheetId = $post->request->timesheet_id;
            $dto->todoId = $post['todo_id'];
            $dto->scheduleId = $post['schedule_id'];
            $dto->startDate = $post['start_date'];
            $dto->endDate = $post['end_date'];
            $dto->duration = $post['duration'];
            $dto->type = $post['type'];
            $dto->description = $post['description'];
            $request = new Request($dto);
            if (@$post['id']) {
                $this->requestService->update($request);
            } else {
                $this->requestService->submit($request);
            }
            $this->response_data = ['status' => 'success', 'id' => $request->getId()];
        } catch (\Exception $e) {
            $this->response_data = ['status' => 'error', 'error' => $e->getMessage()];
        }
    }

    public function action_request()
    {
        $id = $this->request->post('id');
        $request = $this->requestService->findById($id);
        $request = array(
            'id' => $request->getId(),
            'staff_id' => $request->getStaffId(),
            'staff' => $this->staffService->findById($request->getStaffId())->getName(),
            'schedule_id' => $request->getScheduleId(),
            'schedule' => @Model_Schedules::get_schedule($request->getScheduleId())['name'],
            'todo_id' => $request->getTodoId(),
            'todo' => @Model_Todos::search(array('id' => $request->getTodoId()))[0]['title'],
            'timesheet_id' => $request->getTimesheetId(),
            'department_id' => $request->getDepartmentId(),
            'description' => $request->getDescription(),
            'start_date' => $request->getPeriod()->getStartDate(),
            'end_date' => $request->getPeriod()->getEndDate(),
            'duration' => $request->getPeriod()->getDuration()
        );
        $this->response_data = array('status' => 'success', 'request' => $request);
    }

    
    public function action_approve()
    {
        try {
            $this->requestService->approve($this->request->post('id'), $this->userId(), $this->request->post('note'));
            $this->response_data = ['status' => 'success'];
        } catch (\Exception $e) {
            $this->response_data = ['status' => 'error', 'error' => $e->getMessage()];
        }
    }
    
    public function action_decline()
    {
        try {
            $this->requestService->decline($this->request->post('id'), $this->userId(), $this->request->post('note'));
            $this->response_data = ['status' => 'success'];
        } catch (\Exception $e) {
            $this->response_data = ['status' => 'error', 'error' => $e->getMessage()];
        }
    }



//    public function action_update()
//    {
//        try {
//            //$user = Auth::instance()->get_user();
//            $dto = new RequestSearchDto();
//            $dto->id = $this->request->post('id');
//            $request = $this->requestService->findOne($dto);
//            $request->setStatus(new Status($this->request->post('status')));
//            $request->setType(new Type($this->request->post('type')));
//            $this->requestService->update($request, $this->userId(), $this->request->post('note'));
//            $this->response_data = ['status'=>'success'];
//        } catch (\Exception $e) {
//            $this->response_data = ['status'=>'error', 'error' => $e->getMessage()];
//        }
//    }
    
    public function action_addnote()
    {
        try {
            $note = new Note($this->gen->nextId(), $this->request->post('request_id'), $this->userId(), $this->request->post('content'));
            $this->requestService->addNote($note);
            $this->response_data = ['status' => 'success'];
        } catch (\Exception $e) {
            $this->response_data = ['status' => 'error', 'error' => $e->getMessage()];
        }
    }
    
    public function action_assignments()
    {
        try {
            $staff = $this->staffService->findById($this->userId());
            $items = $staff->getAssignments();
            $response = [];
            foreach ($items as $item) {
                $response[] = ['department_id' => $item->getDepartmentId(), 'role' => $item->getRole(), 'position' => $item->getPosition()];
            }
            $this->response_data = ['status' => 'success', 'items' => $response];
        } catch (\Exception $e) {
            $this->response_data = ['status' => 'error', 'error' => $e->getMessage()];
        }
    }
    
    public function action_departments()
    {
        $response = new \Ideabubble\Timesheets\Response\DepartmentList();
        try {
            $dto = new DeptSearchDto();
            $items = $this->departmentService->findAll($dto);
            foreach ($items as $item) {
                $response->items[] = new \Ideabubble\Timesheets\Response\Department($item);
            }
            $response->status = 'success';
        } catch (\Exception $e) {
            $response->status = 'error';
            $response->error = $e->getMessage() . $e->getTraceAsString();
        }
        $this->response_data = $response;
        
    }
    
    public function action_details()
    {
        $this->json = false;
        $this->response->headers('Content-Type', 'text/html');
        $dto = new RequestSearchDto();
        $dto->staffId = $this->request->query('staff_id');
        $dto->managerId = $this->request->query('manager_id');
        $dto->departmentId = $this->request->query('department_id');
        $dto->businessId = $this->request->query('business_id');
        $dto->status = $this->request->query('status') ? explode(',', $this->request->query('status')) : null;
        $dto->type = $this->request->query('type') ? explode(',', $this->request->query('type')) : null;
        $dto->startDate = $this->request->query('period_start_date');
        $dto->endDate = $this->request->query('period_end_date');
        $dto->orderBy = $this->request->query('order_by');
        $dto->orderDir = $this->request->query('order_dir');
        $dto->datesMode = RequestSearchDto::DATES_START;
        $html = $this->detailsReport->run($dto, $this->request->query('period_type'), 8);
        echo $html;
    }

    public function action_detailscsv()
    {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename=details.csv');
        header('Pragma: no-cache');
        $dto = new RequestSearchDto();
        $dto->staffId = $this->request->query('staff_id');
        $dto->managerId = $this->request->query('manager_id');
        $dto->departmentId = $this->request->query('department_id');
        $dto->businessId = $this->request->query('business_id');
        $dto->status = $this->request->query('status') ? explode(',', $this->request->query('status')) : null;
        $dto->type = $this->request->query('type') ? explode(',', $this->request->query('type')) : null;
        $dto->startDate = $this->request->query('period_start_date');
        $dto->endDate = $this->request->query('period_end_date');
        $dto->orderBy = $this->request->query('order_by');
        $dto->orderDir = $this->request->query('order_dir');
        $dto->datesMode = RequestSearchDto::DATES_START;
        $csv = $this->detailsReport->csv($dto, $this->request->query('period_type'), 8);
        echo $csv;
        exit;
    }
    
    public function action_staff()
    {
        $dto = new \Ideabubble\Timesheets\Dto\StaffSearchDto();
        $dto->departmentId = $this->request->query('department_id') ? explode(',',$this->request->query('department_id')) : null;
        $dto->businessId = $this->request->query('business_id');
        $dto->role = $this->request->query('role');
        $dto->orderBy = $this->request->query('order_by');
        $dto->orderDir = $this->request->query('order_dir');
        $dto->offset = $this->request->query('offset');
        $dto->limit = $this->request->query('limit');
        
        
        $items = $this->staffService->findAll($dto);
        $resp = new \Ideabubble\Timesheets\Response\StaffList();
        $resp->status = 'success';
        foreach ($items as $item) {
            $staff = new \Ideabubble\Timesheets\Response\Staff();
            $staff->id = $item->getId();
            $staff->name = $item->getName();
            $resp->items[] = $staff;
        }
        $resp->total = $this->staffService->count($dto);
        $this->response_data = $resp;
    }
    
    public function action_managed_staff()
    {
        $items = $this->staffService->managedStaff($this->userId());
        $resp = new \Ideabubble\Timesheets\Response\StaffList();
        $resp->status = 'success';
        if ($items)
        foreach ($items as $item) {
            $staff = new \Ideabubble\Timesheets\Response\Staff();
            $staff->id = $item->getId();
            $staff->name = $item->getName();
            $resp->items[] = $staff;
        }
        $resp->total = count($items);
        $this->response_data = $resp;
    }
    
    public function action_schedules()
    {
        $dto = new \Ideabubble\Timeoff\Dto\ScheduleSearchDto();
        $dto->staffId = $this->request->query('staff_id');
        $dto->name = $this->request->query('name');
        $dto->orderBy = $this->request->query('order_by');
        $dto->orderDir = $this->request->query('order_dir');
        $dto->offset = $this->request->query('offset');
        $dto->limit = $this->request->query('limit');
        $response = [
            'items' => [],
            'total' => 0,
            'status' => 'success'
        ];
        $items = $this->scheduleService->findAll($dto);
        $response['total'] = $this->scheduleService->count($dto);
        foreach ($items as $item) {
            $response['items'][] = ['id' => $item->getId(), 'name' => $item->getName()];
        }
        $this->response_data = $response;
    }
    
    public function action_todo()
    {
        $items = DB::select()->from('plugin_todos_todos2')->where('type', '=', 'Task')->as_object()->execute();
        $response = ['items' => [], 'status' => 'success'];
        foreach ($items as $item) {
            $response['items'][] = ['id' => $item->id, 'title' => $item->title];
        }
        $this->response_data = $response;
    }
    
    public function action_timesheets()
    {
        $dto = new \Ideabubble\Timesheets\Dto\TimesheetDto();
        if (!Auth::instance()->has_access('timesheets_edit') || $this->request->query('level') == 'contact') {
            $userId = Auth::instance()->get_user()['id'];
            $staff = $this->staffService->findByUserId($userId);
            $dto->staffId = $staff->getId();
        } else {
            $dto->staffId = $this->request->query('staff_id');
        }
        $dto->departmentId = $this->request->query('department_id') ? explode(',', $this->request->query('department_id')) : null;
        $dto->startDate = $this->request->query('start_date');
        $dto->endDate = $this->request->query('end_date');
        $dto->status = $this->request->query('status') ? explode(',', $this->request->query('status')) : null;
        $dto->limit = $this->request->query('limit');
        $dto->offset = $this->request->query('offset');
        $dto->orderBy = $this->request->query('order_by');
        $dto->orderDir = $this->request->query('order_dir');
        $this->response_data = $this->requestService->timesheets($dto, $this->userId());
    }

    public function action_timesheet()
    {
        $dto = new \Ideabubble\Timesheets\Dto\TimesheetDto();
        if (!Auth::instance()->has_access('timesheets_edit')) {
            $userId = Auth::instance()->get_user()['id'];
            $staff = $this->staffService->findByUserId($userId);
            $dto->staffId = $staff->getId();
        } else {
            $dto->staffId = $this->request->query('staff_id');
        }
        $dto->id = $this->request->query('id');
        $timesheet = $this->timesheetService->findOne($dto);
        $data = array();
        $data['id'] = $timesheet->getId();
        $data['staff_id'] = $timesheet->getStaffId();
        $data['staff'] = $this->staffService->findById($data['staff_id']);
        $data['staff'] = array(
            'id' => $data['staff_id'],
            'name' => $data['staff']->getName()
        );
        $data['reviewer_id'] = $timesheet->getReviewerId();
        $data['department_id'] = $timesheet->getDepartmentId();
        $data['status'] = $timesheet->getStatus()->getValue();
        $data['period_start_date'] = $timesheet->getPeriod()->getStartDate();
        $data['period_end_date'] = $timesheet->getPeriod()->getEndDate();
        $data['note'] = $timesheet->getNote();
        $data['minutes_available'] = $this->requestService->minutesAvailable(new Ref("timesheet", $timesheet->getId()));
        //$timesheet = current($timesheet);

        $requests_filter = new RequestSearchDto();
        $requests_filter->timesheetId = $data['id'];
        $requests_filter->orderBy = 'period_start_date';
        $requests_filter->orderDir = 'asc';
        $data['requests'] = $this->requestService->worklogs($requests_filter, $this->userId());
        $this->response_data = array('timesheet' => $data, 'status' => 'success');
    }
    
    public function action_ts_submit()
    {
        try {
            $post = $this->request->post();
            foreach ($post['timesheets'] as $tsData) {
                $this->requestService->submitTimesheet($tsData['id'], $this->userId(), $tsData['reviewer_id'], $tsData['note']);
            }
            $this->response_data = ['status'=>'success'];
        } catch (\Exception $e) {
            $this->response_data = ['status'=>'error', 'error'=>$e->getMessage()];
        }
    }
    
    public function action_ts_approve()
    {
        try {
            $post = $this->request->post();
            foreach ($post['timesheets'] as $tsData) {
                $this->requestService->approveTimesheet($tsData['id'], $this->userId(), $tsData['reviewer_id'], $tsData['note']);
            }
            $this->response_data = ['status'=>'success'];
        } catch (\Exception $e) {
            $this->response_data = ['status'=>'error', 'error'=>$e->getMessage()];
        }
    }

    public function action_ts_reject()
    {
        try {
            $post = $this->request->post();
            foreach ($post['timesheets'] as $tsData) {
                $this->requestService->rejectTimesheet($tsData['id'], $this->userId(), $tsData['reviewer_id'], $tsData['note']);
            }
            $this->response_data = ['status'=>'success'];
        } catch (\Exception $e) {
            $this->response_data = ['status'=>'error', 'error'=>$e->getMessage()];
        }
    }

    
}
