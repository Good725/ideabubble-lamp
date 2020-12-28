<?php defined('SYSPATH') OR die('No Direct Script Access');

Class Controller_Admin_Profile extends Controller_Cms {

    function before() {
        parent::before();

        $this->template->sidebar = View::factory('sidebar');
    }

	public static $extraSections = array();
    public static $sections = array(
        array('id' => 'edit?section=contact', 'title' => 'Profile'),
        array('id' => 'edit?section=address', 'title' => 'Address'),
        array('id' => 'edit?section=family', 'title' => 'Family'),
        array('id' => 'edit?section=notifications', 'title' => 'Notifications'),
        array('id' => 'edit?section=education', 'title' => 'Education'),
        array('id' => 'edit?section=documents', 'title' => 'Documents'),
        array('id' => 'edit?section=payments', 'title' => 'Payments'),
        array('id' => 'edit?section=password', 'title' => 'Password'),
        array('id' => 'edit?section=email', 'title' => 'Email Settings'),
        array('id' => 'edit?section=platform', 'title' => 'Preferences'),
        array('id' => 'edit?section=cards', 'title' => 'Saved Cards'),
    );

    public function action_index()
    {
        $this->request->redirect('/admin/profile/edit?section=contact');
        /*
        $model               = new Model_Users();
        $auth_user           = Auth::instance()->get_user();
        // user details in the session variable, do not update when the database is changed
        $user_data           = $model->get_user($auth_user['id']);
		$role                = new Model_Roles($user_data['role_id']);
		$activity_actions    = Model_Activity::get_action_list();
		$activity_item_types = Model_Activity::get_item_type_list();
		$activity_alerts     = (Model_Plugin::is_enabled_for_role('Administrator', 'Messaging')) ? Model_Messaging::get_activity_alert_list('CMS_USER', $auth_user['id']) : FALSE;
		$dashboards          = (class_exists('Model_Dashboard')) ? Model_Dashboard::get_user_accessible() : FALSE;

		$this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('media').'js/multiple_upload.js"></script>';
		$this->template->scripts[] = '<script src="'. URL::get_engine_plugin_assets_base('media') .'js/image_edit.js"></script>';

        $this->template->body = View::factory('content/edit_profile')->set('data', $user_data)->set('dashboards', $dashboards)->set('role', $role);
		$this->template->body->timezones           = $model->generate_timezone_list();
		$this->template->body->activity_actions    = $activity_actions;
		$this->template->body->activity_item_types = $activity_item_types;
		$this->template->body->activity_alerts     = $activity_alerts;
        $this->template->body->extraSections       = self::$extraSections;
		$this->template->body->printing = Settings::instance()->get('print_office') == 1 ? true : false;
		$this->template->body->printers = Model_Eprinter::search(array('published' => 1));
        if (Model_Plugin::is_enabled_for_role('Administrator', 'Messaging')) {
            $this->template->body->imap_settings = DB::select('*')
                ->from('plugin_messaging_imap_accounts')
                ->where('user_id', '=', $auth_user['id'])
                ->execute()
                ->current();

        }*/
    }

    public function get_contact_from_post($post)
    {
        $model               = new Model_Users();
        $auth_user           = Auth::instance()->get_user();
        $user_data           = $model->get_user($auth_user['id']);
        $user_id             = $auth_user['id'];

        $contact_id = $this->request->query('contact_id');
        if (@$post['contact_id']) {
            $contact_id = $post['contact_id'];
        }

        $members = array();
        if (Model_Plugin::is_enabled_for_role('Administrator', 'contacts3')) {
            $contacts3 = Model_Contacts3::get_contact_ids_by_user($user_data['id']);
            if (!count($contacts3)) {
                Model_Users::create_contacts_if_not_linked($user_data);
                $contacts3 = Model_Contacts3::get_contact_ids_by_user($user_data['id']);
            } else if(current($contacts3)['linked_user_id'] === null) {
                $me = new Model_Contacts3($contacts3[0]['id']);
                $me->set_linked_user_id($auth_user['id']);
                $me->save();
            }
            $me = new Model_Contacts3($contacts3[0]['id']);
            $members = $me->get_family_members($me->get_family_id());
            if ($contact_id != $me->get_id()) {
                if (!$contact_id && $me->get_id() !== null) {
                    $contact_id = $me->get_id();
                } else if($contact_id) {
                    $not_member = true;
                    // check family member
                    foreach ($members as $member) {
                        if ($member['id'] == $contact_id) {
                            $not_member = false;
                            break;
                        }
                    }
                    if ($not_member) {
                        return false;
                    }
                }
            }

            $contact3 = new Model_Contacts3($contact_id);
            $org_contact3 = new Model_Contacts3();
            $linked_org = $contact3->get_contact_relations_details(array('contact_type' => 'organisation'));
            if(count($linked_org) > 0) {
                $org_contact3 = new Model_Contacts3(current($linked_org)['id']);
            }
            $family3 = new Model_Family($contact3->get_family_id());
            $user_id = $contact3->get_linked_user_id();
            $contact = new Model_Contacts();
        } else {
            $contact3 = new Model_Contacts3();
            $family3 = new Model_Family();
            $contact = Model_Contacts::get_linked_contact_to_user($auth_user['id']);
            $contact_id = $contact['id'];
            $contact = new Model_Contacts($contact_id);
            $org_contact3 = new Model_Contacts3();
        }

        return array(
            'contact3' => $contact3,
            'contact' => $contact,
            'family3' => $family3,
            'user_id' => $user_id,
            'user_data' => $user_data,
            'contact_id' => $contact_id,
            'org_contact3' => $org_contact3
        );
    }

    public function action_edit()
    {
        $section = $this->request->query("section") ?: 'contact';

        // Permissions needed to view each section
        $section_permissions = [
            'cards'         => 'payments_store_card',
            'documents'     => 'user_profile_documents',
            'education'     => 'user_profile_education',
            'email'         => 'user_profile_email',
            'family'        => 'family_tab',
            'notifications' => 'show_notification_profile',
            'organisation'  => 'user_profile_organisation',
            'platform'      => 'user_profile_preferences',
        ];

        // Redirect the user away, if they don't have the permission.
        if (isset($section_permissions[$section])) {
            IbHelpers::permission_redirect($section_permissions[$section]);
        }

        $post = $this->request->post();
        $this->template->sidebar = View::factory('sidebar');
        $menu = self::action_ajax_get_submenu(true);
        $menu_items = array();
        foreach ($menu['items'] as $menu_item) {
            $menu_items[] = array('icon' => $menu_item['icon_svg'], 'link' => 'admin/profile/'.$menu_item['id'], 'name' => $menu_item['title']);
        }

        $this->template->sidebar->menus = array($menu_items);

        if ($section == 'contact') {
            $this->template->sidebar->tools = '';

            if (Auth::instance()->has_access('gdpr_download_data')) {
                $this->template->sidebar->tools .= '<a href="/admin/profile/download_data" class="btn btn-primary">' . __('Download My Data') . '</a> &nbsp; ';
            }
            if (Auth::instance()->has_access('gdpr_delete_data')) {
                $this->template->sidebar->tools .= '<button type="button" class="btn btn-danger delete-profile" data-toggle="modal" data-target="#profile-delete-modal">' . __('Delete My Data') . '</button> &nbsp; ';
            }
            if (Auth::instance()->has_access('gdpr_request_cleanse')) {
                $this->template->sidebar->tools .= '<button type="button" class="btn btn-default" data-toggle="modal" data-target="#profile-data_cleanse-confirm-modal">' . __('Request Data Cleanse') . '</button>';
            }
        }

        $cdata = $this->get_contact_from_post($post);
        if (!$cdata) {
            $this->request->redirect('/admin');
            $this->response->status(403);
        }
        $contact = $cdata['contact'];
        $contact3 = $cdata['contact3'];
        $org_contact3 = $cdata['org_contact3'];
        $family3 = $cdata['family3'];
        $contact_id = $cdata['contact_id'];
        $contact_roles = $contact3->get_roles_stubs(true);
        
        $model               = new Model_Users();
        $auth_user           = Auth::instance()->get_user();
        $user_data           = $model->get_user($auth_user['id']);
        $user_id             = $user_data['id'];
        $role                = new Model_Roles($user_data['role_id']);
        $activity_actions    = Model_Activity::get_action_list();
        $activity_item_types = Model_Activity::get_item_type_list();
        $activity_alerts     = (Model_Plugin::is_enabled_for_role('Administrator', 'Messaging')) ? Model_Messaging::get_activity_alert_list('CMS_USER', $auth_user['id']) : FALSE;
        $dashboards          = (class_exists('Model_Dashboard')) ? Model_Dashboard::get_user_accessible() : false;
        $submenu             = Menuarea::get_submenu('profile');
        $title               = ucfirst($section);
        $template_certificate_of_attendance = false;
        $document_templates = Model_Document::get_template();
        foreach($document_templates as $document_template) {
            if ($document_template['name'] == 'certificate_of_attendance') {
                $template_certificate_of_attendance = true;
            }
        }
        foreach ($submenu['items'] as $submenu_item) {
            if ($submenu_item['id'] == 'edit?section='.$section) {
                $title = $submenu_item['title'];
            }
        }
        $section_switcher    = View::factory('snippets/profile_switcher')
            ->set('title',   $title)
            ->set('options', $submenu['items'])
            ->set('section', $section)
            ->set('role',    $role)
        ;

        $contacts3_enabled = Model_Plugin::is_enabled_for_role('Administrator', 'contacts3');

        $this->template->sidebar->breadcrumbs = array(
            array('name' => __('Home'), 'link' => '/admin'),
            array('name' => __('My account'), 'link' => '/admin/profile/edit')
        );

        $this->template->scripts[] = '<script src="' . URL::overload_asset('js/profile.js', ['cachebust' => true]) . '"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_asset('media', 'js/multiple_upload.js', ['cachebust' => true]) . '"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_asset('media', 'js/image_edit.js' , ['cachebust' => true]). '"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_assets_base() . 'js/jquery.validationEngine2.js"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_assets_base() . 'js/jquery.validationEngine2-en.js"></script>';
        $user_data = $model->get_user($user_id);
        $this->template->body = View::factory('admin/profile/' . $section);
        $this->template->body->account = Model_Event::accountDetailsLoad($user_data['id']);
        $this->template->body->billing_address_readonly = $contacts3_enabled && !$contact3->can_edit_billing_address();
        $this->template->body->user = $user_data;
        $this->template->body->contact_id = $contact_id;
        $this->template->body->contact = $contact;
        $this->template->body->contact3 = $contact3;
        $this->template->body->contact_role = (count($contact_roles) > 0) ? current($contact_roles)['name'] : 'Guardian';
        $this->template->body->org_contact3 = $org_contact3;
        $this->template->body->family3 = $family3;
        $this->template->body->counties = Model_Residence::get_all_counties('plugin_courses_counties');
        $this->template->body->notifications = $contact3->get_contact_notifications();
        $this->template->body->notification_types = Model_Contacts3::get_notification_types();
        $this->template->body->preferences = Model_Preferences::get_all_preferences();
        $this->template->body->academic_years = Model_AcademicYear::get_academic_years_options(TRUE);
        $this->template->body->schools = Model_Providers::get_all_schools();
        $this->template->body->years = Model_Years::get_years_where(array(array('publish', '=', 1)));
        $this->template->body->subjects = Model_Subjects::get_all_subjects();
        $this->template->body->subject_preferences_ids = $contact3->get_subject_preferences_ids();
        $this->template->body->levels = Model_Levels::get_all_levels();
        $this->template->body->subject_preferences = $contact3->get_subject_preferences();
        $this->template->body->privileges_preferences = Model_Preferences::get_family_preferences();
        $this->template->body->contact_privileges_preferences = Model_Contacts3::get_contact_privileges_preferences($contact_id);
        $this->template->body->family_members3 = Model_Contacts3::get_family_members($contact3->get_family_id());
        $this->template->body->dashboards = $dashboards;
        $this->template->body->role = $role;
        $this->template->body->timezones = $model->generate_timezone_list();
        $this->template->body->activity_actions    = $activity_actions;
        $this->template->body->activity_item_types = $activity_item_types;
        $this->template->body->activity_alerts     = $activity_alerts;
        $this->template->body->extraSections       = self::$extraSections;
        $this->template->body->printing = (Settings::instance()->get('print_office') == 1);
        $this->template->body->printers = Model_Eprinter::search(array('published' => 1));
        $this->template->body->section_switcher = $section_switcher;
        $this->template->body->nationalities = Model_Country::$nationalities;
        $this->template->body->cards = Model_Contacts3::cards_list($contact_id);
        if (Model_Plugin::is_enabled_for_role('Administrator', 'Messaging')) {
            $this->template->body->imap_settings = DB::select('*')
                ->from('plugin_messaging_imap_accounts')
                ->where('user_id', '=', $auth_user['id'])
                ->execute()
                ->current();
        }
        $this->template->body->has_certificate_template = $template_certificate_of_attendance;
        $this->template->body->doc_array = Model_Document::doc_list_documents(array($contact_id), NULL, FALSE, FALSE, TRUE);
    }

    public function action_save()
    {
        $model    = new Model_Users();
        $user     = Auth::instance()->get_user();
        $post     = $this->request->post();
        $redirect = @$post['redirect'];
        $section = $this->request->query('section');

        $cdata = $this->get_contact_from_post($post);
        if (!$cdata) {
            $error_id = Model_Errorlog::save(null, "SECURITY");
            IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
            $this->request->redirect('/admin');
        }
        $contact = $cdata['contact'];
        $contact3 = $cdata['contact3'];
        $family3 = $cdata['family3'];
        $contact3_id = $cdata['contact_id'];
        $user_id = $cdata['user_id'];
        $contact3 = new Model_Contacts3($contact3_id);
        if ($section == 'cards') {
            Model_Contacts3::card_delete($post['id']);

            IbHelpers::set_message('Cards have been updated.', 'success popup_box');
        } else if ($section == 'education') {
            $contact3->load($post);
            $contact3->address->load($post);

            $contact3->save();

            IbHelpers::set_message('Education details have been updated.', 'success popup_box');
        } else if ($section == 'notifications') {
            if (Model_Plugin::is_enabled_for_role('Administrator', 'contacts3')) {
                $contact3->load($post);
                $contact3->address->load($post);

                $contact3->save();
            }

            if (Model_Plugin::is_enabled_for_role('Administrator', 'events')) {
                $account = Model_Event::accountDetailsLoad($user['id']);
                $account['notify_email_on_buy_ticket'] = @$post['notify_email_on_buy_ticket'] ? 1 : 0;
                Model_Event::accountDetailsSave($account);
            }

            IbHelpers::set_message('Notifications have been updated.', 'success popup_box');
        } else {

            $post = html::clean_array($post);

            if (Model_Plugin::is_enabled_for_role('Administrator', 'contacts3')) {
                if (@$post['mature']) {
                    $contact3->add_role_by_stub('mature');
                }
                if (@$post['email'] || @$post['mobile']) {
                    $post['notifications'] = array();
                    if (@$post['email']) {
                        $post['notifications'][] = array('id' => 'new', 'notification_id' => 1, 'value' => $post['email']);
                    }
                    if (@$post['mobile']) {
                        $post['notifications'][] = array(
                            'id' => 'new',
                            'notification_id' => 2,
                            'dial_code'=> trim(@$post['dial_code_mobile']),
                            'country_dial_code' => trim(@$post['country_dial_code_mobile']),
                            'value' => trim(@$post['mobile'])
                        );
                    }
                }
                $contact3->load($post);
                if (isset($post['address']['personal'])) {
                    $contact3->address->load($post['address']['personal']);
                }

                // Only allow people with the necessary permission to edit the billing address.
                if (isset($post['address']['billing']) && $contact3->can_edit_billing_address()) {
                    $contact3->billing_address->load($post['address']['billing']);
                } else {
                    unset($post['address']['billing']);
                }

                if (array_key_exists('dob', $post)) {
                    $contact3->set_date_of_birth($post['dob']);
                }
                if (array_key_exists('nationality', $post)) {
                    $contact3->set_nationality($post['nationality']);
                }
                if (array_key_exists('gender', $post)) {
                    $contact3->set_gender($post['gender']);
                }
                if (array_key_exists('name', $post)) {
                    $contact3->set_first_name($post['name']);
                }
                if (array_key_exists('surname', $post)) {
                    $contact3->set_last_name($post['surname']);
                }
                if (empty($contact3->get_type())) {
                    $contact3->set_type(Model_Contacts3::find_type('Student')['contact_type_id']);
                }
                if (empty($contact3->get_subtype_id())) {
                    $contact3->set_subtype_id(1);
                }
                // Confirm the contacts3 is saved to the user
                $contact3->set_linked_user_id($user['id']);
                $contact3->save();
                
            } else {
                $contact = new Model_Contacts(@$post['contact_id'], true);
                $contact->set_dob(date::dmy_to_ymd($post['dob']));
                $contact->set_first_name($post['name']);
                $contact->set_last_name($post['surname']);
                $contact->set_linked_user_id($user['id']);
                $contact->set_email($post['email']);
                $contact->set_mobile($post['mobile']);
                if (isset($post['address']['personal'])) {
                    $contact->set_address1($post['address']['personal']['address1']);
                    $contact->set_address2($post['address']['personal']['address2']);
                    $contact->set_address3(Model_Residence::get_county_by_id($post['address']['personal']['county'])['name'] ?? '');
                    $contact->set_country_id(Model_Country::get_country($post['address']['personal']['country'])['id'] ?? '0');
                    $contact->set_postcode($post['address']['personal']['postcode']);
                }
                $contact->test_existing_email = false;
                if ($contact->get_id() == null) {
                    $contact->set_mailing_list('default');
                }
                $contact->save();
            }

            $data = array();
            // filter down to just the details the user should be able to edit
            if (array_key_exists('name', $post)) {
                $data['name'] = $post['name'];
            }
            if (array_key_exists('surname', $post)) {
                $data['surname'] = $post['surname'];
            }
            if (array_key_exists('email', $post)) {
                $data['email'] = $post['email'];
            }
            if (array_key_exists('phone', $post)) {
                $data['phone'] = $post['phone'];
            }
            if (array_key_exists('mobile', $post)) {
                $data['mobile'] = $post['mobile'];
            }
            if (array_key_exists('dial_code_mobile', $post)) {
                $data['dial_code_mobile'] = $post['dial_code_mobile'];
            }
            if (array_key_exists('country_dial_code_mobile', $post)) {
                $data['country_dial_code_mobile'] = $post['country_dial_code_mobile'];
            }
            if (array_key_exists('address', $post) && is_array($post['address'])) {
                if (array_key_exists('address1', $post['address']['personal'])) {
                    $data['address'] = $post['address']['personal']['address1'];
                }
                if (array_key_exists('address2', $post['address']['personal'])) {
                    $data['address_2'] = $post['address']['personal']['address2'];
                }
                if (array_key_exists('address3', $post['address']['personal'])) {
                    $data['address_3'] = $post['address']['personal']['address3'];
                }
                if (array_key_exists('town', $post['address']['personal'])) {
                    $data['address_3'] = $post['address']['personal']['town'];
                }
                if (array_key_exists('country', $post['address']['personal'])) {
                    $data['country'] = $post['address']['personal']['country'];
                }
                if (array_key_exists('county', $post['address']['personal'])) {
                    $data['county'] = $post['address']['personal']['county'];
                }
                if (array_key_exists('postcode', $post['address']['personal'])) {
                    //$data['eircode'] = $post['address']['personal']['postcode'];
                }
            }

            if (array_key_exists('address', $post) && is_string($post['address'])) {
                $data['address'] = $post['address'];
            }
            if (array_key_exists('address_2', $post)) {
                $data['address_2'] = $post['address_2'];
            }
            if (array_key_exists('address_3', $post)) {
                $data['address_3'] = $post['address_3'];
            }
            if (array_key_exists('country', $post)) {
                $data['country'] = $post['country'];
            }
            if (array_key_exists('county', $post)) {
                $data['county'] = $post['county'];
            }
            if (array_key_exists('avatar', $post)) {
                $data['avatar'] = $post['avatar'];
            }
            if (array_key_exists('use_gravatar', $post)) {
                $data['use_gravatar'] = (@$post['use_gravatar'] == 1 OR $post['avatar'] == '') ? 1 : 0;
            }
            if (array_key_exists('eircode', $post)) {
                $data['eircode'] = $post['eircode'];
            }
            if (isset($post['timezone'])) {
                $data['timezone'] = $post['timezone'];
            }
            if (array_key_exists('current_password', $post)) {
                $data['current_password'] = $post['current_password'];
            }
            if (array_key_exists('password', $post)) {
                if (@$data['current_password'] == '') {
                    $this->response->status(403);
                    $this->request->redirect('/admin');
                }
                $data['password'] = $post['password'];
                $error = '';
                if (strlen($data['password']) < 8) {
                    $error = 'This password is too short, please enter a password with a minimum of 8 characters.';
                } else if (!preg_match('/[A-Z]/', $data['password'])) {
                    $error = 'This password must contain at least one capital letter.';
                } else if (!preg_match('/[a-z]/', $data['password'])) {
                    $error = 'This password must contain at least one lower case letter.';
                } else if (!preg_match('/\d/', $data['password'])) {
                    $error = 'This password must contain at least one numerical digit.';
                }
                if ($error != '') {
                    IbHelpers::set_message($error, 'error popup_box');
                    $this->request->redirect('/admin/profile/edit?section=password');
                }
            }
            if (array_key_exists('mpassword', $post)) {
                $data['mpassword'] = $post['mpassword'];
            }
            if (array_key_exists('two_step_auth', $post)) {
                $data['two_step_auth'] = $post['two_step_auth'];
            }
            if (array_key_exists('default_home_page', $post)) {
                $data['default_home_page'] = $post['default_home_page'];
            }
            if (array_key_exists('default_dashboard_id', $post)) {
                $data['default_dashboard_id'] = isset($post['default_dashboard_id']) ? $post['default_dashboard_id'] : null;
            }
            if (array_key_exists('auto_logout_minutes', $post)) {
                $data['auto_logout_minutes'] = $post['auto_logout_minutes'];
            }
            $data['can_login'] = 1;
            if (array_key_exists('user_column_profile', $post)) {
                $data['user_column_profile'] = $post['user_column_profile'];
            }
            $data['date_modified'] = date('Y-m-d H:i:s');
            if (array_key_exists('default_eprinter', $post)) {
                $data['default_eprinter'] = @$post['default_eprinter'];
            }
            if (array_key_exists('default_messaging_signature', $post)) {
                $data['default_messaging_signature'] = @$post['default_messaging_signature'];
            }

            if (isset($post['facebook_pixel_enabled'])) {
                $data['facebook_pixel_enabled'] = $post['facebook_pixel_enabled'];
            }
            if (isset($post['facebook_pixel_code'])) {
                $data['facebook_pixel_code'] = $post['facebook_pixel_code'];
            }

            $result = $model->update_user_data($user['id'], $data);
            //header('content-type: text/plain');print_r($post);var_dump($result);exit;

            if (Model_Plugin::is_enabled_for_role('Administrator', 'Messaging') && array_key_exists('imap', $post)) {
                $imap_settings = $post['imap'];
                if (@$imap_settings['username'] && @$imap_settings['host']) {
                    $imap_settings['deleted'] = 0;
                    $imap_settings['user_id'] = $user['id'];
                    $imap_exists = DB::select('*')
                        ->from('plugin_messaging_imap_accounts')
                        ->where('user_id', '=', $user['id'])
                        ->execute()
                        ->current();
                    if ($imap_exists) {
                        DB::update('plugin_messaging_imap_accounts')
                            ->set($imap_settings)
                            ->where('user_id', '=', $user['id'])
                            ->execute();
                    } else {
                        DB::insert('plugin_messaging_imap_accounts')
                            ->values($imap_settings)
                            ->execute();
                    }
                } else {
                    DB::update('plugin_messaging_imap_accounts')
                        ->set(array('deleted' => 1))
                        ->where('user_id', '=', $user['id'])
                        ->execute();
                }
            }

            if (Model_Plugin::is_enabled_for_role('Administrator', 'Messaging')) {
                Model_Messaging::set_activity_alert_list('CMS_USER', $user['id'], @$post['activity_alert']);
            }

            foreach (self::$extraSections as $extraSection) {
                $extraSection->save($user['id'], $post);
            }

        }

        Auth::instance()->reload_user_data();

        $location = ($redirect == 'save_and_exit') ? '' : 'profile/edit?section=' . $section;
        $this->request->redirect('/admin/' . $location);
    }

    public function action_save_user_column_preference()
    {
        $this->auto_render = FALSE;
        $data = $this->request->post('column');
        $user     = Auth::instance()->get_user();
        $return = DB::update('engine_users')->set(array('user_column_profile'=>$data))->where('id','=',$user['id'])->execute();
        return $return;
    }

	public function action_ajax_set_preference()
	{
		$this->auto_render = FALSE;

		$user   = Auth::instance()->get_user();
		$column = $this->request->post('column');
		$value  = $this->request->post('value');

		$user_model = ORM::factory('Users', $user['id']);
		$user_model->set($column.'_preference', $value);
		$user_model->save();
	}

	public function action_keyboardshortcuts_load()
	{
		$shortcuts = Model_Keyboardshortcut::get_all();
		
		header('content-type: application/json; charset=utf-8');
        echo json_encode($shortcuts);
		exit();
	}

	public function action_acquire_activity_lock()
	{
		$post = $this->request->post();
		$plugin = $post['plugin'];
		$activity = $post['activity'];

		$lock = Model_ActivityLocks::lock($plugin, $activity);
		$this->auto_render = false;
		$this->response->headers('Content-Type', 'application/json');
		echo json_encode($lock);
	}

	public function action_release_activity_lock()
	{
		$post = $this->request->post();
		$plugin = $post['plugin'];
		$activity = $post['activity'];

		$unlock = Model_ActivityLocks::unlock($plugin, $activity);
		$this->auto_render = false;
		$this->response->headers('Content-Type', 'application/json');
		echo json_encode($unlock);
	}

    public function action_ajax_get_submenu($data_only = false)
    {
        $sections = array(
            array('icon_svg' => 'profile',  'id' => 'edit?section=contact', 'title' => 'Profile'),
            array('icon_svg' => 'location', 'id' => 'edit?section=address', 'title' => 'Address'),
        );

        if (Model_Plugin::is_enabled_for_role('Administrator', 'contacts3')) {
            $contact3 = new Model_Contacts3(Model_Contacts3::get_linked_contact_to_user(Auth::instance()->get_user()['id'])['id']);
            if (Auth::instance()->has_access('family_tab')){
                $sections[] = array('icon_svg' => 'family', 'id' => 'edit?section=family', 'title' => 'Family');
            }

            if (Auth::instance()->has_access('user_profile_organisation')) {
                // Check if there are people linked to your organisation
                if (count($contact3->get_contact_relations_details(array('contact_type' => 'organisation'))) > 0) {
                    $sections[] = array('icon_svg' => 'business', 'id' => 'edit?section=organisation', 'title' => 'Organisation');
                }
            }

            if (Auth::instance()->has_access('show_notification_profile')) {
                $sections[] = array('icon_svg' => 'notification', 'id' => 'edit?section=notifications', 'title' => 'Notifications');
            }

            if (Model_Plugin::is_enabled_for_role('Administrator', 'courses') && Auth::instance()->has_access('user_profile_education')) {
                $sections[] = array('icon_svg' => 'courses', 'id' => 'edit?section=education', 'title' => 'Education');
            }
        }

        else if (Model_Plugin::is_enabled_for_role('Administrator', 'events')) {
            if (Auth::instance()->has_access('show_notification_profile')) {
                $sections[] = array('icon_svg' => 'notification', 'id' => 'edit?section=notifications', 'title' => 'Notifications');
            }
        }

        if (Settings::instance()->get('stripe_enabled') == 'TRUE') {
            $sections[] = array('icon_svg' => 'payment', 'id' => 'edit?section=payments', 'title' => 'Payments');
        }

        $sections[] = array('icon_svg' => 'password', 'id' => 'edit?section=password', 'title' => 'Password');
        if (Auth::instance()->has_access('user_profile_documents')) {
            $sections[] = array('icon_svg' => 'template', 'id' => 'edit?section=documents', 'title' => 'Documents');
        }
        if (Auth::instance()->has_access('user_profile_email')) {
            $sections[] = array('icon_svg' => 'email', 'id' => 'edit?section=email', 'title' => 'Email settings');
        }
        if (Auth::instance()->has_access('user_profile_preferences')) {
            $sections[] = array('icon_svg' => 'settings', 'id' => 'edit?section=platform', 'title' => 'Preferences');
        }
        if (Settings::instance()->get('payments_store_card') == 1) {
            $sections[] = array('icon_svg' => 'payment', 'id' => 'edit?section=cards', 'title' => 'Saved cards');
        }

        $return = array(
            'link'  => '',
            'items' => $sections
        );

        if ($data_only) {
            return $return;
        } else {
            $this->auto_render = false;
            $this->response->headers('Content-type', 'application/json; charset=utf-8');
            $this->response->body(json_encode($return));
        }
    }

    public function action_stripe_disconnect()
    {
        $eventIdToRedirect = Session::instance()->get('stripe_connect_after_load_event_id');
        if ($eventIdToRedirect) {
            Session::instance()->set('stripe_connect_after_load_event_id', null);
            $this->request->redirect('/admin/events/edit_event/' . $eventIdToRedirect);
        }

        if (Auth::instance()->has_access('events_edit') && $this->request->param('id')) {
            $account = Model_Event::accountDetailsLoad($this->request->param('id'));
        } else {
            if (Auth::instance()->has_access('events_edit_limited') || !$this->request->param('id')) {
                $user = Auth::instance()->get_user();
                $account = Model_Event::accountDetailsLoad($user['id']);
            } else {
                IbHelpers::set_message("You don't have permission!", 'warning popup_box');
                $this->request->redirect('/admin');
            }
        }

        require_once APPPATH . '/vendor/stripe/lib/Stripe.php';

        $stripe_testing = (Settings::instance()->get('stripe_test_mode') == 'TRUE');
        $stripe['secret_key'] = ($stripe_testing) ? Settings::instance()->get('stripe_test_private_key') : Settings::instance()->get('stripe_private_key');
        $stripe['publishable_key'] = ($stripe_testing) ? Settings::instance()->get('stripe_test_public_key') : Settings::instance()->get('stripe_public_key');
        Stripe::setApiKey($stripe['secret_key']);

        $curl = curl_init('https://connect.stripe.com/oauth/deauthorize');
        if (!defined('CURL_SSLVERSION_TLSv1_2')) {
            define('CURL_SSLVERSION_TLSv1_2', 6);
        }
        curl_setopt($curl, CURLOPT_USERPWD, $stripe['secret_key']);
        curl_setopt($curl, CURLOPT_POSTFIELDS, array('client_id' => Settings::instance()->get('stripe_client_id'), 'stripe_user_id' => $account['stripe_auth']['stripe_user_id']));
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        $result = json_decode(curl_exec($curl), true);
        curl_close($curl);
        if ($result['stripe_user_id'] == $account['stripe_auth']['stripe_user_id']) {
            $account['stripe_auth'] = '';
            $account['use_stripe_connect'] = 0;
            Model_Event::accountDetailsSave($account);
        }
        $this->request->redirect('/admin/profile/edit?section=contact');
    }

    public function action_stripe_connect_start()
    {
        $stripeId = Settings::instance()->get('stripe_client_id');
        $urlParams = array(
            'response_type' => 'code',
            'client_id' => $stripeId,
            'scope' => 'read_write',
            'redirect_uri' => URL::site('/admin/profile/stripe_connect_end')
        );
        $url = 'https://connect.stripe.com/oauth/authorize?' . http_build_query($urlParams);
        $this->request->redirect($url);
    }

    public function action_stripe_connect_end()
    {
        $error = $this->request->query('error');
        if ($error != '') {
            IBHelpers::set_message('Error authorizing stripe(' . $error . ')', 'error popup_box');
        } else {
            $scope = $this->request->query('scope');
            $code = $this->request->query('code');
            $stripeId = Settings::instance()->get('stripe_client_id');
            $stripeSecret = Settings::instance()->get('stripe_test_mode') == 'TRUE' ?
                Settings::instance()->get('stripe_test_private_key') :
                Settings::instance()->get('stripe_private_key');

            if ($code) {
                $token_request_body = array(
                    'grant_type' => 'authorization_code',
                    'client_id' => $stripeId,
                    'code' => $code,
                    'client_secret' => trim($stripeSecret)
                );

                try {
                    $req = curl_init('https://connect.stripe.com/oauth/token');
                    if (!defined('CURL_SSLVERSION_TLSv1_2')) {
                        define('CURL_SSLVERSION_TLSv1_2', 6);
                    }
                    curl_setopt($req, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($req, CURLOPT_POST, true);
                    curl_setopt($req, CURLOPT_POSTFIELDS, http_build_query($token_request_body));
                    curl_setopt($req, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);

                    $respCode = curl_getinfo($req, CURLINFO_HTTP_CODE);
                    $resp = json_decode(curl_exec($req), true);
                    curl_close($req);

                    if (isset($resp['access_token'])) {
                        $user = Auth::instance()->get_user();
                        Model_Event::accountDetailsSave(array(
                            'owner_id' => $user['id'],
                            'use_stripe_connect' => 1,
                            'stripe_auth' => json_encode($resp)
                        ));
                    }
                } catch (Exception $exc) {
                    IBHelpers::set_message('Error authorizing stripe(' . $exc->getMessage() . ')', 'error  popup_box');
                }
            } else {
                IBHelpers::set_message('Error authorizing stripe', 'error popup_box');
            }
        }

        $eventIdToRedirect = Session::instance()->get('stripe_connect_after_load_event_id');
        if ($eventIdToRedirect) {
            Session::instance()->set('stripe_connect_after_load_event_id', null);
            $this->request->redirect('/admin/events/edit_event/' . $eventIdToRedirect);
        } else {
            $this->request->redirect('/admin/profile/edit?section=payments');
        }
    }

    public function action_download_data()
    {
        // Redirect the user away, with an error message, if they don't have permission.
        Ibhelpers::permission_redirect('gdpr_download_data');

        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/csv; charset=utf-8;');
        $this->response->headers('Content-disposition',  'attachment; filename="user_data.csv"');

        $user = Auth::instance()->get_user();
        $user_id = $user['id'];

        $data = Model_Users::download_data($user_id);
        
        $tmp_tfile = fopen('php://output', 'w');

        foreach ($data as $line) {
            foreach ($line as $i => $column) {
                $column = trim($column);
                if (in_array($column[0], ['@', '+', '-', '='])) {
                    $column = substr($column, 1);
                }
                $line[$i] = $column;
            }
            fputcsv($tmp_tfile, $line, ';');
        }
        
    }

    // Send email if the user requests a data cleanse.
    // Return JSON saying if successful or not and an error message, if necessary.
    public function action_request_data_cleanse()
    {
        $this->auto_render = false;

        // Check if the user has permission
        if (!Auth::instance()->has_access('gdpr_request_cleanse')) {
            echo json_encode([
                'success' => false,
                'message' => __('You need access to the $1 permission to perform this action.', [
                    '$1' => '<code>gdpr_request_cleanse</code>'
                ])
            ]);
            return false;
        }

        // Get data for email
        $user      = Auth::instance()->get_user_orm();
        $contact   = Auth::instance()->get_contact();
        $messaging = new Model_Messaging();

        $parameters = [
            'company_name' => Settings::instance()->get('company_title'),
            'contact_id'   => $contact->id,
            'email'        => $user->email,
            'name'         => $user->get_full_name(),
            'user_id'      => $user->id,
        ];

        // Send email
        $sent = $messaging->send_template('gdpr_request_cleanse_admin', null ,null, [], $parameters);

        // Log the activity
        if ($sent) {
            $activity = new Model_Activity();

            $activity
                ->set_action('request_data_cleanse')
                ->set_item_type('contact3')
                ->set_item_id($contact->id)
                ->set_scope_id($contact->id)
                ->save();
        }

        // Return data saying if successful, accompanied by a message.
        echo json_encode([
            'success' => $sent,
            'message' => $sent ? 'Your request has been sent' : 'Error submitting request.'
        ]);
    }

    public function action_delete()
    {
        // Redirect the user away, with a message, if they don't have permission
        IbHelpers::permission_redirect('gdpr_delete_data');

        $user = Auth::instance()->get_user();
        $user_id = $user['id'];
        Model_Users::delete_data($user_id);
        Auth::instance()->logout();
        IBHelpers::set_message('Your account has been deleted', 'info popup_box');
        $this->request->redirect('/admin/login');
    }
}