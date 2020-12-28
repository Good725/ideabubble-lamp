<?php defined('SYSPATH') or die('No direct script access.');

class Model_Contacts3_Residence extends ORM
{
    protected $_table_name     = 'plugin_contacts3_residences';
    protected $_deleted_column = 'delete';
    protected $_primary_key    = 'address_id';

    protected $_has_many = [
        'contacts' => ['model' => 'Contacts3_Contact', 'foreign_key' => 'residence']
    ];


}