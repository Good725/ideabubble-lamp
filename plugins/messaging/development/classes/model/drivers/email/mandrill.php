<?php
require_once Kohana::find_file('vendor/', 'mandrill/src/Mandrill');

class Model_Messaging_Driver_Email_Mandrill implements Model_Messaging_Driver_Interface
{
    protected $api;
    
    public function __construct()
    {
        $mandrill_key = Settings::instance()->get('mandrill_key');
        if($mandrill_key != null){
            $this->api = new Mandrill($mandrill_key);
        }
    }

    public function setup()
    {
        DB::query(0, "INSERT INTO `engine_settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) values ('mandrill_key',        'Mandrill Key',        'bMukrNy2NHnJBR_OOCoatw', 'bMukrNy2NHnJBR_OOCoatw', 'bMukrNy2NHnJBR_OOCoatw', 'bMukrNy2NHnJBR_OOCoatw', 'bMukrNy2NHnJBR_OOCoatw', 'both', '', 'text', 'Mandrill Settings', 0, '')" )->execute();
        DB::query(0, "INSERT INTO `engine_settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) values ('mandrill_from_email', 'Mandrill From Email', 'testing@websitecms.ie', 'testing@websitecms.ie',   'testing@websitecms.ie',  'testing@websitecms.ie',  'testing@websitecms.ie',  'both', '', 'text', 'Mandrill Settings', 0, '')" )->execute();
        DB::query(0, "INSERT INTO `engine_settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) values ('mandrill_from_name',  'Mandrill From Name',  'websitecms.ie',         'websitecms.ie',           'websitecms.ie',          'websitecms.ie',          'websitecms.ie',          'both', '', 'text', 'Mandrill Settings', 0, '')" )->execute();
    }
    
    public function settings_groupname()
    {
        return 'Mandrill Settings';
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
        return true;
    }

    public function max_message_size()
    {
        return 10000000;
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
        return true;
    }

    public function send($from, $to, $subject, $message, $attachments = array(), $replyto = null)
    {
        if(!$this->api){
            throw new Exception('mandrill has not been setup');
        }

        if($from == null){
            $from = Settings::instance()->get('mandrill_from_email');
        }

        $message = array(
            'track_opens' => true,
            'html' => $message,
            'inline_css ' => 1,
            'subject' => $subject,
            'from_email' => $from,
            'from_name' => Settings::instance()->get('mandrill_from_name'),
            'to' => array(
                array(
                    'email' => $to,
                    'type' => 'to'
                )
            )
        );

        if ($replyto) {
            $message['headers'] = array('Reply-To' => $replyto);
        }

        if (count($attachments) > 0) {
            $message['attachments'] = array();
            foreach ($attachments as $attachment) {
                $content = '';
                if (isset($attachment['path'])) {
                    $content = base64_encode(file_get_contents($attachment['path']));
                } else {
                    $content = base64_encode($attachment['content']);
                }
                $message['attachments'][] = array(
                    'type' => $attachment['type'],
                    'name' => $attachment['name'],
                    'content' => $content
                );
            }
        }


        $async = false;
        $ip_pool = 'Main Pool';
        $send_at = gmdate('Y-m-d H:i:s');
        $result = $this->api->messages->send($message, $async, $ip_pool);
        if($result[0]['status'] == 'rejected' || $result[0]['status'] == 'invalid' ){
            return array('status' => 'FAILED', 'id' => $result[0]['_id'], 'details' => '');
        } else {
            return array('status' => 'UNKNOWN', 'id' => $result[0]['_id'], 'details' => '');
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
        file_put_contents("sb" . time() . ".txt", var_export(array(&$_GET, &$_POST), 1) );
        $mandrill_event_types = array('send' => 'SENT',
                                        'open' => 'READ');
        if(isset($_POST['mandrill_events'])){
            $events = json_decode($_POST['mandrill_events'], true);
            $results = array();
            foreach($events as $event){
                $result = array('driver_remote_id' => $event['_id']);
                //twilio possible status list: failed, sent, delivered, or undelivered
                if(isset($mandrill_event_types[$event['event']])){
                    $result['delivery_status'] = $mandrill_event_types[$event['event']];
                } else {
                    $result['delivery_status'] = 'OTHER';
                    $result['delivery_status_details'] = $event['event'];
                }
                $results[] = $result;
            }
            return $results;
        } else {
            return false;
        }
    }

    public function handle_receive_callback()
    {
        if(isset($_POST['mandrill_events'])){
            $events = json_decode($_POST['mandrill_events'], true);
            foreach($events as $event){
                if($event['event'] == 'inbound'){
                    $msg = &$event['msg'];
                    Database::instance()->begin();
                    try{
                        $driver_id = DB::select('id')
                                        ->from('plugin_messaging_drivers')
                                        ->where('driver', '=', 'email')
                                        ->and_where('provider', '=', 'mandrill')
                                        ->execute()
                                        ->get('id');
                        $imessage = array();
                        $imessage['driver_id'] = $driver_id;
                        $imessage['sender'] = $msg['from_email'];
                        $imessage['subject'] = $msg['subject'];
                        $imessage['message'] = isset($msg['html']) ? $msg['html'] : $msg['text'];
                        $imessage['created_by'] = 1;
                        $imessage['date_created'] = $imessage['date_updated'] = date('Y-m-d H:i:s');
                        $imessage['status'] = 'RECEIVED';

                        if (Model_Messaging::is_muted($imessage['sender'])){
                            $imessage['is_spam'] = 1;
                        }

                        foreach($msg['to'] as $to){
                            if (Model_Messaging::is_unavailable($to[0])) {
                                $imessage['received_when_unavailable'] = 1;
                            }
                        }

                        
                        $message_result = DB::insert('plugin_messaging_messages', array_keys($imessage))
                                                ->values($imessage)->execute();
                        $imessage['id'] = $message_id = $message_result[0];
                        
                        foreach($msg['to'] as $to){
                            $itarget = array();
                            $itarget['message_id'] = $message_id;
                            $itarget['target_type'] = 'EMAIL';
                            $itarget['target'] = $to[0];
                            $itarget_result = DB::insert('plugin_messaging_message_targets', array_keys($itarget))->values($itarget)->execute();
                            $final_target = array();
                            $final_target['target_id'] = $itarget_result[0];
                            $final_target['target_type'] = 'EMAIL';
                            $final_target['target'] = $to[0];
                            DB::insert('plugin_messaging_message_final_targets', array_keys($final_target))->values($final_target)->execute();
                        }
                        Database::instance()->commit();

                        Model_Messaging::execute_post_processors($message_id);
                    } catch(Exception $e){
                        Database::instance()->rollback();
                        throw $e;
                    }
                }
            }
            return true;
        } else {
            return false;
        }
    }

    public function __toString()
    {
        return 'Mandrill';
    }

    public function has_settings_ui()
    {
        return false;
    }
}
?>