<?php defined('SYSPATH') or die('No Direct Script Access.');

class Controller_Admin_Homework extends Controller_Cms
{
	function before()
	{
		parent::before();

		// Menu items
		$this->template->sidebar              = View::factory('sidebar');
		$this->template->sidebar->menus       = array(array(
			array('icon' => 'homework', 'name' => 'Homework', 'link' => '/admin/homework')
		));
		$this->template->sidebar->breadcrumbs = array(
			array('name' => 'Home', 'link' => '/admin'),
			array('name' => 'Homework', 'link' => '/admin/homework')
		);

		switch ($this->request->action())
		{
			case 'index':
			case 'add':
			case 'edit':
				if (Auth::instance()->has_access('homework_edit') || Auth::instance()->has_access('homework_edit_limited'))
				{
					$this->template->sidebar->tools = '<a href="/admin/homework/edit/new"><button type="button" class="btn">Add Homework</button></a>';
				}
				break;
		}

		$this->template->scripts[] = '<script src="/engine/shared/js/bootstrap-toggle/bootstrap-toggle.min.js"></script>';
		$this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validate.min.js"></script>';
	}

	/**
	 * Entry point.
	 */
	public function action_index()
	{
		$this->action_list();
	}

    public function action_list()
    {
        /*
         * Check permissions
         *
         * There are two parts to this screen; list of all homework -and- list of your family's homework
         * You need permission to view at least one of them
         */
        $user = Auth::instance()->get_user();
        $linked_to_contact = false;
        if (Model_Plugin::is_enabled_for_role('Administrator', 'contacts3')) {
            $contacts   = Model_Contacts3::get_contact_ids_by_user($user['id']);
            if (isset($contacts[0]) && !empty($contacts[0]['id'])) {
                $linked_to_contact = true;
            }
        }

        $access_all_homework    = Auth::instance()->has_access('homework_index');
        $access_family_homework = $linked_to_contact && ($access_all_homework || Auth::instance()->has_access('homework_index_limited'));

        if ( ! $access_all_homework && ! $access_family_homework) {
            IbHelpers::set_message(
                'To use this feature, you must have access to the &quot;homework_index&quot; permission or be linked to a contact and have the &quot;homework_index_limited&quot; permission',
                'warning popup_box'
            );
            $this->request->redirect('/admin');
        }

        /*
         * Get data
         *
         * Just get the list of family homework here.
         * The list of *all* homework is loaded via an AJAX call later.
         */
        $contacts = array();
        $homework = array();
        if ($access_family_homework) {
            // Get the contact IDS of either the current user or their entire family, depending on permissions
            if (!Auth::instance()->has_access('contacts3_limited_family_access')) {
                $contacts = Model_Contacts3::get_contact_ids_by_user($user['id']);
            } else {
                $contacts = Model_Contacts3::get_all_family_members_for_guardian_by_user($user['id']);
            }

            $contact_ids = array();
            foreach ($contacts as $key => $contact) {
                $contact_ids[] = $contact['id'];
                $contacts[$key]['object'] = new Model_Contacts3($contact['id']);
            }

            // Get the homework for all relevant contacts
            $args = ['limit' => 100, 'query_only' => true];
            foreach ($contact_ids as $contact_id) {
                $filters = ['published_only' => true, 'contact_ids' => [$contact_id]];

                $homework[$contact_id] = Model_Homework::search_for_datatable($filters, $args)->execute()->as_array();
                foreach ($homework[$contact_id] as $i => $homework_item) {
                    $homework[$contact_id][$i]['files'] = DB::select('files.*', 'has.*', DB::expr("CONCAT_WS(' ', users.name, users.surname) AS author"))
                        ->from(array(Model_Homework::TABLE_FILES, 'has'))
                        ->join(array(Model_Files::TABLE_FILE, 'files'))->on('has.file_id', '=', 'files.id')
                        ->join(array('engine_users', 'users'))->on('files.created_by', '=', 'users.id')
                        ->where('files.deleted', '=', 0)
                        ->and_where('has.homework_id', '=', $homework_item['id'])
                        ->execute()
                        ->as_array();

                    $schedule = Model_Schedules::get_schedule($homework_item['schedule_id']);
                    $course = Model_Courses::get_course($schedule['course_id']);
                    $homework[$contact_id][$i]['course'] = $course['title'];
                }
            }
        }

        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('homework').'js/homework.js"></script>';
        $this->template->body      = View::factory('homeworks_list')->set(array(
            'access_all_homework'    => $access_all_homework,
            'access_family_homework' => $access_family_homework,
            'contacts'               => $contacts,
            'family_homework'        => $homework
        ));
    }

    public function action_homeworks_list_data()
    {
        $this->auto_render = FALSE;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        $check_permission_user_id = NULL;

        if (!Auth::instance()->has_access('homework_index')) {
            $user                     = Auth::instance()->get_user();
            $check_permission_user_id = $user['id'];
        }

        $filters = [
            'term'    => $this->request->query('sSearch'),
            'user_id' => $check_permission_user_id
        ];

        $args = [
            'limit'     => $this->request->query('iDisplayLength'),
            'offset'    => $this->request->query('iDisplayStart'),
            'sort_by'   => $this->request->query('iSortCol_0'),
            'direction' => $this->request->query('iSortDir_0')
        ];

        $data   = Model_Homework::search_for_datatable($filters, $args);
        echo json_encode($data);
    }

	public function action_edit()
	{
		$this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('homework').'js/homework.js"></script>';
		$id                        = $this->request->param('id');
		if ($id)
		{
			$homework = Model_Homework::get($id);
			if ($homework['deleted'])
				$this->request->redirect('/admin/homework/');
		}
		else
		{
			$homework = array('id' => 'New', 'title' => '', 'description' => '', 'schedule' => '', 'files' => array());
		}
		$post = $this->request->post();

		if (isset($post['deleted']))
			$deleted = $post['deleted'];
		else
			$deleted = 0;

		if (isset($post['title']))
		{
			$id = Model_Homework::save_homework(
				$id,
				$post['schedule_event_id'],
				$post['title'],
				$post['description'],
				@$post['published'],
				$deleted,
				isset($post['has_file_id']) ? $post['has_file_id'] : array()
			);
			if (@$_POST['action'] == 'save_and_exit') {
				$this->request->redirect('/admin/homework');
			} else {
				$this->request->redirect('/admin/homework/edit/' . $id);
			}
		}
		$events = array();
		if ($homework['course_schedule_event_id'])
		{
			$events = Model_courses::autocomplete_search_schedule_events($homework['schedule_id'], $homework['course_schedule_event_id']);
		}

		$this->template->body           = View::factory('homework_edit');
		$this->template->body->homework = $homework;
		$this->template->body->events   = $events;
	}

    public function action_view()
    {
        $id = $this->request->param('id');
        $homework = null;
        if ($id) {
            $homework = Model_Homework::get($id);
            $schedule = Model_Schedules::get_schedule($homework['schedule_id']);
            $course = Model_Courses::get_course($schedule['course_id']);
            $homework['course'] = $course['title'];
        }

        $events = array();
        if ($homework['course_schedule_event_id']) {
            $events = Model_Courses::autocomplete_search_schedule_events($homework['schedule_id'], $homework['course_schedule_event_id']);
        }

        $this->template->body           = View::factory('homework_view');
        $this->template->body->homework = $homework;
        $this->template->body->events   = $events;
    }

	public function action_delete()
	{
        if (!Auth::instance()->has_access('homework_delete')) {
            IbHelpers::set_message('You need access to the &quot;homework_delete&quot; permission to perform this action');
            $this->request->redirect('/admin');
        }

		$id       = $this->request->post('id');
		$homework = new Model_Homework($id);
		$result   = FALSE;

		if ($homework->id) // check that the homework item exists, before saving it
		{
			$homework->set('deleted', 1);
			$result = $homework->save_with_moddate();
		}

		if ($result)
		{
			IbHelpers::set_message('Homework item #'.$id.': &quot;'.$homework->title.'&quot; deleted', 'success popup_box');
		}
		else
		{
			IbHelpers::set_message('Error deleting homework item #'.$id, 'danger popup_box');
		}
		$this->request->redirect('/admin/homework');

	}

	public function action_upload()
	{
		$this->auto_render = FALSE;
		$this->response->headers('Content-type', 'application/json; charset=utf-8');

		$dirId              = Model_Files::get_directory_id('/homeworks');
		$response           = array();
		$response['errors'] = array();
		$response['files']  = array();
		$response['files']  = array();
		foreach ($_FILES as $file)
		{
			$data                = array();
			$data['name']        = $file['name'];
			$data['file_id']     = Model_Files::create_file($dirId, $file['name'], $file);
			$response['files'][] = $data;
		}
		echo json_encode($response);
	}

	public function action_ajax_homework_delete()
	{
        $this->auto_render = FALSE;

        if (!Auth::instance()->has_access('homework_delete')) {
            IbHelpers::set_message('You need access to the &quot;homework_delete&quot; permission to perform this action');
            return false;
        }

		$params            = $this->request->param();

		$homework = Model_Homework::get($params['id']);

		if ($homework)
		{
			Model_Homework::save_homework(
				$homework['id'],
				$homework['course_schedule_event_id'],
				$homework['title'],
				$homework['description'],
				$homework['published'],
				1,
				$homework['files']);

			$result['status'] = 'success';
		}
		else
		{
			$result['status'] = 'failed';
		}

		return json_encode($result);
	}
	
	public function action_listing()
	{
		$this->template->body = View::factory('homework_view_listing');
	}
	
}
