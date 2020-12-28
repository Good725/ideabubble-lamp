<?php defined('SYSPATH') or die('No direct script access.');

class Model_Todo_HasAssignee extends ORM
{
    protected $_table_name = 'plugin_todos_todos2_has_assigned_contacts';

    protected $_belongs_to = [
        'assignee' => ['model' => 'Contacts3_Contact', 'foreign_key' => 'contact_id'],
        'todo'     => ['model' => 'Todo_Item',         'foreign_key' => 'todo_id'],
    ];

    public function files($args = [])
    {
        $args['direction'] = isset($args['direction']) ? $args['direction'] : 'asc';

        return ORM::factory('Todo_FileSubmission')
            ->where('todo_id', '=', $this->todo_id)
            ->where('contact_id', '=', $this->contact_id)
            ->order_by('version', $args['direction']);
    }

    public function get_first_file()
    {
        return $this->files(['direction' => 'asc'])->find_undeleted();
    }

    public function get_last_file()
    {
        return $this->files(['direction' => 'desc'])->find_undeleted();
    }

}