<?php
class Controller_Admin_Reports extends Controller_Cms
{
    public function before()
    {
        parent::before();

        if(!Auth::instance()->has_access('reports')) {
            IbHelpers::set_message("You need access to the &quot;reports&quot; permission to perform this action.", 'warning');
            $this->request->redirect('/admin');
        }
    }

    public function action_index()
    {
		if ( ! Auth::instance()->has_access('reports'))
		{
			IbHelpers::set_message("You need access to the &quot;reports&quot; permission to perform this action.", 'warning');
			$this->request->redirect('/admin');
		}

        $reports = Model_Reports::get_all_accessible_reports();
        $can_delete_report = Auth::instance()->has_access('reports_delete');
        $can_edit_report   = Auth::instance()->has_access('reports_edit');

        $this->template->body = View::factory('list_reports',array("reports" => $reports, 'can_delete_report' => $can_delete_report, 'can_edit_report' => $can_edit_report));
        $this->template->sidebar = View::factory('sidebar');
        $this->template->sidebar->menus = Model_Reports::get_top_menus();
        $this->template->sidebar->breadcrumbs = Model_Reports::get_breadcrumbs();
        $this->template->sidebar->tools = (Auth::instance()->has_access('reports_edit')) ? '<a href="/admin/reports/add_edit_report"><button type="button" class="btn">Add Report</button></a>' : '';
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('reports').'js/list_reports.js"></script>';
    }

    public function action_read()
    {
        $user = Auth::instance()->get_user();
        $id = $this->request->param('id');
        $report = new Model_Reports($id);
        $report->get(true);
        $report->get_widget(true);
        $chart = new Model_Charts($report->get_chart_id());
    
        if (!$report->has_access($user['id'])) {
            IbHelpers::set_message('You do not have permission to view ' . $report->get_name(), 'error');
            $this->request->redirect('/admin/reports');
            exit();
        }
        
        $this->template->styles = array_merge($this->template->styles, [
            URL::get_engine_plugin_assets_base('reports').'css/reports.css' => 'screen',
            URL::get_engine_plugin_assets_base('reports').'css/jquery.ganttView.css' => 'screen'
        ]);
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('reports').'js/sparkline.js"></script>';
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('reports').'js/reports.js"></script>';
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('reports').'js/jquery.ganttView.js"></script>';
        $this->template->sidebar = View::factory('sidebar');
        $this->template->sidebar->menus = Model_Reports::get_top_menus();
        $this->template->sidebar->breadcrumbs = Model_Reports::get_breadcrumbs();
        $this->template->sidebar->breadcrumbs[] = ['name' => $report->get_name(), 'link' => '/admin/reports/read/'.$this->request->param('id')];
        $this->template->sidebar->tools = View::factory('add_edit_report_actions')->set('id', $report->get_id());
        $this->template->body = View::factory('read_report')
            ->set('chart', $chart)
            ->set('report', $report);
    }

    public function action_add_edit_report()
    {
		if ( ! Auth::instance()->has_access('reports_edit'))
		{
			IbHelpers::set_message("You need access to the &quot;reports edit&quot; permission to perform this action.", 'warning');
			$this->request->redirect('/admin');
		}

        $id     = $this->request->param('id');
		$version_id = $this->request->query('version');
        $report = new Model_Reports($id);
		$report->get(true, $version_id);
        $user   = Auth::instance()->get_user();
		$can_delete_report = Auth::instance()->has_access('reports_delete');
        $print_trays = array('' => '');
        foreach (Model_Eprinter::search(array('published' => 1)) as $eprinter) {
            $print_trays[$eprinter['email']] = trim($eprinter['location'] . ' ' . $eprinter['tray'] . ' (' . $eprinter['email'] . ')');
        }

		if (!$report->has_access($user['id']))
		{
			IbHelpers::set_message('You do not have permission to view ' . $report->get_name(), 'error');
			$this->request->redirect('/admin/reports');
			exit();
		}

        $keywords = $report->get_keywords();
        $report->get_widget(true);
        $autoload = $report->get_autoload();
        $chart = new Model_Charts($report->get_chart_id());
        $this->template->styles = array_merge($this->template->styles, array(
			URL::get_engine_assets_base().'css/validation.css' => 'screen',
            URL::get_engine_assets_base().'css/spectrum.min.css' => 'screen',
			URL::get_engine_plugin_assets_base('reports').'css/reports.css' => 'screen',
			URL::get_engine_plugin_assets_base('reports').'css/jquery.ganttView.css' => 'screen'
		));
		$this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validationEngine2-en.js"></script>';
		$this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/jquery.validationEngine2.js"></script>';
        $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/spectrum.min.js"></script>';
		$this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('reports').'js/sparkline.js"></script>';
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('reports').'js/reports.js"></script>';
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('reports').'js/jquery.ganttView.js"></script>';
        $this->template->sidebar = View::factory('sidebar');
        $this->template->sidebar->menus = Model_Reports::get_top_menus();
        $this->template->sidebar->breadcrumbs = Model_Reports::get_breadcrumbs();
        $this->template->sidebar->breadcrumbs[] = ['name' => $report->get_name(), 'link' => '/admin/reports/add_edit_report/'.$this->request->param('id')];
        $this->template->sidebar->tools = View::factory('add_edit_report_actions')->set('id', $report->get_id());

		$report_widget_type    = ORM::factory('Reports_WidgetType', $report->get_widget_type());
		$activities            = ($id) ? Model_Activity::get_all_for_item('report', $id) : FALSE;
		$role_model            = new Model_Roles;
		$roles                 = $role_model->get_all_roles();
		$widget_types          = ORM::factory('Reports_WidgetType')->order_by('name')->find_all(); // All widget types
		$sparkline_chart_types = ORM::factory('Reports_ChartType')->order_by('name')->where('deleted', '=', 0)->find_all();
		$sparkline_total_types = ORM::factory('Reports_TotalType')->order_by('name')->where('deleted', '=', 0)->find_all();

		$dashboards  = ORM::factory('Dashboard')->find_all_published();

		if(Model_Plugin::is_enabled_for_role('Administrator', 'Messaging')){
			$notification_templates = Model_Messaging::notification_template_list();
		} else {
			$notification_templates = null;
		}

        foreach($roles as $key => $role)
		{
		    // Do not show admin and super user as options
            if (in_array($role['id'], ['1', '2']))
			{
                unset($roles[$key]);
            }
        }

        $this->template->body = View::factory(
			'add_edit_report',
            array(
				'report'                 => $report,
				'report_widget_type'     => $report_widget_type,
				'widget_types'           => $widget_types,
                'chart'                  => $chart,
				'sparkline_chart_types'  => $sparkline_chart_types,
				'sparkline_total_types'  => $sparkline_total_types,
                'keywords'               => $keywords,
                'autoload'               => $autoload,
				'activities'             => $activities,
                'roles'                  => $roles,
				'notification_templates' => $notification_templates,
				'loaded_version_id'      => $version_id,
				'can_delete_report'      => $can_delete_report,
                'print_trays'            => $print_trays,
				'dashboards'             => $dashboards
			)
		);
    }

    public function action_save()
    {
		if ( ! Auth::instance()->has_access('reports_edit'))
		{
			IbHelpers::set_message("You need access to the &quot;reports edit&quot; permission to perform this action.", 'warning');
			$this->request->redirect('/admin');
		}

        $post = $this->request->post();

		if($post['action'] == "rollback_to_version"){
			Model_Reports::rollback_to_version($post['id'], $post['rollback_to_version']);
			$redirect = 'add_edit_report/' . $post['id'];
		}
		else
		{
			$report = new Model_Reports();
			$report->validate($post);
			$report->load($post);
			if ($post['modified'] == '1' || $post['action'] != 'load_version')
			{
				$saved = $report->save($post['action'] != 'load_version');
				/* save parameters */
				if(isset($post['month_id']) AND isset($post['month_value']))
				{
					DB::update('plugin_reports_parameters')
						->set(array('value' => $post['month_value']))
						->where('id', '=', $post['month_id'])
						->execute();
				}

				// Save sparkline data
				// todo: this works when there is only 1 sparkline per report. It will need tweaking when a report can have multiple sparklines.
				$sparkline = ORM::factory('Reports_Sparkline')->where('report_id', '=', $saved->get_id())->find();
				$sparkline->values($post['sparkline']);
				$sparkline->set('report_id', $saved->get_id());
				$sparkline->save();

			}
			if($post['action'] == 'load_version'){
				$redirect = 'add_edit_report/'.$report->get_id() . '?version=' . $post['rollback_to_version'];
			} else {
				$redirect = ($post['action'] == "save") ? 'add_edit_report/'.$report->get_id() : '';
			}
			if($post['action'] == 'save_and_send' && Model_Plugin::is_enabled_for_role('Administrator', 'Messaging')){
                try {
                    $mm = new Model_Messaging();
                    $mm->send_report($report->get_id());
                    IbHelpers::set_message($report->get_name() . ' has been sent', 'success');
                } catch (Exception $exc) {
                    IbHelpers::set_message("Unable to send " . $report->get_name(), 'error');
                }
			}
		}

		$this->request->redirect('/admin/reports/'.$redirect);
    }

    function action_send_report()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        session_commit();
        $this->auto_render = false;

        try {
            $post = $this->request->post();
            $report = new Model_Reports($post['id']);
            $data = @$post['data'];
            $mm = new Model_Messaging();
            if ($data) {
                $mm->send_report($report->get_id(), null, null, $data);
            } else {
                $mm->send_report($report->get_id());
            }
            IbHelpers::set_message($report->get_name() . ' has been sent', 'success');
        } catch (Exception $exc) {
            IbHelpers::set_message("Unable to send " . $report->get_name(), 'error');
        }
    }
	
	public function action_clone_report()
    {
		if ( ! Auth::instance()->has_access('reports_edit'))
		{
			IbHelpers::set_message("You need access to the &quot;reports edit&quot; permission to perform this action.", 'warning');
			$this->request->redirect('/admin');
		}

        $id = $this->request->param('id');
		$new_id = Model_Reports::clone_report($id);
		$this->request->redirect('/admin/reports/add_edit_report/' . $new_id);
	}

    public function action_beautify_sql()
    {
        $this->auto_render = false;
        $post = $this->request->post();
        echo Model_SqlFormatter::format($post['sql']);
    }

    public function action_dashboard()
    {
        $this->auto_render = false;

		if ( ! Auth::instance()->has_access('reports_edit'))
		{
			echo 0;
		}
		else
		{
			$post = $this->request->post();
			Model_Reports::toggle_dashboard($post['report_id']);
		}
    }

	public function action_toggle_favorite()
	{
		$this->auto_render = FALSE;
		if ( ! Auth::instance()->has_access('reports_edit'))
		{
			echo 0;
		}
		else
		{
			$post = $this->request->post();
			Model_Reports::toggle_favorite($post['report_id'], $post['is_favorite']);
		}
	}

    public function action_delete_keyword()
    {
        $this->auto_render = false;
        $post = $this->request->post();
        $keyword = new Model_Keyword();
        $keyword->set_id($post['keyword_id']);
        $keyword->set_report_id($post['keyword_id']);
        $keyword->load(TRUE);
        $result = $keyword->delete();
        $this->response->body($result);
    }

    public function action_categories()
    {
		if ( ! Auth::instance()->has_access('reports_edit'))
		{
			IbHelpers::set_message("You need access to the &quot;reports edit&quot; permission to perform this action.", 'warning');
			$this->request->redirect('/admin');
		}

        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('reports').'js/reports.js"></script>';
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('reports').'js/categories.js"></script>';
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('reports').'js/list_categories.js"></script>';
        $this->template->body = View::factory('list_categories', array(
                'categories' => Model_Reports_Categories::get_all_categories(),
                'can_delete_reports' => Auth::instance()->has_access('reports_delete')
            ));
        $this->template->sidebar = View::factory('sidebar');
        $this->template->sidebar->menus = Model_Reports::get_top_menus();
        $this->template->sidebar->breadcrumbs = Model_Reports::get_breadcrumbs();
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Categories', 'link' => '/admin/reports/categories');
        $this->template->sidebar->tools = '<a href="/admin/reports/add_edit_category"><button type="button" class="btn">Add Category</button></a>';
    }

    public function action_add_edit_category()
    {
		if ( ! Auth::instance()->has_access('reports_edit'))
		{
			IbHelpers::set_message("You need access to the &quot;reports edit&quot; permission to perform this action.", 'warning');
			$this->request->redirect('/admin');
		}
        $category = new Model_Reports_Categories($this->request->param('id'));
        $category->get(true);
        $this->template->styles = array_merge($this->template->styles, array(URL::get_engine_plugin_assets_base('reports').'css/reports.css' => 'screen'));
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('reports').'js/categories.js"/>';
        $this->template->body = View::factory('add_edit_category',array("category" => $category));
        $this->template->sidebar = View::factory('sidebar');
        $this->template->sidebar->menus = Model_Reports::get_top_menus();
        $this->template->sidebar->breadcrumbs = Model_Reports::get_breadcrumbs();
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Categories', 'link' => '/admin/reports/categories');
        $this->template->sidebar->tools = '<a href="/admin/reports/add_edit_category"><button type="button" class="btn">Add Category</button></a>';
    }

    public function action_toggle_publish_category()
    {
        $this->auto_render = false;
		if ( ! Auth::instance()->has_access('reports_edit'))
		{
			echo 0;
		}
		else
		{
			$post = $this->request->post();
			$category = new Model_Reports_Categories($post['category_id']);
			$category->publish(intval($post['publish']));
		}
    }

    public function action_save_category()
    {
		if ( ! Auth::instance()->has_access('reports_edit'))
		{
			IbHelpers::set_message("You need access to the &quot;reports edit&quot; permission to perform this action.", 'warning');
			$this->request->redirect('/admin');
		}

        $post = $this->request->post();
        $category = new Model_Reports_Categories();
        $category->load($post);
        $category->save();
        $this->request->redirect("/admin/reports/categories/".$category->get_id());
    }

    public function action_widget_json()
    {
        $id = $this->request->param('id');
        $post = $this->request->post();
        //$this->response->headers('Content-Type','application/json');
        $sql = (isset($post['sql']) AND is_string($post['sql']) AND $post['sql'] != 'null') ? $post['sql'] : null;
        $this->auto_render = false;
        $report = new Model_Reports($id);
        $report->get(true);
        $report->get_widget(true);
        //only allow admins to change sql on the fly
        //non admins will use saved sql only
        if (Auth::instance()->has_access('reports_edit')) {
            $report->set_sql($sql);
        }
        $report->set_parameters($post['parameters']);
        $report->set_parameters($report->prepare_parameters());
        $widget_json = $report->get_widget_json();
        $this->response->body(trim($widget_json),'"');
    }

    public function action_chart_json()
    {
        $post = $this->request->post();
        $id = $this->request->param('id');
        $sql = (isset($post['sql']) AND is_string($post['sql']) AND $post['sql'] != 'null') ? $post['sql'] : null;
        $this->auto_render = false;
        $report = new Model_Reports($id);
        $report->get(true);
        $report->set_sql($sql);
        $report->set_parameters($post['parameters']);
        $report->set_parameters($report->prepare_parameters());
        $chart_json = $report->get_chart_json();
        $this->response->body(trim($chart_json),'"');
    }

    public function action_get_report_table()
    {
        $post = $this->request->post();
        $sql = (isset($post['sql']) AND is_string($post['sql']) AND $post['sql'] != 'null') ? $post['sql'] : null;
        $report = new Model_Reports($post['id']);
        $report->get(true);
        //prevent non admins to change sql
        //allow admins to change sql
        if (Auth::instance()->has_access('reports_edit')) {
            $report->set_sql($sql);
        }
        $parameters = isset($post['parameters']) ? $post['parameters'] : '';
        $report->set_parameters($parameters);
        $report->set_parameters($report->prepare_parameters());
        $result = $report->report_data_json($post);
        $this->auto_render = false;
        $this->response->body($result);
    }

    public function action_export_report_as_csv()
    {
        $id = $this->request->param('id');
        $post = $this->request->post();
        $this->auto_render = false;
        $report = new Model_Reports($id);
        $report->get(true);
        $report->get_widget(true);
        $report->set_parameters($post['csv_parameters']);
        $report->set_parameters($report->prepare_parameters());
        $report->set_sql($post['csv_sql']);

		// Log the action
		$activity = new Model_Activity;
		$activity->set_item_type('report')->set_action('download')->set_item_id($id)->save();

        ExportCsv::export_report($this->response,$report->get_sql(true),$report->get_name());
    }

    public function action_run_sql()
    {

    }

    public function action_get_new_parameter()
    {
        $this->auto_render = false;
        $parameter = new Model_Parameter(null);
        $this->response->body(View::factory('add_edit_parameter')->bind('parameter',$parameter));
    }

    public function action_save_keyword()
    {
        $this->auto_render = false;
        $post = $this->request->post();
        if(trim($post['keyword']) == '' OR !isset($post['keyword']))
        {
            exit;
        }
        else
        {
            //$post['url'] = URL::site();
            Model_Keyword::factory(null)->set($post)->save();
            $this->action_render_keywords_table($post['report_id']);
        }
    }

    public function action_render_keywords_table($report_id = NULL)
    {
        $report = new Model_Reports($report_id);
        $report->get(true);
        $keywords = $report->get_keywords();
        $search_engine = $report->get_parameters('search_engine','google.ie');
        $this->response->body(View::factory('keywords')->bind('keywords',$keywords)->bind('search_engine',$search_engine[0]['value']));
    }

    public function action_delete_category()
    {
		if ( ! Auth::instance()->has_access('reports_delete'))
		{
			IbHelpers::set_message("You need access to the &quot;reports delete&quot; permission to perform this action.", 'warning');
			$this->request->redirect('admin/');
		}
        $this->auto_render = false;
        $post = $this->request->post();
        $category = new Model_Reports_Categories($post['category_id']);
        $ok = $category->delete();
        $this->response->body($ok);
    }

    public function action_get_sql_parameters()
    {
        $this->auto_render = false;
        $options = Model_Reports::get_sql_parameter_options();
        $this->response->body($options);
    }

    public function action_delete_report()
    {
		if(!Auth::instance()->has_access('reports_delete')) {
            IbHelpers::set_message('You do not have access to this feature.', 'error');
            $this->request->redirect('admin/');
        }
        $this->auto_render = false;
        $post = $this->request->post();
        $report = new Model_Reports($post['report_id']);
        $report->get(true);
        $report->delete();
    }

    public function action_get_serp_widget()
    {
        $this->auto_render = false;
        $post = $this->request->post();
        $id = intval($this->request->param('id'));
        $report_type = Model_Reports::get_report_widget_type($post['report_type']);
        $report = new Model_Reports($post['report_id']);
        $report->get(true);
        $report->get_widget(true);
        $keywords = $report->get_serp_widget_keywords($report_type);
        $results = $keywords['results'];
        $keywords = $keywords['keywords'];
        $widget_title = $report->get_widget_name();
        $report_search_engine = $report->get_parameters('search_engine','google.ie');
        $this->response->body(View::factory('serp_widget')->bind('keywords',$keywords)->bind('widget_title',$widget_title)->bind('widget_engine',$report_search_engine[0]['value'])->bind('results',$results));
    }

    public function action_get_table_columns()
    {
        $this->auto_render = false;
        //only allow admins to use this. only needed in report edit
        if (Auth::instance()->has_access('reports_edit')) {
            $sql = $this->request->post('sql');
            $report = new Model_Reports();
            $report->set_sql($sql);
            $report->sort_table_columns();
            $this->response->body(json_encode($report->get_table_columns()));
        } else {
            $report = new Model_Reports($this->request->post('id'));
            $report->get(true);
            $report->sort_table_columns();
            $this->response->body(json_encode($report->get_table_columns()));
        }
    }

    // AJAX function for generating sublist in the plugins' dropdown
    public function action_ajax_get_submenu($data_only = false)
    {
        $model           = new Model_Reports();
        $items           = $model->get_all_accessible_reports();
        $return['link']  = 'read';
        $return['items'] = array();

        for ($i = 0; $i < sizeof($items) && $i < 10; $i++) {
            $return['items'][] = array('id' => $items[$i]['id'], 'title' => ($items[$i]['name']));
        }

        if ($data_only) {
            return $return;
        } else {
            $this->auto_render = false;
            $this->response->headers('Content-type', 'application/json; charset=utf-8');
            $this->response->body(json_encode($return));
        }
    }

    public function action_ajax_get_custom_fields(){
        header('content-type: application/json; charset=utf-8');
        session_commit();
        Model_Reports::set_logged_in_for_mysql();
        $post = $this->request->post();
        $list = [];
		if(isset($post['id']) && false){ // this logic not use anymore
			/*$answer['custom_input_id'] = Arr::get($post, 'id');
            if (preg_match('/\{\!/', $post['query'])) { //some parameters is not set yet, do not run
                $answer['rows'] = array();
                $answer['status'] = 'success';
            } else {
                try {
                    $answer['rows'] = DB::query(Database::SELECT, $post['query'])->execute()->as_array();
                    $answer['status'] = 'success';
                } catch (Exception $e) {
                    $answer['status'] = 'error';
                    $answer['sql'] = $post['query'];
                }
            }*/
		} else {
			$report_id = $post['report_id'];
			$param = $post['param'];
            $answer['param'] = $param;
            $answer['custom_input_id'] = Arr::get($post, 'id');
			unset($post['report_id']);
			unset($post['param']);
			unset($post['query']);
			$report = new Model_Reports($report_id);
			$report->load(array('parameter_fields' => $post['parameter_fields']));
			$report->load(array('parameter_fields' => $report->prepare_parameters()));
			$sql = DB::select('value')
						->from('plugin_reports_parameters')
						->where('report_id', '=', $report_id)
						->and_where('name', '=', $param)
						->execute()
						->get('value');
			foreach($post as $key => $value){
				$value = $report->get_parameter($key);
                if (is_array($value)) {
                    foreach ($value as $key2 => $val2) {
                        $value[$key2] = "'" . $val2 . "'";
                    }
                    $value = implode(',', $value);
                }
				$sql = str_replace("{!" . $key . "!}", $value, $sql);
			}
			$answer['sql'] = $sql;
            if (preg_match('/\{\!/', $sql)) { //some parameters is not set yet, do not run
                $answer['rows'] = array();
                $answer['status'] = 'success';
            } else {
                try {
                    $answer['rows'] = DB::query(Database::SELECT, $sql)->execute()->as_array();
                    $answer['status'] = 'success';
                } catch (Exception $e) {
                    $answer['status'] = 'error';
                }
            }
		}
        if($answer['status'] != 'error'){
            if(!empty($answer['rows'])){
                foreach($answer['rows'] as $row){
                    $list[] = $row;
                }
                $answer['rows'] = $list;
            } else {
                $answer['status'] = 'empty';
            }
        }
        exit(json_encode($answer));
    }

    public function action_ajax_get_report()
	{
		$this->auto_render = FALSE;
        $post              = $this->request->post();
        $id                = $post['report_id'];
        $report            = new Model_Reports($id);
		$parameters        = isset($post['report_parameters']) ? $post['report_parameters'] : '';
        $report->get(TRUE);
        $report->get_widget(TRUE);
        $report->set_parameters($parameters);
        $report->set_parameters($report->prepare_parameters());
        $report->set_sql($post['report_sql']);

        $timestamp = '_' . strftime("%Y%m%d%H%M%S");
        $filename  = $report->get_name() . $timestamp . '.' . $post['report_format'];

        if(!Model_Files::_sql_get_directory_id('Reports', 1)){
            Model_Files::create_directory(1,'Reports');
        }
        if(!Model_Files::_sql_get_directory_id($report->get_name(), Model_Files::_sql_get_directory_id('Reports', 1))){
            Model_Files::create_directory(Model_Files::_sql_get_directory_id('Reports', 1), $report->get_name());
        }

        $parent_id = Model_Files::_sql_get_directory_id($report->get_name(), Model_Files::_sql_get_directory_id('Reports', 1));
        $tmp_dest = sys_get_temp_dir() . '/tmp_rep/';

        if (!file_exists($tmp_dest)) {
            mkdir($tmp_dest, 0777, true);
        }

        try {
            $rows = $report->execute_sql();
            $answer['status'] = 'success';
        } catch(Exception $e){
            $answer['status'] = 'error';
        }
        if($answer['status'] == 'success') {
            if($rows) {
                switch ($post['report_format']) {
                    case 'csv':
                        $csv_columns = $report->get_csv_columns() != '' ? explode("\n", $report->get_csv_columns()) : false;
                        if ($csv_columns)
                        foreach ($csv_columns as $i => $csv_column) { // clean up any potential unwanted blanks
                            $csv_columns[$i] = trim(strip_tags($csv_column));
                        }
                        foreach($rows AS $row_index => $row)
                        {
                            if ($csv_columns) {
                                foreach ($row as $column => $data) {
                                    if (!in_array($column, $csv_columns)) {
                                        unset ($row[$column]);
                                    }
                                }
                                $new_row = array();
                                foreach($row as $column => $value) {
                                    $new_row[$column] = trim(strip_tags($value));
                                }
                                if (strpos($row_index, 'Phone') !== false) {
                                    $rows[$row_index] = '"' . trim(preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $value )) . '"';
                                } else {
                                    $rows[$row_index] = trim(preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $value ));

                                }

                            } else {
                                $new_row = array();
                                foreach($row as $column => $value) {
                                    $new_row[$column] = trim(preg_replace('@<(\w+)\b.*?>.*?</\1>@si', '', $value ));
                                }
                                $rows[$row_index]  = $new_row;
                            }
                        }

                        ibhelpers::save_csv($rows, $tmp_dest . $filename);
                        $data = file_get_contents($tmp_dest . $filename);
                        break;
                    case 'xml':
                        $data = $this->convertQueryToXML($rows);
                        break;
                    case 'xls':
                    case 'html':
                        $data = $this->convertQueryToHTML($rows);
                        break;
                }

                file_put_contents($tmp_dest . $filename, $data, LOCK_EX);
                $report = array(
                    'name'     => $filename,
                    'tmp_name' => $tmp_dest.$filename,
                    'type'     =>'text/'.$post['report_format'],
                    'size'     => filesize($tmp_dest.$filename)
                );

                $file_id = Model_Files::create_file($parent_id, $filename, $report);

				// Log the action
				$action = ($post['select_purpose'] == 'email') ? 'email' : 'export';
				$activity = new Model_Activity;
				$activity->set_item_type('report')->set_action($action)->set_item_id($id)->set_file_id($file_id)->save();

                IbHelpers::set_message(Model_Files_Messages::FILE_CREATED, 'success');

                if ($post['select_purpose'] == 'download')
				{
                    $answer['file'] = Model_Files::get_file_id($filename, $parent_id);
                }
				else
				{
                    $user = Auth::instance()->get_user();
                    $user = ORM::factory('users', $user['id']);
                    if ($this->send_report_to_email($user->email, $tmp_dest . $filename, $id))
					{
                        $answer['email_status'] = 'success';
                        $answer['email'] = $user->email;
                    }
					else
					{
                        $answer['email_status'] = 'error';
                    }
                }
                $this->removeDirectory($tmp_dest);
            }
			else
			{
                $answer['status'] = 'empty';
            }
        }
        exit(json_encode($answer));
    }

    private function send_report_to_email($email, $file, $reportId){
        try {
            $files[] = $file;
            $subject = 'Requested report from ' . URL::base();
            $email_body = 'Requested report (see attachment) for ' . URL::base() . 'admin/reports/add_edit_report/' . $reportId;
            $from = Settings::instance()->get('account_verification_sender');
            $from = ($from == '') ? 'noreply@ideabubble.ie' : $from;
            IbHelpers::send_email($from, $email, NULL, NULL, $subject, $email_body, $files);
            $status = true;
        } catch(Exception $e) {
            $status = false;
        }
        return $status;
    }

    private function convertQueryToHTML($rows){
        $header  = false;
        $data = '<html><body><table border="1"><thead><tr>';
        foreach($rows as $row){
            if(!$header){
                foreach($row as $name => $cell){
                    $data .= '<th>' . $name . '</th>';
                }
                $data .= '</tr></thead><tbody>';
                $header  = true;
                $data .= '<tr>';
                foreach($row as $cell){
                    $data .= '<td>' . $cell . '</td>';
                }
                $data .= '</tr>';
            } else {
                $data .= '<tr>';
                foreach($row as $cell){
                    $data .= '<td>' . $cell . '</td>';
                }
                $data .= '</tr>';
            }
        }
        $data .= '</tbody></body></html>';
        return $data;
    }

    private function convertQueryToXML($rows){
        $data = '<?xml version="1.0" encoding="UTF-8"?>';
        $data .= '<table>';
        foreach($rows as $row){
            $data .= '<row>';
            foreach($row as $name => $cell){
                $data .= "<$name>$cell</$name>";
            }
            $data .= '</row>';
        }
        $data .= '</table>';
        return $data;
    }

    private function removeDirectory($dir) {
        if ($objs = glob($dir."/*")) {
            foreach($objs as $obj) {
                is_dir($obj) ? $this->removeDirectory($obj) : unlink($obj);
            }
        }
        rmdir($dir);
    }

	/* Render dashboards through an AJAX call */
	public function action_ajax_render_dashboard_reports()
	{
		$this->auto_render = FALSE;
		echo self::action_render_dashboard_reports();
	}

	public static function action_render_dashboard_reports()
	{
		$return  = '';
		$widgets = Model_Reports::get_all_accessible_dashboard_reports_widgets();
		foreach ($widgets as $query)
		{
			$report = new Model_Reports($query['id']);
			$report->get(TRUE);

			if ($report->sparkline->report_id AND $report->sparkline->title)
			{
				$return .= $report->sparkline->render();
			}

			$return .= $report->render_widget();
		}

		return View::factory('dashboard_reports')->set('widgets', $return);
	}

	/**
	 * Save the order of the report widgets for the logged-in user
	 */
	public function action_ajax_save_order()
	{
		$this->auto_render = FALSE;
		$report_ids = $this->request->post('report_ids');

		// If there is more than one report widget, save their order
		if (is_array($report_ids) AND count($report_ids) > 1)
		{
			$user = Auth::instance()->get_user();

			// loop through each report from the widgets
			foreach ($report_ids as $order => $report_id)
			{
				// Get the user's options for this report
				// Or create a new record if the user does not have any options saved for this report yet
				$user_options = ORM::factory('Reports_UserOptions')
					->where('report_id', '=', $report_id)
					->where('user_id', '=', $user['id'])
					->find();

				$user_options->set('order', $order)->set('user_id', $user['id'])->set('report_id', $report_id)->save();
			}
		}
	}

	/* Return the widget HTML for a given report ID */
	public function action_ajax_refresh_widget()
	{
		$this->auto_render = FALSE;
		$report = new Model_Reports($this->request->param('id'));
		$report->get(TRUE);
		$report->get_widget(TRUE);
		echo $report->render_widget();
	}

	public function action_ajax_get_sparkline()
	{
		$this->auto_render = FALSE;
		$sparkline         = new Model_Reports_Sparkline($this->request->param('id'));
		$sparkline->values($this->request->post());
		echo $sparkline->render();
	}

    public function action_generate_documents()
    {
        set_time_limit(0); // doc generation takes long
        session_commit(); // prevent session lock to allow browesing
        ignore_user_abort(true); // continue generating in case user browses to another page

        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        $post = $this->request->post();
        $report_id = $post['report_id'];
        $bulk = (int)$post['bulk'];
        $noprint = (int)$post['noprint'];
        $zip = (int)$post['zip'];
        $params = isset($post['parameters']) ? $post['parameters'] : array();
        $data = json_decode(@$post['table'], true);
        $report = new Model_Reports($report_id);
        if ($bulk) {
            echo json_encode($report->generate_documents_bulk($params, $noprint));
        } else {
            $report->get(true);
            if ($report->get_generate_documents_mode() == 'ROW') {
                $files = array();
                foreach ($data as $row) {
                    $result = $report->generate_documents_from_data($row, $data, $noprint, $params);
                    if (@$result['files']) {
                        $files = array_merge($files, $result['files']);
                    } else {

                    }
                }
                if ($zip) {
                    $zip = new ZipArchive();
                    $zip_tmp = tempnam(Kohana::$cache_dir, 'doc');
                    $zip->open($zip_tmp, ZIPARCHIVE::CREATE);
                    foreach ($files as $file) {
                        $zip->addFile(Model_Files::file_path($file['file_id']), basename($file['filename']));
                    }
                    $zip->close();
                    $tmp_dir = '/tmp';
                    $dir_id = Model_Files::get_directory_id($tmp_dir);
                    if (!$dir_id) {
                        Model_Files::create_directory(1, 'tmp');
                        $dir_id = Model_Files::get_directory_id($tmp_dir);
                    }
                    $fileInfo = array(
                        'name' => 'documents-' . date('YmdHis') . '.zip',
                        'type' => 'application/zip',
                        'size' => filesize($zip_tmp),
                        'tmp_name' => $zip_tmp,
                    );

                    $zip_id = Model_Files::create_file($dir_id, $zip_tmp, $fileInfo, null);
                    unlink($zip_tmp);

                    echo json_encode(array(
                        'messageCreated' => $result['messageCreated'],
                        'noprint' => $result['noprint'],
                        'files' => array(
                            array(
                                'filename' => $fileInfo['name'],
                                'file_id' => $zip_id
                            )
                        )
                    ));
                } else {
                    echo json_encode(array(
                        'messageCreated' => $result['messageCreated'],
                        'noprint' => $result['noprint'],
                        'files' => $files
                    ));
                }
            } else {
                echo json_encode($report->generate_documents_from_data($params, $data, $noprint));
            }
        }
    }

    public function action_ajax_get_users()
    {
        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        $result = array();
        $result['param'] = $this->request->post('param');
        $result['role_id'] = $this->request->post('role_id');
        $result['users'] = Model_Users::get_users_as_option(null, $result['role_id'], false);
        echo json_encode($result);
    }
}
?>