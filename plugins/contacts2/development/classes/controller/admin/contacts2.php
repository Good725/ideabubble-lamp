<?php defined('SYSPATH') or die('No Direct Script Access.');

class Controller_Admin_Contacts2 extends Controller_Cms
{
    function before()
    {
        parent::before();

        // Menu items
        $this->template->sidebar = View::factory('sidebar');
        $this->template->sidebar->breadcrumbs = array(
            array('name' => 'Home',     'link' => '/admin'),
            array('name' => 'Contacts', 'link' => '/admin/contacts2')
        );
        $menus = array (
			array('icon' => 'contacts', 'name' => 'Contacts',      'link' => '/admin/contacts2')
        );
        if (!Auth::instance()->has_access('contacts2_index_limited')) {
            $menus[] = array('icon' => 'messaging', 'name' => 'Mailing Lists', 'link' => '/admin/contacts2/mailing_lists');
        }
        $assets_folder_path = @Kohana::$config->load('config')->assets_folder_path ?: 'default';

        $stylesheets    = array(
            URL::get_engine_plugin_assets_base('messaging').'css/list_messages.css' => 'screen',
            URL::get_engine_plugin_assets_base('contacts2').'css/contacts.css' => 'screen',
            '/assets/'.$assets_folder_path.'/css/validation.css' => 'screen'
        );

        $this->template->styles    = array_merge($this->template->styles, $stylesheets);

        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('contacts2') . 'js/contacts.js"></script>';
        $this->template->scripts[] = '<script type="text/javascript" src="/assets/' . $assets_folder_path . '/js/jquery.validationEngine2.js"></script>';
        $this->template->scripts[] = '<script type="text/javascript" src="/assets/' . $assets_folder_path . '/js/jquery.validationEngine2-en.js"></script>';

        $extensions = Model_Contacts::getExtentions();
        foreach ($extensions as $extension) {
            $menus = $extension->menus($menus);
        }
        $this->template->sidebar->menus = array($menus);


        switch($this->request->action())
        {
            case 'index':
			case 'mailing_lists':
                if (Auth::instance()->has_access('contacts2_edit') && !Auth::instance()->has_access('contacts2_index_limited')) {
                    $this->template->sidebar->tools = '<div class="btn-group">' .
                        '<button type="button" class="btn dropdown-toggle" data-toggle="dropdown">Select Action <span class="caret"></span></button>' .
                        '<ul class="dropdown-menu">' .
                        '<li><a href="/admin/contacts2/edit/" id="contact-list-add">Add Contact</a></li>' .
                        '<li><a href="/admin/contacts2/import_csv">Import CSV</a></li>' .
                        '<li><a href="/admin/contacts2/get_contacts_csv">Download CSV</a></li>' .
						'<li><a href="/admin/contacts2/edit_mailing_list">'.__('Add Mailing List').'</a></li>' .
                        '</ul></div>';
                }
                break;
        }

        Model_Contacts::import_old_email_mobile_to_new_communications();
    }

    /**
     * Entry point.
     */
    public function action_index()
    {
        if (Model_Plugin::is_enabled_for_role('Administrator', 'contacts3')) {
            $this->request->redirect('/admin/contacts3');
        }

        if (!Auth::instance()->has_access('contacts2_index') && !Auth::instance()->has_access('contacts2_index_limited')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        } else {
            // Assets
            $extensions = Model_Contacts::getExtentions();
            foreach ($extensions as $extension) {
                foreach ($extension->required_js() as $required_js) {
                    $this->template->scripts[] = '<script src="' . $required_js . '"></script>';
                }
            }

            // Default action
            $this->action_list();
        }
    }

    public function action_import_csv()
    {
		if ( ! Auth::instance()->has_access('contacts2_edit'))
		{
			IbHelpers::set_message("You need access to the &quot;edit contact&quot; permission to perform this action.", 'warning popup_box');
			$this->request->redirect('/admin/contacts2');
		}

        $contactLists = Model_Contacts::get_mailing_list_all();
        $columns = array(
            'first_name',
            'last_name',
            'email',
            'phone',
            'mobile',
            'notes'
        );
        $step = $this->request->query('step');
        $mapColumns = array();
        $post = $this->request->post();
        $encoding = isset($post['encoding']) ? $post['encoding'] : 'UTF-8';
        $delimiter = isset($post['delimiter']) ? $post['delimiter'] : ',';
        $enclosure = isset($post['enclosure']) ? $post['enclosure'] : '"';
        $tmpfile = null;
        if (!$step) {
            $step = 1;
        } else if ($step == 2) {
            if ($_FILES['csv']['error']) {
                IbHelpers::set_message('File upload failed', 'error popup_box');
                $this->request->redirect('/admin/contacts2/import_csv');
            } else {
                $tmpfile = tempnam('/tmp', 'import_csv');
                move_uploaded_file($_FILES['csv']['tmp_name'], $tmpfile);
                $mapColumns = IbHelpers::csvGetColumns(
                    $tmpfile,
                    $encoding,
                    $delimiter,
                    $enclosure
                );
            }
        } else if ($step == 3) {
            if (isset($post['start'])) {
                set_time_limit(0);
                ignore_user_abort(true);
                $report = IbHelpers::csvTransferToTable(
                    $post['tmpfile'],
                    Model_Contacts::TABLE_CONTACT,
                    $post['map'],
                    'email',
                    array('mailing_list' => Model_Contacts::sql_get_mailing_list_id($contactLists[$post['mailing_list']])),
                    $encoding,
                    $delimiter,
                    $enclosure
                );
                if ($report !== false) {
                    unlink($post['tmpfile']);
                    $_SESSION['contacts2_import_report'] = $report;
                    $this->request->redirect('/admin/contacts2/import_csv?step=3');
                }
            } else {
                if (!isset($_SESSION['contacts2_import_report'])) {
                    $this->request->redirect('/admin/contacts2/import_csv');
                }
            }
        }

        $this->template->body = View::factory('import_csv_step' . $step);
        if ($step == 3){
            $this->template->body->importReport = $_SESSION['contacts2_import_report'];
            unset($_SESSION['contacts2_import_report']);
        } else {
            $this->template->body->contactLists = $contactLists;
            $this->template->body->columns = $columns;
            $this->template->body->mapColumns = $mapColumns;
            $this->template->body->tmpfile = $tmpfile;
            $this->template->body->encoding = $encoding;
            $this->template->body->delimiter = $delimiter;
            $this->template->body->enclosure = $enclosure;
        }
    }

    public function action_sample_csv_import()
    {
        header('Content-Type: text/plain; charset=utf-8');
        header('Content-Disposition: attachment; filename=sample.csv');
        echo '"first_name","last_name","email","mobile","phone"
"Michael","O\'Callaghan","michael@ideabubble.ie","0831234567","1231234567"
';
        exit();
    }

    /**
     * List contacts.
     */
    public function action_list()
    {
        if (!Auth::instance()->has_access('contacts2_index') && !Auth::instance()->has_access('contacts2_index_limited')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        } else {

            // get icon
            $results['plugin'] = Model_Plugin::get_plugin_by_name('contacts2');

            // Select the view
            $this->template->body = View::factory('list_contacts', $results);

            $check_permission_user_id = null;
            if (!Auth::instance()->has_access('contacts2_index')) {
                $user = Auth::instance()->get_user();
                $check_permission_user_id = $user['id'];
            }
            // Fill the template
            $this->template->body->mailing_list = Model_Contacts::get_mailing_list_all();
            //$this->template->body->contacts = Model_Contacts::get_contact_all('id', 'asc', $check_permission_user_id);
        }
    }

    public function action_list_datatable()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json');

        if (!Auth::instance()->has_access('contacts2_index') && !Auth::instance()->has_access('contacts2_index_limited')) {
            $result = array();
            $this->response->status(403);
        } else {
            $params = $this->request->query();
            $check_permission_user_id = null;
            if (!Auth::instance()->has_access('contacts2_index')) {
                $user = Auth::instance()->get_user();
                $params['check_permission_user_id'] = $user['id'];
            }

            $result = Model_Contacts::get_datatable($params);
        }
        echo json_encode($result);
    }

    /**
     * Add / Edit contact.
     */
    public function action_edit()
    {
        if (!Auth::instance()->has_access('contacts2_edit') && !Auth::instance()->has_access('contacts2_view_limited')) {
            if ($this->request->is_ajax()) {
                $this->response->status(403);
            } else {
				IbHelpers::set_message("You need access to the &quot;edit contact&quot; permission to perform this action.", 'warning popup_box');
                $this->request->redirect('/admin');
            }
        }

        $view = View::factory('edit_contact');

        // Load contact
        $contact_id = $this->request->param('id');
        $contact = new Model_Contacts(is_numeric($contact_id) ? $contact_id : null);
        $relations = Model_Contacts::getRelations();
        $permissions = Model_Contacts::getPermissions($contact_id);
        $messages = null;
        if (Model_Plugin::is_enabled_for_role('Administrator', 'Messaging')) {
            $messaging = new Model_Messaging();
            $messages = $messaging->search_messages(array(
                'target' => $this->request->param('id'),
                'target_type' => 'CMS_CONTACT'
            ));
        }

        // Fill the template
        $view->contact_details = $contact->get_details();
        $view->mailing_list = Model_Contacts::get_mailing_list_all();
        $view->messages = $messages;
        $view->relations = $relations;
        $view->permissions = $permissions;
        $view->communication_types = Model_Contacts::get_communication_types();
        $view->preference_types = Model_Contacts::get_preference_types();
        $view->notes = Model_Notes::search(array('type' => 'Contact', 'reference_id' => $contact_id));
        $view->documents = $contact_id ? Model_Document::doc_list_documents(array($contact_id), null, false) : '';
		$view->countries = Model_Event::getCountryMatrix();

        $extensions = Model_Contacts::getExtentions();
        foreach ($extensions as $extension) {
            if ($extension->is_container()) {
                $ext_data = $extension->getData($view->contact_details, $this->request);
                if ($ext_data) {
                    $container_view = View::factory($extension->get_container());
                    $container_view->contact = $view;
					$container_view->countries = $view->countries;
                    $container_view->data = $ext_data;
                    $view = $container_view;
                }
            }
        }

        /*
         * if ajax then send the contact edit fragment
         * else send whole page
         */
        if ($this->request->is_ajax()) {
            $this->auto_render = false;
            $messages = IbHelpers::get_messages();
            $view->alert = $messages;
            echo $view;
        } else {
            // Assets
            $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('contacts2') . 'js/contacts.js"></script>';

            // Select the view
            $this->template->body = $view;
        }
    }

    /**
     * Save.
     */
    public function action_save()
    {
		if ( ! Auth::instance()->has_access('contacts2_edit')) {
			IbHelpers::set_message("You need access to the &quot;edit contact&quot; permission to perform this action.", 'warning popup_box');
			$this->request->redirect('/admin');
		}

        $ok = false;
        $contact = null;
        $details = null;
        if (Model_Contacts::service_validate_submit()) {
            try {
                Database::instance()->begin();
                $post = $this->request->post();
                // Load the contact or create a new one
                $contact = (is_numeric($_POST['id'])) ? new Model_Contacts($_POST['id']) : new Model_Contacts();
                // Set the data
                $contact->set_title($_POST['title']);
                $contact->set_first_name($_POST['first_name']);
                $contact->set_last_name($_POST['last_name']);
                $contact->set_email(@$_POST['email']);
                $contact->set_mailing_list((strlen($_POST['new_mailing_list']) > 0) ? $_POST['new_mailing_list'] : $_POST['mailing_list']);
                $contact->set_phone(@$_POST['phone']);

                $mobile        = isset($_POST['mobile'])        ? trim($_POST['mobile'])        : '';
                $mobile_code   = isset($_POST['mobile_code'])   ? trim($_POST['mobile_code'])   : '';
                $mobile_number = isset($_POST['mobile_number']) ? trim($_POST['mobile_number']) : '';
                $mobile        = $mobile ? $mobile : trim($mobile_code.' '.$mobile_number);

                $contact->set_mobile($mobile);
                $contact->set_notes($_POST['notes']);
                $contact->set_address1($_POST['address1']);
                $contact->set_address2($_POST['address2']);
                $contact->set_address3($_POST['address3']);
                $contact->set_address4(@$_POST['address4']);
                if (isset($_POST['country_id'])) {
                    $contact->set_country_id($_POST['country_id']);
                }
                if (isset($_POST['postcode'])) {
                    $contact->set_postcode($_POST['postcode']);
                }
                if (isset($_POST['coordinates'])) {
                    $contact->set_coordinates($_POST['coordinates']);
                }
                $contact->set_dob($this->request->Post('dob'));
                $contact->set_publish(@$_POST['publish']);
                $contact->set_relations(isset($_POST['has_relation']) ? $_POST['has_relation'] : array());
                //$contact->set_permissions(isset($_POST['has_permission_user_id']) ? $_POST['has_permission_user_id'] : array());
                $contact->set_communications(isset($_POST['comm']) ? $_POST['comm'] : array());
                $contact->set_preferences(isset($_POST['preference']) ? $_POST['preference'] : array());

                // Save
                $ok = $contact->save();
                if ($ok) {
                    $details = $contact->get_details();
                    $extensions = Model_Contacts::getExtentions();
                    foreach ($extensions as $extension) {
                        $extension->saveData($details['id'], $post);
                    }
                }

                Database::instance()->commit();
            } catch (Exception $exc) {
                Database::instance()->rollback();
                throw $exc;
            }
        }

        if ($ok) {
            // Operation completed
            IbHelpers::set_message((is_numeric($_POST['id'])) ? 'Contact successfully updated.' : 'Contact successfully added.', 'success popup_box');


            $this->request->redirect('/admin/contacts2/edit/' . $details['id']);
        } else {
            // Operation not completed
            IbHelpers::set_message('Unable to complete the requested operation. Please, review the form below.', 'info popup_box');

            // Call the proper function
            $this->action_edit();
        }
    }

    /**
     * Toggle contact publish.
     */
    public function action_ajax_toggle_publish()
    {
		$ok = FALSE;

		if (Auth::instance()->has_access('contacts2_edit') OR Auth::instance()->has_access('contacts2_view_limited'))
		{
	        $id = $this->request->param('id');

    	    if (isset($id)) {
    	        $ok = Model_Contacts::toggle_contact_publish($id);
    	    }
		}

        $this->response->body($ok ? '1' : '0');
        $this->auto_render = FALSE;
    }

    /**
     * Delete contact.
     */
    public function action_ajax_delete()
    {
        $ok = FALSE;

		if (Auth::instance()->has_access('contacts2_delete'))
		{
			$id = $this->request->param('id');

			if (isset($id)) {
				$ok = Model_Contacts::delete_contact($id);
			}
		}

        $this->response->body($ok ? '1' : '0');
        $this->auto_render = FALSE;
    }

    public function action_get_contacts_csv()
    {
		if ( ! Auth::instance()->has_access('contacts2_edit') && ! Auth::instance()->has_access('contacts2_view_limited'))
		{
			IbHelpers::set_message("You need access to the &quot;edit contact&quot; permission to perform this action.", 'warning popup_box');
			$this->request->redirect('/admin/contacts2');
		}
        $contacts = Model_Contacts::get_all_contacts_for_csv();
        ExportCsv::export_report_data_array($this->response, $contacts, "contacts");
    }

    public function action_get_contact()
    {
        $this->auto_render = FALSE;
        $post = $this->request->post();
        echo Model_Contacts::get_contact($post);
    }

    public function action_autocomplete_list()
    {
        $contacts = Model_Contacts::autocomplete_list($this->request->query('term'), $this->request->query('list'));
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        echo json_encode($contacts);
    }

    public function action_remove_permissions()
    {
        Model_Contacts::removePermission($this->request->query('contact_id'), $this->request->query('user_id'));
        $this->request->redirect('/admin/contacts2/edit/' . $this->request->query('contact_id'));
    }

    public function action_add_permissions()
    {
        Model_Contacts::addPermission($this->request->query('contact_id'), $this->request->query('user_id'));
        $this->request->redirect('/admin/contacts2/edit/' . $this->request->query('contact_id'));
    }

    public function action_autocomplete_permission_list()
    {
        $contacts = Model_Users::autocomplete_list($this->request->query('term'));
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        echo json_encode($contacts);
    }

    public function action_test_email()
    {
        $contacts = Model_Contacts::search(array('email' => $this->request->post('email')));
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        echo json_encode($contacts);
    }



	public function action_get_documents()
	{
		$this->auto_render = false;

		$contact_id = $this->request->query('contact_id');
		$contact_ids = array($contact_id);

        $documents = Model_Document::doc_list_documents($contact_ids, null, false);
		echo $documents;
	}

	public function action_ajax_get_attendance()
	{
		$this->auto_render = false;
		$view = View::factory('/list_contact_attendance');
		// $view->attendance = Model_KES_Bookings::get_attendance($this->request->post());
		$view->attendance  = array('timeslots' => array(
			array(
				'id'              => '140',
				'booking_item_id' => '108',
				'schedule'        => 'Schedule A',
				'course'          => 'Course',
				'plocation'       => 'Location',
				'location'        => 'Room 1',
				'datetime_start'  => date('Y-m-d', strtotime("+1 week + 18 hours")),
				'datetime_end'    => date('Y-m-d', strtotime("+1 week + 19 hours")),
				'attending'       => 1,
				'timeslot_status' => '',
				'note'            => 'This is a note'
			),
			array(
				'id'              => '150',
				'booking_item_id' => '111',
				'schedule'        => 'Schedule B',
				'course'          => 'Course Y',
				'plocation'       => 'Location',
				'location'        => 'Room 4',
				'datetime_start'  => date('Y-m-d', strtotime("+1 day + 17 hours")),
				'datetime_end'    => date('Y-m-d', strtotime("+1 day + 18 hours")),
				'attending'       => 0,
				'timeslot_status' => '',
				'note'            => ''
			)
		));
		$this->response->body($view);
	}

	public function action_mailing_lists()
	{
		if ( ! Auth::instance()->has_access('contacts2_index') && ! Auth::instance()->has_access('contacts2_index_limited'))
		{
			IbHelpers::set_message("You do not have access to the &quot;contacts2&quto; permission", 'warning popup_box');
			$this->request->redirect('/admin');
		}

		$this->template->sidebar->breadcrumbs[] = array('name' => 'Mailing Lists', 'link' => '/admin/contacts2/mailing_lists');
		$this->template->body = View::factory('list_mailing_lists');
		$this->template->body->lists = Model_Contacts::get_mailing_lists();
	}

	public function action_edit_mailing_list()
	{
		$id = $this->request->param('id', 0);
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('contacts2').'js/mailing_list.js"></script>';
		$this->template->body = View::factory('edit_mailing_list');
		$this->template->body->list = Model_Contacts::get_mailing_lists(array('id' => $id));
        if ($id)
        {
            $this->template->body->contacts = DB::select()
                ->from(Model_Contacts::TABLE_CONTACT)
                ->where('mailing_list', '=', $id)
                ->where('deleted', '=', 0)
                ->execute()->as_array();
        }
        else
        {
            $this->template->body->contacts = array();
        }
	}

	public function action_save_mailing_list()
	{
		$id                  = $this->request->param('id');
		$post                = $this->request->post();
        $post['contact_ids'] = array_filter($post['contact_ids']);
		$saved               = Model_Contacts::save_mailing_list($post);

		if ( ! $saved['success'])
		{
			IbHelpers::set_message($saved['error_message'], 'danger popup_box');
			$this->request->redirect('/admin/contacts2/edit_mailing_list/'.$id);
		}
		else
		{
			if ($post['action'] == 'save_and_exit')
			{
				$this->request->redirect('/admin/contacts2');
			}
			else
			{
				IbHelpers::set_message('Mailing list #'.$saved['data']['id'].': "'.$saved['data']['name'].'" saved.', 'success popup_box');
				$this->request->redirect('/admin/contacts2/edit_mailing_list/'.$saved['data']['id']);
			}
		}
	}

    public function action_ajax_get_contacts()
    {
        header('Content-Type: application/json; charset=utf-8');
        $this->auto_render = FALSE;
        $term = $this->request->query('term');

        if (trim($term))
        {
            $label    = DB::expr("REPLACE(CONCAT(`title`, ' ', `first_name`, ' ', `last_name`, ' (', `email`, ')'), ' ()', '')");
            $contacts = DB::select(
                'c.id', 'c.title', 'c.first_name', 'c.last_name', 'c.email', array('c.id', 'value'), array($label, 'label'),
                array('m.id', 'mailing_list_id'), array('m.name', 'mailing_list')
            )
                ->from(array(Model_Contacts::TABLE_CONTACT, 'c'))
                ->join(array(Model_Contacts::TABLE_MAILING_LIST, 'm'), 'left')->on('c.mailing_list', '=', 'm.id')
                ->where($label, 'like', '%'.trim($term).'%')
                ->and_where('c.deleted', '=', 0)
                ->order_by('c.first_name')
                ->order_by('c.last_name')
                ->limit(10)
                ->execute()
                ->as_array();
        }
        else
        {
            $contacts = array();
        }

        echo json_encode($contacts);
    }


    public function action_import_contacts3()
    {
        $t1 = microtime(1);
        session_commit();
        set_time_limit(0);
        ignore_user_abort(true);

        $contact_map = array();
        $family_map = array();

        $db = Database::instance();
        $db->begin();

        $now = date::now();
        $log = '';
        try {
            $contacts3 = DB::select(
                'contacts3.*',
                'c3family.primary_contact_id',
                'address.address1',
                'address.address2',
                'address.address3',
                'address.country',
                'address.county',
                'address.postcode',
                'address.town',
                'address.coordinates'
            )
                ->from(array('plugin_contacts3_contacts', 'contacts3'))
                    ->join(array('plugin_contacts3_sync', 'sync'), 'left')
                        ->on('contacts3.id', '=', 'sync.contact3_id')
                    ->join(array('plugin_contacts3_family', 'c3family'), 'left')
                        ->on('contacts3.family_id', '=', 'c3family.family_id')
                    ->join(array('plugin_contacts3_residences', 'address'), 'left')
                        ->on('contacts3.residence', '=', 'address.address_id')
                        ->on('address.delete', '=', DB::expr(0))
                ->where('contacts3.delete', '=', 0)
                ->and_where('sync.contact3_id', 'is', null)
                ->order_by('id', 'desc')
                //->limit(10)
                ->execute()
                ->as_array();
            //header('content-type: text/plain');print_r($contacts3);exit;

            $tables_to_update_ids_new = array(
                'plugin_contacts3_contacts' => 'id',
                'plugin_contacts3_contact_has_course_subject_preferences' => 'contact_id',
                'plugin_contacts3_contact_has_course_type_preferences' => 'contact_id',
                'plugin_contacts3_contact_has_notifications' => 'contact_id',
                'plugin_contacts3_contact_has_preferences' => 'contact_id',
                'plugin_contacts3_contact_has_roles' => 'contact_id',
                'plugin_contacts3_contact_has_subject_preferences' => 'contact_id',
                'plugin_contacts3_family' => 'primary_contact_id',
                'plugin_contacts3_mytime' => 'contact_id',
                'plugin_contacts3_users_has_permission' => 'contact3_id',
                'plugin_ib_educate_bookings' => 'contact_id',
                'plugin_bookings_transactions' => 'contact_id'
            );

            /* negate ids for swapping with new contact2 ids*/
            foreach ($tables_to_update_ids_new as $table => $column) {
                DB::update($table)
                    ->set(array($column => DB::expr('-' . $column)))
                    ->where($column, '>', 0)
                    ->execute();
            }

            DB::update('plugin_ib_educate_bookings')
                ->set(array('bill_payer' => '-bill_payer'))
                ->where('bill_payer', '>', 0)
                ->execute();

            DB::update('plugin_messaging_message_targets')
                ->set(array('target' => '-target'))
                ->where('target_type', '=', 'CMS_CONTACT3')
                ->where('target', '>', 0)
                ->execute();

            DB::update('plugin_contacts3_notes')
                ->set(array('link_id' => '-link_id'))
                ->where('table_link_id', '=', 1)
                ->where('link_id', '>', 0)
                ->execute();

            foreach ($contacts3 as $contact3) {
                $user_permissions = DB::select('*')
                    ->from('plugin_contacts3_users_has_permission')
                    ->where('contact3_id', '=', '-' . $contact3['id'])
                    ->execute()
                    ->as_array();

                $notifications = DB::select('*')
                    ->from('plugin_contacts3_contact_has_notifications')
                    ->where_open()
                    ->or_where('contact_id', '=', '-' . $contact3['id'])
                    ->or_where('group_id', '=', $contact3['notifications_group_id'])
                    ->where_close()
                    ->and_where('deleted', '=', 0)
                    ->execute()
                    ->as_array();


                $c_roles = DB::select('role_id')
                    ->from('plugin_contacts3_contact_has_roles')
                    ->where('contact_id', '=', $contact3['id'])
                    ->execute()
                    ->as_array();
                foreach ($c_roles as $i => $c_role) {
                    $c_roles[$i] = $c_role['role_id'];
                }

                $mlist = 1;

                if (in_array(1, $c_roles)) {
                    $mlist = 8;
                } else if (in_array(2, $c_roles)) {
                    $mlist = 9;
                } else if (in_array(3, $c_roles)) {
                    $mlist = 1;
                } else if (in_array(4, $c_roles)) {
                    $mlist = 4;
                } else if (in_array(6, $c_roles)) {
                    $mlist = 2;
                }
                $inserted = DB::insert('plugin_contacts_contact')
                    ->values(
                        array(
                            'title' => $contact3['title'],
                            'first_name' => $contact3['first_name'],
                            'last_name' => $contact3['last_name'],
                            'dob' => $contact3['date_of_birth'],
                            'address1' => $contact3['address1'],
                            'address2' => $contact3['address2'],
                            'address3' => $contact3['address3'],
                            'address4' => $contact3['county'],
                            'country_id' => $contact3['country'],
                            'postcode' => $contact3['postcode'],
                            'coordinates' => $contact3['coordinates'],
                            'publish' => $contact3['publish'],
                            'last_modification' => $now,
                            'notes' => 'imported from contacts3',
                            'mailing_list' => $mlist,
                        )
                    )->execute();
                $contact_id = $inserted[0];

                $log .= 'Contact ' . $contact3['title'] . ' ' . $contact3['first_name'] . ' ' . $contact3['last_name'] . ' => ' . $contact3['id'] . ':' . $contact_id . "\n";

                $contact_map[$contact3['id']] = $contact_id;

                foreach ($notifications as $notification) {
                    DB::insert('plugin_contacts_communications')
                        ->values(
                            array(
                                'contact_id' => $contact_id,
                                'type_id' => $notification['notification_id'],
                                'value' => $notification['value'],
                                'deleted' => 0
                            )
                        )->execute();
                }

                foreach ($user_permissions as $user_permission) {
                    DB::insert('plugin_contacts_users_has_permission')
                        ->values(
                            array('user_id' => $user_permission['user_id'], 'contact_id' => $contact_id)
                        )
                        ->execute();
                }

                foreach ($tables_to_update_ids_new as $table => $column) {
                    DB::update($table)
                        ->set(array($column => $contact_id))
                        ->where($column, '=', '-' . $contact3['id'])
                        ->execute();
                }

                DB::update('plugin_ib_educate_bookings')
                    ->set(array('bill_payer' => $contact_id))
                    ->and_where('bill_payer', '=', '-' . $contact3['id'])
                    ->execute();

                DB::update('plugin_messaging_message_targets')
                    ->set(array('target' => $contact_id))
                    ->where('target_type', '=', 'CMS_CONTACT3')
                    ->and_where('target', '=', '-' . $contact3['id'])
                    ->execute();

                DB::update('plugin_contacts3_notes')
                    ->set(array('link_id' => $contact_id))
                    ->where('table_link_id', '=', 1)
                    ->and_where('link_id', '=', '-' . $contact3['id'])
                    ->execute();

                DB::insert('plugin_contacts3_sync')
                    ->values(array('contact3_id' => $contact_id, 'contact_id' => $contact_id))
                    ->execute();
            }

            foreach ($contacts3 as $contact3) {
                if ($contact3['id'] != $contact3['primary_contact_id'] && $contact3['primary_contact_id'] > 0) {
                    DB::insert('plugin_contacts_has_relations')
                        ->values(
                            array(
                                'contact_1_id' => $contact3['id'],
                                'contact_2_id' => $contact3['primary_contact_id'],
                                'relation_id' => 1,
                            )
                        )
                        ->execute();
                }
            }
            $db->commit();
        } catch (Exception $exc) {
            $db->rollback();
            throw $exc;
        }

        $t2 = microtime(1);
        header('content-type: text/plain');
        echo "imported in " . ($t2 - $t1) . " seconds\n";
        echo $log;
        exit;
    }

    public function action_create_contacts_for_users()
    {
        $t1 = microtime(1);
        $db = Database::instance();
        $db->begin();

        $now = date::now();
        $log = '';
        header('content-type: text/plain');
        try {
            $users = DB::select('users.*')
                ->from(array(Model_Users::MAIN_TABLE, 'users'))
                ->join(array(Model_Contacts::TABLE_PERMISSION_LIMIT, 'xlink'), 'left')->on('users.id', '=', 'xlink.user_id')
                ->where('xlink.user_id', 'is', null)
                ->execute()
                ->as_array();


            foreach ($users as $user) {
                echo $user['id'] . ': ' . $user['name'] . ' ' . $user['surname'] . "\n";
                $contact = new Model_Contacts();
                $contact->set_first_name($user['name'] ?: ' ');
                $contact->set_last_name($user['surname'] ?: ' ');
                $contact->set_linked_user_id($user['id']);
                $contact->set_permissions(array($user['id']));
                $contact->set_mailing_list('default');
                $contact->set_email($user['email']);
                $contact->save();

                if (Model_Plugin::is_enabled_for_role('Administrator', 'Families')) {
                    $family_id = Model_Families::set_family($contact->get_id(), $user['surname'] ?: $user['email'], 1, 0);
                }

            }
            $db->commit();
            echo 'Done';
        } catch (Exception $exc) {
            echo 'Error: ' . $exc->getMessage();
            $db->rollback();
        }
        $t2 = microtime(1);
        echo sprintf("\n%.5f in seconds", $t2 - $t1);
        exit;
    }

    public function action_bulk_transfer_delete()
    {
        $post = $this->request->post();
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        Model_Contacts::bulk_transfer_delete($post['contact']);
        echo json_encode($post);
    }
}
