<?php
use Ideabubble\Timesheets\Entity\Request;
use Ideabubble\Timesheets\Dto\RequestSearchDto;
use Ideabubble\Timesheets\Entity\Period;
use Ideabubble\Timesheets\Entity\Type;
use Ideabubble\Timesheets\RequestService;
use Ideabubble\Timesheets\Kohana\KohanaRequestRepository;
use Ideabubble\Timesheets\Entity\Note;
use Ideabubble\Timesheets\Dto\DeptSearchDto;

class Controller_Admin_Timesheets extends Controller_Cms
{
    
    private $requestService;
    private $staffService;
    private $departmentService;
    private $timesheetService;
    /**
     * @var \Ideabubble\Timesheets\Kohana\KohanaGenerator
     */
    private $gen;

    function before()
    {
        parent::before();

        $this->template->sidebar = View::factory('sidebar');
        
        $menus = array();
        if (Auth::instance()->has_access('timesheets_edit') || Auth::instance()->has_access('timesheets_edit_limited')) {
            $menus[] = array('name' => 'My timesheets', 'link' => '/admin/timesheets', 'icon' => 'my-requests');
        }
        if (Auth::instance()->has_access('timesheets_edit')) {
            $menus[] = array('name' => 'All timesheets', 'link' => '/admin/timesheets/all_requests', 'icon' => 'all-requests');
        }
        $this->template->sidebar->menus = array($menus);
    }
    
    public function __construct(\Request $request, \Response $response)
    {
        // would be nice to have dependency injection here, but its Kohana :)
        $dispatcher = timesheets_event_dispatcher();
        $this->gen = new \Ideabubble\Timesheets\Kohana\KohanaGenerator();
        $departmentRepository = new \Ideabubble\Timesheets\Kohana\KohanaDepartmentRepository();
        $this->staffService = new \Ideabubble\Timesheets\StaffService(new \Ideabubble\Timesheets\Kohana\KohanaStaffRepository($dispatcher));
        $configService = new \Ideabubble\Timesheets\ConfigService(new \Ideabubble\Timesheets\Kohana\KohanaConfigRepository($dispatcher));
        $scheduleRepository = new \Ideabubble\Timeoff\Kohana\KohanaScheduleRepository();
        $scheduleService = new \Ideabubble\Timeoff\ScheduleService($scheduleRepository);
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
            $scheduleService,
            $this->timesheetService,
            new Model_Todos(),
            $this->gen
        );
        
        parent::__construct($request, $response);
    }
    
    public function action_index()
    {
        if (!Auth::instance()->has_access('timesheets')) {
            IbHelpers::set_message("You don't have permission to enter timesheets!", 'warning');
            $this->request->redirect('/admin');
        }
        $stylesheets = array(
            URL::get_engine_assets_base().'css/bootstrap.daterangepicker.min.css' => 'screen',
            URL::get_engine_assets_base().'css/validation.css'                    => 'screen',
            URL::get_engine_plugin_assets_base('timesheets').'css/timesheets.css' => 'screen'
        );
        $this->template->styles    = array_merge($this->template->styles, $stylesheets);
        $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/bootstrap.daterangepicker.min.js"></script>';
        $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validationEngine2-en.js"></script>';
        $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validationEngine2.js"></script>';

        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('timesheets').'js/timesheets.js"></script>';

        $user = Auth::instance()->get_user();
        $contact = Model_Contacts3::get_linked_contact_to_user($user['id']);

        $departments = $this->departmentService->findAll(new DeptSearchDto());
        $departmentsAssoc = [];
        foreach ($departments as $dept) {
            $departmentsAssoc[$dept->getId()] = $dept->getName();
        }

        $staff = $this->staffService->findById($contact['id']);
        $items = $staff->getAssignments();
        $user_departments_assoc = [];
        foreach ($items as $item) {
            if ($item->getRole() == 'manager') {
                $user_departments_assoc[$item->getDepartmentId()] = $departmentsAssoc[$item->getDepartmentId()];
            }
        }

        $staffs = array('' => 'Please Select');
        $items = $this->staffService->managedStaff($contact['id']) ?? array();

        foreach ($items as $item) {
            $staffs[$item->getId()] = $item->getName();
        }
        if (count($staffs) == 2) {
            unset($staffs['']);
        }

        $reviewers = array('' => 'Please Select');
        $dto = new \Ideabubble\Timesheets\Dto\StaffSearchDto();
        $dto->role = 'manager';
        $items = $this->staffService->findAll($dto);
        foreach ($items as $item) {
            $reviewers[$item->getId()] = $item->getName();
        }
        if (count($reviewers) == 2) {
            unset($reviewers['']);
        }

        $this->template->body = View::factory('timesheets', [
            'departments'      => $departmentsAssoc,
            'role'             => 'staff',
            'user'             => $user,
            'staffId' => $staff->getId(),
            'staffs' => $staffs,
            'reviewers' => $reviewers
        ]);

        $this->template->sidebar->breadcrumbs = array(
            array('name' => 'Home',  'link' => 'admin'),
            array('name' => 'Timesheets', 'link' => 'admin/timesheets')
        );

    }

    public function action_my_requests()
    {
        $this->action_index();
        $this->template->body->role = 'staff';
        $this->template->sidebar->breadcrumbs[] = array('name' => 'My timesheets', 'link' => '#');

    }

    public function action_all_requests()
    {
        $this->action_index();
        $this->template->body->role = 'manager';
        $this->template->sidebar->breadcrumbs[] = array('name' => 'All timesheets', 'link' => '#');
    }

    public function action_ajax_get_datatable()
    {
        $this->auto_render = false;
        $parameters = $this->request->query();

        // $parameters will contain filters and information on sorting, pagination, etc.
        // todo: Replace with code to get actual records, using the $parameters
        $requests = array(
            array('id' => '300', 'staff_id' => '100', 'name' => 'Fred Flintstone', 'department' => 'Finance', 'position' => 'Boulder management', 'start_date' => '2018-07-01', 'end_date' => '2018-07-03', 'leave_type' => 'Force majeure', 'duration' => '3 days', 'status' => 'approved', 'date_approved' => '2018-06-01'),
            array('id' => '301', 'staff_id' => '100', 'name' => 'Fred Flintstone', 'department' => 'Finance', 'position' => 'Boulder management', 'start_date' => '2018-05-01', 'end_date' => '2018-05-03', 'leave_type' => 'Time in lieu',  'duration' => '3 days', 'status' => 'approved', 'date_approved' => '2018-06-01'),
            array('id' => '302', 'staff_id' => '100', 'name' => 'Fred Flintstone', 'department' => 'Finance', 'position' => 'Boulder management', 'start_date' => '2018-04-20', 'end_date' => '2018-04-21', 'leave_type' => 'Force majeure', 'duration' => '1 day',  'status' => 'pending',  'date_approved' => false)
        );

        $data = array(
            'iTotalDisplayRecords' => '3', // total number of results
            'iTotalRecords' => '3', // number displayed
            'aaData' => array(),
            'sEcho' => 1
        );

        foreach ($requests as $request) {
            $row = array();
            $row[] = $request['id'];
            $row[] = $request['name'];
            $row[] = $request['department'];
            $row[] = $request['position'];
            $row[] = $request['start_date'] ? date('d/m/Y', strtotime($request['start_date'])) : '';
            $row[] = $request['end_date']   ? date('d/m/Y', strtotime($request['end_date']))   : '';
            $row[] = $request['leave_type'];
            $row[] = $request['duration'];
            $row[] = $request['status'];
            $row[] = $request['date_approved'] ? date('d/m/Y', strtotime($request['date_approved'])) : '';
            $row[] = '<div class="action-btn">
                    <a href="#" class="btn" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="icon-ellipsis-h" aria-hidden="true"></span>
                    </a>

                    <ul class="dropdown-menu">
                        <li><button type="button" class="timeoff-requests-table-view" data-id="'.$request['id'].'" data-leave_type="'.$request['leave_type'].'">'.__('view').'</button></li>
                    </ul>
                </div>';

            $data['aaData'][] = $row;
        }


        $this->response->body(json_encode($data));
    }

    public function action_ajax_get_submenu()
    {
        $return['items'] = array();

        if (Auth::instance()->has_access('timesheets_edit') || Auth::instance()->has_access('timesheets_edit_limited')) {
            $return['items'][] = array('title' => 'My timesheets',  'link' => '/admin/timesheets',              'icon_svg' => 'my-requests');
        }

        if (Auth::instance()->has_access('timesheets_edit')) {
            $return['items'][] = array('title' => 'All timesheets', 'link' => '/admin/timesheets/all_requests', 'icon_svg' => 'all-requests');
        }

        return $return;
    }
    
    public function action_autocomplete_schedules()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        
        $trainer_id = null;
        $auth = Auth::instance();
        if (!$auth->has_access('timesheets_edit')) {
            $user = $auth->get_user();
            $trainer = Model_Contacts3::get_linked_contact_to_user($user['id']);
            if ($trainer) {
                $trainer_id = $trainer['id'];
            }
        }
        $alltime = false;
        if ($this->request->query('alltime') == 'yes') {
            $alltime = true;
        }
        
        $course_id = $this->request->query("course_id");
        echo json_encode(Model_courses::autocomplete_search_schedules($this->request->query('term'), $trainer_id, true,
            !$alltime, $course_id));
    }
}
