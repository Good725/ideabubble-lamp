<?php
class Model_CDSAPI
{
    const API_NAME = 'CDSAPI';

    public $base_url = '';
    protected $curl = null;
    public $last_request;
    public $last_response;
    public $client_id = null;
    public $client_secret = null;
    public $scope = null;
    public $grant_type = null;
    public $ms_auth_url = null;
    protected $auth = null;
    protected $auth_expire = 0;

    public static $countries = array(
        'IRL' => '778390024',
        'NIR' => '778390037',
        'ENG' => '778390014');
    public static $counties = array(
        'CC' => '778390002',
        'CE' => '778390003',
        'DL' => '778390006',
        'DU' => '778390008',
        'GY' => '778390011',
        'KE' => '778390012',
        'KK' => '778390013',
        'KY' => '778390014',
        'LH' => '778390016',
        'LK' => '778390017',
        'LS' => '778390019',
        'MH' => '778390020',
        'MN' => '778390021',
        'MO' => '778390022',
        'OY' => '778390023',
        'RN' => '778390024',
        'SO' => '778390025',
        'TN' => '778390027',
        'WD' => '778390028',
        'WM' => '778390029',
        'WX' => '778390031'
    );

    public function __construct()
    {
        $this->base_url = Settings::instance()->get('cdsapi_api_url');
        $this->client_id = Settings::instance()->get('cdsapi_client_id');
        $this->client_secret = Settings::instance()->get('cdsapi_client_secret');
        $this->scope = Settings::instance()->get('cdsapi_scope');
        $this->ms_auth_url = Settings::instance()->get('cdsapi_ms_auth_url');
    }

    public function auth()
    {
        $time = time();
        if ($this->auth_expire > $time) {
            return true;
        }

        if ($this->client_id == null || $this->client_secret == null || $this->scope == null) {
            return false;
        }

        $auth = $this->request(
            'POST',
            '',
            array(
                'client_id' => $this->client_id,
                'client_secret' => $this->client_secret,
                'scope' => $this->scope,
                'grant_type' => 'client_credentials'
            ),
            $this->ms_auth_url,
            false,
            false
        );
        if (@$auth['access_token'] != null) {
            $this->auth = $auth;
            $this->auth_expire = $time + $auth['expires_in'];
            return true;
        } else {
            $this->auth = null;
            $this->auth_expire = 0;
            return false;
        }
    }

    protected function request($type, $uri, $params = null, $base_url = null, $auth = true, $json = true)
    {
        $response_headers = array();
        $headers = array(
            'Accept: application/json'
        );
        if ($auth) {
            if (!$this->auth()) {
                return false;
            }
            $headers[] = 'OData-MaxVersion: 4.0';
            $headers[] = 'OData-Version: 4.0';
            $headers[] = 'Authorization: Bearer ' . $this->auth['access_token'];
        }
        $url = ($base_url == null ? $this->base_url : $base_url) . $uri;
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->curl, CURLOPT_HEADERFUNCTION, function($curl, $header) use (&$response_headers){
            $len = strlen($header);
            $header = explode(':', $header, 2);
            $response_headers[strtolower(trim($header[0]))][] = trim($header[1]);

            return $len;
        });
        //curl_setopt($this->curl, CURLOPT_VERBOSE, true);

        if ($type != 'GET') {
            curl_setopt($this->curl, CURLOPT_POST, true);
            if ($type != 'POST') {
                curl_setopt($this->curl, CURLOPT_CUSTOMREQUEST, 'PATCH');
            }
            if ($json) {
                $json = json_encode($params, JSON_PRETTY_PRINT);
                $headers[] = 'Content-Type: application/json; charset=utf-8';
                $headers[] = 'Content-Length: ' . strlen($json);
                curl_setopt($this->curl, CURLOPT_HTTPHEADER, $headers);
                curl_setopt($this->curl, CURLOPT_POSTFIELDS, $json);
            } else {
                curl_setopt($this->curl, CURLOPT_POSTFIELDS, $params);
            }
        } else {
            curl_setopt($this->curl, CURLOPT_HTTPGET, true);
        }

        curl_setopt($this->curl, CURLOPT_HEADER, 0);
        curl_setopt($this->curl, CURLOPT_URL, $url);
        $t1 = microtime(true);
        $response = curl_exec($this->curl);
        $t2 = microtime(true);
        $this->last_request = $params;
        $this->last_response = $response;
        $this->last_info = curl_getinfo($this->curl);
        $this->last_response_headers = $response_headers;
        curl_close($this->curl);
        Model_ExternalRequests::create($this->base_url, $url, $params, $response, $this->last_info['status'], date::now(), null, $t2 - $t1);

        return json_decode($response, true);
    }

    public function create_account($contact_id, $account)
    {
        $rs = new Model_Remotesync();
        $synced = $rs->get_object_synced(self::API_NAME . '-Account', $contact_id);
        if ($synced) {
            return $synced['remote_id'];
        }
        $response = $this->request('POST', '/api/data/v9.1/accounts', $account);
        if (isset($this->last_response_headers['odata-entityid'])) {
            preg_match('/accounts\((.+)\)/', $this->last_response_headers['odata-entityid'][0], $cdsid);
            $rs->save_object_synced(self::API_NAME . '-Account', $cdsid[1], $contact_id);
            return $cdsid[1];
        } else {
            return false;
        }
    }

    public function update_account($contact_id, $account)
    {
        $rs = new Model_Remotesync();
        $synced = $rs->get_object_synced(self::API_NAME . '-Account', $contact_id);
        if (!$synced) {
            return false;
        }
        $response = $this->request('PATCH', '/api/data/v9.1/accounts(' . $synced['remote_id'] . ')', $account);
        if (isset($this->last_response_headers['odata-entityid'])) {
            preg_match('/accounts\((.+)\)/', $this->last_response_headers['odata-entityid'][0], $cdsid);
            $rs->save_object_synced(self::API_NAME . '-Account', $cdsid[1], $contact_id);
            return $cdsid[1];
        } else {
            return false;
        }
    }

    public function get_account($contact_id)
    {
        $rs = new Model_Remotesync();
        $synced = $rs->get_object_synced(self::API_NAME . '-Account', $contact_id);
        $response = array();
        if ($synced) {
            $response = $this->request('GET', '/api/data/v9.1/accounts(' . $synced['remote_id'] . ')');
            $response['synced_value'] = $synced['cms_id'];
        }
        return $response;
    }

    public function get_account_by_remote_id($contact_id) {
        $rs = new Model_Remotesync();
        $response = $this->request('GET', '/api/data/v9.1/accounts(' . $contact_id . ')');
        $synced = $rs->get_object_synced(self::API_NAME . '-Account', $response['accountid'], 'remote');
        if ($synced) {
            $response['synced_value'] = $synced['cms_id'];
        } else {
            $response['synced_value'] = '';
        }
        return $response;
    }

    public function search_accounts($cds_field, $cds_value, $strict = false, $exclude_public = true)
    {
        $params = array();
        if ($strict) {
            $params['$filter'] = $cds_field . " eq '" . $cds_value . "' and sp_externalid eq null";
        } else {
            $params['$filter'] = 'contains('. $cds_field . ', \'' . $cds_value.'\') and sp_externalid eq null';
        }
        if ($exclude_public) {
            $params['$filter'] .= " and (sp_publicdomain eq 'false' or sp_publicdomain eq null)";
        }
        $response = $this->request('GET', '/api/data/v9.0/accounts?' . http_build_query($params));
        if (isset($response['value'])) {
            $rs = new Model_Remotesync();
            foreach($response['value'] as &$value) {
                $synced = $rs->get_object_synced(self::API_NAME . '-Account', $value['accountid'], 'remote');
                $value['synced_value'] = !empty($synced['cms_id']) ? $synced['cms_id'] : false;
            }
            return $response['value'];
        } else {
            return false;
        }
    }
}