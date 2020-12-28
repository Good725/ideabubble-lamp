<?php defined('SYSPATH') or die('No direct script access.');

//Manages all payments - Processing using Realex etc.
class Model_PaymentProcessorRealex extends Model {

    private static $_parentElements;
    private static $_currentTSSCheck;
    private static $_currentElement;
    private static $_TSSChecks;
    private static $_XML;
    private $_payment_success;
    private $_payment_fail;


    /**
     * Function used to provide access to a Realex-processing of Payments by a Credit/Debit card.
     *
     * @param int $amount_to_pay - Integer holding the amount to be paid. Will be converted to CENTS before to be sent to Realex.
     * @param String $currency - String holding the currency type to be used.<br />
     * 					  		 At the moment this can be just: Euros: EUR
     * 							 @TODO: check if this works with other currencies: GBP is the other currency which might be used
     * @param int $card_number - Integer holding the Credit/Debit Card number to be used for the Payment processing
     * @param int $card_exp_month - Integer holding the Expiry Month of the Card to be used.
     * @param int $card_exp_year - Integer holding the Expiry Year of the Card to be used.
     * @param String $card_type - String holding the type of the card to be used.<br />
     * 							  Possible values:<br />
     * 							  - <em>visa</em>: Visa<br />
     * 							  - <em>mc</em>: Mastercard<br />
     * 							  - <em>laser</em>: Laser<br />
     * 						 	  - <em>switch</em>: Switch<br />
     * 							  - <em>amex</em>: American Express<br />
     * 							  - <em>diners</em>: Diners Club
     * @param int $card_ccv - Integer holding the Card CCV number.
     * @param String $card_name - String holding the Name written on the Card to be used.
     * @return array - Array Holding the Realex-Request Response result with the following structure:<br />
     * 					array(<br />
     * 						'response' => 2 or 3 digit code indicating the Result of the Realex-Request. Example: '00' (SUCCESS) or '101' (DECLINED).<br />For more info: http://web.ideabubble.ie/confluence/display/IB/Realex+Test+Cards , <br />
     * 						'timestamp' => STRING holding the processed Realex-Request Timestamp,<br />
     * 						'message' => STRING holding the return message for this Realex-Request<br />
     * 					)
     */
    public static function process_realex_payment($amount_to_pay, $currency, $card_number, $card_exp_month, $card_exp_year, $card_type, $card_ccv, $card_name){

        // accept card numbers like 4242-4242-4242-4242 or 4242 4242 4242 4242
        $card_number = str_replace(array(' ', '-'), '', $card_number);

        //Process the payment and return
        return Model_PaymentProcessorRealex::factory('PaymentProcessorRealex')->ib_process_realex(
            number_format($amount_to_pay*100, 0, '', ''),
            $currency,
            $card_number,
            $card_exp_month.$card_exp_year,
            $card_type,
            $card_name,
            $card_ccv
        );
    }//end of function


    /**
     * Function used to provide a Simple-Realex processing of Credit-Card payments.
     *
     * @param $amount - Integer holding the amount to be paid - IN CENTS
     * @param $currency - String holding the currency type to be used.<br />
     * 					  At the moment this can be just: Euros: EUR
     * 					  @TODO: check if this works with other currencies: GBP is the other currency which might be used
     *
     * @param int $cardnumber - Integer holding the Credit/Debit Card number to be used for the Payment processing
     * @param int $expdate - Integer holding the Expiry Date of the Card to be used.<br />
     *				   The sequence of integers MUST BE in the format: <strong>month</strong>.<strong>year</strong>, i.e. ccExpMM.ccExpYY (without the "dot" -> .).
     * @param String $cardtype - String holding the type of the card to be used.<br />
     * 							 Possible values:<br />
     * 							 - <em>visa</em>: Visa<br />
     * 							 - <em>mc</em>: Mastercard<br />
     * 							 - <em>laser</em>: Laser<br />
     * 						 	 - <em>switch</em>: Switch<br />
     * 							 - <em>amex</em>: American Express<br />
     * 							 - <em>diners</em>: Diners Club
     * @param String $cardname - String holding the Name written on the Card to be used.
     * @return array - Array Holding the Realex-Request Response result with the following structure:<br />
     * 					array(<br />
     * 						'response' => 2 or 3 digit code indicating the Result of the Realex-Request. Example: '00' (SUCCESS) or '101' (DECLINED).<br />For more info: http://web.ideabubble.ie/confluence/display/IB/Realex+Test+Cards , <br />
     * 						'timestamp' => STRING holding the processed Realex-Request Timestamp,<br />
     * 						'message' => STRING holding the return message for this Realex-Request<br />
     * 					)
     */
    private function ib_process_realex($amount, $currency, $cardnumber, $expdate, $cardtype, $cardname, $cvn = null) {

        //exit('Hello from PaymentProcessor: ib_process_realex('.$amount.', '.$currency.', '.$cardnumber.', '.$expdate.', '.$cardtype.', '.$cardname.')');

        /**
         * @TODO: the CCV Number is used for: ib_process_worldnet() REALEX PROCESSING - if required this: ib_process_worldnet() function and its related params and functions, to be added from the CMS-Version1 PaymentProcessor
         */

        self::$_parentElements = array();
        self::$_TSSChecks = array();
        self::$_currentElement = 0;
        self::$_currentTSSCheck = '';
        self::$_XML = new stdClass();
        $realex_query_result = array('response' => '', 'timestamp' => '', 'message' => '');

        //Get the Realex Settings from the Settings controller
        $merchantid = Settings::instance()->get('realex_username');
        $secret = Settings::instance()->get('realex_secret_key');
        $realex_mode = Settings::instance()->get('realex_mode');

        // 1. Set timestamp for this Realex Request
        $timestamp = strftime("%Y%m%d%H%M%S");
        mt_srand((double)microtime()*1000000);

        // 2. Set OrderID for this Realex Request
        $orderid = $timestamp."-".mt_rand(1, 999);

        // 3. Set the Realex Request - Secret string
        //3a. setup currency variable for upper case to be used in XML AND HASH :)
        $currency = strtoupper($currency);
        $tmp = "$timestamp.$merchantid.$orderid.$amount.$currency.$cardnumber";
        $md5hash = md5($tmp);
        $tmp = "$md5hash.$secret";
        $md5hash = md5($tmp);

        // 5. Generate the request xml that is send to Realex Payments.
        $xml = "<request type='auth' timestamp='$timestamp'>
					<merchantid>$merchantid</merchantid>
					<account>$realex_mode</account>
					<orderid>$orderid</orderid>
					" . ($cvn ? '<paymentdata><cvn><number>' . $cvn . '</number></cvn></paymentdata>' : '') . "
					<amount currency='$currency'>$amount</amount>
					<card>
							<number>$cardnumber</number>
							<expdate>$expdate</expdate>
							<type>$cardtype</type>
							<chname>$cardname</chname>
					</card>
					<autosettle flag='1'/>
					<md5hash>$md5hash</md5hash>
					<tssinfo>
							<address type=\"billing\">
									<country>ie</country>
							</address>
					</tssinfo>
				</request>";
        // Log the realex request, but star out the card number (except the last 3 digits)
        //log_message('debug', '* * * * * * * * Realex auth request: '.preg_replace("/<number>\d+(\d\d\d)<\/number>/", "<number>**************$1</number>", $xml));

        // 6. Send the request array to Realex Payments
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, Settings::instance()->get('realex_api_url') != '' ? Settings::instance()->get('realex_api_url') : "https://epage.payandshop.com/epage-remote.cgi");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, "payandshop.com php version 0.9");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); // this line makes it work under https
        if (!defined('CURL_SSLVERSION_TLSv1_2')) {
            define('CURL_SSLVERSION_TLSv1_2', 6);
        }
        curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        $response = curl_exec ($ch);
        curl_close ($ch);

        try {
            $ccresult = new SimpleXMLElement($response, LIBXML_NOBLANKS | LIBXML_NOCDATA);
            $realex_query_result['response'] = (string)$ccresult->result;
            $realex_query_result['timestamp'] = (string)$ccresult['timestamp'];
            $realex_query_result['message'] = (string)$ccresult->message;

        } catch (Exception $exc) {
            Log::instance()->add(Log::ERROR, 'Realex payment error: '.$exc->getMessage().$exc->getTraceAsString())->write();

            $realex_query_result['response']  = -1;
            $realex_query_result['timestamp'] = '';
            $realex_query_result['message']   = __('Error processing payment. If this problem continues, please contact the administration.');
        }

        //log_message('debug', '* * * * * * * * Realex response: '.print_r(self::$_XML, true));

        return $realex_query_result;
    }//end of function
}//end of class
