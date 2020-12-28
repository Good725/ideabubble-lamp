<?php
use Ideabubble\Timeoff\Entity\Request;
use Ideabubble\Timeoff\Dto\RequestSearchDto;
use Ideabubble\Timeoff\Entity\Period;
use Ideabubble\Timeoff\Entity\Status;
use Ideabubble\Timeoff\Entity\Type;
use Ideabubble\Timeoff\RequestService;
use Ideabubble\Timeoff\Kohana\KohanaRequestRepository;
use Ideabubble\Timeoff\Entity\Note;
use Ideabubble\Timeoff\Dto\DeptSearchDto;
use Ideabubble\Timeoff\Entity\Ref;
use Ideabubble\Timeoff\Dto\ScheduleSearchDto;


class Controller_Api_Timeoff extends Controller_Api
{
    private $requestService;
    private $staffService;
    private $departmentService;
    private $detailsReport;
    private $configService;
    /**
     * @var \Ideabubble\Timeoff\Kohana\KohanaGenerator
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
        $dispatcher = timeoff_event_dispatcher();
        $this->gen = new \Ideabubble\Timeoff\Kohana\KohanaGenerator();
        $departmentRepository = new \Ideabubble\Timeoff\Kohana\KohanaDepartmentRepository();
        $scheduleRepository = new \Ideabubble\Timeoff\Kohana\KohanaScheduleEventRepository();
        $this->staffService = new \Ideabubble\Timeoff\StaffService(new \Ideabubble\Timeoff\Kohana\KohanaStaffRepository($dispatcher));
        $configService = new \Ideabubble\Timeoff\ConfigService(new \Ideabubble\Timeoff\Kohana\KohanaConfigRepository($dispatcher));
        $this->configService = $configService;
        $this->departmentService = new \Ideabubble\Timeoff\DepartmentService($departmentRepository);
        $this->requestService = new RequestService(
            $configService,
            new KohanaRequestRepository($dispatcher),
            new \Ideabubble\Timeoff\Kohana\KohanaNoteRepository($dispatcher),
            $this->staffService,
            $departmentRepository,
            $scheduleRepository,
            $this->gen
        );
        $this->detailsReport = new \Ideabubble\Timeoff\Kohana\DetailsReport($this->requestService, $this->staffService);
        
        
        parent::__construct($request, $response);
    }
    
    public function action_index()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('timeoff_requests_edit') && !$auth->has_access('timeoff_requests_edit_limited')) {
            $response = new \Ideabubble\Timeoff\Response\RequestList();
            $response->status = 'error';
            $response->error = "You don't have timeoff permissions!";
            $this->response_data = $response;
            //$this->response->status(403);
            return;
        }

        $response = new \Ideabubble\Timeoff\Response\RequestList();
        try {
            $dto = new RequestSearchDto();
            if ($auth->has_access('timeoff_requests_edit')) {
                $dto->staffId = $this->request->query('staff_id');
            } else {
                $user = $auth->get_user();
                $contact = Model_Contacts3::get_linked_contact_to_user($user['id']);
                $dto->staffId = $contact['id'];
            }
            $dto->idList = $this->request->query('id_list') ? explode(',', $this->request->query('id_list')) : null;
            $dto->managerId = $this->request->query('manager_id');
            $dto->departmentId = $this->request->query('department_id');
            $dto->businessId = $this->request->query('business_id');
            $dto->status = $this->request->query('status');
            if(is_array($this->request->query('type')) && count($this->request->query('type')) > 0) {
                $dto->type = $this->request->query('type');
            } else if ($this->request->query('type')) {
                $dto->type = explode(',', $this->request->query('type'));
            } else {
                $dto->type = null;
            }
            $dto->startDate = $this->request->query('period_start_date');
            $dto->endDate = $this->request->query('period_end_date');
            $dto->orderBy = $this->request->query('order_by');
            $dto->orderDir = $this->request->query('order_dir');
            $dto->offset = $this->request->query('offset');
            $dto->limit = $this->request->query('limit');
            $dto->keyword = $this->request->query('text');
            $dto->datesMode = $this->request->query('dates_mode') ? $this->request->query('dates_mode') : RequestSearchDto::DATES_OVERLAP;
            $dto->exclude_id = $this->request->query('exclude_id');
            $requests = $this->requestService->findAll($dto);
            $response->total = $this->requestService->count($dto);
            foreach ($requests as $request) {
                $response->items[] = $this->requestService->respRequest($request);
            }
            $response->status = 'success';
        } catch (\Exception $e) {
            $response->status = 'error';
            $response->error = $e->getMessage() . $e->getTraceAsString();
        }
        $this->response_data = $response;
    }
    
    public function action_csv()
    {
        $this->request->query('offset', null);
        $this->request->query('limit', null);
        $this->action_index();
        $this->json = false;
        header('Content-Type: application/csv');
        header('Content-Disposition: attachment; filename=requests.csv');
        header('Pragma: no-cache');
        echo "sep=;\n";
        echo 'Staff ID;Full Name;Department;Position;Start Date;End Date;Leave Type;Duration;Status;Date Approved' . "\n";
        foreach ($this->response_data->items as $row) {
            $items = [
                $row->staff['id'],
                $row->staff['name'],
                $row->department['name'],
                $row->staff['position'],
                $row->period[0],
                $row->period[1],
                $row->type,
                $row->period[2],
                $row->status,
                $row->manager_updated_at,
            ];
            echo implode(';', $items) . "\n";
        }
        die;
    }

    public function action_timeoff_conflicts()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('timeoff_requests_edit') && !$auth->has_access('timeoff_requests_edit_limited')) {
            $response = new \Ideabubble\Timeoff\Response\RequestList();
            $response->status = 'error';
            $response->error = "You don't have timeoff permissions!";
            $this->response_data = $response;
            //$this->response->status(403);
            return;
        }

        try {
            
            $dto = new RequestSearchDto();
            $dto->id = $this->request->query('id');
            
            $request = $this->requestService->findOne($dto);
            $conflicts = $this->requestService->timeoffConflicts($dto);
            $response = new \Ideabubble\Timeoff\Response\RequestList();
            foreach ($conflicts as $request) {
                $response->items[] = $this->requestService->respRequest($request);
            }
            $response->status = 'success';
        } catch (\Exception $e) {
            $response->status = 'error';
            $response->error = $e->getMessage() . $e->getTraceAsString();
        }
        $this->response_data = $response;
    }
    
    public function action_schedule_conflicts()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('timeoff_requests_edit') && !$auth->has_access('timeoff_requests_edit_limited')) {
            $response = new \Ideabubble\Timeoff\Response\RequestList();
            $response->status = 'error';
            $response->error = "You don't have timeoff permissions!";
            $this->response_data = $response;
            //$this->response->status(403);
            return;
        }

        try {
            $dto = new ScheduleSearchDto();
            $dto->staffId = $this->request->query('staff_id');
            $dto->startDate = $this->request->query('period_start_date');
            $dto->endDate = $this->request->query('period_end_date');
            $dto->orderBy = $this->request->query('order_by');
            $dto->orderDir = $this->request->query('order_dir');
            $dto->offset = $this->request->query('offset');
            $dto->limit = $this->request->query('limit');
            $response = [];
            $schedules = $this->requestService->scheduleConflicts($dto);
            foreach ($schedules as $item) {
                $dt1 = new DateTime($item->getDatetimeStart());
                $dt2 = new DateTime($item->getDatetimeEnd());
                $response[] = ['id'=>$item->getId(), 'title'=>$item->getName(), 'course'=>$item->getName(), 'date'=>$dt1->format('m/d/Y'), 'time'=>$dt1->format('H:i') . ' - ' . $dt2->format('H:i')];
            }
            $this->response_data = ['status'=>'success', 'items'=>$response];
            
        } catch (\Exception $e) {
            $this->response_data = ['status'=>'error', 'error' => $e->getMessage() . $e->getTraceAsString()];
        }
    }
    
    public function action_stats()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('timeoff_requests_edit') && !$auth->has_access('timeoff_requests_edit_limited')) {
            $response = new \Ideabubble\Timeoff\Response\RequestList();
            $response->status = 'error';
            $response->error = "You don't have timeoff permissions!";
            $this->response_data = $response;
            //$this->response->status(403);
            return;
        }

        $ref = new Ref($this->request->query('level'), $this->request->query('level_id'));
        $startDate = $this->request->query('period_start_date');
        $endDate = $this->request->query('period_end_date');
        $this->response_data = [
            'days_available' => $this->requestService->daysAvailable($ref),
            'days_pending_approval' => $this->requestService->daysPendingApproval($ref, $startDate, $endDate),
            'days_in_lieu' => $this->requestService->daysInLieu($ref, $startDate, $endDate),
            'days_approved' => $this->requestService->daysApproved($ref, $startDate, $endDate),
            'days_left' => $this->requestService->daysLeft($ref, $startDate, $endDate),
            'day_length_minutes' => $this->requestService->dayLengthMinutes($ref),
        ];
    }
    
    public function action_getrequest()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('timeoff_requests_edit') && !$auth->has_access('timeoff_requests_edit_limited')) {
            $response = new \Ideabubble\Timeoff\Response\RequestList();
            $response->status = 'error';
            $response->error = "You don't have timeoff permissions!";
            $this->response_data = $response;
            //$this->response->status(403);
            return;
        }

        $dto = new RequestSearchDto();
        $dto->id = $this->request->query('id');
        $request = $this->requestService->findOne($dto);
        if (!$request->can_view()) {
            $response = new \Ideabubble\Timeoff\Response\RequestList();
            $response->status = 'error';
            $response->error = "You don't have timeoff permission to access that request!";
            $this->response_data = $response;
            return;
        }
        $response = $this->requestService->respRequest($request);
        $this->response_data = $response;
    }
    
    
    public function action_ismanager()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('timeoff_requests_edit') && !$auth->has_access('timeoff_requests_edit_limited')) {
            $response = new \Ideabubble\Timeoff\Response\RequestList();
            $response->status = 'error';
            $response->error = "You don't have timeoff permissions!";
            $this->response_data = $response;
            //$this->response->status(403);
            return;
        }

        $staff = $this->staffService->findById($this->userId());
        $departmentId = $this->request->query('department_id');
        $this->response_data = ['is_manager'=>$staff->isManagerOf($departmentId)];
    }
    
    public function action_duration()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('timeoff_requests_edit') && !$auth->has_access('timeoff_requests_edit_limited')) {
            $response = new \Ideabubble\Timeoff\Response\RequestList();
            $response->status = 'error';
            $response->error = "You don't have timeoff permissions!";
            $this->response_data = $response;
            //$this->response->status(403);
            return;
        }

        $userId = Auth::instance()->get_user()['id'];
        $staff = $this->staffService->findByUserId($userId);

        $start = $this->request->query('period_start_date');
        $end = $this->request->query('period_end_date');
        $department_id = $this->request->query('department_id');
        $type = $this->request->query('type');

        if ($department_id == null) {
            $levels = $this->requestService->getConfigLevels(new Ref("contact", $staff->getId()));
            foreach ($levels as $level) {
                if ($level->getLevel() == 'department') {
                    $department_id = $level->getItemId();
                    break;
                }
            }
        }
        if ($type != 'lieu' && date("H:i", strtotime($start)) == "00:00") {
            $day = date("l", strtotime($start));
            $ref = new Ref("contact", $staff->getId());
            $start_cfg = $this->configService->getx('timeoff.time_preferences_' . $day, $this->requestService->getConfigLevels($ref));
            if ($start_cfg) {
                $start .= ' ' . $start_cfg['start_time'];
            }
        }
        if ($type != 'lieu' && date("H:i", strtotime($end)) == "00:00") {
            $day = date("l", strtotime($end));
            $ref = new Ref("contact", $staff->getId());
            $end_cfg = $this->configService->getx('timeoff.time_preferences_' . $day, $this->requestService->getConfigLevels($ref));
            if ($end_cfg) {
                $end .= ' ' . $end_cfg['end_time'];
            }
        }

        $minutes = $this->requestService->getDuration($start, $end, $department_id, $type);
        $this->response_data = ['minutes' => $minutes];
    }

    public static function seconds_to_hour($seconds)
    {
        $minutes = ceil($seconds / 60);
        $hours = floor($minutes / 60);
        $minutes = $minutes % 60;
        return $hours . 'h ' . $minutes . 'm';
    }

    public function action_submit()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('timeoff_requests_edit') && !$auth->has_access('timeoff_requests_edit_limited')) {
            $response = new \Ideabubble\Timeoff\Response\RequestList();
            $response->status = 'error';
            $response->error = "You don't have timeoff permissions!";
            $this->response_data = $response;
            //$this->response->status(403);
            return;
        }

        $post = json_decode(file_get_contents('php://input'));
        try {
            $userId = Auth::instance()->get_user()['id'];
            $staff = $this->staffService->findByUserId($userId);

            $startDate = $post->request->period[0];
            $endDate = $post->request->period[1];
            $type = isset($this->request->type) ? $this->request->type : '';

            $ref = new Ref("contact", $staff->getId());
            $conf_levels = $this->requestService->getConfigLevels($ref);
            if ($type != 'lieu' && date("H:i", strtotime($startDate)) == "00:00") {
                $day = date("l", strtotime($startDate));

                $start_cfg = $this->configService->getx('timeoff.time_preferences_' . $day, $conf_levels);
                if ($start_cfg) {
                    $startDate .= ' ' . $start_cfg['start_time'];
                }
            }
            if ($type != 'lieu' && date("H:i", strtotime($endDate)) == "00:00") {
                $day = date("l", strtotime($endDate));
                $end_cfg = $this->configService->getx('timeoff.time_preferences_' . $day, $conf_levels);
                if ($end_cfg) {
                    $endDate .= ' ' . $end_cfg['end_time'];
                }
            }

            $department_id = $post->request->department->id;
            if ($department_id == null) {
                foreach ($conf_levels as $conf_level) {
                    if ($conf_level->getLevel() == 'department') {
                        $department_id = $conf_level->getItemId();
                        break;
                    }
                }
            }
            $request = new Request(
                $this->gen->nextId(),
                $this->userId(),
                $department_id,
                0, // todo: business_id
                new Period($startDate, $endDate, 8), //todo: blackouts from frontend?
                new Type($post->request->type)
            );
            $this->requestService->submit($request, $post->note);

            $mm = new Model_Messaging();
            $c3 = new Model_Contacts3($this->userId());

            $recipients = array();
            $managers = \Model_Contacts3::get_child_related_contacts($post->request->department->id, 'manager');
            foreach ($managers as $manager_id) {
                $recipients[] = array(
                    'target_type' => 'CMS_CONTACT3',
                    'target' => $manager_id
                );
            }

            $mm->send_template(
                'timeoff-request-created',
                null,
                null,
                $recipients,
                array(
                    'department' => $post->request->department->name,
                    'name' => $c3->get_first_name() . ' ' . $c3->get_last_name(),
                    'type' => $post->request->type,
                    'status' => $post->request->status,
                    'period' => date('Y', strtotime($post->request->period[0])),
                    'date' => $post->request->period[0],
                    'start_date_time' => date('F j, Y H:i', strtotime($startDate)),
                    'end_date_time' => date('F j, Y H:i', strtotime($endDate)),
                    'duration' => self::seconds_to_hour(strtotime($post->request->period[1]) - strtotime($post->request->period[0])),
                    'note' => $post->note,
                )
            );

            $this->response_data = ['status'=>'success', 'id'=>$request->getId()];
        } catch (\Exception $e) {
            $this->response_data = ['status'=>'error', 'error' => $e->getMessage()];
        }
    }
    
    public function action_approve()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('timeoff_requests_approve') && !$auth->has_access('timeoff_requests_approve_limited')) {
            $response = new \Ideabubble\Timeoff\Response\RequestList();
            $this->response_data = ['status'=>'error', 'error' => 'You don\'t have timeoff permissions!'];
            //$this->response->status(403);
            return;
        }

        try {
            $this->requestService->approve($this->request->post('id'), $this->userId(), $this->request->post('note'), $auth->has_access('timeoff_requests_approve'));
            $this->response_data = ['status'=>'success'];
        } catch (\Exception $e) {
            $this->response_data = ['status'=>'error', 'error' => $e->getMessage()];
        }
    }
    
    public function action_decline()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('timeoff_requests_approve') && !$auth->has_access('timeoff_requests_approve_limited')) {
            $response = new \Ideabubble\Timeoff\Response\RequestList();
            $response->status = 'error';
            $response->error = "You don't have timeoff permissions!";
            $this->response_data = $response;
            //$this->response->status(403);
            return;
        }

        try {
            $this->requestService->decline($this->request->post('id'), $this->userId(), $this->request->post('note'), $auth->has_access('timeoff_requests_approve'));
            $this->response_data = ['status'=>'success'];
        } catch (\Exception $e) {
            $this->response_data = ['status'=>'error', 'error' => $e->getMessage()];
        }
    }
    
    

    public function action_save()
    {
        try {
            $auth = Auth::instance();
            $hasAccess = $auth->has_access('timeoff_requests_edit')
                || $auth->has_access('timeoff_requests_edit_limited')
                || $auth->has_access('timeoff_requests_approve')
                || $auth->has_access('timeoff_requests_approve_limited');

            $post = json_decode(file_get_contents('php://input'));

            $endDate = $post->request->period[1];
            $endDate = (strlen(trim($endDate)) < 12) ? $endDate.' 23:59:60' : $endDate;

            $period = new Period(
                $post->request->period[0],
                $post->request->period[1],
                $this->requestService->getDuration($post->request->period[0], $endDate, $post->request->department_id, $post->request->type)
            );
            $type = new Type($post->request->type);

            //$user = Auth::instance()->get_user();
            $dto = new RequestSearchDto();
            $dto->id = $post->request->id;
            $request = $this->requestService->findOne($dto);
            $request->setType($type);
            $request->setPeriod($period);
            $request->setStatus(new Status($post->request->status));
            $hasAccess = $hasAccess && $request->can_edit();
            $this->requestService->save($request, $this->userId(), $post->note, $hasAccess);
            $this->response_data = ['status'=>'success'];
        } catch (\Exception $e) {
            $this->response_data = ['status'=>'error', 'error' => $e->getMessage()];
        }
    }
    
    public function action_addnote()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('timeoff_requests_edit') && !$auth->has_access('timeoff_requests_edit_limited')) {
            $response = new \Ideabubble\Timeoff\Response\RequestList();
            $response->status = 'error';
            $response->error = "You don't have timeoff permissions!";
            $this->response_data = $response;
            //$this->response->status(403);
            return;
        }

        try {
            $note = new Note($this->gen->nextId(), $this->request->post('request_id'), $this->userId(), $this->request->post('content'));
            $this->requestService->addNote($note);
            $this->response_data = ['status'=>'success'];
        } catch (\Exception $e) {
            $this->response_data = ['status'=>'error', 'error' => $e->getMessage()];
        }
    }
    
    public function action_assignments()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('timeoff_requests_edit') && !$auth->has_access('timeoff_requests_edit_limited')) {
            $response = new \Ideabubble\Timeoff\Response\RequestList();
            $response->status = 'error';
            $response->error = "You don't have timeoff permissions!";
            $this->response_data = $response;
            //$this->response->status(403);
            return;
        }

        try {
            $staff = $this->staffService->findById($this->userId());
            $items = $staff->getAssignments();
            $response = [];
            foreach ($items as $item) {
                $response[] = ['department_id' => $item->getDepartmentId(), 'role' => $item->getRole(), 'position' => $item->getPosition()];
            }
            $this->response_data = ['status'=>'success', 'items' => $response];
        } catch (\Exception $e) {
            $this->response_data = ['status'=>'error', 'error' => $e->getMessage()];
        }
    }
    
    public function action_departments()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('timeoff_requests_edit') && !$auth->has_access('timeoff_requests_edit_limited')) {
            $response = new \Ideabubble\Timeoff\Response\RequestList();
            $response->status = 'error';
            $response->error = "You don't have timeoff permissions!";
            $this->response_data = $response;
            //$this->response->status(403);
            return;
        }

        $response = new \Ideabubble\Timeoff\Response\DepartmentList();
        try {
            $dto = new DeptSearchDto();
            $items = $this->departmentService->findAll($dto);
            foreach ($items as $item) {
                $response->items[] = new \Ideabubble\Timeoff\Response\Department($item);
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
        $auth = Auth::instance();
        if (!$auth->has_access('timeoff_requests_edit') && !$auth->has_access('timeoff_requests_edit_limited')) {
            $response = new \Ideabubble\Timeoff\Response\RequestList();
            $response->status = 'error';
            $response->error = "You don't have timeoff permissions!";
            $this->response_data = $response;
            //$this->response->status(403);
            return;
        }

        $this->json = false;
        $this->response->headers('Content-Type', 'text/html');
        $dto = new RequestSearchDto();
        $dto->staffId = $this->request->query('staff_id');
        $dto->managerId = $this->request->query('manager_id');
        $dto->departmentId = $this->request->query('department_id');
        $dto->businessId = $this->request->query('business_id');
        $dto->status = $this->request->query('status') ? explode(',', $this->request->query('status')) : null;
        if(is_array($this->request->query('type')) && count($this->request->query('type')) > 0) {
            $dto->type = $this->request->query('type');
        } else if ($this->request->query('type')) {
            $dto->type = explode(',', $this->request->query('type'));
        } else {
            $dto->type = null;
        }
        $dto->startDate = $this->request->query('period_start_date');
        $dto->endDate = $this->request->query('period_end_date');
        $dto->orderBy = $this->request->query('order_by');
        $dto->orderDir = $this->request->query('order_dir');
        $dto->datesMode = RequestSearchDto::DATES_START;
        $mode = $this->request->query('mode') ? $this->request->query('mode') : 'html';
        if ($mode == 'csv') {
            $this->response->headers('Content-type', 'text/csv; charset=utf-8');
            $this->response->headers('Content-Disposition', 'attachment; filename=details.csv');
            $this->response->headers('Pragma', 'no-cache');
        }
        $html = $this->detailsReport->run($dto, $this->request->query('period_type'), 8, $mode);
        echo $html;
    }

    public function action_bulk_update()
    {
        $this->auto_render = false;
        //$this->response->headers('Content-type', 'text/plain; charset=utf-8');

        $post = $this->request->post();
        $pc = new Ideabubble\Timeoff\PeriodCalculator();
        foreach ($post['request'] as $request_id => $request) {
            $staff_id = $request['staff_id'];
            $request['start_date'] = date::dmy_to_ymd($request['start_date']);
            $request['end_date'] = date::dmy_to_ymd($request['end_date']);

            $levels = $this->requestService->getConfigLevels(new Ref("contact", $staff_id));
            $department_id = null;
            foreach ($levels as $level) {
                if ($level->getLevel() == 'department') {
                    $department_id = $level->getItemId();
                    break;
                }
            }
            if ($request['start_time'] == '') {
                $day = date("l", strtotime($request['start_date']));
                $ref = new Ref("contact", $staff_id);
                $start_cfg = $this->configService->getx('timeoff.time_preferences_' . $day, $this->requestService->getConfigLevels($ref));
                if ($start_cfg) {
                    $request['start_time'] = $start_cfg['start_time'];
                }
            }

            if ($request['end_time'] == '') {
                $day = date("l", strtotime($request['end_date']));
                $ref = new Ref("contact", $staff_id);
                $end_cfg = $this->configService->getx('timeoff.time_preferences_' . $day, $this->requestService->getConfigLevels($ref));
                if ($end_cfg) {
                    $request['end_time'] = $end_cfg['end_time'];
                }
            }

            //$duration = $pc->calculate($request[''])
            $values = array(
                'period_start_date' => $request['start_date'],
                'period_end_date' => $request['end_date'],
                'type' => $request['type']
            );
            if ($request['start_time']) {
                $values['period_start_date'] .= ' ' . $request['start_time'];
            }
            if ($request['end_time']) {
                $values['period_end_date'] .= ' ' . $request['end_time'];
            }
            if ($request['start_date'] != $request['end_date']) {
                $values['duration'] = $pc->calculate($values['period_start_date'], date('Y-m-d H:i', strtotime($values['period_end_date'] . " +1 day")), $department_id);
            } else {
                $values['duration'] = $pc->calculate($values['period_start_date'], $values['period_end_date'], $department_id);
            }

            DB::update('plugin_timeoff_requests')->set($values)->where('id', '=', $request_id)->execute();
        }
        $this->json = false;
        echo "Updated";
    }

    public function action_get_day_config()
    {
        $this->auto_render = false;
        $this->json = false;
        $date = $this->request->query('date');
        $department_id = $this->request->query('department_id');

        $day_of_week = strtolower(date('l', strtotime($date)));

        $department = new \Model_Contacts3_Contact($department_id);
        $organization_config = $department->parents->find_undeleted()->get_timeoff_hours();

        echo json_encode($organization_config[$day_of_week.'_hours']);
    }
}
