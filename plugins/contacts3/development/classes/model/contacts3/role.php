<?php defined('SYSPATH') or die('No direct script access.');

class Model_Contacts3_Role extends ORM
{
    protected $_table_name = 'plugin_contacts3_roles';

    protected $_has_many = [
        'contacts' => ['model' => 'Contacts3_Contact', 'through' => 'plugin_contacts3_contact_has_roles', 'foreign_key' => 'role_id', 'far_key' => 'contact_id'],
    ];
}