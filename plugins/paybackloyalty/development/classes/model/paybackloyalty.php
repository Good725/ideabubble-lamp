<?php

/**
 * Provides access to "Payback Loyalty Webs Service - API"
 *
 * @package PaybackLoyalty WS - API Wrapper
 * @author Kosta
 * @version 1.1
 */
final class Model_PaybackLoyalty extends Model
{

    //PaybackLoyalty WS requests related variables
	private $_ws_url 			= '';
    private $_partnerId			= '';
    private $_requestId 		= '';
    private $_storeId 			= '';
	private $_userId 			= '';
	private	$_is_available	= FALSE;
	private	$_error_message		= '';

    //these are array mappings for some of the variables codes used by the PL-WS
    private $_person_gender_id = array(1 => 'Male', 2 => 'Female');
    private $_person_title_id = array(0 => '', 1 => 'Mr', 2 => 'Mrs', 3 => 'Miss', 4 => 'Ms', 5 => 'Dr', 6 => 'Fr', 7 => 'Prof');
    private $_person_registered = array(1 => 'Registered', 2 => 'Unregistered');
    private $_countries_ids = array(1 => 'Ireland');
    private $_languages_ids = array(43 => 'English');

    /**
     * @NOTE: These are used ONLY for reference while building this API-Wrapper. @TODO: TO BE DELETED after finish!
     *
     * PL WS calls:
     *
     * 1. ValidateMemberAccess:
     *      Used to Login a Customer to the PL-WS Member's Area
     *      Requires partnerId, requestId, username and password
     *
     * 2. GetMemberInfo:
     *      Used to get customer's Information like:
     *              - memberId, Forename, Surname....
     *                ContactByPartners, ContactForResearch, ContactBySMS, DateOfBirth, LoyaltyBalance, SpendBalance
     *                NOTE: The last 6 variables are not received by: ValidateMemberAccess, therefore they will be appended to the $_SESSION['pl_user'] data
     *      Requires: partnerId, requestId, memberId, isMemberId
     *
     * 3. SaveMemberDetails_1_3:
     *      Used to Update Customers PL-Account Details
     *      Requires partnerId, requestId, storeId, userId and memberId + the rest of the PL-Account details
     *
     * 4. GetTransactionInfo:
     *      Used to get Customer's Transaction Info
     *      Requires: partnerId, requestId, memberId, fromDate, toDate, maxNo
     *
     * 5. GetMemberTransactions: NOT SUPPORTED in latest PL version
     *      Used to get all customer's transactions
     *      Requires: partnerId, requestId, memberId
     *
     * 6. RegisterNewMember_1_3: NOT SUPPORTED in latest PL version
     *      Used to register a new member to a Store
     *      Requires: partnerId, requestId, storeId, firstname and surname, mobile or email + the rest of the Registration form
     *
     * 7. UpdatePassword
	 * 		Used to update/edit Customer PL-Login Password
	 * 		Requires: partnerId, requestId, storeId, memberId, oldpassword, newpassword, confirmpassword
	 *
	 * 8. GetCardInfo
	 * 		Used to get all the information for the Cards available for the current Customer
	 * 		Requires: partnerId, requestId, storeId, memberId
	 *
	 * 9. RequestNewCardNumber
	 * 		Used to get a RESERVED Card Number for a New Customer. Once the response is successful, Card Number won’t be reserved anymore.
	 * 		Requires: partnerId, requestId
	 *
	 * 10. AddMemberPassword_1_4
	 * 		Used to create an entry to member login
	 *		Requires: partnerId, requestId, memberId, username, Password
	 *
     * << The following request are used for the PL-Checkout processing >>
     *
     * 11. AdjustPointsBalance:
     *      Used to add points to a PL-Customer account
     *      Requires: partnerId, requestId, memberId, cardNo (same as memberId, if there is not other card?), points (float), reason
     *
     * 12. ConvertPointsToCash:
     *      Used to convert points to cash - returns JUST true on success or false + error_message on fail
     *      Requires: partnerId, requestId, memberId, cardNo, points
     */

    //Constructor
    function __construct(){
        $this->_ws_url 			= settings::instance()->get('ws_url').'?op=';
        $this->_partnerId 		= settings::instance()->get('partner_id');
        $this->_requestId 		= settings::instance()->get('request_id');
        $this->_storeId 		= settings::instance()->get('store_id');
        $this->_userId 			= settings::instance()->get('user_id');
        $this->_is_available	= TRUE;


/*
		//Set the required for the PL-WS GLOBALS
		if(
			(isset($_SESSION['Settings']->payback_loyalty_url		) && !empty($_SESSION['Settings']->payback_loyalty_url			)) &&
			(isset($_SESSION['Settings']->payback_loyalty_partner_id) && !empty($_SESSION['Settings']->payback_loyalty_partner_id	)) &&
			(isset($_SESSION['Settings']->payback_loyalty_request_id) && !empty($_SESSION['Settings']->payback_loyalty_request_id	)) &&
			(isset($_SESSION['Settings']->payback_loyalty_store_id	) && !empty($_SESSION['Settings']->payback_loyalty_store_id		)) &&
			(isset($_SESSION['Settings']->payback_loyalty_user_id	) && !empty($_SESSION['Settings']->payback_loyalty_user_id		))
		){
			$this->_ws_url 			= $_SESSION['Settings']->payback_loyalty_url.'?op=';
			$this->_partnerId 		= $_SESSION['Settings']->payback_loyalty_partner_id;
			$this->_requestId 		= $_SESSION['Settings']->payback_loyalty_request_id;
			$this->_storeId 		= $_SESSION['Settings']->payback_loyalty_store_id;
			$this->_userId 			= $_SESSION['Settings']->payback_loyalty_user_id;
			$this->_is_available	= TRUE;

		//Else -=> disable THIS Model
		}else{
			$this->_is_available	= FALSE;
			$this->_error_message	= 'Payback Loyalty Plugin Settings are not Set Up in the CMS->Settings';
		}*/
    }//end of constructor


    //Used for the Customer's login to PL-WS: Members-Area
    function validate_member($username, $password){

        //local variable to use
        $return_data = new stdClass();
        $return_data->err_msg = 'OK';
        $return_data->request_data = array();


        //send request to the PL-WS - ONLY if THIS Model is AVAILABLE, i.e. PL-WS Settings have been set up in the "CMS->Settings: Payback Loyalty WS"
        if($this->_is_available && empty($this->_error_message)){
			$ws_result = $this->query_web_service(
				'ValidateMemberAccess',
				array(
					'partnerId' => $this->_partnerId,
					'requestId' => $this->_requestId,
					'storeId' => $this->_storeId,
					'username' => $username,
					'password' => $password
				)
			);

		//default the $ws_result and return
		}else{
			$ws_result	= new stdClass();
			$ws_result->err_msg = $this->_error_message;
			$ws_result->xml_msg = '';
		}


        //request was successful
        if($ws_result->err_msg === 'OK'){
            //extract the received data from: $ws_result->xml_msg to an associative array
            $return_data = $this->extract_xml_data_to_assoc($ws_result->xml_msg);
        //request to WS was not successful
        }else{
            $return_data->err_msg = '(ValidateMemberAccess): '.$ws_result->err_msg;
            //xml_message in this case is NULL
            $return_data->request_data = $ws_result->xml_msg;
        }

        //Return
        return $return_data;
    }//end of function


    //Used to get Member's details
    function get_member_info($memberId, $isMemberId = 'true'){
        //local variable to use
        $return_data = new stdClass();
        $return_data->err_msg = 'OK';
        $return_data->request_data = array();


        //send request to the PL-WS - ONLY if THIS Model is AVAILABLE, i.e. PL-WS Settings have been set up in the "CMS->Settings: Payback Loyalty WS"
		if($this->_is_available && empty($this->_error_message)){
			$ws_result = $this->query_web_service(
				'GetMemberInfo',
				array(
					'partnerId'  => $this->_partnerId,
					'requestId'  => $this->_requestId,
					'memberId'   => $memberId,			//either memberId number or cardNo
					'isMemberId' => $isMemberId 		//set to true -=> when memberId is a proper memberId / false when a cardNo is passed instead of memberId
				)
			);

		//default the $ws_result and return
		}else{
			$ws_result	= new stdClass();
			$ws_result->err_msg = $this->_error_message;
			$ws_result->xml_msg = '';
		}

        //request was successful
        if($ws_result->err_msg === 'OK'){
            //extract the received data from: $ws_result->xml_msg to an associative array
            $return_data = $this->extract_xml_data_to_assoc($ws_result->xml_msg);
            //request to WS was not successful
        }else{
            $return_data->err_msg = '(GetMemberInfo): '.$ws_result->err_msg;
            //xml_message in this case is NULL
            $return_data->request_data = $ws_result->xml_msg;
        }

        //Return
        return $return_data;
    }//end of function

    //Used to save/update Member's details
    function update_member_details($memberId, $member_data){

        //local variable to use
        $return_data = new stdClass();
        $return_data->err_msg = 'OK';
        $return_data->request_data = array();

		//send request to the PL-WS - ONLY if THIS Model is AVAILABLE, i.e. PL-WS Settings have been set up in the "CMS->Settings: Payback Loyalty WS"
		if($this->_is_available && empty($this->_error_message)){
			$ws_result = $this->query_web_service(
				'SaveMemberDetails_1_3',
				array(
					'partnerId' => $this->_partnerId,
					'requestId' => $this->_requestId,
					'storeId' => $this->_storeId,
					'userId' => $this->_userId,
					'memberId' => $memberId,
//@TODO: These are not used by the new system. If after testing of latest updates this works fine, remove the USERNAME and PASSWORD from this function calls. Here and in the related function calls.
//	                'username' => $username,
//	                'password' => $password,
					'language' => $member_data['pl_language_id'],
					'gender' => $member_data['pl_gender_id'],
					'title' => $member_data['pl_title_id'],
					'firstname' => $member_data['pl_f_name'],
					'surname' => $member_data['pl_s_name'],
					//must be in format: Full Date/Time: ISO Format: yyyy-mm-ddTh:m:s+01:00
					'dateofbirth' => date('c', strtotime($member_data['pl_dob'])),
					'addr1' => $member_data['pl_addr1'],
					'addr2' => $member_data['pl_addr2'],
					'addr3' => $member_data['pl_addr3'],
					'addr4' => $member_data['pl_addr4'],
					'country' => $member_data['pl_country_id'],
					'email' => $member_data['pl_email'],
					'mobileNo' => $member_data['pl_mobile'],
					'contactForInfo' => (isset($member_data['pl_contact_for_info'] ) AND (int)$member_data['pl_contact_for_info'] == 1) ? '1' : '0',
					'contactForResearch' => (isset($member_data['pl_contact_for_research']) AND (int)$member_data['pl_contact_for_research'] == 1) ? '1' : '0',
					'contactByPartners' => (isset($member_data['pl_contact_by_partners']) AND (int)$member_data['pl_contact_by_partners'] == 1)? '1' : '0' ,
					'contactBySMS' => (isset($member_data['pl_contact_by_sms']) AND (int)$member_data['pl_contact_by_sms'] == 1) ? '1' : '0',
//@TODO: Update the MemberRegistration and Ac_Editor form for this field
					'nationality' => 78 //English: 52, Irish: 78
				)
			);

		//default the $ws_result and return
		}else{
			$ws_result	= new stdClass();
			$ws_result->err_msg = $this->_error_message;
			$ws_result->xml_msg = '';
		}

        //request was successful
        if($ws_result->err_msg === 'OK'){
            //extract the received data from: $ws_result->xml_msg to an associative array
            $return_data = $this->extract_xml_data_to_assoc($ws_result->xml_msg);
        //request to WS was not successful
        }else{
            $return_data->err_msg = '(SaveMemberDetails_1_3): '.$ws_result->err_msg;
            //xml_message in this case is NULL
            $return_data->request_data = $ws_result->xml_msg;
        }

        //Return
        return $return_data;
    }//end of function


    //Used to save/update Member's password
    function update_member_password($memberId, $oldpassword, $newpassword, $confirmpassword){

        //local variable to use
        $return_data = new stdClass();
        $return_data->err_msg = 'OK';
        $return_data->request_data = array();

        //send request to the PL-WS - ONLY if THIS Model is AVAILABLE, i.e. PL-WS Settings have been set up in the "CMS->Settings: Payback Loyalty WS"
		if($this->_is_available && empty($this->_error_message)){
			$ws_result = $this->query_web_service(
				'UpdatePassword',
				array(
					'partnerId' => $this->_partnerId,
					'requestId' => $this->_requestId,
					'storeId' => $this->_storeId,
					'memberId' => $memberId,
					'oldpassword' => $oldpassword,
					'newpassword' => $newpassword,
					'confirmpassword' => $confirmpassword
				)
			);

		//default the $ws_result and return
		}else{
			$ws_result	= new stdClass();
			$ws_result->err_msg = $this->_error_message;
			$ws_result->xml_msg = '';
		}

        //request was successful
        if($ws_result->err_msg === 'OK'){
            //extract the received data from: $ws_result->xml_msg to an associative array
            $return_data = $this->extract_xml_data_to_assoc($ws_result->xml_msg);
            //request to WS was not successful
        }else{
            $return_data->err_msg = '(UpdatePassword): '.$ws_result->err_msg;
            //xml_message in this case is NULL
            $return_data->request_data = $ws_result->xml_msg;
        }

        //Return
        return $return_data;
    }//end of function


    //Used to get Customer's Transaction details for a particular period of time
    function get_transaction_info($memberId, $fromDate, $toDate, $maxNo){

        //local variable to use
        $return_data = new stdClass();
        $return_data->err_msg = 'OK';
        $return_data->request_data = array();

		//send request to the PL-WS - ONLY if THIS Model is AVAILABLE, i.e. PL-WS Settings have been set up in the "CMS->Settings: Payback Loyalty WS"
		if($this->_is_available && empty($this->_error_message)){
			$ws_result = $this->query_web_service(
				'GetTransactionInfo',
				array(
					'partnerId' => $this->_partnerId,
					'requestId' => $this->_requestId,
					'storeId' => $this->_storeId,
					'memberId' => $memberId,
					//'fromDate' => date('c', strtotime($fromDate)),
					'fromDate' => date('c', strtotime($fromDate)),
					'toDate' => date('c', strtotime($toDate)),
					'maxNo' => $maxNo
				)
			);

		//default the $ws_result and return
		}else{
			$ws_result	= new stdClass();
			$ws_result->err_msg = $this->_error_message;
			$ws_result->xml_msg = '';
		}

        //request was successful
        if($ws_result->err_msg === 'OK'){
            //extract the received data from: $ws_result->xml_msg to an associative array
            $return_data = $this->extract_xml_data_to_assoc($ws_result->xml_msg);
        }else{
            $return_data->err_msg = '(GetTransactionInfo): '.$ws_result->err_msg;
            //xml_message in this case is NULL
            $return_data->request_data = $ws_result->xml_msg;
        }

        //Return
        return $return_data;
    }//end of function


    //Used to register a PL-Customer to the PL-WS - NOT
	//@TODO: This WebService call: RegisterNewMember_1_3 is not supported by the latest version
	//@TODO: This Function is REPLACED by: get_transaction_info
	//@TODO: If still here after 1st of July 2013 -=> Remove this function and all of its related files and calls
    function register_member($firstname, $surname, $member_data){

        //local variable to use
        $return_data = new stdClass();
        $return_data->err_msg = 'OK';
        $return_data->request_data = array();

		//send request to the PL-WS - ONLY if THIS Model is AVAILABLE, i.e. PL-WS Settings have been set up in the "CMS->Settings: Payback Loyalty WS"
//		if($this->_is_available && empty($this->_error_message)){
//			$ws_result = $this->query_web_service(
//				'RegisterNewMember_1_3',
//				array(
//					//currently hardcoded as private variables of this Class
//					'partnerId' => $this->_partnerId,
//					'requestId' => $this->_requestId,
//					'storeId' => $this->_storeId,
//					'userId' => $this->_userId,
//					//received from the registration form
//					'language' => $member_data['pl_language_id'],
//					'gender' => $member_data['pl_gender_id'],
//					'title' => $member_data['pl_title_id'],
//					'firstname' => $firstname,
//					'surname' => $surname,
//					//must be in format: Full Date/Time: ISO Format: yyyy-mm-ddTh:m:s+01:00
//					'dateofbirth' => date('c', strtotime($member_data['pl_dob'])),
//					'addr1' => $member_data['pl_addr1'],
//					'addr2' => $member_data['pl_addr2'],
//					'addr3' => $member_data['pl_addr3'],
//					'addr4' => $member_data['pl_addr4'],
//					'country' => $member_data['pl_country_id'],
//					'email' => $member_data['pl_email'],
//					'mobileNo' => $member_data['pl_mobile'],
//					'contactForInfo' => $member_data['pl_contact_for_info'],
//					'contactForResearch' => $member_data['pl_contact_for_research'],
//					'contactByPartners' => $member_data['pl_contact_by_partners'],
//					'contactBySMS' => $member_data['pl_contact_by_sms'],
////@TODO: Update the MemberRegistration and Acc_Editor form for this field
//					'nationality' => 78 //English: 52, Irish: 78
//				)
//			);
//
//		//default the $ws_result and return
//		}else{
//			$ws_result	= new stdClass();
//			$ws_result->err_msg = $this->_error_message;
//			$ws_result->xml_msg = '';
//		}
//
//        //request was successful
//        if($ws_result->err_msg === 'OK'){
//            //extract the received data from: $ws_result->xml_msg to an associative array
//            $return_data = $this->extract_xml_data_to_assoc($ws_result->xml_msg);
//        //request to WS was not successful
//        }else{
//            $return_data->err_msg = '(RegisterNewMember_1_3): '.$ws_result->err_msg;
//            //xml_message in this case is NULL
//            $return_data->request_data = $ws_result->xml_msg;
//        }

        //Return
        return $return_data;
    }//end of function


    /*
     * Used to convert ACTUAL POINTS in a Payback Loyalty Account - card into: "cash" - Card->SpendBalance, which can ALSO be used to purchase goods
     * NOTE: SpendBalance is actual Money, i.e.
     * 		 100 Points -=> 1 € SpendBalance
     * 		 Customer points can be converted to spend balance at the rate of 100 points to 1€.
     */
//@TODO: This WebService call: ConvertPointsToCash is not USED for DEDUCTION of POINTS used as a payment -=> AdjustPointsBalance in (update_account_balance) IS USED INSTEAD
//@TODO: WISHLIST - This Function MIGHT be used for Version 2 -=> to enable ONLINE Payments with "cashed" points
    function convert_points_to_cash($memberId, $cardNo, $points_to_convert){

        //local variable to use
        $return_data = new stdClass();
        $return_data->err_msg = 'OK';
        $return_data->request_data = array();

        //send request to the PL-WS - ONLY if THIS Model is AVAILABLE, i.e. PL-WS Settings have been set up in the "CMS->Settings: Payback Loyalty WS"
		if($this->_is_available && empty($this->_error_message)){
			$ws_result = $this->query_web_service(
				'ConvertPointsToCash',
				array(
					//currently hardcoded as private variables of this Class
					'partnerId' => $this->_partnerId,
					'requestId' => $this->_requestId,
					'storeId' => $this->_storeId,
					'userId' => $this->_userId,
					//obtained form the parameters list of this function
					'memberId' => $memberId,
					'cardNo' => $cardNo,
					'points' => $points_to_convert
				)
			);

		//default the $ws_result and return
		}else{
			$ws_result	= new stdClass();
			$ws_result->err_msg = $this->_error_message;
			$ws_result->xml_msg = '';
		}

        //request was successful
        if($ws_result->err_msg === 'OK'){
            //extract the received data from: $ws_result->xml_msg to an associative array
            $return_data = $this->extract_xml_data_to_assoc($ws_result->xml_msg);
            //request to WS was not successful
        }else{
            $return_data->err_msg = '(ConvertPointsToCash): '.$ws_result->err_msg;
            //xml_message in this case is NULL
            $return_data->request_data = $ws_result->xml_msg;
        }

        //Return
        return $return_data;

    }//end of function

    //Used to add points to a Payback Loyalty Account - card, on successful purchase
    final public function update_points(){

        $pl_points_to_convert = (isset( $_SESSION['pl_user']['pl_points_to_convert'] ) AND  $_SESSION['pl_user']['pl_points_to_convert'] > 0) ?  $_SESSION['pl_user']['pl_points_to_convert'] * -1 : 0;

        //Remove the expended points
        $ws_cptc_request_result = $this->update_account_balance(
        //memberid
            $_SESSION['pl_user']['memberid'],
            //card-number which is in use
            $_SESSION['pl_user']['account_card_in_use'],
            //reduce the Total Number of PL Points on the Card with the number of the used by the Customer points, in this transaction
            $pl_points_to_convert,
            //reason message for this points update
            $_SERVER['HTTP_HOST'] .': Online Shop, ' . date('r')
        );

        if($ws_cptc_request_result->err_msg !== 'OK'){
            Log::instance()->add(Log::ERROR, 'Erron on removing points, "add_points()" ');
        }

        //Add the points based on the expended amount
        $ws_cptc_request_result = $this->update_account_balance(
            //memberid
            $_SESSION['pl_user']['memberid'],
            //card-number which is in use
            $_SESSION['pl_user']['account_card_in_use'],
            //reduce the Total Number of PL Points on the Card with the number of the used by the Customer points, in this transaction
            $_SESSION[Model_Checkout::CART_SESSION_ID]->final_price,
            //reason message for this points update
            $_SERVER['HTTP_HOST'] .': Online Shop, ' . date('r')
        );

        if($ws_cptc_request_result->err_msg !== 'OK'){
            Log::instance()->add(Log::ERROR, 'Erron on adding points, "add_points()" ');
        }

    }


    //Used to add points to a Payback Loyalty Account - card, on successful purchase
    function update_account_balance($memberId, $cardNo, $points_to_add, $reason){

        //local variable to use
        $return_data = new stdClass();
        $return_data->err_msg = 'OK';
        $return_data->request_data = array();

        //send request to the PL-WS - ONLY if THIS Model is AVAILABLE, i.e. PL-WS Settings have been set up in the "CMS->Settings: Payback Loyalty WS"
		if($this->_is_available && empty($this->_error_message)){
			$ws_result = $this->query_web_service(
				'AdjustPointsBalance',
				array(
					//currently hardcoded as private variables of this Class
					'partnerId' => $this->_partnerId,
					'requestId' => $this->_requestId,
					'storeId' => $this->_storeId,
					'userId' => $this->_userId,
					//obtained form the parameters list fo this function
					'memberId' => $memberId,
					'cardNo' => $cardNo,
					'points' => $points_to_add,
					'reason' => $reason
				)
			);

		//default the $ws_result and return
		}else{
			$ws_result	= new stdClass();
			$ws_result->err_msg = $this->_error_message;
			$ws_result->xml_msg = '';
		}

        //request was successful
        if($ws_result->err_msg === 'OK'){
            //extract the received data from: $ws_result->xml_msg to an associative array
            $return_data = $this->extract_xml_data_to_assoc($ws_result->xml_msg);
            //request to WS was not successful
        }else{
            $return_data->err_msg = '(AdjustPointsBalance): '.$ws_result->err_msg;
            //xml_message in this case is NULL
            $return_data->request_data = $ws_result->xml_msg;
        }

        //Return
        return $return_data;

    }//end of function


	//Used to get all card(s) information associated with this Customer
	function get_member_card_info($memberId){
		//local variable to use
		$return_data = new stdClass();
		$return_data->err_msg = 'OK';
		$return_data->request_data = array();

		//send request to the PL-WS - ONLY if THIS Model is AVAILABLE, i.e. PL-WS Settings have been set up in the "CMS->Settings: Payback Loyalty WS"
		if($this->_is_available && empty($this->_error_message)){
			$ws_result = $this->query_web_service(
				'GetCardInfo',
				array(
					//currently hardcoded as private variables of this Class
					'partnerId' => $this->_partnerId,
					'requestId' => $this->_requestId,
					'storeId' => $this->_storeId,
					//obtained form the parameters list for this function
					'memberId' => $memberId,
				)
			);

		//default the $ws_result and return
		}else{
			$ws_result	= new stdClass();
			$ws_result->err_msg = $this->_error_message;
			$ws_result->xml_msg = '';
		}

		//request was successful
		if($ws_result->err_msg === 'OK'){
			//extract the received data from: $ws_result->xml_msg to an associative array
			$return_data = $this->extract_xml_data_to_assoc($ws_result->xml_msg);
			//request to WS was not successful
		}else{
			$return_data->err_msg = '(GetCardInfo): '.$ws_result->err_msg;
			//xml_message in this case is NULL
			$return_data->request_data = $ws_result->xml_msg;
		}

		//Return
		return $return_data;
	}//end of function


	function request_new_card_number(){
		//local variable to use
		$return_data = new stdClass();
		$return_data->err_msg = 'OK';
		$return_data->request_data = array();

		//send request to the PL-WS - ONLY if THIS Model is AVAILABLE, i.e. PL-WS Settings have been set up in the "CMS->Settings: Payback Loyalty WS"
		if($this->_is_available && empty($this->_error_message)){
			$ws_result = $this->query_web_service(
				'RequestNewCardNumber',
				array(
					//currently hardcoded as private variables of this Class
					'partnerId' => $this->_partnerId,
					'requestId' => $this->_requestId
				)
			);

		//default the $ws_result and return
		}else{
			$ws_result	= new stdClass();
			$ws_result->err_msg = $this->_error_message;
			$ws_result->xml_msg = '';
		}

		//request was successful
		if($ws_result->err_msg === 'OK'){
			//extract the received data from: $ws_result->xml_msg to an associative array
			$return_data = $this->extract_xml_data_to_assoc($ws_result->xml_msg);
			//request to WS was not successful
		}else{
			$return_data->err_msg = '(RequestNewCardNumber): '.$ws_result->err_msg;
			//xml_message in this case is NULL
			$return_data->request_data = $ws_result->xml_msg;
		}

		//Return
		return $return_data;
	}//end of function


	//Will AUTOMATICALLY GENERATE the PASSWORD for the specified Member (Customer) and Username
	function add_member_password($memberId, $username, $password){
		//local variable to use
		$return_data = new stdClass();
		$return_data->err_msg = 'OK';
		$return_data->request_data = array();

		//send request to the PL-WS - ONLY if THIS Model is AVAILABLE, i.e. PL-WS Settings have been set up in the "CMS->Settings: Payback Loyalty WS"
		if($this->_is_available && empty($this->_error_message)){
			$ws_result = $this->query_web_service(
				'AddMemberPassword_1_4',
				array(
					//currently hardcoded as private variables of this Class
					'partnerId' => $this->_partnerId,
					'requestId' => $this->_requestId,
					//obtained form the parameters list for this function
					'memberId'  => $memberId,
					'username'  => $username,
					'password'	=> $password
				)
			);

		//default the $ws_result and return
		}else{
			$ws_result	= new stdClass();
			$ws_result->err_msg = $this->_error_message;
			$ws_result->xml_msg = '';
		}

		//request was successful
		if($ws_result->err_msg === 'OK'){
			//extract the received data from: $ws_result->xml_msg to an associative array
			$return_data = $this->extract_xml_data_to_assoc($ws_result->xml_msg);
			//request to WS was not successful
		}else{
			$return_data->err_msg = '(AddMemberPassword_1_4): '.$ws_result->err_msg;
			//xml_message in this case is NULL
			$return_data->request_data = $ws_result->xml_msg;
		}

		//Return
		return $return_data;
	}//end of function

	function generate_password($pass_length){

		//Default Password length to 8 characters if wrong param was set
		if(empty($pass_length) || !is_integer($pass_length) || $pass_length <= 0){
			$pass_length = 8;
		}

		$pass_alphabet = 'abcdefghijklmnopqrstuvwxyz1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ{}[];:,./<>?_+~!@#';
		$pass_alphabet_length   = strlen($pass_alphabet) - 1;
		$random_password = '';

		//generate the random password with the specified length
		for($i=0; $i < $pass_length; $i++){
			$rand_char_pos = rand(0, $pass_alphabet_length);
			$random_password .= $pass_alphabet[$rand_char_pos];
		}
		//shuffle the generated password
		$random_password = str_shuffle($random_password);

		//return generated password
		return $random_password;
	}//end of function


    /**
     * Private Function - used to send a request to the PaybackLoyalty - Web Service using a POST
     *
     * @param $request_type - String holding the type of Request to be sent to the WS engine
     * @param array $request_data - associative array holding the data to be sent to the WS engine with the corresponding request
     * @return stdClass - Object holding:
     *                      On Success:
     *                       ->xml_message: the PL-WebService return message in an XML format
     *                       ->err_msg: OK
     *                      On Fail:
     *                       ->xml_msg = NULL
     *                       ->er_msg = String: the error message received from the PL-WebService
     */
    private function query_web_service($request_type, array $request_data){
		//Flag used to trigger the Printing of Debug statements for TESTING
		$debug = false;
		/*
		 * USE this variable to set the PL-WS Request name to be debugged
		 * Possible Requests:
		 * - ConvertPointsToCash
		 * - AdjustPointsBalance
		 * - RegisterNewMember_1_3 - NOT SUPPORTED in latest PL version
		 * - GetMemberTransactions - NOT SUPPORTED in latest PL version
		 * - GetTransactionInfo
		 * - SaveMemberDetails_1_3
		 * - GetMemberInfo
		 * - ValidateMemberAccess
		 * - UpdatePassword
		 * - GetCardInfo
		 * - RequestNewCardNumber
		 * - AddMemberPassword_1_4
		 */
		$request_to_debug = 'ValidateMemberAccess';

        $ws_response = new stdClass();
        $ws_response->err_msg = 'OK';
        $ws_response->xml_msg = NULL;

        //1. BUILD THE REQUEST STRING for a GET Request - it has to be in the form: field1=value1&field2=value2...
//        $request_string = '';
//        if(sizeof($request_data) > 0){
//            foreach($request_data as $field=>$field_value) $request_string .= $field.'='.$field_value.'&';
//            //clear the last & character from the request_string
//            $request_string = substr($request_string, 0, -1);
//        }

		//1. BUILD THE REQUEST STRING - for a SOAP Request - in the form: <soap:Envelope>...</soap:Envelope>
		$request_string = View::factory('payback_loyalty_requests/'.strtolower($request_type).'_view', $request_data, true)->set('skip_comments_in_beginning_of_included_view_file',true)->render();


/* TESTING */
if($debug && $request_type == $request_to_debug){
	echo date('c')."\n";
	echo "\n### query_web_service(): \nRequest: ".$request_type."\nThe SOAP Request String is: \n\n";
	echo $request_string."\n\n### END of the SOAP CURL Request.\n\n";
	//exit($request_string."\n\nEXIT before to send the CURL Request.");
}
/* END TESTING */

        //2. Initialize curl->request processor - will send a POST Request to the PL-WS
        $request_processor = curl_init();
        curl_setopt($request_processor, CURLOPT_URL, $this->_ws_url.$request_type);
        /*
         * set a time-limit to execute the CURL request
         * Used to prevent website to hang-out waiting for too long responds from the Web-Service,
         * i.e. if the Web-Service is taking too long to respond
         * return an time-execution limit reached, rather to leaving Website-user to wait UNKNOWN time period to get some response.
         *
         * The Time limit is set to 10000 ms = 10 seconds
         */
        curl_setopt($request_processor, CURLOPT_TIMEOUT_MS, 15000);
        curl_setopt($request_processor, CURLOPT_POST, 1);
        curl_setopt($request_processor, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($request_processor, CURLOPT_POSTFIELDS, $request_string);
        curl_setopt($request_processor, CURLOPT_SSL_VERIFYPEER, FALSE); // this line makes it work under https
		curl_setopt(
					$request_processor,
					CURLOPT_HTTPHEADER,
					array(
						'Content-Type: text/xml; charset=utf-8',
						'Content-Length: '.strlen($request_string)
					)
				);
		//2.1 Execute the CURL Request
        $ws_response->xml_msg = curl_exec($request_processor);

/* TESTING */
if($debug && $request_type == $request_to_debug) exit("\n\n### START of the PL-WS SOAP XML Response:\n\n".$ws_response->xml_msg."\n\n### END of SOAP XML Response\n");
/* END TESTING */

		//2.2 If the request time-limit has expired
		if($ws_response->xml_msg === FALSE){
			$error_msg = curl_error($request_processor);
			$ws_response->err_msg = '<strong>System Notice:</strong> The web-service takes too long to respond.<br />Please try again later.'.
									(!empty($error_msg) && (Kohana::$environment == Kohana::TESTING OR Kohana::$environment == Kohana::DEVELOPMENT))? '<br />Curl error: ' .$error_msg : '';
			//return - skip steps 3. & 4.
			return $ws_response;
		}
        //2.3 close curl->request processor
        curl_close($request_processor);

        //3. If there was a problem with the request - WS engine will return an error message (a normal STRING - not an XML formatted string)
        if($ws_response->xml_msg !== FALSE && strpos($ws_response->xml_msg, '?xml ', 0) === FALSE){
            $ws_response->err_msg = $ws_response->xml_msg;
        }//else - if request was OK, the returned result will be: ->err_msg = 'OK' and ->xml_msg = 'the PL-WS response in an XML format';

        //4. Return the message received from the Payback Loyalty Web Service
        return $ws_response;
    }//end of function


    /**
     * Private function used to convert an XML string to an associative array.
     *
     * @param $xml_input - String in XML format to be processed
     * @return stdClass - Holds two variables:
     *                    1. ->err_msg: if there was a problem with passed for processing XML-String a corresponding error message will be returned
     *                                  Default value: OK
     *                    2. ->request_data: on success will hold an associative array of the data passed with the XML string
     *                                       Default value: NULL
     */
    private function extract_xml_data_to_assoc($xml_input){

        //variables to use
        $result = new stdClass();
        $result->err_msg = 'OK';
        $result->request_data = NULL;

//echo "\n### XML Response:\n\n".$xml_input."\n\n### END of XML Response\n\n";

        //check if the passed string has some XML stuff within
        if(strpos($xml_input, '?xml ', 0) !== FALSE){

            //1. set the XML processing errors reporting
            libxml_use_internal_errors(TRUE);

            //2. load the passed string as an XMLObject
            $xml_object_to_process = simplexml_load_string($xml_input);

            //3. check if there were some error with the loading of the $xml_input loading as SimpleXMLElement Object
            $xml_errors = libxml_get_errors();

            //there was an error(s) creating it from a string
            if(sizeof($xml_errors) > 0){
                //clear the error_message
                $result->err_msg = '';
                //re-build the new error_message
                foreach ($xml_errors as $key=>$xml_error)
                    $result->err_msg .= ($key+1).'. '.
                        trim($xml_error->code).':<br />'.
                        trim($xml_error->message).
                        '<br />line: '.$xml_error->line.
                        '<br />column: '.$xml_error->column;

            }else{

//echo "\nThe XML-Object to process is:\n";
//print_r($xml_object_to_process);

                //4. get the //soap:Fault and //NewDataSet part of the XML: will be array of SimpleXMLElement Objects or FALSE
                $xml_object_to_process->registerXPathNamespace('soap', 'http://www.w3.org/2003/05/soap-envelope');
				$xml_response_fault = $xml_object_to_process->xpath('//soap:Fault'); //soap:Fault
				$xml_result_data = $xml_object_to_process->xpath('//NewDataSet'); //NewDataSet

//echo "\nThe body of Response is: ";
//echo "\nThe body of Fault is: ";
//print_r($xml_response_fault);
//echo "\nThe body of Data is: ";
//print_r($xml_result_data);
//echo "\n";
//echo "\n### Start of SOAP XML converted String###\n";
//echo "\nFault: \n";
//print_r(json_decode(json_encode($xml_response_fault)));
//echo "\nData: \n";
//print_r(json_decode(json_encode($xml_result_data)));
//echo "\n### END of SOAP XML converted String###\n";

				//4.1 Check the Fault (Error Returned MSG from the PL-WS), if there is such
				if($xml_response_fault){ //There was an issue with the Sent SOAP Request DATA
					$fault_result = json_encode($xml_response_fault);
					$xml_mixed_fault_data = json_decode($fault_result);
					$xml_fault_result_array = $this->mixed_array_to_assoc($xml_mixed_fault_data);

//echo "\n### Start Fault Data\n";
//print_r($xml_fault_result_array);
//echo "\n### END Fault Data\n";

					//Set the Error Message
					$result->err_msg = '<strong>System Notice:</strong><br />'.
										((Kohana::$environment != Kohana::PRODUCTION)?
												'<em>Fault Code:</em> '.$xml_fault_result_array['faultcode'].'<br />'.'<em>Message:</em> '.$xml_fault_result_array['faultstring'] :
												'There was a problem with the Request to Rewards Club - 3rd Party Web Service.'
										);

				//The Request was OK, proceed as usual
				}else{
					//4.2 the xml_string was successfully loaded as an XMLObject
					if($xml_result_data){ //convert it to array
						//4.2.1 convert the $xml_result_data into associative array
						//converts SimpleXMLObject to assoc_array and its children: SimpleXMLElement to just Objects
						$temp_result = json_encode($xml_result_data);
						$xml_mixed_result_data = json_decode($temp_result);

//echo "\n### Collection to Convert: \n";
//print_r($xml_mixed_result_data);

						//4.2.2 convert the $xml_mixed_result_data into associative array
						$xml_result_array = $this->mixed_array_to_assoc($xml_mixed_result_data);

//echo "\n### Converted Collection: \n";
//print_r($xml_result_array);
//echo "\n### NOW PROCESS EXTRACTED DATA ###\n";

						/*
						 * For All Request the $xml_result_array is in the form:
						 * $xml_result_array = 'OK' or the error message received from the Web-Service
						 * $xml_result_array = array(
						 *                           'table' : HOLDS_THE_RECEIVED_FROM_THE_WS-DATA
						 *                           'table1' : HOLDS_THE_RECEIVED_FROM_THE_WS_RESPONSE
						 *                           )
						 *
						 * In the Case of: RequestNewCardNumber, the $xml_result_array is in the form
						 * $xml_result_array = array(
						 *                           'table1' : HOLDS_THE_RECEIVED_FROM_THE_WS-DATA
						 *                           'table2' : HOLDS_THE_RECEIVED_FROM_THE_WS_RESPONSE
						 *                           )
						 *
						 * @TODO: NOTE: This Request is NO LONGER Used - if this check and everything related to: RegisterNewMember_1_3 IS STILL HERE AFTER 01 JULY 2013 -=> REMOVE IT
						 * In the Case of: RegisterNewMember_1_3 the $xml_result_array is in the form:
						 * $xml_result_array = array(
						 * 							'0' => array(
						 * 											'memberid' => THE_GENERATED_USER_MEMBER_ID
						 * 											'username' => SAME_AS_MEMBERID
						 * 											'password' => THE_GENERATED_PASSWORD_FOR_THIS_USERNAME
						 * 										)
						 * 							'1' => array(
						 * 											'success' => TRUE_OR_FALSE
						 * 										)
						 */
						//check if the request was valid
						if(
							//most request wil have this type of response for success/fail
							(
								(isset($xml_result_array['table1']['success']) && strtolower($xml_result_array['table1']['success']) === 'true' ) &&
								(isset($xml_result_array['table']) && count($xml_result_array['table']) >= 1)
							) ||
							//this response Structure is coming from the RequestNewCardNumber Request: table1 - holds the response Data and table2 - holds the SUCCESS/FAIL details
							(
								(isset($xml_result_array['table2']['success']) && strtolower($xml_result_array['table2']['success']) === 'true') &&
								(isset($xml_result_array['table1']) && count($xml_result_array['table1']) >= 1)
							) ||
							/*
							 * @TODO: NOTE: This Request is NO LONGER Used - if this check and everything related to: RegisterNewMember_1_3 IS STILL HERE AFTER 01 JULY 2013 -=> REMOVE IT
							 * this one is received for the RegisterNewMember_1_3 response -
							 * - $xml_result_array['table1'][0] will hold: memberId, Username and Password
							 * - $xml_result_array['table1'][1] will hold: success TRUE/FALSE + message (maybe?)
							 */
							(
								(isset($xml_result_array['table1'][1]['success']) && strtolower($xml_result_array['table1'][1]['success']) === 'true' ) &&
								(isset($xml_result_array['table1'][0]) && count($xml_result_array['table1'][0]) >= 1)
							)
						){

							/*
							 * Set the Result->request_data taken from Table1
							 * -=> because of the: RequestNewCardNumber Request:
							 * 		table1 - holds the response Data and
							 * 		table2 - holds the SUCCESS/FAIL details
							 */
							if(isset($xml_result_array['table2'])){
								if(count($xml_result_array['table1']) >= 1){
									$result->request_data = $xml_result_array['table1'];
								}
							//Set the Result->request_data as USUAL: table - holds the Response data, table1 - holds the SUCCESS/FAIL details
							}else{
								if(isset($xml_result_array['table']) && count($xml_result_array['table']) >= 1){
									$result->request_data = $xml_result_array['table'];
								}
								if(isset($xml_result_array['table1'][0]) && count($xml_result_array['table1'][0]) >= 1){
									$result->request_data = $xml_result_array['table1'][0];
								}
							}

						//there was a problem with the request - set the WS-error response
						}else if(
							//most request wil have this type of response for success/fail
							(isset($xml_result_array['table1']['success']) && strtolower($xml_result_array['table1']['success']) === 'false') ||
							//request: RequestNewCardNumber has this type of response for success/fail
							(isset($xml_result_array['table2']['success']) && strtolower($xml_result_array['table2']['success']) === 'false') ||
							(isset($xml_result_array['table1'][1]['success']) && strtolower($xml_result_array['table1'][1]['success']) === 'false')
						){
							if(isset($xml_result_array['table1']['message'])){
								$result->err_msg = '<strong>System Notice:</strong> '.$xml_result_array['table1']['message'];
							}
							if(isset($xml_result_array['table2']['message'])){
								$result->err_msg = '<strong>System Notice:</strong> '.$xml_result_array['table2']['message'];
							}
							if(isset($xml_result_array['table1'][1]['message'])){
								$result->err_msg = '<strong>System Notice:</strong> '.$xml_result_array['table1'][1]['message'];
							}
						}

					//there was no such '//NewDataSet' in the received XML response
					}else{
						//set error msg
						$result->err_msg = '<strong>System Notice:</strong> The received Web Service XML-Response had no data to be retrieved!';
					}
				}//end of Processing PL-WS Response

            }//end of processing $xml_input

            //clear errors
            libxml_clear_errors();
            //unset the XML processing errors reporting
            libxml_use_internal_errors(FALSE);

        //the passed xml_string is not XML formatted
        }else{
            $result->err_msg = '<strong>System Notice:</strong> The passed string is not a valid XML formatted string!';
        }

//echo "\nResult before EXIT:\n";
//print_r($result);
//exit("\n\nEXIT before Return");

        //return
        return $result;
    }//end of function


    /**
     * Private RECURSIVE function used to convert a collection (array) of Objects,
     * or Object containing various variables: arrays, objects or basic variables: strings and integers
     * into associative array.
     *
     * @param $collection_to_convert - Array / Object to be converted to associative array
     * @return array - an associative array structure corresponding to the passed $collection_to_convert
     */
    private function mixed_array_to_assoc($collection_to_convert){
        //private variables to use
        $array_to_return = array();

        //loop through each of the elements of the passed Array/Object
        foreach($collection_to_convert as $key=>$item){
            //item is an Object or array - get to its leafs/elements by calling this function recursively
            if($item instanceof stdClass || is_array($item)){
                //if the item has more than 1 elements => return it as an array, otherwise set it EMPTY_STRING
                $array_to_return[strtolower($key)] = (count((array)$item) > 0)? $this->mixed_array_to_assoc($item) : '';

            //this item is an object->leaf (item) or an array item add it to the array to be returned
            }else{
				//Strip whitespace from the beginning and end of a string
                $array_to_return[strtolower($key)] = trim($item);
            }
        }

        /*
         * if the array was in the form: array(array_result_data()) - trim it so it will be just: array_data()
         * NOTE: Only for the outer-most array
         */
        if(array_key_exists(0, $array_to_return) && count($array_to_return) === 1) $array_to_return = $array_to_return[0];

        //return
        return $array_to_return;
    }//end of function


    /**
     * Private Function used to initiate the PL-Customer in a SESSION-object variable
     *
     * @param $u_name - String holding the Validated PL-Customer username
     * @param $u_pass - String holding user's password
     * @param $u_data - Array holding the validated PL-Customer data, received by the validate_member() function
     *
     * @return nothing
     */
    final public function initiate_pl_customer($u_name, $u_pass, $u_data){

        if(!isset($_SESSION['pl_user'])){

            //1. Set this pl_user data to be added to the $_SESSION['pl_user'] object
            $pl_user_uname_pass = array('username' => $u_name, 'password' => $u_pass);
            $pl_user_data = array_merge($pl_user_uname_pass, $u_data);

            //2. Get the rest of this member information like:
            //ContactByPartners, ContactForResearch, ContactBySMS, DateOfBirth, LoyaltyBalance, SpendBalance
            $pl_user_info = $this->get_member_info($pl_user_data['memberid'], 'true');

            //add new data to this pl_user data
            if($pl_user_info->err_msg === 'OK' && $pl_user_info->request_data !== NULL){
                /*
                 * this will add the obtained: ContactByPartners, ContactForResearch, ContactBySMS etc.
                 * and will update the already obtained: memberId, Firstname, Surname etc.
                 * in the $pl_user_data, before to add to the Object: $_SESSION['pl_user']
                 */
                $pl_user_data = array_merge($pl_user_data, $pl_user_info->request_data);
            }//else these are not going to be added to this pl_user - default values will be used

            //3. Get this User's Card Details
            $pl_user_card_info = $this->get_member_card_info($pl_user_data['memberid']);

            //add new data to this pl_user data
            if($pl_user_card_info->err_msg === 'OK' && $pl_user_card_info->request_data !== NULL){
                //this will add the obtained: Account - Card(s) details to the $pl_user_data, before to add to the Object: $_SESSION['pl_user']
                if(
                    is_array($pl_user_card_info->request_data) &&
                    count($pl_user_card_info->request_data) > 0 &&
                    is_array(reset($pl_user_card_info->request_data))
                ){
                    /*
                     * This Account, holds multiple Cards -=> set the as:
                     * Array
                     *	(
                     *		 [CARD_NUMBER] => Array
                     *			 (
                     *				 [devicereference] => CARD_NUMBER
                     *				 [loyaltybalance] => LOYALTY_BALANCE - points: 123.4500
                     *				 [spendbalance] => SPEND_BALANCE - euros: 12.3400
                     *				 [devicestatus] => DEVICE_STATUS - DEVICE ACTIVE or DEVICE BLOCKED
                     *			 )
                     * 		[CARD_NUMBER_2] => Array
                     *			 (
                     *				 [devicereference] => CARD_NUMBER_2
                     *				 [loyaltybalance] => LOYALTY_BALANCE_2
                     *				 [spendbalance] => SPEND_BALANCE_2
                     *				 [devicestatus] => DEVICE_STATUS_2
                     *			 )
                     *	)
                     */
                    foreach($pl_user_card_info->request_data as $account_card){
                        //clear the white spaces on the Card Number - for some reason it gets some white spaces around
                        $account_card['devicereference'] = trim($account_card['devicereference']);
                        $pl_user_data['account_cards'][$account_card['devicereference']] = $account_card;
                    }
                }else{
                    /*
                     * This account holds ONLY 1 Card -=> set it as:
                     * Array
                     *	(
                     *		 [CARD_NUMBER] => Array
                     *			 (
                     *				 [devicereference] => CARD_NUMBER
                     *				 [loyaltybalance] => LOYALTY_BALANCE - points: 123.4500
                     *				 [spendbalance] => SPEND_BALANCE - euros: 12.3400
                     *				 [devicestatus] => DEVICE_STATUS - DEVICE ACTIVE or DEVICE BLOCKED
                     *			 )
                     *	)
                     */
                    $pl_user_card_info->request_data['devicereference'] = trim($pl_user_card_info->request_data['devicereference']);
                    $pl_user_data['account_cards'][$pl_user_card_info->request_data['devicereference']] = $pl_user_card_info->request_data;
                }
                //else Card Details Request has failed or this Account does not have any cards assigned
            }else{
                $pl_user_data['account_cards'] = NULL;
            }

            //4. Set the Default Current Card on this Account Card
            //Default this Customer Current Card to: EMPTY_STRING
            $pl_user_data['account_card_in_use'] = '';
            if(!is_null($pl_user_data['account_cards']) && is_array($pl_user_data['account_cards'])){
                //This Account holds ONLY 1 Card
                if(count($pl_user_data['account_cards']) == 1){
                    $tmp_card = current($pl_user_data['account_cards']);
                    $pl_user_data['account_card_in_use'] = $tmp_card['devicereference'];
                    //This Account holds MULTIPLE Cards
                }else{
                    /*
                     * There is a chance the $pl_user_data['memberid'] is SAME as this Account Default Card
                     * -=> set it as account_card_in_use
                     * - otherwise pick the First - ACTIVE Card for this Account
                     */
                    if(array_key_exists($pl_user_data['memberid'], $pl_user_data['account_cards'])){
                        $pl_user_data['account_card_in_use'] = $pl_user_data['account_cards'][$pl_user_data['memberid']]['devicereference'];
                    }else{
                        //Default account_card_in_use to the First "DEVICE ACTIVE" Card for this Account. Blocked Cards have status: DEVICE BLOCKED
                        foreach($pl_user_data['account_cards'] as $acc_card){
                            if($acc_card['devicestatus'] == 'DEVICE ACTIVE'){
                                $pl_user_data['account_card_in_use'] = $acc_card['devicereference'];
                                //Exit the for loop as we got our First Active Card for this Account
                                break;
                            }
                        }
                    }
                }
            }//else the account_card_in_use is already set to EMPTY_STRING

            //5. Set the pl_user_data as an Object Session-Variable
            $_SESSION['pl_user'] = (object) $pl_user_data;

            //6. Get the Transaction Information for Current User for the last 7 days (by default)
            $fromDate = date('c', strtotime('-1 week')); //Set the Transactions History Default From Date to start from the last 7 days
            $toDate = date('c'); //Set the End of Default Period to today
            $maxNo = 20;
            $u_transactions_info = $this->get_transaction_info($pl_user_data['memberid'], $fromDate, $toDate, $maxNo);
            $_SESSION['pl_user']->transactions_info = array(
                'date_from' => $fromDate,
                'date_to' => $toDate,
                'max_records' => $maxNo,
                'transactions_details' =>  (
                ($u_transactions_info->err_msg === 'OK' && $u_transactions_info->request_data !== NULL)?
                    $u_transactions_info->request_data : $u_transactions_info->err_msg
                )
            );
        }//else: do nothing - PL-Customer has already been logged in
    }//end of function

    /**
     * Reload session values, for example after successful payment for display the earned points
     *
     * @return bool
     */
    final public function reload_session(){
        $session = Session::instance();

        $username = $_SESSION['pl_user']['username'];
        $password = $_SESSION['pl_user']['password'];

        $session->delete('pl_user');

        $ws_request_result = $this->validate_member($username, $password);

        if($ws_request_result->err_msg === 'OK'){

            $this->initiate_pl_customer($username, $password, $ws_request_result->request_data);

            $pl_user = (array) $_SESSION['pl_user'];

            //Add last transaction date in to the session variable
            $pl_user['last_transaction'] = (Array) $this->get_transaction_info($_SESSION['pl_user']->memberid, '2000-07-02T15:44:54+01:00', date('c'), 1);

            $session->set('pl_user', $pl_user);

            return true;
        }
        else{
            IbHelpers::set_message($ws_request_result->err_msg, 'error');

            return false;
        }

    }

}//end of class


?>