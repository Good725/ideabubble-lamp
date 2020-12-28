<?php

class Model_Messaging_Driver_Sms_Bongo implements Model_Messaging_Driver_Interface
{
    protected $curl;
    public $baseUrl = 'http://www.bongolive.co.tz/api/';

    public $sender = '';
    public $username = '';
    public $password = '';
    public $apikey = '';
    public $appendtext = '';
    
    public function __construct()
    {
        $this->sender = Settings::instance()->get('bongo_sender');
        $this->username = Settings::instance()->get('bongo_username');
        $this->password = Settings::instance()->get('bongo_password');
        $this->apikey = Settings::instance()->get('bongo_apikey');
        $this->appendtext = Settings::instance()->get('bongo_append_text');


        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        $this->tmpfile = tempnam('/tmp/', 'bongoapi');
        curl_setopt($this->curl, CURLOPT_COOKIEFILE, $this->tmpfile);
        curl_setopt($this->curl, CURLOPT_COOKIEFILE, $this->tmpfile);
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
    }

    public function __destruct()
    {
        curl_close($this->curl);
        @unlink($this->tmpfile);
    }

    protected function get($url, $params)
    {
        $url = $this->baseUrl . $url . '?' . http_build_query($params);
        curl_setopt($this->curl, CURLOPT_POST, false);
        curl_setopt($this->curl, CURLOPT_HTTPGET, true);
        curl_setopt($this->curl, CURLOPT_URL, $url);
        $response = curl_exec($this->curl);
        $this->last_request = $params;
        $this->last_response = $response;
        $this->last_info = curl_getinfo($this->curl);
        Model_ExternalRequests::create(
            $this->baseUrl,
            $url,
            var_export($params, 1),
            $response,
            var_export($this->last_info, 1)
        );
        return $response;
    }

    public function setup()
    {
        DB::query(0, "INSERT INTO `engine_settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) values ('bongo_sender', 'Bongo Sender Name', '', '', '', '', '', 'both', '', 'text', 'Bongo API Settings', 0, '')" )->execute();
        DB::query(0, "INSERT INTO `engine_settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) values ('bongo_username', 'Bongo Username', '', '', '', '', '', 'both', '', 'text', 'Bongo API Settings', 0, '')" )->execute();
        DB::query(0, "INSERT INTO `engine_settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) values ('bongo_password', 'Bongo Password', '', '', '', '', '', 'both', '', 'text', 'Bongo API Settings', 0, '')" )->execute();
        DB::query(0, "INSERT INTO `engine_settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) values ('bongo_apikey', 'Bongo API Key', '', '', '', '', '', 'both', '', 'text', 'Bongo API Settings', 0, '')" )->execute();
        DB::query(0, "INSERT INTO `engine_settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) values ('bongo_append_text', 'Bongo Append Text', '', '', '', '', '', 'both', '', 'text', 'Bongo API Settings', 0, '')" )->execute();
    }
    
    public function settings_groupname()
    {
        return 'Bongo API Settings';
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
        return false;
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
        if ($this->sender && $this->username && $this->password && $this->apikey) {
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
        if(!$this->is_ready()){
            throw new Exception('twilio has not been setup');
        }

        if (count($attachments)) {
            throw new Exception("Attachments are not supported");
        }

        if($from == null){
            $from = $this->sender;
        }
        if ($this->appendtext != '') {
            $message .= ' ' . $this->appendtext;
        }

        try{
            //"http://www.bongolive.co.tz/api/sendSMS.php?sendername=$sendername&username=$username&password=$password&apikey=$apiKey&destnum=$destnum&message=$message&senddate=$senddate
            $result = $this->get(
                'sendSMS.php',
                array(
                    'sendername' => $from,
                    'username' => $this->username,
                    'password' => $this->password,
                    'apikey' => $this->apikey,
                    'destnum' => $to,
                    'message' => $message
                )
            );
            if ($result < 0) {
                $response = array( 'id' => '', 'status' => $result, 'details' => '' );
            } else {
                $response = array( 'id' => $result, 'status' => $result, 'details' => '' );
            }
        } catch(Exception $exc){
            $response = array( 'id' => '', 'status' => 'ERROR', 'details' => $exc->getMessage() );
        }
        return $response;
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
        throw new Exception(__CLASS__ . ' handle_status_callback is not implemented');
    }
    
    public function handle_receive_callback()
    {
        try{
            Database::instance()->begin();
            $driver_id = DB::select('id')
                            ->from('plugin_messaging_drivers')
                            ->where('driver', '=', 'sms')
                            ->and_where('provider', '=', 'bongo')
                            ->execute()
                            ->get('id');
            $imessage = array();
            $imessage['driver_id'] = $driver_id;
            $imessage['sender'] = $_REQUEST['org'];
            $imessage['subject'] = '';
            $imessage['message'] = $_REQUEST['message'];
            $imessage['created_by'] = 1;
            $imessage['date_created'] = date::now();
            $imessage['date_updated'] = date::now();
            $imessage['ip_address'] = @$_SERVER['REMOTE_ADDR'];
            $imessage['user_agent'] = @$_SERVER['HTTP_USER_AGENT'];
            $imessage['status'] = 'RECEIVED';
            $imessage['is_spam'] = 0;

            if (Model_Messaging::is_muted($imessage['sender'])){
                $imessage['is_spam'] = 1;
            }

            if (Model_Messaging::is_unavailable($_REQUEST['dest'])) {
                $imessage['received_when_unavailable'] = 1;
            }

            $message_result = DB::insert('plugin_messaging_messages', array_keys($imessage))
                                    ->values($imessage)->execute();
            $imessage['id'] = $message_id = $message_result[0];
            
            $itarget = array();
            $itarget['message_id'] = $message_id;
            $itarget['target_type'] = 'PHONE';
            $itarget['target'] = $_REQUEST['dest'];
            $itarget_result = DB::insert('plugin_messaging_message_targets', array_keys($itarget))->values($itarget)->execute();
            $final_target = array();
            $final_target['target_id'] = $itarget_result[0];
            $final_target['target_type'] = 'PHONE';
            $final_target['target'] = $_REQUEST['dest'];
            DB::insert('plugin_messaging_message_final_targets', array_keys($final_target))->values($final_target)->execute();
            Database::instance()->commit();

            if ($imessage['is_spam'] == 0) {
                Model_Messaging::execute_post_processors($message_id);
            }

            header("content-type: text/plain");

            /*echo "\nGET contains: " . print_r($_GET, true);
            echo "\nPOST contains: " . print_r($_POST, true);
            echo "\nREQUEST contains: " . print_r($_REQUEST, true);*/
            echo "OK";
        } catch(Exception $e){
            Database::instance()->rollback();
            throw $e;
        }
    }

    public function __toString()
    {
        return 'Bongo';
    }

    public function has_settings_ui()
    {
        return false;
    }
}
?>