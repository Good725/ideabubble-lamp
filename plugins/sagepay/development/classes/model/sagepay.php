<?php defined('SYSPATH') or die('No Direct Script Access.');

require_once __DIR__ . '/../lib/sagepay.php';

final class Model_Sagepay extends Model
{
	public static $card_types = array("VISA" => "VISA Credit",
										"DELTA" => "VISA Debit",
										"UKE" => "VISA Electron",
										"MC" => "MasterCard",
										"MAESTRO" => "Maestro",
										"AMEX" => "American Express",
										"DC" => "Diner's Club",
										"JCB" => "JCB Card",
										"LASER" => "Laser" );

	public static $countries = array("GB" => "United Kingdom",
										"AF" => "Afghanistan",
										"AX" => "Aland Islands",
										"AL" => "Albania",
										"DZ" => "Algeria",
										"AS" => "American Samoa",
										"AD" => "Andorra",
										"AO" => "Angola",
										"AI" => "Anguilla",
										"AQ" => "Antarctica",
										"AG" => "Antigua and Barbuda",
										"AR" => "Argentina",
										"AM" => "Armenia",
										"AW" => "Aruba",
										"AU" => "Australia",
										"AT" => "Austria",
										"AZ" => "Azerbaijan",
										"BS" => "Bahamas",
										"BH" => "Bahrain",
										"BD" => "Bangladesh",
										"BB" => "Barbados",
										"BY" => "Belarus",
										"BE" => "Belgium",
										"BZ" => "Belize",
										"BJ" => "Benin",
										"BM" => "Bermuda",
										"BT" => "Bhutan",
										"BO" => "Bolivia",
										"BA" => "Bosnia and Herzegovina",
										"BW" => "Botswana",
										"BV" => "Bouvet Island",
										"BR" => "Brazil",
										"IO" => "British Indian Ocean Territory",
										"BN" => "Brunei Darussalam",
										"BG" => "Bulgaria",
										"BF" => "Burkina Faso",
										"BI" => "Burundi",
										"KH" => "Cambodia",
										"CM" => "Cameroon",
										"CA" => "Canada",
										"CV" => "Cape Verde",
										"KY" => "Cayman Islands",
										"CF" => "Central African Republic",
										"TD" => "Chad",
										"CL" => "Chile",
										"CN" => "China",
										"CX" => "Christmas Island",
										"CC" => "Cocos (Keeling) Islands",
										"CO" => "Colombia",
										"KM" => "Comoros",
										"CG" => "Congo",
										"CD" => "Congo, The Democratic Republic of the",
										"CK" => "Cook Islands",
										"CR" => "Costa Rica",
										"CI" => "Côte d'Ivoire",
										"HR" => "Croatia",
										"CU" => "Cuba",
										"CY" => "Cyprus",
										"CZ" => "Czech Republic",
										"DK" => "Denmark",
										"DJ" => "Djibouti",
										"DM" => "Dominica",
										"DO" => "Dominican Republic",
										"EC" => "Ecuador",
										"EG" => "Egypt",
										"SV" => "El Salvador",
										"GQ" => "Equatorial Guinea",
										"ER" => "Eritrea",
										"EE" => "Estonia",
										"ET" => "Ethiopia",
										"FK" => "Falkland Islands (Malvinas)",
										"FO" => "Faroe Islands",
										"FJ" => "Fiji",
										"FI" => "Finland",
										"FR" => "France",
										"GF" => "French Guiana",
										"PF" => "French Polynesia",
										"TF" => "French Southern Territories",
										"GA" => "Gabon",
										"GM" => "Gambia",
										"GE" => "Georgia",
										"DE" => "Germany",
										"GH" => "Ghana",
										"GI" => "Gibraltar",
										"GR" => "Greece",
										"GL" => "Greenland",
										"GD" => "Grenada",
										"GP" => "Guadeloupe",
										"GU" => "Guam",
										"GT" => "Guatemala",
										"GG" => "Guernsey",
										"GN" => "Guinea",
										"GW" => "Guinea-Bissau",
										"GY" => "Guyana",
										"HT" => "Haiti",
										"HM" => "Heard Island and McDonald Islands",
										"VA" => "Holy See (Vatican City State)",
										"HN" => "Honduras",
										"HK" => "Hong Kong",
										"HU" => "Hungary",
										"IS" => "Iceland",
										"IN" => "India",
										"ID" => "Indonesia",
										"IR" => "Iran, Islamic Republic of",
										"IQ" => "Iraq",
										"IE" => "Ireland",
										"IM" => "Isle of Man",
										"IL" => "Israel",
										"IT" => "Italy",
										"JM" => "Jamaica",
										"JP" => "Japan",
										"JE" => "Jersey",
										"JO" => "Jordan",
										"KZ" => "Kazakhstan",
										"KE" => "Kenya",
										"KI" => "Kiribati",
										"KP" => "Korea, Democratic People's Republic of",
										"KR" => "Korea, Republic of",
										"KW" => "Kuwait",
										"KG" => "Kyrgyzstan",
										"LA" => "Lao People's Democratic Republic",
										"LV" => "Latvia",
										"LB" => "Lebanon",
										"LS" => "Lesotho",
										"LR" => "Liberia",
										"LY" => "Libyan Arab Jamahiriya",
										"LI" => "Liechtenstein",
										"LT" => "Lithuania",
										"LU" => "Luxembourg",
										"MO" => "Macao",
										"MK" => "Macedonia, The Former Yugoslav Republic of",
										"MG" => "Madagascar",
										"MW" => "Malawi",
										"MY" => "Malaysia",
										"MV" => "Maldives",
										"ML" => "Mali",
										"MT" => "Malta",
										"MH" => "Marshall Islands",
										"MQ" => "Martinique",
										"MR" => "Mauritania",
										"MU" => "Mauritius",
										"YT" => "Mayotte",
										"MX" => "Mexico",
										"FM" => "Micronesia, Federated States of",
										"MD" => "Moldova",
										"MC" => "Monaco",
										"MN" => "Mongolia",
										"ME" => "Montenegro",
										"MS" => "Montserrat",
										"MA" => "Morocco",
										"MZ" => "Mozambique",
										"MM" => "Myanmar",
										"NA" => "Namibia",
										"NR" => "Nauru",
										"NP" => "Nepal",
										"NL" => "Netherlands",
										"AN" => "Netherlands Antilles",
										"NC" => "New Caledonia",
										"NZ" => "New Zealand",
										"NI" => "Nicaragua",
										"NE" => "Niger",
										"NG" => "Nigeria",
										"NU" => "Niue",
										"NF" => "Norfolk Island",
										"MP" => "Northern Mariana Islands",
										"NO" => "Norway",
										"OM" => "Oman",
										"PK" => "Pakistan",
										"PW" => "Palau",
										"PS" => "Palestinian Territory, Occupied",
										"PA" => "Panama",
										"PG" => "Papua New Guinea",
										"PY" => "Paraguay",
										"PE" => "Peru",
										"PH" => "Philippines",
										"PN" => "Pitcairn",
										"PL" => "Poland",
										"PT" => "Portugal",
										"PR" => "Puerto Rico",
										"QA" => "Qatar",
										"RE" => "Réunion",
										"RO" => "Romania",
										"RU" => "Russian Federation",
										"RW" => "Rwanda",
										"BL" => "Saint Barthélemy",
										"SH" => "Saint Helena",
										"KN" => "Saint Kitts and Nevis",
										"LC" => "Saint Lucia",
										"MF" => "Saint Martin",
										"PM" => "Saint Pierre and Miquelon",
										"VC" => "Saint Vincent and the Grenadines",
										"WS" => "Samoa",
										"SM" => "San Marino",
										"ST" => "Sao Tome and Principe",
										"SA" => "Saudi Arabia",
										"SN" => "Senegal",
										"RS" => "Serbia",
										"SC" => "Seychelles",
										"SL" => "Sierra Leone",
										"SG" => "Singapore",
										"SK" => "Slovakia",
										"SI" => "Slovenia",
										"SB" => "Solomon Islands",
										"SO" => "Somalia",
										"ZA" => "South Africa",
										"GS" => "South Georgia and the South Sandwich Islands",
										"ES" => "Spain",
										"LK" => "Sri Lanka",
										"SD" => "Sudan",
										"SR" => "Suriname",
										"SJ" => "Svalbard and Jan Mayen",
										"SZ" => "Swaziland",
										"SE" => "Sweden",
										"CH" => "Switzerland",
										"SY" => "Syrian Arab Republic",
										"TW" => "Taiwan, Province of China",
										"TJ" => "Tajikistan",
										"TZ" => "Tanzania, United Republic of",
										"TH" => "Thailand",
										"TL" => "Timor-Leste",
										"TG" => "Togo",
										"TK" => "Tokelau",
										"TO" => "Tonga",
										"TT" => "Trinidad and Tobago",
										"TN" => "Tunisia",
										"TR" => "Turkey",
										"TM" => "Turkmenistan",
										"TC" => "Turks and Caicos Islands",
										"TV" => "Tuvalu",
										"UG" => "Uganda",
										"UA" => "Ukraine",
										"AE" => "United Arab Emirates",
										"GB" => "United Kingdom",
										"US" => "United States",
										"UM" => "United States Minor Outlying Islands",
										"UY" => "Uruguay",
										"UZ" => "Uzbekistan",
										"VU" => "Vanuatu",
										"VE" => "Venezuela",
										"VN" => "Viet Nam",
										"VG" => "Virgin Islands, British",
										"VI" => "Virgin Islands, U.S.",
										"WF" => "Wallis and Futuna",
										"EH" => "Western Sahara",
										"YE" => "Yemen",
										"ZM" => "Zambia",
										"ZW" => "Zimbabwe");

	public static $country_map = array('Ireland' => 'IE', 'UK' => 'GB');

	public function charge_basket($card_details, $items, $billing_address, $delivery_address, $deliveryAmount, $discounts, $uniqid = null)
	{
		$sagepay_settings = SagepaySettings::getInstance();
		$sagepay = SagepayApiFactory::create('direct', $sagepay_settings);
		
		$sagepay_billing = new SagepayCustomerDetails();
		$sagepay_billing->firstname = $billing_address['firstname'];
		$sagepay_billing->lastname = $billing_address['lastname'];
		$sagepay_billing->address1 = $billing_address['address1'];
		$sagepay_billing->address2 = @$billing_address['address2'];
		$sagepay_billing->email = @$billing_address['email'];
		$sagepay_billing->phone = @$billing_address['phone'];
		$sagepay_billing->city = $billing_address['city'];
		$sagepay_billing->postcode = $billing_address['postcode'];
		$sagepay_billing->country = $billing_address['country'];
		$sagepay_billing->state = @$billing_address['state'];
		$sagepay->addAddress($sagepay_billing);

		$sagepay_delivery = new SagepayCustomerDetails();
		$sagepay_delivery->firstname = $delivery_address['firstname'];
		$sagepay_delivery->lastname = $delivery_address['lastname'];
		$sagepay_delivery->address1 = $delivery_address['address1'];
		$sagepay_delivery->address2 = @$delivery_address['address2'];
		$sagepay_delivery->email = @$delivery_address['email'];
		$sagepay_delivery->phone = @$delivery_address['phone'];
		$sagepay_delivery->city = $delivery_address['city'];
		$sagepay_delivery->postcode = $delivery_address['postcode'];
		$sagepay_delivery->country = $delivery_address['country'];
		$sagepay_delivery->state = @$delivery_address['state'];
		$sagepay->addAddress($sagepay_delivery);

		$sagepay_card = new SagepayCardDetails();
		$sagepay_card->cardNumber = $card_details['cardNumber'];
		$sagepay_card->cardHolder = $card_details['cardHolder'];
		$sagepay_card->expiryDate = $card_details['expiryDate'];
		$sagepay_card->cv2 = $card_details['cv2'];
		$sagepay_card->cardType = $card_details['cardType'];

		$card_errors = $sagepay_card->validate();
		if(count($card_errors) > 0 ){
			$result = array('errors' => $card_errors);
		} else {
			$sagepay->setPaneValues($card_details);

			$sagepay_basket = new SagepayBasket();
			$sagepay_basket->setId(mt_rand(0, 100000000));
			$sagepay_basket->setDescription('abcxyz');
			$sagepay_basket->setDeliveryNetAmount($deliveryAmount);
			if($discounts){
				$sagepay_basket->setDiscounts($discounts);
			}
			
			foreach( $items as $item ){
				$sagepay_item = new SagepayItem();
				$sagepay_item->setUnitNetAmount($item['net']);
				if(isset($item['description'])){
					$sagepay_item->setDescription($item['description']);
				}
				if(isset($item['sku'])){
					$sagepay_item->setProductSku($item['sku']);
				}
				if(isset($item['code'])){
					$sagepay_item->setProductCode($item['code']);
				}
				if(isset($item['quantity'])){
					$sagepay_item->setQuantity($item['quantity']);
				}
				if(isset($item['tax'])){
					$sagepay_item->setUnitTaxAmount($item['tax']);
				}
				$sagepay_basket->addItem($sagepay_item);
			}

			$sagepay->setBasket($sagepay_basket);
			$result = $sagepay->createRequest();
		}
		if(!isset($result['errors'])){
			if($result && $result['Status'] == '3DAUTH' && $result['3DSecureStatus'] == 'OK'){
				$result['TermUrl'] = URL::site('frontend/sagepay/3dcomplete');
			}
		} else {
			$result['message'] = '';
			foreach($result['errors'] as $key => $details ){
				$result['message'] .= 'Invalid ' . $key;
			}
		}
		return $result;
	}
	
	public function complete3d($acsurl_response)
	{
		$sagepay_settings = SagepaySettings::getInstance();
		$sagepay = SagepayApiFactory::create('direct', $sagepay_settings);
		print_r($acsurl_response);
		$params = array( 'MD' => $acsurl_response['MD'], 'PARes' => $acsurl_response['PaRes'] );
		//echo $sagepay_settings->getPurchaseUrl('direct3d');
		$result = SagepayCommon::requestPost($sagepay_settings->getPurchaseUrl('direct3d'), $acsurl_response);
		print_r($result);
		die();
	}
	
	public function refund($tx, $amount)
	{
		$sagepay_settings = SagepaySettings::getInstance();
		$sagepay = SagepayApiFactory::create('direct', $sagepay_settings);
	}
	
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
	
	public function process_payment($post){
		if(@$post->MD && @$post->PaRes){
			$m_sagepay = new Model_Sagepay();
			$sagepay_settings = SagepaySettings::getInstance();
			$sagepay = SagepayApiFactory::create('direct', $sagepay_settings);
			$params = array('MD' => $post->MD, 'PARes' => $post->PaRes);
			//echo $sagepay_settings->getPurchaseUrl('direct3d');
			$result = SagepayCommon::requestPost($sagepay_settings->getPurchaseUrl('direct3d'), $params);
			return $result;
		} else {
			$checkout_model = new Model_Checkout();
			$cart = $checkout_model->get_cart_details();
			$amount_to_pay  = $cart->final_price;
			$currency       = 'Eur';
			$card_number    = isset($post->ccNum) ? $post->ccNum : '';
			$card_exp_month = isset($post->ccExpMM) ? $post->ccExpMM : '';
			$card_exp_year  = isset($post->ccExpYY) ? $post->ccExpYY : '';
			$card_type      = isset($post->ccType) ? $post->ccType : '';
			$card_ccv       = isset($post->ccv) ? $post->ccv : '';
			$card_name      = isset($post->ccName) ? $post->ccName : '';
	
			$card_details = array( 'cardNumber' => $card_number,
									'cardHolder' => $card_name,
									'expiryDate' => $card_exp_month . $card_exp_year,
									'cv2' => $card_ccv,
									'cardType' => $card_type );
			
			$billing_address = array( 'firstname' => $post->shipping_name,
										'lastname' => $post->shipping_name,
										'address1' => $post->address_1,
										'address2' => '',
										'city' => $post->address_2,
										'country' => @self::$country_map[$post->address_4] ? self::$country_map[$post->address_4] : '',
										'postcode' => @$post->postcode );
			
			$delivery_address = $billing_address;
			
			$items = array();
			foreach($cart->lines as $line){
				$items[] = array( 'description' => $line->product->title,
								'sku' => $line->product->id,
								'code' => $line->product->id,
								'net' => $line->product->price,
								'tax' => $cart->vat_rate > 0 ? round($line->product->price * $cart->vat_rate, 2) : 0,
								'quantity' => $line->quantity );
			}
			
			$deliveryAmount = $cart->vat_rate > 0 ?
					round($cart->shipping_price * (1 + $cart->vat_rate), 2) : $cart->shipping_price;

			$discounts = array();
			if($cart->discounts){
				$discounts['discount'] = array( 'fixed' => $cart->discounts, 'description' => 'Discount' );
			}
			$m_sagepay = new Model_Sagepay();
			$result = $m_sagepay->charge_basket($card_details, $items, $billing_address, $delivery_address, $deliveryAmount, $discounts);
			//print_r($result);die();
			//print_r($post);print_r($cart);die();
			return $result;
		}
	}
}
