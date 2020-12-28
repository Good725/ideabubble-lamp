<?php defined('SYSPATH') or die('No direct script access.');

class Model_Contacts3_Family extends ORM
{
    protected $_table_name     = 'plugin_contacts3_family';
    protected $_primary_key    = 'family_id';
    protected $_deleted_column = 'delete';

    protected $_has_many = [
        'members' => ['model' => 'Contacts3_Contact', 'foreign_key' => 'family_id'],
    ];

    protected $_belongs_to = [
        'primary_contact' => ['model' => 'Contacts3_Contact', 'foreign_key' => 'primary_contact_id'],
    ];
}