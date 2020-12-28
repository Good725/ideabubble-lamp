<?php defined('SYSPATH') or die('No direct script access.');

class Model_Automations extends ORM
{
    const TABLE_AUTOMATIONS = 'plugin_automations';
    const TABLE_HAS_SEQUENCES = 'plugin_automations_has_sequences';
    const TABLE_CONDITIONS = 'plugin_automations_has_conditions';
    const TABLE_CONDITION_VALUES = 'plugin_automations_has_conditions_has_values';
    const TABLE_INTERVALS = 'plugin_automations_has_intervals';
    const TABLE_HAS_SEQUENCE_CONDITIONS = 'plugin_automations_has_sequences_has_conditions';
    const TABLE_HAS_SEQUENCE_CONDITION_VALUES = 'plugin_automations_has_sequences_has_conditions_has_values';
    const TABLE_HAS_SEQUENCE_INTERVALS = 'plugin_automations_has_sequences_has_intervals';
    const TABLE_HAS_SEQUENCE_TODO_CONTACTS = 'plugin_automations_has_sequences_has_todo_contacts';
    const TABLE_HAS_SEQUENCE_TODO_SCHEDULES = 'plugin_automations_has_sequences_has_todo_schedules';
    const TABLE_HAS_SEQUENCE_ATTACHMENTS = 'plugin_automations_has_sequences_has_attachments';
    const TABLE_HAS_SEQUENCE_RECIPIENTS = 'plugin_automations_has_sequences_has_message_recipients';
    const TABLE_LOG = 'plugin_automations_log';
    const TABLE_LOG_MESSAGES = 'plugin_automations_log_messages';
    const TABLE_LOG_TODOS = 'plugin_automations_log_todos';
    const TABLE_TRIGGER_ENABLE = 'plugin_automations_triggers_enabled';

    const PURPOSE_SAVE = 'save';
    const PURPOSE_DELETE = 'delete';

    protected $_table_name = self::TABLE_AUTOMATIONS;
    protected $_publish_column       = 'published';
    protected $_created_by_column    = 'created_by';
    protected $_date_created_column  = 'created_date';
    protected $_modified_by_column   = 'updated_by';
    protected $_date_modified_column = 'updated_date';

    protected static $actions = array();
    protected static $triggers = array();
    protected static $data = array();

    public static $now = null;

    public static $run_messages = array();

    public static $cache_file = '';

    public static function get_cache_filename()
    {
        return $cache_file = Kohana::$cache_dir . '/' . $_SERVER['HTTP_HOST'] . '/automations.json';
    }

    public static function add_trigger($trigger)
    {
        self::$triggers[$trigger->get_name()] = $trigger;
    }

    /**
     * @param $args array
     *      - enabled_only  boolean - only return triggers enabled in the settings
     *      - linked_only   boolean - only return triggers that are used by existing automations
     *
     * @return array - list of triggers
     */
    public static function get_triggers($args = [])
    {
        $enabled_only = !empty($args['enabled_only']);
        $linked_only  = !empty($args['linked_only']);

        $triggers = self::$triggers;

        if ($enabled_only) {
            $enabled_triggers = Model_Automations_Settings::get_enabled_triggers();
            foreach ($triggers as $i => $trigger) {
                if (!in_array($trigger->name, $enabled_triggers)) {
                    unset ($triggers[$i]);
                }
            }
        }

        if ($linked_only) {
            // Get automations with distinct triggers
            $automations = DB::select('trigger')->from(self::TABLE_AUTOMATIONS)->where('deleted', '=', 0)
                ->distinct('trigger')->execute()->as_array('trigger');

            // Filter triggers to just the ones from the previous query
            foreach ($triggers as $i => $trigger) {
                if (!in_array($trigger->name, array_keys($automations))) {
                    unset ($triggers[$i]);
                }
            }

        }

        return $triggers;
    }

    public static function add_action($action)
    {
        self::$actions[$action->get_name()] = $action;
    }

    public static function get_actions()
    {
        return self::$actions;
    }

    protected static function run_action($sequence, $params)
    {
        if (isset(self::$actions[$sequence['action']])) {
            self::$actions[$sequence['action']]->run($params);
            if (self::$actions[$sequence['action']]->message) {
                self::$run_messages[] = self::$actions[$sequence['action']]->message;
            }
        }
    }

    protected static function run_another_automation($sequence, $params)
    {
        foreach (self::$data as $automation) {
            if ($automation['id'] == $sequence['run_after_automation_id']) {
                Model_Automations::run_triggers($automation['trigger'], $params);
            }
        }
    }

    protected static function run_message($sequence, $params)
    {
        $mm = new Model_Messaging();
        $driver = $sequence['message_driver'];
        $subject = $sequence['message_subject'];
        $message = $sequence['message_body'];
        if ($sequence['message_template_id']) {
            $template = $mm->get_notification_template($sequence['message_template_id']);
            if ($subject == '') {
                $subject = $template['subject'];
            }
            if ($message == '') {
                $message = $template['message'];
            }
        }

        $recipients = array();
        foreach ($sequence['recipients'] as $recipient) {
            $target = $recipient['recipient'];
            if (preg_match('/\@([a-z0-9\_]+)\@/i', $recipient['recipient'], $target_param)) {
                $target = $params[$target_param[1]];
            } else if (preg_match('/\$([a-z0-9\_]+)\$?/i', $recipient['recipient'], $target_param)) {
                $target = $params[$target_param[1]];
            }

            if ($target) {
                if (is_numeric($target)) {
                    if ($driver == 'dashboard') {
                        $c3 = new Model_Contacts3($target);
                        $recipients[] = array(
                            'target_type' => 'CMS_USER',
                            'target' => $c3->get_linked_user_id()
                        );
                    } else {
                        $recipients[] = array(
                            'target_type' => 'CMS_CONTACT3',
                            'target' => $target
                        );
                    }
                } else {
                    if (is_array($target)) {
                        foreach ($target as $tt) {
                            $recipients[] = array(
                                'target_type' => 'CMS_CONTACT3',
                                'target' => $tt
                            );
                        }
                    } else {
                        $recipients[] = array(
                            'target_type' => 'EMAIL',
                            'target' => $target
                        );
                    }
                }
            }
        }
        if (count($recipients) == 0) {
            return null;
        }
        $attachments = array();
        $clear_tmp_files = array();
        foreach ($sequence['attachments'] as $attachment) {
            $attachment_name = Model_Files::get_file_name($attachment['file_id']);
            $file_ext = strtolower(substr($attachment_name, strrpos($attachment_name, '.') + 1));
            if ($file_ext == 'docx') { // always convert to pdf if docx attached
                $attachment['process_docx'] = 1;
            }
            if ($attachment['process_docx'] == 1) {
                $attachment_fullpath = Model_Files::file_path($attachment['file_id']);
                $tmpname = tempnam(Kohana::$cache_dir, 'attachment_docx');
                $docx = new IbDocx();
                $docx->processDocx($attachment_fullpath, $params, $tmpname);
                $clear_tmp_files[] = $tmpname;
                if ($attachment['convert_docx_to_pdf'] == 1) {
                    if (!strrpos($attachment_name, '.')) { // no extension in filename
                        $attachment_name .= '.pdf';
                    }
                    $docx->generate_pdf($tmpname, $tmpname . '.pdf');
                    $attachments[] = array(
                        'path' => $tmpname . '.pdf',
                        'name' => str_replace('.docx', '.pdf', $attachment_name)
                    );
                    $clear_tmp_files[] = $tmpname . '.pdf';
                } else {
                    if (!strrpos($attachment_name, '.')) { // no extension in filename
                        $attachment_name .= '.docx';
                    }
                    $attachments[] = array(
                        'path' => $tmpname,
                        'name' => $attachment_name
                    );
                }

                if ($attachment['share'] == 1 && isset($params['studentid'])) {
                    $contact_dir_id = Model_Files::get_directory_id_r('/contacts/' . $params['studentid']);
                    $file_id = Model_Files::create_file(
                        $contact_dir_id,
                        $attachment_name,
                        array(
                            'tmp_name' => $tmpname . ($attachment['convert_docx_to_pdf'] == 1 ? '.pdf' : ''),
                            'name' => $attachment_name,
                            'size' => filesize($tmpname),
                            'type' => $attachment['convert_docx_to_pdf'] == 1 ? 'application/pdf' : 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
                        )
                    );
                    $cf = new Model_Contacts3_Files();
                    $cf->save_data(array('contact_id' => $params['studentid'], 'document_id' => $file_id));
                }
            } else {
                $attachments[] = array(
                    'file_id' => $attachment['file_id']
                );
            }
        }
        if (@$params['automation_attachments']) {
            foreach ($params['automation_attachments'] as $attachment) {
                $attachments[] = $attachment;
            }
        }

        $subject = $mm->render_template($subject, $params);
        $message = $mm->render_template($message, $params);
        if (count($attachments) > 0) {
            $message = array(
                'content' => $message,
                'attachments' => $attachments
            );
        }
        $message_id = $mm->send($driver, null, null, $recipients, $message, $subject);
        foreach ($clear_tmp_files as $clear_tmp_file) {
            @unlink($clear_tmp_file);
        }
        return $message_id;
    }

    protected static function run_create_todo($sequence, $params)
    {
        $todo = array();
        $todo['type'] = 'Task';
        $todo['title'] = $sequence['todo_title'];
        $todo['datetime_end'] = date('Y-m-d H:i:s', self::$now);
        $todo_id = Model_Todos::save($todo);
        if ($sequence['todo_schedules']) {
            Model_Todos::save_has_schedules($todo_id, $sequence['todo_schedules']);
        }
        if (@$params['scheduleid']) {
            Model_Todos::save_has_schedules($todo_id, array($params['scheduleid']));
        }
        if ($sequence['todo_assignee'] == 'Attendee') {
            $assignees = array();
            if (@$params['studentid']) {
                $assignees[] = $params['studentid'];
            }
            /*if (@$params['leadbookerid']) {
                $assignees[] = $params['leadbookerid'];
            }*/
            $assignees = array_unique($assignees);
            Model_Todos::save_assigned_students($todo_id, $assignees);
        } else if ($sequence['todo_assignee'] == 'Trainer') {
            $assignees = array();
            if (@$params['trainerid']) {
                $assignees[] = $params['trainerid'];
            }
            $assignees = array_unique($assignees);
            Model_Todos::save_assigned_students($todo_id, $assignees);
        } else {
            Model_Todos::save_assigned_students($todo_id, $sequence['todo_contacts']);
        }
        return $todo_id;
    }

    public static function check_duplicate($sequence_id, $key)
    {
        return DB::select('*')
            ->from(self::TABLE_LOG)
            ->where('sequence_id', '=', $sequence_id)
            ->and_where('do_not_repeat_key', '=', $key)
            ->execute()
            ->current();
    }

    public static function log_run($sequence, $params, $message_ids, $todo_ids, $do_not_repeat_key)
    {
        $inserted = DB::insert(self::TABLE_LOG)
            ->values(
                array(
                    'sequence_id' => $sequence['id'],
                    'parameters' => json_encode($params, JSON_PRETTY_PRINT),
                    'executed' => date::now(),
                    'do_not_repeat_key' => $do_not_repeat_key
                )
            )->execute();
        $log_id = $inserted[0];

        if (!empty($message_ids)) {
            if (is_numeric($message_ids)) {
                $message_ids = array($message_ids);
            }

            foreach ($message_ids as $message_id) {
                DB::insert(self::TABLE_LOG_MESSAGES)
                    ->values(array('log_id' => $log_id, 'message_id' => $message_id))
                    ->execute();
            }
        }

        if (!empty($todo_ids)) {
            if (is_numeric($todo_ids)) {
                $todo_ids = array($todo_ids);
            }

            foreach ($todo_ids as $todo_id) {
                DB::insert(self::TABLE_LOG_TODOS)
                    ->values(array('log_id' => $log_id, 'todo_id' => $todo_id))
                    ->execute();
            }
        }

        return $log_id;
    }

    public static function log_list($params = array())
    {
        $select = DB::select('log.*', 'has_messages.message_id', 'has_todos.todo_id')
            ->from(array(self::TABLE_LOG, 'log'))
                ->join(array(Model_Automations::TABLE_HAS_SEQUENCES, 'sequences'), 'left')
                    ->on('log.sequence_id', '=', 'sequences.id')
                ->join(array(Model_Automations::TABLE_LOG_MESSAGES, 'has_messages'), 'left')
                    ->on('log.id', '=', 'has_messages.log_id')
                ->join(array(Model_Automations::TABLE_LOG_TODOS, 'has_todos'), 'left')
                    ->on('log.id', '=', 'has_todos.log_id');
        if (@$params['automation_id']) {
            $select->and_where('automation_id', '=', $params['automation_id']);
        }
        if (@$params['sequence_id']) {
            $select->and_where('sequence_id', '=', $params['sequence_id']);
        }
        $select->order_by('log.id', 'desc');
        $log = $select->execute()->as_array();
        return $log;
    }

    public static function get_sequence_variables($sequence)
    {
        $variables = array();
        preg_match_all('/(\@|\$)([a-z0-9\_]+)(\@|\$)/is', $sequence['message_subject'], $match);
        $variables = array_merge($variables, $match[2]);
        preg_match_all('/(\@|\$)([a-z0-9\_]+)(\@|\$)/is', $sequence['message_body'], $match);
        $variables = array_merge($variables, $match[2]);

        foreach ($sequence['attachments'] as $attachment) {
            if ($attachment['process_docx'] == 1) {
                $docx = new IbDocx();
                $docx->open(Model_Files::file_path($attachment['file_id']));
                $docx_variables = $docx->get_variables();
                $docx->close();
                $variables = array_merge($variables, $docx_variables);
            }
        }

        foreach ($sequence['recipients'] as $recipient) {
            if (preg_match('/\@([a-z0-9\_]+)\@/i', $recipient['recipient'], $target_param)) {
                $variables[] = $target_param[1];
            } else {
                if (preg_match('/\$([a-z0-9\_]+)\$?/i', $recipient['recipient'], $target_param)) {
                    $variables[] = $target_param[1];
                }
            }
        }

        if ($sequence['todo_assignee'] == 'Attendee') {
            $variables[] = 'studentid';
            $variables[] = 'trainerid';
            $variables[] = 'leadbookerid';
            $variables[] = 'scheduleid';
        }

        if ($sequence['run_type'] == 'action') {
            $variables = array_merge($variables, self::$actions[$sequence['action']]->params);
            if (in_array('booking_id', self::$actions[$sequence['action']]->params)) {
                $variables[] = 'bookingid';
                if (in_array('contact_id', self::$actions[$sequence['action']]->params)) {
                    $variables[] = 'studentid';
                }
                if (in_array('schedule_id', self::$actions[$sequence['action']]->params)) {
                    $variables[] = 'schedule_id';
                }
            }
        }

        $variables = array_unique($variables);

        return $variables;
    }

    public static function run_sequence($trigger, $sequence, $params)
    {
        if (self::$now == null) {
            self::$now = time();
        }
        if ($filter_data = $trigger->filter($params, $sequence)) {
            $results = array();
            if (!$trigger->multiple_results) {
                $results = array($filter_data);
            } else {
                $results = $filter_data;
            }

            foreach ($results as $result) {
                $do_not_repeat_key = null;
                $run_params = array_merge($params, $result);
                /*$run_params['date'] = date('d/m/Y', Model_Automations::$now);
                $run_params['time'] = date('H:i', Model_Automations::$now);*/
                try {
                    $message_id = null;
                    $todo_id = null;
                    if ($sequence['run_type'] == 'create_todo') {
                        $todo_id = self::run_create_todo($sequence, $run_params);
                    } else if ($sequence['run_type'] == 'automation') {
                        self::run_another_automation($sequence, $run_params);
                    } else if ($sequence['run_type'] == 'message') {
                        $message_id = self::run_message($sequence, $run_params);
                    } else {
                        self::run_action($sequence, $run_params);
                    }
                    self::log_run($sequence, $run_params, $message_id, $todo_id, $do_not_repeat_key);
                } catch (Exception $exc) {
                    Model_Errorlog::save($exc);
                }
            }
        }
    }

    public static function run_cron_sequence($trigger, $sequence, $test = false)
    {
        if (self::$now == null) {
            self::$now = time();
        }
        try {
            $records = $trigger->filter(array(), $sequence);
            $date = date('d/m/Y', Model_Automations::$now);
            $time = date('H:i', Model_Automations::$now);
            /*foreach ($records as $i => $params) {
                $records[$i]['date'] = $date;
                $records[$i]['time'] = $time;
            }*/
            if ($test) {
                return $records;
            }

            if ($sequence['repeat_by_field']) { // repeat by field is not implemented
                /*$grouped_records = array();
                foreach ($records as $record) {
                    if (!isset($grouped_records[$record[$sequence['repeat_by_field']]])) {
                        $grouped_records[$record[$sequence['repeat_by_field']]] = array();
                    }
                    $grouped_records[$record[$sequence['repeat_by_field']]][] = $record;
                }
                foreach ($records as $params) {
                    if ($sequence['run_type'] == 'create_todo') {
                        self::run_create_todo($sequence, $params);
                    } else if ($sequence['run_type'] == 'automation') {
                        self::run_another_automation($sequence, $params);
                    } else if ($sequence['run_type'] == 'message') {
                        self::run_message($sequence, $params);
                    } else {
                        self::run_action($sequence, $params);
                    }
                }*/
            } else {
                foreach ($records as $params) {
                    $do_not_repeat_key = $sequence['run_type'].':';
                    if (count($trigger->no_duplicate_email_tag) > 0) {
                        foreach ($trigger->no_duplicate_email_tag as $no_duplicate_email_tag) {
                            $do_not_repeat_key .= $no_duplicate_email_tag . $params[$no_duplicate_email_tag];
                        }
                        if (self::check_duplicate($sequence['id'], $do_not_repeat_key)) {
                            continue;
                        }
                    }
                    $message_id = null;
                    $todo_id = null;
                    if ($sequence['run_type'] == 'create_todo') {
                        $todo_id = self::run_create_todo($sequence, $params);
                    } else if ($sequence['run_type'] == 'automation') {
                        self::run_another_automation($sequence, $params);
                    } else if ($sequence['run_type'] == 'message') {
                        $message_id = self::run_message($sequence, $params);
                    } else {
                        self::run_action($sequence, $params);
                    }
                    self::log_run($sequence, $params, $message_id, $todo_id, $do_not_repeat_key);
                }
            }
            return $records;
        } catch (Exception $exc) {
            Model_Errorlog::save($exc);
        }
    }

    public static function run_triggers($name, $params)
    {
        self::$run_messages = array();
        if (isset(self::$triggers[$name])) {
            foreach (self::$data as $automation) {
                try {
                    $trigger = self::$triggers[$name];
                    //$trigger = new Model_Automations_Trigger();
                    if ($automation['published'] == 0) {
                        continue;
                    }
                    if ($automation['trigger'] == $trigger->get_name()) {
                        foreach ($automation['sequences'] as $sequence) {
                            // run sequence if no interval or has an immediate interval
                            if (count($sequence['intervals']) == 0) {
                                self::run_sequence($trigger, $sequence, $params);
                            } else {
                                foreach ($sequence['intervals'] as $interval) {
                                    if ($interval['is_periodic'] == -1) {
                                        self::run_sequence($trigger, $sequence, $params);
                                    }
                                }
                            }
                        }
                    }
                } catch (Exception $exc) {
                    Model_Errorlog::save($exc);
                }
            }
        }
    }

    public static function get_data($id)
    {
        $automation = DB::select('*')
            ->from(self::TABLE_AUTOMATIONS)
            ->where('id', '=', $id)
            ->and_where('deleted', '=', 0)
            ->execute()
            ->current();

        /*
         * conditions and intervals are only sequence level
        $automation['conditions'] = DB::select('*')
            ->from(self::TABLE_CONDITIONS)
            ->where('automation_id', '=', $automation['id'])
            ->execute()
            ->as_array();
        foreach ($automation['conditions'] as $index_condition => $condition) {
            $automation['conditions'][$index_condition]['values'] = DB::select('*')
                ->from(self::TABLE_CONDITION_VALUES)
                ->where('condition_id', '=', $condition['id'])
                ->execute()
                ->as_array();
        }
        $automation['intervals'] = DB::select('*')
            ->from(self::TABLE_INTERVALS)
            ->where('automation_id', '=', $automation['id'])
            ->execute()
            ->as_array();
        */

        if ($automation['crontask_id']) {
            $crontask = new Model_Cron($automation['crontask_id']);
            $automation['crontask'] = $crontask->get_instance();
        }

        $automation['sequences'] = DB::select('*')
            ->from(self::TABLE_HAS_SEQUENCES)
            ->where('automation_id', '=', $automation['id'])
            ->execute()
            ->as_array();
        foreach ($automation['sequences'] as $sequence_index => $sequence) {
            $sequence['conditions'] = DB::select('*')
                ->from(self::TABLE_HAS_SEQUENCE_CONDITIONS)
                ->where('sequence_id', '=', $sequence['id'])
                ->execute()
                ->as_array();
            foreach ($sequence['conditions'] as $condition_index => $condition) {
                $sequence['conditions'][$condition_index]['values'] = DB::select('*')
                    ->from(self::TABLE_HAS_SEQUENCE_CONDITION_VALUES)
                    ->where('sequence_condition_id', '=', $condition['id'])
                    ->execute()
                    ->as_array();
                if (strpos($condition['field'], 'date_interval') !== false) {
                    if(in_array($condition['operator'], array('=', '>', '>='))) {
                        $sequence['conditions'][$condition_index]['execute'] = 'after';
                    } else {
                        $sequence['conditions'][$condition_index]['execute'] = 'before';
                    }
                }

                foreach ($sequence['conditions'][$condition_index]['values'] as $value) {
                    $sequence['conditions'][$condition_index]['val'][] = $value['val'];
                }
            }

            $sequence['intervals'] = DB::select('*')
                ->from(self::TABLE_HAS_SEQUENCE_INTERVALS)
                ->where('sequence_id', '=', $sequence['id'])
                ->execute()
                ->as_array();
            $sequence['recipients'] = DB::select('*')
                ->from(self::TABLE_HAS_SEQUENCE_RECIPIENTS)
                ->where('sequence_id', '=', $sequence['id'])
                ->execute()
                ->as_array();
            $sequence['attachments'] = DB::select('attachments.*', 'files.name')
                ->from(array(self::TABLE_HAS_SEQUENCE_ATTACHMENTS, 'attachments'))
                    ->join(array(Model_Files::TABLE_FILE, 'files'), 'inner')
                        ->on('attachments.file_id', '=', 'files.id')
                ->where('sequence_id', '=', $sequence['id'])
                ->execute()
                ->as_array();

            $sequence['todo_contacts'] = DB::select('contact_id')
                ->from(self::TABLE_HAS_SEQUENCE_TODO_CONTACTS)
                ->where('sequence_id', '=', $sequence['id'])
                ->execute()
                ->as_array('contact_id');

            $sequence['todo_schedules'] = DB::select('schedule_id')
                ->from(self::TABLE_HAS_SEQUENCE_TODO_SCHEDULES)
                ->where('sequence_id', '=', $sequence['id'])
                ->execute()
                ->as_array('schedule_id');
            $automation['sequences'][$sequence_index] = $sequence;
        }

        return $automation;
    }

    public static function load()
    {
        // cache all automation data to load faster (takes 0.2-0.5 seconds without cache)
        $cache_file = self::get_cache_filename();
        if (file_exists($cache_file)) {
            self::$data = json_decode(file_get_contents($cache_file), true);
            return self::$data;
        }
        try {
            $delete = DB::delete(self::TABLE_AUTOMATIONS);
            if (count(self::$triggers)) {
                $delete->or_where('trigger', 'not in', array_keys(self::$triggers));
            }
            $delete->execute();

            $delete = DB::delete(self::TABLE_HAS_SEQUENCES)
                ->where('run_type', '=', 'action');
            if (count(self::$actions)) {
                $delete->and_where('action', 'not in', array_keys(self::$actions));
            }
            $delete->execute();

            $ats = DB::select('automations.*', DB::expr('max(log.executed) as last_executed'))
                ->from(array(self::TABLE_AUTOMATIONS, 'automations'))
                    ->join(array(self::TABLE_HAS_SEQUENCES, 'has_sequences'), 'left')
                        ->on('automations.id', '=', 'has_sequences.automation_id')
                    ->join(array(self::TABLE_LOG, 'log'), 'left')
                        ->on('has_sequences.id', '=', 'log.sequence_id')
                ->where('deleted', '=', 0)
                ->order_by('trigger')
                ->group_by('automations.id')
                ->execute()
                ->as_array();
            foreach ($ats as $index_at => $at) {
                $ats[$index_at] = self::get_data($at['id']);
                $ats[$index_at]['last_executed'] = $at['last_executed'];
            }
            self::$data = $ats;
            file_put_contents($cache_file, json_encode($ats, JSON_PRETTY_PRINT));
            return $ats;
        } catch (Exception $exc) {
            throw $exc;
        }
    }

    public static function get_automations()
    {
        return self::$data;
    }

    public function get_for_datatable($filters = [], $datatable_args = [])
    {
        $column_definitions = [
            'automations.id',
            'automations.name',
            'automations.trigger',
            'automations.created_date',
            'automations.updated_date',
            [DB::expr("MAX(`log`.`executed`)"), 'last_executed'],
            null, // actions
        ];

        $q = $this->select([DB::expr("MAX(`log`.`executed`)"), 'last_executed']);

        $automations = $q
            ->apply_filters($filters)
            ->apply_datatable_args($datatable_args, $column_definitions)
            ->find_all_undeleted();

        $datatable_args['unlimited'] = true;
        $all_automations = $q
            ->apply_filters($filters)
            ->apply_datatable_args($datatable_args, $column_definitions)
            ->find_all_undeleted();

        $rows = [];
        foreach ($automations as $automation) {
            $rows[] = [
                $automation->id,
                htmlspecialchars($automation->name),
                htmlspecialchars($automation->trigger),
                IbHelpers::relative_time_with_tooltip($automation->created_date),
                IbHelpers::relative_time_with_tooltip($automation->updated_date),
                IbHelpers::relative_time_with_tooltip($automation->last_executed),
                '<a class="edit-link" href="/admin/automations/edit_automation/'.$automation->id.'">Edit</a>'
            ];
        }

        return [
            'aaData' => $rows,
            'iTotalDisplayRecords' => $all_automations->count(),
            'iTotalRecords' => $automations->count(),
            'sEcho' => intval($datatable_args['sEcho'])
        ];
    }

    public function apply_filters($filters = [])
    {
        $q = $this
            ->select([DB::expr("COUNT(`log`.`executed`)"), 'count_executed'])
            ->join(array(self::TABLE_HAS_SEQUENCES, 'has_sequences'), 'left')
                ->on('automations.id', '=', 'has_sequences.automation_id')
            ->join(array(self::TABLE_LOG, 'log'), 'left')
                ->on('has_sequences.id', '=', 'log.sequence_id')
            ->group_by('automations.id');

        if (!empty($filters['start_date'])) {
            $this->where('log.executed', '>=', $filters['start_date'].' 00:00:00');
        }

        if (!empty($filters['end_date'])) {
            $this->where('log.executed', '<=', $filters['end_date'].' 23:59:59');
        }

        if (!empty($filters['statuses'])) {
            $this->and_where_open();

            if (in_array('active', $filters['statuses'])) {
                $this->or_where('automations.published', '=', 1);
            }

            if (in_array('inactive', $filters['statuses'])) {
                $this->or_where('automations.published', '=', 0);
            }

            if (in_array('complete', $filters['statuses'])) {
                $this->or_where('log.executed', 'is not', null);
            }

            $this->and_where_close();
        }

        if (!empty($filters['triggers'])) {
            $this->where('automations.trigger', 'in', $filters['triggers']);
        }

        $this->where('automations.deleted', '=', 0);

        return $q;
    }

    public static function get_reports($filters = [])
    {
        $reports = [];
        $q = ORM::factory('Automations')->apply_filters($filters);

        // Get total
        $total = clone $q;
        $active = clone $q;
        $inactive = clone $q;

        $total    = $total->find_all();
        $active   = $active->where('automations.published',  '=', 1)->find_all();
        $inactive = $inactive->where('automations.published',  '=', 0)->find_all();

        $executed = 0;
        foreach ($total as $automation) {
            $executed += $automation->count_executed;
        }

        $reports[] = ['amount' => $total->count(),    'text' => 'Total'];
        $reports[] = ['amount' => $active->count(),   'text' => 'Active'];
        $reports[] = ['amount' => $inactive->count(), 'text' => 'Inactive'];
        $reports[] = ['amount' => $executed,          'text' => 'Executed'];

        return $reports;
    }

    protected static function save_sequence($data)
    {
        $sequence = array();

        if (@$data['id']) {
            $sequence['id'] = $data['id'];
        }
        $sequence['automation_id'] = $data['automation_id'];
        $sequence['run_type'] = $data['run_type'];
        $sequence['repeat_by_field'] = $data['repeat_by_field'];

        $sequence['conditions_mode'] = $data['conditions_mode'];
        $sequence['wait_type'] = null;
        $sequence['wait'] = null;
        $sequence['action'] = null;
        $sequence['run_after_automation_id'] = null;
        $sequence['message_template_id'] = null;
        $sequence['message_driver'] = null;
        $sequence['message_subject'] = null;
        $sequence['message_body'] = null;
        $sequence['todo_title'] = null;
        $sequence['todo_assignee'] = null;

        if ($sequence['run_type'] == 'action') {
            $sequence['action'] = $data['action'];
        } else if ($sequence['run_type'] == 'message') {
            $sequence['message_template_id'] = $data['message_template_id'];
            $sequence['message_driver'] = $data['message_driver'];
            $sequence['message_subject'] = $data['message_subject'];
            $sequence['message_body'] = $data['message_body'];
            $sequence['message_from'] = $data['message_from'];
        } else if ($sequence['run_type'] == 'automation') {
            $sequence['run_after_automation_id'] = $data['run_after_automation_id'];
        } else if ($sequence['run_type'] == 'create_todo') {
            $sequence['todo_title'] = $data['todo_title'];
            $sequence['todo_assignee'] = $data['todo_assignee'];
        }

        if ($sequence['id']) {
            DB::update(self::TABLE_HAS_SEQUENCES)->set($sequence)->where('id', '=', $sequence['id'])->execute();
        } else {
            $inserted = DB::insert(self::TABLE_HAS_SEQUENCES)->values($sequence)->execute();
            $sequence['id'] = $inserted[0];
        }

        DB::delete(self::TABLE_HAS_SEQUENCE_CONDITIONS)->where('sequence_id', '=', $sequence['id'])->execute();
        if (isset($data['condition']))
        foreach ($data['condition'] as $condition) {
            $condition_inserted = DB::insert(self::TABLE_HAS_SEQUENCE_CONDITIONS)
                ->values(
                    array(
                        'sequence_id' => $sequence['id'],
                        'field' => $condition['field'],
                        'operator' => $condition['operator']
                    )
                )->execute();
            if (isset($condition['val'])) {
                if (!is_array($condition['val'])) {
                    $condition['val'] = array($condition['val']);
                }
                foreach ($condition['val'] as $condition_value) {
                    DB::insert(self::TABLE_HAS_SEQUENCE_CONDITION_VALUES)
                        ->values(
                            array(
                                'sequence_condition_id' => $condition_inserted[0],
                                'val' => $condition_value
                            )
                        )->execute();
                }
            }
        }

        $existing_intervals = DB::select('*')
            ->from(self::TABLE_HAS_SEQUENCE_INTERVALS)
            ->where('sequence_id', '=', $sequence['id'])
            ->execute()
            ->as_array();
        foreach ($existing_intervals as $existing_interval) {
            if ($existing_interval['crontask_id']) {
                $crontask = new Model_Cron($existing_interval['crontask_id']);
                $crontask->set(array('delete' => 1))->save();
            }
        }
        DB::delete(self::TABLE_HAS_SEQUENCE_INTERVALS)->where('sequence_id', '=', $sequence['id'])->execute();
        if (isset($data['interval']))
        foreach ($data['interval'] as $interval_index => $interval) {
            if (!isset($interval['is_periodic'])) {
                $interval['is_periodic'] = -1;
            }
            if ($interval['is_periodic'] == 0 && @Datetime::createFromFormat("d/M/Y H:i", $interval['execute_once_at_datetime'])) {
                $interval['execute_once_at_datetime'] = Datetime::createFromFormat("d/M/Y H:i", $interval['execute_once_at_datetime'])->format("Y-m-d H:i");
            }
            if ($interval['frequency'] != '') {
                //normally this should not happen.
                $frequency_test = json_decode($interval['frequency'], true);
                if ($frequency_test['minute'][0] === null || $frequency_test['minute'][0] == '') {
                    $frequency_test['minute'][0] = '00';
                }
                if ($frequency_test['hour'][0] === null || $frequency_test['hour'][0] == '') {
                    $frequency_test['hour'][0] = '00';
                }
                if ($frequency_test['day_of_week'][0] === null || $frequency_test['day_of_week'][0] == '') {
                    $frequency_test['day_of_week'][0] = '1';
                }
                if ($frequency_test['day_of_month'][0] === null || $frequency_test['day_of_month'][0] == '') {
                    $frequency_test['day_of_month'][0] = '1';
                }
                if ($frequency_test['month'][0] === null || $frequency_test['month'][0] == '') {
                    $frequency_test['month'][0] = '*';
                }
                $interval['frequency'] = json_encode($frequency_test);
            }
            $interval_inserted = DB::insert(self::TABLE_HAS_SEQUENCE_INTERVALS)
                ->values(
                    array(
                        'sequence_id' => $sequence['id'],
                        'is_periodic' => $interval['is_periodic'],
                        'execute_once_at_datetime' => $interval['execute_once_at_datetime'],
                        'frequency' => $interval['frequency'],
                        'interval_amount' => $interval['interval_amount'],
                        'interval_type' => $interval['interval_type'],
                        'execute' => $interval['execute'],
                        'interval_operator' => $interval['interval_operator'],
                        'interval_field' => $interval['interval_field'],
                        'allow_duplicate_message' => @$interval['do_not_repeat'] == 1 ? 0 : 1
                    )
                )->execute();
            $interval_id = $interval_inserted[0];
            if ($interval['is_periodic'] == 0){
                $frequency_time = strtotime($interval['execute_once_at_datetime']);
                $frequency = array(
                    'minute' => array(date('i', $frequency_time)),
                    'hour' => array(date('H', $frequency_time)),
                    'day_of_week' => array('*'),
                    'day_of_month' => array(date('d', $frequency_time)),
                    'month' => array(date('m', $frequency_time)),
                );
                $crontask = new Model_Cron();
                $crontask->set(
                    array(
                        'internal_only' => 1,
                        'title' => 'sequence: ' . $sequence['id'] . ' - interval:' . $interval_id . ' - task',
                        'frequency' => json_encode($frequency),
                        'plugin_id' => Model_Plugin::get_plugin_by_name('automations')['id'],
                        'action' => 'cron',
                        'extra_parameters' =>
                            'automation_id=' . $sequence['automation_id'] . ' ' .
                            'sequence_id=' . $sequence['id'] . ' ' .
                            'interval_id=' . $interval_id . ' ' .
                            '--uri="/frontend/automations/cron"',
                        'publish' => $data['published']
                    )
                );
                $crontask->save();
                DB::update(self::TABLE_HAS_SEQUENCE_INTERVALS)
                    ->set(array('crontask_id' => $crontask->get_id()))
                    ->where('id', '=', $interval_id)
                    ->execute();
            }
            if ($interval['is_periodic'] == 1 && $interval['frequency'] != '') {
                $crontask = new Model_Cron();
                $crontask->set(
                    array(
                        'internal_only' => 1,
                        'title' => 'sequence: ' . $sequence['id'] . ' - interval:' . $interval_id . ' - task',
                        'frequency' => $interval['frequency'],
                        'plugin_id' => Model_Plugin::get_plugin_by_name('automations')['id'],
                        'action' => 'cron',
                        'extra_parameters' =>
                            'automation_id=' . $sequence['automation_id'] . ' ' .
                            'sequence_id=' . $sequence['id'] . ' ' .
                            'interval_id=' . $interval_id . ' ' .
                            '--uri="/frontend/automations/cron"',
                        'publish' => $data['published']
                    )
                );
                $crontask->save();
                DB::update(self::TABLE_HAS_SEQUENCE_INTERVALS)
                    ->set(array('crontask_id' => $crontask->get_id()))
                    ->where('id', '=', $interval_id)
                    ->execute();
            }
        }

        DB::delete(self::TABLE_HAS_SEQUENCE_ATTACHMENTS)->where('sequence_id', '=', $sequence['id'])->execute();
        if ($sequence['run_type'] == 'message' && isset($data['attachment'])) {
            if (is_array($data['attachment']))
            foreach ($data['attachment'] as $attachment) {
                if (@$attachment['template_id'] != '') {
                    $attachment['file_id'] = $attachment['template_id'];
                }
                if (@$attachment['file_id'] == null) {
                    continue;
                }
                DB::insert(self::TABLE_HAS_SEQUENCE_ATTACHMENTS)
                    ->values(
                        array(
                            'sequence_id' => $sequence['id'],
                            'file_id' => $attachment['file_id'],
                            'process_docx' => $attachment['process_docx'],
                            'convert_docx_to_pdf' => isset($attachment['convert_docx_to_pdf']) ? $attachment['convert_docx_to_pdf'] : 1,
                            'share' => isset($attachment['share']) ? $attachment['share'] : 0
                        )
                    )->execute();
            }
        }

        DB::delete(self::TABLE_HAS_SEQUENCE_TODO_CONTACTS)->where('sequence_id', '=', $sequence['id'])->execute();
        DB::delete(self::TABLE_HAS_SEQUENCE_TODO_SCHEDULES)->where('sequence_id', '=', $sequence['id'])->execute();
        if ($sequence['run_type'] == 'create_todo') {
            if ($sequence['todo_assignee'] == 'Contact') {
                if (isset($data['todo_contact_id']))
                foreach ($data['todo_contact_id'] as $todo_contact) {
                    DB::insert(self::TABLE_HAS_SEQUENCE_TODO_CONTACTS)
                        ->values(
                            array('sequence_id' => $sequence['id'], 'contact_id' => $todo_contact)
                        )->execute();
                }
            } else {
                if (isset($data['todo_schedule_id']))
                foreach ($data['todo_schedule_id'] as $todo_schedule) {
                    DB::insert(self::TABLE_HAS_SEQUENCE_TODO_SCHEDULES)
                        ->values(
                            array('sequence_id' => $sequence['id'], 'schedule_id' => $todo_schedule)
                        )->execute();
                }
            }
        }

        DB::delete(self::TABLE_HAS_SEQUENCE_RECIPIENTS)->where('sequence_id', '=', $sequence['id'])->execute();
        if (!isset($sequence['recipient'])) {
            if (@$data['message_to'] != '') {
                $sequence['recipient'] = array(
                    array(
                        'recipient' => trim($data['message_to'])
                    )
                );
            }
        }
        if (isset($data['recipient'])) {
            $skip_duplicate = array();
            foreach ($data['recipient'] as $recipient) {
                if (in_array($recipient['recipient'], $skip_duplicate)) {
                    continue;
                }
                DB::insert(self::TABLE_HAS_SEQUENCE_RECIPIENTS)
                    ->values(
                        array(
                            'sequence_id' => $sequence['id'],
                            'recipient' => $recipient['recipient'],
                            'recipient_type' => @$recipient['recipient_type'],
                            'x_details' => @$recipient['x_details']
                        )
                    )->execute();
                $skip_duplicate[] = $recipient['recipient'];
            }
        }
    }

    public static function save_automation($data)
    {
        // clean up cache file everytime an automation is changed
        @unlink(self::get_cache_filename());
        try {
            Database::instance()->begin();

            if (!isset(self::$triggers[$data['trigger']])) {
                return false;
            }

            $automation = array();
            if (isset($data['id'])) {
                $automation['id'] = $data['id'];
            }
            $automation['published'] = @$data['published'] ?: 0;
            $automation['name'] = $data['name'];
            $automation['trigger'] = $data['trigger'];
            $automation['conditions_mode'] = $data['conditions_mode'];


            $user = Auth::instance()->get_user();
            if (empty($automation['id'])) {
                $automation['created_by'] = $user['id'];
                $automation['created_date'] = date::now();
            }

            $automation['updated_by'] = $user['id'];
            $automation['updated_date'] = date::now();

            if (!is_numeric(@$automation['id'])) {
                $inserted = DB::insert(self::TABLE_AUTOMATIONS)->values($automation)->execute();
                $automation['id'] = $inserted[0];
            } else {
                DB::update(self::TABLE_AUTOMATIONS)->set($automation)->where('id', '=', $automation['id'])->execute();
                $id = $automation['id'];
            }

            /*crons are for sequences only
             * if (self::$triggers[$data['trigger']]->get_initiator() == Model_Automations_Trigger::INITIATOR_CRON) {
                $crontask = new Model_Cron(@$data['crontask_id']);
                $crontask->set(
                    array(
                        'internal_only' => 1,
                        'title' => $data['name'] . ' - task',
                        'frequency' => $data['frequency'],
                        'plugin_id' => Model_Plugin::get_plugin_by_name('automations')['id'],
                        'action' => 'cron',
                        'extra_parameters' => 'automation_id=' . $automation['id'],
                        'publish' => $automation['published']
                    )
                );
                $crontask->save();
                $automation['crontask_id'] = $crontask->get_id();
                DB::update(self::TABLE_AUTOMATIONS)->set($automation)->where('id', '=', $automation['id'])->execute();
            }*/

            /*
             * conditions and intervals are at sequence level
            DB::delete(self::TABLE_INTERVALS)->where('automation_id', '=', $id)->execute();
            DB::delete(self::TABLE_CONDITIONS)->where('automation_id', '=', $id)->execute();

            if (isset($data['interval'])) {
                foreach ($data['interval'] as $interval) {
                    DB::insert(self::TABLE_INTERVALS)->values($interval)->execute();
                }
            }

            if (isset($data['condition'])) {
                foreach ($data['condition'] as $condition) {
                    $cond_inserted = DB::insert(self::TABLE_CONDITIONS)
                        ->values(
                            array(
                                'automation_id'=> $id,
                                'field' => $condition['field'],
                                'operator' => $condition['operator'],
                            )
                        )
                        ->execute();
                    DB::insert(self::TABLE_CONDITION_VALUES)
                        ->values(array('condition_id' => $cond_inserted[0], 'val' => $condition['val']))
                        ->execute();
                }
            }
            */

            foreach ($data['sequence'] as $sequence) {
                $sequence['automation_id'] = $automation['id'];
                $sequence['published'] = $automation['published'];
                self::save_sequence($sequence);
            }

            Database::instance()->commit();
            return $automation['id'];
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }
    }

    public static function delete_automation($id)
    {
        $user = Auth::instance()->get_user();
        DB::update(self::TABLE_AUTOMATIONS)
            ->set(
                array('deleted' => 1, 'updated_by' => $user['id'], 'updated_date' => date::now())
            )
            ->where('id', '=', $id)->execute();
    }


    public static function filter_query($select, $conditions)
    {

    }
}