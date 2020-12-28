<?php
defined('SYSPATH') OR die('No Direct Script Access');

class Controller_Admin_Families extends Controller_Cms
{

    function before()
    {
        parent::before();

        $this->template->styles[URL::get_engine_plugin_assets_base('contacts2').'css/contacts.css'] = 'screen';

        // Menu items
        $this->template->sidebar = View::factory('sidebar');
        $this->template->sidebar->breadcrumbs = array(
            array('name' => 'Home',     'link' => '/admin'),
            array('name' => 'Contacts', 'link' => '/admin/contacts2')
        );
        $menus = array (
            array('icon' => 'contacts', 'name' => 'Contacts', 'link' => '/admin/contacts2'),
            array('icon' => 'family', 'name' => 'Mailing Lists', 'link' => '/admin/contacts2/mailing_lists')
        );
        $extensions = Model_Contacts::getExtentions();
        foreach ($extensions as $extension) {
            $menus = $extension->menus($menus);
        }
        $this->template->sidebar->menus = array($menus);
    }

    public function action_index()
    {
        if (!Auth::instance()->has_access('contacts2_index') && !Auth::instance()->has_access('contacts2_index_limited')) {
            IbHelpers::set_message("You don't have permission!", 'warning popup_box');
            $this->request->redirect('/admin');
        } else {
            // Assets
            $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('contacts2') . 'js/contacts.js"></script>';
            $extensions = Model_Contacts::getExtentions();
            foreach ($extensions as $extension) {
                foreach ($extension->required_js() as $required_js) {
                    $this->template->scripts[] = '<script src="' . $required_js . '"></script>';
                }
            }

            // Select the view
            $this->template->body = View::factory('list_families');

            $check_permission_user_id = null;
            if (!Auth::instance()->has_access('contacts2_index')) {
                $user = Auth::instance()->get_user();
                $check_permission_user_id = $user['id'];
            }

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

            $result = Model_Families::get_datatable($params);
        }
        echo json_encode($result);
    }

    public function action_save()
    {
        $post = $this->request->post();
        $family_id = Model_Families::set_family(
            $post['family_id'],
            $post['family_name'],
            1,
            0,
            $post['primary_contact_id']
        );

        $this->request->redirect('/admin/contacts2/edit/?family_id=' . $family_id);
    }

    public function action_autocomplete()
    {
        $term = $this->request->query('term');
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json');
        $data = Model_Families::autocomplete($term);
        echo json_encode($data);
    }

	/*
	 * This is copied from Kilmartin.
	 * It will only return a static view. It needs to be updated to display actual data
	 */
	public function action_ajax_get_family_accounts()
	{
		$this->auto_render = FALSE;
		$family_id         = isset($_GET['family_id']) ? $_GET['family_id'] : 0;
		// $transactions      = Model_Kes_Transaction::get_contact_transactions(NULL,$family_id);

		$transactions      = array(
			array(
				'id'             => '1010',
				'outstanding'    => '10',
				'modified_by_id' => '',
				'multiple'       => '0',
				'booking_id'     => '700',
				'first_name'     => 'Forename',
				'last_name'      => 'Surname',
				'schedule'       => 'Schedule',
				'type'           => 'Type',
				'total'          => '400',
				'status_label'   => 'Status',
				'updated'        => date('Y-m-d H:i:s')
			),
			array(
				'id'             => '1008',
				'outstanding'    => '0',
				'modified_by_id' => '',
				'multiple'       => '0',
				'booking_id'     => '697',
				'first_name'     => 'Forename',
				'last_name'      => 'Surname',
				'schedule'       => 'Schedule',
				'type'           => 'Type',
				'total'          => '110',
				'status_label'   => 'Status',
				'updated'        => date('Y-m-d H:i:s', strtotime('-2 weeks'))
			)
		);
		$view = View::factory('list_accounts_transactions')->set('transactions', $transactions);
		$this->response->body($view);
	}

	public function action_ajax_get_family_member_accounts()
	{
		$this->auto_render = FALSE;
		$contact_id        = isset($_GET['contact_id']) ? $_GET['contact_id'] : 0;
		// $transactions      = Model_Kes_Transaction::get_contact_transactions($contact_id, NULL);

		$transactions      = array(
			array(
				'id'             => '1210',
				'outstanding'    => '10',
				'modified_by_id' => '',
				'multiple'       => '0',
				'booking_id'     => '700',
				'first_name'     => 'Forename',
				'last_name'      => 'Surname',
				'schedule'       => 'Schedule',
				'type'           => 'Type',
				'total'          => '400',
				'status_label'   => 'Status',
				'updated'        => date('Y-m-d H:i:s')
			),
			array(
				'id'             => '1108',
				'outstanding'    => '0',
				'modified_by_id' => '',
				'multiple'       => '0',
				'booking_id'     => '697',
				'first_name'     => 'Forename',
				'last_name'      => 'Surname',
				'schedule'       => 'Schedule',
				'type'           => 'Type',
				'total'          => '110',
				'status_label'   => 'Status',
				'updated'        => date('Y-m-d H:i:s', strtotime('-2 weeks'))
			)
		);

		$view = View::factory('list_accounts_transactions')->set('transactions', $transactions);
		$this->response->body($view);
	}
}
