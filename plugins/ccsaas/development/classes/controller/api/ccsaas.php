<?php
class Controller_Api_Ccsaas extends Controller_Api
{
    public function action_init()
    {
        $cms_skin = $this->request->query('cms_skin') ?: 'wine';
        $this->response_data['success'] = 1;
        $this->response_data['msg'] = '';
        Settings::instance()->set('ccsaas_branch_allowed_ips', $_SERVER['REMOTE_ADDR']);
        Settings::instance()->set('ccsaas_mode', Model_Ccsaas::LEAF);
        Settings::instance()->set('cms_template', 'modern');
        Settings::instance()->set('cms_skin', $cms_skin);
    }

    public function action_create_website()
    {
        if (!$this->check_edit_permission()) {
            $this->response_data['success'] = false;
            $this->response_data['msg'] = __('Access denied!');
            return;
        }

        $post = $this->request->post();
        $user = Auth::instance()->get_user();
        $website = new Model_Ccsaas_Websites();
        $data['contact_id'] = @$post['contact_id'];
        if (Model_Ccsaas::verify_hostname($post['hostname']) == false) {
            $this->response_data['success'] = false;
            $this->response_data['msg'] = __('Invalid hostname!');
            return;
        }
        if (@$post['project_folder']) {
            if (Model_Ccsaas::verify_project_folder($post['project_folder']) == false) {
                $this->response_data['success'] = false;
                $this->response_data['msg'] = __('Invalid project folder!');
                return;
            }
        }
        $data['hostname'] = $post['hostname'];
        $data['starts'] = @$post['starts'] ?: date::today();
        $data['expires'] = @$post['expires'] ?: date('Y-m-d', strtotime('+1 year'));
        $data['is_trial'] = @$post['is_trial'] ?: 0;
        $data['project_folder'] = @$post['project_folder'] ?: 'shop1';
        $data['cms_skin'] = @$post['cms_skin'] ?: 'wine';
        $data['date_created'] = date::now();
        $data['date_modified'] = date::now();
        $data['created_by'] = $user['id'];
        $data['modified_by'] = $user['id'];
        $data['published'] = 1;
        $data['deleted'] = 0;
        $data['branch_server_id'] = @$post['branch_server_id'];
        $unused_db = Model_Ccsaas::get_unused_db_id($data['project_folder']);
        if ($unused_db) {
            $data['database_id'] = $unused_db['id'];
        }
        $website->values($data)->create();
        $id = $website->id;
        $settings = Settings::instance();
        $mode = $settings->get('ccsaas_mode');
        if ($mode == Model_Ccsaas::CENTRAL) {
            // send create host request to branch server
            if ($data['branch_server_id']) {
                Model_Ccsaas::create_on_branch($data);
            }
        } else {
            //create apache vhost on self/branch
            $vhost_params = array(
                'PROJECT_FOLDER' => $data['project_folder'],
                'HOSTNAME' => $data['hostname']
            );
            if ($unused_db['db_id'] != '') {
                $vhost_params['VHOST_DB_ID'] = $unused_db['db_id'];
            }
            Model_Ccsaas::create_vhost($vhost_params);
            // create host on self
        }
        $this->response_data['success'] = true;
        $this->response_data['msg'] = '';
        $this->response_data['id'] = $id;

        if (strpos($this->request->referrer(), '/admin/ccsaas/') !== false) {
            IbHelpers::set_message(($id) ? 'Host created' : 'Failed to create host', ($id) ? 'success popup_box' : 'error popup_box');
        }
    }

    public function action_update_website()
    {
        if (!$this->check_edit_permission()) {
            $this->response_data['success'] = false;
            $this->response_data['msg'] = __('Access denied!');
            return;
        }

        $post = $this->request->post();
        $user = Auth::instance()->get_user();
        $website = new Model_Ccsaas_Websites();
        $website->where('hostname', '=', $post['hostname'])->and_where('deleted', '=', 0)->find();
        $data['contact_id'] = @$post['contact_id'];
        if (Model_Ccsaas::verify_hostname($post['hostname']) == false) {
            $this->response_data['success'] = false;
            $this->response_data['msg'] = __('Invalid hostname!');
            return;
        }
        if (@$post['project_folder']) {
            if (Model_Ccsaas::verify_project_folder($post['project_folder']) == false) {
                $this->response_data['success'] = false;
                $this->response_data['msg'] = __('Invalid project folder!');
                return;
            }
        }
        $data['hostname'] = @$post['hostname'];
        $data['starts'] = @$post['starts'] ?: date::today();
        $data['expires'] = @$post['expires'] ?: date('Y-m-d', strtotime('+1 year'));
        $data['is_trial'] = @$post['is_trial'] ?: 0;
        $data['project_folder'] = @$post['project_folder'] ?: 'shop1';
        $data['cms_skin'] = @$post['cms_skin'] ?: 'wine';
        $data['date_modified'] = date::now();
        $data['modified_by'] = $user['id'];
        $data['published'] = 1;
        $data['deleted'] = 0;
        $data['branch_server_id'] = @$post['branch_server_id'];
        $website->values($data)->save();
        $id = $website->id;

        $settings = Settings::instance();
        $mode = $settings->get('ccsaas_mode');
        if ($mode == Model_Ccsaas::CENTRAL) {
            // send create host request to branch server
            if ($data['branch_server_id']) {
                Model_Ccsaas::update_on_branch($data);
            }
        } else {
            //create apache vhost on self/branch
            $vhost_params = array(
                'PROJECT_FOLDER' => $data['project_folder'],
                'HOSTNAME' => $data['hostname'],
            );
            Model_Ccsaas::create_vhost($vhost_params);
            // create host on self
        }

        $this->response_data['success'] = true;
        $this->response_data['msg'] = '';
        $this->response_data['id'] = $id;

        if (strpos($this->request->referrer(), '/admin/ccsaas/') !== false) {
            IbHelpers::set_message(($id) ? 'Host updated' : 'Failed to update host', ($id) ? 'success popup_box' : 'error popup_box');
        }
    }

    public function action_delete_website()
    {
        if (!$this->check_edit_permission()) {
            $this->response_data['success'] = false;
            $this->response_data['msg'] = __('Access denied!');
            return;
        }

        $post = $this->request->post();
        $user = Auth::instance()->get_user();
        $website = new Model_Ccsaas_Websites();
        if (Model_Ccsaas::verify_hostname($post['hostname']) == false) {
            $this->response_data['success'] = false;
            $this->response_data['msg'] = __('Invalid hostname!');
            return;
        }
        $website->where('hostname', '=', $post['hostname'])->and_where('deleted', '=', 0)->find();
        $data = $website->object();

        $settings = Settings::instance();
        $mode = $settings->get('ccsaas_mode');
        if ($mode == Model_Ccsaas::CENTRAL) {
            // send create host request to branch server
            if ($data['branch_server_id']) {
                Model_Ccsaas::delete_from_branch($data);
            }
        } else {
            Model_Ccsaas::delete_vhost($data['hostname']);
            // delete host on self
        }
        $website->values(array('deleted' => 1, 'date_modified' => date::now(), 'modified_by' => $user['id']))->save();
        $id = $website->id;
        $this->response_data['success'] = true;
        $this->response_data['msg'] = '';
        $this->response_data['id'] = $id;
    }

    public function action_create_contact_on_remote()
    {
        if (!$this->check_edit_permission()) {
            $this->response_data['success'] = false;
            $this->response_data['msg'] = __('Access denied!');
            return;
        }

        $contact_id = $this->request->post('contact_id');
        $hostname = $this->request->post('hostname');
        $data = array();
        $data['hostname'] = $hostname;
        $data['contact'] = DB::select('*')
            ->from(Model_Contacts3::CONTACTS_TABLE)
            ->where('id', '=', $contact_id)
            ->execute()
            ->current();
        if ($data['contact']) {
            unset ($data['contact']['id']);
            $data['has_notifications'] = DB::select('*')
                ->from(Model_Contacts3::CONTACT_NOTIFICATION_RELATION_TABLE)
                ->where('group_id', '=', $data['contact']['notifications_group_id'])
                ->execute()
                ->as_array();

            foreach ($data['has_notifications'] as $i => $has_notification) {
                unset($data['has_notifications'][$i]['group_id']);
                unset($data['has_notifications'][$i]['id']);
            }
            $data['user'] = DB::select('*')
                ->from(Model_Users::MAIN_TABLE)
                ->where('id', '=', $data['contact']['linked_user_id'])
                ->execute()
                ->current();
            if ($data['user']) {
                unset ($data['user']['id']);
                $url = 'http://' . $hostname . '/api/ccsaas/create_contact';
                $curl = curl_init();
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));
                curl_setopt($curl, CURLOPT_URL, $url);
                $response = curl_exec($curl);
                $info = curl_getinfo($curl);
                $err = curl_error($curl);
                curl_close($curl);
                $this->response_data['success'] = true;
                $this->response_data['msg'] = __('ok');
                $this->response_data['$response'] = $response;
            }
        }
    }

    public function action_init_dalm_remote()
    {
        $hostname = $this->request->post('hostname');
        $url = 'http://' . $hostname . '/api/ccsaas/init?dalm=force&cms_skin=' . $this->request->post('cms_skin');
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, $url);
        $response = curl_exec($curl);
        $info = curl_getinfo($curl);
        $err = curl_error($curl);
        curl_close($curl);
        $this->response_data['success'] = true;
        $this->response_data['msg'] = __('ok');
        $this->response_data['info'] = $info;
        $this->response_data['error'] = $err;
        $this->response_data['response'] = $response;
        if ($info['http_code']) {
            $this->response->status($info['http_code']);
        } else {
            $this->response->status(501);
        }
    }

    public function action_create_contact()
    {
        if (!$this->check_edit_permission()) {
            $this->response_data['success'] = false;
            $this->response_data['msg'] = __('Access denied!');
            return;
        }

        $this->init_permissions();

        $post = $this->request->post();
        $contact = $post['contact'];
        $user = $post['user'];
        $has_notifications = $post['has_notifications'];

        DB::delete(Model_Users::MAIN_TABLE)->where('email', '=', $user['email'])->execute();

        $roles = new Model_Roles();
        $owner_role_id = $roles->get_id_for_role('Website Owner');
        $user['role_id'] = $owner_role_id;
        $user_inserted = DB::insert(Model_Users::MAIN_TABLE)
            ->values($user)
            ->execute();
        $contact['linked_user_id'] = $user['id'] = $user_inserted[0];
        unset($contact['residence']);
        unset($contact['billing_residence_id']);
        unset($contact['family_id']);
        $group_inserted = DB::insert('plugin_contacts3_notification_groups')
            ->values(array('date_created' => date::now(), 'publish' => 1, 'deleted' => 0))
            ->execute();
        foreach ($has_notifications as $has_notification) {
            $has_notification['group_id'] = $group_inserted[0];
            DB::insert(Model_Contacts3::CONTACT_NOTIFICATION_RELATION_TABLE)->values($has_notification)->execute();
        }
        $contact['notifications_group_id'] = $group_inserted[0];
        $contact_inserted = DB::insert(Model_Contacts3::CONTACTS_TABLE)
            ->values($contact)
            ->execute();
        $contact['id'] = $contact_inserted[0];

        $this->response_data['success'] = true;
        $this->response_data['msg'] = __('ok');
        $this->response_data['user'] = $user;
        $this->response_data['contact'] = $contact;
    }

    public function action_edit_bserver()
    {
        if (!$this->check_edit_permission()) {
            $this->response_data['success'] = false;
            $this->response_data['msg'] = __('Access denied!');
            return;
        }

        $post = $this->request->post();
        $user = Auth::instance()->get_user();
        $bserver = new Model_Ccsaas_Branchservers();
        if (@$post['id']) {
            $bserver->where('id', '=', $post['id'])->and_where('deleted', '=', 0)->find();
        } else {
            $data['date_created'] = date::now();
            $data['created_by'] = $user['id'];
        }
        $data['host'] = $post['host'];
        $data['ip4'] = $post['ip4'];
        $data['date_modified'] = date::now();
        $data['modified_by'] = $user['id'];
        $data['published'] = 1;
        $data['deleted'] = 0;
        $bserver->values($data);
        if (@$post['id']) {
            $bserver->save();
        } else {
            $bserver->create();
        }
        $id = $bserver->id;

        $this->response_data['success'] = true;
        $this->response_data['msg'] = '';
        $this->response_data['id'] = $id;

        if (strpos($this->request->referrer(), '/admin/ccsaas/') !== false) {
            IbHelpers::set_message(($id) ? 'Branch server saved' : 'Failed to save branch server', ($id) ? 'success popup_box' : 'error popup_box');
        }
    }

    public function action_delete_bserver()
    {
        if (!$this->check_edit_permission()) {
            $this->response_data['success'] = false;
            $this->response_data['msg'] = __('Access denied!');
            return;
        }

        $post = $this->request->post();
        $user = Auth::instance()->get_user();

        $bserver = new Model_Ccsaas_Branchservers();
        $bserver->where('id', '=', $post['id'])->and_where('deleted', '=', 0)->find();
        $bserver->values(array('deleted' => 1, 'date_modified' => date::now(), 'modified_by' => $user['id']))->save();
        $id = $bserver->id;
        $this->response_data['success'] = true;
        $this->response_data['msg'] = '';
        $this->response_data['id'] = $id;
    }

    protected function check_edit_permission()
    {
        if (!Auth::instance()->has_access('ccsaas_edit')) {
            $ips = preg_split('/\s*,\s*/', Settings::instance()->get('ccsaas_branch_allowed_ips'));
            if (in_array($_SERVER['REMOTE_ADDR'], $ips) === false) {
                return false;
            }
        }
        return true;
    }

    protected function init_permissions()
    {
        $permissions_admin = array(

            'activecampaign',

            'ccsaas',
            'ccsaas_view',

            'dashboards',
            'documents',

            'files',
            'files_delete',
            'files_edit',
            'files_edit_directory',
            'files_view',

            'formbuilder',
            'formprocessor',

            'lookups',
            'media',

            'menus',
            'messaging',
            'messaging_access_drafts',
            'messaging_access_others_mail',
            'messaging_access_own_mail',
            'messaging_access_system_mail',
            'messaging_global_see_all',
            'messaging_see_under_developed_features',
            'messaging_send_alerts',
            'messaging_send_system_email',
            'messaging_send_system_sms',
            'messaging_view_system_email',
            'messaging_view_system_sms',

            'news',

            'pages',
            'panels',

            'payments',

            'remoteaccounting',

            'reports',
            'reports_delete',
            'reports_edit',

            'roles',
            'role_edit',
            'role_view',

            'settings',
            'settings_activities',
            'settings_index',
            'settings_users_edit_delete_btn',

            'surveys',
            'testimonials',

            'todos',
            'todos_content_tab',
            'todos_edit',
            'todos_edit_create_assesments',
            'todos_edit_create_assignments',
            'todos_edit_create_exams',
            'todos_edit_create_tasks',
            'todos_edit_create_tests',
            'todos_list',
            'todos_manage_all',
            'todos_view_my_todos',
            'todos_view_results',

            'user',
            'user_edit',
            'user_profile',
            'user_profile_education',
            'user_profile_email',
            'user_profile_preferences',
            'user_tools_help',
            'user_tools_messages',
            'user_view',
            'view_website_frontend',

            'contacts3',
            'contacts3_billing',
            'contacts3_frontend_accounts',
            'contacts3_frontend_attendance',
            'contacts3_frontend_attendance_edit',
            'contacts3_frontend_attendance_edit_auth',
            'contacts3_frontend_bookings',
            'contacts3_frontend_homeworks',
            'contacts3_frontend_timetables',
            'contacts3_frontend_wishlist',
            'contacts3_limited_bookings_linked_contacts',
            'contacts3_limited_family_access',
            'contacts3_limited_view',
            'contacts3_settings',

            'courses',
            'courses_academicyear_edit',
            'courses_bookings_see_seating_numbers',
            'courses_booking_edit',
            'courses_category_edit',
            'courses_course_edit',
            'courses_credits',
            'courses_finance',
            'courses_level_edit',
            'courses_location_edit',
            'courses_provider_edit',
            'courses_registration_edit',
            'courses_rollcall',
            'courses_schedule_content_tab',
            'courses_schedule_edit',
            'courses_studymode_edit',
            'courses_subject_edit',
            'courses_timetable_edit',
            'courses_topic_edit',
            'courses_type_edit',
            'courses_view_mycourses',
            'courses_view_mycourses_global',
            'courses_year_edit',
            'courses_zone_edit',
        );

        $permissions_manager = array(
            'activecampaign',

            'ccsaas',
            'ccsaas_view_limited',

            'dashboards',
            'documents',

            'files',
            'files_delete',
            'files_edit',
            'files_edit_directory',
            'files_view',

            'formbuilder',
            'formprocessor',

            'lookups',
            'media',

            'menus',
            'messaging',
            'messaging_access_drafts',
            'messaging_access_others_mail',
            'messaging_access_own_mail',
            'messaging_access_system_mail',
            'messaging_global_see_all',
            'messaging_see_under_developed_features',
            'messaging_send_alerts',
            'messaging_send_system_email',
            'messaging_send_system_sms',
            'messaging_view_system_email',
            'messaging_view_system_sms',

            'news',

            'pages',
            'panels',

            'payments',

            'remoteaccounting',

            'reports',
            'reports_delete',
            'reports_edit',

            'roles',
            'role_edit',
            'role_view',

            'settings',
            'settings_activities',
            'settings_index',
            'settings_users_edit_delete_btn',

            'surveys',
            'testimonials',

            'todos',
            'todos_content_tab',
            'todos_edit',
            'todos_edit_create_assesments',
            'todos_edit_create_assignments',
            'todos_edit_create_exams',
            'todos_edit_create_tasks',
            'todos_edit_create_tests',
            'todos_list',
            'todos_manage_all',
            'todos_view_my_todos',
            'todos_view_results',

            'contacts3',
            'contacts3_billing',
            'contacts3_settings',

            'courses',
            'courses_academicyear_edit',
            'courses_bookings_see_seating_numbers',
            'courses_booking_edit',
            'courses_category_edit',
            'courses_course_edit',
            'courses_credits',
            'courses_finance',
            'courses_level_edit',
            'courses_location_edit',
            'courses_provider_edit',
            'courses_registration_edit',
            'courses_rollcall',
            'courses_schedule_content_tab',
            'courses_schedule_edit',
            'courses_studymode_edit',
            'courses_subject_edit',
            'courses_timetable_edit',
            'courses_topic_edit',
            'courses_type_edit',
            'courses_year_edit',
            'courses_zone_edit',
        );

        $roles = new Model_Roles();
        $administrator_role_id = $roles->get_id_for_role('administrator');
        $owner_role_id = $roles->get_id_for_role('Website Owner');

        $permissions = array_merge($permissions_admin, $permissions_manager);
        $resources = DB::select('*')
            ->from(Model_Resources::TABLE_RESOURCES)
            ->where('alias', 'in', $permissions)
            ->execute()
            ->as_array();
        $permission_map = array();
        foreach ($resources as $resource) {
            $permission_map[$resource['alias']] = $resource['id'];
        }

        DB::delete(Model_Resources::TABLE_HAS_PERMISSION)->where('role_id', '=', $administrator_role_id)->execute();
        $admin_insert = DB::insert(Model_Resources::TABLE_HAS_PERMISSION);
        foreach ($permissions_admin as $permission) {
            $admin_insert->values(
                array('resource_id' => $permission_map[$permission], 'role_id' => $administrator_role_id)
            );
        }
        $admin_insert->execute();

        DB::delete(Model_Resources::TABLE_HAS_PERMISSION)->where('role_id', '=', $owner_role_id)->execute();
        $manager_insert = DB::insert(Model_Resources::TABLE_HAS_PERMISSION);
        foreach ($permissions_manager as $permission) {
            $manager_insert->values(
                array('resource_id' => $permission_map[$permission], 'role_id' => $owner_role_id)
            );
        }
        $manager_insert->execute();
    }
}
