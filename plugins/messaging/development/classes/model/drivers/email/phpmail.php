<?php
require_once Kohana::find_file('vendor', 'Swift/swift_required');
require_once APPPATH . '/vendor/Swift/classes/EmailValidator/EmailValidator.php';
require_once APPPATH . '/vendor/Swift/classes/EmailValidator/RFCValidation.php';

class Model_Messaging_Driver_Email_Phpmail implements Model_Messaging_Driver_Interface
{
    protected $mail;
    protected $mailer;
    
    public function __construct()
    {
        $this->mailer = new Swift_SendmailTransport();
        $this->mail = new Swift_Mailer($this->mailer);
    }

    public function setup()
    {
        DB::query(0, "INSERT INTO `engine_settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) values ('phpmail_from_email', 'PHPmail From Email', 'testing@websitecms.ie', 'testing@websitecms.ie',   'testing@websitecms.ie',  'testing@websitecms.ie',  'testing@websitecms.ie',  'both', '', 'text', 'Phpmail Settings', 0, '')" )->execute();
    }
    
    public function settings_groupname()
    {
        return 'Phpmail Settings';
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
        if ($this->mail) {
            return true;
        } else {
            return false;
        }
    }

    public function has_attachment()
    {
        return true;
    }

    public function send($from, $to, $subject, $message, $attachments = array(), $replyto = null)
    {
        if(!$this->mail){
            throw new Exception('phpmail has not been setup');
        }

        if($from == null){
            $from = Settings::instance()->get('phpmail_from_email');
        }

        $smessage = new Swift_Message();

        $smessage->setSubject($subject);
        $smessage->setFrom($from);
        if ($replyto) {
            $smessage->setReplyTo($replyto);
        }
        $smessage->setTo($to);
        $smessage->setBody($message, 'text/html');
        if (count($attachments) > 0) {
            foreach ($attachments as $attachment) {
                if (isset($attachment['path'])) {
                    $smessage->attach(Swift_Attachment::fromPath($attachment['path'])->setFilename($attachment['name']));
                } else {
                    $smessage->attach(new Swift_Attachment($attachment['content'], $attachment['name'], $attachment['type']));
                }
            }
        }

        $result = $this->mailer->send($smessage);

        if($result){
            return array('status' => 'SENT', 'id' => null, 'details' => '');
        } else {
            return array('status' => 'FAILED', 'id' => sha1(microtime(true)), 'details' => '');
        }
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
        return 'Phpmail';
    }

    public function has_settings_ui()
    {
        return false;
    }
}
?>