<?php
class Model_Messaging_Driver_Dashboard_System implements Model_Messaging_Driver_Interface
{
    public function setup()
    {
    }
    
    public function settings_groupname()
    {
        return null;
    }

    public function has_send()
    {
        return true;
    }
    
    public function has_sendlist()
    {
        return false;
    }

    public function has_receive_callback()
    {
        return false;
    }
    
    public function has_receive_cron()
    {
        return false;
    }
    
    public function has_status()
    {
        return true;
    }
    
    public function has_message_subject()
    {
        return false;
    }

    public function max_message_size()
    {
        return 1000;
    }

    public function has_attachment()
    {
        return false;
    }

    public function is_ready()
    {
        return true;
    }

    public function send($from, $to, $subject, $message, $attachments = array(), $replyto = null)
    {
        if (count($attachments)) {
            throw new Exception("Attachments are not supported");
        }
        return array('status' => 'NO', 'id' => uniqid(), 'details' => '');
    }
    
    public function sendlist($from, $list_id, $subject, $message, $attachments = array(), $replyto = null)
    {
        throw new Exception(__CLASS__ . ' sendlist is not implemented');
        if (count($attachments)) {
            throw new Exception("Attachments are not supported");
        }
    }

    public function receive_cron()
    {
        throw new Exception(__CLASS__ . ' receive cron is not implemented');
    }

    public function message_status($message_id)
    {
    }
    
    public function handle_status_callback()
    {
        
    }
    
    public function handle_receive_callback()
    {
        throw new Exception(__CLASS__ . ' receive callback is not implemented');
    }
    
    public function __toString()
    {
        return 'System';
    }

    public function has_settings_ui()
    {
        return false;
    }
}
?>