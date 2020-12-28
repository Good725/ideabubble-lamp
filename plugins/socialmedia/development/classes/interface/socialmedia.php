<?php
interface Interface_SocialMedia
{
    public function set_status($status);
    public function execute();
    public function format_update();
    public function set_action($action);
    public function return_result();
}
?>