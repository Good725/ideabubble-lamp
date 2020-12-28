<?php
interface Model_Messaging_Driver_Interface
{
    public function setup();
    public function settings_groupname();
    public function has_send();
    public function has_sendlist();
    public function has_receive_cron();
    public function has_receive_callback();
    public function has_status();
    public function has_message_subject();
    public function max_message_size();
    public function has_attachment();

    /*
     *$attachment:
     * {
     * name:"abc.txt" required
     * path:"@media/files/1/1000.txt"
     * content: "actual file content" required
     * type:"text/plain" required
     * }
     *
     * only one of path or content needs to be set
     */
    public function send($from, $to, $subject, $message, $attachments = array(), $replyto = null);
    public function sendlist($from, $list_id, $subject, $message, $attachments = array(), $replyto = null);
    public function receive_cron();
    public function message_status($message_id);
    public function handle_status_callback();
    public function handle_receive_callback();
    public function is_ready();
    public function has_settings_ui();
}
?>