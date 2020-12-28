<?php

class Model_Messaging_Driver_Email_Sparkpost implements Model_Messaging_Driver_Interface
{
    protected $curl;
    public $key = '';
    public $baseUrl = 'https://api.sparkpost.com/api/v1/';
    
    public function __construct()
    {
        $sparkpost_key = Settings::instance()->get('sparkpost_key');
        if($sparkpost_key != null){
            $this->key = $sparkpost_key;
        }

        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('Accept: application/json','Content-Type: application/json'));
        $this->tmpfile = tempnam('/tmp/', 'sparkpost');
        curl_setopt($this->curl, CURLOPT_COOKIEFILE, $this->tmpfile);
        curl_setopt($this->curl, CURLOPT_COOKIEFILE, $this->tmpfile);
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
    }

    public function __destruct()
    {
        curl_close($this->curl);
        @unlink($this->tmpfile);
    }

    protected function post($url, $params)
    {
        curl_setopt($this->curl, CURLOPT_USERPWD, $this->key);
        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, json_encode($params));
        curl_setopt($this->curl, CURLOPT_URL, $url);
        $response = curl_exec($this->curl);
        $this->last_request = $params;
        $this->last_response = $response;
        $this->last_info = curl_getinfo($this->curl);
        return json_decode($response, true);
    }

    public function setup()
    {
        DB::query(0, "INSERT INTO `engine_settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) values ('sparkpost_key',        'Sparkpost Key',        'bMukrNy2NHnJBR_OOCoatw', 'bMukrNy2NHnJBR_OOCoatw', 'bMukrNy2NHnJBR_OOCoatw', 'bMukrNy2NHnJBR_OOCoatw', 'bMukrNy2NHnJBR_OOCoatw', 'both', '', 'text', 'Sparkpost Settings', 0, '')" )->execute();
        DB::query(0, "INSERT INTO `engine_settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) values ('sparkpost_from_email', 'Sparkpost From Email', 'testing@websitecms.ie', 'testing@websitecms.ie',   'testing@websitecms.ie',  'testing@websitecms.ie',  'testing@websitecms.ie',  'both', '', 'text', 'Sparkpost Settings', 0, '')" )->execute();
        DB::query(0, "INSERT INTO `engine_settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) values ('sparkpost_from_name',  'Sparkpost From Name',  'websitecms.ie',         'websitecms.ie',           'websitecms.ie',          'websitecms.ie',          'websitecms.ie',          'both', '', 'text', 'Sparkpost Settings', 0, '')" )->execute();
    }
    
    public function settings_groupname()
    {
        return 'Sparkpost Settings';
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
        if ($this->key) {
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
        if(!$this->key){
            throw new Exception('sparkpost has not been setup');
        }

        if($from == null){
            $from = Settings::instance()->get('sparkpost_from_email');
        }

        $message = array(
            'content' => array(
                'from' => array(
                    'email' => $from
                ),
                'html' => $message,
                'subject' => $subject,
            ),
            'recipients' => array(
                array(
                    'address' => array(
                        'email' => $to
                    )
                )
            )
        );

        if ($replyto) {
            $message['content']['reply_to'] = $replyto;
        }

        if (count($attachments) > 0) {
            $message['content']['attachments'] = array();
            foreach ($attachments as $attachment) {
                if (isset($attachment['path'])) {
                    $content = base64_encode(file_get_contents($attachment['path']));
                } else {
                    $content = base64_encode($attachment['content']);
                }
                $message['content']['attachments'][] = array(
                    'type' => $attachment['type'],
                    'name' => $attachment['name'],
                    'data' => $content
                );
            }
        }

        $result = $this->post(
            $this->baseUrl . 'transmissions',
            $message
        );


        if (isset($result['results']) && $result['results']['total_accepted_recipients'] > 0) {
            return array('status' => 'UNKNOWN', 'id' => $result['results']['id'], 'details' => '');
        } else {
            return array('status' => 'FAILED', 'id' => null, 'details' => '');
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
        throw new Exception(__CLASS__ . ' does not provide status callback');
    }

    public function handle_receive_callback()
    {
        throw new Exception(__CLASS__ . ' does not provide receive callback');
    }

    public function __toString()
    {
        return 'Sparkpost';
    }

    public function has_settings_ui()
    {
        return false;
    }
}
?>