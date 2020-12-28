<?php

class Model_Todos_AssigneeAlertAction extends Model_Automations_Action
{
    const NAME = 'Todo Alert Assignee';
    public function __construct()
    {
        $this->name = self::NAME;
        $this->purpose = Model_Automations::PURPOSE_SAVE;
        $this->params = array('todo_id');
    }

    public function run($params = array())
    {
        $date_format = Settings::instance()->get('date_format');
        if ($date_format == '') {
            $date_format = 'd-m-Y H:i';
        }

        $todo = Model_Todos::get($params['todo_id']);
        if (array_key_exists('assignees', $params)) {
            $assignees = $params['assignees'];
        } else {
            $assignees = Model_Todos::get_assignees_assigned_to_todo($params['todo_id']);
        }

        if (count($assignees) == 0) {
            return;
        }

        $mm = new Model_Messaging();
        $recipients = array();
        $params = array(
            'title' => $todo['title'],
            'summary' => $todo['summary'],
            'type' => $todo['type'],
            'date' => date($date_format, strtotime($todo['datetime_end'])),
            'link' => URL::site('/admin/todos/view/' . $todo['id']),
            'id' => $todo['id']
        );
        $template = $mm->get_notification_template('todo-alert-assignee-email');
        foreach ($assignees as $assignee) {
            $params['name'] = $assignee['first_name'] . ' ' . $assignee['last_name'];
            $message = $template['message'];
            $message = $mm->render_template($message, $params);
            $recipients[] = array(
                'target_type' => 'CMS_CONTACT3',
                'target' => $assignee['contact_id'],
                'message' => $message
            );
        }
        $mm->send_template('todo-alert-assignee-email', null, null, $recipients);
    }
}