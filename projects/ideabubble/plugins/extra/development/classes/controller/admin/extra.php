<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Controller_Admin_Extra extends Controller_Cms
{
    /**
     * Function to be executed before every action.
     */
    public function before()
    {
        parent::before();

        // Select the view
        $this->template->sidebar = View::factory('sidebar');

        // Fill the template
        $this->template->sidebar->menus = array
        (
            array
            (
                array( 'name' => 'Customers'  , 'link' => '/admin/extra/customers'),
                array( 'name' => 'Services'   , 'link' => '/admin/extra/services' ),
				array( 'name' => 'Invoices'   , 'link' => '/admin/extra/invoices' ),
                array( 'name' => 'Projects'   , 'link' => '/admin/extra/projects' ),
                array('name' => 'Sprints', 'link' => '/admin/extra/sprints2'),
            )
        );

        // Commons
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('products').'js/common.js"></script>';
    }

    public function action_index()
    {
        self::action_customers();
    }

    public function action_asset()
    {
        $ext = $this->request->param('ext');
        $file_path = PROJECTPATH.'plugins/extra/development/assets/'.$this->request->param('filepath').'.'.$ext;

        switch (strtolower($ext)) {
            case 'css' : $mime_type = 'text/css'; break;
            case 'js'  : $mime_type = 'application/javascript'; break;
            default    : $mime_type = mime_content_type($file_path); break;
        }

        $this->auto_render = false;
        $this->response->headers('Content-Type', $mime_type);
        echo file_get_contents($file_path);
    }

    public function action_services()
    {
        $model = new Model_Extra();
        $services = $model->get_service_data();

        $this->template->body = View::factory('admin/list_services');
        $this->template->body->services = $services;
    }

    public function action_add_service()
    {
        if (HTTP_Request::POST == $this->request->method()) {
            $this->action_save_service();
        }
        
        $model = new Model_Extra();
        $dropdowns = $model->get_dropdowns_data();

        $this->template->styles = array(URL::get_project_plugin_assets_base('extra').'css/services.css' => 'screen');
        $this->template->scripts[] = '<script src="' . URL::get_project_plugin_assets_base('extra').'js/services.js"></script>';
        $this->template->body = View::factory('admin/add_edit_service');
        $this->template->body->dropdowns = $dropdowns;
    }

    public function action_edit_service()
    {
        $id = $this->request->param('id');
        $model = new Model_Extra();
        $data = $model->get_service_data($id);
        $notes = $model->get_service_notes($data['id']);
        $dropdowns = $model->get_dropdowns_data();

        if (isset($data['url'])) {
            $data['whois_data'] = Kohana_Whois::instance()->lookup($data['url']);
        }

        $this->template->styles = array(URL::get_project_plugin_assets_base('extra').'css/services.css' => 'screen');
        $this->template->scripts[] = '<script src="' . URL::get_project_plugin_assets_base('extra').'js/services.js"></script>';
        $this->template->body = View::factory('admin/edit_service');
        $this->template->body->data = $data;
        $this->template->body->notes = $notes;
        $this->template->body->dropdowns = $dropdowns;
    }

    public function action_save_service()
    {
        $postData = $this->request->post();
        $model = new Model_Extra();

        $data = $model->get_update_data($postData);
        $dropdowns = $model->get_dropdowns_data();
        $data['company_id'] = $data['company_id'] == '' ? 0 : $data['company_id'];

        if ($model->validate_service($data))
        {
            if (!isset($postData['id']) OR $postData['id'] == '')
            {
                $new_id = $model->add_service($data);

                if ($postData['redirect'] == '')
                    $postData['redirect'] = 'admin/extra/edit_service/'.$new_id;
            }
            else
            {
                if(isset($data['service_new_host'])){
                    $new_host = $data['service_new_host'];
                   $model->edit_hosts($new_host);
                }else
                $model->edit_service($data);
            }
            $this->request->redirect($postData['redirect']);
        }
        else
        {
            if (!isset($postData['id']) OR $postData['id'] == '')
            {
                $this->template->body = View::factory('admin/add_edit_service');
                $this->template->body->data = $data;
                $this->template->body->dropdowns = $dropdowns;
            }
            else
            {
                $this->request->redirect('admin/extra/edit_service/'.$postData['id']);
            }
        }
    }

    public function action_delete_service()
    {
        $id     = $this->request->param('id');
        $model  = new Model_Extra();
        $result = $model->delete_service($id);

        if ($result > 0)
        {
            IbHelpers::set_message('Service #'.$id.' deleted', 'info');
        }
        else
        {
            IbHelpers::set_message('Deletion of service #'.$id.' failed', 'error');
        }

        $this->request->redirect(URL::site().'admin/extra/services');
    }

	public function action_invoices()
    {
		$date_from = $this->request->query('date_from');
		$date_to = $this->request->query('date_to');
        $invoices = Model_Extra::list_invoices_b(null, $date_from, $date_to);

        $this->template->body = View::factory('admin/list_invoices');
        $this->template->body->invoices = $invoices;
    }

    public function action_create_invoice()
    {
        $post = $this->request->post();
        $id = Model_Extra::create_invoice(
            $post['service_id'],
            Date::dmy_to_ymd($post['date_from']),
            Date::dmy_to_ymd($post['date_to']),
            Date::dmy_to_ymd($post['due_date']),
            $post['status'],
            $post['amount'],
            @$post['bullethq_save']
        );
        if ($id) {
            IbHelpers::set_message('Invoice has been created', 'info');
        } else {
            IbHelpers::set_message('Unable to create invoice', 'info');
        }
        $this->request->redirect('/admin/extra/invoices');
    }

    public function action_customers()
    {
        $customer = new Model_Customers();
        $results['customers'] = $customer->action_get_customers();
        $this->template->scripts[] = '<script src="' . URL::get_project_plugin_assets_base('extra').'js/list_customers.js"></script>';
        $this->template->body = View::factory('admin/list_customers',$results);
    }

    public function action_view_customer()
    {
        $id = $this->request->param('id');
        $customer = new Model_Customers($id);
        $contacts = Model_Contacts::get_all_contacts_selected($customer);
        $services = Model_Extra::get_service_data(NULL, $id);
        $billing_contacts = Model_Contacts::get_all_contacts_selected($customer,true);
		$cards = Model_Customers::get_cards($id);
        $this->template->scripts[] = '<script src="' . URL::get_project_plugin_assets_base('extra').'js/customers.js"></script>';
        $this->template->body = View::factory('admin/view_customer', array('customer' => $customer->load(),
																			'contacts' => $contacts,
																			'billing_contacts' => $billing_contacts,
																			'counties' => $customer->counties_of_ireland(), 
																			'services' => $services,
																			'cards' => $cards));
    }

    public function action_save_customer()
    {
        $post = $this->request->post();
        $customer = new Model_Customers($post['id'],$post);
        $customer->set($post);
		$customer_id = $customer->save();
        $this->request->redirect(URL::site().'admin/extra/view_customer/' . $customer_id);
    }

    public function action_ajax_get_customer_details()
    {
        $customer_model = new Model_Customers();
        $contact_model = new Model_Contacts();

        $id = $this->request->param('id');
        if($id == 0){
            $return['contact'] = 'No details';
            $return['billing_contact'] = 'No details';
        } else {
        $customer_details = $customer_model->get_customer_details($id);
        $contact['id'] = $customer_details['contact'];
             if($contact['id'] == ''){
                 $return['contact'] = 'No details';
                 $return['billing_contact'] = 'No details';
                } else {
             $billing_contact['id'] = $customer_details['billing_contact'];
             $return['contact'] = json_decode($contact_model->get_contact($contact));
             $return['billing_contact'] = json_decode($contact_model->get_contact($billing_contact));
             }
        }
        // Return
        $this->auto_render = FALSE;
        $this->response->body(json_encode ($return));
    }

    public function action_ajax_refresh_services()
    {
        $model = new Model_Extra();
        $services = $model->get_service_data();

        $i = 0;
        $return = array();
        foreach ($services as $service)
        {
            $data['id'] = $service['id'];
            $data['ip_address'] = gethostbyname($service['url']);

            if (!filter_var($data['ip_address'], FILTER_VALIDATE_IP)) {
                $data['ip_address'] = '';
            }

            $model->edit_service($data);

            $return[$i] = $data;
            $i++;
        }

        // Return
        $this->auto_render = FALSE;
        $this->response->body (json_encode ($return));
    }

    public function action_ajax_get_reminder_text()
    {
        $model = new Model_Extra();
        $serviceId = $this->request->post('serviceId');
        $service = $model->get_service_data($serviceId);

        $vat_rate = (float)Settings::instance()->get('vat_rate');
        $customer_model = new Model_Customers();
        $customer = $customer_model->get_customer_details($service['company_id']);

        $contact = new Model_Contacts($service['contact']);
        $contact = $contact->get_details();

        if($service['billing_contact']){
            $billing_contact = new Model_Contacts($service['billing_contact']);
            $billing_contact = $billing_contact->get_details();
        } else {
            $billing_contact = $contact;
        }

        $data = array();
        $data['contact_name'] = $billing_contact ? $billing_contact['first_name'] : $contact['first_name'];
        $data['company_title'] = $customer['company_title'];
        $data['service_name'] = $service['url'];
        $data['service_type'] = $service['service_type'];
        $data['subtotal'] = number_format($service['price'], 2);
        $data['vat'] = number_format($service['price'] - $service['price'] * (1 / (1 + $vat_rate)), 2);
        $data['price'] = $service['price'];
        $data['total'] = number_format($service['price'] * (1 / (1 + $vat_rate)), 2);
        $data['date_end'] = date('D jS M Y', strtotime($service['date_end']));
        $data['today'] = date('D jS M Y');
        $data['remind_date'] = date('D jS M Y', strtotime('-10 days', strtotime($service['date_end'])));
        $data['credit'] = 0;

        $messaging = new Model_Messaging();
        $return['email_text'] = $messaging->generate_template('service-expire-reminder', NULL, $data);
        $return['service_id'] = $serviceId;
        $return['to']         = $billing_contact['email'];

        // Return
        $this->auto_render = FALSE;
        $this->response->body(json_encode($return));
    }

    public function action_ajax_get_sprints2()
    {
        $post = $this->request->post();
        if (isset($post['sEcho'])) {
            $return['sEcho'] = $post['sEcho'];
        }
        $sort = 'id';
        if ($post['iSortCol_0'] == 0) {
            $sort = 'id';
        }
        if ($post['iSortCol_0'] == 1) {
            $sort = "customer";
        }
        if ($post['iSortCol_0'] == 2) {
            $sort = "sprint";
        }
        if ($post['iSortCol_0'] == 3) {
            $sort = "summary";
        }
        if ($post['iSortCol_0'] == 4) {
            $sort = "budget";
        }
        if ($post['iSortCol_0'] == 5) {
            $sort = "spent";
        }
        if ($post['iSortCol_0'] == 6) {
            $sort = "balance";
        }
        if ($post['iSortCol_0'] == 7) {
            $sort = "progress";
        }
        if ($post['iSortCol_0'] == 8) {
            $sort = "project_status_type_id";
        }
        if ($post['iSortCol_0'] == 11) {
            $sort = "last_synced";
        }
        $return['aaData'] = Model_Extra::get_sprints2($post['iDisplayLength'], $post['iDisplayStart'], $sort,
            $post['sSortDir_0'], $post['sSearch']);
        $return['iTotalDisplayRecords'] = "16";
        echo json_encode($return);
        exit;
    }

    public function action_ajax_send_reminder()
    {
        $to = $this->request->post('to');
        $message = $this->request->post('message');
        $serviceId = $this->request->post('serviceId');
        $subject = 'Service Expire Reminder';
        $from = 'accounts@ideabubble.ie';

        $recipients = array();
        $recipients[] = array('target' => $to, 'target_type' => 'EMAIL');

        Model_Messaging::get_recipient_providers();
        $messaging = new Model_Messaging();
        $mprovider = $messaging->get_active_provider('email');
        $messaging->send('email', $mprovider['provider'], $from, $recipients, $message, $subject);

        $note_data = array(
            'type'    => 1,
            'link_id' => $serviceId,
            'notes'   => 'Reminder email sent'
        );
        $model = new Model_Extra();
        $model->add_note($note_data);

        $return['success'] = true;
        $this->auto_render = FALSE;
        $this->response->body (json_encode($return));
    }

    public function action_ajax_load_notes_list()
    {
        $model = new Model_Extra();
        $id = $this->request->post('id');
        $notes = $model->get_service_notes($id);
        $this->template->body = View::factory('admin/list_notes', $notes);
        $this->template->body->notes = $notes;
    }

    public function action_ajax_add_service_note()
    {
        $id = $this->request->param('id');
        if (!isset($id)) {
            return false;
        }
        $note['service'] = Model_Extra::get_service_data($id);
        $this->template->body = View::factory('admin/list_notes_edit', $note);
    }

    public function action_ajax_edit_listed_note()
    {
        $id = $this->request->param('id');
        if ( ! isset($id)) {
            return false;
        }
        $model = new Model_Extra;
        $note = $model->get_notes($id);

        $this->template->body = View::factory('admin/list_notes_edit', $note);
        $this->template->body->note = $note;
    }

    function action_ajax_add_service_note_add()
    {
        $post_data = $this->request->post();

        if( ! isset($post_data) OR empty($post_data))
        {
            $this->template->body = '';
        }
        else
        {
            $model = new Model_Extra;
            $res = $model->add_note($post_data);
            $this->template->body = $res;
        }
    }

    function action_ajax_edit_listed_note_save()
    {
        $post_data = $this->request->post();

        if(!isset($post_data) OR empty($post_data))
        {
            $this->template->body = '';
        }
        else
        {
            $model = new Model_Extra();
            $id = $post_data['id'];
            $notes = $post_data['notes'];
            $res = $model->update_note($id, $notes);
            $this->template->body = $res;
        }
    }

    public function action_ajax_save_sprint2()
    {
        $post = $this->request->post();
        $data = array();
        if (isset($post['sprint_id'])) {
            $sprint_id = $post['sprint_id'];
        } else {
            return false;
        }
        if (isset($post['budget']) && is_numeric($post['budget'])) {
            $data['budget'] = $post['budget'];
            $sprint_data = Model_Extra::get_sprint2($sprint_id);
            $data['balance'] = $data['budget'] - $sprint_data['spent'];
        }
        if (isset($post['content'])) {
            $data['summary'] = $post['content'];
        }
        if (isset($post['progress']) && is_numeric($post['progress'])) {
            $data['progress'] = $post['progress'];
        }
        if (isset($post['sprint_status_id'])) {
            $data['project_status_type_id'] = $post['sprint_status_id'];
        }

        //If there is something going into the DB..., else there is not...
        if (count($data) > 0) {
            $query = Model_Extra::projects_save_sprint2($sprint_id, $data);
        }
    }

    public function action_date_service()
    {
        $model = new Model_Extra();
        $post =  $this->request->post();

        $from = @$post['date_from'];
        $to   = @$post['date_to'];

        $services = $model->get_sorted_service_data($from, $to);


        $this->template->body = View::factory('admin/list_services');
        $this->template->body->services = $services;
        $this->template->body->date_from = $from;
        $this->template->body->date_to = $to;
    }

    public function action_delete_customer()
    {
        $post = $this->request->post();
        $customer = new Model_Customers($post['id']);
        $customer->delete();
		exit();
    }

    public function action_check_email()
    {
        $this->auto_render = false;
        $post = $this->request->post();
        $ok = Model_Customers::check_email($post['email']);
        $this->response->body($ok);
    }

    public function action_bullet()
    {
        $action = $this->request->param('id');
        $id = $this->request->param('toggle');
        $bullet = new Model_BulletHQ();
        $data = array();

        switch($action)
        {
            case 'add_customer':
                $x = $bullet->add_client(array('name' => 'Client Name','email' => 'dale@ideabubble.ie','addressLine1' => 'Ideabubble Ltd','addressLine2' => 'John St.','phoneNumber' => '061513030'
                ,'vatNumber' => '1234643424','countryCode' => 'IE'));
                break;
            case 'delete_customer':
                $x = $bullet->delete_client($id);
                break;
            case 'update_customer':
                $x = $bullet->update_client($data,$id);
                break;
            default:
                break;
        }
    }
	
	public function action_email_invoice()
	{
		$bullethqb = new Model_BulletHQB();
		if($bullethqb->login()){
			$bullethqb->email_invoice($this->request->query('bullethq_invoice_id'));
		}
		$this->request->redirect('/admin/extra/invoices');
		exit();
	}
	
	public function action_projects()
	{
		$projects = Model_Extra::projects_list();

        $this->template->body = View::factory('admin/list_projects');
        $this->template->body->projects = $projects;
	}

    public function action_sprints()
    {
        $sprints = Model_Extra::sprints_list();

        $this->template->body = View::factory('admin/list_sprints');
        $this->template->body->sprints = $sprints;
    }
	
	public function action_projects_report()
	{
		$params = $this->request->query();
		if(!isset($params['group_by'])){
			$params['group_by'] = array('Project');
		}
		$projects = Model_Extra::projects_list();
		$months = Model_Extra::projects_report_month_list();
		$authors = Model_Extra::projects_report_author_list();
		$report = Model_Extra::projects_report2($params);

        $this->template->body = View::factory('admin/report_projects');
        $this->template->body->report = $report;
		$this->template->body->params = $params;
		$this->template->body->months = $months;
		$this->template->body->authors = $authors;
		$this->template->body->projects = $projects;
	}
	
	public function action_projects_sync_jira()
	{
		Model_Extra::projects_sync_jira();
		$this->request->redirect('/admin/extra/projects');
	}
	
	public function action_project_sync_jira()
	{
		set_time_limit(0);
		session_commit(); // can take a long time to complete so do not block session
		$project_id = $this->request->query('project_id');
		if($project_id == 'all'){
			$success = Model_Extra::projects_sync_jira_all();
            IbHelpers::set_message('All projects syncronized');
            $this->request->redirect('/admin/extra/projects');
		} else {
			$success = Model_Extra::project_sync_jira($project_id, true);
            IbHelpers::set_message('Project ' . $project_id . ($success ? '' : ' NOT ') . ' syncronized');
            $this->request->redirect('/admin/extra/project_view?id=' . $project_id);
		}
	}
	
	public function action_project_view()
	{
		$project_id = $this->request->query('id');
		$project = Model_Extra::project_details($project_id);
		$this->template->body = View::factory('admin/view_project');
        $this->template->body->project = $project;
	}
	
	public function action_rapid_views()
	{
		$rapid_views = Model_Extra::rapid_views_list();
		$this->template->body = View::factory('admin/list_rapid_views');
        $this->template->body->rapid_views = $rapid_views;
	}
	
	public function action_rapid_view()
	{
		$rapid_view_id = $this->request->query('id');
		$rapid_view = Model_Extra::rapid_view_details($rapid_view_id);
		$this->template->body = View::factory('admin/view_rapid_view');
        $this->template->body->rapid_view = $rapid_view;
	}
	
	public function action_sprint()
	{
		$sprint_id = $this->request->query('id');
		$sprint = Model_Extra::sprint_details($sprint_id);
		$this->template->body = View::factory('admin/view_sprint');
        $this->template->body->sprint = $sprint;
	}

    public function action_sync_sprint()
    {
        set_time_limit(0);
        session_commit(); // can take a long time to complete so do not block session
        $sprint_id = $this->request->query('id');
        $sprint = Model_Extra::sync_sprint($sprint_id);
        $this->request->redirect('/admin/extra/sprint?id=' . $sprint_id);
    }
	
	public function action_sprints_report()
	{
		$params = $this->request->query();
		if(!isset($params['group_by'])){
			$params['group_by'] = array('Sprint');
		}
		$sprints = Model_Extra::sprints_list();
		$months = Model_Extra::projects_report_month_list();
		$authors = Model_Extra::projects_report_author_list();
		$report = Model_Extra::sprints_report($params);

        $this->template->body = View::factory('admin/report_sprints');
        $this->template->body->report = $report;
		$this->template->body->params = $params;
		$this->template->body->months = $months;
		$this->template->body->authors = $authors;
		$this->template->body->sprints = $sprints;
	}
	
	public function action_rapid_views_sync_jira()
	{
        set_time_limit(0);
        session_commit();
		Model_Extra::projects_sync_sprints();
		$this->request->redirect('/admin/extra/rapid_views');
	}

    public function action_upwork_import()
    {
        $upworklog = null;
        if (isset($_FILES['upwork'])  && $_FILES['upwork']['error'] === 0) {
            //header('content-type: text/plain');print_r($_FILES);exit;
            $json = file_get_contents($_FILES['upwork']['tmp_name']);
            $data = json_decode($json);
            //header('content-type: text/plain');print_r($data);exit;
            $result = array();
            $header = array();
            foreach ($data->table->cols as $col) {
                //$header[] = $col->label;
            }
            $header = array(
                'worked_on',
                'task',
                'start',
                'duration',
                'durationh'
            );
            // $result[] = $header;
            $day = null;
            $time = 0;
            foreach ($data->table->rows as $row) {
                if ($row->c[0]->v != $day) {
                    $day = $row->c[0]->v;
                    $time = substr($day, 0, 4) . '-' . substr($day, 4, 2) . '-' . substr($day, 6, 2) . ' 08:00:00';
                    $time = strtotime($time);
                    //echo $time;exit;
                    $duration = $row->c[6]->v;
                }
                $rrow = array();
                foreach ($row->c as $col) {
                    //$rrow[] = $col->v;
                }
                $dhours = str_pad(floor((floatval($row->c[6]->v) * 60) / 60), 2, '0', STR_PAD_LEFT);
                //$dminutes = floatval($row->c[6]->v) - floor(floatval($row->c[6]->v)); //(floatval($row->c[6]->v) * 60) % 60; //0.333333
                $dminutes = $row->c[6]->v;
                if (strpos($dminutes, '.166667')) {
                    $dminutes = 10;
                } else if (strpos($dminutes, '.333333')) {
                    $dminutes = 20;
                } else if (strpos($dminutes, '.5')) {
                    $dminutes = 30;
                } else if (strpos($dminutes, '.666667')) {
                    $dminutes = 40;
                } else if (strpos($dminutes, '.833333')) {
                    $dminutes = 50;
                } else {
                    $dminutes = (floatval($row->c[6]->v) * 60) % 60;
                }
                $dminutes = str_pad($dminutes, 2, '0', STR_PAD_LEFT);

                /*switch ($dminutes) {
                    case '0.166667':
                        $dminutes = '10';
                        break;
                    case '0.333333':
                        $dminutes = '20';
                        break;
                    case '0.5':
                        $dminutes = '30';
                        break;
                    case '0.666667':
                        $dminutes = '40';
                        break;
                    case '0.833333':
                        $dminutes = '50';
                        break;
                    //$dminutes =
                }*/
                $rrow = array(
                    $row->c[0]->v,
                    $row->c[3]->v,
                    date('Y-m-d H:i:s', $time),
                    $row->c[6]->v,
                    //floor((floatval($row->c[6]->v) * 60) / 60) . ':' . (floatval($row->c[6]->v) * 60) % 60
                    $dhours . ':' . $dminutes . ':00'
                );
                $time += round(floatval($row->c[6]->v) * 3600);
                //echo date('Y-m-d H:i:s', $time) . "\n";
                $result[] = $rrow;
            }
            $upworklog = $result;
            //header('content-type: text/plain');print_r($result);exit;
            $tmp = tempnam('/tmp', 'upwork');
            //$csv = fopen('php://temp', 'w+');
            //$csv = tmpfile();
            /*$csv = fopen($tmp, 'w+');
            foreach ($result as $rows) {
                fputcsv($csv, $rows, ',', '"');
            }
            fseek($csv, 0, 0);
            fclose($csv);*/
            /*$rrow;
            $jira = new Model_JIRA();
            if (0)
            $worklog = $jira->add_worklog(
                $rrow[1],
                $rrow[2],
                round($rrow[3] * 3600),
                'upwork import test'
            );
            //$worklog = $jira->delete_worklog($rrow[1], 48752);
            $jira->delete_worklog($rrow[1], 48753);
            $jira->delete_worklog($rrow[1], 48755);
            $jira->delete_worklog($rrow[1], 48756);
            print_r($rrow);
            print_r($worklog);
            header('content-type: text/plain');
            readfile($tmp);exit;*/
        }
        $this->template->body = View::factory('admin/upwork_import', array('upworklog' => $upworklog));
    }

    public function action_sprints2()
    {
        $this->template->scripts[] = '<script src="' . URL::get_project_plugin_assets_base('extra') . 'js/list_sprints.js"></script>';
        $this->template->styles[URL::get_project_plugin_assets_base('extra') . "css/list_sprints.css"] = 'screen';
        $this->template->body = View::factory('admin/list_sprints2');
    }

    public function action_sync_sprints2()
    {
        Model_Extra::projects_sync_sprints2();
        $this->request->redirect('/admin/extra/sprints2');
    }

    public function action_sync_sprint2()
    {
        $jira_sprint_id = $this->request->query('jira_sprint_id');
        Model_Extra::projects_sync_individual_sprint2($jira_sprint_id);
        $this->request->redirect('/admin/extra/sprints2');
    }

    public function action_jira_worklog_add()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        $post = $this->request->post();
        $jira = new Model_JIRA();
        //if (0)
        if (@strtotime($post['duration'])) {
            $post['duration'] = round((strtotime($post['duration']) - strtotime($post['date'])) / 60) . "m";
        }

        $response = $jira->add_worklog(
            $post['key'],
            $post['date'],
            $post['duration'],
            $post['comment']
        );
        //$response = array('id' => mt_rand());
        echo json_encode($response);
    }
}