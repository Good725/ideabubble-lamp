<?php defined('SYSPATH') OR die('No Direct Script Access');

Class Controller_Admin_Calendars extends Controller_Head
{
    protected $do_not_check_permissions_for_actions = array('get_calendar_dates');

    public function action_index()
    {
        $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/eventscalendar/js/jquery.eventCalendar.js"></script>';
		$this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/calendar.js"></script>';
        $this->template->styles    = array_merge($this->template->styles, array(
			URL::get_engine_plugin_assets_base('courses').'css/timepicker.css' => 'screen',
			URL::get_engine_assets_base().'js/eventscalendar/css/eventCalendar.css' => 'screen',
			URL::get_engine_assets_base().'js/eventscalendar/css/eventCalendar_theme_responsive.css' => 'screen'
        ));
        $this->template->body         = View::factory('content/settings/calendar');
        $this->template->sidebar->breadcrumbs[] = array('name'=> 'Calendar','link'=>'/admin/calendars');

        $this->template->body->events = ORM::factory('Calendar_Event')->find_all_undeleted();
        $this->template->body->rules  = ORM::factory('Calendar_Rule')->find_all_undeleted();
        $this->template->body->types  = ORM::factory('Calendar_Type')->find_all_undeleted();
        $this->template->body->dates  = Model_Calendar_Event::get_all_published_dates();
        $types    = ORM::factory('Calendar_Type')->find_all_undeleted();
        $rules    = ORM::factory('Calendar_Rule')->find_all_undeleted();
        $type_array = array();$rule_array=array();$type_array[0]='';$rule_array[0]='';
        foreach($types as $type)
        {
            $type_array[$type->id]=$type->title;
        }
        foreach($rules as $rule)
        {
            $rule_array[$rule->id]=$rule->title;
        }

        $this->template->body->events   = ORM::factory('Calendar_Event')->find_all_undeleted();
        $this->template->body->rules    = $rules;
        $this->template->body->types    = $types;
        $this->template->body->dates    = Model_Calendar_Event::get_all_published_dates();
        $this->template->body->publish_rules    = ORM::factory('Calendar_Rule')->where('publish','=',1)->find_all_undeleted();
        $this->template->body->publish_types    = ORM::factory('Calendar_Type')->where('publish','=',1)->find_all_undeleted();
        $this->template->body->type_array       = $type_array;
        $this->template->body->rule_array       = $rule_array;
    }

    /***                       EVENTS                        ***/

    public function action_edit_event2()
    {
        $id = $this->request->param('id');
        $event = ORM::factory('Calendar_Event', $id);
        if (!empty($id) && (empty($event->id) || $event->deleted == 1)) {
            $this->request->redirect('admin/calendars/index');
        }
        $this->template->body = View::factory('content/settings/calendar_form_event3');
        $this->template->sidebar->breadcrumbs[] = array('name'=> 'Calendar','link'=>'/admin/calendars/index');
        $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/calendar.js"></script>';
        $this->template->body->event = $event;
        $this->template->body->types = ORM::factory('Calendar_Type')->find_all_undeleted();
        $this->template->body->rules = ORM::factory('Calendar_Rule')->find_all_undeleted();
    }

    public function action_save_event2()
    {
        try
        {
            $user = Auth::instance()->get_user();

            $post = $this->request->post();
            //die('<pre>' . print_r($post, 1) . '</pre>');
            $event = ORM::factory('Calendar_Event', $this->request->post('id'));

            if ((!empty($this->request->post('id')) && $this->request->post('id') != 'new')  && (empty($event->id) || $event->deleted == 1)) {
                throw Exception('Event was deleted or doesn\'t exist');
            }
            $event->values($post);
            $event->set('start_date', date::dmy_to_ymd($post['start_date']));
            if ($post['end_date'] == '')
            {
                $event->set('end_date', date::dmy_to_ymd($post['start_date']));
            }
            else
            {
                $event->set('end_date', date::dmy_to_ymd($post['end_date']));
            }
            $event->set('updated_by', $user['id']);
            $event->set('updated_on', date("Y-m-d H:i:s"));
            if ( ! is_numeric($post['id']))
            {
                $event->set('created_by', $user['id']);
                $event->set('created_on', date("Y-m-d H:i:s"));
            }
            $event->save();
            IbHelpers::set_message('The calendar event: '.$post['title'].' was '.is_numeric($post['id'])?'Updated':'Created'.' successfully.', 'success popup_box');
            if ($post['save_exit'] == "true")
            {
                $this->request->redirect('admin/calendars/index');
            }
            else
            {
                $this->request->redirect('/admin/calendars/edit_event2/' . $event->id);
            }
        }
        catch(Exception $e)
        {
            IbHelpers::set_message('Error saving the calendar event.', 'error popup_box');
            $this->request->redirect('admin/calendars/index');
        }
    }

    public function action_get_calendar_dates()
    {
        $eventIds = $this->request->post('blackout_calendar_event_ids');
        $plugin = $this->request->post('plugin');
        if (!$plugin) {
            $plugin = null;
        }
        if (is_array($eventIds) || $eventIds != 'none') {
            $dates = Model_Calendar_Event::get_all_published_dates($plugin, $eventIds);
        } else {
            $dates = array();
        }

        $this->auto_render = false;
        $this->response->headers('Content-Type', 'application/json');
        echo json_encode($dates);
    }

    /***                               RULES                                              ***/

    public function action_edit_rule()
    {
        $id = $this->request->param('id');
        $plugins = Model_Plugin::get_all();
        $rule = ORM::factory('Calendar_Rule', $id);
        if (!empty($id) && (empty($rule->id) || $rule->deleted == 1)) {
            $this->request->redirect('admin/calendars/index');
        }
        $this->template->sidebar->breadcrumbs[] = array('name'=> 'Calendar','link'=>'/admin/calendars/index');
        $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/calendar.js"></script>';
        $this->template->body = View::factory('content/settings/calendar_form_rule');
        $this->template->body->rule = $rule;
        $this->template->body->plugins = $plugins;
    }

    public function action_save_rule()
    {
        try
        {
            $user = Auth::instance()->get_user();

            $post = $this->request->post();
            $rule = ORM::factory('Calendar_Rule', $this->request->post('id'));
            $rule->values($post);
            $rule->set('updated_by', $user['id']);
            $rule->set('updated_on', date("Y-m-d H:i:s"));
            if ( ! is_numeric($post['id']))
            {
                $rule->set('created_by', $user['id']);
                $rule->set('created_on', date("Y-m-d H:i:s"));
            }
            $rule->save();
            IbHelpers::set_message('The calendar rule: ' . $post['title'] . ' was ' . is_numeric($post['id']) ? 'Updated' : 'Created' . ' successfully.', 'success popup_box');
            if ($post['save_exit'] == "true")
            {
                $this->request->redirect('admin/calendars/index');
            }
            else
            {
                $this->request->redirect('/admin/calendars/edit_rule/' . $rule->id);
            }
        }
        catch(Exception $e)
        {
            IbHelpers::set_message('Error saving the calendar rule.', 'error popup_box');
            $this->request->redirect('admin/calendars/index');
        }
    }

    /***                                 TYPES                                            ***/

    public function action_edit_type()
    {
        $id = $this->request->param('id');
        $type = ORM::factory('Calendar_Type', $id);
        if (!empty($id) && (empty($type->id) || $type->deleted == 1)) {
            $this->request->redirect('admin/calendars/index');
        }
        $this->template->sidebar->breadcrumbs[] = array('name'=> 'Calendar','link'=>'/admin/calendars/index');
        $this->template->scripts[] = '<script src="'.URL::get_engine_assets_base().'js/calendar.js"></script>';
        $this->template->body = View::factory('content/settings/calendar_form_type');
        $this->template->body->type = $type;
    }

    public function action_save_type()
    {
        try
        {
            $user = Auth::instance()->get_user();

            $post = $this->request->post();
            $type = ORM::factory('Calendar_Type', $this->request->post('id'));
            $type->values($post);
            $type->set('updated_by', $user['id']);
            $type->set('updated_on', date("Y-m-d H:i:s"));
            if ( ! is_numeric($post['id']))
            {
                $type->set('created_by', $user['id']);
                $type->set('created_on', date("Y-m-d H:i:s"));
            }
            $type->save();
            IbHelpers::set_message('The calendar type: ' . $post['title'] . ' was ' . is_numeric($post['id']) ? 'Updated' : 'Created' . ' successfully.', 'success popup_box');
            if ($post['save_exit'] == "true")
            {
                $this->request->redirect('admin/calendars/index');
            }
            else
            {
                $this->request->redirect('/admin/calendars/edit_rule/' . $type->id);
            }
        }
        catch(Exception $e)
        {
            IbHelpers::set_message('Error saving the calendar type.', 'error popup_box');
            $this->request->redirect('admin/calendars/index');
        }
    }

    /***                            Functions for status change             ***/

    public function action_ajax_publish()
    {
        $data = $this->request->post();
        $result = array('status'=>'error') ;
        switch ($data['table'])
        {
            case 'calendar_events':
                $event = ORM::factory('Calendar_Event', $data['id']);
                $event->set('publish',$event->publish == 1 ? 0 : 1);
                $event->save();
                $answer = ORM::factory('Calendar_Event', $data['id']);
                break;
            case 'calendar_rules':
                $rule = ORM::factory('Calendar_Rule', $data['id']);
                $rule->set('publish',$rule->publish == 1 ? 0 : 1);
                $rule->save();
                $answer = ORM::factory('Calendar_Event', $data['id']);
                break;
            case 'calendar_types':
                $type = ORM::factory('Calendar_Type', $data['id']);
                $type->set('publish',$type->publish == 1 ? 0 : 1);
                $type->save();
                $answer = ORM::factory('Calendar_Event', $data['id']);
                break;
        }
        if ($answer)
        {
            $result['status']='success';
        }
        exit(json_encode($result));
    }
    
    public function action_ajax_delete()
    {
        $data = $this->request->post();
        $result = array('status'=>'error') ;
        switch ($data['table'])
        {
            case 'calendar_events':
                $event = ORM::factory('Calendar_Event', $data['id']);
                $event->set('deleted',$event->deleted == 1 ? 0 : 1);
                $event->save();
                $answer = ORM::factory('Calendar_Event', $data['id']);
                break;
            case 'calendar_rules':
                $rule = ORM::factory('Calendar_Rule', $data['id']);
                $rule->set('deleted',$rule->deleted == 1 ? 0 : 1);
                $rule->save();
                $answer = ORM::factory('Calendar_Event', $data['id']);
                break;
            case 'calendar_types':
                $type = ORM::factory('Calendar_Type', $data['id']);
                $type->set('deleted',$type->deleted == 1 ? 0 : 1);
                $type->save();
                $answer = ORM::factory('Calendar_Event', $data['id']);
                break;
        }
        if ($answer)
        {
            $result['status']='success';
        }
        exit(json_encode($result));
    }
}
