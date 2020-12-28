<?php defined('SYSPATH') or die('No direct script access.');

require_once __DIR__ . '/drivers/interface.php';
require_once Kohana::find_file('vendor/', 'cron-expression/src/Cron/FieldInterface');
require_once Kohana::find_file('vendor/', 'cron-expression/src/Cron/AbstractField');
require_once Kohana::find_file('vendor/', 'cron-expression/src/Cron/HoursField');
require_once Kohana::find_file('vendor/', 'cron-expression/src/Cron/MinutesField');
require_once Kohana::find_file('vendor/', 'cron-expression/src/Cron/MonthField');
require_once Kohana::find_file('vendor/', 'cron-expression/src/Cron/DayOfMonthField');
require_once Kohana::find_file('vendor/', 'cron-expression/src/Cron/DayOfWeekField');
require_once Kohana::find_file('vendor/', 'cron-expression/src/Cron/FieldFactory');
require_once Kohana::find_file('vendor/', 'cron-expression/src/Cron/CronExpression');

class Model_Messaging extends Model
{
    const MESSAGES_TABLE = 'plugin_messaging_messages';
    const MESSAGE_TARGETS_TABLE = 'plugin_messaging_message_targets';
    const MESSAGE_FTARGETS_TABLE = 'plugin_messaging_message_final_targets';
	const ATTACHMENTS_TABLE = 'plugin_messaging_message_attachments';
    const READ_BY_TABLE = 'plugin_messaging_read_by';
    const WHITELIST_TABLE = 'plugin_messaging_outbox_whitelist';
    const SEND_ROLE_TABLE = 'plugin_messaging_roles_can_send';

	protected $drivers = null;
	public $schedule_miss_duration_seconds = 36000; // 10 hours
	const SCHEDULE_MISS_DURATION_SECONDS   = 36000; // 10 hours
	public $trigger_activity = true;

	public $tag_whitelist       = '<a><abbr><acronym><address><area><b><bdo><big><blockquote><br><button><caption><center><cite><code><col><colgroup><dd><del><dfn><dir><div><dl><dt><em><fieldset><font><form><h1><h2><h3><h4><h5><h6><hr><i><img><input><ins><kbd><label><legend><li><map><menu><ol><optgroup><option><p><pre><q><s><samp><select><small><span><strike><strong><sub><sup><table><tbody><td><textarea><tfoot><th><thead><u><tr><tt><u><ul><var>';
	public $attribute_whitelist = array('align', 'alt', 'bgColor', 'border', 'cellpadding', 'cellspacing', 'cite', 'color', 'cols', 'colspan', 'datetime', 'dir', 'height', 'hidden', 'href', 'hreflang', 'lang', 'reversed', 'rowspan', 'scope', 'sizes', 'span', 'spellcheck', 'src', 'scrset', 'start', 'style', 'summary', 'target', 'title', 'translate', 'width');
	public $override_recipients = false;
	public $override_recipient_email = '';
	public $override_recipient_sms = '';

	public $last_error = null;

	protected static $incoming_post_processors = array();

	public static function register_post_processor($pp)
	{
		self::$incoming_post_processors[] = $pp;
	}

	public static function execute_post_processors($message_id)
	{
		$message = DB::select('*')
				->from('plugin_messaging_messages')
				->where('id', '=', $message_id)
				->execute()
				->current();
		if ($message) {
			$message['recipients'] = DB::select('*')
					->from('plugin_messaging_message_targets')
					->where('message_id', '=', $message_id)
					->execute()
					->as_array();
			$message['recipients_final'] = DB::select('f.*')
					->from(array('plugin_messaging_message_targets', 'r'))
						->join(array('plugin_messaging_message_final_targets', 'f'), 'inner')
							->on('r.id', '=', 'f.target_id')
					->where('message_id', '=', $message_id)
					->execute()
					->as_array();
			$message['attachments'] = DB::select('*')
					->from(self::ATTACHMENTS_TABLE)
					->where('message_id', '=', $message_id)
					->execute()
					->as_array();

			foreach (self::$incoming_post_processors as $pp) {
				$pp->process($message);
			}
		}
	}

	public static function get_recipient_providers()
	{
		static $providers = null;
		if($providers == null){
			$providers = array();
			foreach(DB::select('*')->from('plugin_messaging_recipient_providers')->order_by('priority', 'desc')->execute()->as_array() as $provider){
				$class_name = $provider['class_name'];
				$plugin = $provider['plugin'];
				@include_once ENGINEPATH . '/plugins/' . $plugin . '/development/classes/model/' . str_replace('model_', '', strtolower($class_name)) . '.php';
				@include_once PROJECTPATH . '/plugins/' . $plugin . '/development/classes/model/' . str_replace('model_', '', strtolower($class_name)) . '.php';
				$providers[$provider['id']] = new $class_name();
			}
		}
		return $providers;
	}

	public static function get_drivers_data()
	{
		$drivers_ = DB::select()->from('plugin_messaging_drivers')->order_by('driver', 'asc')->order_by('status', 'asc')->execute()->as_array();
		$drivers = array();
		foreach($drivers_ as $driver){
			$drivers[$driver['driver'] . ' ' . $driver['provider']] = $driver;
		}

        return $drivers;
	}
	
	public static function set_drivers_data($post)
	{
		foreach($post['status'] as $driver => $providers){
			foreach($providers as $provider => $status){
				$data = array();
				$data['status'] = $status;
				$data['is_default'] = @$post['default_provider'][$driver] == $provider && $status == 'ACTIVE' ? 'YES' : 'NO';
				DB::update('plugin_messaging_drivers')
					->set($data)
					->where('driver', '=', $driver)
					->and_where('provider', '=', $provider)
					->execute();
			}
		}
	}
	
	public static function insert_driver($driver)
	{
		return DB::insert('plugin_messaging_drivers', array_keys($driver))->values($driver)->execute();
	}

	public function get_drivers()
	{
		if($this->drivers == null){
            $this->override_recipients = Settings::instance()->get('messaging_override_recipients') == '1' ? true : false;
            $this->override_recipient_email = Settings::instance()->get('messaging_override_recipients_email');
            $this->override_recipient_sms = Settings::instance()->get('messaging_override_recipients_sms');

			$this->drivers = array();
			$drivers_data = self::get_drivers_data();
			
			$dirs = scandir(__DIR__ . '/drivers');
			$types = array();
			$drivers = array();
			foreach($dirs as $dir){
				if(is_dir(__DIR__ . '/drivers/' . $dir) && $dir != '.' && $dir != '..'){
					$types[] = $dir;
					$this->drivers[$dir] = array();
					$files = scandir(__DIR__ . '/drivers/' . $dir);
					foreach($files as $file){
						if(is_file( __DIR__ . '/drivers/' . $dir . '/' . $file ) && $file != '.' && $file != '..'){
							$provider = basename($file, '.php');
							require_once __DIR__ . '/drivers/' . $dir . '/' . $provider . '.php';
							$className = 'Model_Messaging_Driver_' . ucfirst( $dir ). '_' . ucfirst( $provider );
							$object = new $className();
							$this->drivers[$dir][$provider] = $object;
							if(!isset($drivers_data[$dir . ' ' . $provider])){
								self::insert_driver(array( 'driver' => $dir,
														'provider' => $provider,
														'is_default' => 'NO',
														'status' => 'ACTIVE' ));
								$object->setup();
							}
						}
					}
				}
			}
		}
		return $this->drivers;
	}
	
	public function get_active_provider($driver, $has_send, $has_receive)
	{
		$providers = DB::select('*')
						->from('plugin_messaging_drivers')
						->where('driver', '=', $driver)
						->and_where('status', '=', 'ACTIVE')
						->order_by(DB::expr("IF(is_default='YES', 1, 0)"), 'desc')
						->execute()
						->as_array();
        $this->get_drivers();
        foreach ($providers as $provider) {
            if ($this->drivers[$driver][$provider['provider']]->is_ready()) {
				if ($has_send && !$this->drivers[$driver][$provider['provider']]->has_send()) {
					continue;
				}
				if ($has_receive && !($this->drivers[$driver][$provider['provider']]->has_receive_cron() || $this->drivers[$driver][$provider['provider']]->has_receive_callback())) {
					continue;
				}
                return $provider;
            }
        }
		return false;
	}

	public static function set_status($driver, $provider, $status)
	{
	}

	public static function get_status($driver, $provider)
	{
	}

	public function search_messages($params = array())
	{
		DB::update('plugin_messaging_messages')
				->set(array('status' => 'SCHEDULE_MISSED'))
				->where('status', '=', 'SCHEDULED')
				->and_where('schedule', '<=', date('Y-m-d H:i:s', time() - $this->schedule_miss_duration_seconds))
				->execute();

        $query = DB::select(
            'messages.*',
            "target.custom_message",
            'drivers.driver', 'drivers.provider',
            array(DB::expr("IF(`sender`.`email` IS NOT NULL AND `sender`.`email` <> '', `sender`.`email`, `messages`.`sender`)"), 'from'),
            'ftarget.delivery_status'
        )
            ->from(array('plugin_messaging_messages', 'messages'))
            ->join(array('plugin_messaging_drivers', 'drivers'), 'left')
                ->on('messages.driver_id', '=', 'drivers.id')
            ->join(array('engine_users', 'sender'), 'LEFT')
                ->on('messages.sender', '=', 'sender.id')
                ->on('drivers.driver', '=', DB::expr("'email'"))
            ->join(array('plugin_messaging_message_targets', 'target'), 'left')
                ->on('target.message_id', '=', 'messages.id')
            ->join(array('plugin_messaging_message_final_targets', 'ftarget'), 'left')
                ->on('ftarget.target_id', '=', 'target.id');

        $query->where('messages.deleted', '=', 0);
        
		if(isset($params['status'])){
			$query->and_where('messages.status', 'in', $params['status']);
		}

        if(isset($params['sender'])){
            $query->and_where('messages.sender', '=', $params['sender']);
        }
        if(isset($params['driver'])){
            $query->and_where('drivers.driver', '=', $params['driver']);
        }

		if (isset($params['target_type']) AND isset($params['target']))
		{
            $recipient_providers = self::get_recipient_providers();
            $target_list = array();
            $warnings = array();
            if (isset($recipient_providers[$params['target_type']])){
                $target_to_resolve = array(
                    'driver'      => 'email',
                    'target_type' => 'EMAIL',
                    'target'      => $params['target'],
                    'target_id'   => null,
                );
                $recipient_providers[$params['target_type']]->resolve_final_targets($target_to_resolve, $target_list, $warnings);
            }

            $query
                ->and_where_open()
                    ->or_where_open()
                        ->and_where('target.target_type', '=', $params['target_type'])
                        ->and_where('target.target', '=', $params['target'])
                    ->or_where_close();


            if (empty($params['direct_mail_only']) && count($target_list)) {
                foreach ($target_list as $target) {
                    $query
                        ->or_where_open()
                            ->and_where('ftarget.target_type', '=', $target['target_type'])
                            ->and_where('ftarget.target', '=', $target['target'])
                        ->or_where_close();
                }
			}
            $query->and_where_close();
		}

		if(isset($params['created_before'])){
			$query->and_where('messages.date_created', '<=', $params['created_before']);
		}
		if(isset($params['created_after'])){
			$query->and_where('messages.date_created', '>=', $params['created_after']);
		}
		if(isset($params['created_before']) || isset($params['created_after'])){
			$query->order_by('messages.date_created', 'desc');
		}
		
		if(isset($params['sent_before'])){
			$query->and_where('messages.sent_completed', '<=', $params['sent_before']);
		}
		if(isset($params['sent_after'])){
			$query->and_where('messages.sent_completed', '>=', $params['sent_after']);
		}
		if(isset($params['sent_before']) || isset($params['sent_after'])){
			$query->order_by('messages.sent_completed', 'desc');
		}
		
		if(isset($params['scheduled_before'])){
			$query->and_where('messages.scheduled', '<=', $params['scheduled_before']);
		}
		if(isset($params['scheduled_after'])){
			$query->and_where('messages.scheduled', '>=', $params['scheduled_after']);
		}
		if(isset($params['scheduled_before']) || isset($params['scheduled_after'])){
			$query->order_by('messages.scheduled', 'desc');
		} else {
			$query->order_by('messages.id', 'desc');
		}
		
		if(isset($params['limit_start']) && isset($params['limit'])){
			$query->limit($params['limit_start'], $params['limit']);
		}

        $query->group_by('messages.id');

        if (isset($params['assoc']) && $params['assoc']) {
            $messages = $query->execute()->as_array($params['assoc']);
        } else {
            $messages = $query->execute()->as_array();
        }

        if (isset($params['attachments']) && $params['attachments']) {
            foreach ($messages as $key => $message) {
                $messages[$key]['attachments'] = DB::select('*')
                    ->from(self::ATTACHMENTS_TABLE)
                    ->where('message_id', '=', $message['id'])
                    ->execute()
                    ->as_array();
            }
        }

        return $messages;
	}

    /**
     * @param      $filters - dataTables API parameters
     * @param null $args - other parameters
     * @return array
     */
    public static function filter_messages($filters, $args = null)
	{
        $auth                = Auth::instance();
        $logged_in_user      = $auth->get_user();
        $parameters          = isset($filters['parameters']) ? is_string($filters['parameters']) ? json_decode($filters['parameters']) : (object) $filters['parameters'] : array();
        $show_everyones_mail = !empty($parameters->show_everyones_mail);

        if (isset($args['ignore_permissions']) && $args['ignore_permissions'] == true) {
            $access_all_mail = $access_system_mail = $access_others_mail = $access_own_mail = true;
        }
        else {
            $access_all_mail    = $auth->has_access('messaging_global_see_all');
            $access_system_mail = $auth->has_access('messaging_access_system_mail') || $access_all_mail;
            $access_others_mail = $auth->has_access('messaging_access_others_mail') || $access_all_mail;
            $access_own_mail    = $auth->has_access('messaging_access_own_mail')    || $access_all_mail;
        }

        // If the user attempts to view a specific user's mail and has permission
        if (!empty($args['user_id']) && is_numeric($args['user_id']) && ($access_others_mail || ($access_own_mail && $args['user_id'] == $logged_in_user['id']))) {
            $user = Model_Users::get_user($args['user_id']);
        }
        // If the user attempts to view "system" mail and has permission
        else if (isset($args['user_id']) && $args['user_id'] == 'system' && $access_system_mail) {
            $user = array('id' => -1, 'email' => Settings::instance()->get('default_email_sender'));
            $show_everyones_mail = true;
        }
        // If the user attempts to view "system" mail and has permission
        else if (isset($args['user_id']) && $args['user_id'] == 'all' && $access_all_mail) {
            $user = $logged_in_user;
            $show_everyones_mail = true;
        }
        elseif ($access_own_mail || $access_others_mail || $access_all_mail || $access_system_mail) {
            $user = $logged_in_user;
        }
        else {
            return array(
                'messages' => array(),
                'error_message' => 'Permission error accessing this mailbox.'
            );
        }

        $use_columns = array();

        if ( ! empty($args['datatable']))
        {
            if ( ! isset($filters['use_columns']))
            {
                $use_columns = array('actions', 'from', 'subject', 'to', 'folder', 'status', 'last_activity', 'info');
            }
            else
            {
                $use_columns = explode(',', $filters['use_columns']);
            }

            // Columns that can be searched. Use MySQL references. These will be used in WHERE clauses
            // These must be ordered, as they appear in the resultant table and there must be one per column
            $columns   = array();
            $column_aliases = array();
            if (in_array('actions', $use_columns))
                $columns[] = 'driver.driver';

            if (in_array('from', $use_columns)) {
                $expr = "IF (`message`.`sender` REGEXP '[0-9]+', `sender`.`email`, `message`.`sender`)";
                $columns[] = $expr;
                $column_aliases[$expr] = "from";
            }

            if (in_array('subject', $use_columns))
                $columns[] = 'message.subject';

            if (in_array('to', $use_columns)) {
                $expr = "IF (ftarget.target_type='CMS_USER', GROUP_CONCAT(`recipient`.`email` SEPARATOR ' '), GROUP_CONCAT(ftarget.target SEPARATOR ' '))";
                $columns[] = $expr;
                $column_aliases[$expr] = "to";
            }

            if (in_array('scheduled', $use_columns))
                $columns[] = 'message.schedule';

            if (in_array('folder', $use_columns))
            {
                $expr = "IF (`message`.`is_draft`,
					'drafts',
					IF ((`ftarget`.`target_type` ='CMS_USER' AND `ftarget`.`target` = ".$user['id'].") OR `ftarget`.`target` = '".$user['email']."',
						'inbox',
						IF ((`target`.`target_type` ='CMS_USER' AND `target`.`target` = ".$user['id'].") OR `target`.`target` = '".$user['email']."',
						'sent',
						'')
						)
					)";
                $columns[] = $expr;
                $column_aliases[$expr] = "folder";
            }

		    if (in_array('status', $use_columns))
			    $columns[] = 'message.status'; // status

            if (in_array('last_activity', $use_columns))
                $columns[] = 'message.date_updated';

            if (in_array('info', $use_columns))
                $columns[] = '';
        }

        $q = DB::select(
            DB::expr('SQL_CALC_FOUND_ROWS message.id'), 'message.subject', 'message.message', 'message.schedule', 'message.sender', 'message.date_created', 'message.date_updated', 'message.sent_started', 'message.sent_completed', 'message.send_interrupted', 'message.schedule', 'message.status', 'message.is_draft', 'message.keep_in_outbox',
            'driver.driver', 'driver.provider', array('template.name', 'template'), array('type.summary', 'type'), 'ftarget.delivery_status', 'target.target_type',
            array('template.name', 'template'),
            array(DB::expr('IF (`starred`.`user_id` > 0, 1, 0)'), 'starred'),
            array(DB::expr("GROUP_CONCAT(IF (`ftarget`.`target_type`='CMS_USER', `recipient`.`email`, `ftarget`.`target`) SEPARATOR '<br />')"), 'to'),
            array(DB::expr("IF (`sender`.`email` is not null and `sender`.`email` <> '', `sender`.`email`, `message`.`sender`)"), 'from'),
            array(DB::expr("IF (`message`.`is_draft`,
					'Drafts',
					IF (INSTR(CONCAT('* ', GROUP_CONCAT(IF(`ftarget`.`target_type` = 'CMS_USER', `recipient`.`email`, `ftarget`.`target`) SEPARATOR '* '), '* '), '* ".$user['email']."') > 0,
						'Inbox',
						IF ((`target`.`target_type` ='CMS_USER' AND `message`.`sender` = ".$user['id'].") OR `sender`.`email` = '".$user['email']."',
						'Sent',
						'Other')
						)
					)"), 'folder'),
            'received_when_unavailable',
            array(DB::expr("IF (`read_by`.`user_id`, 1, 0)"), 'is_read')
        )
            ->from(array('plugin_messaging_messages', 'message'))
            ->join(array('plugin_messaging_drivers', 'driver'), 'left')->on('message.driver_id', '=', 'driver.id')
            ->join(array('plugin_messaging_message_stars', 'starred'), 'LEFT')
            ->on('starred.message_id', '=', 'message.id')
            ->on('starred.user_id',    '=', DB::expr($user['id']))
            ->join(array('plugin_messaging_message_targets',          'target'), 'LEFT')->on('target.message_id',        '=', 'message.id')
            ->join(array('plugin_messaging_message_final_targets',   'ftarget'), 'LEFT')->on('ftarget.target_id',        '=', 'target.id')
            ->join(array('plugin_messaging_notifications',      'notification'), 'LEFT')->on('notification.message_id',  '=', 'message.id')
            ->join(array('plugin_messaging_notification_templates', 'template'), 'LEFT')->on('notification.template_id', '=', 'template.id')
            ->join(array('plugin_messaging_notification_types',         'type'), 'LEFT')->on('template.type_id',         '=', 'type.id')
            ->join(array('engine_users',                           'recipient'), 'LEFT')->on('ftarget.target',           '=', 'recipient.id')
            ->join(array('engine_users',                              'sender'), 'LEFT')->on('message.sender',           '=', 'sender.id')
            ->join(array(self::READ_BY_TABLE,                        'read_by'), 'LEFT')->on('read_by.message_id',       '=', 'message.id')->on('read_by.user_id', '=', DB::expr($user['id']))

            ->where('message.deleted', '=', 0)
            ->group_by('message.id')
        ;

        // If the logged-in user does not have the "see all" permission, only show messages where they are the sender or receiver
        if ( ! $show_everyones_mail && !isset($parameters->targets) && !$access_all_mail)
        {
            $q->and_having_open();

                    // sent to the user
                    $q->having(DB::expr("INSTR(CONCAT('* ',GROUP_CONCAT(IF (`ftarget`.`target_type`='CMS_USER', `recipient`.`email`, `ftarget`.`target`) SEPARATOR '* '),'* '), '* ".$user['email']."*')"), '>', 0);
                    // sent by the user
                    $q->or_having('message.sender', 'in', array($user['email'], $user['id']));

                // These extra messages are not be shown, if a specific user is selected
                if ( ! isset($args['user_id']) || $args['user_id'] == 'all' || $args['user_id'] == '' || $args['user_id'] == 'system')
                {
                    if ($auth->has_access('messaging_view_system_email')) {
                        // Sent using the system email
                        $q->or_having('sender', '=', Settings::instance()->get('mandrill_from_email'));
                    }
                    if ($auth->has_access('messaging_view_system_sms')) {
                        // Sent using the system phone number
                        $q->or_having('sender', '=', Settings::instance()->get('twilio_phone_number'));
                    }
                }

            $q->and_having_close();
        }

        if (isset($filters['parameters'])) {
            if (isset($parameters->outbox) AND $parameters->outbox) {
                $q->and_where('message.keep_in_outbox', '=', 'YES');
            } else {
                $q->and_where('message.keep_in_outbox', '=', 'NO');
            }

            if (isset($parameters->inbox) AND $parameters->inbox)
            {
                $q->and_where('message.status', 'IN', array('SENT', 'RECEIVED'));

                if ( ! $show_everyones_mail || (isset($args['user_id']) && $args['user_id'] == 'system')) {
                    // See if the user is in the list of recipients
                    $q->and_having_open();
                    $q->having(DB::expr("INSTR(CONCAT('* ',GROUP_CONCAT(IF (`ftarget`.`target_type`='CMS_USER', `recipient`.`email`, `ftarget`.`target`) SEPARATOR '* '),'* '), '* ".$user['email']."*')"), '>', 0);

                    if (isset($args['user_id']) && $args['user_id'] == 'system') {
                        $q->or_having('template.name', 'is not', null);
                    }

                    $q->and_having_close();
                }
            }

            if (@$parameters->is_spam == 1) {
                $q->and_where('is_spam', '=', 1);
            } else {
                $q->and_where('is_spam', '=', 0);
            }

            if (isset($parameters->sent) AND $parameters->sent)
            {
                $q
                    ->and_where('message.status', '=', 'SENT')
                    ->and_where('message.sender', 'IN', array($user['id'], $user['email']));
            }

            if ( ! empty($parameters->message_type))
            {
               $q->and_where('driver.driver', '=', $parameters->message_type);
            }

            if (isset($parameters->status))
            {
                $q->and_where('message.status', '=', $parameters->status);
            }

            if (isset($parameters->scheduled))
            {
                $q
                    ->and_where('message.schedule', 'is not ', null)
                    ->and_where('message.sender', 'IN', array($user['id'], $user['email']));
            }

            if (isset($parameters->starred) AND $parameters->starred)
            {
                $q->and_where('starred.user_id', '>', 0);
            }

            if (isset($parameters->is_draft) && $parameters->is_draft)
            {
                $q
                    ->and_where('message.is_draft', '=', $parameters->is_draft)
                    ->and_where('message.sender', 'IN', array($user['id'], $user['email']));

                $is_draft = (int)$parameters->is_draft;
            }

            if (isset($parameters->targets) || isset($parameters->ftargets))
            {
                $q->and_where_open();
                if (isset($parameters->targets))
                foreach ($parameters->targets as $target) {
                    $q->or_where_open();
                        $q->where('target.target_type', '=', $target->target_type);
                        $q->and_where('target.target', '=', $target->target);
                    $q->or_where_close();
                }
                if (isset($parameters->ftargets))
                    foreach ($parameters->ftargets as $ftarget) {
                        $q->or_where_open();
                            $q->where('ftarget.target_type', '=', $ftarget->target_type);
                            $q->and_where('ftarget.target', '=', $ftarget->target);
                        $q->or_where_close();
                    }
                $q->and_where_close();
            }
        }

        if ( ! empty($args['datatable']))
        {
            // Global search
            if (isset($filters['sSearch']) AND $filters['sSearch'] != '')
            {
                $q->and_having_open();
                $q->or_having('message.message', 'like', '%' . $filters['sSearch'] . '%');
                for ($i = 0; $i < count($columns); $i++)
                {
                    if (isset($filters['bSearchable_'.$i]) AND $filters['bSearchable_'.$i] == "true" AND $columns[$i] != '')
                    {
                        //$q->or_where($columns[$i],'like','%'.$filters['sSearch'].'%');
                        if (@$column_aliases[$columns[$i]]) {
                            $q->or_having($column_aliases[$columns[$i]], 'like', '%' . $filters['sSearch'] . '%');
                        } else {
                            $q->or_having($columns[$i], 'like', '%' . $filters['sSearch'] . '%');
                        }
                    }
                }
                $q->and_having_close();
            }
            // Individual column search
            for ($i = 0; $i < count($columns); $i++)
            {
                if (isset($filters['bSearchable_'.$i]) AND $filters['bSearchable_'.$i] == "true" AND $filters['sSearch_'.$i] != '')
                {
                    if (@$column_aliases[$columns[$i]]) {
                        $q->having($column_aliases[$columns[$i]], 'like', '%' . $filters['sSearch_' . $i] . '%');
                    } else {
                        $q->and_where($columns[$i], 'like', '%' . $filters['sSearch_' . $i] . '%');
                    }
                }
            }
        }
        elseif (isset($parameters) && ! empty($parameters->search))
        {
            // Searching, when not using jQuery dataTables
            $q
                ->and_where_open()
                    ->where('message.subject', 'like', '%'.$parameters->search.'%')
                    ->or_where(DB::expr("IF (`sender`.`email` is not null and `sender`.`email` <> '', `sender`.`email`, `message`.`sender`)"), 'like', '%'.$parameters->search.'%')
                ->and_where_close();
        }

        // $q_all will be used to count the total number of records.
        // It's largely the same as the main query, but won't be paginated
        //echo $q;exit;
        $q_all = clone $q;
        $q_all_unread = clone $q;
        $q_all_unread->having('is_read', '!=', 1);

        if (!isset($filters['iDisplayLength'])) {
            $filters['iDisplayLength'] = 100;
        }
        // Limit. Only show the number of records for this paginated page
        if (isset($filters['iDisplayLength']) AND $filters['iDisplayLength'] != -1)
        {
            $q->limit(intval($filters['iDisplayLength']));
            if (isset($filters['iDisplayStart']))
            {
                $q->offset(intval($filters['iDisplayStart']));
            }
        }
        // Order
        if (isset($filters['iSortCol_0']) AND $filters['iSortCol_0'])
        {
            for ($i = 0; $i < $filters['iSortingCols']; $i++)
            {
                if ($columns[$filters['iSortCol_'.$i]] != '')
                {
                    $q->order_by($columns[$filters['iSortCol_'.$i]], $filters['sSortDir_'.$i]);
                }
            }
        }
        $q->order_by('message.date_created', 'desc');

        $results = $q->execute()->as_array();
        $cnt = DB::select(DB::expr('found_rows() as cnt'))->execute()->get('cnt');


        $rp = self::get_recipient_providers();
        foreach ($results as $i => $result) {
            $recipients = DB::select('t.*', array('ft.target', 'ftarget'))
                ->from(array('plugin_messaging_message_targets', 't'))
                    ->join(array('plugin_messaging_message_final_targets', 'ft'), 'left')->on('t.id', '=', 'ft.target_id')
                ->where('t.message_id', '=', $result['id'])
                ->execute()
                ->as_array();
            $to = array();
            $cc = array();
            $bcc = array();
            foreach ($recipients as $recipient) {
                if ($recipient['x_details'] == '') {
                    $recipient['x_details'] = 'to';
                }
                $a = $recipient['x_details'];
                $ftarget = $recipient['ftarget'];
                if (isset($rp[$recipient['target_type']])) {
                    $recipient = $rp[$recipient['target_type']]->get_by_id($recipient['target']);
                    if ($recipient) {
                        array_push($$a, $recipient['label'] . '<' . $ftarget . '>');
                    }
                } else {
                    array_push($$a, $ftarget);
                }
            }

            $results[$i]['to'] = implode('<br />', $to);
            $results[$i]['cc'] = implode('<br />', $cc);
            $results[$i]['bcc'] = implode('<br />', $bcc);
        }

        $q_all_unread->execute();
        $all_cnt = DB::select(DB::expr('found_rows() as cnt'))->execute()->get('cnt');
        $q_all_unread->execute();
        $unread_cnt = DB::select(DB::expr('found_rows() as cnt'))->execute()->get('cnt');

        return array(
            'messages'        => $results,
            'offset'          => isset($filters['iDisplayStart']) ? $filters['iDisplayStart'] : 0,
            'total_displayed' => $cnt,
            'total_unread'    => $unread_cnt,
            'total_all'       => $all_cnt,
            'use_columns'     => $use_columns
        );
    }

    public static function count_unread($filters, $args = null)
    {
        $messages = self::filter_messages($filters, $args);
        return isset($messages['total_unread']) ? $messages['total_unread'] : 0;
    }

	public static function get_for_datatable($filters)
	{
        $popout_enabled    = Settings::instance()->get('messaging_popout_menu');
        $filtered_messages = self::filter_messages($filters, array('datatable' => TRUE));
        $results           = $filtered_messages['messages'];
        $use_columns       = $filtered_messages['use_columns'];
        $output            = array(
            'iTotalDisplayRecords' => $filtered_messages['total_all'],
            'iTotalRecords'        => $filtered_messages['total_displayed'],
            'aaData'               => array()
        );

		$icons      = array('dashboard' => 'bell-o', 'email' => 'envelope-o', 'sms' => 'mobile');
        $parameters = (isset($filters['parameters'])) ? $parameters = json_decode($filters['parameters']) : NULL;
        $parameters = is_string($parameters) ? json_decode($parameters) : (object) $parameters;

		if ($parameters AND isset($parameters->scheduled))
		{
			$templates = self::notification_template_list();
			foreach($templates as $template){
				if($template['send_interval'] == ''){
					continue;
				}
				$row = array();
				
				if (in_array('actions', $use_columns))
				{
					$driver   = '<span class="sr-only">'.$template['driver'].'</span><span class="icon-'.$icons[strtolower($template['driver'])].'" title="'.$template['driver'].'"></span>';
					$row[]    = '<span class="messaging-actions">'.$driver.'</span>';
				}
	
				if (in_array('from', $use_columns))
					$row[] = $template['sender'];
	
				if (in_array('subject', $use_columns))
					$row[] = $template['subject'];
	
				if (in_array('to', $use_columns))
					$row[] = @$template['to'];
				
				if (in_array('scheduled', $use_columns)){
					$next_run = $template['send_interval'];
					$ce = Cron\CronExpression::factory($template['send_interval']);
					$ce->isDue();
					$next_run = $ce->getNextRunDate()->format('Y-m-d H:i:s');
					$row[] = $next_run;
				}
	
				if (in_array('folder', $use_columns))
					$row[] = '';
	
				if (in_array('status', $use_columns))
					$row[] = 'Wait Next Run';
	
				if (in_array('last_activity', $use_columns))
				{
					// If the "updated" date is empty, fallback to the "created" date
					$use_date = (in_array($template['date_updated'], array('', '0000-00-00 00:00:00'))) ? $template['date_created'] : $template['date_updated'];
					$row[]    = IbHelpers::relative_time_with_tooltip($use_date);
				}
	
				if (in_array('info', $use_columns))
				{
					$row[] = '<a
						href="/admin/messaging/notification_template?id='.$template['id'].'"
						class="sr-only message_details_link"
						>details</a>';
				}
				
				$output['aaData'][] = $row;
			}
		}

		// Data to appear in the outputted table cells
		foreach ($results as $result)
		{
			$row = array();

			if (in_array('actions', $use_columns))
			{
				$checkbox = '<label class="checkbox-tick-label"><input type="checkbox" data-id="'.$result['id'].'" /><span class="checkbox-tick-icon"></span></label>';
				$star     = '<label class="checkbox-star-label">
						<span class="hidden">'.($result['starred'] ? 1 : 0).'</span>
						<input type="checkbox" class="toggle_starred" data-id="'.$result['id'].'"'.($result['starred'] ? ' checked' : '' ).' />
						<span class="checkbox-star-icon"></span>
					</label>';
				$driver   = '<span class="sr-only">'.$result['driver'].'</span><span class="icon-'.$icons[$result['driver']].'" title="'.$result['driver'].'"></span>';
				$row[]    = '<span class="messaging-actions">'.$checkbox.$star.$driver.'</span>';
			}

			if (in_array('from', $use_columns))
				$row[] = $result['from'];

			if (in_array('subject', $use_columns))
				$row[] = $result['subject'];

			if (in_array('to', $use_columns))
				$row[] = $result['to'];
			
			if (in_array('scheduled', $use_columns))
				$row[] = $result['schedule'];

			if (in_array('folder', $use_columns))
				$row[] = $result['folder'];

			if (in_array('status', $use_columns))
                if ($result['keep_in_outbox'] == 'YES') {
                    $row[] = '<a class="btn send_outbox" data-message_id="' . $result['id'] . '">' . __('Send Now') . '</a>';
                } else {
                    $row[] = ucfirst(strtolower($result['status']));
                }

			if (in_array('last_activity', $use_columns))
			{
				// If the "updated" date is empty, fallback to the "created" date
				$use_date = (in_array($result['date_updated'], array('', '0000-00-00 00:00:00'))) ? $result['date_created'] : $result['date_updated'];
				$row[]    = IbHelpers::relative_time_with_tooltip($use_date);
			}

			if (in_array('info', $use_columns))
			{
				$row[] = '<a
					href="/admin/messaging/details?message_id='.$result['id'].'"
					class="sr-only message_details_link'.($result['is_draft'] ? ' view_draft' : '').'"
					>details</a>
				<button                        
                    type="button" 
                    class="btn-link" 
					data-id="'.$result['id'].'"
					data-read="'.($result['is_read']).'"
					data-toggle="popover" data-placement="bottom" data-trigger="focus click" data-html="true"
					data-content="<dl class=&quot;dl-horizontal&quot;>
							<dt>Created:    </dt><dd>'.htmlentities(IbHelpers::relative_time_with_tooltip($result['date_created'])).'</dd>
							<dt>Started:    </dt><dd>'.htmlentities(IbHelpers::relative_time_with_tooltip($result['sent_started'])).'</dd>
							<dt>Interrupted:</dt><dd>'.(($result['send_interrupted'] != '') ? $result['send_interrupted'] : 'n/a').'</dd>
							<dt>Scheduled:  </dt><dd>'.(($result['schedule'] != '') ? $result['schedule'] : 'n/a').'</dd>
							<dt>Type:       </dt><dd>'.(($result['type']     != '') ? $result['type']     : 'none').'</dd>
							<dt>Service:    </dt><dd>'.$result['provider'].'</dd>
							<dt>Template:   </dt><dd>'.(($result['template'] != '') ? $result['template'] : 'none').'</dd>' .
						($result['received_when_unavailable'] ? '<dt>Offline:</dt><dd>Yes</dd>' : '') . '
						</dl>
						"
					><span class="icon-info-circle"></span></button>';
			}

            if ($popout_enabled) {
                $view_popover = View::factory('snippets/message_popover')->set('message', $result);
                $row[] = '<button
                        type="button"
                        class="btn-link"
                        data-id="'.$result['id'].'"
                        data-toggle="popover" data-placement="top" data-trigger="click focus" data-html="true"
                        data-content="'.htmlentities($view_popover).'"
                        >
                        <span class="icon-eye"></span>
                    </button>';
            }
			
			$output['aaData'][] = $row;
		}
		

		return json_encode($output);

	}

	public function getAttachmentDetails($attachmentId)
	{
		$attachment = DB::select('*')
				->from(self::ATTACHMENTS_TABLE)
				->where('id', '=', $attachmentId)
				->execute()
				->current();
		return $attachment;
	}

	public function get_message_details($message_id, $more_target_info = false)
	{
		$recipient_providers = self::get_recipient_providers();
		$details = DB::select('m.*', DB::expr('IFNULL(u.email, m.sender) as sender_d'))
						->from(array('plugin_messaging_messages', 'm'))
							->join(array('engine_users', 'u'), 'left')->on('m.sender', '=', 'u.id')
						->where('m.id', '=', $message_id)
						->execute()
						->as_array();
		if($details){
			$details = $details[0];
		} else {
			return false;
		}
		$details['driver'] = DB::select('*')
									->from('plugin_messaging_drivers')
									->where('id', '=',$details['driver_id'])
									->execute()
									->as_array();
		$details['driver'] = $details['driver'][0];
		$target_d = array();
		$target_d[] = 'IF(t.target_type = \'EMAIL\', t.target, \'\')';
		$target_d[] = 'IF(t.target_type = \'PHONE\', t.target, \'\')';
		foreach($recipient_providers as $recipient_provider){
			$column = $recipient_provider->message_details_column();
			if ($column) {
				$target_d[] = $column;
			}
		}
		$target_d = 'CONCAT_WS(\'\',' . implode(', ', $target_d) . ') as target_d';
		$query = DB::select('t.*', DB::expr($target_d))
						->from(array('plugin_messaging_message_targets', 't'));
		foreach($recipient_providers as $recipient_provider){
			$recipient_provider->message_details_join($query);
		}
		$details['targets'] = $query->where('t.message_id', '=', $message_id)
									->and_where('t.deleted', '=', 0)
									->execute()
									->as_array();
		if($more_target_info){
			foreach($details['targets'] as $i => $target){
				if(isset($recipient_providers[$target['target_type']])){
					$target_details = $recipient_providers[$target['target_type']]->get_by_id($target['target']);
					$details['targets'][$i] = array_merge($target_details, $target);
				} else {
					$details['targets'][$i]['label'] = $details['targets'][$i]['target_d'] = $target['target'];
				}
			}
		}
		$details['final_targets'] = DB::select('plugin_messaging_message_final_targets.*',
												'plugin_messaging_message_targets.custom_subject',
												'plugin_messaging_message_targets.custom_message',
												array('plugin_messaging_message_final_targets.target', 'ftarget'))
									->from('plugin_messaging_message_final_targets')
										->join('plugin_messaging_message_targets', 'inner')
										->on('plugin_messaging_message_targets.id', '=', 'plugin_messaging_message_final_targets.target_id')
									->where('message_id', '=', $message_id)
									->execute()
									->as_array();
        $details['attachments'] = DB::select('*')
            ->from(self::ATTACHMENTS_TABLE)
            ->where('message_id', '=', $message_id)
            ->execute()
            ->as_array();
		return $details;
	}

	public function process_scheduled_messages()
	{
		$this->get_drivers();
		DB::update('plugin_messaging_messages')
				->set(array('status' => 'SCHEDULE_MISSED'))
				->where('status', '=', 'SCHEDULED')
				->and_where('schedule', '<=', date('Y-m-d H:i:s', time() - $this->schedule_miss_duration_seconds))
				->execute();

		$messages = DB::select('id')
						->from('plugin_messaging_messages')
						->where('status', '=', 'SCHEDULED')
						->and_where('schedule', '<=', date('Y-m-d H:i:s'))
						->execute()
						->as_array();
		$result = array();
		foreach($messages as $message){
			$result[$message['id']] = $this->process_message($message['id']);
		}
		return $result;
	}

	public function process_scheduled_reports()
	{
		$this->get_drivers();
		$reports = DB::select('*')
						->from('plugin_reports_reports')
						->where('bulk_message_interval', '<>', '')
						->execute()
						->as_array();
		foreach($reports as $report){
			
		}
		print_r($reports);die();
	}

	public function process_report($report)
	{
		
	}

	public function process_message($message_id, $show_progress = false, $outbox_send = false)
	{
		$this->get_drivers();
		try{
            $details = $this->get_message_details($message_id);
			$driver = $details['driver']['driver'];
			$provider = $details['driver']['provider'];
			if (!$this->drivers[$driver][$provider]->is_ready()) {
				throw new Exception($driver . "->" . $provider . " is not ready");
			}
			DB::update('plugin_messaging_messages')
				->set(array('status' => 'SENDING', 'sent_started' => date('Y-m-d H:i:s')))
				->where('id', '=', $message_id)
				->execute();
			if($show_progress){
				$target_count = count($details['final_targets']);
				printf($target_count . " messages to send\n");
			}
            
            $attachments = array();
            foreach ($details['attachments'] as $attachment) {
                unset($attachment['id']);
                unset($attachment['message_id']);
                if ($attachment['content_encoding'] == 'base64') {
                    $attachment['content'] = base64_decode($attachment['content']);
                }
                if ($attachment['content_encoding'] == 'file_id') {
                    $attachment['content'] = Model_Files::getFileContent($attachment['content']);
                }
                unset($attachment['content_encoding']);
                $attachments[] = $attachment;
            }
            unset($details['attachments']); //to save memory in case of big files

            $all_sent = true;
			foreach($details['final_targets'] as $i => $final_target){
				if($show_progress){
					printf("sending %5d of %5d\r", $i + 1, $target_count);
				}
				$ftresult = array();
				try {
                    if ($details['keep_in_outbox'] == 'YES' && $outbox_send == false) {
                        if (!self::test_recipient_whitelist($final_target['target']) && $outbox_send == false) {
                            $all_sent = false;
                            continue;
                        }
                    }
                    if ($details['keep_in_outbox'] == 'YES' && $outbox_send == true) {
                        self::add_recipient_whitelist($final_target['target']);
                    }

                    $subject = $final_target['custom_subject'] ? $final_target['custom_subject'] : $details['subject'];
					$message = $final_target['custom_message'] ? $final_target['custom_message'] : $details['message'];
					$replyto = $details['replyto'];

					unset($final_target['custom_subject']);
					unset($final_target['custom_message']);
					//echo "send:", $final_target['target'], $subject, $message, "\n";
                    if ($this->override_recipients && $this->override_recipient_email) {
                        if ($driver == 'email') {
                            $message = $message . "\noverridden to:" . $final_target['target'];
                            $final_target['target'] = $this->override_recipient_email;
                        }
                        if ($driver == 'sms' && $this->override_recipient_sms) {
                            $message = $message . "to:" . $final_target['target'];
                            $final_target['target'] = $this->override_recipient_sms;
                        }
                    }

                    $send_result = $this->drivers[$driver][$provider]->send(null, $final_target['target'], $subject, $message, $attachments, $replyto);
					//print_r($send_result);
					if($send_result['status']){
						$ftresult['delivery_status'] = $send_result['status'];
						$ftresult['delivery_status_details'] = @$send_result['details'];
					}
					$ftresult['driver_remote_id'] = $send_result['id'];
				} catch(Exception $e){
					$ftresult['delivery_status'] = 'ERROR';
					$ftresult['delivery_status_details'] = $e->getMessage() . ':' . $e->getFile() . ':' . $e->getLine();
					print_r($e);
				}
				DB::update('plugin_messaging_message_final_targets')->set($ftresult)->where('id', '=', $final_target['id'])->execute();
			}
            $message_update = array('status' => 'SENT', 'sent_completed' => date('Y-m-d H:i:s'));
            if ($details['keep_in_outbox'] == 'YES') {
                if ($all_sent == true || $outbox_send == true) {
                    $message_update = array('keep_in_outbox' => 'NO');
                } else {
                    $message_update = array('keep_in_outbox' => 'YES');
                }
            }
			DB::update('plugin_messaging_messages')
				->set($message_update)
				->where('id', '=', $message_id)
				->execute();
			
			if($this->trigger_activity){
				$activity_action = 'send';
				if (in_array($driver, array('sms', 'email'))) {
					$activity_action = $driver;
				}
				$activity = new Model_Activity;
				$activity->set_action($activity_action)->set_item_type('message')->set_item_id($message_id)->save();
			}

			return true;
		} catch(Exception $exc){
			print_r($exc);
			if($show_progress){
				
			}
			DB::update('plugin_messaging_messages')
				->set(array('status' => 'INTERRUPTED', 'send_interrupted' => date('Y-m-d H:i:s')))
				->where('id', '=', $message_id)
				->execute();
			return false;
		}
	}

	public function send_report($report, $schedule = null, $attachments = array(), $report_data = null)
	{
		//echo $report_id . ":" . $schedule . "\n";
		if(is_numeric($report)){
			$report = DB::select('*')
						->from('plugin_reports_reports')
						->where('id', '=', $report)
						->execute()
						->as_array();
			$report = $report[0];
		}
		if($report['bulk_message_sms_number_column']){
			$driver = 'sms';
		} else {
			$driver = 'email';
		}
		$provider_details = $this->get_active_provider($driver, true, null);
		
		$m_report = new Model_Reports($report['id']);
		$m_report->get(true);
		if (isset($_POST['sql'])) {
			$m_report->set_sql($_POST['sql']);
		}
		if (isset($_POST['parameters'])) {
			$m_report->set_parameters($_POST['parameters']);
			$m_report->set_parameters($m_report->prepare_parameters());
		}
        if ($report_data === null) {
            $report_data = $m_report->run_report();
        }

		$targets = array();
		foreach($report_data as $row){
			$target = array();
			if($report['bulk_message_sms_number_column']){
				if($row[$report['bulk_message_sms_number_column']] == ''){
					continue;
				}
				$target['target_type'] = 'PHONE';
				$target['target'] = $row[$report['bulk_message_sms_number_column']];
			} else {
				if($row[$report['bulk_message_email_column']] == ''){
					continue;
				}
				$target['target_type'] = 'EMAIL';
				$target['target'] = $row[$report['bulk_message_email_column']];
			}
			if($report['bulk_message_subject_column']){
				$target['subject'] = $row[$report['bulk_message_subject_column']];
			}
			if($report['bulk_message_body_column']){
				$target['message'] = $row[$report['bulk_message_body_column']];
			}
			if ($report['bulk_message_body']) {
				$msg = $report['bulk_message_body'];
				foreach ($row as $key => $val) {
					$msg = str_ireplace('{' . $key . '}', $val, $msg);
				}
				if ($msg != $report['bulk_message_body']) {
					$target['message'] = $msg;
				}

			}
			$targets[] = $target;
		}

		if(count($targets) > 0){
			$message = $report['bulk_message_body'];
            if (count($attachments) > 0){
                $message = array(
                    'content' => $message,
                    'attachments' => $attachments
                );
            }
			$subject = $report['bulk_message_subject'] ? $report['bulk_message_subject'] : $report['name'];
            $message_per_minute = (int)Settings::instance()->get('messaging_send_message_per_minute') ?: (int)$report['bulk_messages_per_minute'];
            if ($message_per_minute > 0) {
                $sleep_seconds = 60 / $message_per_minute;
                foreach ($targets as $target) {
                    $message = $target['message'];
                    $message_id = $this->send($driver, $provider_details['provider'], null, array($target), $message, $subject, $schedule);
                    $notification = array();
                    $notification['report_id'] = $report['id'];
                    $notification['message_id'] = $message_id;
                    DB::insert('plugin_messaging_report_notifications', array_keys($notification))->values($notification)->execute();
                    usleep($sleep_seconds * 1000000);
                }
            } else {
                $message_id = $this->send($driver, $provider_details['provider'], null, $targets, $message, $subject, $schedule);
                $notification = array();
                $notification['report_id'] = $report['id'];
                $notification['message_id'] = $message_id;
                DB::insert('plugin_messaging_report_notifications', array_keys($notification))->values($notification)->execute();
            }
		}
	}

    public static function render_template($message, $params = array())
    {
        $params['base_url'] = URL::site(); // standard parameter

        foreach ($params as $param => $value) {
            $use_html = false;

            // The parameter value may be formatted ['value' => $string, 'html' => $boolean] to allow it to contain raw HTML
            // e.g. ['value' => '<strong>Hello</strong>', 'html' => true]
            if (is_array($value) && array_key_exists('value', $value)) {
                $use_html = !empty($value['html']);
                $value = $value['value'];
            }

            // Replace special characters with entities to prevent HTML/JS injection.
            // Don't apply this when the code opts to specifically allow for HTML.
            $value = $use_html ? $value : (is_string($value) ? htmlentities($value) : $value);
            $value = empty($value) ? '' : $value;

            // Replace variable names with values
            if (is_string($value) || is_numeric($value)) {
                $message = str_replace('$' . $param, $value, $message);
                $message = str_replace('@' . $param . '@', $value, $message);
            }
        }
        return $message;
    }

	public function generate_template($template_id, $message, $template_parameters = array())
	{
		$template = $this->get_notification_template($template_id);
		if($template){
			$attachments = array();
			if (is_array($message)) {
				if (isset($message['attachments'])) {
					$attachments = $message['attachments'];
				}
				if (isset($message['content'])) {
					$message = $message['content'];
				} else {
					$message = null;
				}
			}
			if (count($template['attachments'])) {
				foreach($template['attachments'] as $attachment){
					if ($attachment['file_id']) {
						$attachments[] = array('file_id' => $attachment['file_id']);
					} else if($attachment['path']) {
						$attachments[] = array(
							'name' => $attachment['name'],
							'path' => $attachment['path'],
							'type' => $attachment['type']
						);
					} else {
						$attachments[] = array(
							'name' => $attachment['name'],
							'content' => $attachment['content'],
							'type' => $attachment['type']
						);
					}
				}
			}
			if ($template['doc_generate'] && $template['doc_helper'] && $template['doc_template_path'] && $template['doc_type']) {
				$docParameters = array();
				$docValues = array();
				foreach (get_class_methods('Model_Docarrayhelper') as $docHelper) {
					if ($docHelper == $template['doc_helper']) {
						$rm = new ReflectionMethod('Model_Docarrayhelper', $docHelper);
						foreach ($rm->getParameters() as $param) {
							$docParameters[] = $param->getName();
						}
					}
				}
				$firstDocParam = null;
				foreach ($docParameters as $pname) {
					if ($firstDocParam == null) {
						$firstDocParam = '-' . $pname . '-' . $template_parameters[$pname];
					}
					$docValues[$pname] = $template_parameters[$pname];
				}
				$dah = new Model_Docarrayhelper();
				$docTemplateValues = call_user_func_array(array($dah, $template['doc_helper']), $docValues);
				$document = new Model_Document();
				$document->doc_gen_and_storage(
					Model_Files::getFileId($template['doc_template_path']),
					$docTemplateValues,
					$template['name'],
					preg_replace('/^[a-z0-9\-]/', '', $firstDocParam),
					isset($template['contact_id']) ? $template['contact_id'] : 0,
					'',
					1,
					$template['doc_type'] == 'PDF'
				);
				if (count($document->generated_documents)) {
					if ($template['doc_type'] == 'PDF' && @$document->generated_documents['url_pdf'] && file_exists(@$document->generated_documents['url_pdf'])){
						$attachments[] = array(
							'content' => file_get_contents($document->generated_documents['url_pdf']),
							'name' => basename($document->generated_documents['url_pdf']),
							'type' => 'application/pdf'
						);
					} else {
						if (file_exists($document->generated_documents['url_docx'])) {
							$attachments[] = array(
								'content' => file_get_contents($document->generated_documents['url_docx']),
								'name' => basename($document->generated_documents['url_docx']),
								'type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
							);
						}
					}
					if (file_exists(@$document->generated_documents['url_pdf'])) {
						unlink(@$document->generated_documents['url_pdf']);
					}
					if (file_exists(@$document->generated_documents['url_docx'])) {
						unlink(@$document->generated_documents['url_docx']);
					}
				}
			}
			if($message == null OR $template['overwrite_cms_message'] == 1)
			{
				if($template['page_id']){
					$message = Model_Pages::get_rendered_output($template['page_id']);
				} else {
					$message =  $template['message'];
				}
			}
			if(count($template_parameters)){
				$message = self::render_template($message, $template_parameters);
			}
			if ($attachments) {
				$message = array(
					'content' => $message,
					'attachments' => $attachments
				);
			}
			return $message;
		}

		return false;
	}

	public function send_template($template_id, $message, $schedule = null, $extra_targets = array(), $template_parameters = array(), $subject = null, $sender = null, $replyto = null)
	{
        $template = $this->get_notification_template($template_id);
		if($template){
            if (@$template['publish'] == 0) {
                return true;
            }
            $attachments = array();
            if (is_array($message)) {
				if (isset($message['attachments'])) {
					$attachments = $message['attachments'];
				}
				if (isset($message['content'])) {
					$message = $message['content'];
				} else {
					$message = null;
				}
            }
            if (count($template['attachments'])) {
                foreach($template['attachments'] as $attachment){
                    if ($attachment['file_id']) {
                        $attachments[] = array('file_id' => $attachment['file_id']);
                    } else if($attachment['path']) {
                        $attachments[] = array(
                            'name' => $attachment['name'],
                            'path' => $attachment['path'],
                            'type' => $attachment['type']
                        );
                    } else {
                        $attachments[] = array(
                            'name' => $attachment['name'],
                            'content' => $attachment['content'],
                            'type' => $attachment['type']
                        );
                    }
                }
            }
			if ($template['doc_generate'] && $template['doc_helper'] && $template['doc_template_path'] && $template['doc_type']) {
                $docParameters = array();
                $docValues = array();
                foreach (get_class_methods('Model_Docarrayhelper') as $docHelper) {
                    if ($docHelper == $template['doc_helper']) {
                        $rm = new ReflectionMethod('Model_Docarrayhelper', $docHelper);
                        foreach ($rm->getParameters() as $param) {
                            $docParameters[] = $param->getName();
                        }
                    }
                }
                $firstDocParam = null;
                foreach ($docParameters as $pname) {
                    if ($firstDocParam == null) {
                        $firstDocParam = '-' . $pname . '-' . $template_parameters[$pname];
                    }
                    $docValues[$pname] = $template_parameters[$pname];
                }
                $dah = new Model_Docarrayhelper();
                $docTemplateValues = call_user_func_array(array($dah, $template['doc_helper']), $docValues);
                $document = new Model_Document();
                $docGenerated = $document->doc_gen_and_storage(
                    Model_Files::getFileId($template['doc_template_path']),
                    $docTemplateValues,
                    $template['name'],
                    preg_replace('/^[a-z0-9\-]/', '', $firstDocParam),
                    isset($template['contact_id']) ? $template['contact_id'] : 0,
                    '',
                    1,
                    $template['doc_type'] == 'PDF'
                );
                if (count($document->generated_documents)) {
                    if ($template['doc_type'] == 'PDF' && @$document->generated_documents['url_pdf'] && file_exists(@$document->generated_documents['url_pdf'])){
                        $attachments[] = array(
                            'content' => file_get_contents($document->generated_documents['url_pdf']),
                            'name' => basename($document->generated_documents['url_pdf']),
                            'type' => 'application/pdf'
                        );
                    } else {
						if (file_exists($document->generated_documents['url_docx'])) {
							$attachments[] = array(
									'content' => file_get_contents($document->generated_documents['url_docx']),
									'name' => basename($document->generated_documents['url_docx']),
									'type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
							);
						}
                    }
                    if (file_exists(@$document->generated_documents['url_pdf'])) {
                        unlink(@$document->generated_documents['url_pdf']);
                    }
                    if (file_exists(@$document->generated_documents['url_docx'])) {
                        unlink(@$document->generated_documents['url_docx']);
                    }
                }
            }
            if($message == null OR $template['overwrite_cms_message'] == 1)
			{
				if($template['page_id']){
					$message = Model_Pages::get_rendered_output($template['page_id']);
				} else {
					$message =  $template['message'];
				}
			}

			$subject = is_null($subject) ? $template['subject'] : $subject;
			if ($replyto == null) {
				$replyto = $template['replyto'];
			}
            if (empty($replyto)) {
                $replyto = @Settings::instance()->get('default_email_sender');
            }

			if(count($template_parameters)){
				$message = self::render_template($message, $template_parameters);
				$subject = self::render_template($subject, $template_parameters);
				$subject = strip_tags($subject);
			}
            if ($template['signature']) {
				$message .= $template['signature']['content'];
			}

			if ($attachments) {
                $message = array(
                    'content' => $message,
                    'attachments' => $attachments
                );
            }
            $driver = strtolower($template['driver']);
			$provider_details = $this->get_active_provider($driver, true, null);
			$targets = array_unique(array_merge($template['targets'], $extra_targets), SORT_REGULAR);
			$sender  = is_null($sender)  ? $template['sender']  : $sender;
			if (empty($sender)) {
                $sender = @Settings::instance()->get('default_email_sender');
            }
            $targets = array();
            $target_check_list = array();
            if ($template['targets'])
            foreach ($template['targets'] as $target) {
                if (!isset($target_check_list[ $target['target_type'] . $target['target'] ])) {
                    $target_check_list[ $target['target_type'] . $target['target'] ] = true;
                    $targets[] = $target;
                }
            }
            if ($extra_targets)
            foreach ($extra_targets as $target) {
                if (!isset($target_check_list[ $target['target_type'] . $target['target'] ])) {
                    $target_check_list[ $target['target_type'] . $target['target'] ] = true;
                    $targets[] = $target;
                }
            }
			$message_id = $this->send($driver, $provider_details['provider'], $sender, $targets, $message, $subject, $schedule, 0, 'new', array(), $replyto);
			if($message_id){
                DB::update('plugin_messaging_notification_templates')->set(['last_sent' => date("Y-m-d H:i:s")])->where('id', '=', $template['id'])->execute();
                if (is_array($message_id)) {
                    foreach ($message_id as $mid) {
                        $notification = array('template_id' => $template['id'], 'message_id' => $mid);
                        $notification_result = DB::insert('plugin_messaging_notifications')->values($notification)->execute();
                    }
                } else {
                    $notification = array('template_id' => $template['id'], 'message_id' => $message_id);
                    $notification_result = DB::insert('plugin_messaging_notifications')->values($notification)->execute();
                }

	
				if($this->trigger_activity){
					$activity = new Model_Activity;
					$activity->set_action('create')->set_item_type('notification')->set_item_id($notification_result[0]['id'])->save();
				}
				return $notification_result[0];
			} else {
				return false;
			}
		} else {
			return false;
		}
	}

	public static function getMessageIdFromNotification($id)
	{
		return DB::select('message_id')
			->from('plugin_messaging_notifications')
			->where('id', '=', $id)
			->execute()
			->get('message_id');
	}

	/*
	 * $message can be a string or
	 * an array like {content:'some message', attachments:[{attachment},...]}
	 * {attachment} can be {name:"abc.txt", type: "text/plain", file_id:321, path: "/var/www/abc.txt", "content": "hello world"}
	 * only one of file_id, path or content needs to be set
	 * */
	public function send($driver, $provider, $from, $target_list, $message, $subject, $schedule = null, $is_draft = 0, $message_id = 'new', $remove_targets = array(), $replyto = null, $reply_to_message_id = null, $allow_reply = null)
	{
        $message_per_minute = (int)Settings::instance()->get('messaging_send_message_per_minute');
        if (count($target_list) > 1 && $message_per_minute > 0) {
            $ids = array();
            $sleep_seconds = 60 / $message_per_minute;
            if (is_null($from) && $driver == 'email') {
                $from = @Settings::instance()->get('default_email_sender');
            }
            foreach ($target_list as $target) {
                $ids[] = $this->send($driver, $provider, $from, array($target), $message, $subject, $schedule = null, $is_draft, $message_id, $remove_targets, $replyto, $reply_to_message_id, $allow_reply);
                usleep($sleep_seconds * 1000000);
            }
            return $ids;
        }
		$this->last_error = null;
		//header('content-type: text/plain; charset=utf-8');print_r(func_get_args());die();
		$this->get_drivers();
		if ($provider == null || $provider == 'default') {
			$defaultProvider = $this->get_active_provider($driver, true, null);
			$provider = $defaultProvider['provider'];
		}
		$recipient_providers = self::get_recipient_providers();
		$recipient_provider_ids = array_keys($recipient_providers);
		Database::instance()->begin();
		$imessage = null;
        $warnings = array();
        try{
			$driver_id = DB::select('id')
								->from('plugin_messaging_drivers')
								->where('driver', '=', $driver)
								->and_where('provider', '=', $provider)
								->and_where('status', '=', 'ACTIVE')
								->execute()
								->get('id');
			$logged_in_user = Auth::instance()->get_user();
			$imessage = array();
			$imessage['driver_id'] = $driver_id;
			$imessage['sender'] = $from;
			$imessage['replyto'] = $replyto;
			$imessage['subject'] = $subject != null ? $subject : '';

			if (is_array($message)) {
				$realMessage = $message['content'];
			} else {
				$realMessage = (string)$message;
			}

            if ($driver == 'email') {
                $realMessage = self::apply_wrapper($realMessage);
            }

			$imessage['message'] = $realMessage;
			$imessage['created_by'] = $logged_in_user ? $logged_in_user['id'] : 1;
			$imessage['date_created'] = $imessage['date_updated'] = date('Y-m-d H:i:s');
			$imessage['is_draft'] = $is_draft;
			if($is_draft){
				$imessage['status'] = 'DRAFTED';
			} else {
				if($schedule == null){
					$imessage['status'] = 'SENDING';
					$imessage['sent_started'] = date('Y-m-d H:i:s');
				} else {
					$imessage['schedule'] = $schedule;
					$imessage['status'] = 'SCHEDULED';
				}
			}

			$iplimitAllSeconds = (int)Settings::instance()->get('messaging_ip_all_msg_limit_seconds');
			$iplimitAllNumber = (int)Settings::instance()->get('messaging_ip_all_msg_limit_number');
            $iplimitSameSeconds = (int)Settings::instance()->get('messaging_ip_same_msg_limit_seconds');
            $iplimitSameNumber = (int)Settings::instance()->get('messaging_ip_same_msg_limit_number');

            if ((!$logged_in_user || !$logged_in_user['id']) && php_sapi_name() != 'cli') {
                $iplimitAllCnt = DB::select(DB::expr('COUNT(*) AS cnt'))
                    ->from('plugin_messaging_messages')
                    ->where('ip_address', '=', Request::$client_ip)
                    ->and_where('date_created', '>=', date('Y-m-d H:i:s', time() - $iplimitAllSeconds))
                    ->execute()
                    ->get('cnt');
                if ($iplimitAllCnt >= $iplimitAllNumber) {
                    Database::instance()->commit();
                    return false;
                }

                $iplimitSameCnt = DB::select(DB::expr('COUNT(*) AS cnt'))
                    ->from('plugin_messaging_messages')
                    ->where('message', '=', $realMessage)
                    ->and_where('ip_address', '=', Request::$client_ip)
                    ->and_where('date_created', '>=', date('Y-m-d H:i:s', time() - $iplimitSameSeconds))
                    ->execute()
                    ->get('cnt');
                if ($iplimitSameCnt >= $iplimitSameNumber) {
                    Database::instance()->commit();
                    return false;
                }
            }

			if(!is_numeric($message_id)){
                $form_data = $_POST;

                // Don't save credit card details
                if (isset($form_data['ccNum']))   unset($form_data['ccNum']);
                if (isset($form_data['ccv']))     unset($form_data['ccv']);
                if (isset($form_data['ccExpMM'])) unset($form_data['ccExpMM']);
                if (isset($form_data['ccExpYY'])) unset($form_data['ccExpYY']);

                if (isset($form_data['checkout']))
                {
                    $checkout_decoded = json_decode($form_data['checkout']);
                    $is_json = (json_last_error() == JSON_ERROR_NONE);
                    $checkout_decoded = $is_json ? (array) $checkout_decoded : $form_data['checkout'];

                    if (isset($checkout_decoded['ccNum']))   unset($checkout_decoded['ccNum']);
                    if (isset($checkout_decoded['ccv']))     unset($checkout_decoded['ccv']);
                    if (isset($checkout_decoded['ccExpMM'])) unset($checkout_decoded['ccExpMM']);
                    if (isset($checkout_decoded['ccExpYY'])) unset($checkout_decoded['ccExpYY']);

                    $form_data['checkout'] = $is_json ? json_encode($checkout_decoded) : $checkout_decoded;
                }

                $form_data = ibhelpers::clear_cc($form_data);

				$imessage['form_data']  = json_encode($form_data);
				$imessage['ip_address'] = Request::$client_ip;
				$imessage['user_agent'] = Request::$user_agent;

				if (isset($this->drivers[$driver][$provider])) {
					if (!$this->drivers[$driver][$provider]->is_ready()) {
						$imessage['status'] = 'FAILED';
						$this->last_error = __('Messaging driver is not ready');
					}
				} else {
					$imessage['status'] = 'FAILED';
					$this->last_error = __('No messaging driver is set');
				}
				if(!$driver_id){
					$imessage['status'] = 'FAILED';
					$this->last_error = __('Unable to send');
				}

                if (Settings::instance()->get('messaging_keep_outbox')) {
                    $imessage['keep_in_outbox'] = 'YES';
                }

                $imessage['reply_to_message_id'] = $reply_to_message_id;
                $imessage['allow_reply'] = $allow_reply;

				$message_result = DB::insert('plugin_messaging_messages', array_keys($imessage))->values($imessage)->execute();
				$imessage['id'] = $message_id = $message_result[0];
			} else {
				$message_result = DB::update('plugin_messaging_messages')->set($imessage)->where('id', '=', $message_id)->execute();
				$imessage['id'] = $message_id;
			}

			if (is_array($message)) {
				if (isset($message['attachments'])) {
					foreach ($message['attachments'] as $attachment) {
						$this->addAttachment($message_id, $attachment);
					}
				}
			}
			$final_target_list = array();
			foreach($remove_targets as $remove_target){
				DB::query(null, 'UPDATE plugin_messaging_message_targets SET deleted=1 WHERE id=' . $remove_target)->execute();
			}
			foreach($target_list as $target){
				$itarget = array();
				$itarget['message_id'] = $message_id;
				$itarget['target_type'] = $target['target_type'];
				if(in_array($itarget['target_type'], $recipient_provider_ids) && (int)$target['target'] != 0){
					$itarget['target'] = (int)$target['target'];
				} else {
					$itarget['target'] = $target['target'];
				}
				if(isset($target['subject'])){
					$itarget['custom_subject'] = $target['subject'];
				}
				if(isset($target['message'])){
					$itarget['custom_message'] = $target['message'];
				}
				if(isset($target['x_details'])){
					$itarget['x_details'] = $target['x_details'];
				}
				$target_result = DB::insert('plugin_messaging_message_targets', array_keys($itarget))->values($itarget)->execute();
				$ref_target_id = $target_result[0];
                if (!$is_draft) {
                    $target_to_resolve = array(
                        'driver'            => $driver,
                        'target_id'         => $ref_target_id,
                        'target_type'       => $target['target_type'],
                        'target'            => $target['target'],
                        'final_target'      => (!empty($target['final_target']))      ? $target['final_target']      : '',
                        'final_target_type' => (!empty($target['final_target_type'])) ? $target['final_target_type'] : ''
                    );
                    $this->resolve_final_targets($target_to_resolve, $final_target_list, $warnings);
                }
            }
            if (count($target_list) == 1 && @$itarget['custom_message'] != '') {
                DB::update('plugin_messaging_messages')
                    ->set(array('message' => $itarget['custom_message']))
                    ->where('id', '=', $message_id)
                    ->execute();
            }
            foreach($final_target_list as $final_target) {
                $insert_data = array(
                    'target_id'        => $final_target['target_id'],
                    'target_type'      => $final_target['target_type'],
                    'target'           => $final_target['target'],
                    'delivery_status'  => 'UNKNOWN',
                    'driver_remote_id' => ''
                );
                try {
                    $final_target_result = DB::insert('plugin_messaging_message_final_targets',
                        array_keys($insert_data))->values($insert_data)->execute();
                } catch (Exception $exc) {
                    if (stripos($exc, 'Duplicate entry') == false) { // ignore duplicate email
                        throw $exc;
                    }
                }
			}
			
			if($this->trigger_activity){
				$activity = new Model_Activity;
				$activity->set_action('create')->set_item_type('message')->set_item_id($message_id)->save();
			}

            Database::instance()->commit();
			
			if($schedule == null && !$is_draft && $imessage['status'] != 'FAILED'){
				$this->process_message($imessage['id']);
			}
			//header('content-type: text/plain; charset=utf-8');print_r($message_id);print_r(func_get_args());print_r($warnings);die();
			return $message_id;
        }
        catch(Exception $e)
        {
            Database::instance()->rollback();
			//header('content-type: text/plain; charset=utf-8');print_r($e);print_r(func_get_args());print_r($warnings);die();
			throw $e;
        }
	}

    public function apply_wrapper($message, $args = [])
    {
        $wrapper = Settings::instance()->get('email_wrapper_html');
        if (!empty($wrapper)) {
            $theme = Model_Engine_Theme::get_current_theme();

            /* Get the logo from the settings. If the setting has not been used, get the one used on the front end. */
            $logo  = Settings::instance()->get('email_logo');
            if (!$logo) {
                $logo = Settings::instance()->get('site_logo');
            }
            if ($logo) {
                $logo_src = Model_Media::get_image_path($logo, 'logos');
            } else {
                $logo_src = trim(URL::base(), '/').'/assets/'.Kohana::$config->load('config')->assets_folder_path.'/images/logo.png';
            }

            // Remove the automatic padding added around the message by including <!-- #reset padding --> in the message.
            // This should be replaced with something more GUI-orientated. Maybe a field on the message template editor.
            $reset_padding = (strpos($message, '#reset padding') !== false);

            $params = [
                // Variables that get substituted before $message_body (ones that should only affect the wrapper)
                'company_name'     => Settings::instance()->get('company_name'),
                'content_padding'  => $reset_padding ? '0px' : '16px',
                'footer_text'      => ['html' => true, 'value' => Settings::instance()->get('email_footer_text')],
                'logo_src'         => $logo_src,
                'need_help_text'   => ['html' => true, 'value' => Settings::instance()->get('email_help_text')],

                // $message_body
                'message_body'     => ['html' => true, 'value' => $message],

                // Variables that are substituted after $message_body (ones that affect the wrapper and the message body)
                'email_link_color' => !empty($theme->email_link_color)   ? $theme->email_link_color   : '#0000ee',
                'theme_color'      => !empty($theme->email_header_color) ? $theme->email_header_color : '#37478f',
            ];

            $message = self::render_template($wrapper, $params);
        }

        if (!empty($args['add_html_body']) && strpos($message, '<html') === false) {
            $message = '<!DOCTYPE html><html><head><style>body{font-family:sans-serif;margin: 0;}</style></head><body>'.$message.'</body></html>';
        }

        return $message;
    }

	protected function addAttachment($messageId, $attachment)
	{
        $attachment = is_object($attachment) ? (array) $attachment : $attachment;
		$data = array();
		$data['message_id'] = $messageId;
        $data['content_encoding'] = 'base64';
		if (isset($attachment['file_id'])) {
            $fileDetails = Model_Files::getFileActiveVersionDetails($attachment['file_id']);
            if ($fileDetails) {
                $data['content_encoding'] = 'file_id';
                $data['name'] = $fileDetails['name'];
                $data['type'] = $fileDetails['mime_type'];
                $data['content'] = $attachment['file_id'];
                    //base64_encode(Model_Files::getFileContent($attachment['file_id']));
            } else {
                return false;
            }
		} else if(isset($attachment['path'])) {
            $data['content_encoding'] = 'file_id';
            $dir_id = Model_Files::get_directory_id_r("/messaging/attachments");
            $file_name = $attachment['name'] ? $attachment['name'] : 'content-' . date('YmdHis') . '-' . mt_rand(1000, 9999);
            $file_info = array
            (
                'name'     => $file_name,
                'type'     => mime_content_type($attachment['path']),
                'size'     => filesize($attachment['path']),
                'tmp_name' => $attachment['path'],
            );
            $file_id = Model_Files::create_file($dir_id, $file_name, $file_info);
			$data['content'] = $file_id;
		} else {
            $data['content_encoding'] = 'file_id';
            $tmp_file = tempnam(Kohana::$cache_dir, 'attachment_');
            file_put_contents($tmp_file, $attachment['content']);
            $dir_id = Model_Files::get_directory_id_r("/messaging/attachments");
            $file_name = 'content-' . date('YmdHis') . '-' . mt_rand(1000, 9999);
            $file_info = array
            (
                'name'     => $file_name,
                'type'     => mime_content_type($tmp_file),
                'size'     => filesize($tmp_file),
                'tmp_name' => $tmp_file,
            );
            $file_id = Model_Files::create_file($dir_id, $file_name, $file_info);
            $data['content'] = $file_id;
            @unlink($tmp_file);
		}
        if (isset($attachment['name'])) {
            $data['name'] = $attachment['name'];
        }
        if (isset($attachment['type'])) {
            $data['type'] = $attachment['type'];
        }
		DB::insert(self::ATTACHMENTS_TABLE, array_keys($data))
            ->values($data)
            ->execute();
	}
	
	public function delete($message_id)
	{
        return false; // never delete a message.
		if(!is_numeric($message_id)){
			return false;
		}
        try{
            Database::instance()->begin();

            $logged_in_user = Auth::instance()->get_user();
			DB::query(null, 'UPDATE plugin_messaging_messages SET plugin_messaging_messages.deleted=1 WHERE id=' . $message_id)->execute();
			DB::query(null, 'UPDATE plugin_messaging_messages m
									INNER JOIN plugin_messaging_message_targets t ON m.id = t.message_id
								SET t.deleted=1 WHERE m.id=' . $message_id)->execute();
			DB::query(null, 'UPDATE plugin_messaging_messages m
									INNER JOIN plugin_messaging_message_targets t ON m.id = t.message_id
									INNER JOIN plugin_messaging_message_final_targets ft on t.id = ft.target_id
								SET ft.deleted=1 WHERE m.id=' . $message_id)->execute();
            Database::instance()->commit();
			return true;
		} catch(Exception $e){
            Database::instance()->rollback();
			throw $e;
		}
	}

	public static function old_event_send($event, $params)
	{
		$mm = new Model_Messaging();
		$mm->get_drivers();
		$event_name = $event->get_name();
		$template = DB::select('*')->from('plugin_messaging_notification_templates')->where('name', '=', $event_name)->execute()->as_array();
		if(!$template){
			$template_id = $mm->import_notification_template_from_old_notification_event($event->get_id());
			if(!$template_id){
				return false;
			}
		} else {
			$template_id = $template[0]['id'];
		}
		$mm->send_template($template_id, $params['body']);
	}

    public static function to_autocomplete($driver, $term, $provider = null, $is_template = false)
    {
        if (Model_Plugin::is_enabled_for_role('Administrator', 'contacts3')) {
            $data = [];
            // This could be brought into a separate function for easy autocomplete contact finding
            $contacts = ORM::factory('Contacts3_Contact');
                 if ($driver === 'dashboard') {
                     // Only show users
                     $contacts->join(array(Model_Users::MAIN_TABLE, 'contacts3_user'), 'inner')
                         ->on('contacts3_contact.linked_user_id', '=', 'contacts3_user.id');
                 }
                 $contacts
                     ->or_where_open()
                        ->or_where_open()
                            ->where_email_like($term, ($driver === 'email') ? 'inner' : 'left')
                        ->or_where_close()
                        ->or_where_open()
                            ->where_mobile_like($term,
                            ($driver === 'sms') ? 'inner' : 'left')->or_where_close()->or_where('contacts3_contact.id', '=',
                            $term)
                            ->or_where(db::expr('concat_ws(" ", contacts3_contact.first_name, contacts3_contact.last_name)'),
                            'like', "%{$term}%")
                     ->or_where_close()
                     ->group_by('id');

            $contacts = $contacts->limit(10)->find_all_undeleted();
            $contacts->as_array('id');
            foreach ($contacts as $contact) {
                $contact_data = [];
                $contact_data['email'] = $contact->get_email()['value'] ?? '';
                $contact_data['sms'] = $contact->get_mobile_number() ?? '';
                $contact_data['deleted'] = $contact->delete;
                // Don't show contacts to autocomplete if they requested a specific driver and the contact has no data for that driver
                if (empty($contact_data[$driver]) && $driver !== 'dashboard') {
                    continue;
                }
                if ($driver === 'dashboard') {
                    $contact_data['category'] = "CMS_USER";
                    $contact_data['value'] = $contact_data['db_id'] = $contact->linked_user_id;
                } else {
                    $contact_data['category'] = "CMS_CONTACT3";
                    $contact_data['value'] = $contact_data['db_id'] = $contact->id;
                }
                $contact_data['label'] = "{$contact->id} - {$contact->first_name} {$contact->last_name}";
                $contact_data['label'] .= ($driver === 'sms') ? " - {$contact_data['sms']}" : " - {$contact_data['email']}";
                $data[] = $contact_data;
            }
        } else {
            $data = array();
            $recipient_providers = self::get_recipient_providers();
            foreach ($recipient_providers as $recipient_provider) {
                if ($recipient_provider->supports($driver) && ($provider == null || $provider == $recipient_provider->pid())) {
                    // "post var" only appears in the template autcomplete
                    if ($recipient_provider->pid() != 'POST_VAR' || $is_template) {
                        $recipient_provider->to_autocomplete($term, $data, $driver);
                    }
                }
            }
        }
        return $data;
	}

	// public function resolve_final_targets($driver, $target_type, $target_id, $ref_target_id, &$target_list, &$warnings)
    public function resolve_final_targets($target, &$target_list, &$warnings)
    {
        $target_type = isset($target['target_type']) ? $target['target_type'] : '';
        $driver      = isset($target['driver'])      ? $target['driver']      : '';
        $providers   = self::get_recipient_providers();
        $supports    = array('PHONE' => array('sms'), 'EMAIL' => array('email'));

        if (!empty($target['final_target']) && !empty($target['final_target_type'])) {
            $target['target']      = $target['final_target'];
            $target['target_type'] = $target['final_target_type'];
            $target_list[]         = $target;
        }

    else if (isset($providers[$target_type])) {
            $providers[$target_type]->resolve_final_targets($target, $target_list, $warnings);
        }

        else if (array_key_exists($target_type, $supports)) {
            if (in_array($driver, $supports[$target_type])) {
                $target_list[] = $target;
            } else {
                $warnings[] = $driver . ' messaging does not support ' . $target_type;
            }
        }
        else {
            $warnings[] = $target_type . ' messaging is unknown';
        }
	}

	public function get_messages_for_notification_tray($unread_only = FALSE, $user_id = NULL, $date_from = NULL, $date_to = NULL, $recipients = array())
	{
		if (is_null($user_id))
		{
			$user = (object) Auth::instance()->get_user();
		}
		else
		{
			$user = new Model_User($user_id);
		}
		$date_from = is_null($date_from) ? date('Y-m-d H:i:s', strtotime('-14 days')) : $date_from;
		$date_to   = is_null($date_to)   ? date('Y-m-d H:i:s')                        : $date_to;

		$messages = DB::select(
			'target.message_id', 'ftarget.target', 'ftarget.id', 'ftarget.delivery_status', 'driver.driver', 'message.message', 'message.subject', 'message.sent_started',
            ['sender.id', 'sender_id'], [DB::expr("IF (`message`.`sender` REGEXP '[0-9]+', CONCAT(`sender`.`name`, ' ', `sender`.`surname`), `message`.`sender`)"), 'sender'],
            array(DB::expr("IF (`read_by`.`user_id`, 1, 0)"), 'is_read')
        )
			->from(array('plugin_messaging_message_final_targets', 'ftarget'))
			->join(array('plugin_messaging_message_targets',       'target' ), 'INNER')->on('ftarget.target_id',  '=', 'target.id')
			->join(array('plugin_messaging_messages',              'message'), 'INNER')->on('target.message_id',  '=', 'message.id')
			->join(array('plugin_messaging_drivers',               'driver' ), 'LEFT' )->on('message.driver_id',  '=', 'driver.id')
			->join(array('engine_users',                           'sender' ), 'LEFT' )->on('message.sender',     '=', 'sender.id')
            ->join(array(self::READ_BY_TABLE,                      'read_by'), 'LEFT' )->on('read_by.message_id', '=', 'message.id')->on('read_by.user_id', '=', DB::expr($user->id))
            ->where('driver.driver', '=', 'dashboard')
        ;

		if ($recipients) {
			$messages->and_where_open();
			foreach ($recipients as $recipient) {
				$messages
					->or_where_open()
						->and_where('target.target_type', '=', $recipient['target_type'])
						->and_where('target.target', '=', $recipient['target'])
					->or_where_close();
			}
			$messages->and_where_close();
		} else {
			$messages
				// Only show messages for the current user
				->and_where_open()
					->or_where_open()
						->and_where('ftarget.target_type', '=', 'CMS_USER')
						->and_where('ftarget.target', '=', $user->id)
					->or_where_close()
					->or_where_open()
						->and_where('target.target_type', '=', 'CMS_USER')
						->and_where('target.target', '=', $user->id)
					->or_where_close()
					->or_where('ftarget.target', '=', $user->email)
				->and_where_close();
		}
		$messages
			// Only show sent (non-draft) messages in the date range
			->and_where_open()
				->where('message.schedule', 'is', NULL)
				->or_where('message.schedule', '<=', $date_to)
			->and_where_close()
			->and_where('message.status',          '=',  'SENT')
			->and_where('message.sent_completed',  '>=',  $date_from)
			->and_where('message.sent_completed',  '<=',  $date_to)
			->and_where('message.deleted',         '=',   0)
			->and_where('message.is_draft',        '=',   0)
			->group_by('message.id')
			->order_by('message.sent_completed', 'desc')
		;

        if ($unread_only) {
            $messages->where(DB::expr("IF (`read_by`.`user_id`, 1, 0)"), '=', 0);
        }

		return $messages->execute()->as_array();
	}

	public static function get_system_message($id)
	{
		$message = DB::select('messages.message', 'messages.subject', 'messages.sent_started', 'ftargets.*', 'types.icon', 'types.title')
						->from(array('plugin_messaging_message_final_targets', 'ftargets'))
							->join(array('plugin_messaging_message_targets', 'targets'), 'inner')
								->on('ftargets.target_id', '=', 'targets.id')
							->join(array('plugin_messaging_messages', 'messages'), 'inner')
								->on('targets.message_id', '=', 'messages.id')
							->join(array('plugin_messaging_notifications', 'notifications'), 'left')
								->on('messages.id', '=', 'notifications.message_id')
							->join(array('plugin_messaging_notification_templates', 'templates'), 'left')
								->on('notifications.template_id', '=', 'templates.id')
							->join(array('plugin_messaging_notification_types', 'types'), 'left')
								->on('templates.type_id', '=', 'types.id')
						->where('ftargets.id', '=', $id)
						->execute()
						->as_array();
		if($message){
			return $message[0];
		} else {
			return null;
		}
	}

	// Get a system message when you know the message ID and recipient details
	public static function get_system_message_for_target($message_id, $target_type, $target)
	{
		$message = DB::select('messages.message', 'messages.subject', 'messages.sent_started', 'ftargets.*', 'types.icon', 'types.title')
			->from(array('plugin_messaging_message_final_targets', 'ftargets'))
			->join(array('plugin_messaging_message_targets', 'targets'), 'inner')
			->on('ftargets.target_id', '=', 'targets.id')
			->join(array('plugin_messaging_messages', 'messages'), 'inner')->on('targets.message_id', '=', 'messages.id')
			->join(array('plugin_messaging_notifications', 'notifications'), 'left')->on('messages.id', '=', 'notifications.message_id')
			->join(array('plugin_messaging_notification_templates', 'templates'), 'left')->on('notifications.template_id', '=', 'templates.id')
			->join(array('plugin_messaging_notification_types', 'types'), 'left')->on('templates.type_id', '=', 'types.id')
			->where('messages.id', '=', $message_id);

		if ($target_type == 'CMS_USER')
		{
			$user = ORM::factory('User', $target);
			$contact_id = DB::select('id')->from('plugin_contacts_contact')->where('email', '=', $user->email)->execute()->get('id', 0);

			$message
				->where_open()
					->or_where_open()
						->where('ftargets.target_type', '=', $target_type)->where('ftargets.target', '=', $target)
					->or_where_close()
					->or_where_open()
						->where('ftargets.target_type', '=', 'EMAIL')->where('ftargets.target', '=', $user->email)
					->or_where_close();

					if ($contact_id)
					{
						$message
							->or_where_open()
								->where('ftargets.target_type', '=', 'CMS_CONTACT')->where('ftargets.target', '=', $contact_id)
							->or_where_close();
					}
			$message
				->and_where_close();
		}
		else
		{
			$message = $message
				->where('ftargets.target_type', '=', $target_type)
				->where('ftargets.target', '=', $target);

		}

		$message = $message->execute()->as_array();

		if ($message)
		{
			return $message[0];
		}
		else
		{
			return NULL;
		}
	}

	public static function set_delivery_status_system_message($id, $status)
	{
		return DB::update('plugin_messaging_message_final_targets')
					->set(array('delivery_status' => $status))
					->where('id', '=', $id)
					->execute();
	}

	public static function delete_system_message($id)
	{
		return DB::update('plugin_messaging_message_final_targets')
					->set(array('deleted' => date('Y-m-d H:i:s')))
					->where('id', '=', $id)
					->execute();
	}

	public static function set_final_target_delivery_status($id, $status)
	{
		return DB::update('plugin_messaging_message_final_targets')
					->set(array('delivery_status' => $status))
					->where('id', '=', $id)
					->execute();
	}

	public static function get_newsletter_page_list()
	{
		$pages = DB::select('plugin_pages_pages.*')
					->from('plugin_pages_pages')
					->join('plugin_pages_layouts', 'inner')->on('plugin_pages_pages.layout_id', '=', 'plugin_pages_layouts.id')
					->where('plugin_pages_layouts.layout', '=', 'Newsletter')
					->execute()
					->as_array();
		return $pages;
	}
	
	public static function get_news_page($id)
	{
		$page = DB::select('*')
					->from('plugin_pages_pages')
					->where('id', '=', $id)
					->execute()
					->as_array();
		return $page[0];
	}
	
	
	public static function get_notification_types()
	{
		return DB::select('*')->from('plugin_messaging_notification_types')->execute()->as_array();
	}
	
	public static function get_notification_types2()
	{
		$types = array();
		foreach(DB::select('*')->from('plugin_messaging_notification_types')->execute()->as_array() as $type){
			$types[$type['id']] = $type['title'];
		}
		return $types;
	}
	
	public function save_notification_template($post, $template_id)
	{
		//header('content-type: text/plain; charset=utf-8');print_r(func_get_args());die();
		$recipient_providers = self::get_recipient_providers();
		$recipient_provider_ids = array_keys($recipient_providers);

        try{
            Database::instance()->begin();
            $logged_in_user = Auth::instance()->get_user();
			if(is_numeric($template_id)){
				$template = DB::select('*')->from('plugin_messaging_notification_templates')->where('id', '=', $template_id)->execute()->as_array();
				$template = @$template[0];
				if(!$template){
					return false;
				}
				$template['date_updated'] = date('Y-m-d H:i:s');
				$template_status = 'update';
			} else {
				$template = array();
				$template['date_created'] = date('Y-m-d H:i:s');
				$template['created_by'] = $logged_in_user['id'];
				$template_status = 'insert';
			}
			if(isset($post['interval']['minute']) && isset($post['interval']['hour']) && isset($post['interval']['day_of_month']) && isset($post['interval']['month']) && isset($post['interval']['day_of_week']) && $post['interval']['minute'] != '' && $post['interval']['hour'] != '' && $post['interval']['day_of_month'] != '' && $post['interval']['month'] != '' && $post['interval']['day_of_week'] != ''){
				$interval = implode(',', $post['interval']['minute']) . ' ' . 
							implode(',', $post['interval']['hour']) . ' ' . 
							implode(',', $post['interval']['day_of_month']) . ' ' . 
							implode(',', $post['interval']['month']) . ' ' . 
							implode(',', $post['interval']['day_of_week']);
			} else {
				$interval = null;
			}
			//print_r($post['interval']);echo $interval;die();
			$template['send_interval'] = $interval;
			$template['name']          = $post['template_name'];
			$template['description']   = $post['description'];
			$template['type_id']       = $post['type_id'];
			$template['driver']        = $post['driver'];
			$template['sender']        = $post['sender'];
			$template['replyto']       = @$post['replyto'];
			$template['subject']       = $post['subject'];
			$template['header']        = $post['header'];
			$template['footer']        = $post['footer'];
			$template['message']       = $post['message'];
			$template['page_id']       = isset($post['page_id']) ? $post['page_id'] : '';
            $template['category_id']   = isset($post['category_id']) ? $post['category_id'] : '';

			if (isset($post['overwrite_cms_message']))
			{
				$template['overwrite_cms_message'] = $post['overwrite_cms_message'];
			}
			if(isset($post['publish'])){
				$template['publish'] = $post['publish'];
			}
			if(!is_numeric($template_id)){
				$template_result = DB::insert('plugin_messaging_notification_templates', array_keys($template))->values($template)->execute();
				$template_id = $template_result[0];
			} else {
				$template_result = DB::update('plugin_messaging_notification_templates')->set($template)->where('id', '=', $template_id)->execute();
			}
			
			if(isset($post['target_id_deleted'])){
				//DB::update('plugin_messaging_notification_template_targets')->set(array('deleted' => 1))->where('id', 'in', $post['target_id_deleted'])->execute();
				DB::delete('plugin_messaging_notification_template_targets')->where('id', 'in', $post['target_id_deleted'])->execute();
			}
			if(isset($post['messaging_target_remove'])){
				DB::delete('plugin_messaging_notification_template_targets')->where('id', 'in', $post['messaging_target_remove'])->execute();
			}
            $target_ids = array();
            if(isset($post['recipient']['id']))
			foreach ($post['recipient']['id'] as $key => $recipient_id)
			{
				$target = array();
				$target['template_id'] = $template_id;
				$target['target_type'] = $post['recipient']['pid'][$key];
				$target['target']      = $recipient_id;
				$target['x_details']   = isset($post['recipient']['x_details'][$key]) ? $post['recipient']['x_details'][$key] : '';
				if (!empty($post['recipient']['db_id'][$key]) && $post['recipient']['db_id'][$key] == $post['recipient']['id'][$key]) {
                    $status = 'insert';
                } else {
				    $status =  'update';
                }
				if($status == 'insert' || $status == 'new'){
					$inserted_t = DB::insert('plugin_messaging_notification_template_targets', array_keys($target))->values($target)->execute();
                    $target_ids[] = $post['recipient']['id'][$key];
				}  elseif ($status == 'update' || is_numeric($status)){
				    $existing = DB::select('*')
                        ->from('plugin_messaging_notification_template_targets')
                        ->where('target', '=', $post['recipient']['id'][$key])
                        ->and_where('template_id', '=', $template_id)
                        ->execute()
                        ->current();
				    if (empty($existing)) {
                        $inserted_t = DB::insert('plugin_messaging_notification_template_targets', array_keys($target))
                            ->values($target)->execute();

                    } else {
                        DB::update('plugin_messaging_notification_template_targets')
                            ->set($target
                            )->where('target', '=', $post['recipient']['id'][$key])
                            ->and_where('template_id', '=', $template_id)->execute();
                    }
                    $target_ids[] = $post['recipient']['id'][$key];

                }
			}
            $deleteq = DB::delete('plugin_messaging_notification_template_targets')
                ->where('template_id', '=', $template_id);
            if (count($target_ids) > 0) {
                $deleteq->and_where('target', 'not in', $target_ids);
            }
            $deleteq->execute();
            DB::delete('plugin_messaging_notification_template_attachments')->where('template_id', '=', $template_id)->execute();
            if(isset($post['attachments']))
            foreach ($post['attachments'] as $attachment) {
                if (@$attachment['file_id'] == '' || @$attachment['file_id'] == 0 || @$attachment['file_id'] == "0") {
                    continue;
                }
                $attachment['template_id'] = $template_id;
                DB::insert('plugin_messaging_notification_template_attachments', array_keys($attachment))
                    ->values($attachment)
                    ->execute();
            }
			
			$activity = new Model_Activity;
			if($this->trigger_activity){
				if($template_status == 'insert'){
					$activity->set_action('create')->set_item_type('notification_template')->set_item_id($template_id)->save();
				} else {
					$activity->set_action('update')->set_item_type('notification_template')->set_item_id($template_id)->save();
				}
			}
            Database::instance()->commit();
            IbHelpers::set_message('The template: '.$template_id.' was '.$template_status == 'insert'?'Created':'Updated'.' successfully.', 'success popup_box');
			return $template_id;
		} catch(Exception $exc){
            // header('content-type: text/plain; charset=utf-8');print_r($exc);die();
            Database::instance()->rollback();
            IbHelpers::set_message('Error saving the template event.', 'error popup_box');
			throw $exc;
		}
	}
	
	public function get_notification_template($template_id_name, $more_target_info = false)
	{
		if(is_numeric($template_id_name)){
			$template = DB::select('*')->from('plugin_messaging_notification_templates')->where('id', '=', $template_id_name)->execute()->as_array();
		} else {
			$template = DB::select('*')->from('plugin_messaging_notification_templates')->where('name', '=', $template_id_name)->execute()->as_array();
		}
		if($template){
			$template = $template[0];
			$template['targets'] = DB::select(DB::expr('targets.*'))
				->from(array('plugin_messaging_notification_template_targets', 'targets'))
				->where('template_id', '=', $template['id'])->execute()->as_array();
            if($more_target_info){
				$recipient_providers = self::get_recipient_providers();
				foreach($template['targets'] as $i => $target){
					if (in_array($target['target_type'], array('EMAIL', 'PHONE'))){
						$target_details = array('id' => $target['target'], 'label' => $target['target']);
					} else {
						$target_details = $recipient_providers[$target['target_type']]->get_by_id($target['target']);
					}
					$target_details['category'] = $target['target_type'];
					$target_details['db_id'] = $target['id'];
					$target_details['value'] = $target['target'];
					$template['targets'][$i] = array_merge($target_details, $target);
				}
			}
            $template['attachments'] = DB::select('*')
                ->from('plugin_messaging_notification_template_attachments')
                ->where('template_id', '=', $template['id'])
                ->execute()
                ->as_array();
			$template['signature'] = $template['signature_id'] ? Model_Signature::get($template['signature_id']) : null;
			return $template;
		} else {
			return false;
		}
	}
	
	public function clone_notification_template($template_id)
	{

        try{
            Database::instance()->begin();
			$template = $this->get_notification_template($template_id);
			$new_template_id = false;
			if($template){
				$clone_query = "insert into plugin_messaging_notification_templates 
									(name, description, driver, type_id, subject, sender, message, page_id, header, footer, schedule, date_created, created_by, date_updated, last_sent, publish, deleted)
									(select concat(name, '-clone-', unix_timestamp()), description, driver, type_id, subject, sender, message, page_id, header, footer, schedule, date_created, created_by, date_updated, last_sent, publish, deleted from plugin_messaging_notification_templates where id=" . $template['id'] . ")";
				$template_result = DB::query(Database::INSERT, $clone_query)->execute();
				$new_template_id = $template_result[0];
				
				$clone_query = "insert into plugin_messaging_notification_template_targets
									(template_id, target_type, target)
									(select " . $new_template_id . ", target_type, target from plugin_messaging_notification_template_targets where template_id=" . $template['id'] . ")";
				DB::query(Database::INSERT, $clone_query)->execute();
			}
            Database::instance()->commit();
			return $new_template_id;
		} catch(Exception $exc){
			header('content-type: text/plain; charset=utf-8');print_r($exc);die();
            Database::instance()->rollback();
			throw $exc;
		}
	}
	
	public function import_notification_template_from_old_notification_event($old_event_id)
	{
        try{
            Database::instance()->begin();
			$old_event = DB::select('*')
							->from('plugin_notifications_event')
							->where('id', '=', $old_event_id)
							->execute()
							->as_array();
			if($old_event){
				$old_event = $old_event[0];
				$logged_in_user = Auth::instance()->get_user();
				
				$template = array();
				$template['date_created'] = date('Y-m-d H:i:s');
				$template['created_by'] = $logged_in_user['id'];
				$template['name'] = $old_event['name'];
				$template['description'] = $old_event['description'];
				$template['type_id'] = 1;
				$template['driver'] = 'EMAIL';
				$template['sender'] = $old_event['from'];
				$template['subject'] = $old_event['subject'];
				$template['header'] = $old_event['header'];
				$template['footer'] = $old_event['footer'];
				$template['message'] = '';
				$template['publish'] = 1;
				$template_result = DB::insert('plugin_messaging_notification_templates', array_keys($template))->values($template)->execute();
				$template_id = $template_result[0];
				
				foreach(array('to' => 'plugin_notifications_to', 'cc' => 'plugin_notifications_cc', 'bcc' => 'plugin_notifications_bcc') as $x_details => $copy_table){
					$recipient_list = DB::select('*')
											->from($copy_table)
											->where('id_event', '=', $old_event['id'])
											->execute()
											->as_array();
					foreach($recipient_list as $recipient){
						$target = array();
						$target['template_id'] = $template_id;
						$target['target_type'] = 'CMS_CONTACT';
						$target['target'] = $recipient['id_contact'];
						$target['x_details'] = $x_details;
						DB::insert('plugin_messaging_notification_template_targets', array_keys($target))->values($target)->execute();
					}
				}
                Database::instance()->commit();
				return $template_id;
			} else {
				return false;
			}
		} catch(Exception $exc){
			//header('content-type: text/plain; charset=utf-8');print_r($exc);die();
            Database::instance()->rollback();
			throw $exc;
		}
	}
	
	public static function notification_template_list($published_only = false, $where = [], $order_by = false)
	{
        $target_d = array();
        $recipient_providers = self::get_recipient_providers();
        foreach($recipient_providers as $recipient_provider){
            $column = $recipient_provider->message_details_column();
            if ($column) {
                $target_d[] = $column;
            }
        }

        $target_to = 'GROUP_CONCAT(IF(t.x_details <> \'cc\' AND t.x_details <> \'bcc\', CONCAT_WS(\'\', t.target_type, \' \', IF(t.target_type = \'EMAIL\', t.target, \'\'), ' . implode(', ', $target_d) . '), \'\')) as `to`';
        $target_cc = 'GROUP_CONCAT(IF(t.x_details = \'cc\', CONCAT_WS(\'\',t.target_type, \' \', IF(t.target_type = \'EMAIL\', t.target, \'\'), ' . implode(', ', $target_d) . '), \'\')) as `cc`';
        $target_bcc = 'GROUP_CONCAT(IF(t.x_details = \'bcc\', CONCAT_WS(\'\',t.target_type, \' \', IF(t.target_type = \'EMAIL\', t.target, \'\'), ' . implode(', ', $target_d) . '), \'\')) as `bcc`';

        $query = DB::select(
            DB::expr('templates.*, types.title as type, driver.driver, cats.name as category'),
            ['creator_role.master_group', 'is_system'],
            DB::expr($target_to),
            DB::expr($target_cc),
            DB::expr($target_bcc)
        )->from(array('plugin_messaging_notification_templates', 'templates'))
            ->join(array('plugin_messaging_notification_types', 'types'), 'left')
                ->on('templates.type_id', '=', 'types.id')
            ->join(array('plugin_messaging_drivers', 'driver'), 'left')
                ->on('templates.driver', '=', 'driver.driver')
            ->join(array('plugin_messaging_notification_categories', 'cats'), 'left')
                ->on('templates.category_id', '=', 'cats.id')
            ->join(array('plugin_messaging_notification_template_targets', 't'), 'left')
                ->on('templates.id', '=', 't.template_id')
            ->join(['engine_users', 'creator'], 'left')
                ->on('templates.created_by', '=', 'creator.id')
            ->join(['engine_project_role', 'creator_role'], 'left')
                ->on('creator.role_id', '=', 'creator_role.id');

        foreach($recipient_providers as $recipient_provider){
            $recipient_provider->message_details_join($query);
        }
        foreach ($where as $where_column => $where_clause) {
            $query->and_where($where_column, $where_clause['op'], $where_clause['value']);
        }
        if ($published_only) {
            $query->and_where('templates.publish', '=', 1);
        }
        if ($order_by) {
            $query->order_by($order_by['column'], $order_by['direction'])->limit($limit);
        }
		$notification_template_list = $query->where('templates.deleted', '=', 0)
            ->order_by('date_updated', 'desc')
            ->group_by('templates.id')
            ->execute()
            ->as_array();

		return $notification_template_list;
		
	}
	
	public static function notification_template_set_publish($template_id, $publish)
	{
		DB::update('plugin_messaging_notification_templates')
				->set(array('publish' => $publish))
				->where('id', '=', $template_id)
				->execute();
	}
	
	public static function notification_template_delete($template_id)
	{
        IbHelpers::permission_redirect('messaging_delete_template');

        $template = new Model_Messaging_Template();

        if ($template->is_system()) {
            IbHelpers::permission_redirect('messaging_delete_system_template');
        }

		DB::update('plugin_messaging_notification_templates')
				->set(array('deleted' => 1))
				->where('id', '=', $template_id)
				->execute();
	}
	
	public function notification_list()
	{
		return DB::select(DB::expr('templates.name as template, messages.*, types.title as type, drivers.driver, drivers.provider, cats.name as category'))
					->from(array('plugin_messaging_notification_templates', 'templates'))
						->join(array('plugin_messaging_notifications', 'notifications'))
							->on('templates.id', '=', 'notifications.template_id')
						->join(array('plugin_messaging_messages', 'messages'))
							->on('messages.id', '=', 'notifications.message_id')
						->join(array('plugin_messaging_drivers', 'drivers'))
							->on('messages.driver_id', '=', 'drivers.id')
						->join(array('plugin_messaging_notification_types', 'types'))
							->on('templates.type_id', '=', 'types.id')
                        ->join(array('plugin_messaging_notification_categories', 'cats'))
                            ->on('templates.category_id', '=', 'cats.id')
					->where('templates.deleted', '=', 0)
					->order_by('messages.date_created', 'desc')
					->execute()
					->as_array();
	}
	
	public function schedule_template_notifications()
	{
		$scheduled = 0;
		$templates = DB::select(DB::expr('templates.*, types.title as type'))
						->from(array('plugin_messaging_notification_templates', 'templates'))
							->join(array('plugin_messaging_notification_types', 'types'))
								->on('templates.type_id', '=', 'types.id')
						->where('deleted', '=', 0)
						->where('send_interval', 'is not', null)
						->order_by('date_updated', 'desc')
						->execute()
						->as_array();
		$time = 'now';
		foreach($templates as $template){
			$current_period = self::get_current_period_of_interval($template['send_interval'], $time);
			$has_notification = DB::select(DB::expr('count(*) as cnt'))
									->from(array('plugin_messaging_notification_templates', 'templates'))
										->join(array('plugin_messaging_notifications', 'notifications'), 'inner')
											->on('notifications.template_id', '=', DB::expr($template['id']))
											->on('templates.id', '=', 'notifications.template_id')
										->join(array('plugin_messaging_messages', 'messages'), 'inner')
											->on('notifications.message_id', '=', 'messages.id')
									->where('messages.schedule', '=', $current_period)
									->execute()
									->get('cnt');
			if($has_notification == 0){
				++$scheduled;
				$this->send_template($template['id'], null, $current_period);
			}
		}
		return $scheduled;
	}
	
	public function schedule_report_notifications()
	{
		$scheduled = 0;
		$reports = DB::select('*')
						->from('plugin_reports_reports')
						->where('bulk_message_interval', '<>', '')
						->execute()
						->as_array();
		//print_r($reports);
		$time = 'now';
		foreach($reports as $report){
			$current_period = self::get_current_period_of_interval($report['bulk_message_interval'], $time);
			//echo $current_period . "\n";
			$has_notification = DB::select(DB::expr('count(*) as cnt'))
									->from(array('plugin_reports_reports', 'reports'))
										->join(array('plugin_messaging_report_notifications', 'notifications'), 'inner')
											->on('notifications.report_id', '=', DB::expr($report['id']))
											->on('reports.id', '=', 'notifications.report_id')
										->join(array('plugin_messaging_messages', 'messages'), 'inner')
											->on('notifications.message_id', '=', 'messages.id')
									->where('messages.schedule', '=', $current_period)
									->execute()
									->get('cnt');
			if($has_notification == 0){
				$this->send_report($report, $current_period);
				++$scheduled;
			}
		}
		//header('content-type: text/plain; charset=utf-8');print_r($has_notification.":");die();
		return $scheduled;
	}
	
	public function handle_frontend_status_callback()
	{
		list($driver, $provider) = explode('-', @$_GET['driver']); // something like driver=sms-twilio , driver=email-mandrill
		if(isset($this->drivers[$driver][$provider])){
			$statuses = $this->drivers[$driver][$provider]->handle_status_callback();
			//file_put_contents("callback." . $driver . ".".$provider . time().".txt", var_dump($status,1));
			if($statuses){
				foreach($statuses as $status){
					DB::update('plugin_messaging_message_final_targets')->set($status)->where('driver_remote_id', '=', $status['driver_remote_id'])->execute();
				}
			}
		} else {
			throw new Exception('unknown request');
		}
	}
	
	public static function get_activity_alert_list($target_type, $target)
	{
		$alerts_tmp = DB::select('*')
						->from('plugin_messaging_activity_alerts')
						->where('target', '=', $target)
						->and_where('target_type', '=', $target_type)
						->execute()
						->as_array();
		$alerts = array();
		foreach($alerts_tmp as $alert){
			$alerts[$alert['action_id']][$alert['item_type_id']] = $alert;
		}
		return $alerts;
	}
	
	public static function set_activity_alert_list($target_type, $target, $activity_alerts)
	{
        try{
            Database::instance()->begin();
			DB::delete('plugin_messaging_activity_alerts')->where('target', '=', $target)->and_where('target_type', '=', $target_type)->execute();
			if($activity_alerts){
				foreach($activity_alerts as $action_id => $item_types){
					foreach($item_types as $item_type_id => $checked){
						if($checked){
							DB::insert('plugin_messaging_activity_alerts', array('target_type', 'target', 'action_id', 'item_type_id'))
								->values(array($target_type, $target, $action_id, $item_type_id))
								->execute();
						}
					}
				}
			}
            Database::instance()->commit();
			return true;
		} catch(Exception $exc){
            Database::instance()->rollback();
			return false;
		}
	}
	
	public static function search_activity_alert_list($params)
	{
		$query = DB::select('*')->from('plugin_messaging_activity_alerts');
		if(isset($params['action_id'])){
			if(is_array($params['action_id'])){
				$query->where('action_id', 'in', $params['action_id']);
			} else {
				$query->where('action_id', '=', $params['action_id']);
			}
		}
		if(isset($params['item_type_id'])){
			if(is_array($params['item_type_id'])){
				$query->where('item_type_id', 'in', $params['item_type_id']);
			} else {
				$query->where('item_type_id', '=', $params['item_type_id']);
			}
		}
		if(isset($params['user_id'])){
			if(is_array($params['user_id'])){
				$query->where('user_id', 'in', $params['user_id']);
			} else {
				$query->where('user_id', '=', $params['user_id']);
			}
		}
		if(isset($params['target_type'])){
			if(is_array($params['target_type'])){
				$query->where('target_type', 'in', $params['target_type']);
			} else {
				$query->where('target_type', '=', $params['target_type']);
			}
		}
		if(isset($params['target'])){
			if(is_array($params['target'])){
				$query->where('target', 'in', $params['target']);
			} else {
				$query->where('target', '=', $params['target']);
			}
		}
		return $query->execute()->as_array();
	}
	
	public static function send_activity_alerts($activity)
	{
		$alert_targets = self::search_activity_alert_list(array('action_id' => $activity['action_id'], 'item_type_id' => $activity['item_type_id']));
		if(count($alert_targets) > 0){
			$mm = new Model_Messaging();
			$mm->trigger_activity = false;
			$mm->get_drivers();
			$action = DB::select('name')->from('engine_activities_actions')->where('id', '=', $activity['action_id'])->execute()->get('name');
			$type = DB::select('name')->from('engine_activities_item_types')->where('id', '=', $activity['item_type_id'])->execute()->get('name');
			$message = 'new activity => user_id:' . $activity['user_id'] . ' item_type:' . $type . ' action:' . $action;
			$mm->send('dashboard', 'system', $activity['user_id'], $alert_targets, $message, 'System Activity');
		}
	}

	// minute hour day_of_month month day_of_week
	public static function parse_interval($interval)
	{
		$parts = preg_split('/\s+/', trim($interval));
		if(count($parts) != 5){
			return array(array(), array(), array(), array(), array());
		}
		foreach($parts as $i => $part){
			$parts[$i] = explode(',', $part);
			/*if($part != '*'){
				sort($parts[$i], SORT_NUMERIC);
			}*/
		}
		
		return $parts;
	}
	
	public static function check_interval($interval, $time = null)
	{
		$date = getdate($time);
		//print_r($date);
		$parts = self::parse_interval($interval);
		
		$minute_matched = false;
		foreach($parts[0] as $minutes){
			if($minutes == '*' || $minutes == $date['minutes']){
				$minute_matched = true;
				break;
			}
		}
		
		$hour_matched = false;
		foreach($parts[1] as $hours){
			if($hours == '*' || $hours == $date['hours']){
				$hour_matched = true;
				break;
			}
		}
		
		$day_of_month_matched = false;
		$last_day_of_month = date('t', $time);
		foreach($parts[2] as $day_of_months){
			if($day_of_months == '*' || $day_of_months == $date['mday'] || ($day_of_months == 'last' && $last_day_of_month == $date['mday'])){
				$day_of_month_matched = true;
				break;
			}
		}
		
		$month_matched = false;
		foreach($parts[3] as $months){
			if($months == '*' || $months == $date['mon']){
				$month_matched = true;
				break;
			}
		}
	
		$day_of_week_matched = false;
		foreach($parts[4] as $day_of_weeks){
			if($day_of_weeks == '*' || $day_of_weeks == $date['wday']){
				$day_of_week_matched = true;
				break;
			}
		}
		
		return $minute_matched && $hour_matched && $day_of_month_matched && $month_matched && $day_of_week_matched;
	}
	
	public static function get_current_period_of_interval($interval, $time = null)
	{
		if($time == null){
			$time = time();
		}
		$ce = Cron\CronExpression::factory($interval);
		$ce->isDue();
		$dt = $ce->getPreviousRunDate($time)->format('Y-m-d H:i:s');
		return $dt;
	}
	
	public static function array_column_values($array, $column)
	{
		$v = array();
		foreach($array as $a){
			$v[] = $a[$column];
		}
		return $v;
	}
	
	public function receive_cron()
	{
        $received = 0;
		foreach($this->drivers as $driver => $providers){
			foreach($providers as $provider){
				if($provider->has_receive_cron()){
					$received += $provider->receive_cron();
				}
			}
		}
        return $received;
	}
	
	public function handle_frontend_receive_callback()
	{
		list($driver, $provider) = explode('-', @$_GET['driver']); // something like driver=sms-twilio , driver=email-mandrill
		if(isset($this->drivers[$driver][$provider])){
			if($this->drivers[$driver][$provider]->has_receive_callback()){
				$this->drivers[$driver][$provider]->handle_receive_callback();
			}
		} else {
			throw new Exception('unknown request');
		}
	}

	// Remove potentially harmful content from messages
	public function clean_message($message)
	{
		$message = trim($message);
		if ($message == '') {
			return '';
		} else {
			/* Remove non-whitelisted tags */
			$message = strip_tags($message, $this->tag_whitelist);

			/* Remove non-whitelisted attributes */
			// Load HTML as DOMDocument object
			$dom = new DOMDocument;
			libxml_use_internal_errors(true); // do not generate errors for not wellformed htmls
			$dom->strictErrorChecking = false;
			$dom->formatOutput = true;
			$dom->loadHTML($message);
			// Find and loop through all elements
			$xpath = new DOMXPath($dom);
			$nodes = $xpath->query('//*');
			foreach ($nodes as $node) {
				// Loop through each attribute
				$attribute_count = isset($node->attributes) ? $node->attributes->length : 0;
				for ($i = $attribute_count - 1; $i >= 0; --$i) {
					// If the attribute is not in the whitelist, remove it
					$attribute = $node->attributes->item($i);
					if ($attribute != null && !in_array($attribute->name, $this->attribute_whitelist)) {
						$node->removeAttribute($attribute->name);
					}
				}
			}

			$cleaned = $dom->saveHTML();
			if (preg_match('#<body.*?>(.*?)</body>#is', $cleaned, $body)) {
				$cleaned = $body[1];
			}
			return $cleaned;
		}
	}


	public static function getNotificationCategories()
	{
		$cats = DB::select('*')
				->from('plugin_messaging_notification_categories')
				->order_by('name')
				->execute()
				->as_array();
		$result = array();
		foreach ($cats as $cat) {
			$result[$cat['id']] = $cat['name'];
		}
		return $result;
	}

	public static function set_unavailable($user_id, $from, $to, $auto_reply, $reply_message)
	{
		self::unset_unavailable($user_id);
		DB::insert('plugin_messaging_user_unavailable')
				->values(
						array(
								'user_id' => $user_id,
								'from_date' => $from,
								'to_date' => $to,
								'auto_reply' => (int)$auto_reply,
								'reply_message' => $reply_message
						)
				)->execute();
	}

	public static function unset_unavailable($user_id)
	{

		if ($user_id === null) {
			DB::delete('plugin_messaging_user_unavailable')->where('user_id', 'is', null)->execute();
		} else {
			DB::delete('plugin_messaging_user_unavailable')->where('user_id', '=', $user_id)->execute();
		}
	}

	public static function get_unavailable($user_id)
	{
		$u = DB::select('*')
				->from('plugin_messaging_user_unavailable')
				->where('user_id', '=', $user_id)
				->or_where('user_id', 'is', null)
				->order_by('user_id', 'desc')
				->limit(1)
				->execute()
				->current();
		return $u;
	}

	public static function is_unavailable($email_mobile)
	{
		$user = DB::select('*')->from(Model_Users::MAIN_TABLE)
				->where('deleted', '!=', '1')
				->and_where_open()
				->or_where('email', '=', $email_mobile)
				->or_where('mobile', '=', $email_mobile)
				->or_where('phone', '=', $email_mobile)
				->and_where_close()
				->execute()
				->current();
		$unavailable = self::get_unavailable($user ? $user['id'] : null);

		if ($unavailable) {
			if ($unavailable['from_date'] != '' && strtotime($unavailable['from_date']) > time()) {
				return false;
			}
			if ($unavailable['to_date'] != '' && strtotime($unavailable['to_date']) < time()) {
				return false;
			}

			return $unavailable;
		}
		return false;
	}

	public static function mute($sender)
	{
		$exists = DB::select('*')
				->from('plugin_messaging_mute_list')
				->where('sender', '=', $sender)
				->execute()
				->current();
		if (!$exists) {
			DB::insert('plugin_messaging_mute_list')
				->values(
					array(
						'sender' => $sender
					)
				)
				->execute();
		} else {
			DB::update('plugin_messaging_mute_list')
				->set(
					array(
						'deleted' => 0
					)
				)
				->execute();
		}
	}

	public static function unmute($sender)
	{
		DB::update('plugin_messaging_mute_list')
			->set(array('deleted' => 1))
			->where('sender', '=', $sender)
			->execute();
	}

	public static function is_muted($sender)
	{
		$sender = DB::select('*')
				->from('plugin_messaging_mute_list')
				->where('sender', '=', $sender)
				->and_where('deleted', '=', 0)
				->execute()
				->current();
		if ($sender) {
			return true;
		}

		return false;
	}

	public static function mute_list($params)
	{
		$searchq = DB::select('*')
				->from('plugin_messaging_mute_list')
				->where('deleted', '=', 0)
				->order_by('sender');

		$results = $searchq->execute()->as_array();
		return $results;
	}

	public static function mutes_get_for_datatable($filter)
	{
		$searchq = DB::select(DB::expr('SQL_CALC_FOUND_ROWS *'))
				->from('plugin_messaging_mute_list')
				->where('deleted', '=', 0)
				->order_by('sender');

		$rows = $searchq->execute()->as_array();
		$output = array();

		$output['iTotalDisplayRecords'] = DB::select(DB::expr("FOUND_ROWS() as `cnt`"))->execute()->get('cnt'); // total number of results
		$output['iTotalRecords']        = count($rows); // displayed results
		$output['aaData']               = array();

		foreach ($rows as $row) {
			$output['aaData'][] = array(
				$row['sender'],
				'<a class="unmute" data-id="' . $row['id'] . '">' . __('Unmute') . '</a>'
			);
		}

		return $output;
	}

    public static function mark_as_read($message_ids, $user_id, $status = 'read')
    {
        if (empty($message_ids))
        {
            return FALSE;
        }

        $message_ids = is_array($message_ids) ? $message_ids : array($message_ids);
        $message_ids = array_unique($message_ids);

        if (strtolower($status) == 'unread')
        {
            // Remove "read" flag
            return DB::delete(self::READ_BY_TABLE)
                ->where('message_id', 'IN', $message_ids)
                ->where('user_id',    '=',  $user_id)
                ->execute();
        }
        else
        {
            // Check which of the selected messages are already read
            $read_messages = DB::select()
                ->from(self::READ_BY_TABLE)
                ->where('message_id', 'IN', $message_ids)
                ->where('user_id',    '=',  $user_id)
                ->execute();
            $currently_read = array();
            foreach ($read_messages as $read_message) $currently_read[] = $read_message['message_id'];

            // If all selected messages are already read, don't continue
            if (count($read_messages) == count($message_ids))
            {
                return FALSE;
            }

            // Flag each message as read, if not already read
            $query = DB::insert(self::READ_BY_TABLE, array('message_id', 'user_id'));
            foreach ($message_ids as $message_id)
            {
                if ( ! in_array($message_id, $currently_read))
                {
                    $query->values(array($message_id, $user_id));
                }
            }
            return $query->execute();
        }
    }

    public static function add_recipient_whitelist($email)
    {
        try {
            DB::insert(self::WHITELIST_TABLE)
                ->values(array('email' => $email))
                ->execute();
        } catch (Exception $exc) {
        }
    }

    public static function remove_recipient_whitelist($email)
    {
        DB::DELETE(self::WHITELIST_TABLE)
            ->WHERE('email', '=', $email)
            ->execute();
    }

    public static function list_recipient_whitelist()
    {
        return DB::select('*')
            ->from(self::WHITELIST_TABLE)
            ->order_by('email')
            ->execute()
            ->as_array();
    }

    public static function test_recipient_whitelist($email, $cache = true)
    {
        static $whitelist = null;

        if (!$cache) {
            $whitelist = null;
        }
        if ($whitelist === null) {
            $wemails = self::list_recipient_whitelist();
            $whitelist = array();
            foreach ($wemails as $wemail) {
                $whitelist[$wemail['email']] = $wemail['email'];
            }
        }

        return isset($whitelist[$email]);
    }

    public static function check_message_targets($message_id, $contact_id)
    {
        $emails = DB::select('value')
            ->from(array(Model_Contacts3::CONTACT_NOTIFICATION_RELATION_TABLE, 'emails'))
                ->join(array(Model_Contacts3::CONTACTS_TABLE, 'contacts'), 'inner')
                    ->on('emails.group_id', '=', 'contacts.notifications_group_id')
            ->where('contacts.id', '=', $contact_id)
            ->and_where('emails.deleted', '=', 0)
            ->execute()
            ->as_array(null, 'value');
        if (count($emails) == 0) {
            return false;
        }
        $has_targets = DB::select('*')
            ->from(array(self::MESSAGES_TABLE, 'messages'))
                ->join(array(self::MESSAGE_TARGETS_TABLE, 'targets'), 'inner')
                    ->on('messages.id', '=', 'targets.message_id')
                ->join(array(self::MESSAGE_FTARGETS_TABLE, 'ftargets'), 'inner')
                    ->on('targets.id', '=', 'ftargets.target_id')
            ->where('messages.id', '=', $message_id)
            ->where('ftargets.target', 'in', $emails)
            ->execute()
            ->as_array();
        return count($has_targets) > 0;
    }
}
?>
