<?php
require_once Kohana::find_file('vendor/', 'twilio/Services/Twilio');

class Model_Messaging_Driver_Sms_Twilio implements Model_Messaging_Driver_Interface
{
    protected $api;
    
    public function __construct()
    {
        $twilio_account_sid = Settings::instance()->get('twilio_account_sid');
        $twilio_auth_token = Settings::instance()->get('twilio_auth_token');
        if($twilio_account_sid != null && $twilio_auth_token != null){
            $this->api = new Services_Twilio($twilio_account_sid, $twilio_auth_token);
        }
        
    }

    public function setup()
    {
        DB::query(0, "INSERT IGNORE INTO `engine_settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) values ('twilio_account_sid', 'Twilio Account SID', '', '', '', '', '', 'both', '', 'text', 'Twilio Settings', 0, '')" )->execute();
        DB::query(0, "INSERT IGNORE INTO `engine_settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) values ('twilio_auth_token', 'Twilio Auth Token', '', '', '', '', '', 'both', '', 'text', 'Twilio Settings', 0, '')" )->execute();
        DB::query(0, "INSERT IGNORE INTO `engine_settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) values ('twilio_phone_number', 'Twilio Phone Number', '', '', '', '', '', 'both', '', 'text', 'Twilio Settings', 0, '')" )->execute();
    }
    
    public function settings_groupname()
    {
        return 'Twilio Settings';
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
        return true;
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
        return 160;
    }

    public function is_ready()
    {
        if ($this->api) {
            return true;
        } else {
            return false;
        }
    }

    public function has_attachment()
    {
        return false;
    }

    public function send($from, $to, $subject, $message, $attachments = array(), $replyto = null)
    {
        if(!$this->api){
            throw new Exception('twilio has not been setup');
        }

        if (count($attachments)) {
            throw new Exception("Attachments are not supported");
        }

        if($from == null){
            $from = Settings::instance()->get('twilio_phone_number');
        }
        //do not apply country code for "From" incase it's alpha numberic. e.g.
        if (Settings::instance()->get('twilio_apply_code')) {
            $to   = self::apply_country_code($to);
            //$from = self::apply_country_code($from);
        }

        try{
            $params = array( 
                'To' => $to,
                'From' => $from, 
                'Body' => $message,  
                'StatusCallback' => URL::site('/') . "frontend/messaging/status_callback?driver=sms-twilio", 
            );
            $sms = $this->api->account->messages->create($params);
            $response = array( 'id' => $sms->sid, 'status' => 'NO', 'details' => '' );
        } catch(Exception $exc){
            $response = array( 'id' => '', 'status' => 'ERROR', 'details' => $exc->getMessage() );
        }
        return $response;
    }

    public function apply_country_code($number)
    {
        // Remove everything, but numbers and plus symbols
        $number = preg_replace('/[^\+0-9]/', '', $number);

        // If the number is using 00 as the international prefix, use "+" instead
        // e.g. 00353830000000 -> +3538630000000
        $number = preg_replace('/\b00/', '+', $number);

        // If the country does not being with a country code, use the one from the settings
        // e.g. 0830000000 -> +353830000000
        if (strpos($number, '0') === 0) {
            // Ensure the code begins with a "+" and only contains numbers after it.
            $default_country_code = Settings::instance()->get('twilio_default_country_code');
            $default_country_code = '+'.preg_replace('/[^\0-9]/', '', $default_country_code);

            // Replace the initial 0 with the country code
            $number = preg_replace('/\b0/', $default_country_code, $number);
        }
        $number = str_replace('++', '+', $number);
        return $number;
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
        throw new Exception(__CLASS__ . ' send is not implemented');
    }

    public function message_status($message_id)
    {
        throw new Exception(__CLASS__ . ' send is not implemented');
    }
    
    public function handle_status_callback()
    {
        //file_put_contents("sb" . time() . ".txt", var_export(array(&$_GET, &$_POST), 1) );
        if(isset($_POST['SmsSid'])){
            $result = array('driver_remote_id' => $_POST['SmsSid']);
            //twilio possible status list: failed, sent, delivered, or undelivered
            $result['delivery_status'] = strtoupper($_POST['SmsStatus']);
            return array($result);
        } else {
            return false;
        }
    }
    
    public function handle_receive_callback()
    {
        try{
            Database::instance()->begin();
            $driver_id = DB::select('id')
                            ->from('plugin_messaging_drivers')
                            ->where('driver', '=', 'sms')
                            ->and_where('provider', '=', 'twilio')
                            ->execute()
                            ->get('id');
            $imessage = array();
            $imessage['driver_id'] = $driver_id;
            $imessage['sender'] = $_POST['From'];
            $imessage['subject'] = '';
            $imessage['message'] = $_POST['Body'];
            $imessage['created_by'] = 1;
            $imessage['date_created'] = date::now();
            $imessage['date_updated'] = date::now();
            $imessage['ip_address'] = @$_SERVER['REMOTE_ADDR'];
            $imessage['user_agent'] = @$_SERVER['HTTP_USER_AGENT'];
            $imessage['status'] = 'RECEIVED';

            if (Model_Messaging::is_muted($imessage['sender'])){
                $imessage['is_spam'] = 1;
            }

            if (Model_Messaging::is_unavailable($_POST['To'])) {
                $imessage['received_when_unavailable'] = 1;
            }
            
            $message_result = DB::insert('plugin_messaging_messages', array_keys($imessage))
                                    ->values($imessage)->execute();
            $imessage['id'] = $message_id = $message_result[0];
            
            $itarget = array();
            $itarget['message_id'] = $message_id;
            $itarget['target_type'] = 'PHONE';
            $itarget['target'] = $_POST['To'];
            $itarget_result = DB::insert('plugin_messaging_message_targets', array_keys($itarget))->values($itarget)->execute();
            $final_target = array();
            $final_target['target_id'] = $itarget_result[0];
            $final_target['target_type'] = 'PHONE';
            $final_target['target'] = $_POST['To'];
            DB::insert('plugin_messaging_message_final_targets', array_keys($final_target))->values($final_target)->execute();
            Database::instance()->commit();
            header("content-type: text/xml");
echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>
<Response>
<Message>received</Message>
</Response>
";
            Model_Messaging::execute_post_processors($message_id);
        } catch(Exception $e){
            Database::instance()->rollback();
            throw $e;
        }
    }

    public function __toString()
    {
        return 'Twilio';
    }

    public function has_settings_ui()
    {
        return false;
    }
}
?>