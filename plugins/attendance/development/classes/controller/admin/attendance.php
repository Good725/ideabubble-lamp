<?php

class Controller_Admin_Attendance extends Controller_Cms
{
    protected $_plugin = 'attendance';
    protected $_crud_items = [
        'attendance' => [
            'name' => 'Attendance',
            'model' => 'Booking_Item',
        ]
    ];

    public function before()
    {
        if (!Auth::instance()->has_access('attendance')) {
            IbHelpers::set_message('You need access to the "attendance" permission to perform this action.', 'error popup_box');
            $this->request->redirect('/admin');
        }

        parent::before();

        $this->template->sidebar = View::factory('sidebar');
        $this->template->sidebar->menus = [[
            ['name' => 'My attendance',  'link' => '/admin/attendance', 'icon' => 'surveys'],
            ['name' => 'All attendance', 'link' => '/admin/attendance/all_attendance', 'icon' => 'manage-all-todos'],
        ]];
        $this->template->sidebar->breadcrumbs = [
            ['name' => 'Home',       'link' => '/admin'],
            ['name' => 'Attendance', 'link' => '/admin/attendance']
        ];
    }

    public function action_index($args = [])
    {
        $all = !empty($args['all']);
        $this->template->sidebar->breadcrumbs[] = $all
            ? ['name' => __('All attendance'), 'link' => '/admin/attendance/all_attendance']
            : ['name' => __('My attendance'), 'link' => '/admin/attendance'];

        if ($all) {
            $this->template->sidebar->tools = View::factory('admin/attendance_header_buttons');;
        }

        $staff_role = new Model_Contacts3_Role(['name' => 'teacher']);
        $staff = $staff_role->contacts
            ->order_by('last_name')->order_by('first_name')->find_all_undeleted()->as_array('id', 'full_name');

        if ($all) {
            $students = ORM::factory('Contacts3_Contact')->where_has_bookings()
                ->order_by('last_name')->order_by('first_name')->find_all_undeleted();
            $student_options = $students->as_array('id', 'full_name');
        } else {
            $self = Auth::instance()->get_contact();
            $student_options = [$self->id => $self->get_full_name()];
        }

        $item = new Model_Booking_Item();
        $statuses = $item->get_set_options('timeslot_status');
        sort($statuses);

        $courses = ORM::factory('Course')->order_by('title')->find_all_undeleted()->as_array('id', 'title');
        $schedules = ORM::factory('Course_Schedule')->order_by('name')->find_all_undeleted()->as_array('id', 'name');

        $filter_menu_options = [
            ['label' => 'Course',   'name' => 'course_ids',   'options' => $courses],
            ['label' => 'Schedule', 'name' => 'schedule_ids', 'options' => $schedules],
            ['label' => 'Trainer',  'name' => 'trainer_ids',  'options' => $staff],
            ['label' => 'Student',  'name' => 'student_ids',  'options' => $student_options, 'selected' => $all ? null : $self->id],
            ['label' => 'Status',   'name' => 'statuses',     'options' => array_combine($statuses, $statuses)],
        ];

        $filters = ['current_year' => true, 'student_ids' => $all ? [] : [$self->id]];
        $metrics = Model_Booking_Item::get_reports($filters);

        $this->template->body .= View::factory('iblisting')->set([
            'below'               => $all
                ? '<div id="attendance-details" class="hidden">'.View::factory('admin/attendance_details', compact('filters', 'students', 'statuses')).'</div>'
                : '',
            'columns'             => ['Booking ID', 'Trainer', 'Student', 'Course', 'Schedule', 'Status', 'Date and time', 'Location'],
            'daterangepicker'     => true,
            'filter_menu_options' => $filter_menu_options,
            'id_prefix'           => 'attendance',
            'plugin'              => 'attendance',
            'reports'             => $metrics,
            'searchbar_on_top'    => false,
            'type'                => 'attendance',
        ]);
    }

    public function action_my_attendance()
    {
        $this->request->redirect('/admin/attendance');
    }

    public function action_all_attendance()
    {
        $this->action_index(['all' => true]);
    }

    public function action_ajax_refresh_details()
    {
        $filters = $this->request->query('filters');
        $students = ORM::factory('Contacts3_Contact')->where_has_bookings();

        if (!empty($filters['student_ids'])) {
            $students->where('id', 'in', $filters['student_ids']);
        }
        $students = $students->order_by('last_name')->order_by('first_name')->find_all_undeleted();

        $statuses = ORM::factory('Booking_Item')->get_set_options('timeslot_status');

        $this->auto_render = false;
        echo View::factory('admin/attendance_details', compact('filters', 'statuses', 'students'));
    }

    public function action_ajax_get_submenu()
    {
        $auth = Auth::instance();
        $return['items'] = [];

        if ($auth->has_access('attendance')) {
            $return['items'][] = ['title' => 'My attendance', 'link' => '/admin/attendance', 'icon_svg' => 'surveys'];
        }

        if ($auth->has_access('attendance')) {
            $return['items'][] = ['title' => 'All attendance', 'link' => '/admin/attendance/all_attendance', 'icon_svg' => 'manage-all-todos'];
        }

        return $return;
    }
}