<?php
use Ideabubble\Timeoff\Entity\Request;
use Ideabubble\Timeoff\Dto\RequestSearchDto;
use Ideabubble\Timeoff\Entity\Period;
use Ideabubble\Timeoff\Entity\Type;
use Ideabubble\Timeoff\RequestService;
use Ideabubble\Timeoff\Kohana\KohanaRequestRepository;
use Ideabubble\Timeoff\Entity\Note;
use Ideabubble\Timeoff\Dto\DeptSearchDto;

class Controller_Admin_Timeoff extends Controller_Cms
{
    
    private $requestService;
    private $staffService;
    private $departmentService;
    /**
     * @var \Ideabubble\Timeoff\Kohana\KohanaGenerator
     */
    private $gen;

    function before()
    {
        parent::before();
        $this->template->sidebar = View::factory('sidebar');
        $auth = Auth::instance();
        $menus = [];
        $menus[] = ['name' => 'My requests', 'link' => '/admin/timeoff', 'icon' => 'my-requests'];
        if ($auth->has_access('timeoff_requests_edit')) {
            $menus[] = ['name' => 'All requests', 'link' => '/admin/timeoff/all_requests', 'icon' => 'all-requests'];
        }
        $this->template->sidebar->menus = [$menus];
    }

    public function __construct(\Request $request, \Response $response)
    {
        // would be nice to have dependency injection here, but its Kohana :)
        $dispatcher = timeoff_event_dispatcher();
        $this->gen = new \Ideabubble\Timeoff\Kohana\KohanaGenerator();
        $departmentRepository = new \Ideabubble\Timeoff\Kohana\KohanaDepartmentRepository();
        $this->staffService = new \Ideabubble\Timeoff\StaffService(new \Ideabubble\Timeoff\Kohana\KohanaStaffRepository($dispatcher));
        $configService = new \Ideabubble\Timeoff\ConfigService(new \Ideabubble\Timeoff\Kohana\KohanaConfigRepository($dispatcher));
        $scheduleRepository = new \Ideabubble\Timeoff\Kohana\KohanaScheduleEventRepository();
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
        
        parent::__construct($request, $response);
    }
    
    public function action_index()
    {
        $this->action_my_requests();
    }
    
    private function buildPage()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('timeoff_requests_edit') && !$auth->has_access('timeoff_requests_edit_limited')) {
            IbHelpers::set_message("You don't have timeoff permissions!", 'warning popup_box');
            $this->response->status(403);
            $this->request->redirect('/admin');
        }
        $stylesheets = array(
            URL::get_engine_assets_base().'css/bootstrap.daterangepicker.min.css' => 'screen',
            URL::get_engine_plugin_assets_base('timeoff').'css/timeoff.css' => 'screen'
        );
        $this->template->styles    = array_merge($this->template->styles, $stylesheets);
        $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/bootstrap.daterangepicker.min.js"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('timeoff') . 'js/timeoff_new.js"></script>';

        $user = Auth::instance()->get_user();
        $contact = Model_Contacts3::get_linked_contact_to_user($user['id']);
    
        $departments = $this->departmentService->findAll(new DeptSearchDto());
        $departmentsAssoc = [];
        foreach ($departments as $dept) {
            $departmentsAssoc[$dept->getId()] = $dept->getName();
        }

        $staff = $this->staffService->findById($contact['id']);
        if (empty($staff)) {
            IbHelpers::set_message("This user has no linked record in contacts3 plugin!", 'warning popup_box');
            $this->response->status(403);
            $this->request->redirect('/admin');
        }
        $items = $staff->getAssignments();
        $user_departments = [];
        foreach ($items as $item) {
            $user_departments[] = ['id' => $item->getDepartmentId(),  'name' => $departmentsAssoc[$item->getDepartmentId()],  'role' => $item->getRole(), 'position' => $item->getPosition()];
        }

        $period_list = [
            '2019' => ['name' => '2019', 'startDate' => '2019-01-01', 'endDate' => '2019-12-31'],
            '2020' => ['name' => '2020', 'startDate' => '2020-01-01', 'endDate' => '2020-12-31'],
        ];

        $staff_role = new Model_Contacts3_Type(['name' => 'staff', 'deletable' => 0]);
        $staff_members = $staff_role->contacts->order_by('last_name')->find_all_undeleted();

        $this->template->body = View::factory('timeoff', [
            'departments'      => $departmentsAssoc,
            'role'             => 'staff',
            'user'             => $user,
            'user_departments' => $user_departments,
            'staff_members'    => $staff_members,
            'staffId'          => $staff->getId(),
            'period_list'      => $period_list,
            'leave_types'      => Database::instance()->list_columns('plugin_timeoff_requests')['type']['options'] ?? [],
        ]);

        $this->template->sidebar->breadcrumbs = array(
            array('name' => 'Home',  'link' => '/admin'),
            array('name' => 'Timeoff', 'link' => '/admin/timeoff')
        );

        $this->template->styles[URL::get_engine_assets_base() . 'css/validation.css'] = 'screen';
        $this->template->scripts[] = '<script src="' . URL::get_engine_assets_base() . 'js/jquery.validationEngine2.js"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_assets_base() . 'js/jquery.validationEngine2-en.js"></script>';
    }

    public function action_my_requests()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('timeoff_requests_edit') && !$auth->has_access('timeoff_requests_edit_limited')) {
            IbHelpers::set_message("You don't have timeoff permissions!", 'warning popup_box');
            $this->response->status(403);
            $this->request->redirect('/admin');
        }

        $this->buildPage();
        $this->template->body->role = 'staff';
        $this->template->body->filterStaffId = Model_Contacts3::get_linked_contact_to_user($auth->get_user()['id'])['id'];
        $this->template->sidebar->breadcrumbs[] = array('name' => 'My requests', 'link' => '#');

    }

    public function action_all_requests()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('timeoff_requests_edit')) {
            IbHelpers::set_message("You don't have timeoff permissions!", 'warning popup_box');
            $this->response->status(403);
            $this->request->redirect('/admin');
        }

        $this->buildPage();
        $this->template->body->role = 'manager';
        $this->template->sidebar->breadcrumbs[] = array('name' => 'All requests', 'link' => '#');
    }

    public function action_settings()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('timeoff_requests_edit')) {
            IbHelpers::set_message("You don't have timeoff permissions!", 'warning popup_box');
            $this->response->status(403);
            $this->request->redirect('/admin');
        }
    }

    public function action_ajax_get_datatable()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('timeoff_requests_edit') && !$auth->has_access('timeoff_requests_edit_limited')) {
            IbHelpers::set_message("You don't have timeoff permissions!", 'warning popup_box');
            $this->response->status(403);
            $this->request->redirect('/admin');
        }

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
        $auth = Auth::instance();
        $return['items'] = array();

        if ($auth->has_access('timeoff_requests_edit') || $auth->has_access('timeoff_requests_edit_limited')) {
            $return['items'][] = array('title' => 'My requests', 'link' => '/admin/timeoff/my_requests', 'icon_svg' => 'my-requests');
        }

        if ($auth->has_access('timeoff_requests_edit')) {
            $return['items'][] = array('title' => 'All requests', 'link' => '/admin/timeoff/all_requests', 'icon_svg' => 'forms');
        }

        return $return;
    }
}
