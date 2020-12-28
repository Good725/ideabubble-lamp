<?php defined('SYSPATH') or die('No direct script access.');

class Model_Booking_Files extends ORM
{
    protected $_table_name  = 'plugin_ib_educate_bookings_has_files';
    protected $_primary_key = 'id';
    protected $_deleted_column = 'deleted';

    protected $_has_one = [
        'contact' => ['model' => 'Booking_Booking', 'foreign_key' => 'booking_id', 'far_key' => 'booking_id'],
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

    public static function is_shared($booking_id, $file_id, $transaction_id = null) {
        $query = DB::select('*')
            ->from('plugin_ib_educate_bookings_has_files')
            ->join(array('plugin_files_file'), 'inner')
            ->on('plugin_ib_educate_bookings_has_files.document_id', '=' , 'plugin_files_file.id')
            ->where('booking_id', '=', $booking_id)
            ->where('document_id', '=', $file_id)
            ->and_where('plugin_ib_educate_bookings_has_files.deleted', '=', 0)
            ->and_where('plugin_files_file.deleted', '=', 0);
        if($transaction_id) {
            $query->and_where('transaction_id' , '=', $transaction_id);
        }
           $result =  $query->execute()->current();
        return !empty($result);
    }

    public static function get_files_by_booking_id($booking_id, $shared_only = false) {
        $query = DB::select('*')
            ->from('plugin_ib_educate_bookings_has_files')
            ->join(array('plugin_files_file'), 'inner')
            ->on('plugin_ib_educate_bookings_has_files.document_id', '=' , 'plugin_files_file.id')
            ->where('booking_id', '=', $booking_id)
            ->and_where('plugin_ib_educate_bookings_has_files.deleted', '=', 0)
            ->and_where('plugin_files_file.deleted', '=', 0);
        if($shared_only) {
            $query->and_where('shared', '=' , $shared_only);
        }
        $result = $query->execute()->as_array();
        return $result;
    }

    public static function get_files_by_booking_and_transaction($booking_id, $transaction_id, $shared_only = false) {
        $query = DB::select('id')
            ->from('plugin_ib_educate_bookings_has_files')
            ->join(array('plugin_files_file'), 'inner')
            ->where('booking_id', '=', $booking_id)
            ->and_where('transaction_id', '=', $transaction_id)
            ->and_where('deleted', '=', 1);
        if($shared_only) {
            $query->and_where('shared', '=' , $shared_only);
        }
        $result = $query->execute()->as_array();
        return $result;
    }

}