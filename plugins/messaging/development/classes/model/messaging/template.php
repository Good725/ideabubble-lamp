<?php defined('SYSPATH') or die('No direct script access.');

class Model_Messaging_Template extends ORM
{
    protected $_table_name = 'plugin_messaging_notification_templates';

    protected $_belongs_to = [
        'creator' => ['model' => 'User', 'foreign_key' => 'created_by'],
    ];

    public function is_system()
    {
        return (bool) $this->creator->role->master_group;
    }
}
