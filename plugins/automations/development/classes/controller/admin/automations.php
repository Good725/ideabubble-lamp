<?php

class Controller_Admin_Automations extends Controller_Cms
{
    public function before()
    {
        parent::before();
        $this->template->sidebar = View::factory('sidebar');
    }

    protected $_plugin = 'automations';

    protected $_crud_items = [
        'automation' => [
            'name' => 'automation',
            'model' => 'Automations',
            'delete_permission' => 'automations_edit',
            'edit_permission'   => 'automations_edit',
        ]
    ];

    public function action_index()
    {
        if (!isset($_GET['old_ui'])) {
            return self::action_automations2();
        }

        if (!Auth::instance()->has_access('automations_edit') && !Auth::instance()->has_access('automations_view')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('automations') . 'js/automations.js"></script>';

        $post = $this->request->post();
        if (@$post['action'] == 'save') {
            Model_Automations::save_automation(@$post['map'] ?: array());
        }

        $actions = Model_Automations::get_actions();
        $triggers = Model_Automations::get_triggers(['enabled_only' => true]);
        $triggers_actions = Model_Automations::load();
        $message_templates = Model_Messaging::notification_template_list();
        $this->template->body = View::factory('admin/automations')
            ->set('actions', $actions)
            ->set('triggers', $triggers)
            ->set('triggers_actions', $triggers_actions)
            ->set('message_templates', $message_templates);
    }

    public function action_automations2()
    {
        Ibhelpers::permission_redirect(['automations_view', 'automations_edit']);

        $this->template->sidebar->breadcrumbs = [
            ['name' => 'Home', 'link' => '/admin/'],
            ['name' => 'Automations', 'link' => '/admin/automations'],
            ['name' => 'All automations', 'link' => '#'],
        ];

        $this->template->sidebar->tools = '<a href="/admin/automations/edit_automation" class="btn btn-primary">New automation</a>';

        $metrics = Model_Automations::get_reports();

        $statuses = [
            'active'   => 'Active',
            'inactive' => 'Inactive',
            'complete' => 'Complete'
        ];

        $triggers = Model_Automations::get_triggers(['linked_only' => true]);
        $options = [];
        foreach ($triggers as $trigger) {
            $options[$trigger->name] = $trigger->name;
        }

        $filter_menu_options = [
            ['label' => 'Trigger',  'name' => 'triggers', 'options' => $options],
            ['label' => 'Statuses', 'name' => 'statuses', 'options' => $statuses],
        ];

        $this->template->body = View::factory('iblisting')->set([
            'columns'             => ['ID', 'Name', 'Trigger', 'Created', 'Updated', 'Last activity', 'Actions'],
            'default_order'       => 'Last activity',
            'default_dir'         => 'desc',
            'daterangepicker'     => true,
            'daterange_start'     => false,
            'daterange_end'       => false,
            'filter_menu_options' => $filter_menu_options,
            'id_prefix'           => 'automations',
            'plugin'              => 'automations',
            'reports'             => $metrics,
            'top_filter'          => false,
            'searchbar_on_top'    => false,
            'type'                => 'automation'
        ]);
    }

    public function action_edit_automation()
    {
        IbHelpers::permission_redirect('automations_edit');

        $automation = new Model_Automations($this->request->param('id'));
        $automation_data = null;
        if ($this->request->param('id')) {
            $automation_data = Model_Automations::get_data($this->request->param('id'));
        }

        $this->template->sidebar->breadcrumbs = [
            ['name' => 'Home', 'link' => '/admin/'],
            ['name' => 'Automations', 'link' => '/admin/automations'],
            ['name' => ($automation->id ? 'Edit automation' : 'New automation'), 'link' => '#'],
        ];
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('automations') . 'js/automations_edit.js"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('media') . 'js/image_edit.js"></script>';

        $triggers = Model_Automations::get_triggers(['enabled_only' => true]);
        $trigger_options = [];
        foreach ($triggers as $trigger) {
            $trigger_options[$trigger->name] = $trigger->name;
        }
        natcasesort($trigger_options);
        $actions = Model_Automations::get_actions();
        $triggers_actions = Model_Automations::load();
        $message_templates = Model_Messaging::notification_template_list();
        $schedules = Model_Schedules::search();
        foreach ($schedules as $i => $schedule) {
            $schedules[$i]['name'] = '#' . $schedule['id'] . ' - ' . $schedule['name'];
        }
        $contacts = Model_Contacts3::search();
        foreach ($contacts as $i => $contact) {
            $contacts[$i]['fullname'] = '#' . $contact['id'] . ' - ' . $contact['first_name'] . ' ' . $contact['last_name'] . '(' . $contact['email'] . ')';
        }
        $docx_templates = Model_Files::getDirectoryTree('/templates');

        $this->template->body = View::factory('/admin/edit_automation')
            ->set([
                'automation' => $automation,
                'trigger_options' => $trigger_options,
                'actions' => $actions,
                'triggers' => $triggers,
                'triggers_actions' => $triggers_actions,
                'message_templates' => $message_templates,
                'automation_data' => $automation_data,
                'schedules' => $schedules,
                'contacts' => $contacts,
                'docx_templates' => $docx_templates
            ]);
    }

    public function action_delete()
    {
        IbHelpers::permission_redirect('automations_edit');

        $this->auto_render = false;
        $id = $this->request->query('id');
        Model_Automations::delete_automation($id);
        $this->request->redirect('/admin/automations');
    }

    // Largely the same as `action_delete`, but redirects to the new UI and shows an alert.
    // The two can be merged when the new UI is the default.
    public function action_delete2()
    {
        IbHelpers::permission_redirect('automations_edit');

        $this->auto_render = false;
        Model_Automations::delete_automation($this->request->query('id'));
        IbHelpers::set_message('Automation has been deleted', 'success popup_box');
        $this->request->redirect('/admin/automations');
    }

    public function action_save()
    {
        IbHelpers::permission_redirect('automations_edit');

        $post = $this->request->post();
        $this->auto_render = false;
        Model_Automations::save_automation($post);
        $this->request->redirect('/admin/automations?old_ui=1');
    }

    // Save function used for the new UI
    // This can be replaced with the other save function after all fields have been put into the new form...
    // ... so we can ensure no data is getting wiped.
    public function action_save2()
    {
        IbHelpers::permission_redirect('automations_edit');
        $automation = new Model_Automations($this->request->param('id'));
        $post = $this->request->post();
        Model_Automations::save_automation($post);

        IbHelpers::set_message('Automation has been saved', 'success popup_box');
        $this->request->redirect('/admin/automations');
    }

    public function action_settings()
    {
        if (!Auth::instance()->has_access('automations_settings')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $post = $this->request->post();
        if (@$post['action'] == 'save') {
            Model_Automations_Settings::save_enabled_triggers($post['trigger']);
            IbHelpers::set_message("Settings have been updated!", 'info popup_box');
            $this->request->redirect('/admin/automations/settings');
        }

        $triggers = Model_Automations::get_triggers();
        $enabled_triggers = Model_Automations_Settings::get_enabled_triggers();
        $this->template->body = View::factory('admin/automation_settings')
            ->set('triggers', $triggers)
            ->set('enabled_triggers', $enabled_triggers);
    }

    public function action_ajax_get_submenu()
    {
        $return['items'] = [];

        if (Auth::instance()->has_access('automations_edit') || Auth::instance()->has_access('automations_view')) {
            $return['items'][] = ['title' => 'All automations', 'link' => '/admin/automations', 'icon_svg' => 'zones'];
        }

        return $return;
    }

    public function action_upload_attachment_tmp()
    {
        $this->auto_render = false;
        $this->response->headers('content-type', 'application/json');

        $tmp_file_id = Model_Files::get_directory_id_r('/tmp/' . time());
        $file_id = Model_Files::create_file($tmp_file_id, $_FILES['file']['name'], $_FILES['file']);
        echo json_encode([
            'file_id' => $file_id,
            'name' => $_FILES['file']['name']
        ]);
    }

    public function action_test()
    {
        $this->auto_render = false;
        $this->response->headers('content-type', 'text/plain');
        Model_Automations::run_triggers(
            Model_Bookings_Adminbookingcreatetrigger::NAME,
            array(
                'booking_id' => 143,
                'tags' => array(),
            )
        );
    }
}