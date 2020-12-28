<?php

require_once __DIR__ . '/../../imap.php';

class Model_Messaging_Driver_Email_Imap implements Model_Messaging_Driver_Interface
{
    protected $mail;
    protected $mailer;
    const ACCOUNTS_TABLE = 'plugin_messaging_imap_accounts';
    
    public function __construct()
    {

    }

    public function setup()
    {
        DB::query(0, "CREATE TABLE " . self::ACCOUNTS_TABLE . "
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NULL,
  host VARCHAR(255),
  username  VARCHAR(255),
  password  VARCHAR(255),
  port  VARCHAR(10),
  security ENUM('NONE', 'SSL', 'TLS'),
  use_pop3 TINYINT NOT NULL DEFAULT 0,
  last_synced DATETIME,
  auto_sync_minutes INT,
  deleted TINYINT NOT NULL DEFAULT 0
)
ENGINE=INNODB
CHARSET=UTF8
" )->execute();
    }
    
    public function settings_groupname()
    {
        return 'Imap Settings';
    }

    public function has_send()
    {
        return false;
    }
    
    public function has_sendlist()
    {
        return false;
    }
    
    public function has_receive_cron()
    {
        return true;
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
        throw new Exception(__CLASS__ . ' send is not implemented');
    }
    
    public function sendlist($from, $list_id, $subject, $message, $attachments = array(), $replyto = null)
    {
        throw new Exception(__CLASS__ . ' sendlist is not implemented');
    }

    public function receive_cron()
    {
        $sync_only_id = @$_GET['id'];
        session_commit();
        $driver_id = DB::select('id')
            ->from('plugin_messaging_drivers')
            ->where('driver', '=', 'email')
            ->and_where('provider', '=', 'imap')
            ->execute()
            ->get('id');

        //use locking to prevent multiple instances at the same time
        $lockFile = '/tmp/imap_receive_lock_' . $_SERVER['HTTP_HOST'] . '.pid';
        $lock = fopen($lockFile, "c+");
        if(!$lock){
            printf("Unable to open lock file!\n");
            exit(2);
        }
        if(flock($lock, LOCK_EX | LOCK_NB)){
            $accounts = DB::select('accounts.*')
                ->from(array(self::ACCOUNTS_TABLE, 'accounts'))
                ->where('accounts.deleted', '=', 0)
                ->execute()
                ->as_array();
            foreach ($accounts as $account) {
                if ($account['auto_sync_minutes']) {
                    if (((time() - $account['auto_sync_minutes'] * 60) - @strtotime($account['last_synced'])) < 0){
                        continue;
                    }
                }
                if ($sync_only_id && $sync_only_id != $account['id']) {
                    continue;
                }
                $imap = new IMAP();
                $imap->host = $account['host'];
                $imap->port = $account['port'];
                $imap->username = $account['username'];
                $imap->password = $account['password'];
                $imap->secure = $account['security'];
                $imap->pop3 = $account['use_pop3'] == 1;

                if ($imap->connect()) {
                    $fetch_all = false;
                    if($fetch_all){
                        $inboxIds = $imap->getAllMailIds();
                    } else {
                        $inboxIds = $imap->getNewMailIds();
                    }

                    if($inboxIds === false){
                        $unread = 0;
                    } else {
                        $unread = count($inboxIds);
                    }

                    if($unread > 0){
                        foreach($inboxIds as $inboxId){
                            $mail = $imap->getMail($inboxId);
                            try{
                                Database::instance()->begin();
                                $imessage = array();
                                $imessage['driver_id'] = $driver_id;
                                $imessage['sender'] = $mail['header']->from[0]->mailbox . '@' . $mail['header']->from[0]->host;
                                $imessage['subject'] = $mail['header']->subject;

                                $imessage['message'] = @$mail['content']['html'] ? @$mail['content']['html'] : @$mail['content']['text'];
                                $imessage['replyto'] = @$mail['header']->reply_to ? $mail['header']->reply_to[0]->mailbox . '@' . $mail['header']->reply_to[0]->host : '';
                                $imessage['created_by'] = 1;
                                $imessage['date_created'] = date::now();
                                $imessage['date_updated'] = date::now();
                                $imessage['ip_address'] = '';
                                $imessage['user_agent'] = 'cron';
                                $imessage['status'] = 'RECEIVED';
                                $imessage['is_spam'] = 0;

                                if (Model_Messaging::is_muted($imessage['sender'])){
                                    $imessage['is_spam'] = 1;
                                }

                                $message_result = DB::insert('plugin_messaging_messages', array_keys($imessage))
                                    ->values($imessage)->execute();
                                $imessage['id'] = $message_id = $message_result[0];

                                if(count($mail['content']['attachments'])){
                                    $inline_img_replaced_message = $imessage['message'];
                                    foreach($mail['content']['attachments'] as $attachment){
                                        $iattachment = array('message_id' => $message_id);
                                        $iattachment['name'] = $attachment['filename'];
                                        $iattachment['content'] = base64_encode(file_get_contents($attachment['content']));
                                        $iattachment['type'] = 'application/octet-stream';
                                        $iattachment['content_encoding'] = 'base64';
                                        $iattachment_inserted = DB::insert(Model_Messaging::ATTACHMENTS_TABLE)->values($iattachment)->execute();
                                        if (@$attachment['id']) {
                                            $inline_img_replaced_message = str_replace(
                                                'cid:' . trim($attachment['id'], '<>'),
                                                '/admin/messaging/download_attachment/' . $iattachment_inserted[0],
                                                $inline_img_replaced_message
                                            );
                                        }
                                    }
                                    if ($inline_img_replaced_message != $imessage['message']) {
                                        DB::update('plugin_messaging_messages')->set(array('message' => $inline_img_replaced_message))->where('id', '=', $message_id)->execute();
                                    }
                                }

                                $itarget = array();
                                $itarget['message_id'] = $message_id;
                                $itarget['target_type'] = 'EMAIL';
                                $itarget['target'] = strpos($account['username'], '@') ? $account['username'] : $account['username'] . '@' . $account['host'];
                                $itarget_result = DB::insert('plugin_messaging_message_targets', array_keys($itarget))->values($itarget)->execute();
                                $final_target = array();
                                $final_target['target_id'] = $itarget_result[0];
                                $final_target['target_type'] = 'EMAIL';
                                $final_target['target'] = strpos($account['username'], '@') ? $account['username'] : $account['username'] . '@' . $account['host'];
                                DB::insert('plugin_messaging_message_final_targets', array_keys($final_target))->values($final_target)->execute();
                                Database::instance()->commit();

                                if ($imessage['is_spam'] == 0) {
                                    Model_Messaging::execute_post_processors($message_id);
                                }

                                $imap->markAsRead($inboxId);
                            } catch(Exception $e){
                                Database::instance()->rollback();
                                throw $e;
                            }
                        }
                    }
                    DB::update(self::ACCOUNTS_TABLE)
                        ->set(array('last_synced' => date::now()))
                        ->where('id', '=', $account['id'])
                        ->execute();
                } else {
                    Log::instance(Log::WARNING, "Can not connect to" . $account['host'] . ':' . $account['username']);
                }
            }

            flock($lock, LOCK_UN);
            unlink($lockFile);
        }
        fclose($lock);
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
        return 'Imap';
    }

    public function has_settings_ui()
    {
        return array(
            'link' => 'Imap Accounts',
            'php' => __DIR__ . '/settings/imap.php',
        );
    }

    public function save_settings($post)
    {
        $data = arr::extract($post, array('user_id', 'host', 'username', 'password', 'port', 'security', 'use_pop3', 'auto_sync_minutes', 'deleted'), null, false);

        if (@$post['action'] == 'test') {
            try {
                $imap = new IMAP();
                $imap->host = $post['host'];
                $imap->port = $post['port'];
                $imap->username = $post['username'];
                $imap->password = $post['password'];
                $imap->secure = $post['security'];
                $imap->pop3 = $post['use_pop3'] == 1;

                if ($imap->connect()) {
                    return true;
                } else {
                    return false;
                }
            } catch (Exception $exc) {
                return false;
            }
            return true;
        } else {
            if (is_numeric(@$post['id'])) {
                DB::update(self::ACCOUNTS_TABLE)->set($data)->where('id', '=', $post['id'])->execute();
            } else {
                DB::insert(self::ACCOUNTS_TABLE)->values($data)->execute();
            }
            return true;
        }
    }

    public function load_settings()
    {
        $select = DB::select('*')
            ->from(self::ACCOUNTS_TABLE)
            ->where('deleted', '=', 0)
            ->order_by('host')
            ->order_by('username');

        $select->and_where('user_id', 'is', null);

        $accounts = $select
            ->execute()
            ->as_array();
        return $accounts;
    }
}
?>