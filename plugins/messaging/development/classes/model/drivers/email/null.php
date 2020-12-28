<?php

class Model_Messaging_Driver_Email_Null implements Model_Messaging_Driver_Interface
{
    public function __construct()
    {
    }

    public function setup()
    {
        if (Kohana::$environment != Kohana::PRODUCTION) {
            DB::query(null, "Update plugin_messaging_drivers set is_default='NO'")->execute();
            DB::query(null, "Update plugin_messaging_drivers set is_default='YES' WHERE provider='null'")->execute();
        } else {
            DB::query(null, "Update plugin_messaging_drivers set is_default='NO' WHERE provider='null'")->execute();
        }
    }
    
    public function settings_groupname()
    {
        return 'Null Settings';
    }

    public function has_send()
    {
        return true;
    }
    
    public function has_sendlist()
    {
        return false;
    }
    
    public function has_receive_cron()
    {
        return false;
    }
    
    public function has_receive_callback()
    {
        return false;
    }
    
    public function has_status()
    {
        return false;
    }
    
    public function has_message_subject()
    {
        return true;
    }

    public function max_message_size()
    {
        return 10000000;
    }

    public function is_ready()
    {
        return true;
    }

    public function has_attachment()
    {
        return true;
    }

    public function send($from, $to, $subject, $message, $attachments = array(), $replyto = null)
    {
        return array('status' => 'SENT', 'id' => date('YmdHis'), 'details' => '');
    }
    
    public function sendlist($from, $list_id, $subject, $message, $attachments = array(), $replyto = null)
    {
        throw new Exception(__CLASS__ . ' sendlist is not implemented');
    }

    public function receive_cron()
    {
        throw new Exception(__CLASS__ . ' receive cron is not implemented');
    }

    public function message_status($message_id)
    {
        throw new Exception(__CLASS__ . ' does not provide message status');
    }
    
    public function handle_status_callback()
    {

    }

    public function handle_receive_callback()
    {

    }

    public function __toString()
    {
        return 'Null';
    }

    public function has_settings_ui()
    {
        return false;
    }
}
?>