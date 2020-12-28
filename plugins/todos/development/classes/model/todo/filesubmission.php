<?php defined('SYSPATH') or die('No direct script access.');

class Model_Todo_FileSubmission extends ORM
{
    protected $_table_name = 'plugin_todos_todos2_file_submissions';

    protected $_belongs_to = [
        'media' => ['model' => 'Media_Upload', 'foreign_key' => 'file_id'],
    ];

    function calculate_version()
    {
        if ($this->version) {
            // If this has a version number, return it
            return $this->version;
        } else {
            // Otherwise, get the version number of the last file the contact submitted for this to-do
            // and increase it by 1
            $past_version = ORM::factory('Todo_FileSubmission')
                ->where('todo_id', '=', $this->todo_id)
                ->where('contact_id', '=', $this->contact_id)
                ->order_by('version', 'desc')
                ->find_undeleted();

            return (int) $past_version->version + 1;
        }
    }

}