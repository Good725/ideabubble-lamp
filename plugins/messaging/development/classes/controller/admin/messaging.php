<?php
defined('SYSPATH') OR die('No Direct Script Access');

class Controller_Admin_Messaging extends Controller_Cms
{
	protected $mm = null;
	
	function before()
	{
		if (!Auth::instance()->has_access('messaging')) {
			$error_id = Model_Errorlog::save(null, 'SECURITY');
			if (Auth::instance()->get_user() != null) {
				IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
                $this->request->redirect('/admin');
			} else {
				exit;
            }
		}
        parent::before();
		
		$this->mm = new Model_Messaging();
	
		$this->template->sidebar = View::factory('sidebar');
		$this->template->sidebar->menus = array(array(
			array('link' => '/admin/messaging/inbox',                  'icon' => 'inbox',        'name' => 'Inbox'),
			array('link' => '/admin/messaging/starred',                'icon' => 'starred',      'name' => 'Starred'),
			array('link' => '/admin/messaging/sent',                   'icon' => 'message',      'name' => 'Sent'),
			array('link' => '/admin/messaging/outbox',                 'icon' => 'outbox',       'name' => 'Outbox'),
            array('link' => '/admin/messaging/outbox_whitelist',       'icon' => 'outbox-whitelist', 'name' => 'Outbox Whitelist'),
			array('link' => '/admin/messaging/scheduled',              'icon' => 'scheduled',    'name' => 'Scheduled'),
			array('link' => '/admin/messaging/drafts',                 'icon' => 'drafts',       'name' => 'Drafts'),
			array('link' => '/admin/messaging/spam',                   'icon' => 'spam',         'name' => 'Spam'),
			array('link' => '/admin/messaging/mutes',                  'icon' => 'muted-senders', 'name' => 'Muted Senders'),
			array('link' => '/admin/messaging/notification_templates', 'icon' => 'template',     'name' => 'Templates'),
			array('link' => '/admin/messaging/signatures',             'icon' => 'signatures',   'name' => 'Signatures'),
			array('link' => '/admin/messaging/settings',               'icon' => 'settings',     'name' => 'Settings'),
			array('link' => '/admin/messaging/drivers',                'icon' => 'drivers',      'name' => 'Drivers')
		));

		foreach ($this->mm->get_drivers() as $driver => $providers) {
			foreach ($providers as $pname => $provider) {
				$settings_ui = $provider->has_settings_ui();
				if ($settings_ui['link']) {
					$this->template->sidebar->menus[0][] = array(
						'link' => '/admin/messaging/custom_settings/' . $driver . '-' . $pname,
						'name' => $settings_ui['link'],
                        'icon' => 'profile'
					);
				}
			}
		}

		if (Auth::instance()->has_access('messaging_global_see_all'))
		{
			array_unshift($this->template->sidebar->menus[0], array('link' => '/admin/messaging/all', 'name' => 'All', 'icon' => 'all'));
		}
		$this->template->sidebar->breadcrumbs = array(
			array('name' => 'Home',  'link' => '/admin'),
			array('name' => 'Messaging', 'link' => '/admin/messaging')
		);
	}

	public function action_index()
    {
        if (!Auth::instance()->has_access('messaging_edit')) {
            IbHelpers::set_message('You do not have access to the &quot;messaging&quot; feature', 'warning popup_box');
            $this->request->redirect('/admin');
        }

        if ($this->request->is_ajax())
		{
			$params = $_GET;
			$messages = $this->mm->search_messages($params);
			echo json_encode($messages);
			exit();
		}
		else
		{
			self::action_inbox();
		}
	}

	public function action_inbox()
	{
        if (!Auth::instance()->has_access('messaging_access_own_mail')) {
            IbHelpers::set_message('You do not have access to the &quot;messaging&quot; feature', 'warning popup_box');
            $this->request->redirect('/admin');
        }

		$this->template->styles                 = array_merge($this->template->styles, array(URL::get_engine_plugin_assets_base('messaging').'css/list_messages.css' => 'screen'));
		$this->template->scripts[]              = '<script src="'.URL::get_engine_plugin_assets_base('messaging').'js/messaging.js"></script>';
		$this->template->scripts[]              = '<script src="'.URL::get_engine_plugin_assets_base('messaging').'js/list_messages.js"></script>';
		$this->template->sidebar->breadcrumbs[] = array('name' => 'Inbox', 'link' => '/admin/messaging/inbox');
		$this->template->sidebar->tools         = View::factory('messaging_actions');
		$use_columns                            = array('actions', 'from', 'subject', 'to', 'status', 'last_activity', 'info');
		$parameters                             = array('inbox' => 1);
		$this->template->body                   = View::factory('messaging_list_ajax')
			->set('use_columns', $use_columns)
			->set('parameters',  $parameters);
		$this->template->body->signatures       = Model_Signature::search();
	}

	public function action_all()
	{
		if (!Auth::instance()->has_access('messaging_global_see_all')) {
			IbHelpers::set_message('You do not have access to the &quot;messaging&quot; feature', 'warning popup_box');
			$this->request->redirect('/admin');
		}

			$this->template->styles                 = array_merge($this->template->styles, array(URL::get_engine_plugin_assets_base('messaging').'css/list_messages.css' => 'screen'));
			$this->template->scripts[]              = '<script src="'.URL::get_engine_plugin_assets_base('messaging').'js/messaging.js"></script>';
			$this->template->scripts[]              = '<script src="'.URL::get_engine_plugin_assets_base('messaging').'js/list_messages.js"></script>';
			$this->template->sidebar->breadcrumbs[] = array('name' => 'All', 'link' => '/admin/messaging/all');
			$this->template->sidebar->tools         = View::factory('messaging_actions');
			$use_columns                            = array('actions', 'from', 'subject', 'to', 'folder', 'status', 'last_activity', 'info');
			$this->template->body                   = View::factory('messaging_list_ajax')
                ->set('use_columns', $use_columns)
                ->set('parameters', array('show_everyones_mail' => true));
			$this->template->body->signatures       = Model_Signature::search();
	}

	public function action_starred()
	{
        if (!Auth::instance()->has_access('messaging_edit')) {
            IbHelpers::set_message('You do not have access to the &quot;messaging&quot; feature', 'warning popup_box');
            $this->request->redirect('/admin');
        }

		$this->template->styles                 = array_merge($this->template->styles, array(URL::get_engine_plugin_assets_base('messaging').'css/list_messages.css' => 'screen'));
		$this->template->scripts[]              = '<script src="'.URL::get_engine_plugin_assets_base('messaging').'js/messaging.js"></script>';
		$this->template->scripts[]              = '<script src="'.URL::get_engine_plugin_assets_base('messaging').'js/list_messages.js"></script>';
		$this->template->sidebar->breadcrumbs[] = array('name' => 'Starred', 'link' => '/admin/messaging/starred');
		$this->template->sidebar->tools         = View::factory('messaging_actions');
		$use_columns                            = array('actions', 'from', 'subject', 'to', 'folder', 'status', 'last_activity', 'info');
		$parameters                             = array('starred' => '1');
		$this->template->body                   = View::factory('messaging_list_ajax')
			->set('use_columns', $use_columns)
			->set('parameters',  $parameters);
		$this->template->body->signatures       = Model_Signature::search();
	}

	public function action_sent()
	{
        if (!Auth::instance()->has_access('messaging_edit')) {
            IbHelpers::set_message('You do not have access to the &quot;messaging&quot; feature', 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $this->template->styles                 = array_merge($this->template->styles, array(URL::get_engine_plugin_assets_base('messaging').'css/list_messages.css' => 'screen'));
		$this->template->scripts[]              = '<script src="'.URL::get_engine_plugin_assets_base('messaging').'js/messaging.js"></script>';
		$this->template->scripts[]              = '<script src="'.URL::get_engine_plugin_assets_base('messaging').'js/list_messages.js"></script>';
		$this->template->sidebar->breadcrumbs[] = array('name' => 'Sent', 'link' => '/admin/messaging/sent');
		$this->template->sidebar->tools         = View::factory('messaging_actions');
		$use_columns                            = array('actions', 'from', 'subject', 'to', 'status', 'last_activity', 'info');
		$parameters                             = array('sent' => 1, 'is_draft' => 0);
		$this->template->body                   = View::factory('messaging_list_ajax')
			->set('use_columns', $use_columns)
			->set('parameters',  $parameters);
		$this->template->body->signatures       = Model_Signature::search();
	}

	public function action_scheduled()
	{
        if (!Auth::instance()->has_access('messaging_edit')) {
            IbHelpers::set_message('You do not have access to the &quot;messaging&quot; feature', 'warning popup_box');
            $this->request->redirect('/admin');
        }


        $this->template->styles                 = array_merge($this->template->styles, array(URL::get_engine_plugin_assets_base('messaging').'css/list_messages.css' => 'screen'));
		$this->template->scripts[]              = '<script src="'.URL::get_engine_plugin_assets_base('messaging').'js/messaging.js"></script>';
		$this->template->scripts[]              = '<script src="'.URL::get_engine_plugin_assets_base('messaging').'js/list_messages.js"></script>';
		$this->template->sidebar->breadcrumbs[] = array('name' => 'Scheduled', 'link' => '/admin/messaging/scheduled');
		$this->template->sidebar->tools         = View::factory('messaging_actions');
		$use_columns                            = array('actions', 'from', 'subject', 'to', 'scheduled', 'status', 'last_activity', 'info');
		$parameters                             = array('scheduled' => true);
		$this->template->body                   = View::factory('messaging_list_ajax')
			->set('use_columns', $use_columns)
			->set('parameters',  $parameters);
		$this->template->body->signatures       = Model_Signature::search();
	}

    public function action_outbox()
    {
        if (!Auth::instance()->has_access('messaging_edit')) {
            IbHelpers::set_message('You do not have access to the &quot;messaging&quot; feature', 'warning popup_box');
            $this->request->redirect('/admin');
        }


        $this->template->styles                 = array_merge($this->template->styles, array(URL::get_engine_plugin_assets_base('messaging').'css/list_messages.css' => 'screen'));
        $this->template->scripts[]              = '<script src="'.URL::get_engine_plugin_assets_base('messaging').'js/messaging.js"></script>';
        $this->template->scripts[]              = '<script src="'.URL::get_engine_plugin_assets_base('messaging').'js/list_messages.js"></script>';
        $this->template->sidebar->breadcrumbs[] = array('name' => 'Outbox', 'link' => '/admin/messaging/outbox');
        $this->template->sidebar->tools         = View::factory('messaging_actions');
        $use_columns                            = array('actions', 'from', 'subject', 'to', 'scheduled', 'status', 'last_activity', 'info');
        $parameters                             = array('outbox' => true);
        $this->template->body                   = View::factory('messaging_list_ajax')
            ->set('use_columns', $use_columns)
            ->set('parameters',  $parameters);
        $this->template->body->signatures       = Model_Signature::search();
    }

	public function action_drafts()
	{
        if (!Auth::instance()->has_access('messaging_edit')) {
            IbHelpers::set_message('You do not have access to the &quot;messaging&quot; feature', 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $this->template->styles                 = array_merge($this->template->styles, array(URL::get_engine_plugin_assets_base('messaging').'css/list_messages.css' => 'screen'));
		$this->template->scripts[]              = '<script src="'.URL::get_engine_plugin_assets_base('messaging').'js/messaging.js"></script>';
		$this->template->scripts[]              = '<script src="'.URL::get_engine_plugin_assets_base('messaging').'js/list_messages.js"></script>';
		$this->template->sidebar->breadcrumbs[] = array('name' => 'Drafts', 'link' => '/admin/messaging/drafts');
		$this->template->sidebar->tools         = View::factory('messaging_actions');
		$use_columns                            = array('actions', 'from', 'subject', 'to', 'last_activity', 'info');
		$parameters                             = array('is_draft' => 1);
		$this->template->body                   = View::factory('messaging_list_ajax')
			->set('use_columns', $use_columns)
			->set('parameters',  $parameters);
		$this->template->body->signatures       = Model_Signature::search();
	}

	public function action_spam()
	{
        if (!Auth::instance()->has_access('messaging_edit')) {
            IbHelpers::set_message('You do not have access to the &quot;messaging&quot; feature', 'warning popup_box');
            $this->request->redirect('/admin');
        }


        $this->template->styles                 = array_merge($this->template->styles, array(URL::get_engine_plugin_assets_base('messaging').'css/list_messages.css' => 'screen'));
		$this->template->scripts[]              = '<script src="'.URL::get_engine_plugin_assets_base('messaging').'js/messaging.js"></script>';
		$this->template->scripts[]              = '<script src="'.URL::get_engine_plugin_assets_base('messaging').'js/list_messages.js"></script>';
		$this->template->sidebar->breadcrumbs[] = array('name' => 'Sent', 'link' => '/admin/messaging/sent');
		$this->template->sidebar->tools         = View::factory('messaging_actions');
		$use_columns                            = array('actions', 'from', 'subject', 'to', 'status', 'last_activity', 'info');
		$parameters                             = array('is_spam' => 1);
		$this->template->body                   = View::factory('messaging_list_ajax')
				->set('use_columns', $use_columns)
				->set('parameters',  $parameters);
		$this->template->body->signatures       = Model_Signature::search();
	}

	public function action_settings()
	{
        if (!Auth::instance()->has_access('messaging_edit')) {
            IbHelpers::set_message('You do not have access to the &quot;messaging&quot; feature', 'warning popup_box');
            $this->request->redirect('/admin');
        }


        $settings = new Model_Settings();
		$post = $this->request->post();
		if(isset($post['save'])){
			$settings->update($this->request->post());
			if (@$post['is_unavailable'] == 1) {
				Model_Messaging::set_unavailable(
					null,
					@$post['unavailable_from_date'] ? date::dmyh_to_ymdh($post['unavailable_from_date']) : null,
					@$post['unavailable_to_date'] ? date::dmyh_to_ymdh($post['unavailable_to_date']) : null,
					@$post['unavailable_auto_reply'],
					@$post['unavailable_reply_message']
				);
			} else {
				Model_Messaging::unset_unavailable(null);
			}
			$this->request->redirect('/admin/messaging/settings');
		}

		$unavailability = Model_Messaging::get_unavailable(null);
		$settings = new Model_Settings;
		$this->template->sidebar->breadcrumbs[] = array('name' => 'Settings', 'link' => '/admin/messaging/settings');
		$this->template->body                   = View::factory('messaging_settings');
		$settings_groups = array('Message Settings');
		foreach($this->mm->get_drivers() as $driver => $providers){
			foreach($providers as $provider){
				if($provider->settings_groupname() != null){
					$settings_groups[] = $provider->settings_groupname();
				}
			}
		}
		$this->template->body->forms            = $settings->build_form(NULL, $settings_groups);
		$this->template->body->unavailability = $unavailability;

	}

	public function action_drivers()
	{
        if (!Auth::instance()->has_access('messaging_edit')) {
            IbHelpers::set_message('You do not have access to the &quot;messaging&quot; feature', 'warning popup_box');
            $this->request->redirect('/admin');
        }


        $post = $this->request->post();
		if(isset($post['save'])){
			Model_Messaging::set_drivers_data($post);
			$this->request->redirect('/admin/messaging/drivers');
		}
		$this->template->sidebar->breadcrumbs[] = array('name' => 'Drivers', 'link' => '/admin/messaging/drivers');

		//header('content-type: text/plain; charset=utf-8');print_r(Model_Messaging::get_drivers());die();
		$this->template->body = View::factory('messaging_list_drivers');
		$this->template->body->messaging_drivers = $this->mm->get_drivers();
		$this->template->body->messaging_drivers_data = Model_Messaging::get_drivers_data();
		//header('content-type: text/plain');print_r($this->template->body->messaging_drivers);print_r($this->template->body->messaging_drivers_data);exit;
	}
	
	public function action_details()
	{
		$message_id = $this->request->query('message_id');

        $auth = Auth::instance();
        $contact3 = Model_Contacts3::get_linked_contact_to_user($auth->get_user()['id']);
        if (!$auth->has_access('messaging_access_others_mail')) {
            if ($auth->has_access('messaging_access_own_mail')) {
                if (!Model_Messaging::check_message_targets($message_id, $contact3['id'])) {
                    $error_id = Model_Errorlog::save(null, 'SECURITY');
                    IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
                    $this->request->redirect('/admin');
                }
            } else {
                if (!Model_Messaging::check_message_targets($message_id, $contact3['id'])) {
                    $error_id = Model_Errorlog::save(null, 'SECURITY');
                    IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
                    $this->request->redirect('/admin');
                }
            }
        }

		$this->template->sidebar->breadcrumbs[] = array('name' => 'Message Details', 'link' => '/admin/messaging/details?message_id=' . $message_id);

		$details                  = $this->mm->get_message_details($message_id);
		if (count($details['targets']) == 1 && @$details['targets'][0]['custom_message'] != '') {
			$details['message'] = @$details['targets'][0]['custom_message'];
		}
		$details['clean_message'] = isset($details['message']) ? $this->mm->clean_message($details['message']) : '';
		$user                     = Auth::instance()->get_user();

        // Mark the message as read by this user
        Model_Messaging::mark_as_read($message_id, $user['id']);

		$this->template->body = View::factory('messaging_details');
		$this->template->body->details = $details;
	}

    public function action_view_message()
    {
        // Get details for the message.
        $message_id = $this->request->param('id');
        $auth = Auth::instance();
        $contact3 = Model_Contacts3::get_linked_contact_to_user($auth->get_user()['id']);
        if (!$auth->has_access('messaging_access_others_mail')) {
            if ($auth->has_access('messaging_access_own_mail')) {
                if (!Model_Messaging::check_message_targets($message_id, $contact3['id'])) {
                    $error_id = Model_Errorlog::save(null, 'SECURITY');
                    IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
                    $this->request->redirect('/admin');
                }
            } else {
                if (!Model_Messaging::check_message_targets($message_id, $contact3['id'])) {
                    $error_id = Model_Errorlog::save(null, 'SECURITY');
                    IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
                    $this->request->redirect('/admin');
                }
            }
		}

        $details = $this->mm->get_message_details($message_id);

        // Message should already have harmful content removed, but just in case...
        $message = isset($details['message']) ? $this->mm->clean_message($details['message']) : '';

        $this->auto_render = false;
        echo View::factory('view_message')->set(compact('message'));
    }

	public function action_send_start()
	{
        if (!Auth::instance()->has_access('messaging_send')) {
            IbHelpers::set_message('You do not have access to the &quot;messaging&quot; feature', 'warning popup_box');
            $this->request->redirect('/admin');
        }

        ignore_user_abort(); // continue when user closes the browser
		session_commit(); // prevent locking session
		$message_id = $_REQUEST['message_id'];
        $outbox_send = @$_REQUEST['outbox_send'] == 1;
		header("Content-Type: text/plain; charset=utf-8");
		printf("Processing message queue...\n");
		flush();
		$this->mm->process_message($message_id, true, $outbox_send);
		exit();
	}

	public function action_send_one()
	{
        if (!Auth::instance()->has_access('messaging_send')) {
            IbHelpers::set_message('You do not have access to the &quot;messaging&quot; feature', 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $recipient_providers = Model_Messaging::get_recipient_providers();
		$recipient_provider_ids = array_keys($recipient_providers);

		$this->template->sidebar->breadcrumbs[] = array('name' => 'Send a Message', 'link' => '/admin/messaging/send_one');

		$post = $this->request->post();
		$send_result = null;
		if(isset($post['send'])){
			$logged_in_user = Auth::instance()->get_user();
			$from = $logged_in_user ? $logged_in_user['id'] : 'CMS';
			$to_list = array();
			foreach($post['target_type'] as $i => $target_type){
				$target_list[] = array('target_type' => $target_type, 'target' => $post['target'][$i]);
			}
			$provider = explode('.', $post['provider']);
			if($post['page_id']){
				$page = Model_Messaging::get_news_page($post['page_id']);
				$message = file_get_contents(URL::site('/') . $page['name_tag']);
			} else {
				$message = $post['message'];
			}
			//$schedule = date('Y-m-d H:i:s', time() + 100);
			$schedule = null;
			$send_result = $this->mm->send($provider[0], $provider[1], $from, $target_list, $message, @$post['subject'], $schedule);
		}
		//header('content-type: text/plain; charset=utf-8');print_r($post);die();
		$this->template->body = View::factory('messaging_send_one');
		$this->template->body->messaging_drivers = $this->mm->get_drivers();
		$this->template->body->send_result = $send_result;
		$this->template->body->news_pages = Model_Messaging::get_news_page_list();
		$this->template->body->recipient_provider_ids = $recipient_provider_ids;
	}

	public function action_ajax_send_message()
	{
        if (!Auth::instance()->has_access('messaging_send')) {
            IbHelpers::set_message('You do not have access to the &quot;messaging&quot; feature', 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $logged_in_user = Auth::instance()->get_user();
		$from           = $logged_in_user ? $logged_in_user['id'] : 'CMS';
		$post           = $this->request->post();
		$driver         = $post['driver'];
		$aprovider      = $this->mm->get_active_provider($driver, true, null);
		$alert = '';
        if ($aprovider) {
            $provider = $aprovider['provider'];
        }
        if ($driver === 'email' && $post[$driver]['from'] === 'phpmail_from_email' &&
            Auth::instance()->has_access('messaging_access_system_mail')) {
            $from = Settings::instance()->get('phpmail_from_email');
        } else {
            if ($post[$driver]['from'] === '') {
                $from = $logged_in_user['email'];
            }
        }
        $subject = isset($post[$driver]['subject']) ? $post[$driver]['subject'] : '';
        $replyto = isset($post[$driver]['replyto']) ? $post[$driver]['replyto'] : null;
        $message = array('content' => $post[$driver]['message']);
        if (isset($post['attachment'])) {
            $message['attachments'] = $post['attachment'];
        }
        $operation = $post['operation'];
        if ($operation == 'save_as_template'){
			$template_data = $post[$driver];
            if (!isset($template_data['subject'])) {
                $template_data['subject'] = '';
            }
            if (!isset($template_data['header'])) {
                $template_data['header'] = '';
            }
            if (!isset($template_data['footer'])) {
                $template_data['footer'] = '';
            }
			$template_data['driver'] = $post['driver'];
			$template_data['type_id'] = 1;
			$template_data['sender'] = $template_data['from'];
			$template_data['replyto'] = @$template_data['replyto'];
			$template_data['page_id'] = null;
			if (isset($post['overwrite_cms_message']))
			{
				$template_data['overwrite_cms_message'] = $post['overwrite_cms_message'];
			}
			if(isset($post[$driver . '_recipient'])){
				$template_data['recipient'] = $post[$driver . '_recipient'];
			} else {
				$template_data['recipient'] = array();
			}
            if (isset($post['attachment'])) {
                $template_data['attachments'] = $post['attachment'];
            }
			$return['id'] = $this->mm->save_notification_template($template_data, null);
			$return['message'] = $return['id'] ? 'template saved' : 'unable to save template';
		} else {
			$is_draft = in_array($operation, array('save', 'save_and_exit')) ? 1 : 0;
			if($is_draft){
				$draft_id = $post['message_id'];
			} else {
				$draft_id = null;
			}
			$target_list = array();
			if(isset($post[$driver . '_recipient'])){
				foreach($post[$driver . '_recipient']['id'] as $i => $recipient_id){
					if($post[$driver . '_recipient']['db_id'][$i] == 'new' || !$is_draft){
						$target = array(
							'target_type' => $post[$driver.'_recipient']['pid'][$i],
							'target'      => $recipient_id,
							'x_details'   => isset($post[$driver.'_recipient']['x_details'][$i]) ? $post[$driver.'_recipient']['x_details'][$i] : '',
                            'final_target'      => (!empty($post[$driver.'_recipient']['final_target'][$i]))      ? $post[$driver.'_recipient']['final_target'][$i]      : '',
                            'final_target_type' => (!empty($post[$driver.'_recipient']['final_target_type'][$i])) ? $post[$driver.'_recipient']['final_target_type'][$i] : ''
						);

						if ($target['target_type'] == 'MOBILE') {
							$target['target_type'] = 'PHONE';
						}
						if ($target['target_type'] == 'EMAIL') {
							$target['target_type'] = 'EMAIL';
						}
						if (@$post[$driver.'_recipient']['template_helper_function'][$i] && @$post[$driver.'_recipient']['template_data_id'][$i]) {
							$da = new Model_Docarrayhelper();
							$target['message'] = Model_Messaging::render_template(
									$post[$driver]['message'],
									call_user_func_array(
										array($da, @$post[$driver.'_recipient']['template_helper_function'][$i]),
										array(@$post[$driver.'_recipient']['template_data_id'][$i])
									)
							);
						}
						$target_list[] = $target;
					}
				}
			}
			foreach(array('to', 'cc', 'bcc') as $x_details){
				if(isset($post[$driver][$x_details])){
					$post[$driver][$x_details] = trim($post[$driver][$x_details]);
					if($post[$driver][$x_details] != ''){
						$recipients = preg_split('/\s*(,|;)\s*/', $post[$driver][$x_details]);
						foreach($recipients as $recipient){
							$target_list[] = array(
								'target_type' => $driver == 'email' ? 'EMAIL' : 'PHONE',
								'target'      => $recipient,
								'x_details'   => $driver == 'email' ? $x_details : ''
							);
						}
					}
				}
			}
			if (isset($post[$driver]['page_id']) && is_numeric($post[$driver]['page_id'])) {
				$message = Model_Pages::get_rendered_output($post[$driver]['page_id']);
			}
			if (@$post[$driver]['signature_id']) {
				if (@$post[$driver]['signature_id'] == 'profile') {
					$model               = new Model_Users();
					$auth_user           = Auth::instance()->get_user();
					$user_data           = $model->get_user($auth_user['id']);
					$message['content'] .= "\r\n" . $user_data['default_messaging_signature'];
				} else {
					$signature = Model_Signature::get($post[$driver]['signature_id']);
					$message['content'] .= "\r\n" . $signature['content'];
				}
			}

			$remove_targets = isset($post['messaging_target_remove']) ? $post['messaging_target_remove'] : array();
			$schedule = null;
			//echo json_encode($post);exit();

			if ($operation == 'delete' AND isset($post['message_id']) AND $post['message_id'] != '')
			{
				$result = $this->mm->delete($post['message_id']);
				if ($result)
				{
					$alert = 'Message deleted';
				}
			}
			else
			{
				session_commit();
				ignore_user_abort(true);
				set_time_limit(0);
				$result = $this->mm->send($driver, $provider, $from, $target_list, $message, $subject, $schedule, $is_draft, $draft_id, $remove_targets, $replyto);
				if(is_numeric($post['message_id']) && !$is_draft){ // delete the original draft after sending message
					$this->mm->delete($post['message_id']);
				}
				if ($result)
				{
					$alert = $is_draft ? 'Draft saved.' : ($this->mm->last_error ? $this->mm->last_error : 'Message sent.');
				}
				else
				{
					$alert = 'Message not sent';
				}
			}
			$return['id']      = $result;
			$return['message'] = $alert;
			$return['error'] = $this->mm->last_error ? true : false;
		}
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($return);
		exit();
	}

	public function action_system_read()
	{
        if (!Auth::instance()->has_access('messaging_view_system_email') && !auth::instance()->has_access('messaging_view_system_sms')) {
            IbHelpers::set_message('You do not have access to the &quot;messaging&quot; feature', 'warning popup_box');
            $this->request->redirect('/admin');
        }
		$delete_result = null;
		$id = $_GET['id'];
		if(isset($_POST['delete'])){
			 Model_Messaging::delete_system_message($id);
			 $this->request->redirect('/admin/messaging');
		}
		$message = Model_Messaging::get_system_message($id);
		if($message){
			Model_Messaging::set_delivery_status_system_message($id, 'READ');
		}
		$this->template->body = View::factory('messaging_read');
		$this->template->body->delete_result = $delete_result;
		$this->template->body->message = $message;
	}

    public function action_to_autocomplete()
    {
        if (!Auth::instance()->has_access('messaging_send')) {
            IbHelpers::set_message('You do not have access to the &quot;messaging&quot; feature', 'warning popup_box');
            $this->request->redirect('/admin');
        }
		$this->auto_render = false;
		$this->response->headers('Content-type', 'application/json; charset=utf-8');
        $driver      = $this->request->query('driver');
        $term        = $this->request->query('term');
        $type        = $this->request->query('type');
        $is_template = (bool) $this->request->query('template');
        $output = Model_Messaging::to_autocomplete($driver, $term, $type, $is_template);
        echo json_encode($output);
    }

    public function action_ajax_course_contact_finder_autocomplete()
	{
        if (!Auth::instance()->has_access('messaging_send')) {
            IbHelpers::set_message('You do not have access to the &quot;messaging&quot; feature', 'warning popup_box');
            $this->request->redirect('/admin');
        }
		$this->auto_render = FALSE;
		$this->response->headers('Content-type', 'application/json; charset=utf-8');
		$filter = $this->request->query();
		$schedules = ORM::factory('Course_Schedule');
		if(count($filter['location_ids']) > 0 && !empty($filter['location_ids'])) {
			$schedules = $schedules
				->join(array('plugin_courses_locations', 'course_location'), 'left')->on('Course_Schedule.location_id', '=', 'course_location.id')
				->where_open()
					->or_where('Course_Schedule.location_id', 'in', $filter['location_ids'])
					->or_where('course_location.parent_id', 'in', $filter['location_ids'])
				->where_close();

		}
		if(count($filter['course_category_ids']) > 0 && !empty($filter['course_category_ids'])) {
			$schedules = $schedules->join(array('plugin_courses_courses', 'course'), 'left')->on('Course_Schedule.course_id', '=', 'course.id')
				->where('course.category_id', 'in', $filter['course_category_ids']);
		}
		$result['schedules'] = $schedules->find_all_undeleted()->as_array('id', 'name');
		foreach($result['schedules'] as $schedule_id => $schedule_name) {
			$num_students = count(Model_Schedules::get_students($schedule_id));
			$trainer = Model_Schedules::get_trainer_by_id($schedule_id);
			$trainer = (!empty(Model_Schedules::get_trainer_by_id($schedule_id))) ? ' - ' . current($trainer) : '';
			$schedule_name = "#{$schedule_id} - {$schedule_name}{$trainer} ({$num_students})";
			$result['schedules'][$schedule_id] = $schedule_name;
		}
		$result['categories'] = (count($result['schedules']) > 0) ?
		$result['categories'] = ORM::factory('Course_Category')
			->join(array('plugin_courses_courses', 'course'), 'inner')->on('Course_Category.id', '=', 'course.category_id')
			->join(array('plugin_courses_schedules', 'course_schedule'), 'inner')->on('course.id', '=', 'course_schedule.course_id')
			->find_all_undeleted()->as_array('id', 'category') : [];
		echo json_encode($result);
	}

	public function action_check_notifications()
	{
		session_commit();
		if (@$_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') {
			$this->request->redirect('/admin');
		}
		header('Content-Type: application/json; charset=utf-8');
        $notifications    = $this->mm->get_messages_for_notification_tray();
        $return['amount'] = 0;

        if (!empty($notifications)) {
            $user = Auth::instance()->get_user_orm();
            $notifications_last_checked = $user->notifications_last_checked;
            $latest_notification_date = $notifications[0]['sent_started'];

            // Only show the new notifications notice, if there are notifications since the user last opened the tray.
            // (The notice can be hidden, even if there are unread messages. As long as the user has opened the tray while they were visible.)
            if (strtotime($latest_notification_date) > strtotime($notifications_last_checked)) {
                $return['amount'] = count($this->mm->get_messages_for_notification_tray(true));
            }
        }

        $return['html']   = (string) View::factory('messaging_popout_menu')->set('notifications', $notifications);
		echo json_encode($return);
		exit();
	}

    /* Returns a simple view, containing message details. This is used in the notifications usermenu dropout. */
    public function action_ajax_view_message()
    {
        if (!Auth::instance()->has_access('messaging_send')) {
            IbHelpers::set_message('You do not have access to the &quot;messaging&quot; feature', 'warning popup_box');
            $this->request->redirect('/admin');
        }

            $user              = Auth::instance()->get_user();
            $id      = $this->request->param('id');
            $auth = Auth::instance();
            $contact3 = Model_Contacts3::get_linked_contact_to_user($auth->get_user()['id']);
            if (!$auth->has_access('messaging_access_others_mail')) {
                if ($auth->has_access('messaging_access_own_mail')) {
                    if (!Model_Messaging::check_message_targets($id, $contact3['id'])) {
                        $error_id = Model_Errorlog::save(null, 'SECURITY');
                        IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
                        $this->request->redirect('/admin');
                    }
                } else {
                    if (!Model_Messaging::check_message_targets($id, $contact3['id'])) {
                        $error_id = Model_Errorlog::save(null, 'SECURITY');
                        IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
                        $this->request->redirect('/admin');
                    }
                }
            }
            $model   = new Model_Messaging;
            $message = $model->get_message_details($id);
            $return  = (string) View::factory('messaging_ajax_view')->set('message', $message);
            Model_Messaging::mark_as_read($message['id'], $user['id']);
            echo $return;
            exit();
    }

	/* Ajax function that makes a server-side query to mark a message as read. */
	public function action_ajax_mark_as_read()
	{
        if (!Auth::instance()->has_access('messaging_edit')) {
            IbHelpers::set_message('You do not have access to the &quot;messaging&quot; feature', 'warning popup_box');
            $this->request->redirect('/admin');
        }
			$this->auto_render = FALSE;
			$user              = Auth::instance()->get_user();
			$message_id        = $this->request->param('id');

            Model_Messaging::mark_as_read($message_id, $user['id']);
	}

	public function action_notification_types()
	{
        if (!Auth::instance()->has_access('messaging_edit')) {
            IbHelpers::set_message('You do not have access to the &quot;messaging&quot; feature', 'warning popup_box');
            $this->request->redirect('/admin');
        }

		$this->template->body = View::factory('messaging_list_notification_types');
		$this->template->body->notification_types = $this->mm->get_notification_types();
	}
	
	public function action_notifications()
	{
        if (!Auth::instance()->has_access('messaging_view')) {
            IbHelpers::set_message('You do not have access to the &quot;messaging&quot; feature', 'warning popup_box');
            $this->request->redirect('/admin');
        }

		$this->template->sidebar->breadcrumbs[] = array('name' => 'Notifications', 'link' => '/admin/messaging/notifications');
		$this->template->body                   = View::factory('messaging_list_notifications');
		$this->template->sidebar->tools         = '<a href="/admin/messaging/notification_template"><button type="button" class="btn">Create Notification Template</button></a>';
		$this->template->body->notifications    = $this->mm->notification_list();
	}

	public function action_notification_templates()
	{
        if (!Auth::instance()->has_access('messaging_view')) {
            IbHelpers::set_message('You do not have access to the &quot;messaging&quot; feature', 'warning popup_box');
            $this->request->redirect('/admin');
        }

		$this->template->sidebar->breadcrumbs[]       = array('name' => 'Templates', 'link' => '/admin/messaging/notification_templates');
		$this->template->body                         = View::factory('messaging_list_notification_templates');
		$this->template->sidebar->tools               = '<a href="/admin/messaging/notification_template"><button type="button" class="btn">Create Notification Template</button></a>';
		$this->template->body->notification_templates = Model_Messaging::notification_template_list();
		$this->template->body->signatures             = Model_Signature::search();
	}
	
	public function action_notification_template()
	{
        if (!Auth::instance()->has_access('messaging_view')) {
            IbHelpers::set_message('You do not have access to the &quot;messaging&quot; feature', 'warning popup_box');
            $this->request->redirect('/admin');
        }

		$recipient_providers    = Model_Messaging::get_recipient_providers();
		$recipient_provider_ids = array_keys($recipient_providers);
		$notification_template  = NULL;
		$save_result            = NULL;
		$id                     = $this->request->query('id');
		$interval               = array(array(), array(), array(), array(), array());
		if($id){
			$notification_template = $this->mm->get_notification_template($id, true);
			if($notification_template['send_interval'] != ''){
				$interval = Model_Messaging::parse_interval($notification_template['send_interval']);
				//print_r($interval);die();
			}
		}
		$post = $this->request->post();
		if(isset($post['save'])){
			if($post['has_interval'] == 0){
				unset($post['interval']);
			}
            if (isset($post['attachment'])) {
                $post['attachments'] = $post['attachment'];
                unset($post['attachment']);
            }
			$saved_id = $this->mm->save_notification_template($post, $id);
			if($saved_id === false){ //error
			}
            else
            {
                if($post['save'] == '')
                {
                    $this->request->redirect('/admin/messaging/notification_template?id=' . $saved_id);
                }
                else
                {
                    $this->request->redirect('/admin/messaging/notification_templates');
                }
			}
		}
		$this->template->scripts[]                    = '<script src="'.URL::get_engine_plugin_assets_base('messaging').'js/messaging.js"></script>';
		$this->template->scripts[]                    = '<script src="'.URL::get_engine_plugin_assets_base('messaging').'js/messaging_notification_template.js"></script>';
		$this->template->sidebar->breadcrumbs[]       = array('name' => 'Notification Templates', 'link' => '/admin/messaging/notification_templates');
		$this->template->body                         = View::factory('messaging_notification_template');
		$this->template->body->recipient_provider_ids = $recipient_provider_ids;
		$this->template->body->newsletter_pages       = Model_Messaging::get_newsletter_page_list();
		$this->template->body->notification_types     = Model_Messaging::get_notification_types2();
		$this->template->body->save_result            = $save_result;
		$this->template->body->notification_template  = $notification_template;
		$this->template->body->interval               = $interval;
		$this->template->body->signatures             = Model_Signature::search();
	}

	public function action_notification_template_details()
	{
        if (!Auth::instance()->has_access('messaging_edit')) {
            IbHelpers::set_message('You do not have access to the &quot;messaging&quot; feature', 'warning popup_box');
            $this->request->redirect('/admin');
        }


        $id = $this->request->post('id');
		$notification_template = $this->mm->get_notification_template($id, false);
		$this->auto_render = false;
		$this->response->headers('Content-Type', 'application/json; charset=utf-8');
		echo json_encode($notification_template, JSON_PRETTY_PRINT);
	}

    /* Generate a preview of the message to be sent by a template */
    public function action_template_preview()
    {
        if (!Auth::instance()->has_access('messaging_edit')) {
            IbHelpers::set_message('You do not have access to the &quot;messaging&quot; feature', 'warning popup_box');
            $this->request->redirect('/admin');
        }
        $this->auto_render = false;

        $id = $this->request->param('id');
        $template = $this->mm->get_notification_template($id, false);

        if (trim($template['message'])) {
            $message = $template['message'];
        } else {
            $message = '<p style="color: #f00;">No message setup within the template. The code might insert something here.</p>';
        }

        echo $this->mm->apply_wrapper($message, ['add_html_body' => true]);
    }

	public function action_test_template()
	{
        if (!Auth::instance()->has_access('messaging_edit')) {
            IbHelpers::set_message('You do not have access to the &quot;messaging&quot; feature', 'warning popup_box');
            $this->request->redirect('/admin');
        }
        $id                     = $this->request->query('id');
		$this->mm->send_template($id, 'welcome', null, array(array('target_type' => 'EMAIL', 'target' => 'x@y.com')));
		exit();
	}
	
	public function action_notification_template_set_publish()
	{
        if (!Auth::instance()->has_access('messaging_edit')) {
            IbHelpers::set_message('You do not have access to the &quot;messaging&quot; feature', 'warning popup_box');
            $this->request->redirect('/admin');
        }

			$post = $this->request->post();
			$id = (int)$post['id'];
			$publish = (int)$post['publish'];
			Model_Messaging::notification_template_set_publish($id, $publish);
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode(array('id' => $id, 'publish' => $publish));
			exit();
	}
	
	public function action_notification_template_delete()
	{
        if (!Auth::instance()->has_access('messaging_edit')) {
            IbHelpers::set_message('You do not have access to the &quot;messaging&quot; feature', 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $post = $this->request->post();
        $id = (int)$post['id'];
        Model_Messaging::notification_template_delete($id);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(array('id' => $id));
        exit();
	}

	public function action_clone_notification_template()
	{
        if (!Auth::instance()->has_access('messaging_edit')) {
            IbHelpers::set_message('You do not have access to the &quot;messaging&quot; feature', 'warning popup_box');
            $this->request->redirect('/admin');
        }

		$new_template_id = null;
		$id = $this->request->query('id');
		if($id){
			$new_template_id = $this->mm->clone_notification_template($id);
		}
		if($new_template_id){
			$this->request->redirect('/admin/messaging/notification_template?id=' . $new_template_id);
		} else {
			$this->request->redirect('/admin/messaging/notification_templates');
		}
	}
	
	public function action_import_notification_template_from_old_notification_event()
	{
        if (!Auth::instance()->has_access('messaging_edit')) {
            IbHelpers::set_message('You do not have access to the &quot;messaging&quot; feature', 'warning popup_box');
            $this->request->redirect('/admin');
        }

        $old_event_id = $_GET['old_event_id'];
		header("Content-Type: text/plain; charset=utf-8");
		echo "new template id:" . $this->mm->import_notification_template_from_old_notification_event($old_event_id);
		exit();
	}

	// AJAX function for generating sublist in the plugins' dropdown
	public function action_ajax_get_submenu($data_only = false)
	{
        $mm              = new Model_Messaging();
		$unread_messages = $mm->get_messages_for_notification_tray(true, null, 0);
		$number_unread   = count($unread_messages) > 0 ? count($unread_messages) : '';

		$return['items']   = array(
			array('icon_svg' => 'inbox',     'link' => '/admin/messaging',           'title' => 'Inbox <span class="user_tools_notification_amount">'.$number_unread.'</span>'),
			array('icon_svg' => 'starred',   'link' => '/admin/messaging/starred',   'title' => 'Starred'),
			array('icon_svg' => 'message',   'link' => '/admin/messaging/sent',      'title' => 'Sent'),
			array('icon_svg' => 'scheduled', 'link' => '/admin/messaging/scheduled', 'title' => 'Scheduled'),
			array('icon_svg' => 'drafts',    'link' => '/admin/messaging/drafts',    'title' => 'Drafts'),
			array('icon_svg' => 'template',  'link' => '/admin/messaging/notification_templates', 'title' => 'Templates'),
			array('icon_svg' => 'settings',  'link' => '/admin/messaging/settings',  'title' => 'Settings')
		);

        if ($data_only) {
            return $return;
        } else {
            if (Auth::instance()->has_access('messaging_global_see_all')) {
                array_unshift($this->template->sidebar->menus[0], array('link' => '/admin/messaging/all', 'name' => 'All'));
            }

            $this->auto_render = false;
            $this->response->headers('Content-type', 'application/json; charset=utf-8');
            $this->response->body(json_encode($return));
        }
	}

	/* Get data for the datatables serverside */
	public function action_ajax_get_datatable()
	{
        if (!Auth::instance()->has_access('messaging_view')) {
            IbHelpers::set_message('You do not have access to the &quot;messaging&quot; feature', 'warning popup_box');
            $this->request->redirect('/admin');
        }
		$this->auto_render = FALSE;
		$auth = Auth::instance();
		$allowed =
				$auth->has_access('messaging_access_own_mail')
				||
				$auth->has_access('messaging_access_system_mail')
				||
				$auth->has_access('messaging_global_see_all')
				||
				$auth->has_access('messaging_view_system_email')
				||
				$auth->has_access('messaging_view_system_sms')
				||
				$auth->has_access('messaging_access_others_mail');
		if ($allowed)
		{
			$this->response->body(Model_Messaging::get_for_datatable($this->request->query()));
		} else {
			$this->response->body('{"iTotalDisplayRecords":0,"iTotalRecords":0,"aaData":[]}');
		}
	}

	// Mark multiple messages as read or unread.
	public function action_ajax_bulk_mark_as_read()
	{
        if (!Auth::instance()->has_access('messaging_edit')) {
            IbHelpers::set_message('You do not have access to the &quot;messaging&quot; feature', 'warning popup_box');
            $this->request->redirect('/admin');
        }
		$this->auto_render = FALSE;
		$user              = Auth::instance()->get_user();

			$message_ids       = $this->request->post('ids');
			$status            = ($this->request->post('read') == 1) ? 'read' : 'unread';

            Model_Messaging::mark_as_read($message_ids, $user['id'], $status);
	}

	// Delete multiple messages at once
	public function action_ajax_bulk_delete()
	{
        if (!Auth::instance()->has_access('messaging_edit')) {
            IbHelpers::set_message('You do not have access to the &quot;messaging&quot; feature', 'warning popup_box');
            $this->request->redirect('/admin');
        }
		$this->response->status(403);
        return;// never delete a message.
		$this->auto_render = FALSE;
		$message_ids       = $this->request->post('ids');

		try
		{
			if (count($message_ids) > 0)
			{
				DB::update('plugin_messaging_messages')->set(array('deleted' => 1))->where('id', 'IN', $message_ids)->execute();
				IbHelpers::set_message('Messages successfully deleted', 'success popup_box');
			}
			else
			{
				IbHelpers::set_message('No messages selected.', 'warning popup_box');
			}
			echo IBHelpers::get_messages();
		}
		catch (Exception $e)
		{
			Log::instance()->add(Log::ERROR, "Error deleting messages\n".$e->getTraceAsString());
			IbHelpers::set_message('Error deleting the message. Please check the system logs.', 'danger popup_box');
			echo IBHelpers::get_messages();
		}
	}

	public function action_ajax_toggle_starred()
	{
		$this->auto_render   = FALSE;
		$id                  = $this->request->param('id');
		$user                = Auth::instance()->get_user();
		$starred             = ORM::factory('Messaging_Starred')->where('message_id', '=', $id)->where('user_id', '=', $user['id'])->find();

		// Save as starred
		if ($this->request->query('is_starred') != 0)
		{
			if ($starred->user_id == '') // not currently starred
			{
				$starred->set('message_id', $id);
				$starred->set('user_id', $user['id']);
				$starred->save();
			}
		}
		elseif ($starred->user_id) // ensure this has already been saved as "starred", before unstarring it
		{
			$starred->unstar();
		}
	}

	public function action_ajax_message_data()
	{
		$id = $this->request->query('id');
        $auth = Auth::instance();
        $contact3 = Model_Contacts3::get_linked_contact_to_user($auth->get_user()['id']);
        if (!$auth->has_access('messaging_access_others_mail')) {
            if ($auth->has_access('messaging_access_own_mail')) {
                if (!Model_Messaging::check_message_targets($id, $contact3['id'])) {
                    $error_id = Model_Errorlog::save(null, 'SECURITY');
                    IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
                    $this->request->redirect('/admin');
                }
            } else {
                if (!Model_Messaging::check_message_targets($id, $contact3['id'])) {
                    $error_id = Model_Errorlog::save(null, 'SECURITY');
                    IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
                    $this->request->redirect('/admin');
                }
            }
        }
		$data = $this->mm->get_message_details($id, true);
		header('Content-Type: application/json; charset=utf-8');
		echo json_encode($data);
		exit();
	}

	public function action_download_attachment()
	{
		$id = $this->request->param('id');
		$attachment = $this->mm->getAttachmentDetails($id);
		if ($attachment['content_encoding'] == 'base64') {
            $attachment['content'] = base64_decode($attachment['content']);
        } else if ($attachment['content_encoding'] == 'file_id') {
            $attachment['content'] = Model_Files::getFileContent($attachment['content']);
        }
        header('Content-Type: ' . $attachment['type']);
        header('Content-Disposition: attachment; filename=' . urlencode($attachment['name']));
        echo $attachment['content'];
        exit();
	}

	public function action_mutes()
	{
        if (!Auth::instance()->has_access('messaging_edit')) {
            IbHelpers::set_message('You do not have access to the &quot;messaging&quot; feature', 'warning popup_box');
            $this->request->redirect('/admin');
        }
		$this->template->body = View::factory('messaging_list_mutes');
	}

	public function action_mutes_get_datatable()
	{
        if (!Auth::instance()->has_access('messaging_edit')) {
            IbHelpers::set_message('You do not have access to the &quot;messaging&quot; feature', 'warning popup_box');
            $this->request->redirect('/admin');
        }
		$this->auto_render = false;
		$this->response->headers('Content-Type', 'application/json; charset=utf-8');
		echo json_encode(Model_Messaging::mutes_get_for_datatable($this->request->query()));
	}

	public function action_unmute()
	{
        if (!Auth::instance()->has_access('messaging_edit')) {
            IbHelpers::set_message('You do not have access to the &quot;messaging&quot; feature', 'warning popup_box');
            $this->request->redirect('/admin');
        }
		$this->auto_render = false;
		$this->response->headers('Content-Type', 'application/json; charset=utf-8');
		Model_Messaging::unmute($this->request->post('sender'));
		echo json_encode(array('result' => 'Ok'));
	}
	public function action_message()
	{
        if (!Auth::instance()->has_access('messaging_view')) {
            IbHelpers::set_message('You do not have access to the &quot;messaging&quot; feature', 'warning popup_box');
            $this->request->redirect('/admin');
        }
		$template = Settings::instance()->get('cms_template');
		if ($template == 'default')
		{
			$styles = array(
				URL::get_engine_plugin_assets_base('messaging').'css/message.css' => 'screen',
				URL::get_engine_assets_base().'css/fixed_layout.css' => 'screen'
			);
		}
		else
		{
			$styles = array(URL::get_engine_plugin_assets_base('messaging').'css/message.css' => 'screen');
		}
        $this->template->styles = array_merge($this->template->styles, $styles);

        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_asset('messaging', 'js/messages.js', ['cachebust' => true]).'"></script>';
		$this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('messaging').'js/slick.min.js"></script>';
		$this->template->body = View::factory('message');
	}

	public function action_sms()
	{
        if (!Auth::instance()->has_access('messaging_view')) {
            IbHelpers::set_message('You do not have access to the &quot;messaging&quot; feature', 'warning popup_box');
            $this->request->redirect('/admin');
        }
		$template = Settings::instance()->get('cms_template');
		if ($template == 'default')
		{
			$styles = array(
				URL::get_engine_plugin_assets_base('messaging').'css/message.css' => 'screen',
				URL::get_engine_assets_base().'css/fixed_layout.css' => 'screen'
			);
		}
		else
		{
			$styles = array(URL::get_engine_plugin_assets_base('messaging').'css/message.css' => 'screen');
		}

        $this->template->styles    = array_merge($this->template->styles, $styles);
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_asset('messaging', 'js/messages.js', ['cachebust' => true]).'"></script>';
		$this->template->body = View::factory('sms');
	}

    public function action_ajax_get_sidemenu_messages()
    {
        $this->auto_render = false;
        $args              = $this->request->query('args');
        $items_per_page    = 4;
        $filters           = array(
            'parameters'     => $this->request->query('params'),
            'iDisplayLength' => $items_per_page,
            'iDisplayStart'  => ( ! empty($args['page_number']) ? ($args['page_number'] - 1) * $items_per_page : 0)
        );
        $mm                = new Model_Messaging();
        $messages          = $mm->filter_messages($filters, $args);
        $auth              = Auth::instance();
        echo View::factory('popout/column_message_list')
            ->set('messages', $messages)
            ->set('filters', $filters)
            ->set('args', $args)
            ->set('auth', $auth);
    }

    public function action_ajax_get_unread_counters()
    {
		session_commit();
        $this->auto_render = false;
        $result            = array('counters' => array());
        $mm                = new Model_Messaging();
        $args              = $this->request->query('args');
        $params_per_folder = $this->request->query('params');

        foreach ($params_per_folder as $name => $params)
        {
            $result['counters'][$name] = $mm->count_unread(array('parameters' => $params), $args);
        }

        echo json_encode($result);
    }

    public function action_ajax_get_message()
    {
        $this->auto_render = false;
        $id      = $this->request->param('id');
        $auth = Auth::instance();
        $contact3 = Model_Contacts3::get_linked_contact_to_user($auth->get_user()['id']);
        if (!$auth->has_access('messaging_access_others_mail')) {
            if ($auth->has_access('messaging_access_own_mail')) {
                if (!Model_Messaging::check_message_targets($id, $contact3['id'])) {
                    $error_id = Model_Errorlog::save(null, 'SECURITY');
                    IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
                    $this->request->redirect('/admin');
                }
            } else {
                if (!Model_Messaging::check_message_targets($id, $contact3['id'])) {
                    $error_id = Model_Errorlog::save(null, 'SECURITY');
                    IbHelpers::set_message('Unexpected system error (' . $error_id . ')', 'error popup_box');
                    $this->request->redirect('/admin');
                }
            }
        }
        $message = $this->mm->get_message_details($id, true);
        $auth    = Auth::instance();
        $user    = $auth->get_user();
        $return  = array(
            'message_html'    => View::factory('popout/message_read')->set('auth', $auth)->set('message', $message)->render(),
            'attachment_html' => count($message['attachments']) ? View::factory('popout/view_attachments')->set('message', $message)->render() : ''
        );

        // Flag the message as having been read by the user
        Model_Messaging::mark_as_read($id, $user['id']);

        echo json_encode($return);

    }

	public function action_custom_settings()
	{
        if (!Auth::instance()->has_access('messaging_edit')) {
            IbHelpers::set_message('You do not have access to the &quot;messaging&quot; feature', 'warning popup_box');
            $this->request->redirect('/admin');
        }
		$driver_provider = $this->request->param('id');
		$driver_provider = explode('-', $driver_provider);

		$drivers = $this->mm->get_drivers();
		$provider = $drivers[$driver_provider[0]][$driver_provider[1]];
		$post = $this->request->post();
		if (@$post['action'] == 'save' || @$post['action'] == 'test') {
			$success = $provider->save_settings($post);
			if ($this->request->is_ajax()) {
				header('content-type: application/json; charset=utf-8');
				echo json_encode(array('success' => $success));
				exit;
			} else {
				$this->request->redirect('/admin/messaging/custom_settings/' . $driver_provider[0] . '-' . $driver_provider[1]);
			}
		}
		$settings_ui = $provider->has_settings_ui();
		$this->template->body = View::factory('messaging_custom_settings');
		$this->template->body->php_to_include = $settings_ui['php'];
		$this->template->body->settings = $provider->load_settings();
	}

	public function action_receive_sync()
	{
		$driver_provider = $this->request->param('id');
		$driver_provider = explode('-', $driver_provider);

		$drivers = $this->mm->get_drivers();
		$provider = $drivers[$driver_provider[0]][$driver_provider[1]];
		$provider->receive_cron();
		$this->request->redirect('/admin/messaging/drivers');
	}

	public function action_signatures()
	{
        if (!Auth::instance()->has_access('messaging_edit')) {
            IbHelpers::set_message('You do not have access to the &quot;messaging&quot; feature', 'warning popup_box');
            $this->request->redirect('/admin');
        }

		$this->template->sidebar->breadcrumbs[]       = array('name' => 'Signatures', 'link' => '/admin/messaging/signatures');
		$this->template->body                         = View::factory('messaging_list_signatures');
		$this->template->sidebar->tools               = '<a href="/admin/messaging/signature/new"><button type="button" class="btn">Create Signature</button></a>';
		$this->template->body->signatures = Model_Signature::search();
	}

	public function action_signature()
	{
        if (!Auth::instance()->has_access('messaging_edit')) {
            IbHelpers::set_message('You do not have access to the &quot;messaging&quot; feature', 'warning popup_box');
            $this->request->redirect('/admin');
        }

		$id = $this->request->param('id');
		if (!is_numeric($id)) {
			$id = false;
		}

		$post = $this->request->post();
		if (isset($post['action'])) {
			if ($post['action'] == 'delete') {
				$data = array();
				$data['id'] = $id;
				Model_Signature::delete($data);
				$this->request->redirect('/admin/messaging/signatures');
			} else {
				$data = array();
				$data['title'] = $post['title'];
				$data['content'] = $post['content'];
				$data['format'] = $post['format'];
				if ($id) {
					$data['id'] = $id;
				}
				$id = Model_Signature::save($data);
				if ($post['action'] == 'save_and_exit') {
					$this->request->redirect('/admin/messaging/signatures');
				} else {
					$this->request->redirect('/admin/messaging/signature/' . $id);
				}
			}
		}
		$signature = Model_Signature::get($id);
		$save_result = null;

		$this->template->sidebar->breadcrumbs[]       = array('name' => 'Signatures', 'link' => '/admin/messaging/signatures');
		$this->template->sidebar->breadcrumbs[]       = array('name' => ($signature ? 'Edit ' . $signature['title']  : 'New Signature'), 'link' => '/admin/messaging/signature/' . $id);
		$this->template->body                         = View::factory('messaging_edit_signature');
		$this->template->body->signature              = $signature;
		$this->template->body->save_result            = $save_result;
	}

	public function action_ajax_filter_contacts()
	{
		$filters = $this->request->query();
        $this->auto_render = false;
        $this->response->headers('Content-type', 'application/json; charset=utf-8');

        if (Model_Plugin::is_enabled_for_role('Administrator', 'contacts3')) {
			$contacts = Model_Contacts3::search_messaging($filters['type'], $filters);
			$total = DB::select(DB::expr('@found_rows as total'))->execute()->get('total');

			$output['iTotalDisplayRecords'] = $total;
			$output['iTotalRecords']        = count($contacts);
			$output['aaData']               = array();

			foreach ($contacts as $contact) {
				$row = array();
				$allow_email = $allow_sms = $allow_phone = false;
				foreach ($contact['preferences'] as $preference) {
					if ($preference['stub'] == 'email' && $preference['value'] == 1) {
						$allow_email = true;
					}
					if ($preference['stub'] == 'text_messaging' && $preference['value'] == 1) {
						$allow_sms = true;
					}
					if ($preference['stub'] == 'phone_call' && $preference['value'] == 1) {
						$allow_phone = true;
					}

				}

				$td = '';
				if ($allow_email) {
					$td .= '<span class="icon-envelope ml-1 mr-1"></span>';
				}
				if ($allow_sms) {
					$td .= '<span class="icon-mobile ml-1 mr-1"></span>';
				}
				if ($allow_phone) {
					$td .= '<span class="icon-phone ml-1 mr-1"></span>';
				}
				$notification = (@$filters['type'] === 'sms') ? $contact['mobile'] : $contact['email'];
				$row[] = $td;
				$row[] = '<span class="messaging-search-contact-name">' . $contact['first_name'] . ' ' . $contact['last_name'] . '</span>';
				$row[] = '<span class="messaging-search-contact-notification">' . $notification . '</span>';
				$row[] = '<span class="messaging-search-contact-primary">' . $contact['type'] . '</span>';

				$row[] = Form::ib_checkbox(null, 'contact_ids[]', $contact['id'], false,
					['class' => 'messaging-compose-search-select_contact', 'data-category' =>  $contact['rtype'],
						'data-label' => "{$contact['first_name']} {$contact['last_name']}",
						'data-email' => $contact['email'], 'data-sms' => $contact['mobile'],
						'data-template_data_id' => (@$contact['template_field'] ? $contact[$contact['template_field']] : ''),
						'data-template_helper_function' => (@$contact['template_helper_function'] ? $contact['template_helper_function'] : ''),
						'data-primary_contact_id' => $contact['primary_contact']['id'],
                        'data-primary_contact_label' => $contact['primary_contact']['first_name'] . ' ' . $contact['primary_contact']['last_name']]);
				$output['aaData'][] = $row;
			}
			$output['sEcho'] = intval($filters['sEcho']);
        } else {
			$output = [];
        }

        echo json_encode($output);
	}

	public function action_ajax_get_all_filter_contacts()
	{
		$this->auto_render = false;
		$this->response->headers('Content-type', 'application/json; charset=utf-8');
		$filters = $this->request->query();
		$output = (Model_Plugin::is_enabled_for_role('Administrator', 'contacts3')) ? Model_Contacts3::search_messaging($filters['type'], $filters) : [];

		echo json_encode($output);
	}
    public function action_outbox_whitelist()
    {
        if ($this->request->post('delete')) {
            $addresses = $this->request->post('address');
            foreach ($addresses as $address) {
                Model_Messaging::remove_recipient_whitelist($address);
            }
            $this->request->redirect('/admin/messaging/outbox_whitelist');
        }
        $whitelist = Model_Messaging::list_recipient_whitelist();
        $this->template->body = View::factory('messaging_list_outbox_whitelist')
            ->set('whitelist',  $whitelist);
    }

    public function action_convert_attachment_base64_to_file_id()
    {
        if (!Auth::instance()->has_access('messaging_edit')) {
            IbHelpers::set_message('You do not have access to the &quot;messaging&quot; feature', 'warning popup_box');
            $this->request->redirect('/admin');
        }
        $this->auto_render = false;
        set_time_limit(0);
        ignore_user_abort(true);

        $attachments = DB::select(
            'id'
        )
            ->from(Model_Messaging::ATTACHMENTS_TABLE)
            ->where('content_encoding', '=', 'base64')
            //->limit(1)
            ->execute()
            ->as_array();

        foreach ($attachments as $attachment) {
            $attachment = DB::select(
                '*'
            )
                ->from(Model_Messaging::ATTACHMENTS_TABLE)
                ->where('id', '=', $attachment['id'])
                //->limit(1)
                ->execute()
                ->current();
            $tmp_file = tempnam(Kohana::$cache_dir, 'attachment_');
            file_put_contents($tmp_file, base64_decode($attachment['content']));
            $dir_id = Model_Files::get_directory_id_r("/messaging/attachments");
            $file_name = 'content-' . date('YmdHis') . '-' . mt_rand(1000, 9999);
            $file_info = array
            (
                'name' => $file_name,
                'type' => mime_content_type($tmp_file),
                'size' => filesize($tmp_file),
                'tmp_name' => $tmp_file,
            );
            $file_id = Model_Files::create_file($dir_id, $file_name, $file_info);
            $attachment['content'] = $file_id;
            $attachment['encoding'] = 'file_id';
            DB::update(Model_Messaging::ATTACHMENTS_TABLE)
                ->set(
                    array(
                        'content' => $file_id,
                        'content_encoding' => 'file_id'
                    )
                )
                ->where('id', '=', $attachment['id'])
                ->execute();

            unset($attachment);
        }
    }

	public function action_test()
	{
		$this->auto_render = false;
		$this->response->headers('content-type', 'text/plain');


	}
}
?>
