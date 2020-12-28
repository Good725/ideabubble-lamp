<?php defined('SYSPATH') or die('No direct script access.');

class Model_Contacts3_Files extends ORM
{
    protected $_table_name  = 'plugin_ib_educate_contacts3_contact_has_files';
    protected $_primary_key = 'id';
    protected $_deleted_column = 'deleted';

    protected $_has_one = [
        'contact' => ['model' => 'Contacts3_Contact', 'foreign_key' => 'contact_id', 'far_key' => 'contact_id'],
    ];


    public function find_all_undeleted()
    {
        // Records are not flagged as deleted from this table. The corresponding record in the bookings table is used.
        return $this
            ->where('plugin_ib_educate_bookings_has_files.deleted', '=', 0)
            ->find_all();
    }

    public function save_data($data) {
        $this->values($data);
        $this->save();
    }

    public static function is_shared($contact_id, $file_id) {
        $query = DB::select('id')
            ->from('plugin_ib_educate_contacts3_contact_has_files')
            ->where('contact_id', '=', $contact_id)
            ->where('document_id', '=', $file_id)->execute()->current();
        return !empty($query);
    }

}