<?php

class Model_Allpoints
{
    const TRANSACTIONS_TABLE = 'plugin_payments_allpoints_transactions';

    public $chargeurl = 'https://www.allpointsmessaging.com/mno/api/chargetobill.htm';
    public $sendsmsurl = 'https://www.allpointsmessaging.com/bulksms/sendsms/sendbulksms.htm';
    public $username = '';
    public $charge_password = '';
    public $sendsms_password = '';
    public $senderid = '';
    public $contentid = '';

    protected $curl;
    protected $tmpfile;
    protected $logged = false;

    public function __construct()
    {
        $settings = Settings::instance();
        $this->username = $settings->get('allpoints_username');
        $this->sendsms_password = $settings->get('allpoints_sendsms_password');
        $this->charge_password = $settings->get('allpoints_charge_password');
        $this->senderid = $settings->get('allpoints_sms_senderid');
        $this->sendsmsurl = $settings->get('allpoints_sms_url');
        $this->chargeurl = $settings->get('allpoints_charge_url');
        $this->contentid = $settings->get('allpoints_contentid');

        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
        if (!defined('CURL_SSLVERSION_TLSv1_2')) {
            define('CURL_SSLVERSION_TLSv1_2', 6);
        }
        curl_setopt($this->curl, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
    }

    public function __destruct()
    {
        curl_close($this->curl);
    }

    public function charge($mobilenumber, $amount, $currency, $contentid, $operator, $description = '', $channel = 'WEB', $reference = '')
    {
        $mobilenumber = str_replace(' ', '', $mobilenumber);
        if ($mobilenumber[0] == '0') {
            $mobilenumber = '353' . substr($mobilenumber, 1);
        }
        if ($mobilenumber[0] == '+') {
            $mobilenumber = substr($mobilenumber, 1);
        }
        // for testing purposes
        if (Kohana::$environment != Kohana::PRODUCTION) {
            $amount = 0.01;
        }

        $channels = array('WEB' => 1, 'APP' => 2, 'SMS' => 4, 'IVR' => 8);
        $xml = '<CHARGETOBILLREQUEST>
<USERNAME>' . $this->username . '</USERNAME>
<PASSWORD>' . $this->charge_password . '</PASSWORD>
<OPERATORID>' . $operator .  '</OPERATORID>
<MSISDN>' . $mobilenumber . '</MSISDN>
<CONTENTID>' . $contentid . '</CONTENTID>
<CONTENTDESCRIPTION>' . $description . '</CONTENTDESCRIPTION>
<CURRENCYCODE>' . $currency .   '</CURRENCYCODE>
<TRANSACTIONAMOUNT>' . ($amount * 100) . '</TRANSACTIONAMOUNT>
<CHANNEL>' . $channels[$channel] . '</CHANNEL>
<DATEREQUEST>' . gmdate('Y-m-d H:i:s') . '</DATEREQUEST>
<REFERENCE>' . $reference . '</REFERENCE>' .
/*<SUBSCRIBE>
<DATESUBSCRIPTION>2010-12-31 00:00:00</DATESUBSCRIPTION>
<FREQUENCY>4</FREQUENCY>
<REPEATCOUNT>12</REPEATCOUNT>
</SUBSCRIBE>*/
'</CHARGETOBILLREQUEST>';

        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($this->curl, CURLOPT_URL, $this->chargeurl);
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
        $response = curl_exec($this->curl);
        $this->last_request = $xml;
        $this->last_response = $response;
        $this->last_info = curl_getinfo($this->curl);
        $this->last_error = curl_error($this->curl);
        $lxml = $xml;
        Model_ExternalRequests::create(
            $this->chargeurl,
            $this->chargeurl,
            $lxml,
            $this->last_response,
            $this->last_info['http_code']
        );
        if ($response) {
            $response = new SimpleXMLElement($response, LIBXML_NOBLANKS | LIBXML_NOCDATA);
        }
        return $response;
    }

    public function sendsms($message, $tonumber)
    {
        $tonumber = str_replace(' ', '', $tonumber);
        if ($tonumber[0] == '0') {
            $tonumber = '353' . substr($tonumber, 1);
        }
        if ($tonumber[0] == '+') {
            $tonumber = substr($tonumber, 1);
        }

        $params = array(
            'username' => $this->username,
            'password' => $this->sendsms_password,
            'header' => $this->senderid,
            'message' => $message,
            'destinationAddress' => $tonumber
        );

        $url = $this->sendsmsurl . '?' . http_build_query($params);
        curl_setopt($this->curl, CURLOPT_POST, false);
        curl_setopt($this->curl, CURLOPT_URL, $url);

        $response = curl_exec($this->curl);
        $this->last_request = $url;
        $this->last_response = $response;
        $this->last_info = curl_getinfo($this->curl);
        $this->last_error = curl_error($this->curl);

        $response = explode(', ', $response);
        $return = array();
        foreach ($response as $param) {
            $param = explode('=', $param);
            $return[$param[0]] = $param[1];
        }
        Model_ExternalRequests::create(
            $this->sendsmsurl,
            $this->sendsmsurl,
            $tonumber . ':' . $message,
            $this->last_response,
            $this->last_info['http_code']
        );
        return $return;
    }


    public static function create_transaction($amount, $mobilenumber, $operator, $description, $reference = null)
    {
        // for testing purposes
        if (Kohana::$environment != Kohana::PRODUCTION) {
            $amount = 0.01;
        }

        $now = date::now();
        $verification_code = random_int(100000, 999999);
        $tx = array(
            'amount' => $amount,
            'mobilenumber' => $mobilenumber,
            'operator' => $operator,
            'created' => $now,
            'updated' => $now,
            'status' => 'NEW',
            'verification_code' => $verification_code,
            'description' => $description,
            'reference' => $reference
        );
        $inserted = DB::insert(self::TRANSACTIONS_TABLE)
            ->values($tx)
            ->execute();
        $tx['id'] = $inserted[0];
        return $tx;
    }

    public static function get_transaction($id)
    {
        $tx = DB::select('*')
            ->from(self::TRANSACTIONS_TABLE)
            ->where('id', '=', $id)
            ->and_where('deleted', '=', 0)
            ->execute()
            ->current();
        return $tx;
    }

    public static function verify_transaction($id, $code)
    {
        $tx = DB::select('*')
            ->from(self::TRANSACTIONS_TABLE)
            ->where('id', '=', $id)
            ->and_where('status', '=', 'NEW')
            ->and_where('verification_code', '=', $code)
            ->and_where('deleted', '=', 0)
            ->execute()
            ->current();
        if ($tx) {
            DB::update(self::TRANSACTIONS_TABLE)
                ->set(array('status' => 'VERIFIED', 'updated' => date::now()))
                ->where('id', '=', $id)
                ->execute();
            $tx['status'] = 'VERIFIED';
            return $tx;
        } else {
            DB::update(self::TRANSACTIONS_TABLE)
                ->set(array(
                    'updated' => date::now(),
                    'verification_fails' => DB::expr('verification_fails + 1')
                ))
                ->where('id', '=', $id)
                ->execute();
            return false;
        }
    }

    public static function complete_transaction($id, $remote_tx_id)
    {
        if (is_numeric($id)) {
            $tx = DB::select('*')
                ->from(self::TRANSACTIONS_TABLE)
                ->where('id', '=', $id)
                ->and_where('status', '=', 'VERIFIED')
                ->and_where('deleted', '=', 0)
                ->execute()
                ->current();
        } else {
            $tx = $id;
            $id = $tx['id'];
        }
        if ($tx) {
            DB::update(self::TRANSACTIONS_TABLE)
                ->set(array('status' => 'COMPLETED', 'updated' => date::now(), 'remote_tx_id' => $remote_tx_id))
                ->where('id', '=', $id)
                ->execute();
            return $tx;
        } else {
            return false;
        }
    }

    public static function verify_and_charge($id, $code)
    {
        if ($tx = self::verify_transaction($id, $code)) {
            $api = new Model_Allpoints();
            $response = $api->charge(
                $tx['mobilenumber'],
                $tx['amount'],
                'EUR',
                $api->contentid,
                $tx['operator'],
                $tx['description'],
                'WEB',
                $tx['reference'] == null ? (str_pad($tx['id'], 8, '0', STR_PAD_LEFT)) : $tx['reference']
            );
            if ($response->TRANSACTIONID > 0 && stripos($response->RESPONSETEXT, 'Success') !== false) {
                return self::complete_transaction($tx, $response->TRANSACTIONID);
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public static function mark_abandoned_transactions()
    {
        $q = DB::update(self::TRANSACTIONS_TABLE)
            ->set(array('status' => 'ABANDONED'))
            ->where('status', 'in', array('NEW'))
            ->and_where('deleted', '=', 0)
            ->and_where('updated', '<=', date('Y-m-d H:i:s', strtotime('-1 hour')));
        echo $q;
        $q->execute();
    }

    public static function get_operators()
    {
        return array(
            1 => 'O2 Ireland',
            2 => 'Vodafone Ireland',
            3 => 'Three Ireland',
            22 => 'Vodafone Ireland PSMS hybrid',
            44 => 'Meteor/ eir Mobile Ireland PSMS hybrid'
        );
    }

    public static function get_operators_options($current, $filter_by_enabled = false, $filter_by_template = false)
    {
        $operators = self::get_operators();
        $options = '';
        foreach ($operators as $id => $operator) {
            $selected = false;
            if (is_array($current)) {
                $selected = in_array($id, $current);
            } else {
                $selected = $current == $id;
            }
            $options .= '<option value="' . $id . '" . ' . ($selected ? 'selected="selected"' : '') . '>' . $operator . '</option>';
        }
        return $options;
    }
}
