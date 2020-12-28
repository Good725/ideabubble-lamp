
<?php
/**
 * Created by Provident CRM.
 * User: itabarino
 * Date: 29/01/15
 * Time: 09:58
 */

//get config file
$live_settings = dirname(__FILE__) . '/config.php';
include $live_settings;

$local_settings = dirname(__FILE__) . '/config-dev.php';
if (file_exists($local_settings))
{
    include $local_settings;
}


// Require Cache
require_once("RestAPICacheHelper.php");

// Configure here or set into the ProvidentSugarRest.php
$sugar_rest = new RestAPICacheHelper($instance_rest_url,$instance_username,md5($instance_password));

// Verify if exists some error
if($sugar_rest == FALSE)
{
    print_r('Error loading RestAPICacheHelper');
    return;
}



// Return Invoice
if ($_REQUEST['request_type'] == 'getInvoice')
{
    $parameters = array(
        'module_name' => "Quotes",
        //The ID of the record to retrieve.
        'id' => $_REQUEST['invoice_id'],
        //The list of fields to be returned in the results
        'select_fields' => array(
            'id',
            'name',
            'total',
            'account_id',
            'fdc_paid_c',
            'quote_num',
            'pr_payment_link_code',
        ),
    );

    // Call cache helper - cache time: 0 min
    $results = $sugar_rest->callAPIMethod('get_entry', $parameters, false, 0);


    // check if debug is set so we can gather more info after the call
    if (Settings::instance()->get("debug_mode")==1)
    {
        Log::instance()->add(Log::DEBUG,"Debugging output of result var from call : results = ".json_encode($results));
    }

    //check if anything was returned from API call at all and stop processing if nothing.
     if (!$results->entry_list[0]) {

         $ret = array(
             'error' => '1',
             'message' => 'Nothing returned from API after the call.'
         );

         Log::instance()->add(Log::ERROR,"Nothing returned from call ".print_r($results));

         // Return
         header('Content-Type: application/json');
         echo json_encode($ret);
         die();
     }


         if ($_REQUEST['iid'] != $results->entry_list[0]->name_value_list->pr_payment_link_code->value) {

        $ret = array(
            'error' => '1',
            'message' => 'Invalid payment URL.'
        );

    } else {
        // Creating return array with necessary values
        $ret = array(
            'invoice_id' => $results->entry_list[0]->name_value_list->id->value,
            'account_id' => $results->entry_list[0]->name_value_list->account_id->value,
            'name' => $results->entry_list[0]->name_value_list->name->value,
            'total' => $results->entry_list[0]->name_value_list->total->value,
            'paid' =>  $results->entry_list[0]->name_value_list->fdc_paid_c->value,
            'quote_num' => $results->entry_list[0]->name_value_list->quote_num->value
        );

    }

    // Return
    header('Content-Type: application/json');
    echo json_encode($ret);
    die();
}

// Create Payment, relationship with account and returning data from new Payment
if ($_REQUEST['request_type'] == 'createPayment')
{
    // Create Payment
    $payment = createPayment($sugar_rest);

    // Create Relationship with Accounts
    $relationship = createRelationship($sugar_rest, $_REQUEST['account_id'], $payment->id);

    // Get Payment Data
    $paymentData = getPaymentData($sugar_rest, $payment->id);

    if ($paymentData['payment_status'] == 'PAID') {
        updateInvoice($sugar_rest);
    }

    // Return
    header('Content-Type: application/json');
    echo json_encode($paymentData);
    die();
}

// Create Payment
function createPayment($sugar_rest)
{
    $parameters = array(
        'module_name' => "PR_PA_PR_Payments",
        //The list of fields to be inserted
        'name_value_list' => array(
            array(
                'name' => 'pr_via_api',
                'value' => '1',
            ),
            array(
                'name' => 'name',
                'value' => $_REQUEST['name'],
            ),
            array(
                'name' => 'description',
                'value' => 'Payment Online',
            ),
            array(
                'name' => 'payment_type',
                'value' => 'Card',
            ),
            array(
                'name' => 'payment_amount',
                'value' => trim($_REQUEST['amount']),
            ),
            array(
                'name' => 'payment_status',
                'value' => 'DECLINED',
            ),
            array(
                'name' => 'card_type',
                'value' => $_REQUEST['card_type'],
            ),
            array(
                'name' => 'card_expiry',
                'value' => substr($_REQUEST['expiry'], 0, 2) . substr($_REQUEST['expiry'], -2),
            ),
            array(
                'name' => 'cvv',
                'value' => $_REQUEST['cvv'],
            ),
            array(
                'name' => 'card_number',
                'value' => str_replace(' ', '', $_REQUEST['number']),
            ),
            array(
                'name' => 'total_payment',
                'value' => trim($_REQUEST['amount']),
            ),
	        array(
		        'name' => 'pr_api_invoice_id',
		        'value' => trim($_REQUEST['invoice_id']),
            ),
        ),
    );

    // Call cache helper - cache time: 0 min
    $results = $sugar_rest->callAPIMethod('set_entry', $parameters, false, 0);

    // Return
    return $results;
}


function createRelationship($sugar_rest, $account_id, $payment_id)
{
    $parameters = array(
        'module_name' => "Accounts",
        'module_id' => $account_id,
        'link_field_name' => 'accounts_pr_pa_pr_payments',
        //The list of record ids to relate
        'related_ids' => array(
            $payment_id,
        ),
    );

    // Call cache helper - cache time: 0 min
    $results = $sugar_rest->callAPIMethod('set_relationship', $parameters, false, 0);

    // Return
    return $results;
}


function getPaymentData($sugar_rest, $payment_id)
{
    $parameters = array(
        'module_name' => "PR_PA_PR_Payments",
        //The ID of the record to retrieve.
        'id' => $payment_id,
        //The list of fields to be returned in the results
        'select_fields' => array(
            'id',
            'name',
            'payment_status',
            'receipt_number',
        ),
    );

    // Call cache helper - cache time: 0 min
    $results = $sugar_rest->callAPIMethod('get_entry', $parameters, false, 0);

    // Creating return array with necessary values
    $ret = array(
        'payment_id' => $results->entry_list[0]->name_value_list->id->value,
        'name' => $results->entry_list[0]->name_value_list->name->value,
        'payment_status' => $results->entry_list[0]->name_value_list->payment_status->value,
        'receipt_number' => $results->entry_list[0]->name_value_list->receipt_number->value,
    );

    return $ret;
}

function updateInvoice($sugar_rest) {
    $parameters = array(
        'module_name' => "Quotes",
        //The list of fields to be inserted
        'name_value_list' => array(
            array(
                "name" => "id",
                "value" => $_REQUEST['invoice_id']
            ),
            array(
                'name' => 'fdc_paid_c',
                'value' => 'Yes',
            ),

            array(
                'name' => 'fdc_paid_date_c',
                'value' => date('Y-m-d H:i:s'),
            ),
        ),
    );

    // Call cache helper - cache time: 0 min
    $results = $sugar_rest->callAPIMethod('set_entry', $parameters, false, 0);
}
