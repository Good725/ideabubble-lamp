<?php defined('SYSPATH') or die('No direct script access.');

/**
 * This class manage the document generation when triggers an action
 *
 * DEVELOPMENT MODE
 * When this class is instanced from a DEVELOPMENT or TESTING the $print_mail will be overwrite with the $print_mail_development
 *
 *
 * Example:
 *
 *  $dtr = new Model_DocumentTriggers();  Instance the class
 *
 *  Don't print the document in blank paper
 *  $dtr->set_auto_print(false);
 *
 *  Print in header paper
 *  $dtr->set_auto_print_with_header(true);
 *
 *
 *  Add documents
 *  $dtr->add_document_by_function('doc_template_agent_renewal_letter(1)');
 *
 * //old style, only for policys
 *  $dtr->add_document_by_function_name('doc_template_agent_debit_new_business', 1);
 *
 *
 *  Execute the actions (upload, print, mail...)
 *  if(!$res = $dtr->execute()){
 *      IbHelpers::set_message( $dtr->display_error(), 'error');
 *  }
 *
 * By default print functions are disabled, only print when you set the print functions
 *
 * Database Table for document queue: pinsurance_document_queue
 *
 * @documents http://wiki.ideabubble.ie/confluence/display/IMS/Documents
 */
class Model_DocumentTriggers extends Model_Document
{
    //Set default data, mail will be initialized in the constructor
    private $print_mail = '';
    private $header_print_mail = '';
    private $server_print_mail = '';
    private $knowledgetree_mail = '';
    private $customer_mail = 'ibiscustomer@ideabubble.ie';
    private $report_mail = 'ibiscustomer@ideabubble.ie'; //For error reports (not implemented)
    private $document_functions = array();
    private $error_list = array();
    private $success_list = array();
    private $error_report = 'screen';
    private $auto_print = false;
    private $auto_print_with_header = false;
    private $email_to_customer = false;
    private $email_to_customer_last_document = false;

    /**
     * Development test
     * This variables overwrite the other variables in 'Testing' and 'Development'  (.TEST, .DEV)
     **/
    private $print_mail_development = 'ibisprintdocs@ideabubble.ie'; //Overwrite  $print_mail


    public function __construct()
    {
        //Initialize the mail values from the config file
        if(Kohana::$environment == Kohana::TESTING OR Kohana::$environment == Kohana::DEVELOPMENT OR Kohana::$environment == Kohana::STAGING)
        {
            $print_mails = Kohana::$config->load('config')->get('dev_print_mails');
        }
        else if(Kohana::$environment == Kohana::PRODUCTION)
        {
            $print_mails = Kohana::$config->load('config')->get('print_mails');
        }
        $this->print_mail = $print_mails['print_mail'];
        $this->header_print_mail = $print_mails['header_print_mail'];
        $this->server_print_mail = $print_mails['server_print_mail'];
        $this->knowledgetree_mail = $print_mails['knowledgetree_mail'];
    }

    /***
     * SET VARIABLES
     ***/
    public function set_email_to_customer($customer_id = false)
    {
        if ($customer_id) {
            $this->email_to_customer = $customer_id;
        } else {
            $this->email_to_customer = false;
        }
    }

    public function set_email_to_customer_last_document($last = false)
    {
        if ($last) {
            $this->email_to_customer_last_document = true;
        } else {
            $this->email_to_customer_last_document = false;
        }
    }

    public function set_print_mail($mail)
    {
        if (Valid::email($mail)) {
            $this->print_mail = $mail;
        } else {
            $this->error_list[] = "The print e-mail $mail is not a valid e-mail";
        }
    }

    public function set_customer_mail($mail)
    {
        if (Valid::email($mail)) {
            $this->customer_mail = $mail;
        } else {
            $this->error_list[] = "The customer e-mail $mail is not a valid e-mail";
        }
    }

    public function set_report_mail($mail)
    {
        if (Valid::email($mail)) {
            $this->report_mail = $mail;
        } else {
            $this->error_list[] = "The report e-mail $mail is not a valid e-mail";
        }
    }

    public function set_auto_print($bool)
    {
        if ($bool == true) {
            $this->auto_print = true;
        } else {
            $this->auto_print = false;
        }
    }

    public function set_auto_print_with_header($bool)
    {
        if ($bool == true) {
            $this->auto_print_with_header = true;
        } else {
            $this->auto_print_with_header = false;
        }
    }

    /***
     * END SET VARIABLES
     ***/


    /***
     * HANDLE ERRORS
     ***/


    /**
     * Call the display error method
     */
    public function display_error()
    {

        switch ($this->error_report) {
            case 'screen':
                return $this->screen_display_error();
                break;
            default:
                return $this->screen_display_error();
                break;
        }
    }

    public function screen_display_error()
    {

        $error_msg = '';
        foreach ($this->error_list as $error) {
            $error_msg .= $error . '  ';
        }

        return $error_msg;

    }

    /***
     * END HANDLE ERRORS
     ***/


    /**
     * OLD VERSION PLEASE DON'T USE THIS FUNCTION
     * Add documents to the trigger
     * @param String $document_function_name The name of the function for generate the document, All function for generate document are in the Model_doc
     * @param Int $policy_id
     *
     * @return bool
     */
    public function add_document_by_function_name($document_function_name, $policy_id)
    {
        $doc = new Model_doc();
        if (method_exists($doc, $document_function_name)) {
            $this->document_functions['document_function'][] = $document_function_name . '(' . $policy_id . ')';
            /*$this->document_functions['document_function'][] = $document_function_name;
            $this->document_functions['policy_id'][] = $policy_id;*/
            $this->add_document_to_queue($document_function_name . '(' . $policy_id . ')');
            return true;
        } else {
            $this->error_list[] = "The function $document_function_name don't exist";
            return false;
        }
    }

    /**
     * Add documents to the trigger
     * @param String $function The name of the function for generate the document, All function for generate document are in the Model_doc
     *
     * @return bool
     */
    public function add_document_by_function($function)
    {
        $this->document_functions['document_function'][] = $function;
        //Insert the document in to the database
        $this->add_document_to_queue($function);
        return true;
    }

    /**
     * Add documents to the trigger
     * @param String $document_function_name The name of the function for generate the document, All function for generate document are in the Model_doc
     * @param Int $policy_id
     *
     * @return bool
     */
    public function add_document_from_queue()
    {
        //Get first with status 0 (limit 1)
        $document = DB::select()
            ->from('pinsurance_documents_queue')
            ->where('status', '=', '0')
            ->limit(1)
            ->execute()
            ->as_array();
        //Update status (in progress)
        if (!empty($document)) {
            DB::update('pinsurance_documents_queue')
                ->set(array('status' => '1', 'date' => DB::expr('NOW()')))
                ->where('id', '=', $document[0]['id'])
                ->limit(1)
                ->execute();

            //Add to the document_functions array()
            $this->document_functions['document_function'][] = $document[0]['document_function'];
            $this->document_functions['document_queue_id'][] = $document[0]['id'];
            //changed to force plain paper by default on advice of Mike. IBIS-3009
            if ($document[0]['print_plain_paper'] == '1') {
                $this->set_auto_print(true);
            } else {
                $this->set_auto_print(false);
            }

            if ($document[0]['print_header_paper'] == '1') {
                $this->set_auto_print_with_header(true);
            } else {
                $this->set_auto_print_with_header(false);
            }
            if ($document[0]['email_to_customer'] > 0) {
                $this->set_email_to_customer($document[0]['email_to_customer']);
            } else {
                $this->set_email_to_customer(false);
            }
            if ($document[0]['email_to_customer_last_doc'] == 1) {
                $this->set_email_to_customer_last_document(true);
            } else {
                $this->set_email_to_customer_last_document(false);
            }
        }
    }

    /**
     * Add documents to the documents queue table
     * @param String $project_name  Only in case of call this function from a different project set up the project name as ('ibis')
     * @return bool
     */
    public function execute($project_name = false)
    {

        if (!empty($this->error_list)) {
            return false;
        } else {
            try {
                /*
                foreach($this->document_functions['document_function'] as $function){
                    $this->add_document_to_queue($function);
                }*/
                //Set the environment before call the CLI script, THIS IS A MANDATORY or all the scripts will be running in the test/development database
                $environment = Kohana::$environment;
                // Example:    /usr/bin/php /Library/WebServer/Documents/wms/projects/ibis/www/index.php --kohana_env=40 --uri=admin/executedocumentsqueue > /dev/null &
                exec('/usr/bin/php ' . $this->get_script_project_name($project_name) . ' --kohana_env=' . $environment . ' --uri=admin/executedocumentsqueue > /dev/null &');
                unset($this->document_functions);
                //$this->execute_cli(); //for debug
                return true;
            } catch (exception $e) {
                $this->error_list[] = $e->getMessage();;
                return false;
            }
        }
    }

    private function add_document_to_queue($document_function)
    {
        //check auto print documents
        if ($this->auto_print) {
            $print_plain_paper = 1;
        } else {
            $print_plain_paper = 0;
        }

        if ($this->auto_print_with_header) {
            $print_header_paper = 1;
        } else {
            $print_header_paper = 0;
        }

        if ($this->email_to_customer) {
            $email_to_customer = $this->email_to_customer;
        } else {
            $email_to_customer = 0;
        }

        if ($this->email_to_customer_last_document) {
            $email_to_customer_last_doc = 1;
        } else {
            $email_to_customer_last_doc = 0;
        }

        DB::insert('pinsurance_documents_queue', array('document_function', 'print_plain_paper', 'print_header_paper', 'email_to_customer', 'email_to_customer_last_doc', 'date'))
            ->values(array($document_function, $print_plain_paper, $print_header_paper, $email_to_customer, $email_to_customer_last_doc, DB::expr('NOW()')))
            ->execute();
    }

    /**
     * Trigger all the documents actions
     * @return bool
     */
    public function execute_cli()
    {

        if (!empty($this->error_list)) {
            return false;
        } else {
            try {
                //Check if there is some documents stuck
                $this->fix_stuck_queue_document_and_prevent_parallel_executions();

                //Check if there are any documents set in this call, otherwise add for the queue
                if (empty($this->document_functions['document_function'])) {
                    $this->add_document_from_queue();
                }

                $doc = new Model_doc();
                $doc->set_external_error_handing(true);
                //Check if mail send is set
                if ($this->auto_print) {
                    if (Kohana::$environment == Kohana::TESTING OR Kohana::$environment == Kohana::DEVELOPMENT OR Kohana::$environment == Kohana::STAGING) {
                        $this->print_mail = $this->print_mail_development;
                    }
                    $doc->set_mail($this->print_mail, 'plain_paper', 'This message is only for a printer', 'ibis_system@ideabuble.ie');
                }
                if ($this->auto_print_with_header) {
                    if (Kohana::$environment == Kohana::TESTING OR Kohana::$environment == Kohana::DEVELOPMENT OR Kohana::$environment == Kohana::STAGING) {
                        $this->header_print_mail = $this->print_mail_development;
                    }
                    $doc->set_mail($this->header_print_mail, 'headed_paper', 'This message is only for a printer', 'ibis_system@ideabuble.ie');
                }

                //If this document is generated in quote page, then add to a folder all the PDF for this customer and send to the customer mail
                if ($this->email_to_customer) {
                    //Check if is last document,if there are not other documents in the queue stucked skip this document until the other document are generated/printed
                    if ($this->email_to_customer_last_document) {
                        if (!$this->check_if_last_document($this->document_functions)) {
                            //Do not generate the document if there is remaining documents in the queue
                            exit;
                        }
                    }

                    //stuff to do

                    //Set global var with the folder path
                    $GLOBALS['quote_email_customer'] = $this->email_to_customer;
                }

                foreach ($this->document_functions['document_function'] as $pos => $function) {
                    //Reset the script time to 60s every document.
                    set_time_limit('300');
                    //Run the function
                    $function_tmp = explode('(', $function);
                    $function_name = $function_tmp[0];
                    $function_parameters = rtrim($function_tmp[1], ')');
                    $parameters = explode(',', $function_parameters);
                    //$dev_tmp =  (string)$doc->$function_name . '('. $function_parameters .')';
                    call_user_func_array(array($doc, $function_name), $parameters);
                    //$doc->$function_name($function_parameters);
                    $this->success_list[] = $function;

                    if (isset($this->document_functions['document_queue_id'][$pos]) AND (int)$this->document_functions['document_queue_id'][$pos] > 0) { //If is queued, remove from queue
                        $this->dequeue($this->document_functions['document_queue_id'][$pos]);
                        unset($this->document_functions['document_queue_id'][$pos]);
                    }
                    unset($this->document_functions['document_function'][$pos]);
                    //Set the environment before call the CLI script, THIS IS A MANDATORY or all the scripts will be running in the test/development database
                    $environment = Kohana::$environment;
                    if ((int)$this->count_queue_document() > 0) {
                        exec('/usr/bin/php ' . $_SERVER['SCRIPT_FILENAME'] . ' --kohana_env=' . $environment . ' --uri=admin/executedocumentsqueue > /dev/null &');
                        //$this->execute_cli(); //for debug
                    }

                    //if is last document in the quote process, then email the customer and remove the folder
                    if ($this->email_to_customer_last_document) {
                        $from = 'info@yachtsman.ie';
                        $to = $this->get_customer_email($this->email_to_customer);
                        $doc_config = Kohana::$config->load('config')->get('doc_config');
                        $pdf_folder = $doc_config['cache'] . $this->email_to_customer;
                        $subject = 'Yachtsman Euromarine Policy Confirmation';
                        $files = $this->get_customer_files($pdf_folder);
                        $bcc = '';
                        //Don't send the BCC is we aren't in LIVE
                        if (Kohana::$environment == Kohana::TESTING OR Kohana::$environment == Kohana::DEVELOPMENT OR Kohana::$environment == Kohana::STAGING) {
                            $bcc = '';
                        }

                        //Notifications Step 4 PAYMENT
                        $template_data_quote_paid = array(
                            'contactname' => '',
                            'AddressLine1' => '',
                            'AddressLine2' => '',
                            'AddressLine3' => '',
                            'AddressLine4' => '',
                            'HOMETEL' => '',
                            'EMAIL' => '',
                            'PolicyNumber' => '',
                            'VESSELCLASS' => '',
                            'CURRENCYSYMBOL_FOR_MAIL' => '',
                            'COVER_START_DATE' => ''
                        );

                        //Fill the info for the mail, get the policy ID from the function
                        switch ($function_name) {
                            case 'doc_receipt_template_receipt':
                                $model_payment = new Model_Payment();
                                $payment = $model_payment->get_all($parameters[0]);
                                $transaction = Model_Transaction::get_all($payment[0]['transaction_id']);
                                $policy_id = $transaction[0]['policy_id'];
                                break;
                            default:
                                $policy_id = $parameters[0];
                        }
                        $docarrayhelper = new Docarrayhelper($policy_id);

                        $template_data_quote_paid = $docarrayhelper->fill_template_array($template_data_quote_paid);
                        $mail_file_step_payment = file_get_contents(Kohana::find_file('views', 'quote_mail_after_payment'));
                        foreach ($template_data_quote_paid AS $tmp_data_index => $td) {
                            $mail_file_step_payment = str_replace('@' . $tmp_data_index . '@', $td, $mail_file_step_payment);
                        }


                        IbHelpers::send_email($from, $to, NULL, $bcc, $subject, $mail_file_step_payment, $files);
                        //Send the mail with all the attached PDF, take the URL for the global var and delete (unlink) the folder
                        $this->remove_folder($pdf_folder);
                    }

                    return true; //At the moment the script run only 1 time

                }

                return true;
            } catch (exception $e) {

                $error = (string)$e;
                $this->error_list[] = $error;
                $this->update_error_log($error);
                //If there is an error, i will continue with the others documents in the queue.
                $environment = Kohana::$environment;
                if ((int)$this->count_queue_document() > 0) {
                    exec('/usr/bin/php ' . $_SERVER['SCRIPT_FILENAME'] . ' --kohana_env=' . $environment . ' --uri=admin/executedocumentsqueue > /dev/null &');
                    //$this->execute_cli(); //for debug
                }
                return false;
            }
        }
    }

    public function fix_stuck_queue_document_and_prevent_parallel_executions()
    {
        //If there is document processing for more than 5 minutes, then assumes the document is stuck and reset the status to 0 for regenerate again
        DB::query(Database::UPDATE, 'UPDATE pinsurance_documents_queue SET `status` = 0, restarts = (restarts + 1) WHERE TIMESTAMPDIFF(MINUTE, DATE_ADD(date, INTERVAL 5 MINUTE), NOW()) > 5 AND `status` = 1')
            ->execute();

        //Check if another instance of the same script is running in the background
        $query = DB::select()
            ->from('pinsurance_documents_queue')
            ->where('status', '=', '1')
            ->execute()
            ->as_array();
        if (!empty($query)) {
            //kill the script, prevent parallel execution.
            //exit();
        }
    }

    public function update_error_log($error)
    {
        DB::update('pinsurance_documents_queue')
            ->set(array('log' => $error))
            ->where('id', '=', $this->document_functions['document_queue_id'][0])
            ->limit(1)
            ->execute();
    }

    public function count_queue_document()
    {

        try {
            $query = DB::select('id')
                ->from('pinsurance_documents_queue')
                ->where('status', '=', '0')
                ->execute()
                ->count();
            return $query;
        } catch (exception $e) {
            return false;
        }
    }

    public function dequeue($id)
    {
        try {
            DB::delete('pinsurance_documents_queue')
                ->where('id', '=', $id)
                ->limit(1)
                ->execute();
        } catch (exception $e) {
            $this->error_list[] = $e->getMessage();;
            return false;
        }
    }

    /**
     * Check is there are more documents in the queue for the same customer.
     * Use this function when the las document is generated for make sure we don't send the mail without all documents.
     */
    public function check_if_last_document($document)
    {
        try {
            $id = $document['document_queue_id']['0'];

            $query = DB::select('id')
                ->from('pinsurance_documents_queue')
                ->where('id', '<', $id)
                ->execute()
                ->as_array();

            if (count($query) > 0) {
                return false;
            } else {
                return true;
            }

        } catch (Exception $e) {
            //Just in case something is wrong, better to keep going for prevent queue problems.
            true;
        }


    }

    /**
     * @param $customer_id
     * @return string Email
     */
    public function get_customer_email($customer_id)
    {
        try {
            $query = DB::select('contactdetail_value')
                ->from('pcustomer_contact_details')
                ->where('contact_id', '=', $customer_id)
                ->and_where('contactdetail_type', '=', 'email')
                ->limit(1)
                ->execute()
                ->as_array();
            return $query[0]['contactdetail_value'];
        } catch (Exception $e) {
            return 'get_customer_email@ideabubble.ie';
        }
    }

    public function get_customer_files($path)
    {
        $files = array();
        if ($handle = opendir($path)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    $files[] = $path . DIRECTORY_SEPARATOR . $entry;
                }
            }
            closedir($handle);
        }
        return $files;
    }

    /**
     * Remove the folder and the fails in the folder, this isn't a recursive function, just lv 0 files.
     * @param $path
     */
    public function remove_folder($path)
    {
        try {
            if ($handle = opendir($path)) {
                while (false !== ($entry = readdir($handle))) {
                    if ($entry != "." && $entry != "..") {
                        $file = $path . DIRECTORY_SEPARATOR . $entry;
                        unlink($file);
                    }
                }
                closedir($handle);
            }
            rmdir($path);
        } catch (Exception $e) {
            //$msg = $e->getMessage();
            //Nothing to do for now
        }
    }

    /**
     *
     * @param String $project_name
     * @return String
     */
    public function get_script_project_name($project_name = false)
    {
        if ($project_name) {
            $tmp_string = explode('projects/', $_SERVER['SCRIPT_FILENAME']);
            $tmp_string = explode('/', $tmp_string[1]);
            $old_project_name = $tmp_string[0];
            return str_replace($old_project_name, $project_name, $_SERVER['SCRIPT_FILENAME']);
        } else {
            return $_SERVER['SCRIPT_FILENAME'];
        }
    }


    //Functions to be defined (set_error_report, add_error, get_errors);


}