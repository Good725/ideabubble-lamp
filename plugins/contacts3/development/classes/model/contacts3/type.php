<?php defined('SYSPATH') or die('No direct script access.');

class Model_Contacts3_Type extends ORM
{
    protected $_table_name = 'plugin_contacts3_contact_type';
    protected $_primary_key = 'contact_type_id';
    protected $_deleted_column = 'deletable';
    protected $_has_many = [
        'contacts' => ['model' => 'Contacts3_Contact', 'foreign_key' => 'type']
    ];


}