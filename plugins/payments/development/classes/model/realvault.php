<?php defined('SYSPATH') or die('No direct script access.');

class Model_Realvault
{
    CONST USER_AGENT = 'Mozilla/5.0 (Windows NT 6.1; WOW64; rv:41.0) Gecko/20100101 Firefox/41.0';
    public $remote = 'https://api.sandbox.realexpayments.com/epage-remote.cgi';
    public $merchantid = null;
    public $secret = null;
    public $mode = null;
    public $refundpass = '';
    public $rebatepass = '';

    protected $curl;
    protected $tmpfile;
    protected $logged = false;
    
    public function __construct()
    {
        $this->merchantid = Settings::instance()->get('realex_username');
        $this->secret = Settings::instance()->get('realex_secret_key');
        $this->mode = Settings::instance()->get('realex_mode');
        $this->refundpass = Settings::instance()->get('realex_refund_password');
        $this->rebatepass = Settings::instance()->get('realex_rebate_password');
        $this->remote = Settings::instance()->get('realex_api_url');

        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        $this->tmpfile = tempnam('/tmp/', 'realvault');
        curl_setopt($this->curl, CURLOPT_COOKIEFILE, $this->tmpfile);
        curl_setopt($this->curl, CURLOPT_COOKIEFILE, $this->tmpfile);
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
        if (!defined('CURL_SSLVERSION_TLSv1_2')) {
            define('CURL_SSLVERSION_TLSv1_2', 6);
        }
        curl_setopt($this->curl, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
    }
    
    public function __destruct()
    {
        curl_close($this->curl);
        unlink($this->tmpfile);
    }
    
    protected function post($url, $xml)
    {
        curl_setopt($this->curl, CURLOPT_POST, true);
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($this->curl, CURLOPT_URL, $url);
        $response = curl_exec($this->curl);
        $this->last_request = $xml;
        $this->last_response = $response;
        $this->last_info = curl_getinfo($this->curl);
        $lxml = preg_replace('#<number>.*?</number>#i', '<number>****</number>', $xml);
        Model_ExternalRequests::create(
            $this->remote,
            $url,
            $lxml,
            $this->last_response,
            $this->last_info['http_code']
        );
        if ($response) {
            $response = new SimpleXMLElement($response, LIBXML_NOBLANKS | LIBXML_NOCDATA);
        }
        return $response;
    }
    
    public function create_payer($payer_ref, $firstname, $lastname, $email, $order_id = null, $amount = null, $currency = null)
    {
        $timestamp = gmdate('YmdHis');
        //$timestamp = strftime("%Y%m%d%H%M%S");
        $hash = implode('.', array($timestamp, $this->merchantid, $order_id, $amount, $currency, $payer_ref));
        $hash = sha1(sha1($hash) . '.' . $this->secret);
        //timestamp.merchantid.orderid.amount.currency.payerref
        //Example: 20030516175919.yourmerchantid.uniqueid…smithj01
        
        $xml = '<request type="payer-new" timestamp="' . $timestamp . '">
<merchantid>' . $this->merchantid . '</merchantid>
<account>' . $this->mode . '</account>
' . ($order_id ? '<orderid>' . $order_id . '</orderid>' : '') . '
<payer type="Business" ref="' . $payer_ref . '">
<firstname>' . $firstname . '</firstname>
<surname>' . $lastname . '</surname>
<email>' . $email . '</email>
</payer>
<sha1hash>' . $hash . '</sha1hash>
</request>';

        $response = $this->post($this->remote, $xml);
        return $response;
    }
    
    public function update_payer($payer_ref, $firstname, $lastname, $email, $order_id = null, $amount = null, $currency = null)
    {
        $timestamp = gmdate('YmdHis');
        //$timestamp = strftime("%Y%m%d%H%M%S");
        $hash = implode('.', array($timestamp, $this->merchantid, $order_id, $amount, $currency, $payer_ref));
        $hash = sha1(sha1($hash) . '.' . $this->secret);
        //timestamp.merchantid.orderid.amount.currency.payerref
        //Example: 20030516175919.yourmerchantid.uniqueid…smithj01
        
        $xml = '<request type="payer-edit" timestamp="' . $timestamp . '">
<merchantid>' . $this->merchantid . '</merchantid>
<account>' . $this->mode . '</account>
' . ($order_id ? '<orderid>' . $order_id . '</orderid>' : '') . '
<payer type="Business" ref="' . $payer_ref . '">
<firstname>' . $firstname . '</firstname>
<surname>' . $lastname . '</surname>
<email>' . $email . '</email>
</payer>
<sha1hash>' . $hash . '</sha1hash>
</request>';

        $response = $this->post($this->remote, $xml);
        return $response;
    }
    
    public function create_card($card_type, $card_number, $expdate, $holder_name, $payer_ref, $card_ref, $order_id = null, $amount = null, $currency = null)
    {
        $card_number = str_replace(['-', ' '], '', $card_number);
        $timestamp = gmdate('YmdHis');
        $hash = implode('.', array($timestamp, $this->merchantid, $order_id, $amount, $currency, $payer_ref, $holder_name, $card_number));
        $hash = sha1(sha1($hash) . '.' . $this->secret);
        //timestamp.merchantid.orderid.amount.currency.payerref.chname.(card)number
        //Example: 20030516181127.yourmerchantid.uniqueid…smithj01.JohnSmith.498843******9991
        
        $xml = '<request type="card-new" timestamp="' . $timestamp . '">
<merchantid>' . $this->merchantid . '</merchantid>
<account>' . $this->mode . '</account>
' . ($order_id ? '<orderid>' . $order_id . '</orderid>' : '') . '
<card>
<ref>' . $card_ref . '</ref>
<payerref>' . $payer_ref . '</payerref>
<number>' . $card_number . '</number>
<expdate>' . $expdate . '</expdate>
<chname>' . $holder_name . '</chname>
<type>' . $card_type . '</type>
<issueno />
</card>
<sha1hash>' . $hash . '</sha1hash>
</request>';

        $response = $this->post($this->remote, $xml);
        return $response;
    }
    
    public function cancel_card($payer_ref, $card_ref)
    {
        $timestamp = gmdate('YmdHis');
        $hash = implode('.', array($timestamp, $this->merchantid, $payer_ref, $card_ref));
        $hash = sha1(sha1($hash) . '.' . $this->secret);
        //Timestamp.merchantID.payerref.pmtref
        //Example 20010427124523.merchantid.payerref.pmtref
    
        $xml = '<request timestamp="' . $timestamp . '" type="card-cancel-card">
<merchantid>' . $this->merchantid . '</merchantid>
<account>' . $this->mode . '</account>
<card>
<ref>' . $card_ref . '</ref>
<payerref>' . $payer_ref . '</payerref>
</card>
<sha1hash>' . $hash . '</sha1hash>
</request>';

        $response = $this->post($this->remote, $xml);
        return $response;
    }
    
    public function charge_card($payer_ref, $card_ref, $order_id, $amount, $currency, $cvn = null, $recurring = null)
    {
        $currency = strtoupper($currency);
        $timestamp = gmdate('YmdHis');
        $amount = $amount * 100;
        $hash = implode('.', array($timestamp, $this->merchantid, $order_id, $amount, $currency, $payer_ref));
        $hash = sha1(sha1($hash) . '.' . $this->secret);
        //timestamp.merchantid.orderid.amount.currency.payerref
        //Example: 20030520151742.yourmerchantid.transaction01.9999.EUR.bloggsj01

        $xml = '<request type="receipt-in" timestamp="' . $timestamp . '">
<merchantid>' . $this->merchantid . '</merchantid>
<account>' . $this->mode . '</account>
<orderid>' . $order_id . '</orderid>
' . ($cvn ? '<paymentdata><cvn><number>' . $cvn . '</number></cvn></paymentdata>' : '') . '
<amount currency="' . $currency . '">' . $amount . '</amount>
<payerref>' . $payer_ref . '</payerref>
<paymentmethod>' . $card_ref . '</paymentmethod>
<autosettle flag="1" />
' . ($recurring ? '<recurring type="' . $recurring['type'] . '" sequence="' . $recurring['sequence'] . '" />' : '') . '
<md5hash />
<sha1hash>' . $hash . '</sha1hash>
</request>';

        $response = $this->post($this->remote, $xml);
        return $response;
    }

    public function charge($order_id, $amount, $currency, $cardnumber, $expdate, $cardtype, $cardname, $cvn, $recurring = null)
    {
        $cardnumber = str_replace(['-', ' '], '', $cardnumber);
        $currency = strtoupper($currency);
        $timestamp = gmdate('YmdHis');
        $amount = $amount * 100;
        $hash = implode('.', array($timestamp, $this->merchantid, $order_id, $amount, $currency, $cardnumber));
        $hash = sha1(sha1($hash) . '.' . $this->secret);
        //timestamp.merchantid.orderid.amount.currency.payerref
        //Example: 20030520151742.yourmerchantid.transaction01.9999.EUR.bloggsj01

        $xml = '<request type="auth" timestamp="' . $timestamp . '">
<merchantid>' . $this->merchantid . '</merchantid>
<account>' . $this->mode . '</account>
<orderid>' . $order_id . '</orderid>
' . ($cvn ? '<paymentdata><cvn><number>' . $cvn . '</number></cvn></paymentdata>' : '') . '
<amount currency="' . $currency . '">' . $amount . '</amount>
<card>
<number>' . $cardnumber. '</number>
<expdate>' . $expdate . '</expdate>
<type>' . $cardtype . '</type>
<chname>' . $cardname . '</chname>
</card>
<autosettle flag="1"/>
' . ($recurring ? '<recurring type="' . $recurring['type'] . '" sequence="' . $recurring['sequence'] . '" />' : '') . '
<md5hash />
<sha1hash>' . $hash . '</sha1hash>
</request>';

        $response = $this->post($this->remote, $xml);
        return $response;
    }

    public function validate($order_id, $payerref, $cardnumber, $expdate, $cardtype, $cardname, $presind = null, $cvn = null)
    {
        $timestamp = gmdate('YmdHis');
        $hash = implode('.', array($timestamp, $this->merchantid, $order_id, $payerref));
        $hash = sha1(sha1($hash) . '.' . $this->secret);
        //timestamp.merchantid.orderid.payerref

        $xml = '<request type="otb" timestamp="' . $timestamp . '">
<merchantid>' . $this->merchantid . '</merchantid>
<account>' . $this->mode . '</account>
<orderid>' . $order_id . '</orderid>
<card>
<number>' . $cardnumber. '</number>
<expdate>' . $expdate . '</expdate>
<type>' . $cardtype . '</type>
<chname>' . $cardname . '</chname>
' . ($cvn ||$presind ? '<paymentdata><cvn>' . ($cvn ? '<number>' . $cvn . '</number>' : '') . ($presind ? '<presind>' . $presind . '</presind>' : '') . '</cvn></paymentdata>' : '') . '
</card>
<md5hash />
<sha1hash>' . $hash . '</sha1hash>
</request>';

        $response = $this->post($this->remote, $xml);
        return $response;
    }
    
    public function refund_card($payer_ref, $card_ref, $order_id, $amount, $currency, $refund_hash)
    {
        $currency = strtoupper($currency);
        $timestamp = gmdate('YmdHis');
        $hash = implode('.', array($timestamp, $this->merchantid, $order_id, $amount, $currency, $payer_ref));
        $hash = sha1(sha1($hash) . '.' . $this->secret);
        //timestamp.merchant_id.order_id.amount.currency.payerref
        //Example: 20090320151742.yourmerchantid.transaction01.9999.EUR.bloggsj01

        $xml = '<request type="payment-out" timestamp="' . $timestamp . '">
<merchantid>' . $this->merchantid . '</merchantid>
<account>' . $this->mode . '</account>
<orderid>' . $order_id . '</orderid>
<amount currency="' . $currency . '">' . $amount . '</amount>
<payerref>' . $payer_ref . '</payerref>
<paymentmethod>' . $card_ref . '</paymentmethod>
<md5hash />
<sha1hash>' . $hash . '</sha1hash>
<refundhash>' . $refund_hash . '</refundhash>
</request>';

        $response = $this->post($this->remote, $xml);
        return $response;
    }
    
    public function verify_3d_enrolled($payer_ref, $card_ref, $order_id, $amount, $currency)
    {
        $currency = strtoupper($currency);
        $timestamp = gmdate('YmdHis');
        $hash = implode('.', array($timestamp, $this->merchantid, $order_id, $amount, $currency, $payer_ref));
        $hash = sha1(sha1($hash) . '.' . $this->secret);
        //timestamp.merchantid.orderid.amount.currency.payerref
        //Example: 20030520151742.yourmerchantid.transaction01.9999.EUR.bloggsj01
        
        $xml = '<request type="realvault-3ds-verifyenrolled" timestamp="' . $timestamp . '">
<merchantid>' . $this->merchantid . '</merchantid>
<account>' . $this->mode . '</account>
<orderid>' . $order_id . '</orderid>
<amount currency="' . $currency . '">' . $amount . '</amount>
<payerref>' . $payer_ref . '</payerref>
<paymentmethod>' . $card_ref . '</paymentmethod>
<autosettle flag="1" />
<md5hash />
<sha1hash>' . $hash . '</sha1hash>
</request>';

        $response = $this->post($this->remote, $xml);
        return $response;
    }

    public function rebate($authcode, $ref, $order_id, $amount, $currency)
    {
        $amount = $amount * 100;
        $refundhash = sha1($this->rebatepass);
        $currency = strtoupper($currency);
        $timestamp = gmdate('YmdHis');
        $hash = implode('.', array($timestamp, $this->merchantid, $order_id, $amount, $currency, ""));
        $hash = sha1(sha1($hash) . '.' . $this->secret);
        $xml = '<request type="rebate" timestamp="' . $timestamp . '">
<merchantid>' . $this->merchantid . '</merchantid>
<account>' . $this->mode . '</account>
<orderid>' . $order_id . '</orderid>
<amount currency="' . $currency . '">' . $amount . '</amount>
<pasref>' . $ref . '</pasref>
<authcode>' . $authcode . '</authcode>
<sha1hash>' . $hash . '</sha1hash>
<refundhash>' . $refundhash . '</refundhash>
</request>';

        $response = $this->post($this->remote, $xml);
        return $response;
    }
}
