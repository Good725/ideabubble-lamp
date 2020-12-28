<?php

class Controller_Admin_Safety extends Controller_Cms
{
    protected $_plugin = 'safety';

    protected $_crud_items = [
        'incident' => [
            'name' => 'Incident',
            'model' => 'Safety_Incident',
            'delete_permission' => false,
            'edit_permission'   => false,
        ],
        'precheck' => [
            'name' => 'Precheck',
            'model' => 'Safety_Precheck',
            'delete_permission' => false,
            'edit_permission' => false
        ]
    ];

    public function before()
    {
        $auth = Auth::instance();
        if (!$auth->has_access('safety')) {
            IbHelpers::set_message('You need access to the "safety" permission to perform this action.', 'error popup_box');
            $this->request->redirect('/admin');
        }

        parent::before();

        $menus[] = ['name' => 'My incidents', 'link' => '/admin/safety', 'icon' => 'spam'];
        if (true || $auth->has_access('safety_incidents_edit')) {
            $menus[] = ['name' => 'All incidents', 'link' => '/admin/safety/all_incidents', 'icon' => 'all-requests'];
        }

        if ($auth->has_access('safety')) {
            $menus[] = ['name' => 'My pre-checks', 'link' => '/admin/safety/my_prechecks', 'icon' => 'surveys'];
        }

        if ($auth->has_access('safety')) {
            $menus[] = ['name' => 'All pre-checks', 'link' => '/admin/safety/all_prechecks', 'icon' => 'manage-all-todos'];
        }

        $this->template->sidebar = View::factory('sidebar');
        $this->template->sidebar->menus = [$menus];
        $this->template->sidebar->breadcrumbs = [
            ['name' => 'Home',    'link' => '/admin'],
            ['name' => 'Safety', 'link' => '/admin/safety']
        ];

    }

    public function action_index($args = [])
    {
        if (false && !Auth::instance()->has_access('safety_incidents_list')) {
            IbHelpers::set_message('You need access to the "safety_incidents_list" permission to perform this action.', 'error popup_box');
            $this->request->redirect('/admin');
        } else {
            $this->template->sidebar->breadcrumbs[] = ['name' => (empty($args['all']) ? 'My incidents' : 'All incidents'), 'link' => '#'];

            $locations  = ORM::factory('Course_Location')->order_by('name')->find_all_published();
            $incident   = new Model_Safety_Incident();
            $severities = $incident->get_enum_options('severity');
            $statuses   = $incident->get_enum_options('status');

            $styles = [
                URL::get_engine_assets_base().'css/validation.css' => 'screen',
            ];
            $this->template->styles    = array_merge($this->template->styles, $styles);
            $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validationEngine2.js"></script>';
            $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validationEngine2-en.js"></script>';
            $this->template->scripts[] = URL::get_engine_plugin_asset('safety', 'js/safety.js', ['script_tags' => true, 'cachebust' => true]);

            $severity_filters = array_combine($severities, $severities) + ['' => 'No severity selected'];

            $this->template->body .= View::factory('iblisting')->set([
                'show_mine_only'  => empty($args['all']),
                'columns'         => ['ID', 'Title', 'Reported', 'Location', 'Reporter', 'Severity', 'Status', 'Updated', 'Actions'],
                'status_filters'  => array_combine($statuses, $statuses),
                'daterangepicker' => true,
                'id_prefix'       => 'incidents',
                'plugin'          => 'safety',
                'reports'         => $incident->get_reports(),
                'action_button'   => '<button type="button" class="btn btn-primary form-btn right text-uppercase" data-toggle="modal" data-target="#incidents-report-modal">Report incident</button>',
                'below'           => View::factory('admin/incident_form')->set([
                    'mode'          => 'backend',
                    'locations'     => $locations,
                    'severities'    => $severities,
                    'statuses'      => $statuses
                ]),
                'top_filter'      => Form::ib_select(__('Severity'), 'severities[]', $severity_filters, '{all}', ['multiple' => 'multiple', 'class' => 'incidents-table-filter'], ['multiselect_options' => ['includeSelectAllOption' => true, 'selectAllText' => __('ALL')]]),
                'type'            => 'incident',
            ]);
        }
    }

    public function action_my_incidents()
    {
        $this->request->redirect('/admin/safety');
    }

    public function action_all_incidents()
    {
        $this->action_index(['all' => true]);
    }

    public function action_ajax_get_incident()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');

        $incident = new Model_Safety_Incident($this->request->param('id'));
        $data = $incident->as_array();
        $data['injured_people'] = $incident->get_injured_people();
        $data['witnesses']      = $incident->get_witnesses();
        $data['reporter'] = [
            'first_name' => $incident->reporter->first_name,
            'last_name'  => $incident->reporter->last_name,
            'email'      => $incident->reporter->get_notification('email'),
            'mobile'     => $incident->reporter->get_notification('mobile')
        ];

        echo json_encode(['success' => true, 'data' => $data]);
    }

    public function action_save_incident()
    {
        if (false && !Auth::instance()->has_access('safety_incidents_edit')) {
            IbHelpers::set_message('You need access to the "safety_incidents_edit" permission to perform this action.', 'error popup_box');
        }
        else {
            try {
                $incident = new Model_Safety_Incident($this->request->post('id'));
                $incident->save_data($this->request->post());

                // Send email, if this is a new report
                if (!$this->request->post('id')) {
                    $incident->send_notifications(['admin', 'reporter']);
                }

                IbHelpers::set_message(htmlspecialchars('Incident #'.$incident->id.': "'.$incident->title.'" successfully saved.'), 'success popup_box');

                $this->request->redirect('/admin/safety');
            }
            catch (Exception $e) {
                Log::instance()->add(Log::ERROR, "Error saving incident.\n".$e->getMessage()."\n".$e->getTraceAsString());
                IbHelpers::set_message('Unexpected error saving incident. If this problem continues, please ask an administrator to check the error logs.', 'error popup_box');
                $this->request->redirect('/admin/safety');
            }
        }
    }

    public function action_my_prechecks()
    {
        self::action_all_prechecks(['mine_only' => true]);
    }

    public function action_all_prechecks($args = [])
    {
        $mine_only = !empty($args['mine_only']);
        if ($mine_only) {
            $this->template->sidebar->breadcrumbs[]  = ['name' => 'My pre-checks', 'link' => '/admin/safety/my_prechecks'];
        } else {
            $this->template->sidebar->breadcrumbs[] = ['name' => 'All pre-checks', 'link' => '/admin/safety/all_prechecks'];
        }
        $this->template->sidebar->tools  = View::factory('admin/precheck_header_buttons')/*->set('view_toggle', true)*/;

        $precheck = new Model_Safety_Precheck();

        $top_level_prechecks = ORM::factory('Survey')->where('type', '=', 'Pre-check')->where_top_level()->order_by('title')->find_all_published();
        $precheck_options    = $top_level_prechecks->as_array('id', 'title');

        if ($mine_only) {
            $self = Auth::instance()->get_contact();
            $filter_menu_options = [
                ['label' => 'Staff',     'name' => 'staff_ids',    'options' => [$self->id => $self->get_full_name()], 'selected' => $self->id],
                ['label' => 'Pre-check', 'name' => 'precheck_ids', 'options' => $precheck_options],
            ];
            $metrics = $precheck->get_reports(['staff_id' => $self->id]);


        } else {
            $staff_role = new Model_Contacts3_Type(['name' => 'staff']);
            $staff_members = $staff_role->contacts->order_by('last_name')->find_all_undeleted();
            $staff_options = [];
            foreach ($staff_members as $staff_member) {
                $staff_options[$staff_member->id] = $staff_member->get_full_name();
            }

            $department_type = new Model_Contacts3_Type(['name' => 'department']);
            $departments = $department_type->contacts->order_by('first_name')->find_all_published()->as_array('id', 'first_name');

            $filter_menu_options = [
                // ['label' => 'Department', 'name' => 'department_ids[]', 'options' => $departments],
                ['label' => 'Staff',      'name' => 'staff_ids',      'options' => $staff_options],
                ['label' => 'Pre-check',  'name' => 'precheck_ids',   'options' => $precheck_options],
            ];
            $metrics = $precheck->get_reports();
        }

        $form = View::factory('admin/precheck_form_new', compact('top_level_prechecks'));

        $styles = [
            URL::get_engine_assets_base().'css/validation.css' => 'screen',
        ];
        $this->template->styles    = array_merge($this->template->styles, $styles);
        $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validationEngine2.js"></script>';
        $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validationEngine2-en.js"></script>';
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_asset('safety', 'js/prechecks.js').'"></script>';

        $this->template->body = View::factory('iblisting')->set([
            'below'               => $form,
            'columns'             => ['ID', 'Staff ID', 'Staff', 'Stock ID', 'Stock', 'Pre-check', 'Created', 'Modified', 'Actions'],
            'filter_menu_options' => $filter_menu_options,
            'daterangepicker'     => true,
            'id_prefix'           => 'safety-precheck',
            'mine_only'           => $mine_only,
            'plugin'              => 'safety',
            'reports'             => $metrics,
            'top_filter'          => false,
            'searchbar_on_top'    => false,
            'type'                => 'precheck',
        ]);
    }

    public function action_start_precheck_response()
    {
        $this->auto_render = false;

        $precheck = new Model_Safety_Precheck($this->request->param('id'));

        Session::instance()->set('active_precheck', [
            'precheck_id' => $precheck->id,
            'surveys'     => $precheck->get_responses_array()
        ]);
    }

    public function action_ajax_change_precheck_page()
    {
        $survey_id = $this->request->post('current_survey_id');
        $responses = $this->request->post('responses');
        $responses = json_decode(json_encode($responses ? $responses : []), true);
        $stock_ids = array_keys($responses);
        $active_precheck = Session::instance()->get('active_precheck');

        // Record responses from the current page
        if (!empty($survey_id) && !empty($responses)) {
            foreach ($stock_ids as $stock_id) {
                $active_precheck['surveys'][$survey_id][$stock_id]['responses']   = (array) $responses[$stock_id];
                $active_precheck['surveys'][$survey_id][$stock_id]['stock_id']    = $stock_id ? $stock_id : null;
                $active_precheck['surveys'][$survey_id][$stock_id]['course_id']   = $this->request->post('course_id');
                $active_precheck['surveys'][$survey_id][$stock_id]['schedule_id'] = $this->request->post('schedule_id');
            }

            Session::instance()->set('active_precheck', $active_precheck);
        }

        // Get the form for the next page
        $survey    = new Model_Survey($this->request->post('new_survey_id'));
        $result    = $active_precheck['surveys'][$survey->id];
        $responses = isset($active_precheck['surveys'][$survey->id]) ? $active_precheck['surveys'][$survey->id] : [];

        $this->auto_render = false;
        echo json_encode([
            'html' => View::factory('admin/precheck_form_rendered', compact('responses', 'result', 'survey'))->render()
        ]);
    }

    public function action_ajax_save_precheck()
    {
        $this->auto_render = false;

        $survey_id = $this->request->post('current_survey_id');
        $responses = $this->request->post('responses');
        $responses = json_decode(json_encode($responses ? $responses : []), true);
        $stock_ids = array_keys($responses);
        $active_precheck = Session::instance()->get('active_precheck');

        // Record responses from the current page
        if (!empty($survey_id)) {
            foreach ($stock_ids as $stock_id) {
                $active_precheck['surveys'][$survey_id][$stock_id]['responses']   = $responses[$stock_id];
                $active_precheck['surveys'][$survey_id][$stock_id]['stock_id']    = $stock_id ? $stock_id : null;
                $active_precheck['surveys'][$survey_id][$stock_id]['course_id']   = $this->request->post('course_id');
                $active_precheck['surveys'][$survey_id][$stock_id]['schedule_id'] = $this->request->post('schedule_id');
            }

            Session::instance()->set('active_precheck', $active_precheck);
        }

        $precheck = new Model_Safety_Precheck($this->request->param('id'));

        if (empty($precheck->id)) {
            $active_precheck['assignee_id'] = $this->request->post('assignee_id')
                ? $this->request->post('assignee_id')
                : Auth::instance()->get_contact()->id;
        }

        $precheck->save_data($active_precheck);

        echo json_encode([
            'status' => 'success',
            'message' => 'Precheck has been saved'
        ]);
    }

    public function action_ajax_get_submenu()
    {
        $auth = Auth::instance();
        $return['items'] = [];

        if ($auth->has_access('safety')) {
            $return['items'][] = ['title' => 'My incidents', 'link' => '/admin/safety', 'icon_svg' => 'spam'];
        }

        if ($auth->has_access('safety')) {
            $return['items'][] = ['title' => 'All incidents', 'link' => '/admin/safety/all_incidents', 'icon_svg' => 'all-requests'];
        }

        if ($auth->has_access('safety')) {
            $return['items'][] = ['title' => 'My pre-checks', 'link' => '/admin/safety/my_prechecks', 'icon_svg' => 'surveys'];
        }

        if ($auth->has_access('safety')) {
            $return['items'][] = ['title' => 'All pre-checks', 'link' => '/admin/safety/all_prechecks', 'icon_svg' => 'manage-all-todos'];
        }

        return $return;
    }
}