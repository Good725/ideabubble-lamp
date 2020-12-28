<?php defined('SYSPATH') or die('No direct script access.');

class Model_Contacts3_NotificationGroup extends ORM
{
    protected $_table_name     = 'plugin_contacts3_notification_groups';
    protected $_deleted_column = 'deleted';

    protected $_has_many = [
        'contact_notifications' => ['model' => 'Contacts3_Notification', 'foreign_key' => 'group_id'],
    ];

}