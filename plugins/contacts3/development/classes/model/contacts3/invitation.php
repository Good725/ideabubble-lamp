<?php defined('SYSPATH') or die('No direct script access.');

class Model_Contacts3_Invitation extends ORM
{
    protected $_table_name = 'plugin_contacts3_invitations';

    protected $_belongs_to = [
        'invited_by_contact' => ['model' => 'Contacts3_Contact', 'foreign_key' => 'invited_by_contact_id'],
        'invited_contact'    => ['model' => 'Contacts3_Contact', 'foreign_key' => 'invited_contact_id'],
    ];
}