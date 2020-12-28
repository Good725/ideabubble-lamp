<?php
defined('SYSPATH') OR die('No Direct Script Access');

/**
 * Created by PhpStorm.
 * User: yann
 * Date: 05/05/15
 * Time: 13:10
 */
class Controller_Admin_Documents extends Controller_Cms {


    // Array of system documents
    private $documents;
    // Redirect on or off
    private $redirect = TRUE;

    public function before()
    {
        parent::before();
    }

    /**
     * List the upload location folders
     */
    public function action_ajax_get_upload_locations()
    {
        $doc_model = new Model_Document();
        $save_to_options = $doc_model->get_folder_list();
        $this->template->body = View::factory('/admin/snippets/documents_upload_modal_form');
        $this->template->body->save_to_options = $save_to_options;
    }

    /**
     * Does document upload
     */
    public function action_ajax_upload_document()
    {
        // Get the post data
        $post_data = $this->request->post();

        // Handle uploaded files
        if ( ! empty($_FILES))
        {
            if (isset($post_data['doc_type']) AND $post_data['doc_type'] == 'image')
            {
                $storage_location = '/contacts/'.$post_data['contact_id'].'/';
                $types = array("jpeg", "png", "JPG", "dds", "gif", "jpg", "png", "psd", "tif", "tiff");
            }
            else
            {
                $storage_location = '/contacts/'.$post_data['contact_id'].'/';
                $types = 'all';
            }

            try
            {
                // Make the folder structure if needed
                // Acquire the settings for KT login and API URL
                $knowledgetree_data = Kohana::$config->load('config')->get('KnowledgeTree');
                // Create KT connection
                $kt_connection = New KTClient($knowledgetree_data['url']);
                $kt_connection->initSession($knowledgetree_data['username'], $knowledgetree_data['password']);
                Model_Document::initialise_directory_structure($kt_connection, $post_data['contact_id']);
                $message = IbHelpers::uploadfile($_FILES, $types, $storage_location, $post_data['contact_id']);
                IbHelpers::set_message($message['message'], $message['type']);
                if (Model_Plugin::is_enabled_for_role('Administrator', 'contacts3')) {
                    $this->request->redirect('/admin/contacts3?id=' . $post_data['contact_id']);
                } else {
                    $this->auto_render = false;
                    echo 'uploaded';
                }
            }
            catch (Exception $e)
            {
                IbHelpers::set_message($e->getMessage());
                // $this->request->redirect('/admin/contacts3?contact='.$post_data['contact_id']);
            }
        }
        else
        {
            IbHelpers::set_message('No File Uploaded', 'error');
            // $this->request->redirect('/admin/contacts3?contact='.$post_data['contact_id']);
        }
    }

    /**
     * IBIS document controller Follows Check code
     */

    public function action_index()
    {

        $post_data = $this->request->post();

        $this->template->body = View::factory('admin/documents/list_all_templates');

        if ( ! empty($post_data))
        {
            $justonedoller = '';
            $nondollarstring = '';
            if (isset($_POST['submit']))
            {
                $this->template->body->policy_id = $post_data['id'];
            }
            elseif (isset($_POST['name2']))
            {
                if ( ! isset($_FILES['name1']))
                {
                    return;
                }
                else
                {
                    $file_name = $_FILES['name1']['name'];
                    $ext = pathinfo($file_name, PATHINFO_EXTENSION);
                    if ($ext !== 'docx')
                    {
                        return;
                    }
                    else
                    {
                        $uploaddir = '/tmp/';
                        $uploadfile = $uploaddir . basename($_FILES['name1']['name']);

                        if (move_uploaded_file($_FILES['name1']['tmp_name'], $uploadfile))
                        {
                            $new = array();
                            $old = array();
                            $zip = new ZipArchive;
                            if ($zip->open($uploadfile) === TRUE)
                            {

                                if (($index = $zip->locateName('word/document.xml')) !== FALSE)
                                {


                                    $data = $zip->getFromIndex($index);
                                    if ($data == '')
                                    {
                                        return;
                                    }
                                    else
                                    {

                                        $xml = new DOMDocument();
                                        $xml->loadxml($data);


                                        if ($xml->getElementsByTagName('r')->length >= 1)
                                        {
                                            $item = $xml->getElementsByTagName('r');

                                            $partone = '';
                                            foreach ($item as $item1)
                                            {
                                                if ($item1->getElementsByTagName('t')->length >= 1)
                                                {

                                                    $textvalue = $item1->getElementsByTagName('t')->item(0)->nodeValue;
                                                }
                                                else
                                                {
                                                    $textvalue = '';
                                                }
                                                if ($item1->getElementsByTagName('instrText')->length >= 1)
                                                {
                                                    $textvalue1 = $item1->getElementsByTagName('instrText')->item(0)->nodeValue;
                                                }
                                                else
                                                {
                                                    $textvalue1 = '';
                                                }

                                                if ($textvalue !== '')
                                                {
                                                    $reverse = strrev($textvalue);
                                                    if ((stripos($textvalue, "]") !== FALSE) || (($textvalue{0} == '«') && ($reverse{0} !== '»')))
                                                    {
                                                        $textvalue = str_replace("«", "", $textvalue);
                                                        $textvalue = str_replace("»", "", $textvalue);
                                                        $textvalue = trim($textvalue);

                                                        $old[] = $textvalue;
                                                    }
                                                    elseif (strpos($textvalue, '$') !== FALSE)
                                                    {
                                                        $textvalue = trim($textvalue);
                                                        //$reverse = strrev( $textvalue );
                                                        $strcountsub = substr_count($textvalue, '$');
                                                        // echo $strcountsub;
                                                        if ($strcountsub >= 2)
                                                        {
                                                            if (($reverse{0} == '$') && (strlen($textvalue) >= 2))
                                                            {

                                                                $arr = preg_split('/[\s]+/', $textvalue);
                                                                foreach ($arr as $arr1)
                                                                {
                                                                    $arr1 = str_replace('$', '', $arr1);
                                                                    $arr1 = trim($arr1);
                                                                    $new[] = $arr1;
                                                                }
                                                            }
                                                            else
                                                            {

                                                                $partone = $textvalue;

                                                            }

                                                        }
                                                        elseif (($reverse{0} == '$') && ($partone !== ''))
                                                        {

                                                            $newvalue = $partone . $textvalue;
                                                            $partone = '';
                                                            $arr = preg_split('/[\s]+/', $newvalue);
                                                            foreach ($arr as $arr1)
                                                            {
                                                                $arr1 = str_replace('$', '', $arr1);
                                                                $arr1 = trim($arr1);
                                                                $new[] = $arr1;

                                                            }
                                                        }
                                                        elseif (strlen($textvalue) == 1 && $nondollarstring == '')
                                                        {
                                                            $justonedoller = $textvalue;
                                                        }
                                                        elseif (strlen($textvalue) == 1 && $nondollarstring !== '')
                                                        {

                                                            $new[] = $nondollarstring;

                                                            $nondollarstring = '';
                                                            $justonedoller = '';
                                                        }

                                                    }
                                                    elseif ($justonedoller !== '' && $nondollarstring == '')
                                                    {
                                                        $nondollarstring = $textvalue;
                                                        //$justonedoller='';
                                                    }
                                                    /*elseif($justonedoller!=='' && $nondollarstring!=='')
                                                    {
                                                        $new[] = $nondollarstring;
                                                        $nondollarstring ='';
                                                        $justonedoller='';

                                                    }*/

                                                }

                                            }

                                        }

                                    }

                                }

                                $zip->close();
                                $xml->saveXML();
                                $old = array_unique($old);
                                $new = array_unique($new);

                                $this->template->body->new = $new;
                                $this->template->body->old = $old;
                                $this->template->body->key = '1';
                                $this->template->body->file_name = $file_name;

                            }
                            else
                            {
                                return;
                            }
                        }
                        else
                        {
                            return;
                        }
                    }


                }
            }
        }
    }

    /**
     * Does document import automatically
     */
    public function action_ajax_import_form_submit()
    {

        $post_data = $this->request->post();

        if ( ! isset($post_data['doc']))
        {
            $message = array('type' => 'error', 'message' => 'No documents selected');
        }
        else
        {
            $documents = $post_data['doc'];
            $inner_dir = $post_data['import_to_directory'];
            $contact_id = $post_data['contact_id'];
            // Create instance of doc model
            $doc_model = new Model_Document();
            // Preform import action and collect feedback
            $message = $doc_model->import($documents, $inner_dir, $contact_id);

            if ( ! empty($message))
            {
                IbHelpers::set_message($message['type'], $message['message']);
            }
        }
        // $this->redirect('/admin/insurance/policy/?id=' . $policy_id .'&action=documents');
    }

    private function format_function_name($function_name)
    {
        return str_replace('_', ' ', str_replace('template_', '', $function_name));
    }

    // Returns an array of all template generation functions in this class
    public static function list_templates()
    {

        $functions = get_class_methods('Controller_Admin_doc');

        $doc_gen_functions = NULL;
        foreach ($functions as $key => $function)
        {
            if (strpos($function, 'action_doc_template') !== FALSE)
            {
                $cache = explode('action_doc_', $function);
                $doc_gen_functions[] = $cache[1];
            }
        }
        return $doc_gen_functions;
    }

    // Download the document requested by the user
    public function action_doc_quick_download()
    {
        $document_id = $this->request->param('id');
        $knowledgetree_data = Kohana::$config->load('config')->get('KnowledgeTree');
        $kt_connection = New KTClient($knowledgetree_data['url']);
        $kt_connection->initSession($knowledgetree_data['username'], $knowledgetree_data['password']);
        $kt_connection->downloadDocument($document_id);
    }

    // Generates HTML list of images and document categories
    public function action_ajax_list_images()
    {

        $id = $this->request->query('c_id');
        $inner_directory = $this->request->query('inner_directory');

        // Gets all the documents from KT for a given user
        $question_data = self::doc_acquire_documents($id, $inner_directory);

        if ( ! empty($question_data))
        {
            // Creates an array for the datatabe
            $doc_array = self::document_table_builder($question_data);
        }
        else
        {
            $doc_array = NULL;
        }
        // Check to see if there are any documents
        if (empty($doc_array))
        {
            $html = "There are no images stored on the system for this user.";
        }
        else
        {
            $table_headder = "<table class='table table-striped dataTable'>
							<thead>
								<tr>
									<th>Image ID</th>
									<th>Name</th>
									<th>Created</th>
									<th>Last Modified</th>
									<th>Size (KB)</th>
									<th>Type</th>
									<th>Download</th>
								</tr>
								</thead>
								<tbody>";

            $table_body = "";

            foreach ($doc_array['document'] as $id => $doc)
            {
                $table_body .= '
			<tr>
				<td>' . $doc['id'] . '</td>
				<td>' . $doc['name'] . '</td>
				<td>' . $doc['created'] . '</td>
				<td>' . $doc['last modified'] . '</td>
				<td>' . $doc['size'] . '</td>
				<td>' . pathinfo($doc['filename'], PATHINFO_EXTENSION) . '</td>
				<td title="Location: ' . $doc['path'] . '">' . $doc['download'] . '</td>
			</tr>';
            }

            $table_footer = '</tbody></table>';

            $html = $table_headder . $table_body . $table_footer;
        }

        echo $html;
    }

    // Generates HTML list of documents and document categories
    public function action_ajax_list_documents()
    {

        $id = $this->request->query('c_id');
        $inner_directory = $this->request->query('inner_directory');

        // Gets all the documents from KT for a given user
        $question_data = self::doc_acquire_documents($id, $inner_directory);

        if ( ! empty($question_data))
        {
            // Creates an array for the datatabe
            $doc_array = self::document_table_builder($question_data);
        }
        else
        {
            $doc_array = NULL;
        }
        // Check to see if there are any documents
        if (empty($doc_array))
        {
            $html = "There are no documents stored on the system for this user.";
        }
        else
        {
            $table_headder = "<table class='table table-striped dataTable'>
							<thead>
								<tr>
									<th>Doc ID</th>
									<th>Name</th>
									<th>Created</th>
									<th>Last Modified</th>
									<th>Size (KB)</th>
									<th>Type</th>
									<th>Download</th>
								</tr>
								</thead>
								<tbody>";

            $table_body = "";

            foreach ($doc_array['document'] as $id => $doc)
            {
                $table_body .= '
			<tr>
				<td>' . $doc['id'] . '</td>
				<td>' . $doc['name'] . '</td>
				<td>' . $doc['created'] . '</td>
				<td>' . $doc['last modified'] . '</td>
				<td>' . $doc['size'] . '</td>
				<td>Test' . pathinfo($doc['filename'], PATHINFO_EXTENSION) . '</td>
				<td title="Location: ' . $doc['path'] . '">' . $doc['download'] . '</td>
			</tr>';
            }

            $table_footer = '</tbody></table>';

            $html = $table_headder . $table_body . $table_footer;
        }
        echo $html;
    }

    // Generates HTML list of documents and document categories
//    public function action_ajax_documents_as_option()
//    {
//
//        $option_list = NULL;
//        $id = $this->request->query('c_id');
//        $inner_directory = $this->request->query('inner_directory');
//        $selected = $this->request->query('selected');
//        $selected = explode(',', $selected);
//
//        // Check to see if the directory structure is in place for this client
//        self::quick_directory_check_autonomous($id);
//
//        // Gets all the documents from KT for a given user
//        $question_data = self::doc_acquire_documents($id, $inner_directory);
//
//        if ( ! empty($question_data))
//        {
//            // Creates an array for the database
//            $doc_array = self::document_table_builder($question_data);
//        }
//        else
//        {
//            $doc_array = NULL;
//        }
//        // Check to see if there are any documents
//        if (empty($doc_array))
//        {
//            $option_list = "<option>No Documents</option>";
//        }
//        else
//        {
//            foreach ($doc_array['document'] as $id => $doc)
//            {
//                if (in_array($doc['id'], $selected))
//                {
//                    $option_list .= '<option value="' . $doc['id'] . '" selected>' . $doc['filename'] . '</option>';
//                }
//                else
//                {
//                    $option_list .= '<option value="' . $doc['id'] . '">' . $doc['filename'] . '</option>';
//                }
//            }
//        }
//        echo $option_list;
//    }

    // Generates HTML list of documents and document categories
//    public static function doc_list_documents($id, $inner_directory = NULL)
//    {
//
//        // Gets all the documents from KT for a given user
//        $question_data = self::doc_acquire_documents($id, $inner_directory);
//
//        if ( ! empty($question_data))
//        {
//            // Creates an array for the datatabe
//            $doc_array = self::document_table_builder($question_data);
//        }
//        else
//        {
//            $doc_array = NULL;
//        }
//        // Check to see if there are any documents
//        if (empty($doc_array))
//        {
//            $html = "There are no documents stored on the system for this user.";
//        }
//        else
//        {
//            $table_headder = "<table class='table table-striped dataTable'>
//								<thead>
//									<tr>
//										<th>Doc ID</th>
//										<th>Name</th>
//										<th>Created</th>
//										<th>Last Modified</th>
//										<th>Size (KB)</th>
//										<th>Type</th>
//										<th>Download</th>
//									</tr>
//									</thead>
//									<tbody>";
//
//            $table_body = "";
//
//            if (isset($doc_array['document']))
//            {
//                foreach ($doc_array['document'] as $id => $doc)
//                {
//                    $table_body .= '
//					<tr>
//						<td>' . $doc['id'] . '</td>
//						<td>' . $doc['name'] . '</td>
//						<td>' . $doc['created'] . '</td>
//						<td>' . $doc['last modified'] . '</td>
//						<td>Test 2' . $doc['size'] . '</td>
//							<td>' . pathinfo($doc['filename'], PATHINFO_EXTENSION) . '</td>
//						<td title="Location: ' . $doc['path'] . '">' . $doc['download'] . '</td>
//					</tr>';
//                }
//            }
//
//            $table_footer = '</tbody></table>';
//
//            $html = $table_headder . $table_body . $table_footer;
//        }
//        return $html;
//    }

    /**
     * Parse the browsed directory and see if provided exists
     */
    public static function check_if_directory_exists($directories, $dir_id)
    {

        $result = NULL;
        if ( ! empty($directories))
        {
            foreach ($directories as $directory)
            {
                if ($directory['title'] == $dir_id)
                {
                    $result = '1';
                    return $result;
                }
                else
                {
                    $result = '0';
                }
            }
        }
        else
        {
            $result = '0';
        }
        return $result;
    }

    /**
     * This KT function is the very same as 'quick_directory_check' except this
     * function creates its own connection to the KT repository.
     */

    public static function quick_directory_check_autonomous($contact_id)
    {
        // Acquire the settings for KT login and API URL
        $knowledgetree_data = Kohana::$config->load('config')->get('KnowledgeTree');
        // Create KT connection
        $kt_connection = New KTClient($knowledgetree_data['url']);
        $kt_connection->initSession($knowledgetree_data['username'], $knowledgetree_data['password']);

        /* Check to see if directory is available for this clients documents */
        /*$dir_array = $kt_connection->browse('/contacts/', 1);
        $exists = self::check_if_directory_exists($dir_array, $contact_id);*/
        try
        {
            $dir_array = $kt_connection->browse('/contacts/' . $contact_id, 1);
        }
        catch (Exception $e)
        {
            $dir_array = NULL;
        }

        $exists = ($dir_array != NULL) ? 1 : 0;

        // Check $exists result
        if ($exists != 1)
        {
            self::initialise_directory_structure($kt_connection, $contact_id);
        }

    }

    /**
     * Should, recursively examine the provide array and create a document array suitable for a data table
     */
    public static function document_table_builder($doc_array, $documents = NULL, $counter = 0, $path = NULL)
    {

        foreach ($doc_array as $key => $value)
        {
            if ($value['item_type'] == 'D')
            {
                $documents[$counter] = array(
                    'id' => $value['id'], 'name' => $value['title'], 'path' => $value['full_path'],
                    'created' => $value['created_date'], 'last modified' => $value['modified_date'],
                    'size' => number_format($value['filesize'] / 1024, 2, '.', ''), 'type' => $value['mime_display'],
                    'download' => self::create_download_link($value['id'], $value['title']),
                    'filename' => $value['filename'],
                );
                $counter++;
            }
            else if ($value['item_type'] == 'F' && ! empty($value['items']))
            {
                $path = $value['full_path'];
                $rec = self::document_table_builder($value['items'], $documents, $counter, $path);
                $documents = $rec['document'];
                $counter = $rec['counter'];
            }
        }
        $rec = array('document' => $documents, 'counter' => $counter);
        return $rec;
    }

    /**
     * Creates a HTML link to the doc download doc function.
     */
    private static function create_download_link($id, $title = NULL)
    {
        return '<a href="/admin/document/doc_quick_download/'.$id.'" class="skip-save-warning"><i class="icon-download-alt"></i></a>';
    }

    public static function create_download_link_from_array($docs)
    {
        $link_html = NULL;
        $docs = explode(',', $docs);
        foreach ($docs as $id => $docs)
        {
            $link_html .= self::create_download_link($docs);
        }
        echo $link_html;
    }

    /****                    START PROJECTS RELATED FUNCTIONS                           ****/

    /****             Load the documents templates for manual Generation                ****/

    /**
     * Load the generate Document Modal Box For KES
     * With all the data needed to get the documents
     */
    public function action_ajax_get_kes_documents()
    {
        // Stops the printing of the entire CMS template into the body
        $this->auto_render = FALSE;

        $contact_id = $this->request->query('contact_id');
        $family_id  = $this->request->query('family_id');

        // Specifies if a column containing the ID of the contact should appear in the table
        $show_contact_id_column = FALSE;

        $contact_ids = array();
        if (is_numeric($contact_id))
        {
            // If a contact, use the ID of the contact
            $contact_ids[] = $contact_id;
        }
        elseif (is_numeric($family_id))
        {
            // If a family, use the ID of each family member
            $family_members = Model_Contacts3::get_family_members($family_id);
            foreach ($family_members as $family_member)
            {
                $contact_ids[] = $family_member['id'];
            }
            $show_contact_id_column = TRUE;
        }

        // load documents as html
        $doc_table     = Model_Document::doc_list_documents($contact_ids, NULL, $show_contact_id_column, TRUE, FALSE);
        $doc_templates = Model_Document::get_all_template_documents();

        $academic_years = ORM::factory('Course_AcademicYear')->order_by('start_date')->find_all_undeleted();
        $exams          = ORM::factory('Todo_Item')->where('type', '=', 'State-Exam')->order_by('datetime', 'desc')->find_all_undeleted();
        $teacher_role   = new Model_Contacts3_Role(['stub' => 'teacher', 'deleted' => 0]);
        $tutors         = $teacher_role->contacts->select([DB::expr("CONCAT(`first_name`, ' ', `last_name`)"), 'name'])->find_all_undeleted();

        $outstandings = ORM::factory('Kes_Transaction')->get_contact_outstanding_transactions($contact_id);
        $payg = ORM::factory('Kes_Transaction')->get_contact_payg_booking($contact_id,TRUE);
        $cancelled_payg = ORM::factory('Kes_Transaction')->get_contact_payg_booking($contact_id,FALSE);
        $bookings = Model_KES_Bookings::get_contact_family_bookings(null, $contact_id);
        $cancelled_bookings = Model_KES_Bookings::get_contact_canceled_bookings($contact_id);
        $confirmed_bookings = Model_KES_Bookings::get_contact_family_bookings(NULL,$contact_id,NULL,NULL,'Confirmed');
        $payments = ORM::factory('Kes_Payment')->get_contact_payment($contact_id);
        $courses = ORM::factory('Course')->find_all_published();
        //check for any message set when running the above model calls
        $alert = IbHelpers::get_messages();

        $doc_parameters = View::factory('documents/documents_templates_parameters')->set([
            'academic_years'     => $academic_years,
            'bookings'           => $bookings,
            'cancelled_bookings' => $cancelled_bookings,
            'cancelled_payg'     => $cancelled_payg,
            'confirmed_bookings' => $confirmed_bookings,
            'exams'              => $exams,
            'outstandings'       => $outstandings,
            'payg'               => $payg,
            'payments'           => $payments,
            'tutors'             => $tutors,
            'courses'            => $courses
        ]);
        // set view and send data to view
        $this->response->body(View::factory('admin/list_documents_templates')
            ->set(array(
                'datatable' => $doc_table,
                'alert' => $alert,
                'templates' => $doc_templates,
                'doc_parameters'        => $doc_parameters
            )));
    }

        public function action_ajax_share_document($data = []){
            $this->auto_render = FALSE;
            if (empty($data)) {
                $data = $this->request->post();
            }
            if (empty($data))
            {
                $data = $this->request->query();
            }

            $file_model = new Model_Contacts3_Files($data['id']);
            $file = $file_model ->as_array();
            $share_data = array(
                'contact_id' => $data['contact_id'],
                'document_id' => $data['id'],
                'shared' => true);
            if (empty($data['contact_id'])) {
                IbHelpers::set_message('Contact is not found', 'error');
            }
            if (empty($data['id'])) {
                IbHelpers::set_message('Document is not found', 'error');
            }
            if (!$file['id']) {
                try {
                    $file_model->save_data($share_data);

                } catch (Exception $e) {
                    IbHelpers::set_message('Failed to share Document', 'error');
                }
            } else {
                IbHelpers::set_message('Document is already shared', 'error');
            }
            $this->response->body('Document shared');

        }


    /****            MANUAL GENERATION FUNCTIONS            ****/

    /**
     * Generate Documents for KES
     * @throws \Kohana_Exception
     *
     */
    public function action_ajax_generate_kes_document($data = [])
    {
		$this->auto_render = FALSE;
        // Set form values
        if (empty($data)) {
            $data = $this->request->post();
        }
        if (empty($data))
        {
            $data = $this->request->query();
        }
        $direct = $data['direct_download'];//$this->request->query('direct_download');
        $document_name = $data['document_name']; // $this->request->query('document_name');
        $contact_id = $data['contact_id'];  //$this->request->query('contact_id');
        $pdf = TRUE;

        // Make sure direct is set
        if (is_null($direct))
        {
            $direct = 0;
        }
        $doc_helper = new Model_Docarrayhelper();
        $doc = new Model_Document();

        $template = DB::select()->from('plugin_files_file')->where('name', '=', $document_name)->execute()->as_array();

        if ($template)
        {
            switch ($document_name)
            {
                case 'course_brochure':
                    $template = $doc_helper->course_brochure($data['course_id']);
                    $template['template_name'] = 'course_brochure';
                    $template['contact_id'] = $contact_id;
                    $use_ib_doc_gen = true;
                    break;
                case 'Payment_Reminder':
                    $template = $doc_helper->payment_reminder($data['transaction_id']);
                    break;
                case 'Teacher_Booking_Confirmation':
                    $template = $doc_helper->teacher_booking_confirmation($data['booking_id']);
                    break;
                case 'Teacher_Booking_Cancellation':
                    $template = $doc_helper->teacher_booking_cancellation($data['booking_id']);
                    break;
                case 'Payment_Receipt':
                    $template = $doc_helper->booking_receipt($data['payment_id']);
                    break;
                case 'Booking':
                    $template = $doc_helper->booking_document($data['booking_id']);
                    break;
                case 'Booking_Cancellation':
                    $template = $doc_helper->booking_cancellation($data['transaction_id']);
                    break;
                case 'Booking_Confirmation':
                    $template = $doc_helper->booking_document($data['booking_id']);
                    break;
                case 'Booking_Alteration':
                    $template = $doc_helper->booking_document($data['booking_id'], false);
                    break;
                case 'Student_Details_and_Bookings':
                    $template = $doc_helper->student_details_and_bookings($contact_id);
                    break;
                case 'work_experience_welcome_note':
                    $template = $doc_helper->work_experience_welcome_note($contact_id);
                    break;
                case 'hf_summer_school_letter_house_drop':
                    $template = $template = $doc_helper->hf_summer_school_letter_house_drop($contact_id);
                    break;
                case 'academic_year_welcome_note':
                    $template = $template = $doc_helper->academic_year_welcome_note($contact_id);
                    break;
                case 'certificate_of_attendance':
                    $template = $doc_helper->certificate_of_attendance($data);
                    $use_ib_doc_gen = true;
                    break;
                case 'student_report_card';
                case 'student_provisional_letter';
                    $template = $doc_helper->student_report_card($data);
                    $use_ib_doc_gen = true;
                    break;
                case 'tutor_meeting';
                    $template = $doc_helper->tutor_meeting($data);
                    $use_ib_doc_gen = true;
                    break;
            }

            if (!empty($use_ib_doc_gen)) {
                if (isset($template['file_name'])) {
                    $prefix = $template['file_name'];
                } else {
                    $prefix = $document_name;
                }
                $document_id = Model_Files::get_file_id($document_name, Model_Files::get_directory_id_r('/templates'));
                $file = Model_Files::file_path($document_id);
                $tmp_file = tempnam(Kohana::$cache_dir, 'docgen');
                $ib_doc = new IbDocx();
                $ib_doc->processDocx($file, $template, $tmp_file);

                $pdf = true;
                if ($pdf) {
                    $tmp_file_pdf = tempnam(Kohana::$cache_dir, 'docgenpdf');
                    $ib_doc->generate_pdf($tmp_file, $tmp_file_pdf);
                    header('Content-disposition: attachment; filename="'.$prefix.'.pdf"');
                    header('Content-type: application/pdf');

                    if ($direct) {
                        readfile($tmp_file_pdf);
                        unlink($tmp_file_pdf);
                    }
                } else {
                    if ($direct) {
                        header('Content-disposition: attachment; filename="'.$prefix.'.docx"');
                        header('Content-type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
                        readfile($tmp_file);
                    }
                }

                unlink($tmp_file);
                $pdf_save = Settings::instance()->get("word2pdf_savepdf");
                if (!$pdf_save || $direct) {
                    return true;
                }
                if (isset($template['file_name'])&& !empty($template['file_name'])) {
                    $doc_prefix = $template['file_name'];
                } else {
                    $doc_prefix = $template['template_name'];
                }
                if (isset($template['doc_postfix']) && !empty($template['doc_postfix'])) {
                    $doc_postfix = '_' . $template['doc_postfix'];
                } else {
                    $doc_postfix = '';
                }

                $KT_location = '';

                if (!array_key_exists('contact_id', $template))
                {
                    $template['contact_id'] = 'all';
                }
                $temporary_folder = Kohana::$cache_dir . '/' . Settings::instance()->get("doc_temporary_path");
                if (!file_exists($temporary_folder)) {
                    mkdir($temporary_folder, 0777, true);
                }
                copy($tmp_file_pdf, $temporary_folder.$doc_prefix.$doc_postfix . '.pdf');
                //$doc->auto_generate_document($template,$direct,$pdf);x
                $doc->document_save($doc_prefix.$doc_postfix . '.pdf', $temporary_folder, $template['contact_id'], $KT_location, $direct, $pdf_save);
            } else {
                $doc->auto_generate_document($template,$direct,$pdf);
            }
            if ($direct == 0)
            {
                $this->response->body('Document saved');
            }
            // Download the document to the desktop
            elseif (isset($doc->generated_documents['url_docx']) AND file_exists($doc->generated_documents['url_docx']))
            {
                $this->response->body(file_get_contents($doc->generated_documents['url_docx']));
                $this->response->send_file(TRUE, $doc->generated_documents['file']);
            }
            else
            {
                IbHelpers::set_message(' Document not found', 'error');
            }
        }
        else
        {
            IbHelpers::set_message(' Document Template doesn\'t exists', 'error');
        }
   }

	/**
	 * To load the menu for the documents tab actions
	 */
	public function action_ajax_load_doc_actions()
	{
		$this->auto_render    = FALSE;
		// Specifies if the tab is at contact level or family level
		$level                = $this->request->query('level');
		$view                 = View::factory('/documents/contacts/document_actions_menu')->set('level', $level);
		$this->response->body($view);
	}

    public function action_test_docx()
    {
        $this->auto_render = false;
        //$this->response->headers('content-type', 'text/plain');

        $d = new IbDocx();

        $document_id = Model_Files::get_file_id('test.docx', Model_Files::get_directory_id_r('/templates'));
        $file = Model_Files::file_path($document_id);
        $tmp_file = tempnam(Kohana::$cache_dir, 'docgen');
        $tmp_file_pdf = tempnam(Kohana::$cache_dir, 'docgenpdf');

        $template_data = array(
            'assessment_methods' => array('method xxx', 'method yyy', 'method zzz'),
            'learning_methodologies' => array('type' => 'block', 'html' => '<p><b><ul><li>aaaa</li><li>bbbb</li><li>cccc</li></ul></b></p>'),
            'pingus' => array('type' => 'image', 'file' => '/var/www/z4.jpg')
        );

        $doc = new IbDocx();
        $doc->processDocx($file, $template_data, $tmp_file);

        if (1) {
            header('Content-disposition: attachment; filename="test.docx"');
            header('Content-type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
            readfile($tmp_file);
        }
         else {
        header('Content-disposition: attachment; filename="test.pdf"');
        header('Content-type: application/pdf');
        $tmp_file_pdf = tempnam(Kohana::$cache_dir, 'docgenpdf');
        $doc->generate_pdf($tmp_file, $tmp_file_pdf);
        readfile($tmp_file_pdf);
        unlink($tmp_file_pdf);
        }
        unlink($tmp_file);
        exit;
    }
}