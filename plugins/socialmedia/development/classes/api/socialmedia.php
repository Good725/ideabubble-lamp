<?php
abstract class API_SocialMedia implements Interface_SocialMedia
{
    /*** PROTECTED STATIC MEMBERS ***/
    protected static $curl_ssl_version = 3;
    protected static $curl_user_agent = 'Mozilla/5.0 (Windows NT 6.1; WOW64) AppleWebKit/537.17 (KHTML, like Gecko) Chrome/24.0.1312.52 Safari/537.17';

    /*** PROTECTED MEMBERS ***/
    protected $status;
    protected $action;
    protected $result;

    /*** PUBLIC FUNCTIONS ***/
    public function set_result($result)
    {
        $this->result = $result;
    }
}
?>