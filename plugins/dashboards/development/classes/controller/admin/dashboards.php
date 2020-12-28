<?php
defined('SYSPATH') OR die('No Direct Script Access');

class Controller_Admin_Dashboards extends Controller_Cms
{
	function before()
	{
		parent::before();

		$this->template->sidebar = View::factory('sidebar');
		if (Auth::instance()->has_access('edit_all_dashboards')) {
			$this->template->sidebar->menus = array(
					array(
							array('icon' => 'dashboard', 'name' => 'Dashboards', 'link' => '/admin/dashboards')
					)
			);
			$this->template->sidebar->breadcrumbs = array(
					array('name' => 'Home', 'link' => '/admin'),
					array('name' => 'Dashboards', 'link' => '/admin/dashboards')
			);
		} else {
			if ($this->request->action() != 'view_dashboard') {
				IbHelpers::set_message("You don't have permission!", 'warning popup_box');
				$this->request->redirect('/admin');
			}
			$this->template->sidebar->menus = array();
			$this->template->sidebar->breadcrumbs = array(
				array('name' => 'Home', 'link' => '/admin'),
			);
		}
	}

	public function action_index()
	{
		if (!Auth::instance()->has_access('edit_all_dashboards')) {
			$error_id = Model_Errorlog::save(null, 'SECURITY');
			IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
			$this->request->redirect('/admin');
		}
        $this->template->sidebar->tools = '<a href="/admin/dashboards/add_edit_dashboard" class="btn btn-primary">Add dashboard</a>';
		$this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('dashboards').'js/list_dashboards.js"></script>';
		$this->template->body      = View::factory('dashboards_list');
	}

	// Form for editing dashboards
	public function action_add_edit_dashboard()
	{
		if (!Auth::instance()->has_access('edit_all_dashboards')) {
			$error_id = Model_Errorlog::save(null, 'SECURITY');
			IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
			$this->request->redirect('/admin');
		}

		$this->template->scripts[] = '<script type="text/javascript" src="/engine/shared/js/jquery.validationEngine2.js"></script>';
		$this->template->scripts[] = '<script type="text/javascript" src="/engine/shared/js/jquery.validationEngine2-en.js"></script>';
		$user                      = Auth::instance()->get_user();

		// Load dashboard data
		$id                        = $this->request->param('id');
		$dashboard                 = ORM::factory('Dashboard', $id);

		// Get an array of IDs of user groups that the dashboard has been shared with
		$shared_with_groups        = array();
		foreach ($dashboard->sharing->find_all() as $sharing)
		{
			$shared_with_groups[] = $sharing->group_id;
		}

		// Check if the dashboard has been shared with the user
		if (count($shared_with_groups) != 0 AND ! in_array($user['role_id'], $shared_with_groups))
		{
			IbHelpers::set_message('Dashboard #'.$id.' has not been shared with you', 'warning popup_box');
			$this->request->redirect('/admin/dashboards');
		}

		// Check if the user has permission to edit the dashboard
		if ( ! $dashboard->user_has_edit_permission())
		{
			IbHelpers::set_message('You do not have permission to edit other people&#39;s dashboards.', 'warning popup_box');
			$this->request->redirect('/admin/dashboards');
		}

		// Array of all user groups (needed for the dropdown)
		$role_model                = new Model_Roles;
		$roles                     = $role_model->get_all_roles();

		// Is the dashboard one of the user's favourites
		$favorite                  = ORM::factory('Dashboards_Favorite')->where('dashboard_id', '=', $id)->where('user_id', '=', $user['id'])->find_all();
		$is_favorite               = (count($favorite) > 0);


		// Details for the preview tab
		$preview_tab_content = '';
		if ($dashboard->id)
		{
			$session = Session::instance();
			$session->set('dashboard-from',       isset($_GET['dashboard-from'])       ? $_GET['dashboard-from']       : date('Y-m-d', strtotime(date('Y-m-d').' -1 year + 1 day')));
			$session->set('dashboard-to',         isset($_GET['dashboard-to'])         ? $_GET['dashboard-to']         : date('Y-m-d'));
			$session->set('dashboard-range_type', isset($_GET['dashboard-range_type']) ? $_GET['dashboard-range_type'] : 'Year');

			$this->template->styles    = array_merge($this->template->styles, array(
				URL::get_engine_assets_base().'css/bootstrap.daterangepicker.min.css' => 'screen',
				URL::get_engine_plugin_assets_base('reports').'css/reports.css' => 'screen',
				URL::get_engine_plugin_assets_base('dashboards').'css/dashboards_view.css' => 'screen',
				'/engine/shared/css/validation.css' => 'screen'
			));

			$this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/moment.min.js"></script>';
			$this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/bootstrap.daterangepicker.min.js"></script>';
			$this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('dashboards').'js/view_dashboards.js"></script>';
			$this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('reports').'js/sparkline.js"></script>';
		}

		// Render the view
		$view                      = View::factory('dashboards_edit')
			->set('dashboard', $dashboard)
			->set('is_favorite', $is_favorite)
			->set('roles', $roles)
			->set('shared_with_groups', $shared_with_groups);

		$this->template->sidebar->tools = View::factory('dashboards_edit_actions')->set('id', $id);
		$this->template->body           = $view;
	}

	// Screen where the dashboard can be viewed and widgets/sparklines can be added
	public function action_view_dashboard()
	{
		$session = Session::instance();
		$session->set('dashboard-from',       isset($_GET['dashboard-from'])       ? $_GET['dashboard-from']       : date('Y-m-d', strtotime(date('Y-m-d').' -1 year + 1 day')));
		$session->set('dashboard-to',         isset($_GET['dashboard-to'])         ? $_GET['dashboard-to']         : date('Y-m-d'));
		$session->set('dashboard-range_type', isset($_GET['dashboard-range_type']) ? $_GET['dashboard-range_type'] : 'Year');

		$dashboard = new Model_Dashboard($this->request->param('id'));

		// If the dashboard has not been shared with the user, redirect and display error
		if ( ! $dashboard->shared_with_user())
		{
			IbHelpers::set_message('Dashboard #'.$dashboard->id.' has not been shared with you', 'warning popup_box');
			$this->request->redirect('/admin/dashboards');
		}

        $this->template->header->no_plugin_tools = !$dashboard->user_has_edit_permission();

        $this->template->styles[URL::get_engine_assets_base().'css/bootstrap.daterangepicker.min.css'] = 'screen';
        $this->template->styles[URL::get_engine_plugin_assets_base('reports').'css/reports.css'] = 'screen';
        $this->template->styles[URL::get_engine_plugin_assets_base('dashboards').'css/dashboards_view.css'] = 'screen';
		$this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/moment.min.js"></script>';
		$this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/bootstrap.daterangepicker.min.js"></script>';
		$this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('dashboards').'js/view_dashboards.js"></script>';
		$this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('reports').'js/sparkline.js"></script>';
		$this->template->body      = $dashboard->render();
	}

	public function action_save_dashboard()
	{
		if (!Auth::instance()->has_access('edit_all_dashboards')) {
			$error_id = Model_Errorlog::save(null, 'SECURITY');
			IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
			$this->request->redirect('/admin');
		}
		$post      = $this->request->post();
		$dashboard = ORM::factory('Dashboard', $this->request->post('id'));
		$dashboard->values($post);

		// Save and set message
		if ($dashboard->save_data($post))
		{
			IbHelpers::set_message('Dashboard #'.$dashboard->id.': <strong>'.$dashboard->title.'</strong> successfully saved.', 'success popup_box');
		}
		else
		{
			IbHelpers::set_message('Error saving dashboard', 'error popup_box');
		}

		/* Redirect, depending on what button was clicked */
		if ($dashboard->id AND (isset($post['save']) AND $post['save'] == 'save_and_exit'))
		{
			// Clicked "Save and Exit" -> return to the listing
			$this->request->redirect('/admin/dashboards');
		}
		elseif ($dashboard->id AND (isset($post['save']) AND $post['save'] == 'save_and_view'))
		{
			// Clicked "Save and View" -> open the dashboard
			$this->request->redirect('/admin/dashboards/view_dashboard/'.$dashboard->id);
		}
		else
		{
			// Clicked "Save" -> reload the form
			$this->request->redirect('/admin/dashboards/add_edit_dashboard/'.$dashboard->id);
		}
	}

	// Delete function, called when the delete button on the edit screen is clicked
	public function action_delete_dashboard()
	{
		if (!Auth::instance()->has_access('edit_all_dashboards')) {
			$error_id = Model_Errorlog::save(null, 'SECURITY');
			IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
			$this->request->redirect('/admin');
		}
		$dashboard = ORM::factory('Dashboard', $this->request->param('id'));
		$dashboard->set('deleted', 1);
		$dashboard->set('date_modified', date('Y-m-d H:i:s'));

		if ( ! $dashboard->user_has_edit_permission())
		{
			IbHelpers::set_message('You do not have permission to edit other people&#39;s dashboards.', 'warning popup_box');
			$this->request->redirect('/admin/dashboards');
		}
		// Save and set message
		if ($dashboard->save())
		{
			IbHelpers::set_message('Dashboard #'.$dashboard->id.': <strong>'.$dashboard->title.'</strong> successfully deleted.', 'success popup_box');
		}
		else
		{
			IbHelpers::set_message('Error deleting dashboard', 'error popup_box');
		}

		// Return to the listing
		$this->request->redirect('/admin/dashboards');
	}

	/* Get data for the datatables serverside */
	public function action_ajax_get_datatable()
	{
		if (!Auth::instance()->has_access('edit_all_dashboards')) {
			$error_id = Model_Errorlog::save(null, 'SECURITY');
			IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
			$this->request->redirect('/admin');
		}
		$this->auto_render = FALSE;
		$this->response->body(Model_Dashboard::get_for_datatable($this->request->query()));
	}

	// AJAX deletion of a dashboard. Called when a delete icon on the dashboard list is clicked
	public function action_ajax_delete_dashboard()
	{
		if (!Auth::instance()->has_access('edit_all_dashboards')) {
			$error_id = Model_Errorlog::save(null, 'SECURITY');
			IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
			$this->request->redirect('/admin');
		}
		$this->auto_render = FALSE;
		$id        = $this->request->param('id');
		$dashboard = ORM::factory('Dashboard', $id);

		if ( ! $dashboard->user_has_edit_permission())
		{
			$saved = false;
		}
		else
		{
			$dashboard->set('deleted', 1);
			$dashboard->set('date_modified', date('Y-m-d H:i:s'));
			$saved     = $dashboard->save();
		}

		echo $saved ? 1 : 0;
	}

	// AJAX toggle of favourite status
	public function action_ajax_toggle_favorite()
	{
		$this->auto_render   = FALSE;
		$data['is_favorite'] = $this->request->query('is_favorite');
		$dashboard           = ORM::factory('Dashboard', $this->request->param('id'));
		echo ($dashboard->save_data($data)) ? 1 : 0;
	}

	// AJAX function for generating sublist in the plugins' dropdown
	public function action_ajax_get_submenu($data_only = false)
	{
		$user            = Auth::instance()->get_user();
        $favorites       = ORM::factory('Dashboards_Favorite')->where('user_id', '=', $user['id'])->find_all();
        $return['link']  = 'view_dashboard';
        $return['items'] = array();

        if (count($favorites)) {
            // If the user has favourites, show them in the submenu
            foreach ($favorites as $favorite) {
                $dashboard         = ORM::factory('Dashboard', $favorite->dashboard_id);
                $return['items'][] = array('id' => $dashboard->id, 'title' => $dashboard->title);
            }
        } else {
            // Otherwise show the first ten dashboards that they have access to
            $dashboards = Model_Dashboard::get_user_accessible(true);
            for ($i = 0; $i < count($dashboards) && $i < 10; $i++) {
                $return['items'][] = array('id' => $dashboards[$i]['id'], 'title' => $dashboards[$i]['title']);
            }
        }

        if ($data_only) {
            return $return;
        } else {
            $this->auto_render = false;
            $this->response->body(json_encode($return));
        }
	}

	// Save a sparkline to a dashboard (incomplete)
	public function action_ajax_add_sparkline()
	{
		$this->auto_render = FALSE;
		$post = $this->request->post();

		$dashboard = new Model_Dashboard($post['dashboard_id']);
		if ( ! $dashboard->user_has_edit_permission())
		{
			echo '';
		}
		else
		{
			$gadget_type = ORM::factory('Dashboards_Gadgettype')->where('stub', '=', 'sparkline')->find();

			$gadget = new Model_Dashboards_Gadget;
			$gadget->set('dashboard_id', $post['dashboard_id']);
			$gadget->set('gadget_id',    $post['report_id']);
			$gadget->set('column',       $post['column']);
			$gadget->set('type_id',      $gadget_type->id);
			$gadget->save();

			echo $gadget->id;
		}
	}

	// Save a report widget to a dashboard
	public function action_ajax_add_report_widget()
	{
		$this->auto_render = FALSE;
		$post              = $this->request->post();

		$dashboard = new Model_Dashboard($post['dashboard_id']);
		if ( ! $dashboard->user_has_edit_permission())
		{
			echo '';
		}
		else
		{
			// Need this to get the ID for the "widget" gadget type
			$gadget_type       = ORM::factory('Dashboards_Gadgettype')->where('stub', '=', 'widget')->find();

			// Save the widget to the dashboard gadgets
			$gadget = ORM::factory('Dashboards_Gadget');
			$gadget->set('dashboard_id', $post['dashboard_id']);
			$gadget->set('gadget_id',    $post['report_id']);
			$gadget->set('column',       $post['column']);
			$gadget->set('type_id',      $gadget_type->id);
			$gadget->save();

			// Return the widget HTML
			$report      = new Model_Reports($post['report_id']);
			$report->get(TRUE);
			$report->get_widget(TRUE);
			$widget_html = preg_replace('/data-per_row=".*?"/i', '$1', $report->render_widget());
			$widget_html = '<li data-id="'.$gadget->id.'">'.$widget_html.'</li>';
			echo $widget_html;
		}
	}

	// Save the order and column index of gadgets, after they have been dragged around
	public function action_ajax_save_gadget_order()
	{
		$this->auto_render = FALSE;
		$gadgets = $this->request->post('gadgets');
		$gadgets = json_decode($gadgets);

		if (isset($gadget[0]))
		{
			$dashboard = new Model_Dashboard($gadget[0]->dashboard_id);
			if ( ! $dashboard->user_has_edit_permission())
			{
				return false;
			}
		}
		foreach ($gadgets as $data)
		{
			$gadget = ORM::factory('Dashboards_Gadget', $data->id);

			$gadget->set('column', $data->column);
			$gadget->set('order', $data->order);
			$gadget->save();
		}
	}

	// Remove a gadget (widget or sparkline) from the dashboard via AJAX
	public function action_ajax_remove_gadget()
	{
		$this->auto_render = FALSE;
		$gadget            = ORM::factory('Dashboards_Gadget', $this->request->param('id'));

		$dashboard = new Model_Dashboard($gadget->dashboard_id);
		if ( ! $dashboard->user_has_edit_permission())
		{
			$saved = false;
		}
		else
		{
			$gadget->set('deleted', 1);
			$saved = $gadget->save();
		}

		echo $saved ? 1 : 0;
	}
}
