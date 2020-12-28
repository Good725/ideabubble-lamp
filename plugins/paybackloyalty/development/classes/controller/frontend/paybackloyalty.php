<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Controller_Frontend_Paybackloyalty extends Controller
{

    /**
     * Function Used to validate a Payback Loyalty - User
     * for the Payback Loyalty - Members Area login-form.
     * Can also be called internally (by $this->save_account_details())
     *
     *
     * @param null $user_name
     * @param null $user_pass
     * @param bool $internal_call
     * @return mixed
     */
    function action_user_login($user_name=NULL, $user_pass=NULL, $internal_call=FALSE){

        $pl_model = new Model_PaybackLoyalty();

        //local variables to use
        $respond = array('err_msg' => 'OK', 'respond_view' => '');

        //get the passed or $_POST-ed data
        $u_name = ($user_name !== NULL)? $user_name : $this->request->post('pl_username');
        $pass = ($user_pass !== NULL)? $user_pass :  $this->request->post('pl_password');

        //validate the user using the corresponding model->function
        $ws_request_result = $pl_model->validate_member($u_name, $pass);

        //check if the request was OK
        if($ws_request_result->err_msg === 'OK'){

            //Create this PL-Customer
            $pl_model->initiate_pl_customer($u_name, $pass, $ws_request_result->request_data);

            if($internal_call) return $ws_request_result->err_msg;

            /*
             * just load the PL-Customer account_editor view - once the SESSION['pl_user'] - is set up
             * The view will be automatically populated by the set-up SESSION['pl_user'] data - otherwise it will return the member_area_login_view
             */
            //$respond['respond_view'] = $this->load->view('pl_user_acc_editor_view', '', TRUE);
            //there was a problem with the request

            $pl_user = (array) $_SESSION['pl_user'];

            //Add last transaction date in to the session variable
            $pl_user['last_transaction'] = (Array) $pl_model->get_transaction_info($_SESSION['pl_user']->memberid, '2000-07-02T15:44:54+01:00', date('c'), 1);

            $session = Session::instance();
            $session->set('pl_user', $pl_user);

            /*
            $referer = $this->request->post('referer');
            if(empty($referer)){
                $referer = '/';
            }*/

            $referer = '/members-area.html';

        }else{
            //Set the return error message
            $respond['err_msg'] = $ws_request_result->err_msg;

            IbHelpers::set_message($respond['err_msg'], 'error');

            $referer = 'login.html';
        }

        //return
        //echo json_encode($respond);

        $this->request->redirect($referer);

    }//end of function

    function action_user_logout($user_name=NULL, $user_pass=NULL, $internal_call=FALSE){
        $session = Session::instance();
        $session->delete('pl_user');

        $this->request->redirect('/');
    }

    /*
 * Function used to convert this PL-Customer points to cash
 *
 * NOTE: This function will convert the points "internally".
 *       Once the purchase is done successfully - the payment Processor will call
 *       the Payback_loyalty_wrapper model to make the actual PL-Customer points conversion and PL Account points update
 */
    function action_convert_points_to_cash(){

        $session = Session::instance();

        $respond = array(
            'err_msg' => 'OK',
            'points_left' => 0,
            'left_pts_euro' => 0,
            'left_pts_cents' => 0,
            'cashed_points' => 0,
            'cashed_euros' => 0,
            'cashed_cents' => 0
        );

        $cart = $_SESSION[Model_Checkout::CART_SESSION_ID];

        /*
         * this is to be done INTERNALLY, i.e. in our system
         * ACTUAL conversion etc. will be done ONLY on successful payment
         */
        if(isset($_SESSION['pl_user'])){
            /*
             * These are the current pl_user variables which hold points:
             * spendbalance - this is a converted Points to Cash, which can be used as a Cash equivalent
             * loyaltybalance - this is the points-balance on this customer's loyalty card
             * memberpointbalance - ths is total of all customer's loyalty cards
             *
             * THIS is the one we need: loyaltybalance
             */

            /*
             * 1. We need to know the cart total so we don't convert ALL points into cash
             *    CART-total is in cents
             */
            if(isset($cart) && $cart->number_of_items > 0){

                //If there is Available Cards to Use
                if(
                    !is_null($_SESSION['pl_user']['account_cards']) &&
                    is_array($_SESSION['pl_user']['account_cards']) &&
                    count($_SESSION['pl_user']['account_cards']) > 0
                ){
                    //If there is a Selected Card to Use
                    if($_SESSION['pl_user']['account_cards'] != ''){

                        //If the Selected Card Exists and is DEVICE ACTIVE. Blocked Cards have status: DEVICE BLOCKED
                        if(
                            array_key_exists($_SESSION['pl_user']['account_card_in_use'], $_SESSION['pl_user']['account_cards']) &&
                            $_SESSION['pl_user']['account_cards'][$_SESSION['pl_user']['account_card_in_use']]['devicestatus'] == 'DEVICE ACTIVE'
                        ){

                            /*
                             * If points have already been converted - return a system message
                             * or the converted points cover whatever is the Cart-total
                             */
                            if(
                                (
                                    isset($_SESSION['pl_user']['pl_points_to_convert']) &&
                                    $_SESSION['pl_user']['pl_points_to_convert'] > 0
                                ) ||
                                (
                                    isset($_SESSION['pl_user']['pl_points_to_convert']) &&
                                    $_SESSION['pl_user']['pl_points_to_convert'] > 0 /*&&
                                    $_SESSION[$cart_type.'Details']->total = $_SESSION['pl_user']->pl_points_to_convert*/
                                )
                            ){
                                //its either: all points have been converted, or just the points which cover this Cart-total purchase have been converted
                                $respond['err_msg'] = '<p><strong>System message:</strong> All available Loyalty points on Loyalty Card Number: '.
                                    $_SESSION['pl_user']['account_card_in_use'] .
                                    ' have already been cashed for this purchase .</p>';

                                //convert the points etc. as normal
                            }else{
                                /*
                                 * 1.1 calculate the amount of available points to convert and take the CART-total
                                 *    NOTE: 1 point is managed by the PL-Web Service as: 1.0000
                                 *    we need just the 1.00 part, i.e.
                                 *      if there is 127.9800 points available to be converted to cash
                                 *      we need ONLY the 127 part as these are in fact 127 cents = 1.27 euros
                                 */
                                $pl_points = number_format(
                                    floor($_SESSION['pl_user']['account_cards'][$_SESSION['pl_user']['account_card_in_use']]['loyaltybalance']),
                                    0,
                                    '.',
                                    ''
                                );
                                //postage is not included in the cart total.
                                $cart_total = $cart->cart_price;

                                /*
                                 * 1.2 calculate how many points we need to take of the total PL-points
                                 *    In this case:
                                 *     - if the PL-Points, which this customer has and can be converted to cash
                                 *       are more than the CART->total => Customer should be able to pay with the LOYALTY-POINTS,
                                 *       i.e. if the CART-total is 2.00 euros and PL-points are equivalent to 3.00 euros,
                                 *       customer should not be asked to pay with any card (bypass Realex processing)
                                 *     - if the PL-points are less than the CART-total, convert ONLY the ones which are available to be converted to cash
                                 */
                                if((int)$pl_points > 0 AND ((float)$cart_total <  ((float)$pl_points / 100))){
                                    $points_to_convert = $cart_total * 100;
                                }
                                else{
                                    $points_to_convert = $pl_points;
                                }


                                /*
                                 * 1.3 Update this PL_USER->loyaltybalance
                                 *    NOTE: this is done ONLY IN OUR SYSTEM,
                                 *          ONCE the Customer purchase is completed,
                                 *           these will be updated properly by using the PL-Web Service functions.
                                 *          If the Customer purchase is cancelled by customer, or the payment was unsuccessful
                                 *          these changes will be discarded and will not affect the Customer - Actual PL-Account
                                 */
                                $_SESSION['pl_user']['pl_points_to_convert'] = $points_to_convert;

                                // 1.4 Prepare the data to be returned to Checkout view
                                $respond['cashed_points'] = $points_to_convert;
                                $respond['cashed_euros'] = number_format(floor($points_to_convert/100), 2, '.', '');
                                $respond['cashed_cents'] = (($points_to_convert/100) - $respond['cashed_euros']) * 100;
                                if($points_to_convert > 0){
                                    $respond['cashed_total'] = $points_to_convert/100;
                                }
                                else{
                                    $respond['cashed_total'] = 0;
                                }
                                $respond['points_left'] = number_format(
                                    ($_SESSION['pl_user']['account_cards'][$_SESSION['pl_user']['account_card_in_use']]['loyaltybalance'] - $points_to_convert),
                                    3,
                                    '.',
                                    ''
                                );
                                $respond['left_pts_euro'] = number_format(floor($respond['points_left']/100), 2, '.', '');
                                $respond['left_pts_cents'] = floor((($respond['points_left']/100) - $respond['left_pts_euro']) * 100);

                                //UPDATE CART, SET PL DISCOUNT
                                $checkout_model = new Model_Checkout();
                                $checkout_model->add_payback_loyalty_discount((int)$points_to_convert);

                            }//end of converting points

                            //The Selected PL Card is NOT a VALID Card or is DEACTIVATED
                        }else{
                            $respond['err_msg'] = '<p><strong>System message:</strong> Sorry, the selected Loyalty Card Number: '.$_SESSION['pl_user']['account_card_in_use'].
                                ' is NOT a VALID Loyalty Card, or it has been DEACTIVATED.</p>';
                        }

                        //There NOT Selected A PL Card for this ONLINE Purchase
                    }else{
                        $respond['err_msg'] = '<p><strong>System message:</strong> Please Select a Loyalty Card to be used.</p>';
                    }

                    //There is NO PL Cards under this Account, which can be used for this ONLINE Purchase
                }else{
                    $respond['err_msg'] = '<p><strong>System message:</strong> Sorry there is NOT Available Loyalty Card to be used.</p>';
                }

                //there must be at least 1 item in the cart, i.e. cart-total MUST be more than 0 euros
            }else{
                $respond['err_msg'] = '<p><strong>System message:</strong> Your shopping cart cannot be empty.</p>';
            }

            //PL-Customer has not logged in yet - prompt to log in first
        }else $respond['err_msg'] = '<p><strong>System message:</strong> You have to be logged in the Payback Loyalty system, before to use this functionality.</p>';

        //return
        echo json_encode($respond);
    }//end of function



    //Used to clear the "internally" converted Payback Loyalty points back to normal
    function action_revert_converted_points(){

        $session = Session::instance();

        $respond = array(
            'err_msg' => 'OK',
            'returned_points' => 0,
            'returned_euros' => 0,
            'returned_cents' => 0,
            'updated_points' => 0,
            'updated_pts_euro' => 0,
            'updated_pts_cents' => 0
        );

        /*
         * this is to be done INTERNALLY, i.e. in our system
         * ACTUAL conversion etc. will be done ONLY on successful payment
         */
        if(isset($_SESSION['pl_user'])){

            if(isset($_SESSION['pl_user']['pl_points_to_convert']) && $_SESSION['pl_user']['pl_points_to_convert'] > 0){
                $internally_converted_points = $_SESSION['pl_user']['pl_points_to_convert'];

                // Prepare the data to be returned to Checkout view
                $respond['returned_points'] = $internally_converted_points;
                $respond['returned_euros'] = number_format(floor($internally_converted_points/100), 2, '.', '');
                $respond['returned_cents'] = $internally_converted_points - ($respond['returned_euros'] * 100);
                $respond['returned_total'] = $internally_converted_points/100;
                //put the loyalty points back to normal
                $respond['updated_points'] = number_format(
                    $_SESSION['pl_user']['account_cards'][$_SESSION['pl_user']['account_card_in_use']]['loyaltybalance'],
                    3,
                    '.',
                    ''
                );
                $respond['updated_pts_euro'] = number_format(floor($internally_converted_points/100), 2, '.', '');
                $respond['updated_pts_cents'] = $internally_converted_points - ($respond['updated_pts_euro'] * 100);

                //unset the converted points
                unset($_SESSION['pl_user']['pl_points_to_convert']);

                //UPDATE CART, SET PL DISCOUNT
                $checkout_model = new Model_Checkout();
                $checkout_model->remove_payback_loyalty_discount();

                //there is no "internally" converted points
            }else{
                $respond['err_msg'] = '<p><strong>System message:</strong> There is no "converted" points to be reverted.</p>';
            }


            //PL-Customer has not logged in yet - prompt to log in first
        }else $respond['err_msg'] = '<p><strong>System message:</strong> You have to be logged in the Payback Loyalty system, before to use this functionality.</p>';

        //return
        echo json_encode($respond);
    }//end of function


    function action_register_new_member(){

        //local variables to use
        $respond 							= array('err_msg' => 'OK', 'respond_view' => '', 'respond_message' => '');
        $ws_request_result 					= new stdClass();
        $ws_get_new_card_request_result 	= new stdClass();
        $ws_get_member_info_request_result 	= new stdClass();
        $ws_save_member_info_request_result	= new stdClass();
        $ws_add_member_pass_request_result	= new stdClass();
        $proceed_to_step_2 					= TRUE;
        $proceed_to_step_3 					= TRUE;
        $proceed_to_step_4 					= TRUE;
        $proceed_to_step_5 					= TRUE;
        $pl_model                           = new Model_PaybackLoyalty();

        //get the $_POST data
        //take the whole POST-ed data - filtered trough the CodeIgniter Filter Functionality
        $post_data = $this->request->post();

        /*
         * ####################################
         * STEP 1. RequestNewCardNumber - used ONLY for registering of BRAND NEW MEMBERS, i.e. there is no Card Tag (Number) provided in the Registration Form
         *	       1.1 For existent Members, i.e. who provide the Card Number -=> this step will be skipped
         * 				-=> DEFAULT $ws_get_new_card_request_result structure will be set instead
         * ####################################
         */
        if(isset($post_data['pl_card_number']) AND $post_data['pl_card_number'] == '0'){ //if we did not receive a card no in the registration

            //Request a Card Number for This Customer
            $ws_get_new_card_request_result = $pl_model->request_new_card_number();

            //check if there was a problem with the request
            if($ws_get_new_card_request_result->err_msg !== 'OK'){
                $ws_request_result->err_msg = 'Online Registration - STEP 1 Error:<br />'.$ws_get_new_card_request_result->err_msg;
				$ws_request_result->request_data = NULL;
				$proceed_to_step_2	= FALSE;

                //else Request was OK -=> PROCEED to STEP 2
            }else{
                $proceed_to_step_2	= TRUE;
            }

            //else Customer has provided a Card Number -=> Default the $ws_get_new_card_request_result values, SKIP STEP 2 and PROCEED to Step 3
        }else{
            //default the $ws_get_new_card_request_result, when the Card Number is provided
            $ws_get_new_card_request_result->request_data['cardno'] = $post_data['pl_card_number'];
            $ws_get_new_card_request_result->err_msg = 'OK';
            $proceed_to_step_2	= TRUE;
        }//END of STEP 1


        /*
         * ####################################
         * STEP 2. GetMemberInfo - to get the just_requested / entered by customer Card - Member details
         *	       2.1 For Existent Customer - At this point we might use some validation Rules to check if the passed in the Form Customer Details
         *		       and the returned by the PL-WS Member Details for the specified Card Number
         *	       2.2 For BRAND NEW Customers -=> get the corresponding memberId for the JUST Obtained NEW Card Number
         * ####################################
         */
        if($proceed_to_step_2){

            /*
             * Get the Details of the Requested from PL-WS / Provided by Customer - Card Number details
             * We are NOT Sure if the Requested from PL-WS / Provided by Customer Card Number is same as its corresponding memberId
             * -=> we set the isMemberId flag to false
             */
            $ws_get_member_info_request_result = $pl_model->get_member_info(
                $ws_get_new_card_request_result->request_data['cardno'],
                'false' //true => this is a MemberId, false => NOT a MemberId, i.e. just getInfo for the specified CardNumber
            );

            //check if there was a problem with the "get_member_info" request
            if($ws_get_member_info_request_result->err_msg !== 'OK'){
                $ws_request_result->err_msg = 'Online Registration - STEP 2 Error:<br />'.$ws_get_member_info_request_result->err_msg;
                $proceed_to_step_3 = FALSE;
                /*
                 * At this Point we will have the Card Number, Generated by PL-WS or Provided by Customer in STEP 1
                 * 	-=> add Card Number to: $ws_request_result->request_data['cardno'] and $ws_request_result->request_data['username']
                 * for Reporting Purposes
                 */
                $ws_request_result->request_data['cardno'] = $ws_get_new_card_request_result->request_data['cardno'];
                $ws_request_result->request_data['username'] = $ws_get_new_card_request_result->request_data['cardno'];

                //else Request was OK -=> VALIDATE the POST-ed data with the Retrieved ONE, before to: PROCEED to STEP 3 - THIS IS DONE ONLY for Registering of EXISTENT Customers
            }else if($post_data['pl_card_number'] != 0){
                //Validate on forename, surname and emailaddress, in the case the Specified Card Number has assigned emailaddress
                if($ws_get_member_info_request_result->request_data['emailaddress'] != ''){
                    /*
                     * POST-ed data DOES NOT correspond to the specified Card Number-details, i.e.:
                     * Somebody is playing CLEVER, and is trying to Register with someone else's Card or a dummy card -=> spammer?
                     */
                    if(
                        !(
                            $post_data['pl_f_name'] == $ws_get_member_info_request_result->request_data['forename'	] &&
                                $post_data['pl_s_name'] == $ws_get_member_info_request_result->request_data['surname'	  	] &&
                                $post_data['pl_email' ] == $ws_get_member_info_request_result->request_data['emailaddress']
                        )
                    ){
                        $ws_request_result->err_msg = 'Sorry, there is a problem with your registration.<br />'.
                            'The provided details DO NOT match to the specified Card Number: '.
                            $ws_get_new_card_request_result->request_data['cardno'].'<br /><br />'.
                            'Please try again or contact us.';
                        $proceed_to_step_3 = FALSE;

                        //else POST-ed data corresponds to the specified Card Number-details -=> PROCEED to STEP 3
                    }else $proceed_to_step_3 = TRUE;

                    //This Card DID NOT have an email assigned to it -=> validate POST-ed Data with the specified Card details based on: forename, surname and dateofbirth
                }else{
                    /*
                     * POST-ed data DOES NOT correspond to the specified Card Number-details
                     * NOTE:
                     *  - $ws_get_member_info_request_result->request_data['dateofbirth'] is in the form: 1994-11-02T00:00:00+00:00
                     *  - $post_data[$post_form_identifier.'pl_dob' ] is in the form: 01-11-1994
                     *  -=> we need to "normalize" them before to compare
                     */
                    if(
                        !(
                            $post_data['pl_f_name'] 						  == $ws_get_member_info_request_result->request_data['forename'] &&
                                $post_data['pl_s_name'] 						  == $ws_get_member_info_request_result->request_data['surname' ] &&
                                date('d-m-Y', strtotime($post_data['pl_dob' ])) == date('d-m-Y', strtotime($ws_get_member_info_request_result->request_data['dateofbirth']))
                        )
                    ){
                        $ws_request_result->err_msg = 'Sorry, there is a problem with your registration.<br />'.
                            'The provided details DO NOT match to the specified Card Number: '.
                            $ws_get_new_card_request_result->request_data['cardno'].'<br /><br />'.
                            'Please try again or contact us.';
                        $proceed_to_step_3 = FALSE;

                        //else POST-ed data corresponds to the specified Card Number-details -=> PROCEED to STEP 3
                    }else $proceed_to_step_3 = TRUE;

                }//end of Validating the POST-ed Data with the Retrieved from the PL-WS CardNumber-details

                //else Request was OK - we got the Requested from PL-WS Card Details and we DON'T NEED to do any VALIDATION, as this is BRAND NEW CUSTOMER -=> proceed to STEP 3
            }else if($post_data['pl_card_number'] == 0){
                $proceed_to_step_3 = TRUE;
            }//end of checking if there was a problem with the request $ws_get_member_info_request_result

            /*
             * There was some ISSUE in STEP 1 -=> RETURN with the corresponding ERROR-MESSAGE.
             * NOTE error message is set under: $ws_request_result->err_msg in STEP 1
             */
        }else{
            $proceed_to_step_3 = FALSE;
        }//END of STEP 2


        /*
         * ####################################
         * STEP 3. SaveMemberDetails - to update the member's information as per the passed in the form One
         *	 	 3.1 Note there is 4 different Function Calls for this PL-WS
         *		   - SaveMemberDetails_1_2 - Not Include Nationality
         *		   - SaveMemberDetails_1_3 - Include Nationality
         *		   - SaveMemberDetails_1_4 - Include ContactByEmail, carRegNo , fuelHeat , homeHeat , employee , inAppNotification
         *		   - SaveMemberDetails_1_5 - Include Registration Type
         *	 	 3.2 NOT Sure what are all these for, but we are currently using the ONE with the "Include Nationality": SaveMemberDetails_1_3
         * ####################################
         */
        if($proceed_to_step_3){

            /*
             * Save POSTED Data for this Member. NOTE THIS Request RETURNS ONLY: TRUE / (FALSE + error message)
             * NOTE:
             * 		- EXISTING MEMBERS will ALREADY HAVE a memberID
             * 		- NEW MEMBERS Cards should ALSO have their specific memberId, obtained ib the: $ws_get_member_info_request_result
             * 		  IF there was a problem with $ws_get_member_info_request_result
             *        we SHOULD NOT get to this point, i.e. STEP 3 will be DISABLED and System will return with a corresponding "Failed Registration" email to Admin,
             * 		  and whoever is set to receive this email in the CMS->Actions
             */
            $ws_save_member_info_request_result = $pl_model->update_member_details(
                $ws_get_member_info_request_result->request_data['memberid'],
                //username -=> NOT USED but we set the provided/generated Card Number to be a username, just in case. Reason for using Card Number is BECAUSE email CAN BE EDITED
//                $ws_get_new_card_request_result->request_data['cardno'],
//                't3mPpA55wOrD!', //password -=> NOT USED but we will set a temporary one, just in case
                $post_data
            );

            //check if there was a problem with the request
            if($ws_save_member_info_request_result->err_msg !== 'OK'){
                $ws_request_result->err_msg = 'Online Registration - STEP 3 Error:<br />'.$ws_save_member_info_request_result->err_msg;
                $proceed_to_step_4 = FALSE;
                /*
                 * At this Point we will have the Card Number, Generated by PL-WS or Provided by Customer in STEP 1
                 * and Member ID obtained at STEP 2
                 * 	-=> add Card NUmber to: $ws_request_result->request_data['cardno'] and $ws_request_result->request_data['username']
                 *  -=> add memberid to the: $ws_request_result->request_data['memberid']
                 * for Reporting Purposes
                 */
                $ws_request_result->request_data['cardno']		= $ws_get_new_card_request_result->request_data['cardno'];
                $ws_request_result->request_data['username']	= $ws_get_new_card_request_result->request_data['cardno'];
                $ws_request_result->request_data['memberid']	= $ws_get_member_info_request_result->request_data['memberid'];

                //else Request was OK -=> PROCEED to STEP 4
            }else $proceed_to_step_4 = TRUE;

            /*
             * There was some ISSUE in STEPS 1 or 2 -=> RETURN with the corresponding ERROR-MESSAGE.
             * NOTE error message is set under: $ws_request_result->err_msg in either STEP 1 or STEP 2
             */
        }else{
            $proceed_to_step_4 = FALSE;
        }//end of STEP 3


        /*
         * ####################################
         * STEP 4. AddMemberPassword - to set a proper Username and Password for the Member to Register
         *		 4.1 There is 2 different Functions for this one:
         *		   - AddMemberPassword - same as the bellow
         *		   - AddMemberPassword_1_4 - Include partnerId, requestId, memberId, username, password
         * ####################################
         */
        if($proceed_to_step_4){

            //Generate the RANDOM Password for this User
            $generated_password = $pl_model->generate_password(8);

            /*
             * Generate the corresponding Login: username and password for this Customer
             * NOTE:
             * 		- EXISTING MEMBERS will ALREADY HAVE a memberID
             * 		- NEW MEMBERS -=> NEWLY Generated Card Number have their own memberId, obtained at STEP 2
             * 		  IF there was a problem with $ws_get_member_info_request_result
             *        we SHOULD NOT get to this point, i.e. STEPS 3 and 4 will be DISABLED and System will return with a corresponding "Failed Registration" email to Admin,
             * 		  and whoever is set to receive this email in the CMS->Actions
             */
            $ws_add_member_pass_request_result = $pl_model->add_member_password(
                $ws_get_member_info_request_result->request_data['memberid'],
                //set the provided/generated Card Number, to be the username for this Customer (Member). Reason for using Card Number is BECAUSE email CAN BE EDITED
//@TODO: FURTHER DISCUSS AND APPROVE THIS WITH MIKE as there is an OPTION that Card could be CANCELLED -= >what we do in this case?
                $ws_get_new_card_request_result->request_data['cardno'],
                $generated_password
            );

            //check if there was a problem with the "add_member_password" request
            if($ws_add_member_pass_request_result->err_msg !== 'OK'){
                $ws_request_result->err_msg = 'Online Registration - STEP 4 Error:<br />'.$ws_add_member_pass_request_result->err_msg;
//				$proceed_to_step_5 = FALSE;
                /*
                 * At this Point we will have the Card Number, Generated by PL-WS or Provided by Customer in STEP 1
                 * and Member ID obtained at STEP 2,
                 * and $generated_password in THIS Step - STEP 4
                 * 	-=> add Card NUmber to: $ws_request_result->request_data['cardno'] and $ws_request_result->request_data['username']
                 *  -=> add memberid to: $ws_request_result->request_data['memberid']
                 *  -=> ad Generated Random Password to: $ws_request_result->request_data['password']
                 * for Reporting Purposes
                 */
                $ws_request_result->request_data['cardno']		= $ws_get_new_card_request_result->request_data['cardno'];
                $ws_request_result->request_data['username']	= $ws_get_new_card_request_result->request_data['cardno'];
                $ws_request_result->request_data['memberid']	= $ws_get_member_info_request_result->request_data['memberid'];
                $ws_request_result->request_data['password']	= $generated_password;

                //else Request was OK -=> Set the $ws_request_result->request_data and PROCEED to STEP 5
            }else{
                /*
                 * NOTE:
                 * 		- EXISTING MEMBERS will ALREADY HAVE a memberID
                 * 		- NEW MEMBERS -=> will have their NEWLY Generated Card Number set up as their memberId
                 * 		- set the provided/generated Card Number, to be the username for this Customer (Member). Reason for using Card Number is BECAUSE email CAN BE EDITED
                 */
                $ws_request_result->request_data = array(
                    'cardno' => $ws_get_new_card_request_result->request_data['cardno'],
                    'memberid' => $ws_get_member_info_request_result->request_data['memberid'],
                    'username' => $ws_get_new_card_request_result->request_data['cardno'],
                    'password' => $generated_password
                );
//				$proceed_to_step_5 = TRUE;
            }

            /*
             * ELSE: There was some ISSUE in STEPS 1, 2 or 3 -=> RETURN with the corresponding ERROR-MESSAGE.
             * 		 NOTE error message is set under: $ws_request_result->err_msg in either STEP 1, 2 or STEP 3
             */
//		}//else{
//			$proceed_to_step_5 = FALSE;
        }//end of STEP 4 - NOTE: There is NO NEED to DISABLING STEP 5 as the Email Notifications WILL BE SENT depending on whether REGISTRATION has SUCCEEDED or FAILED in one of the above STEPS


        /*
         * ####################################
         * 5. Send the Admin and Customer emails as usual - WE HAVE THIS ALREADY BUILT
         *	  JUST need to update the "Brand New Member" Registration accordingly, as New and Existent members will be able to use their Online PL-WS
         *	  as soon as they get the Confirmation Emails with their Username and Passwords
         *
         *    STEP 5 - Sending of Emails WILL BE EXECUTED in EITHER Way: SUCCESSFUL REGISTRATION or FAILED REGISTRATION
         * 	  Based on SUCCESS / FAILEd Registration different Emails Notifications will be sent
         *
         *    NOTE - THIS STEP is working with the CMS->Actions Feature
         * ####################################
         */
        if($proceed_to_step_5){

            //There was a Problem with the Registration -=> Set corresponding Event for the Failed Registration
            if(isset($ws_request_result->err_msg) && $ws_request_result->err_msg !== 'OK'){

               //Set the return error message, generated in one of the previous steps: STEP 1, 2, 3 or 4

                $respond['err_msg'] = 'Sorry, there was a problem with your registration.<br />'.
                    'Please excuse us for the inconvenience.<br />An Email notification has been sent to Admin and will further action your request.';

                IbHelpers::set_message( $respond['err_msg'], 'error');


				//payback_loyalty_registration_fail

				//Send email
				$mail_data['post_data'] = $post_data;
				$mail_data['request_result'] = isset($ws_request_result->request_data) ? $ws_request_result->request_data : '';
				$mail_data['request_err_msg'] = $ws_request_result->err_msg;
				$mail_html = View::factory('email/payback_loyalty_registration_fail', $mail_data)->set('skip_comments_in_beginning_of_included_view_file', TRUE)->render();

				$event_id = Model_Notifications::get_event_id(Kohana::$config->load('config')->get('failed_payback_new_member'));

				if ($event_id !== FALSE)
				{
					$notifications_model = new Model_Notifications($event_id);
					//Send Emails to the Specified in this Notification ADMIN Emails
					$notifications_model->send($mail_html);
				}

                $this->request->redirect('/loyalty-registration-form.html');
            }else{
                $respond['respond_message'] = (isset($ws_request_result->request_return_msg) && $ws_request_result->request_return_msg !== '')?
                    $ws_request_result->request_return_msg :
                    'Your Registration was successful!<br />'.
                        'An email confirmation with your login details has been sent to the specified by you email address: '. $post_data['pl_email'];

                IbHelpers::set_message($respond['respond_message'], 'success');

                //payback_loyalty_registration_success

                //Send customer and admin email
                $mail_data['post_data'] = $post_data;
                $mail_data['request_result'] = $ws_request_result->request_data;
                $mail_html = View::factory('email/payback_loyalty_registration_confirmation', $mail_data)->set('skip_comments_in_beginning_of_included_view_file', TRUE)->render();

                $event_id = Model_Notifications::get_event_id(Kohana::$config->load('config')->get('successful_payback_new_member'));

                if ($event_id !== FALSE)
                {
                    $notifications_model = new Model_Notifications($event_id);
					//Send Customer Notification
                    $notifications_model->send_to($post_data['pl_email'], $mail_html);
					//Send Admin Notification
                    $notifications_model->send($mail_html); // [GBS-208]
                }

                //send admin email
                $mail_html = View::factory('email/payback_loyalty_registration_success_admin', $mail_data)->set('skip_comments_in_beginning_of_included_view_file', TRUE)->render();

                $event_id = Model_Notifications::get_event_id(Kohana::$config->load('config')->get('successful_payback_new_member_admin'));

                if ($event_id !== FALSE)
                {
                    $notifications_model = new Model_Notifications($event_id);
					//Send Admin Notification Only (no customer email needed here)
                    $notifications_model->send($mail_html);
                }

                $redirect = (isset($post_data['redirect']))? $post_data['redirect'] : '/login.html';
                $this->request->redirect($redirect);
            }//end of defining Registration SUCCESS/FAIL Event

        }//end of STEP 5 - Sending Email Notifications

        //return
        echo json_encode($respond);
    }//end of function

    public function action_update_account_card_in_use(){

        $session = Session::instance();

        $respond = array(
            'err_msg' 				=> 'OK',
            'card_loyalty_points' 	=> 0,
            'card_spend_balance' 	=> 0,
            'card_converted_points' => 0
        );

        //get the Card Number to be set as Active (IN USE) cart_type
        $card_to_use_number = $type=$this->request->post('card_num_to_use');

        //Check if User is Logged In
        if(isset($_SESSION['pl_user'])){
            //Update Card to Use if Specified Card is VALID
            if(
                $card_to_use_number !== FALSE &&
                is_array($_SESSION['pl_user']['account_cards']) &&
                array_key_exists($card_to_use_number, $_SESSION['pl_user']['account_cards'])
            ){
                //Check if the Selected Card is ACTIVE, i.e. can be used and Set it as account_card_in_use
                if($_SESSION['pl_user']['account_cards'][$card_to_use_number]['devicestatus'] == 'DEVICE ACTIVE'){
                    $_SESSION['pl_user']['account_card_in_use'] = $card_to_use_number;
                    $loyalty_points_euro  = number_format(floor($_SESSION['pl_user']['account_cards'][$card_to_use_number]['loyaltybalance']/100), 2, '.', '');
                    $loyalty_points_cents = floor((($_SESSION['pl_user']['account_cards'][$card_to_use_number]['loyaltybalance']/100) - $loyalty_points_euro) * 100)/100;
                    $respond['card_loyalty_points'	] = (float)$loyalty_points_euro + (float)$loyalty_points_cents;
                    $respond['card_spend_balance'	] = number_format($_SESSION['pl_user']['account_cards'][$card_to_use_number]['spendbalance'], 2, '.', '');

                    //Set the Points to Convert
                    if(isset($_SESSION['pl_user']['pl_points_to_convert']) && $_SESSION['pl_user']['pl_points_to_convert'] > 0){
                        //Clear the Converted Points, when a PL Card has been switched
                        unset($_SESSION['pl_user']['pl_points_to_convert']);
                        $respond['card_converted_points'] = 0;
                    }else{
                        $respond['card_converted_points'] = 0;
                    }

                    //The selected Account Card is NOT ACTIVE, i.e. cannot be used
                }else{
                    $respond['err_msg'] = '<p><strong>System message:</strong> The Card Number: '.$card_to_use_number.
                        ' that you have selected is not ACTIVE for Account: '.$_SESSION['pl_user']['memberid'].'.<br />'.
                        'Please Select Another Card Number to be Used for your Online Purchases.</p>';
                }

                //Return a Message for NON Existent Card
            }else{
                $respond['err_msg'] = '<p><strong>System message:</strong> The Card Number: '.$card_to_use_number.
                    ' that you have selected is not VALID.</p>';
            }

            //PL-Customer has not logged in yet - prompt to log in first
        }else $respond['err_msg'] = '<p><strong>System message:</strong> You have to be logged in the Payback Loyalty system, before to use this functionality.</p>';

        //return
        echo json_encode($respond);
    }

    /**
     * Like "action_update_account_card_in_use()" but doesn't change the selected card, just return information about the card
     */
    public function action_get_car_info(){

        $session = Session::instance();

        $respond = array(
            'err_msg' 				=> 'OK',
            'card_loyalty_points' 	=> 0,
            'card_spend_balance' 	=> 0,
            'card_converted_points' => 0
        );

        //get the Card Number to be set as Active (IN USE) cart_type
        $card_to_use_number = $type=$this->request->post('card_num_to_use');

        //Check if User is Logged In
        if(isset($_SESSION['pl_user'])){
            //Update Card to Use if Specified Card is VALID
            if(
                $card_to_use_number !== FALSE &&
                is_array($_SESSION['pl_user']['account_cards']) &&
                array_key_exists($card_to_use_number, $_SESSION['pl_user']['account_cards'])
            ){
                //Check if the Selected Card is ACTIVE, i.e. can be used and Set it as account_card_in_use
                if($_SESSION['pl_user']['account_cards'][$card_to_use_number]['devicestatus'] == 'DEVICE ACTIVE'){
                    $loyalty_points_euro  = number_format(floor($_SESSION['pl_user']['account_cards'][$card_to_use_number]['loyaltybalance']/100), 2, '.', '');
                    $loyalty_points_cents = floor((($_SESSION['pl_user']['account_cards'][$card_to_use_number]['loyaltybalance']/100) - $loyalty_points_euro) * 100)/100;
                    $respond['card_loyalty_points'	] = (float)$loyalty_points_euro + (float)$loyalty_points_cents;
                    $respond['card_spend_balance'	] = number_format($_SESSION['pl_user']['account_cards'][$card_to_use_number]['spendbalance'], 2, '.', '');

                }else{
                    $respond['err_msg'] = '<p><strong>System message:</strong> The Card Number: '.$card_to_use_number.
                        ' that you have selected is not ACTIVE for Account: '.$_SESSION['pl_user']['memberid'].'.<br />'.
                        'Please Select Another Card Number to be Used for your Online Purchases.</p>';
                }

                //Return a Message for NON Existent Card
            }else{
                $respond['err_msg'] = '<p><strong>System message:</strong> The Card Number: '.$card_to_use_number.
                    ' that you have selected is not VALID.</p>';
            }

            //PL-Customer has not logged in yet - prompt to log in first
        }else $respond['err_msg'] = '<p><strong>System message:</strong> You have to be logged in the Payback Loyalty system, before to use this functionality.</p>';

        //return
        echo json_encode($respond);
    }

    // Used to save/update PL-WS Member's Account Details
    public function action_save_account_details(){

        $session  = Session::instance();

        $pl_model = new Model_PaybackLoyalty();

        //Double check if the session exist
        if(isset($_SESSION['pl_user']['memberid']) AND !empty($_SESSION['pl_user']['memberid'])){

            $member_id   = $_SESSION['pl_user']['memberid'];
            $member_data = $this->request->post();
            $status      = $pl_model->update_member_details($member_id, $member_data);

            if($status->err_msg == 'OK'){

                //Close and reopen the session for update the data
                $username =  $_SESSION['pl_user']['username'];
                $password =  $_SESSION['pl_user']['password'];
                $session->delete('pl_user');

                $ws_request_result = $pl_model->validate_member($username, $password);

                if($ws_request_result->err_msg === 'OK'){
                    $pl_model->initiate_pl_customer($username, $password, $ws_request_result->request_data);
                    $pl_user = (array) $_SESSION['pl_user'];

                    //Add last transaction date in to the session variable
                    $pl_user['last_transaction'] = (Array) $pl_model->get_transaction_info($_SESSION['pl_user']->memberid, '2000-07-02T15:44:54+01:00', date('c'), 1);

                    $session = Session::instance();
                    $session->set('pl_user', $pl_user);

                    IbHelpers::set_message('Details updated successfully!', 'info');
                }
                else{
                    IbHelpers::set_message($ws_request_result->err_msg, 'error');
                }
            }
            else{
                IbHelpers::set_message($status->err_msg, 'error');
            }

            if(isset($_SERVER['HTTP_REFERER'])){
                $this->request->redirect($_SERVER['HTTP_REFERER']);
            }
            else{
                $this->request->redirect('/members-area.html');
            }
        }
    }

    // Used to save/update PL-WS Member's Account Details
    public function action_update_password(){

        $session  = Session::instance();

        $pl_model = new Model_PaybackLoyalty();

        //Double check if the session exist
        if(isset($_SESSION['pl_user']['memberid']) AND !empty($_SESSION['pl_user']['memberid'])){

            $member_id       = $_SESSION['pl_user']['memberid'];
            $oldpassword     = $this->request->post('pl_oldpassword');
            $newpassword     = $this->request->post('pl_newpassword');
            $confirmpassword = $this->request->post('pl_confirmpassword');
            $status          = $pl_model->update_member_password($member_id, $oldpassword, $newpassword, $confirmpassword);

            if($status->err_msg == 'OK'){

                //Close and reopen the session for update the data
                $username =  $_SESSION['pl_user']['username'];
                $password =  $newpassword;
                $session->delete('pl_user');

                $ws_request_result = $pl_model->validate_member($username, $password);

                if($ws_request_result->err_msg === 'OK'){
                    $pl_model->initiate_pl_customer($username, $password, $ws_request_result->request_data);
                    $pl_user = (array) $_SESSION['pl_user'];

                    //Add last transaction date in to the session variable
                    $pl_user['last_transaction'] = (Array) $pl_model->get_transaction_info($_SESSION['pl_user']->memberid, '2000-07-02T15:44:54+01:00', date('c'), 1);

                    $session = Session::instance();
                    $session->set('pl_user', $pl_user);

                    IbHelpers::set_message('Details updated successfully!', 'info');
                }
                else{
                    IbHelpers::set_message($ws_request_result->err_msg, 'error');
                }
            }
            else{
                IbHelpers::set_message($status->err_msg, 'error');
            }

            if(isset($_SERVER['HTTP_REFERER'])){
                $this->request->redirect($_SERVER['HTTP_REFERER']);
            }
            else{
                $this->request->redirect('/members-area.html');
            }
        }
    }

    public function action_get_transaction_info(){

        $session  = Session::instance();

        $pl_model = new Model_PaybackLoyalty();

        $post = $this->request->post();

        $ws_request_result = $pl_model->get_transaction_info($_SESSION['pl_user']['memberid'], $post['pl_from_date'], $post['pl_to_date'], $post['pl_max_records']);

        if($ws_request_result->err_msg == "OK"){

            //Update this Customer Transactions Data
            $_SESSION['pl_user']['transactions_info'] = array(
                'date_from' => $post['pl_from_date'],
                'date_to' =>  $post['pl_to_date'],
                'max_records' => $post['pl_max_records'],
                //If there was NO available record for th especified Period -=> return the success respond Message instead of NULL
                'transactions_details' =>  (!is_null($ws_request_result->request_data)? $ws_request_result->request_data : $ws_request_result->err_msg)
            );

            //change date format
            $length = count($ws_request_result->request_data);
            for( $i = 0; $i < $length; $i++){
                $ws_request_result->request_data[$i]['transdate'] = date('d-m-Y', strtotime($ws_request_result->request_data[$i]['transdate']));
            }

            return $this->response->body(json_encode($ws_request_result->request_data));
        }
        else{
            return $this->response->body($ws_request_result->err_msg);
        }

    }

//    public function action_update_selected_card(){
//
//        $session  = Session::instance();
//
//        $pl_model = new Model_PaybackLoyalty();
//
//        if(isset($_SESSION['pl_user']['memberid']) AND !empty($_SESSION['pl_user']['memberid'])){
//
//        }
//    }
}
