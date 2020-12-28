<?php
class Model_BulletHQB extends Model
{
    CONST USER_AGENT = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:41.0) Gecko/20100101 Firefox/41.0';
    public $base_url = 'https://accounts-app.bullethq.com';
    public $username = 'accounts@ideabubble.ie';
    public $password = 'bullet!951';
    public $company_id = '';

    protected $curl;
    protected $tmpfile;
    protected $logged = false;
    
    public function __construct()
    {
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        $this->tmpfile = tempnam('/tmp/', 'bullethq');
        curl_setopt($this->curl, CURLOPT_COOKIEFILE, $this->tmpfile);
        curl_setopt($this->curl, CURLOPT_COOKIEFILE, $this->tmpfile);
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
        $this->company_id = Settings::instance()->get('bullethq_company_id');
    }
    
    public function __destruct()
    {
        curl_close($this->curl);
        unlink($this->tmpfile);
    }
    
    protected function request($url, $type, $params = array())
    {
        $rurl = $this->base_url . $url;
        if($type == 'post'){
            curl_setopt($this->curl, CURLOPT_POST, true);
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, http_build_query($params));
        } else {
            curl_setopt($this->curl, CURLOPT_HTTPGET, true);
            if(count($params)){
                $rurl .= '?' . http_build_query($params);
            }
        }
        curl_setopt($this->curl, CURLOPT_URL, $rurl);
        $result = curl_exec($this->curl);
        $this->last_info = curl_getinfo($this->curl);
        Model_ExternalRequests::create(
            $this->base_url,
            $rurl,
            http_build_query($params),
            $result,
            $this->last_info['http_code']
        );
        return $result;
    }
    
    public function login()
    {
        $this->request('/users/login.page', 'get');
        $response = $this->request(
            '/security/login.page',
            'post',
            array('username' => $this->username, 'password' => $this->password)
        );
        if (strpos($response, '<form id="submitTheSelectedCompanyForm" action="/select/company.page" method="POST">')) {
            if (preg_match(
                '#<a class="list-group-item" name="(\d+)" href="\#">\s*<img src="/images/logo\.page\?showDefault=true&companyId=' . $this->company_id . '#is',
                $response,
                $membershipId
            )) {
                $response = $this->request('/select/company.page', 'post', array('membershipId' => $membershipId[1]));
            } else {
                IbHelpers::set_message('Some BulletHQ functionality may not be working:' . __LINE__, 'info');
            }
        }
        if(strpos($response, '<a id="logout-button" href="/security/logout.page" role="button">') !== false){
            $this->logged = true;
            return true;
        } else {
            $this->logged = false;
            return false;
        }
        //header('content-type: text/plain');
        //echo $response;exit();
    }
    
    public function get_invoice_token($invoice_id)
    {
        if (!$this->logged) {
            $this->login();
        }
        if (!$this->logged) {
            return false;
        }
        $response = $this->request('/invoices/view.page', 'get', array('id' => $invoice_id));
        if(preg_match('#<a href="https://accounts-app\.bullethq\.com/clientViews/invoice\.page\?token=(.+?)"#', $response, $match)){
            return $match[1];
        } else {
            return false;
        }
    }
    
    public function email_invoice($invoice_id)
    {
        if (!$this->logged) {
            $this->login();
        }
        if (!$this->logged) {
            return false;
        }
        $response = $this->request('/invoices/send.page', 'get', array('id' => $invoice_id));
        if(preg_match('#<form id="objectData".+?</form>#s', $response, $match)){
            $form = $match[0];
            preg_match_all('#<input.*?name="(.*?)".*?value="(.*?)"#', $form, $match2);
            $inputs = array();
            foreach($match2[1] as $i => $input){
                $inputs[$input] = $match2[2][$i];
            }
            preg_match('#<textarea.*?>(.*?)</textarea>#', $form, $match3);
            $inputs['message'] = $match3[1];
            //print_r($inputs);
            $this->request('/invoices/send.page?id=' . $invoice_id, 'post', $inputs);
            return true;
        } else {
            return false;
        }
    }
    
    public static function get_invoice_view_url($token)
    {
        return 'https://accounts-app.bullethq.com/clientViews/invoice.page?token=' . $token;
    }
    
    public static function get_invoice_download_url($token)
    {
        return 'https://accounts-app.bullethq.com/clientViews/invoice.pdf?token=' . $token . '&download=true';
    }
}
?>