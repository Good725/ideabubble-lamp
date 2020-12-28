<?php defined('SYSPATH') or die('No direct access allowed.');

class Model_User extends Model_Auth_User {

    protected $_has_many = [
        'contacts' => ['model' => 'Contacts3_Contact', 'foreign_key' => 'linked_user_id']
    ];

    protected $_belongs_to = [
        'role' => ['model' => 'Roles', 'foreign_key' => 'role_id']
    ];

    public function get_full_name()
    {
        if (!$this->id) {
            return '';
        }

        $contact = $this->contacts->find_undeleted();
        $full_name = $contact->get_full_name();

        if (!trim($full_name)) {
            $full_name  =trim($this->name.' '.$this->surname);
        }

        return $full_name;
    }

} // End User Model