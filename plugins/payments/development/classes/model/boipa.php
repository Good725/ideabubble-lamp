<?php defined('SYSPATH') or die('No direct script access.');

class Model_Boipa
{
    public $baseUrl = 'https://testvpos.boipa.com:19445';
    public $baseUrl2 = 'https://testvpos.boipa.com';
    public $clientId = '';
    public $password = '';
    public $storekey = '';
    public $name = '';

    protected $currencyCodes = array(
        'PLN' => 985,
        'EUR' => 978,
        'USD' => 840,
        'GBP' => 826,
        'CHF' => 756,
        'DKK' => 208,
        'CAD' => 124,
        'NOK' => 578,
        'SEK' => 752,
        'RUB' => 643,
        'LTL' => 440,
        'RON' => 946,
        'CZK' => 203,
        'JPY' => 392,
        'HUF' => 348,
        'HRK' => 191,
        'UAH' => 980,
        'TRY' => 949
    );

    protected $curl = null;

    public function __construct($clientId = null, $password = null, $name = null, $storekey = null, $storetype = null)
    {
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
        if (true) {
            curl_setopt($this->curl, CURLOPT_VERBOSE, true);
        }
        curl_setopt($this->curl, CURLOPT_SSLVERSION, 6); //force tls1.2
        curl_setopt($this->curl, CURLOPT_SSL_VERIFYPEER, false);

        $apiHost = Settings::instance()->get('boipa_apihost');
        if ($apiHost) {
            $this->baseUrl = $apiHost;
        }
        $this->clientId = $clientId;
        $this->password = $password;
        $this->name = $name;
        $this->storekey = $storekey;
        $this->storetype = $storetype;

        if ($clientId == null) {
            $this->clientId = Settings::instance()->get('boipa_clientid');
            $this->password = Settings::instance()->get('boipa_password');
            $this->name = Settings::instance()->get('boipa_name');
            $this->storekey = Settings::instance()->get('boipa_storekey');
            $this->storetype = Settings::instance()->get('boipa_storetype');
        }
    }

    public function __destruct()
    {
        if ($this->curl) {
            curl_close($this->curl);
        }
    }

    protected function request($url, $type, $params = array(), $baseUrl = null)
    {
        if ($baseUrl == null) {
            $baseUrl = $this->baseUrl;
        }
        $rurl = $baseUrl . $url;
        if ($type == 'post') {
            curl_setopt($this->curl, CURLOPT_POST, true);
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, http_build_query($params));
        } else {
            curl_setopt($this->curl, CURLOPT_HTTPGET, true);
            if (count($params)) {
                $rurl .= '?' . http_build_query($params);
            }
        }
        curl_setopt($this->curl, CURLOPT_URL, $rurl);
        if (!defined('CURL_SSLVERSION_TLSv1_2')) {
            define('CURL_SSLVERSION_TLSv1_2', 6);
        }
        curl_setopt($this->curl, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        $result = curl_exec($this->curl);
        $result = iconv('iso-8859-2', 'utf-8', $result);
        $this->lastError = curl_error($this->curl);
        $this->lastInfo = curl_getinfo($this->curl);
        Model_ExternalRequests::create(
            $this->baseUrl,
            $rurl,
            isset($params['DATA']) ? $params['DATA'] : http_build_query($params),
            $result,
            $this->lastInfo['http_code']
        );
        return $result;
    }

    public function getTokenFor($orderId, $total, $currency)
    {
        $params = array(
            'ClientId' => $this->clientId,
            'Password' => $this->storekey,
            'OrderId' => $orderId,
            'Total' => $total,
            'Currency' => $this->currencyCodes[$currency]
        );
        $response = $this->request('/pg/token', 'get', $params, $this->baseUrl2);
        parse_str($response, $response);
        return $response;
    }

    public function redirect3D($params)
    {

    }

    public function charge($params)
    {
        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n" .
            '<CC5Request>' .
                '<Name>' . $this->name . '</Name>' .
                '<Password>' . $this->password . '</Password>' .
                '<ClientId>' . $this->clientId . '</ClientId>' .
                '<Type>Auth</Type>' .
                '<OrderId>' . $params['orderId'] . '</OrderId>' .
                '<Total>' . $params['total'] . '</Total>' .
                '<Currency>' . $this->currencyCodes[$params['currency']] . '</Currency>' .
                '<Number>' . $params['number'] . '</Number>' .
                '<Expires>' . $params['expires'] . '</Expires>' .
                '<Cvv2Val>' . $params['cv'] . '</Cvv2Val>' .
                '<IPAddress>' . $params['ip'] . '</IPAddress>';

        if (isset($params['email'])) {
            $xml .= '<Email>' . $params['email'] . '</Email>';
        }

        if (isset($params['bill'])) {
            $xml .= '<BillTo>';
            if (isset($params['bill']['name'])) {
                $xml .= '<Name>' . $params['bill']['name'] . '</Name>';
            }
            if (isset($params['bill']['company'])) {
                $xml .= '<Company>' . $params['bill']['company'] . '</Company>';
            }
            if (isset($params['bill']['address1'])) {
                $xml .= '<Street1>' . $params['bill']['address1'] . '</Street1>';
            }
            if (isset($params['bill']['city'])) {
                $xml .= '<City>' . $params['bill']['city'] . '</City>';
            }
            if (isset($params['bill']['postalcode'])) {
                $xml .= '<PostalCode>' . $params['bill']['postalcode'] . '</PostalCode>';
            }
            if (isset($params['bill']['country'])) {
                $xml .= '<Country>' . $params['bill']['country'] . '</Country>';
            }
            if (isset($params['bill']['phone'])) {
                $xml .= '<TelVoice>' . $params['bill']['phone'] . '</TelVoice>';
            }
            $xml .= '</BillTo>';
        }
        if (isset($params['ship'])) {
            $xml .= '<ShipTo>';
            if (isset($params['ship']['name'])) {
                $xml .= '<Name>' . $params['bill']['name'] . '</Name>';
            }
            if (isset($params['ship']['company'])) {
                $xml .= '<Company>' . $params['bill']['company'] . '</Company>';
            }
            if (isset($params['ship']['address1'])) {
                $xml .= '<Street1>' . $params['bill']['address1'] . '</Street1>';
            }
            if (isset($params['ship']['city'])) {
                $xml .= '<City>' . $params['bill']['city'] . '</City>';
            }
            if (isset($params['ship']['postalcode'])) {
                $xml .= '<PostalCode>' . $params['bill']['postalcode'] . '</PostalCode>';
            }
            if (isset($params['ship']['country'])) {
                $xml .= '<Country>' . $params['bill']['country'] . '</Country>';
            }
            if (isset($params['ship']['phone'])) {
                $xml .= '<TelVoice>' . $params['bill']['phone'] . '</TelVoice>';
            }
            $xml .= '</ShipTo>';
        }

        if (isset($params['items'])) {
            $xml .= '<OrderItemList>';
            foreach ($params['items'] as $i => $item) {
                $xml .= '<OrderItem>';
                $xml .= '<ItemNumber>' . ($i + 1) . '</ItemNumber>';
                if (isset($item['code'])) {
                    $xml .= '<ProductCode>' . $item['code'] . '</ProductCode>';
                }
                if (isset($item['quantity'])) {
                    $xml .= '<Qty>' . $item['quantity'] . '</Qty>';
                }
                if (isset($item['name'])) {
                    $xml .= '<Desc>' . $item['name'] . '</Desc>';
                }
                if (isset($item['id'])) {
                    $xml .= '<Id>' . $item['id'] . '</Id>';
                }
                if (isset($item['price'])) {
                    $xml .= '<Price>' . $item['price'] . '</Price>';
                }
                if (isset($item['total'])) {
                    $xml .= '<Total>' . $item['total'] . '</Total>';
                }
                $xml .= '</OrderItem>';
            }
            $xml .= '</OrderItemList>';
        }
        $xml .= '</CC5Request>';

        $response = $this->request('/fim/api', 'post', array('DATA' => $xml));
        try {
            $response = self::xml2array($response);

        } catch (Exception $exc) {
            $response = false;
        }
        return $response;
    }

    public static function xml2array($xml)
    {
        if (is_string($xml)) {
            $xml = new SimpleXMLElement($xml, LIBXML_NOCDATA | LIBXML_NOBLANKS);
        }
        $a = (array)$xml;
        foreach ($a as $key => &$value) {
            if (is_a($value, 'SimpleXMLElement')){
                $value = self::xml2array($value);
                if (count($value) == 0){
                    $value = '';
                }
            }
        }
        return $a;
    }

    // cloned from realexpayments->validate
    public function validate($post)
    {
        $valid = true;

        try{
            $checkout_model = new Model_Checkout();
            $products = $checkout_model->get_cart_details();

            if( !isset($post) OR empty($post) ) return false;
            if( !isset($products) OR empty($products) ) return false;

            //Check if post data is the same than the session data, Is only checking the ID and the Amount, the function can be updated to be more or less strict
            foreach ($products->lines as $key => $line) {
                //Same ID
                if((int)$line->product->id != $post->products[$key]->id) return false;
                //Same quantity
                if($line->quantity != $post->products[$key]->quantity) return false;
            }

            //Check if the postal destination is set
            if(!isset($products->shipping_price) OR (is_null($products->shipping_price))){
                return false;
            }
        }
        catch(Exception $e){
            Log::instance()->add(Log::ERROR, $e->getMessage());
            $valid = false;
        }

        return $valid;
    }

    public function process_payment($post)
    {
        $boipaParams = array();
        $boipaParams['orderId'] = 'order-' . time();
        $checkout_model = new Model_Checkout();
        $cart = $checkout_model->get_cart_details();
        $boipaParams['total']  = $cart->final_price;
        $boipaParams['currency'] = 'EUR';
        $boipaParams['number'] = $post->ccNum;
        $boipaParams['expires'] = $post->ccExpMM . '/' . $post->ccExpYY;
        $boipaParams['cv'] = isset($post->ccv) ? $post->ccv : '';
        $boipaParams['mail'] = $post->email;
        $boipaParams['ip'] = $_SERVER['REMOTE_ADDR'];
        if (isset($post->email)) {
            $boipaParams['email'] = $post->email;
        }

        $result = $this->charge($boipaParams);
        return $result;
    }
}