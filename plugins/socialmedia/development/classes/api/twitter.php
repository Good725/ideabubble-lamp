<?php
/***
 * Class API_Twitter
 * Here we will define the data & actions needed to interact with the Twitter API as referenced here: https://dev.twitter.com/rest/reference/
 * Initial commit is for adding a status to a user only.
 */
class API_Twitter extends API_SocialMedia
{
    /*** CONSTANTS ***/

    CONST API_BASE_URL = 'https://api.twitter.com/1.1/';

    /*** PRIVATE MEMBER VARIABLES ***/
    private $API_URLS = array('update_status' => 'statuses/update.json');

    /*** PUBLIC FUNCTIONS ***/

    public function __construct($status = '')
    {
        $this->sign_in();
        $this->set_status($status);
    }

    public function set_status($status)
    {
        $this->status = $status;
        return $this;
    }

    public function execute()
    {
        $result = $this->send();
        $this->set_result($result);
        return $this;
    }

    public function format_update()
    {
        $this->status = substr(urlencode($this->status),0,140);
        return $this;
    }

    public function set_action($action)
    {
        $this->action = $action;
        return $this;
    }

    public function return_result()
    {
        return $this->result;
    }

    /*** PRIVATE FUNCTIONS ***/

    private function get_twitter_post()
    {
        $this->format_update();
        return array('status' => $this->status,
        'possibly_sensitive' => false,
        'display_coordinates' => false);
    }

    private function send()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL,self::API_BASE_URL.$this->API_URLS[$this->action]);
        curl_setopt($ch, CURLOPT_USERPWD,'');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSLVERSION,self::$curl_ssl_version);
        curl_setopt($ch, CURLOPT_POSTFIELDS,http_build_query($this->get_twitter_post()));
        curl_setopt($ch, CURLOPT_USERAGENT, self::$curl_user_agent);
        $result = curl_exec($ch);
        curl_close($ch);
        return $result;
    }

    private function sign_in()
    {

    }

    /*** PUBLIC STATIC FUNCTIONS ***/
    public static function factory($status = '')
    {
        return new self($status);
    }
}
?>