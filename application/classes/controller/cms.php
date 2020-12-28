<?php defined('SYSPATH') or die('No direct script access.');

class Controller_Cms extends Controller_Template
{
    protected $_crud_items = [];
    protected $_plugin = '';

    public $require_login = true;
    public $external_referer_check_skips = array(
        'https://connect.stripe.com/',
        'paypal.com'
    );
    public $no_session_close_ajax_actions = array();

	/**
	 * This is the main cms controller. All other cms controllers _Must_ extend this controller.
	 * Authentication is checked here to make sure the user is logged in.
	 * The templating variables are setup with lovely defaults that you shouldn't have to override,
	 * but if you do you can override everything in the
	 */
	function before()
	{
		parent::before();
		// Load user language for localization
		I18n::init_user_lang();
        $user = Auth::instance()->get_user();
        $session = Session::instance();

        /**
         * If this is an AJAX request, the session should be closed. Otherwise the next request will be queued.
         */
        $ajax = false;
        //close session to prevent locking
        if ($this->request->post('ajax') ||  $this->request->query('ajax') || @$_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
            if (!in_array($this->request->action(), $this->no_session_close_ajax_actions)){
                $auth_cfg = Kohana::$config->load('auth');
                if ($user) {
                    $user['idle_time'] = time() + $auth_cfg['idle_time'];
                    $session->set($auth_cfg['session_key'], $user);
                }
                session_write_close();
            }
            $ajax = true;
        }
		// Tell it what template view to use.
		$template = Settings::instance()->get('cms_template');
		$this->template = 'cms_templates/'.($template == '' ? 'default' : $template).'/template';
		$admin_action = stripos(get_class($this), '_Admin_') !== false;
        if ($admin_action) {
            if ($this->is_external_referer()){
                $referer_error = true;
                foreach ($this->external_referer_check_skips as $referer_check_skip) {
                    if (stripos($_SERVER['HTTP_REFERER'], $referer_check_skip) !== false) {
                        $referer_error = false;
                        break;
                    }
                }
                if ($referer_error) {
                    $error_id = Model_Errorlog::save(null, "SECURITY");
                    IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
                    $this->request->redirect('/admin/login/logout');
                }
            }
        }
		parent::before();

		// Check if user is logged in
		if (!Auth::instance()->logged_in() && $this->require_login) {
			if (@$_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
                echo $this->response->status(403)
                    ->send_headers()
                    ->body();

                // Stop execution
                exit;
			} else {
				// Put the redirect page in the session so when the user re-login it will redirect them back to this uri
				Session::instance()->set('login_redirect', $_SERVER['REQUEST_URI']);
				// Set a nice message
				IbHelpers::set_message(__('Your session has expired. You must re-enter your current username and password to log back in.'),
						'info popup_box');

				// Redirect to the login page
				$this->request->redirect('admin/login');
			}
		} else {

            $user = ORM::factory('users', $user['id']);

            // Store the user ID in a MySQL global variable
            DB::query('-1', 'SET @user_id='.($user->id ? $user->id : 'null'))->execute();

            // Check if the user has access to the dashboard
//            if ( Auth::instance()->check_for_super_level() == 'FALSE' AND Auth::instance()->check_permission_code('access_dashboard') == 'FALSE' )
//            {
//                $this->request->redirect('/');
//            }
//            else
//            {
            // Set the Global view variables
            // Whoo. Think twice before adding anything here or the rath of Diarmuid will befall you!
            $current_controller = $this->request->controller();
            View::bind_global('current_controller', $current_controller); // The current controller
            $current_action = $this->request->action();
            View::bind_global('current_action', $current_action); // The current action
            $current_id = $this->request->param('id');
            View::bind_global('current_id', $current_id); // The current id parameter
            $current_uri = $this->request->uri();
            View::bind_global('current_uri', $current_uri); // The current uri
            $assets_implemented = array('assets_implemented' => array('browser_sniffer' => false));
            View::bind_global('page_data', $assets_implemented);

            // Page title (this is by default the controller name) Can be changed in the controller
            $this->template->title = ucfirst($this->request->controller());

            // Load the header (menu) view.
            $template = Settings::instance()->get('cms_template');
            $action = $this->request->action();
            if($action == 'sms'){
                //$this->template->header = View::factory('cms_templates/' . ($template == '' ? 'default' : $template) . '/header_sms');
                $this->template->header = View::factory('../views/snippets/sms/header_sms');
            }else{
                $this->template->header = View::factory('cms_templates/' . ($template == '' ? 'default' : $template) . '/header');
            }

            // do not generate menu for ajax requests
            $this->template->header->menu = array();
            $this->template->header->notification_links = array();
            if ($ajax == false) {
                $this->template->header->menu = MenuArea::factory()->generate_links($this->request->controller());
                $this->template->header->notification_links = NotificationArea::factory()->generate_links($this->request->controller());
            }

            if (class_exists('Model_Dashboard'))
            {
                $this->template->header->available_dashboards = Model_Dashboard::get_user_accessible(TRUE);
            }

            $this->template->sidebar = NULL;
            if (!$this->request->post('ajax') &&  !$this->request->query('ajax') && @$_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {
                $this->template->latest_activity = Model_Activity::get_latest_activity();
            } else {
                $this->template->latest_activity = array();
            }

            // do not generate menu for ajax requests
            if ($template == 'modern') {
                if ($ajax == false) {
                    $this->template->sidebar_menu = array();
                    MenuArea::factory()->register_link('/profile/edit?section=contact', 'Profile', 'profile', null,
                        'avatar', 'profile');
                    $this->template->sidebar_menu = MenuArea::factory()->generate_icon_links();
                }
            }
            $this->template->user_details = $user;

            // Load the default view body
            $this->template->body = View::factory('body');
            $this->template->jira = new Model_JIRA();

            $this->template->scripts = array();
            $this->template->styles = array();

            if ($template == 'modern' AND Model_Plugin::is_enabled_for_role('Administrator', 'Insurance'))
            {
                // The "New Policy" button appears on all screens in the modern plugin. These assets are needed.
                $this->template->styles = array_merge($this->template->styles, array(URL::get_engine_assets_base().'js/jquery.multi-select/multi-select.css' => 'screen'));
                $this->template->styles = array_merge($this->template->styles, array(URL::get_project_plugin_assets_base('contacts') .'css/list_contacts.css' => 'screen'));
                $this->template->styles = array_merge($this->template->styles, array(URL::get_project_plugin_assets_base('accounts') .'css/list_accounts.css' => 'screen'));
                $this->template->styles = array_merge($this->template->styles, array(URL::get_project_plugin_assets_base('insurance').'css/claims.css' => 'screen'));
                $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.multi-select/jquery.multi-select.js"></script>';
                $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/list_activities.js"></script>';
                $this->template->scripts[] = '<script src="'.URL::get_project_plugin_assets_base('contacts') .'js/list_contacts.js"></script>';
                $this->template->scripts[] = '<script src="'.URL::get_project_plugin_assets_base('accounts') .'js/list_accounts.js"></script>';
                $this->template->scripts[] = '<script src="'.URL::get_project_plugin_assets_base('insurance').'js/policy.js"></script>';
                $this->template->scripts[] = '<script src="'.URL::get_project_plugin_assets_base('insurance').'js/claims.js"></script>';
            }

            if ($template == 'modern' AND Model_Plugin::is_enabled_for_role('Administrator', 'Messaging') AND Settings::instance()->get('messaging_popout_menu') == '1')
            {
                $styles = array(
                    URL::get_engine_plugin_assets_base('messaging').'css/message.css' => 'screen',
                    'https://cdn.jsdelivr.net/jquery.slick/1.6.0/slick.css' => 'screen',
                );
                $this->template->styles = array_merge($this->template->styles, $styles);
                $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('messaging').'js/slick.min.js"></script>';
                $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('messaging').'js/messaging.js"></script>';
                $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('messaging').'js/list_messages.js"></script>';
                $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_asset('messaging', 'js/messages.js', ['cachebust' => true]).'"></script>';
            }

            if ($template == 'modern' && Model_Plugin::get_isplugin_enabled_foruser($user->role_id, 'chat')) {
                $this->template->recent_conversations = Model_Chat::get_joined_rooms(
                    $user->id,
                    array('get_last_messages' => true, 'limit' => 5)
                );
            }

            // Load the Footer view here.
            $this->template->footer = View::factory('footer')->set('is_backend', true);
//            }

        }
	}

	function after()
	{
		if(Model_Cron::$cron_log_started){
			Model_Cron::complete_log();
		}
		$this->template->title = 'CourseCo :: ' . $this->template->title;


		// If we have template->body as view then assign alerts to it
		if ($this->template->body instanceof View) {

			// Load the messages from the IbHelper.
			$messages = IbHelpers::get_messages();

			// If there are messages
			if ($messages) {
				// Add the message to the alert string if it exists
				if (isset($this->template->body->alert)) {
					$this->template->body->alert = $this->template->body->alert . $messages;
				}
					// Else create an alert string
				else
				{
					$this->template->body->alert = $messages;
				}
			}

		}

        /**
         * If this is ajax request (marked by specific parameter) then we dont need to
         * render template and will return only rendered body
         */
        if ($this->request->post('ajax') ||  $this->request->query('ajax')) {
            // Do not render body if it is already rendered in sub-sub-request
            if ($this->template->body instanceof View) {
                $this->response->body($this->template->body->render());
            }
            else
            {
                $this->response->body($this->template->body);
            }
            return;
        }

		// If this is called as internal request then we do not want render template, but only body.
		// So it could be used as internal request. For example:
		//
		//    public function action_platform() {
		//          $this->template->body = Request::factory('admin/products/products_cat')->execute()->body();
		//    }

		if (!$this->request->is_initial() && $this->auto_render === TRUE) {
			// Do not render body if it is already rendered in sub-sub-request
			if ($this->template->body instanceof View) {
				$this->response->body($this->template->body->render());
			}
			else
			{
				$this->response->body($this->template->body);
			}
			return;
		}

        $breadcrumbs = $plugin_tools = NULL;
		if (isset($this->template->sidebar)) {
			// generate 2nd level menu from sidebar
            if (isset($this->template->sidebar->menus)) {
                MenuArea::factory()->register_sidmenu_as_sub_links($this->template->sidebar->menus);
            }

            if (isset($this->template->sidebar->breadcrumbs))
            {
                $breadcrumbs = $this->template->sidebar->breadcrumbs;
                MenuArea::factory()->register_breadcrumb_links($breadcrumbs);
                MenuArea::factory()->register_mobile_breadcrumb_links($breadcrumbs);
            }
            if (isset($this->template->sidebar->tools))
            {
                $plugin_tools = $this->template->sidebar->tools;
            }
		}
		$this->template->header->submenu_message      = MenuArea::factory()->get_sub_menu_links();
		$this->template->header->submenu      = MenuArea::factory()->generate_sub_menu_links($breadcrumbs);
        $this->template->header->breadcrumbs  = MenuArea::factory()->generate_breadcrumb_links();
        $this->template->header->mobile_breadcrumbs  = MenuArea::factory()->generate_mobile_breadcrumb_links();

        $this->template->header->plugin_tools = $plugin_tools;

        if (isset($this->template->header->no_plugin_tools) && $this->template->header->no_plugin_tools)
            $this->template->header->plugin_tools = null;

        $this->template->skip_comments_in_beginning_of_included_view_file = true; //Exclude comments in this file, prevent IE issues when the first line in the website is a comment

		parent::after();

	}

    /*
     * Generic controllers
     */
    public function action_ajax_get_item_datatable()
    {
        $this->auto_render = false;
        $type_stub = $this->request->param('id');
        $type      = $this->_crud_items[$type_stub];
        $model     = ORM::factory($type['model']);

        $filters = $this->request->query('filters');
        $results = $model->get_for_datatable($filters, $this->request->query());
        echo json_encode($results);
    }


    public function action_ajax_refresh_reports()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');

        $type_stub = $this->request->param('id');
        $type      = $this->_crud_items[$type_stub];
        $model     = ORM::factory($type['model']);
        $filters   = $this->request->query('filters');
        $reports   = method_exists($model, 'get_reports') ? $model->get_reports($filters) : [];

        echo json_encode(['success' => true, 'reports' => $reports]);
    }

    // Generic delete controller e.g. admin/courses/delete_item/spec/5
    public function action_delete_item()
    {
        try {
            $type_stub = $this->request->param('id');
            $type = $this->_crud_items[$type_stub];
            $id   = $this->request->param('toggle');

            if (empty($type)) {
                IbHelpers::set_message('This action cannot be performed on items of type '.$type_stub, 'error popup_box');
                $this->request->redirect('/admin/'.$this->_plugin);
            }

            if (!empty($type['delete_permission']) && !Auth::instance()->has_access($type['delete_permission'])) {
                IbHelpers::set_message('You need access to the &quot;'.$type['delete_permission'].'&quot; permission to perform this action.', 'error popup_box');
                $this->request->redirect('/admin/'.$this->_plugin);
            }

            $model = ORM::factory($type['model'])->where('id', '=', $id)->find_undeleted();
            $model->delete_and_save();

            IbHelpers::set_message(htmlspecialchars($type['name'].' #'.$id.' successfully deleted.'), 'success popup_box');

            $name_plural = isset($type['name_plural']) ? $type['name_plural'] : $type['name'].'s';
            $stub_plural = isset($type['stub_plural']) ? $type['stub_plural'] : $type_stub.'s';

            $this->request->redirect('/admin/'.$this->_plugin.'/'.$stub_plural);
        }
        catch (Exception $e) {
            Log::instance()->add(Log::ERROR, "Error deleting\n".$e->getMessage()."\n".$e->getTraceAsString());
            IbHelpers::set_message('Unexpected internal error deleting. If this problem continues, please ask an administrator to check the error logs.');
            $this->request->redirect('/admin');
        }
    }

    public function action_ajax_delete_item()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');

        try {
            $type_stub = $this->request->param('id');
            $type = $this->_crud_items[$type_stub];
            $id   = $this->request->param('toggle');

            if (empty($type)) {
                $result = [
                    'message' => 'This action cannot be performed on items of type '.$type_stub,
                    'success' => false
                ];
            }
            else  if (!empty($type['delete_permission']) && !Auth::instance()->has_access($type['delete_permission'])) {
                $result = [
                    'message' => 'You need access to the &quot;'.$type['delete_permission'].'&quot; permission to perform this action.',
                    'success' => false
                ];
            }
            else {
                $model = ORM::factory($type['model'])->where('id', '=', $id)->find_undeleted();
                $model->delete_and_save();

                $result = [
                    'message' => $type['name'].' #'.$id.' successfully deleted.',
                    'success' => true
                ];
            }
        }
        catch (Exception $e) {
            Log::instance()->add(Log::ERROR, "Error deleting course spec.\n".$e->getMessage()."\n".$e->getTraceAsString());
            $result = ['message' => 'Unexpected error deleting spec. If this problem continues, please ask an administrator to check the error logs.', 'success' => false];
        }

        echo json_encode($result);
    }

    public function action_ajax_toggle_publish_state()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $type_stub = $this->request->param('id');

        try {
            $type = $this->_crud_items[$type_stub];
            $id   = $this->request->param('toggle');

            if (empty($type)) {
                $result = [
                    'message' => 'This action cannot be performed on items of type '.$type_stub,
                    'success' => false
                ];
            }
            else  if (!empty($type['edit_permission']) && !Auth::instance()->has_access($type['delete_permission'])) {
                $result = [
                    'message' => 'You need access to the &quot;'.$type['edit_permission'].'&quot; permission to perform this action.',
                    'success' => false
                ];
            }
            else {
                $model = ORM::factory($type['model'])->where('id', '=', $id)->find_undeleted();
                $model->toggle_publish_and_save($this->request->query('published'));

                $message = $this->request->query('published') ? $type_stub .' successfully published' : $type_stub .' successfully unpublished';
                $result = ['message' => ucfirst($message), 'success' => true];
            }

        }
        catch (Exception $e) {
            Log::instance()->add(Log::ERROR, "Error saving ".$type_stub."\n".$e->getMessage()."\n".$e->getTraceAsString());
            $result = ['message' => 'Unexpected error saving '.$type_stub.'. If this problem continues, please ask an administrator to check the error logs.', 'success' => false];
        }

        echo json_encode($result);
    }

    public function action_ajax_search_item()
    {
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $term = $this->request->query('term');
        $type_stub = $this->request->param('id');
        $type      = $this->_crud_items[$type_stub];
        $model     = ORM::factory($type['model']);
        // If user has inputted only a space, we assume they want all results
        $result = $model->where($this->_crud_items[$type_stub]['name'], 'like', ($term === ' ') ? '%%' : '%' . $term . '%')->limit(10)->find_all_published()->as_array('id', $this->_crud_items[$type_stub]['name']);
        $return = array();
        foreach($result as $id => $row)
        {
            $return[] = ['id' => $id, 'value' => $result[$id]];
        }
        $this->response->body(json_encode($return));
    }
}
