<?php defined('SYSPATH') or die('No direct script access.');

/**
 * Settings loader and updater
 *
 * @category   Settings
 * @author     Diarmuid
 */
class IbHelpers
{

    private static $content_mapping = NULL;

    /**
     *  Helper to create alerts. as used with Twitter Bootstrap.
     *
     *  See http://twitter.github.com/bootstrap/components.html#alerts
     */
    public static function alert($content, $type = NULL, $header = NULL, $block = FALSE)
    {
        //Setup the default headers
        if (!$header && (!$type OR $type === 'alert')) // Type is alert
        {
            $header = 'Caution: ';
        } elseif (!$header && $type === 'error') // Type is error
        {
            $header = 'Warning:';
        } elseif (!$header && $type === 'success') // Type is success
        {
            $header = 'Success: ';
        } elseif (!$header && $type === 'info') // Type is info
        {
            $header = 'Attention: ';
        }

        // Set the basic class
        $classes = 'alert';

        // Set the alert as an alert-block
        if ($block) {
            $classes .= ' alert-block';
        }

        // Add the alert type class
        if ($type OR $type !== 'alert') {
            $classes .= ' alert-' . (($type == 'error') ? 'danger' : $type);
        }

        // Add on the close button
        $alert = '<div class="' . $classes . '"><a class="close" data-dismiss="alert">×</a>';

        // Is ther a header for the alert block
        if ($block && $header) {
            $alert .= '<h4 class="alert-heading">' . $header . '</h4>';
        } // Else add a normal header
        elseif ($header) {
            $alert .= '<strong>' . $header . '</strong> ';
        }

        $alert .= $content . '</div>';

        return $alert;

    }

    /**
     *  Helper to set messages to be shown on the next call of the get_messages function
     *
     *  See http://twitter.github.com/bootstrap/components.html#alerts
     */
    public static function set_message($content, $type = 'info', $header = NULL, $block = FALSE)
    {
        // Be sure to only profile if it's enabled
        if (Kohana::$profiling === TRUE) {
            // Start a new benchmark
            $benchmark = Profiler::start('Set Alert Messages', __FUNCTION__);
        }

        // Initialise the session instance
        $session = Session::instance();

        // Setup our message array
        $message = array(
            'content' => $content,
            'type' => $type,
            'header' => $header,
            'block' => $block,
            'timestamp' => time(),
        );

        // Check if their are already messages in the session
        if ($messages = $session->get('messages')) {
            // Merge our message onto the existing messages
            $messages = array_merge($messages, array($message));
        } else {
            $messages = array($message);
        }

        // Set the message array to the session
        $session->set('messages', $messages);

        if (isset($benchmark)) {
            // Stop the benchmark
            Profiler::stop($benchmark);
        }
    }

    /**
     *  Helper to return a string of formatted messages from the session
     */
    public static function get_messages()
    {
        // Be sure to only profile if it's enabled
        if (Kohana::$profiling === TRUE) {
            // Start a new benchmark
            $benchmark = Profiler::start('Get Alert Messages', __FUNCTION__);
        }

        // Initialise the session instance
        $session = Session::instance();

        // Get the messages from teh session
        $messages = $session->get('messages');

        // Initialise our return variable as an empty string
        $return = '';

        // check is there are messages
        if (is_array($messages)) {
            foreach ($messages as $message) {
                if ((time() - $message['timestamp']) < 60 * 60) // Only show messages that are younger than 1 hour
                {
                    // Add the alert strings to the return variable
                    $return .= IbHelpers::alert($message['content'], $message['type'].' popup_box', $message['header'], $message['block']);
                }
            }

            // Delete the session messages once they are read
            $session->delete('messages');

            if (isset($benchmark)) {
                // Stop the benchmark
                Profiler::stop($benchmark);
            }

            // Return the formatted messages
            return $return;
        }

        if (isset($benchmark)) {
            // Stop the benchmark
            Profiler::stop($benchmark);
        }

        // No messages in the session so return FALSE
        return FALSE;

    }


    /**
     * Helper to return a nice little formated string for debugging.
     * @static
     * @param $debug
     */
    public static function pre_r($debug)
    {
        echo "<pre>" . print_r($debug, true) . "</pre>";
    }

    /**
     *  Helper to return a nice little formated string for debugging then DIE.
     * @static
     * @param $debug
     */
    public static function die_r($debug)
    {
        die(IbHelpers::pre_r($debug));

    }

    /**
     * Helper to return a nice flattened array.
     * @static
     * @param $debug
     * @return array
     * @author David
     */
    public static function flatten_array($debug)
    {

        $flat_array = array();

        foreach ($debug as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $key1 => $value1) {
                    $flat_array[] = $value1;
                }
            }
        }

        return $flat_array;
    }

    /**
     * @param $file_post $_FILES
     * @return array returns reorgonized array of files see http://php.net/manual/en/features.file-upload.multiple.php
     */
    private static function reArrayFiles(&$file_post)
    {

        $file_ary = array();
        $file_count = count($file_post['name']);
        $file_keys = array_keys($file_post);

        for ($i = 0; $i < $file_count; $i++) {
            foreach ($file_keys as $key) {

                $file_ary[$i][$key] = $file_post[$key][$i];
            }
        }

        $index = -1;
        foreach ($file_ary as $item) {
            $index++;
            if ($item['name'] == "") {
                unset($file_ary[$index]);
            }
        }

        return $file_ary;
    }


    /**
     * Handles the upload of multiple files to KnowledgeTree
     * @static
     * @param $files $_FILES array to be saved one by one.
     * @param $allowedExts Array with the allow extensions | String 'all' for allow all types
     * @param $kt_folder
     * @param null $contact_id
     * @return array
     * @throws Exception
     * @author Taron
     */
    public static function uploadmultiplefiles($files, $allowedExts, $kt_folder, $contact_id = null)
    {

        $return_data = array();

        if (Kohana::$environment != Kohana::PRODUCTION) {
            $kt_folder = '' . $kt_folder;
        }
        $localFilepath = Kohana::$config->load('config')->get('upload_location');
        if (!file_exists(@$localFilepath['temp'])) {
            $localFilepath['temp'] = '/tmp/';
        }

        $files = IbHelpers::reArrayFiles($files);


        foreach ($files as $file) {

            $location = Upload::save($file, $file['name'], $localFilepath['temp']);
            if ($location == false) {
                throw new Exception($file["name"] . ' not uploaded.');
            }

            // Can the directory be written to, if not tell the user.
            if (!is_writable($localFilepath['temp'])) {
                throw new Exception($localFilepath['temp'] . " is not writable.");
            }

            $test = $file["name"];
            $extension = explode(".", $test);
            $extension = end($extension);

            if ($allowedExts != 'all') {
                $extension_ok = in_array($extension, $allowedExts);
            } else {
                $extension_ok = true;
            }

            if ($extension_ok) {
                if ($file["error"] > 0) {
                    return array('message' => "Code: " . $file["error"], 'type' => 'error');
                } else {
                    $knowledgetree_data = Kohana::$config->load('config')->get('KnowledgeTree');
                    // Call KT function to upload document
                    $kt_connection = New KTClient($knowledgetree_data['url']);
                    $kt_connection->initSession($knowledgetree_data['username'], $knowledgetree_data['password']);

                    // Checks the directory structure and sees if the contact has an initialised space on KT
                    if (class_exists('Model_Dms')) {
                        Model_Dms::quick_directory_check($kt_connection, $contact_id);
                    } elseif (class_exists('Model_Document')) {
                        Model_Document::quick_directory_check($kt_connection, $contact_id);
                    }


                    $document_id = $kt_connection->addDocument($file['name'], $location, $kt_folder);
                    // remove the file
                    unlink($location);
                    array_push($return_data, array('message' => $file["name"] . ' has been saved', 'type' => 'success', 'document_id' => $kt_connection->lastFileId));
                }
            } else {
                // Tell the user there is a problem
                array_push($return_data, array('message' => $file["name"] . ' is invalid.', 'type' => 'error'));
            }

        }
        return $return_data;

    }


    /**
     * Handles the upload of files to KnowledgeTree
     * @static
     * @param $notneeded
     * @param $allowedExts Array with the allow extensions | String 'all' for allow all types
     * @param $kt_folder
     * @param null $contact_id
     * @return array
     * @throws Exception
     * @author David
     */
    public static function uploadfile($notneeded, $allowedExts, $kt_folder, $contact_id = null)
    {

        if (Kohana::$environment != Kohana::PRODUCTION) {
            $kt_folder = '' . $kt_folder;
        }
        $localFilepath = Kohana::$config->load('config')->get('upload_location');
        if (!file_exists(@$localFilepath['temp'])) {
            $localFilepath['temp'] = '/tmp/';
        }
        $location = Upload::save($_FILES['file'], $_FILES['file']['name'], $localFilepath['temp']);
        if ($location == false) {
            throw new Exception($_FILES["file"]["name"] . ' not uploaded.');
        }

        // Can the directory be written to, if not tell the user.
        if (!is_writable($localFilepath['temp'])) {
            throw new Exception($localFilepath['temp'] . " is not writable.");
        }

        $test = $_FILES["file"]["name"];
        $extension = explode(".", $test);
        $extension = end($extension);

        if ($allowedExts != 'all') {
            $extension_ok = in_array($extension, $allowedExts);
        } else {
            $extension_ok = true;
        }

        if ($extension_ok) {
            if ($_FILES["file"]["error"] > 0) {
                return array('message' => "Code: " . $_FILES["file"]["error"], 'type' => 'error');
            } else {
                $knowledgetree_data = Kohana::$config->load('config')->get('KnowledgeTree');
                // Call KT function to upload document
                $kt_connection = New KTClient($knowledgetree_data['url']);
                $kt_connection->initSession($knowledgetree_data['username'], $knowledgetree_data['password']);

                // Checks the directory structure and sees if the contact has an initialised space on KT
                if (class_exists('Model_Dms')) {
                    Model_Dms::quick_directory_check($kt_connection, $contact_id);
                } elseif (class_exists('Model_Document')) {
                    Model_Document::quick_directory_check($kt_connection, $contact_id);
                }


                $kt_connection->addDocument($_FILES['file']['name'], $location, $kt_folder);
                // remove the file
                unlink($location);
                return array('message' => $_FILES["file"]["name"] . ' has been saved', 'type' => 'success');
            }
        } else {
            // Tell the user there is a problem
            return array('message' => $_FILES["file"]["name"] . ' is invalid.', 'type' => 'error');
        }
    }

    /**
     * This function removes a document passed to it by ID reference only.
     * @static
     * @param $doc_id
     * @param null $message
     * @return array
     * @author David
     */
    public static function remove_document($doc_id, $message = null)
    {

        // Get the KT connection config data
        $knowledgetree_data = Kohana::$config->load('config')->get('KnowledgeTree');
        // Create connection to KT
        $kt_connection = New KTClient($knowledgetree_data['url']);
        $kt_connection->initSession($knowledgetree_data['username'], $knowledgetree_data['password']);
        // Remove selected document
        try {
            // Remove the Document
            $kt_connection->removeDocument($doc_id, $message);

        } catch (Exception $e) {
            // Set feedback var to error
            $feedback = array('message' => $e->getMessage(), 'type' => 'error');
        }

        // If no errors then set message to success
        if (empty($feedback)) {
            $feedback = array('message' => 'Document with ID of ' . $doc_id . ' has been removed', 'type' => 'success');
        }

        // Return message
        return $feedback;

    }

    // Validate the input data
    public static function validate($to_be_validated, $post_data)
    {

        $status = TRUE;

        foreach ($to_be_validated as $key => $value) {
            if (empty($post_data[$value])) {
                IbHelpers::set_message('' . Text::ucfirst($value) . ' must be set.', 'error');
                $status = FALSE;
            }
        }
        return $status;
    }

    /**
     * Convert mysql date to Ireland date format
     *
     * @static
     *
     * @param $mysql_date
     */
    public static function date_format($mysql_date)
    {
        if (empty($mysql_date) OR $mysql_date === '00-00-0000' OR $mysql_date === '0000-00-00' OR $mysql_date === '0000-00-00 00:00:00') {
            return '';
        } else {
            return date('d-m-Y', strtotime($mysql_date));
        }
    }

    /**
     * Convert date from Ireland format to mysql format
     *
     * @static
     * @param $ireland_date
     * @return string
     */
    public static function mysql_date($ireland_date)
    {
        if (empty($ireland_date)) {
            return null;
        } else {
            $datetime = explode(' ', $ireland_date); // handle datetime
            $date = $datetime[0];
            if (strpos($ireland_date, '/')) { // d/m/Y 15/05/2017
                $date = explode('/', $date);
            } else {
                $date = explode('-',  $date); // d-m-Y or Y-m-d
            }

            try {
                $month = $date[1];
                if (strlen($date[0]) == 4) { // Y-m-d
                    $year = $date[0];
                    $day = $date[2];
                } else {
                    $year = $date[2]; // d-m-Y
                    $day = $date[0];
                }
                return $year . '-' . $month . '-' . $day;
            } catch (Exception $exc) {
                return null;
            }
            //return date('Y-m-d', strtotime($ireland_date));
        }
    }

    /**
     * Search array of multiple rows for some value. Returns all rows that mach.
     *
     * Sample:
     *     $x=array(
     *          array('id' => 'IE', 'option' => 'Ireland', 'local_tax' => 10)
     *         ,array('id' => 'ES', 'option' => 'Spain', 'local_tax' => 6)
     *         ,array('id' => 'GB', 'option' => 'UK', 'local_tax' => 5)
     *         ,array('id' => 'GB-NIE', 'option' => 'UK:Northern Ireland', 'local_tax' => 5));
     *
     *  1)
     *  IBHelpers::array_msearch($x, 'id','GB') returns
     *       array(
     *         array('id' => 'GB', 'option' => 'UK', 'local_tax' => 5)
     *    )
     *
     *  2)
     *  IBHelpers::array_msearch($x, 'local_tax',5) returns
     *    array(
     *            array('id' => 'GB', 'option' => 'UK', 'local_tax' => 5)
     *         ,array('id' => 'GB-NIE', 'option' => 'UK:Northern Ireland', 'local_tax' => 5)
     *    )
     *
     * @param $array
     * @param $key
     * @param $value
     * @return array
     *
     */
    public static function array_msearch($array, $key, $value)
    {
        $results = array();

        if (is_array($array)) {
            if (isset($array[$key]) && $array[$key] == $value)
                $results[] = $array;

            foreach ($array as $subarray)
                $results = array_merge($results, self::array_msearch($subarray, $key, $value));
        }

        return $results;
    }

    /**
     * Dev: David
     * Boolean flip on currency
     *
     * If 1 then € is provided else £
     *
     * @param $currency_bit
     * @return char € or £
     *
     */
    public static function currency_flip($currency_bit)
    {

        if ($currency_bit == 1) {
            return '€';
        } else {
            return '£';
        }
    }

    // Returns an answer to a risk question
    public static function getRiskQuestionAnswers($questionID, $policyID)
    {
        $result = DB::select()
            ->from('pinsurance_view_policyelement_has_elementrisk')
            ->where('riskquestions_id', '=', $questionID)
            ->and_where('policy_id', '=', $policyID)
            ->and_where('deleted', '=', 0)
            ->execute()
            ->as_array();

        if (isset($result[0])) {
            return $result[0];
        } else {
            return '';
        }
    }

    /*
         * Returns the current environment
         *
         * @static
         * @return null|string
         */
    public static function getEnvironment()
    {
        $mode = null;
        //
        $environment = Kohana::$environment;
        if ($environment == Kohana::PRODUCTION) {
            $mode = 'ie';
        } elseif ($environment == Kohana::STAGING) {
            $mode = 'ie';
        } elseif ($environment == Kohana::TESTING) {
            $mode = 'test';
        } else {
            $mode = 'dev';
        }
        return $mode;
    }

    /**
     * returns loading image
     * @static
     * @return Image
     */
    public static function getLoadingImage()
    {
        return URL::overload_asset('img/ajax-loader.gif');
    }

    /**
     * @param string $from The sender.
     * @param array|string $to An array with the 'to' recipients or an string like 'email@domain.com,email@domain.com...'.
     * @param array|string|null $cc An array with the 'cc' recipients or an string like 'email@domain.com,email@domain.com...'. For no 'cc' recipients, pass an empty array or set a NULL value.
     * @param array|string|null $bcc An array with the 'bcc' recipients or an string like 'email@domain.com,email@domain.com...'. For no 'bcc' recipients, pass an empty array or set a NULL value.
     * @param string $subject The subject of the mail.
     * @param string $message The message to be sent.
     * @param array|null $files Files to be attached. For no files to be attached, pass an empty array or set a NULL value.
     * @return bool TRUE if the function success. Otherwise, FALSE.
     */
    public static function send_email($from, $to, $cc, $bcc, $subject, $message, $files = NULL)
    {
        //Apply Tags
        if (is_callable(array('Model_Notifications', 'apply_tags'))) {
            Model_Notifications::apply_tags($message);
        }
        if (Model_Plugin::is_enabled_for_role('Administrator', 'Messaging')) {
            $recipients = array();
            if (is_array($to)) {
                foreach ($to as $recipient) {
                    $recipients[] = array('target_type' => 'EMAIL', 'target' => $recipient, 'x_details' => 'to');
                }
            } else {
                if ($to != '') {
                    $recipients[] = array('target_type' => 'EMAIL', 'target' => $to, 'x_details' => 'to');
                }
            }
            if (is_array($cc)) {
                foreach ($cc as $recipient) {
                    $recipients[] = array('target_type' => 'EMAIL', 'target' => $recipient, 'x_details' => 'cc');
                }
            } else {
                if ($cc != '') {
                    $recipients[] = array('target_type' => 'EMAIL', 'target' => $cc, 'x_details' => 'cc');
                }
            }
            if (is_array($bcc)) {
                foreach ($bcc as $recipient) {
                    $recipients[] = array('target_type' => 'EMAIL', 'target' => $recipient, 'x_details' => 'bcc');
                }
            } else {
                if ($bcc != '') {
                    $recipients[] = array('target_type' => 'EMAIL', 'target' => $bcc, 'x_details' => 'bcc');
                }
            }
            $attachments = array();
            if ($files != null)
                foreach ($files as $path) {
                    $attachments[] = array(
                        'path' => $path,
                        'name' => basename($path)
                    );
                }
            $messaging = new Model_Messaging();
            return $messaging->send(
                'email',
                null,
                $from,
                $recipients,
                count($attachments) ? array('content' => $message, 'attachments' => $attachments) : $message,
                $subject
            );
        } else {
            // compile RAW Email for local sending
            // CR/LF
            $CRLF = "\n";

            // If attachments, a mixed email will be generated
            $mixed_email = ($files !== null AND is_array($files) AND count($files) > 0);

            // Generate a random hash to append to each boundary
            $random_hash = md5(time());

            // Boundaries
            $mixed_boundary = "mixed-boundary-$random_hash";
            $alternative_boundary = "alternative-boundary-$random_hash";

            // Headers
            $headers = "MIME-Version: 1.0" . $CRLF;
            $headers .= ($mixed_email) ? "Content-Type: multipart/mixed; boundary=\"$mixed_boundary\"" . $CRLF : "Content-Type: multipart/alternative; boundary=\"$alternative_boundary\"" . $CRLF;
            $headers .= "From: $from" . $CRLF;
            $headers .= ($cc == null OR (is_array($cc) AND count($cc) == 0)) ? '' : "Cc: " . (is_array($cc) ? implode(',',
                    $cc) : $cc) . $CRLF;
            $headers .= ($bcc == null OR (is_array($bcc) AND count($bcc) == 0)) ? '' : "Bcc: " . (is_array($bcc) ? implode(',',
                    $bcc) : $bcc) . $CRLF;

            // Mixed Begin
            $content = ($mixed_email) ? "--$mixed_boundary" . $CRLF : '';
            $content .= ($mixed_email) ? "Content-Type: multipart/alternative; boundary=\"$alternative_boundary\"" . $CRLF : '';
            $content .= ($mixed_email) ? $CRLF : '';

            // Text Plain Message
            $content .= "--$alternative_boundary" . $CRLF;
            $content .= "Content-Type: text/plain; charset=UTF-8" . $CRLF;
            $content .= "Content-Transfer-Encoding: 8bit" . $CRLF;

            $content .= $CRLF;
            $content .= strip_tags($message) . $CRLF;
            $content .= $CRLF;

            // HTML Message
            $content .= "--$alternative_boundary" . $CRLF;
            $content .= "Content-Type: text/html; charset=UTF-8" . $CRLF;
            $content .= "Content-Transfer-Encoding: 8bit" . $CRLF;

            $content .= $CRLF;
            $content .= $message . $CRLF;
            $content .= $CRLF;

            // Alternative End
            $content .= "--$alternative_boundary--" . $CRLF;
            $content .= $CRLF;

            // Attach files
            if ($mixed_email) {
                foreach ($files as $file) {
                    if (is_file($file)) {
                        // Get the file details
                        $name = basename($file);
                        $data = chunk_split(base64_encode(file_get_contents($file)), 76, $CRLF);

                        // Attach it to the email
                        $content .= "--$mixed_boundary" . $CRLF;
                        $content .= "Content-Type: application/octet-stream; name=\"$name\"" . $CRLF;
                        $content .= "Content-Description: $name" . $CRLF;
                        $content .= "Content-Disposition: attachment; filename=\"$name\"" . $CRLF;
                        $content .= "Content-Transfer-Encoding: base64" . $CRLF;

                        $content .= $CRLF;
                        $content .= $data;
                        $content .= $CRLF;
                    }
                }
            }

            // Mixed End
            $content .= ($mixed_email) ? "--$mixed_boundary--" . $CRLF : '';

            // check is SMTP auth option is active
            $smtp_server = Settings::instance()->get('smtp_server');
            if (!empty($smtp_server) AND strlen($smtp_server) > 1) {
                // SMTP auth is active (server entry completed) then use
                return (BOOL)$mail_sent = Email::send($from, $to, $cc, $bcc, $subject, $message, $html = true, $files);

            } else // else default to local machine sender
            {
                if (!mail(is_array($to) ? implode(',', $to) : $to, $subject, $content, $headers, '-f' . $from)) {
                    IbHelpers::set_message("We could not sent your email due to local php mail issues", 'error');
                    return false;
                } else {
                    // for tracking add an activity record for successful email sent
                    $activity = new Model_Activity();
                    $activity
                        ->set_item_type('user')
                        ->set_action('email')
                        ->set_item_id($subject)
                        ->set_user_id(Request::$client_ip)
                        ->save();
                    return true;
                }
            }
        }
    }

    public static function breadcrumb_navigation($delimeter = '»')
    {

        // $_SERVER['REQUEST_URI' HOLDS Pluses and some URL Encoded EMPTY_SPACES, which when urldecode-(d) are messing up the product's name and It CANNOT BE FOUND in the DB
//		$parsed_url = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
        // Use SCRIPT_URL or SCRIPT_URI either will work. @NOTE: the SCRIPT_URL has the same Structure as the REQUEST_URI, ONLY difference is these URL encoded empty spaces
        $parsed_url = explode('/', trim($_SERVER['SCRIPT_URL'], '/'));
        $localisation_used = Settings::instance()->get('localisation_content_active');
        $breadcrumb_builder = '';
        // If localisation is active on the front end, the first portion of the parsed URL will be the language ID.
        if ($localisation_used AND isset($parsed_url[0])) {
            $breadcrumb_builder .= '/' . $parsed_url[0];
            unset($parsed_url[0]);
        }
        $last_crumb = end($parsed_url);

        //Skip FIRST Breadcrumbs generation, as we DON't want to show it when we are in the First breadcrumb
        if (count($parsed_url) > 1) {
            foreach ($parsed_url as $breadcrumb) {
                $breadcrumb_builder .= '/' . $breadcrumb;
                $crumb_title = str_replace('.html', '', $breadcrumb);
                if ($breadcrumb != $last_crumb) {
                    echo '<a href="', $breadcrumb_builder, '">';
                }
                echo __(ucfirst(str_replace(array('-', '_'), array(' ', ' '), $crumb_title)));
                if ($breadcrumb != $last_crumb) {
                    echo '</a>';
                    echo '<span class="delimiter">', $delimeter, '</span>';
                }
            }
        }

    }

    /**
     * @param Request $request
     * @param string $breadcrumbs
     * @param string $home_string
     * @param bool $capitalize_first
     * @return array
     */
    public static function generate_breadcrumbs($breadcrumbs = NULL, $request = NULL, $home_string = 'Home', $capitalize_first = TRUE)
    {
        $request = ($request === NULL) ? Request::$initial : $request;

        $breadcrumbs = array_filter(explode('/', isset($breadcrumbs) ? $breadcrumbs : $request->url()));
        $result = array();

        // Home
        $result[] = array
        (
            'title' => $home_string,
            'url' => '/'
        );

        // Breadcrumbs
        if (count($breadcrumbs) > 0) {
            $url = URL::site();

            foreach ($breadcrumbs as $bc) {
                $url .= self::generate_friendly_url($bc) . '/';

                $result[] = array
                (
                    'title' => $capitalize_first ? ucfirst($bc) : $bc,
                    'url' => $url,
                );
            }
        }

        return $result;
    }

    /**
     * Generates an unsorted list from the $breadcrumbs array. The result will be an HTML like this:
     *
     *     <ul class="$ul_class">
     *         <li><a href="#">Home</a></li>
     *         <li><a href="#">Category 1</a></li>
     *         <li><a href="#">Category 2</a></li>
     *         ...
     *         <li>Current Item</li>
     *     </ul>
     *
     * @param array $breadcrumbs
     * @param string $ul_class
     * @return string
     */
    public static function render_breadcrumbs($breadcrumbs, $ul_class = '')
    {
        $list = '';

        if (is_array($breadcrumbs) and count($breadcrumbs) > 0) {
            $list .= ((mb_strlen($ul_class) > 0) ? '<ul class=">' . $ul_class . '">' : '<ul>') . PHP_EOL;

            for ($i = 0; $i < count($breadcrumbs) - 1; $i++) {
                $link = $breadcrumbs[$i];

                $list .= '<li><a href="' . $link['url'] . '">' . $link['title'] . '</a></li>' . PHP_EOL;
            }

            $link = $breadcrumbs[$i];

            $list .= '<li>' . $link['title'] . '</li>' . PHP_EOL;
            $list .= '</ul>' . PHP_EOL;
        }

        return $list;
    }

    /**
     * @param $mapping
     */
    public static function set_content_mapping($mapping)
    {
        if (isset(self::$content_mapping)) {
            self::$content_mapping = array_merge(self::$content_mapping, $mapping);
        } else {
            self::$content_mapping = $mapping;
        }
    }

    /**
     * @param $content
     * @return string
     */
    public static function get_content($content)
    {
        return isset(self::$content_mapping[$content]) ? Request::factory(self::$content_mapping[$content])->execute() : '';
    }

    /**
     * @param $url
     * @return string
     */
    public static function generate_friendly_url($url)
    {
        return mb_strtolower(preg_replace('/%../', '', urlencode(preg_replace('/ /', '-', $url))));
    }

    public static function breadcrumb_navigation_v2($delimeter = '»', $spacer = ' ')
    {

        $parsed_url = explode('/', trim($_SERVER['REQUEST_URI'], '/'));
        foreach ($parsed_url as $purl => $_value) {
            if (strpos($_value, "?") !== false) {
                unset($parsed_url[$purl]);
            } else {
                $parsed_url[$purl] = str_replace('+', ' ', $_value);
            }
        }
        $breadcrumb_builder = '';
        $last_crumb = end($parsed_url);
        //Skip FIRST Breadcrumbs generation, as we DON't want to show it when we are in the First breadcrumb
        if (count($parsed_url) > 0) {
            foreach ($parsed_url as $breadcrumb) {
                if ($breadcrumb == 'booking-form' OR $breadcrumb == 'checkout' OR $breadcrumb == 'course-detail') {
                    $breadcrumb = 'course-list';
                }
                $breadcrumb_builder .= '/' . $breadcrumb;
                $crumb_title = str_replace('.html', '', urldecode($breadcrumb));
                if ($breadcrumb != $last_crumb) {
                    echo '<a href="', $breadcrumb_builder, '.html">';
                }
                if ($breadcrumb == $last_crumb AND isset($_GET['id']) AND $parsed_url[0] != 'course-detail') {
                    $id = $_GET['id'];
                    $query = DB::select('name')->from('plugin_courses_schedules')->where('id', '=', $id)->and_where('delete', '=', 0)->and_where('publish', '=', 1)->execute()->as_array();
                    if (isset($query[0])) {
                        $name = $query[0]['name'];
                        echo str_replace(array('-', '_'), array(' ', ' '), $name);
                    }
                } else {
                    echo ucfirst(str_replace(array('-', '_'), array(' ', ' '), $crumb_title));
                }
                if ($breadcrumb != $last_crumb) {
                    echo '</a>';
                    echo '<span class="delimiter">', $spacer, $delimeter, $spacer, '</span>';
                }
            }
        }

    }

    /**
     * @param $url
     * @return string
     * this function should return a "true" or "false" if 1 or 0 values are passed.
     */
    public static function int_to_bool($value)
    {
        if ($value == 1) {
            return "true";
        } else
            return "false";
    }

    public static function t_tag($txt)
    {
        preg_match_all('#<t>(.*?)</t>#i', $txt, $tags);
        //print_r($tags);die();
        foreach ($tags[0] as $i => $tag) {
            $txt = str_replace($tag, __($tags[1][$i]), $txt);
        }
        return $txt;
    }


    /**
     * This function will return a string part of the current URL between '/' symbols
     * @param $part
     * @return mixed
     */
    public static function get_cur_url_part($part = 1)
    {
        $current_url = Request::current()->url() . URL::query();
        // get current url
        $current_url_parts = parse_url($current_url);

        $current_url_parts_exp = explode('/', $current_url_parts["path"]);
        $ret_part = $current_url_parts_exp[$part];

        return $ret_part;
    }

    /**
     * This function will check if a given string exists in the current url
     * @param $part
     * @return mixed
     */
    public static function find_str_in_url($search)
    {
        $search = strtolower($search);
        $current_url = Request::current()->url() . URL::query();

        // check if the search param exists in the current web address
        if (strpos($current_url, $search) !== false) {
            return true;
        } else
            return false;
    }

    /**
     * Convert a string to a URL slug
     */
    public static function slugify($string)
    {
        return strtolower(preg_replace('/[^a-z0-9]+/i', '-', $string));
    }

    // Convert a timestamp to a relative time. e.g. "1 hour ago"
    public static function relative_time($timestamp)
    {
        if ($timestamp == null) {
            return '';
        }
        // Convert the timestamp to seconds since 1970, if it is not already of that format
        if (!ctype_digit($timestamp)) {
            $timestamp = strtotime($timestamp);
        }

        $seconds_ago = time() - $timestamp; // find how far the timestamp is from now

        if ($seconds_ago == 0) // the time is now
        {
            return 'now';
        } elseif ($seconds_ago > 0) // the time is in the past
        {
            $days_ago = floor($seconds_ago / 86400); // convert seconds to days rounded down

            // If the time is today, say how many minutes or hours ago it is
            if ($days_ago == 0) {
                if ($seconds_ago < 60) return 'just now';
                elseif ($seconds_ago < 120) return '1 minute ago';
                elseif ($seconds_ago < 3600) return floor($seconds_ago / 60) . ' minutes ago';
                elseif ($seconds_ago < 7200) return '1 hour ago';
                elseif ($seconds_ago < 86400) return floor($seconds_ago / 3600) . ' hours ago';
            } // Display an appropriate message for number of days, weeks or months, depending on the amount of days
            elseif ($days_ago == 1) return 'yesterday';
            elseif ($days_ago < 7) return $days_ago . ' days ago';
            elseif ($days_ago < 14) return 'last week';
            elseif ($days_ago < 31) return ceil($days_ago / 7) . ' weeks ago';
            elseif ($days_ago < 60) return 'last month';
            // For 60 days or more, give the month and year
            return date('F Y', $timestamp);
        } else // the time is in the future
        {
            $seconds_ahead = abs($seconds_ago); // convert to a positive number

            // Logic is similar to the "past" section.
            $days_ahead = floor($seconds_ahead / 86400);
            if ($days_ahead == 0) {
                if ($seconds_ahead < 60) return 'within a minute';
                if ($seconds_ahead < 120) return 'in a minute';
                if ($seconds_ahead < 3600) return 'in ' . floor($seconds_ahead / 60) . ' minutes';
                if ($seconds_ahead < 7200) return 'in an hour';
                if ($seconds_ahead < 86400) return 'in ' . floor($seconds_ahead / 3600) . ' hours';
            }
            if ($days_ahead == 1) return 'tomorrow';
            if ($days_ahead < 4) return date('l', $timestamp);
            if ($days_ahead < 7 + (7 - date('w'))) return 'next week';
            if (ceil($days_ahead / 7) < 4) return 'in ' . ceil($days_ahead / 7) . ' weeks';
            if (date('n', $timestamp) == date('n') + 1) return 'next month';
            return date('F Y', $timestamp);
        }
    }

    // Get the relative time, with the time and date as a tooltip
    public static function relative_time_with_tooltip($timestamp)
    {
        if (!$timestamp) {
            return '';
        }

        return '<time
            data-toggle="tooltip"
            data-placement="top"
            datetime="' . $timestamp . '"
            title="' . date('D d/M/Y H:i:s', strtotime($timestamp)) . '"
            >
            <span class="hidden">' . $timestamp . '</span>' . // This is for datatable sorting
        self::relative_time($timestamp) .
        '</time>';

    }

    /**
     * Convert a timestamp to the standardised format for human reading
     * Requested format: https://ideabubble.atlassian.net/wiki/spaces/CC/pages/703430761/Attendance?focusedCommentId=728236035#comment-728236035
     *
     * @param $timestamp string   The date time in ISO format or a unix timestamp
     * @param $args      array    Customisation parameters
     *                              $time (bool) If the output should contain a time (as apposed to just a date)
     * @return           string   The time in the standardised format
     */
    public static function formatted_time($timestamp, $args = [])
    {
        if ($timestamp) {
            $args['time'] = isset($args['time']) ? $args['time'] : true;

            $format = 'jS D M Y';
            $format = $args['time'] ? $format . ' H:i' : $format;

            return date($format, strtotime($timestamp));
        } else {
            return '';
        }
    }

    /*
     * Convert a number of hours in decimal to a string showing the number of hours and minutes
     * e.g. 1.5 -> '1h 30m'
    */
    public static function hours_to_time($number)
    {
        $hours   = floor($number);
        $minutes = floor(($number - $hours) * 60);

        if ($hours == 0 && $minutes == 0) {
            return '0m';
        } else {
            return trim(($hours > 0 ? $hours.'h ' : '').($minutes > 0 ? $minutes.'m' : ''));
        }
    }

    public static function seconds_to_time($total_seconds, $format = 'short')
    {
        $hours   = floor($total_seconds / 60 / 60);
        $minutes = floor(($total_seconds - $hours * 60 * 60) / 60);
        $seconds = floor($total_seconds % 60);

        switch ($format) {
            case 'long':
                $h_label = $hours   == 1 ? ' hour'   : ' hours';
                $m_label = $minutes == 1 ? ' minute' : ' minutes';
                $s_label = $seconds == 1 ? ' second' : ' seconds';
                break;

            case 'medium':
                $h_label = 'hr';
                $m_label = 'min';
                $s_label = 'sec';
                break;

            case 'short':
            default:
                $h_label = 'h';
                $m_label = 'm';
                $s_label = 's';
                break;
        }

        $formatted_time  = $hours   ? $hours.$h_label.' '   : '';
        $formatted_time .= $minutes ? $minutes.$m_label.' ' : '';
        $formatted_time .= $seconds ? $seconds.$s_label.''  : '';

        $formatted_time  = $formatted_time ? trim($formatted_time) : '0s';

        return $formatted_time;
    }

    /**
     * Get the number of days from a formatted string
     * e.g. 3d 4h -> 3.5d, for 8-hour days
     */
    public static function time_to_days($time_string, $day_length)
    {
        if (is_numeric($time_string)) {
            return $time_string;
        }

        $days = 0;
        if (preg_match('/([0-9])+d/', $time_string, $matches)) {
            $days += $matches[0];
        }
        if (preg_match('/([0-9])+h/', $time_string, $matches)) {
            $days += $matches[0] / $day_length;
        }
        if (preg_match('/([0-9])+m/', $time_string, $matches)) {
            $days += $matches[0] / $day_length / 60;
        }
        if (preg_match('/([0-9])+s/', $time_string, $matches)) {
            $days += $matches[0] / $day_length / 60 / 60;
        }

        return $days;
    }

    /**
     * Get the number of hours from a formatted string
     * e.g. 4h 30m -> 4.5
     */
    public static function time_to_hours($time_string)
    {
        if (is_numeric($time_string)) {
            return $time_string;
        }

        $hours = 0;
        if (preg_match('/([0-9])+h/', $time_string, $matches)) {
            $hours += $matches[0];
        }
        if (preg_match('/([0-9])+m/', $time_string, $matches)) {
            $hours += $matches[0] / 60;
        }
        if (preg_match('/([0-9])+s/', $time_string, $matches)) {
            $hours += $matches[0] / 60 / 60;
        }

        return $hours;
    }

    // Convert a verb to past tense
    public static function verb_past_tense($verb)
    {
        $verb = trim($verb);
        $vowels = array('a', 'e', 'i', 'o', 'u');
        $consonants = array('b', 'c', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'm', 'n', 'p', 'q', 'r', 's', 't', 'v', 'x', 'y', 'z');

        // If two words are provided, assume the first word is the verb.
        // Convert it to past tense and append the remaining words. e.g. "log in" -> "logged in"
        $words = explode(' ', $verb);
        $extra = '';

        if (count($words) > 1) {
            $verb = $words[0];
            unset($words[0]);
            $extra = implode(' ', $words);
        }

        if ($verb == '') {
            $return = '';
        } // Irregular verbs, used in the system
        elseif (strtolower($verb) == 'send') {
            $return = 'sent';
        } // If the verb ends in "e", add "-d". e.g. create -> created
        elseif (substr($verb, -1) == 'e') {
            $return = $verb . 'd';
        } // If the verb ends in a consonant, followed by "y", change "y" to "i" and add "-ed". e.g. modify -> modified
        elseif (strlen($verb) > 2 AND substr($verb, -1) == 'y' AND in_array(substr($verb, -2, 1), $consonants)) {
            $return = substr_replace($verb, 'i', -1, 1) . 'ed';
        } // If the verb ends in a vowel followed by a consonant, but not "w" or "y", double the consonant and add "-ed". e.g. log -> logged
        elseif (strlen($verb) > 2 AND in_array(substr($verb, -2, 1), $vowels) AND in_array(substr($verb, -1), $consonants) AND substr($verb, -1) != 'w' AND substr($verb, -1) != 'y') {
            $return = $verb . substr($verb, -1) . 'ed';
        } // Otherwise add "-ed"
        else {
            $return = $verb . 'ed';
        }

        return trim($return . ' ' . $extra);
    }

    /*
    the standard php function array_diff_assoc() does not handle multi dimensional arrays.
    so this is needed
    */
    public static function array_diff_assoc_recursive($a1, $a2)
    {
        $diff = array();
        foreach ($a1 as $key => $value) {
            if (!isset($a2[$key])) {
                $diff[$key] = $value;
            } else {
                if (is_array($value)) {
                    $tmp = self::array_diff_assoc_recursive($value, $a2[$key]);
                    if (count($tmp) > 0) {
                        $diff[$key] = $tmp;
                    }
                } else {
                    if ($value != $a2[$key]) {
                        $diff[$key] = $value;
                    }
                }
            }
        }
        return $diff;
    }

    public static function in_array_r($test, $array)
    {
        foreach ($array as $value) {
            if (is_array($value)) {
                if (self::in_array_r($test, $value)) {
                    return true;
                }
            } else {
                if ($test == $value) {
                    return true;
                }
            }
        }
        return false;
    }

    // Replace a short tag with the content generated by its corresponding feed.
    // e.g. {form-ContactUs} will be replaced by a form builder form, whose name is "ContactUs".
    public static function expand_short_tags($content)
    {
        // Search for content inside curly brackets
        preg_match_all('/{(.*?)}/', $content, $matches);
        if (is_array($matches) AND isset($matches[1]) AND !empty($matches[1])) {
            foreach ($matches[1] AS $key => $match) {
                // Content before the first "-" is the name of the short tag
                // Content after is the identifier
                // If the "-" is within quotes, it is not used to split the input
                $values = str_getcsv(htmlspecialchars_decode($match), '-', '"'); // handle short tags like abcdef-123
                if (count($values) <= 1) { // handle short tags like abcdef:123
                    $values = str_getcsv(htmlspecialchars_decode($match), ':', '"');
                }
                if (count($values) > 1) {
                    // Get the function corresponding to the tag
                    $q = DB::select('function_call', 'content')->from('engine_feeds')->where('short_tag', '=', $values[0])->and_where('publish', '=', 1)->and_where('deleted', '=', 0)->execute()->current();
                    if ($q['function_call']) {
                        $tag = explode(',', $q['function_call']);
                        // Run the function using the identifier as the argument
                        // Replace the instance of the tag in the supplied text with the content returned by the function
                        if (method_exists($tag[0], $tag[1]) AND is_callable($tag[0] . "::" . $tag[1])) {
                            // Remove surrounding <p></p> tags, if any
                            $content = preg_replace('#\<p\>(\{.*\})<\/p\>#', '\1', $content);
                            $value2  = (isset($values[2]) ? $values[2] : null);
                            $content = str_replace($matches[0][$key], call_user_func($tag[0] . "::" . $tag[1], $values[1], $value2), $content);
                        }
                    }
                    else if ($q['content']) {
                        $content = preg_replace('#\<p\>(\{.*\})<\/p\>#', '\1', $content);
                        $content = str_replace($matches[0][$key], $q['content'], $content);
                    }
                    // Document tags are only used in document generation. Remove the tags as there is no replacement needed.
                    else if ($values[0] == 'document') {
                        // Remove any surrounding tags, not just <p></p>.
                        $content = preg_replace('#\<[^>]*\>(\{document-.*\})<\/[^>]*\>#', '', $content);
                    }
                }
            }
        }
        return $content;
    }

    public static function parse_block_editor($page_content = '')
    {
        $doc = new DOMDocument();
        libxml_use_internal_errors(true); // do not generate errors for not wellformed htmls
        $doc->strictErrorChecking = false;
        if (trim($page_content) && $doc->loadHtml($page_content)) {
            $xpath = new DOMXPath($doc);

            // Find all block editor sections
            $blocks = $xpath->query('//div[contains(concat(" ", @class, " "), " simplebox ")]');

            $changes = false;
            foreach ($blocks as $i => $block) {
                // Find the div containing the background image.
                // Set the contained image as the CSS background. Then remove the div containing the image.
                $images = $xpath->query('.//div[@class="simplebox-background_image"] //img', $blocks[$i]);

                $background_image = ($images->length > 0) ? $images->item(0)->getAttribute('src') : '';

                if ($background_image) {
                    $current_style = rtrim($blocks[$i]->getAttribute('style'), ';').';';
                    $blocks[$i]->setAttribute('style', $current_style.'background-image: url(\''.$background_image.'\');');
                    $images->item(0)->parentNode->removeChild($images->item(0));
                    $changes = true;
                }
            }

            // Toolbars are only used in the page editor. Remove them when displaying on the front end.
            $toolbars = $xpath->query('//div[@class="simplebox-content-toolbar"]');
            foreach ($toolbars as $toolbar) {
                $toolbar->parentNode->removeChild($toolbar);
                $changes = true;
            }

            // If the titles are empty, remove them
            $titles = $xpath->query('//div[@class="simplebox-title"]');
            foreach ($titles as $title) {
                $html = "";
                foreach ($title->childNodes as $child) {
                    $html .= $title->ownerDocument->saveHTML($child);
                }
                $html = trim($html, " \t\n\r\0\x0B\xC2\xA0");

                if ($html == '' || $html == "<p>\xC2\xA0</p>") {
                    $title->parentNode->removeChild($title);
                    $changes = true;
                }
            }

            // Save changes, if any
            if ($changes) {
                $page_content = $doc->saveHTML();
                // Using "LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD" to remove the DOCTYPE, html and body
                // ... is not supported in live 1's libXML version. A regex replace is used instead here.
                $page_content = preg_replace('/^<!DOCTYPE.+?>/', '', str_replace( array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $page_content));
            }
        }

        return $page_content;
    }

    public static function parse_page_content($content)
    {
        return Model_Localisation::get_ctag_translation(Ibhelpers::parse_block_editor(Ibhelpers::expand_short_tags($content)), I18n::$lang);
    }

    public static function save_csv($data, $filename, $map = null, $delimiter = ',', $enclosure = '"')
    {
        $fd = fopen($filename, 'w+');
        if (!$fd) {
            return false;
        } else {
            if (flock($fd, LOCK_EX)) {
                ftruncate($fd, 0);
                if ($map) {
                    fputcsv($fd, $map, $delimiter, $enclosure);
                } else {
                    if (count($data)) {
                        fputcsv($fd, array_keys($data[0]), $delimiter, $enclosure);
                    }
                }

                foreach ($data as $record) {
                    if ($map) {
                        $csv_record = array();
                        foreach ($map as $from => $to) {
                            $csv_record[$to] = $record[$from];
                        }
                    } else {
                        $csv_record = $record;
                    }
                    fputcsv($fd, $csv_record, $delimiter, $enclosure);
                }
                fclose($fd);
                return true;
            } else {
                fclose($fd);
                return false;
            }
        }
    }

    public static function load_csv($file, $map = null, $first_line_is_keys = true, $delimiter = ',', $enclosure = '"')
    {
        $fd = fopen($file, "r");
        if ($fd) {
            if (flock($fd, LOCK_SH)) {
                $data = array();
                $first_line = true;
                $keys = null;
                while (!feof($fd)) {
                    $row = fgetcsv($fd, 0, $delimiter, $enclosure);
                    if ($first_line) {
                        $first_line = false;
                        if ($first_line_is_keys) {
                            $keys = array_flip($row);
                            continue;
                        } else {
                            $keys = array_keys($row);
                        }
                    }
                    if ($map != null) {
                        $mapped_row = array();
                        foreach ($map as $from => $to) {
                            $mapped_row[$to] = $row[$keys[$from]];
                        }
                        $data[] = $row;
                    } else {
                        $data[] = $row;
                    }
                }
                flock($fd, LOCK_UN);
                fclose($fd);
                return $data;
            } else {
                fclose($fd);
                return false;
            }
        } else {
            return false;
        }
    }

    public static function csvGetColumns($csvfile, $encoding, $delimiter = ',', $enclosure = '"')
    {
        $csv = fopen($csvfile, "r");
        if ($csv) {
            $columns = fgetcsv($csv, 0, $delimiter, $enclosure);
            if (strtolower($encoding) != 'utf-8') {
                foreach ($columns as $i => $column) {
                    $columns[$i] = iconv($encoding, 'utf-8', $column);
                }
            }
            return $columns;
            fclose($csv);
        } else {
            return false;
        }
    }

    public static function csvTransferToTable($csvfile, $table, $columnMap, $keyColumn, $fixedValues = array(), $encoding = 'utf-8', $delimiter = ',', $enclosure = '"')
    {
        $csv = fopen($csvfile, "r");
        if ($csv) {

            // unset empty targets
            foreach ($columnMap as $source => $target) {
                if ($target == '') {
                    unset ($columnMap[$source]);
                }
            }

            // columns in source
            $columns = fgetcsv($csv, 0, $delimiter, $enclosure);
            if (strtolower($encoding) != 'utf-8') {
                foreach ($columns as $i => $column) {
                    $columns[$i] = iconv($encoding, 'utf-8', $column);
                }
            }
            $rcolumns = array_flip($columns);

            $report = array();

            try {
                Database::instance()->begin();
                while (($row = fgetcsv($csv)) != false) {
                    $keyValue = $row[$rcolumns[$columnMap[$keyColumn]]];
                    $exists = DB::select(DB::expr('count(*) as cnt'))
                        ->from($table)
                        ->where($keyColumn, '=', $keyValue)
                        ->execute()
                        ->get('cnt');
                    if ($exists > 0) {
                        $report[] = array('key' => $keyValue, 'action' => 'skip');
                        // skip
                    } else {
                        if (is_array($fixedValues)) {
                            $ivalues = $fixedValues;
                            $icolumns = array_keys($fixedValues);
                        } else {
                            $ivalues = array();
                            $icolumns = array();
                        }

                        foreach ($columnMap as $tableColumn => $csvColumn) {
                            $ivalues[$tableColumn] = $row[$rcolumns[$csvColumn]];
                            $icolumns[] = $tableColumn;
                        }
                        $insertResult = DB::insert($table, $icolumns)
                            ->values($ivalues)
                            ->execute();
                        $report[] = array('key' => $keyValue, 'action' => 'insert', 'id' => $insertResult[0]);
                    }
                }
                Database::instance()->commit();
            } catch (Exception $exc) {
                Database::instance()->rollback();
                fclose($csv);
                throw $exc;
            }

            fclose($csv);
            return $report;
        } else {
            return false;
        }
    }

    /**
     * Convert a time string to number of seconds
     * @param $string - e.g. '1h 10m 5s'
     * @return float - the number of seconds
     */
    public static function duration_to_seconds($string)
    {
        $string = trim(strtolower($string));
        if (trim($string) == '') return 0;                 // blank input, return 0
        if (is_numeric($string)) return (float)$string; // input is purely numeric, assume it's the number of seconds

        $string = ' ' . $string; // Add a space to the start to make it easier to recognise the first number

        // Look for space, one or more numbers, zero or more spaces and then a word representing a unit of time...
        // ... and get the numbers for each unit of time
        preg_match('/\s([0-9]+)\s*(d|day|days)/', $string, $d); // Look for numbers before "d", "day" or "days"
        preg_match('/\s([0-9]+)\s*(h|hour|hours)/', $string, $h); // Look for numbers before "h", "hour" or "hours"
        preg_match('/\s([0-9]+)\s*(m|minute|minutes)/', $string, $m); // Look for numbers before "m", "minute" or "minutes"
        preg_match('/\s([0-9]+)\s*(s|second|seconds)/', $string, $s); // Look for numbers before "s", "second" or "seconds"

        // Get the numbers from the above regex checks
        $days = empty($d[1]) ? 0 : $d[1];
        $hours = empty($h[1]) ? 0 : $h[1];
        $minutes = empty($m[1]) ? 0 : $m[1];
        $seconds = empty($s[1]) ? 0 : $s[1];

        // Convert everything to seconds
        return (($days * 24 + $hours) * 60 + $minutes) * 60 + $seconds;
    }

    // Run htmlspecialchars on each item in an array
    public static function htmlspecialchars_array(&$variable)
    {
        foreach ($variable as &$value) {
            if (!is_array($value)) {
                $value = htmlspecialchars($value);
            } else {
                self::htmlspecialchars_array($value);
            }
        }
    }

    // Run strip_tags on each item in an array
    public static function strip_tags_array(&$variable)
    {
        if (!is_array($variable)) $variable = strip_tags($variable);
        else foreach ($variable as &$value) self::strip_tags_array($value);
    }


    public static function get_path_from_url($url)
    {
        if (preg_match('#^((https|http)(://))?([^/]+/)?(.*)$#i', $url, $parts)) {
            return $parts[5];
        } else {
            return $url;
        }
    }

    public static function twitter_widget($id, $username = NULL, $js = TRUE)
    {
        if (!$username AND !is_numeric($id)) {
            $username = $id;
            $id = NULL;
        }

        $return = '<a class="twitter-timeline" data-height="600"';
        if ($id) {
            $return .= ' data-id="' . $id . '"';
        }
        $return .= ' href="https://twitter.com/' . $username . '">Tweets by @' . $username . '</a>';

        if ($js) {
            $return .= '<script async src="//platform.twitter.com/widgets.js" charset="utf-8"></script>';
        }

        return $return;

    }

    // Embed the AddThis toolbox
    public static function addthis_toolbox()
    {
        $setting_html = trim(Settings::instance()->get('addthis_toolbox_html'));

        if ($setting_html) {
            // Embed the HTML specified in the settings
            return $setting_html;
        } else {
            // Otherwise embed the default view
            $addthis_id = Settings::instance()->get('addthis_id');
            $code_path  = APPPATH . 'assets/shared/img/social/addthis/';
            $url_path   = '/engine/shared/img/social/addthis/';

            return View::factory('snippets/addthis_toolbox')->set(compact('addthis_id', 'code_path'));
        }
    }

    public static function iconv_array($a, $in_charset = 'iso-8859-15', $out_charset = 'utf-8')
    {
        $b = array();
        foreach ($a as $key => $value) {
            if (is_array($value)) {
                $b[$key] = self::iconv_array($value);
            } else {
                $b[$key] = @iconv($in_charset, $out_charset, $value);
            }
        }
        return $b;
    }

    public static function fix_target_link($link)
    {
        if (is_array($link)) {
            $link = current($link);
        }
        $add_blank = 0;
        $self = str_ireplace('www.', '', $_SERVER['HTTP_HOST']);
        if (!preg_match('#target=([^\s\>]*?)#i', $link)) { // replace if target is not set already
            if (preg_match('#href=("|\')(http|https)://(www\.)?(.+?)([\\:\\?\\/].*)?("|\')#i', $link, $parse_host)) {
                if (strcasecmp($parse_host[4], $_SERVER['HTTP_HOST'])) {
                    $add_blank = 1;
                }
            }

            if (preg_match('#\.(pdf|docx|doc|xlsx|xls|odt|ods|zip|rar|gz|tgz)#i', $link, $parse_extension)) {
                $add_blank = 2;
            }
        }
        if ($add_blank) {
            $link = str_replace(array('<a', '<A'), array('<a target="_blank"', '<A target="_blank"'), $link);
        }
        return $link;
    }

    public static function is_external($url)
    {
        $current_domain = $_SERVER['HTTP_HOST'];
        $components = parse_url($url);
        if (empty($components['host'])) return false;  // URL is relative
        if (strcasecmp($components['host'], $current_domain) === 0) return false; // url host looks exactly like the local host
        return strrpos(strtolower($components['host']), '.'.$current_domain) !== strlen($components['host']) - strlen('.'.$current_domain); // check if the url host is a subdomain
    }

    public static function clear_cc1(&$value, $key)
    {
        $value = self::clear_cc($value);
    }

    public static function clear_cc($text)
    {
        if (is_array($text)) {
            array_walk_recursive($text, 'IbHelpers::clear_cc1');
            return $text;
        } else {
            $text_cleaned = preg_replace('/(\d\d\d\d)([0-9\-\s\+]){8,}(\d)/', '$1 * * * * $3', $text);
            return $text_cleaned;
        }
    }

    public static function get_login_logo()
    {
        $custom_logo = trim(Settings::instance()->get('login_form_logo'));

        if ($custom_logo) {
            $src = Model_Media::get_image_path($custom_logo, 'logos');
        }
        else {
            $src = URL::overload_asset('img/ib-logo-white.png');
        }

        return $src;
    }

    public static function has_ssl($url = null)
    {
        $url = empty($url) ? URL::base() : $url;

        // Get the domain from the URL
        $url       = trim($url, '/');
        $url       = (!preg_match('#^http(s)?://#', $url)) ? 'http://'.$url : $url;
        $url_parts = parse_url($url);
        $domain    = preg_replace('/^www\./', '', $url_parts['host']);

        $res    = false;
        $stream = @stream_context_create(array('ssl' => array('capture_peer_cert' => true)));
        $socket = @stream_socket_client('ssl://'.$domain.':443', $errno, $errstr, 30, STREAM_CLIENT_CONNECT, $stream);

        // If we got a ssl certificate we check here, if the certificate domain
        // matches the website domain.
        if ($socket) {
            $cont = stream_context_get_params($socket);
            $cert_ressource = $cont['options']['ssl']['peer_certificate'];
            $cert = openssl_x509_parse($cert_ressource);

            // Expected name has format "/CN=*.yourdomain.com"
            $namepart = explode( '=', $cert['name'] );

            // We want to correctly confirm the certificate even
            // for subdomains like "www.yourdomain.com"
            if (count($namepart) == 2) {
                $cert_domain  = trim($namepart[1], '*. ');
                $check_domain = substr($domain, -strlen($cert_domain));
                $res          = ($cert_domain == $check_domain);
            }

            // Check for alt names
            $res = (!$res && strpos($cert['extensions']['subjectAltName'], "DNS:$domain") !== false) ? true : $res;
        }

        return $res;
    }

    /**
     * Check if the logged-in user has a certain permission. If not, redirect them somewhere and display a message.
     *
     * @param $permissions mixed - String containing a single permission or array of permissions, one of which the user must have.
     * @param $redirect string - Send the user here if they do not have permission
     */
    public static function permission_redirect($permissions, $redirect = '/admin')
    {
        // Array of permissions. User must have at least one.
        if (is_array($permissions)) {
            $has_access = false;
            foreach ($permissions as $permission) {
                $has_access = $has_access || Auth::instance()->has_access($permission);
            }

            $message  = __(
                'You need access to at least one of the following permissions to perform this action: $1',
                ['$1' => '<code>'.implode('</code>, <code>', $permissions).'</code>']
            );
        }
        // Single permission. User must have it.
        else {
            $has_access = Auth::instance()->has_access($permissions);
            $message = __(
                'You need access to the $1 permission to perform this action.',
                ['$1' => '<code>'.$permissions.'</code>']
            );
        }

        if (!$has_access) {
            IbHelpers::set_message($message, 'warning popup_box');
            Request::current()->redirect($redirect);
        }
    }

    /**
     * Embed an SVG from the engine assets folder
     * This is useful, if the SVG is to be styled with CSS
     */
    public static function embed_svg($icon, $args = array())
    {
        $attributes  = 'data-icon="'.$icon.'"';
        $attributes .= (isset($args['width']))  ? ' width="'. $args['width']. '"' : '';
        $attributes .= (isset($args['height'])) ? ' height="'.$args['height'].'"' : '';

        try {
            $html = file_get_contents(ENGINEPATH.'application/assets/shared/img/icons/'.$icon.'.svg');
            $attributes .= (!empty($args['color'])) ? ' class="svg-color"' : '';

            if ($attributes) {
                $html = str_replace('<svg ', '<svg '.$attributes, $html);
            }

        } catch (Exception $e) {
            // If the SVG does not exist, show a broken image, rather than a code error
            $html = '<img src="'.URL::get_engine_assets_base().'img/icons/'.$icon.'.svg" '.$attributes.' alt="" />';
        }

        return $html;
    }

    /* Render an SVG from a spritesheet */
    public static function svg_sprite($icon, $args = array())
    {
        $attributes = '';
        $attributes .= (isset($args['width']))  ? ' width="'. $args['width']. '"' : '';
        $attributes .= (isset($args['height'])) ? ' height="'.$args['height'].'"' : '';
        return '
            <svg class="svg-sprite'.(!empty($args['color']) ? ' svg-color' : '').'"'.$attributes.'>
                <use xlink:href="#sprite-'.$icon.'"></use>
            </svg>';
    }

    /* Get the spritesheet for the current theme or the default spritesheet */
    public static function get_spritesheet()
    {
        $cms_skin  = Settings::instance()->get('cms_skin');
        if (file_exists(APPPATH.'assets/'.$cms_skin.'/img/spritesheets/icons.svg')) {
            $file = APPPATH.'assets/'.$cms_skin.'/img/spritesheets/icons.svg';
        } else {
            $file = APPPATH.'assets/shared/img/spritesheets/icons.svg';
        }

        return file_get_contents($file);
    }

    /**
     * Embed a video. Works by adding {video-$url} to a page
     *
     * @param $url - The full URL to a video (YouTube, Vimeo) or the file name or media ID of a locally uploaded video
     *
     */
    public static function render_video($url)
    {
        $url = strip_tags($url);

        // YouTube video
        if (strpos($url, 'youtu') !== false) {
            // Get the ID from the URL
            preg_match("#(?<=v=)[a-zA-Z0-9-]+(?=&)|(?<=v\/)[^&\n]+(?=\?)|(?<=v=)[^&\n]+|(?<=youtu.be/)[^&\n]+#", $url, $video_id);
            $video_id = isset($video_id[0]) ? $video_id[0] : '';
            $src = 'https://www.youtube.com/embed/'.$video_id.'?origin=https://plyr.io&amp;iv_load_policy=3&amp;modestbranding=1&amp;playsinline=1&amp;showinfo=0&amp;rel=0&amp;enablejsapi=1';

            $html_id = 'youtube-'.$video_id;
        }
        // Vimeo video
        else if (strpos($url, 'vimeo') !== false) {
            preg_match('/(https?:\/\/)?(www\.)?(player\.)?vimeo\.com\/([a-z]*\/)*([0-9]{6,11})[?]?.*/', $url, $video_id);
            $video_id = isset($video_id[5]) ? $video_id[5] : '';
            $src = 'https://player.vimeo.com/video/'.$video_id.'?loop=false&amp;byline=false&amp;portrait=false&amp;title=false&amp;speed=true&amp;transparent=0&amp;gesture=media';

            $html_id = 'vimeo-'.$video_id;
        }

        // YouTube or Vimeo
        if (!empty($src)) {
            $return = '<div class="ib-video-wrapper plyr__video-embed" id="video-'.$html_id.'">
                    <iframe src="'.$src.'" allowfullscreen allowtransparency allow="autoplay"></iframe>
                </div>';
        }
        // Uploaded video
        elseif (is_numeric($url)) {
            // Media ID
            $filepath = Model_Media::get_path_to_id($url);
            $return = '<div class="ib-video-wrapper" id="video-local-id-'.$url.'"><video controls style="width: 100%;"><source src="'.$filepath.'"></video></div>';
        } else {
            // File name
            $filepath = Model_Media::get_image_path($url, 'videos');
            $id = preg_replace('/\W+/','',strtolower($url)); // Make ID-attribute-friendly
            $return = '<div class="ib-video-wrapper" id="video-local-name-'.$id.'"><video controls style="width: 100%;"><source src="'.$filepath.'"></video></div>';
        }

        return $return;
    }

    /**
     * Embed an audio file. Works by adding {audio-$url} to a page
     *
     * @param $url - The filename or ID of the media item
     * @return string
     */
    public static function render_audio($url)
    {
        // Currently only local uploads are supported.
        if (is_numeric($url)) {
            // Media ID
            $filepath = Model_Media::get_path_to_id($url);
            $id = 'audio-local-id-'.preg_replace('/\W+/','',strtolower($url));
        } else {
            // File name
            $filepath = Model_Media::get_image_path($url, 'audios');
            $id = 'audio-local-name-'.preg_replace('/\W+/','',strtolower($url));
        }

        return '<div class="ib-audio-wrapper" id="'.$id.'"><audio class="w-100" controls src="'.$filepath.'">Your browser does not support the <code>audio</code> element.</audio></div>';
    }

    public static function runonce($fcall)
    {
        $runalready = DB::select('*')
            ->from('engine_runonce')
            ->where('fcall', '=', $fcall)
            ->execute()
            ->current();
        if (@$runalready['runat'] == null) {
            if ($runalready) {
                DB::update('engine_runonce')->set(array('runat' => date::now()))->where('fcall', '=', $fcall)->execute();
            } else {
                DB::insert('engine_runonce')->values(array('fcall' => $fcall, 'runat' => date::now()))->execute();
            }
            $fcall();
        }
    }
}
