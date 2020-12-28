<?php
class Mailchimp
{
    protected $ch;
    public $host = 'https://DC.api.mailchimp.com';

    public $last_info;
    public $apikey = '';

    public function __construct($apikey = null)
    {
        $this->ch = curl_init();
        curl_setopt($this->ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        curl_setopt($this->ch, CURLOPT_POST, true);
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, false);
        //curl_setopt($this->ch, CURLOPT_SSL_VERIFYPEER, 1);
        //curl_setopt($this->ch, CURLOPT_SSL_VERIFYHOST, 2);

        if (!defined('CURL_SSLVERSION_TLSv1_2')) {
            define('CURL_SSLVERSION_TLSv1_2', 6);
        }
        curl_setopt($this->ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        if ($apikey == null) {
            $this->apikey = Settings::instance()->get('mailchimp_apikey');
        }

        preg_match('/\-(.+)$/', $this->apikey, $datacenter);
        $this->host = 'https://' . $datacenter[1] . '.api.mailchimp.com';
    }

    public function __destruct()
    {
        curl_close($this->ch);
    }

    protected function post($url, $data)
    {
        curl_setopt($this->ch, CURLOPT_USERPWD, 'user:' . $this->apikey);
        curl_setopt($this->ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($this->ch, CURLOPT_HTTPHEADER, array('Accept: application/json','Content-Type: application/json'));
        curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($this->ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($this->ch, CURLOPT_VERBOSE, true);

        curl_setopt($this->ch, CURLOPT_URL, $this->host . $url);
        $response = curl_exec($this->ch);
        $this->last_info = curl_getinfo($this->ch);
        $this->last_error = curl_error($this->ch);
        $this->last_response = $response;
        return json_decode($response, true);
    }

    public function add_to_list($email, $status, $ip_signup, $list_id = null)
    {
        if ($list_id == null) {
            $list_id = Settings::instance()->get('mailchimp_list_id');
        }
        $result = $this->post(
            '/3.0/lists/' . $list_id . '/members',
            array(
                'email_address' => $email,
                'status' => $status,
                'ip_signup' => $ip_signup
            )
        );
        return $result;
    }
}