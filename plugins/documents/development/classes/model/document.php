<?php
/**
 * Created by PhpStorm.
 * User: yann
 * Date: 05/05/15
 * Time: 14:05
 * IBIS document model
 */
defined('SYSPATH') OR die('No Direct Script Access');

Class Model_Document extends Model
{

	const FILES_TABLE = 'plugin_files_file';
	const FOLDER_OPTION_TABLE = 'plugin_documents_folder_options';

	// Array of system documents
	public $generated_documents;
	// Redirect on or off
	public $redirect = TRUE;
	//Array for send mails
	public $mails = array();

	private $external_error_handing = FALSE;

	//Array for storing data for populating tags in doc template
	private $template_data;
	public $lastFileId = null;

	/**
	 * Used For documents Generated Automatically from another action
	 * @param      $template
	 * @param int  $direct
	 * @param bool $pdf
	 * @return bool
	 * @throws \Exception
	 */
	public function auto_generate_document($template, $direct = 0, $pdf = TRUE)
	{
		$status = TRUE;
		$document = DB::select()->from('plugin_files_file')
            ->where('name', '=', $template['template_name'])
            ->and_where('deleted', '=', 0)
            ->execute()
            ->as_array();
		$document_id = $document[0]['id'];
		$doc_prefix = $template['template_name'].'_';
		$KT_location = '';

		$doc_postfix = $template['doc_postfix'];

		// Use the all folder to store documents when no contact is set
		if (!array_key_exists('contact_id', $template))
		{
			$template['contact_id'] = 'all';
		}

		// This function takes the document meta data, generates the document and stores it. It also returns a status message.
		$error = self::doc_gen_and_storage($document_id, $template, $doc_prefix, $doc_postfix, $template['contact_id'], $KT_location, $direct, $pdf);

		if (!is_null($error))
		{
			Log::instance()->add(Log::ERROR, $error);
			$status = FALSE;
		}
		return $status;
	}

	/**
	 * Folder options for each contacts
	 * @return mixed
	 */
	public function get_folder_list()
	{
		$folders = DB::select('folder_name', 'friendly_name')
			->from(self::FOLDER_OPTION_TABLE)
			->where('publish', '=', 1)
			->where('deleted', '=', 0)
			->execute()
			->as_array();
		return $folders;
	}

	public function set_external_error_handing($status)
	{
		if ($status === TRUE)
		{
			$this->external_error_handing = TRUE;
		}
		else
		{
			$this->external_error_handing = FALSE;
		}
	}

	public function before()
	{
		parent::before();
	}

	/**
	 * @todo Add a setting for the template file location
	 * @return mixed
	 */
	public static function get_template_folder()
	{
		$template = DB::select()->from(self::FILES_TABLE)->where('name', '=', 'templates')->and_where('deleted', '=', 0)->execute()->as_array();
		return $template[0]['id'];
	}

	/**
	 * @todo add  a setting for base folder saving documents
	 * @return mixed
	 */
	public function get_contact_base_folder()
	{
		$contact = DB::select()->from(self::FILES_TABLE)->where('name', '=', 'contacts')->and_where('deleted', '=', 0)->execute()->as_array();
		return $contact[0]['id'];
	}

	/**
	 * @todo add a setting for the subfoldername
	 * @param $contact_id
	 * @return string
	 */
	public function get_contact_folder($contact_id)
	{
		$home = '/contacts/'.$contact_id;
		$base = $this->get_contact_base_folder();
		$contact = DB::select()
			->from(self::FILES_TABLE)
			->where('parent_id', '=', $base)
			->where('name', '=', $contact_id)
            ->and_where('deleted', '=', 0)
			->execute();
		if (is_null($contact))
		{
			DB::insert(self::FILES_TABLE, array('name', 'type', 'parent_id'))
				->values(array($contact_id, 0, $base))
				->execute();
		}
		return $home;
	}

	public function get_template_data($contact_id)
	{
		$doc_elements = new Docarrayhelper($contact_id);
		$this->template_data = $doc_elements->get_template_data();
		return $this->template_data;
	}

	public static function get_template()
	{
		$templates_folder = self::get_template_folder();

		// Get all files within the folders
		$documents = DB::select()
			->from(self::FILES_TABLE)
			->where('parent_id', '=', $templates_folder)
			->and_where('type', '=', 1)
			->and_Where('deleted', '=', 0)
			->order_by('language', 'asc')
			->order_by('name', 'asc')
			->execute()
			->as_array();

		return $documents;
	}

	public function generate_test_document($contact_id, $document)
	{
		$contact = new Doc_Elements($contact_id);
		$template_data = $contact->get_template_data();

		$name = readdir($document);

		$doc = new Docgenerator();

		$doc->make_document($template_data, $name);

	}

	public function action_index()
	{

		$post_data = $this->request->post();

		$this->template->body = View::factory('list_all_templates');

		if (!empty($post_data))
		{
			$justonedoller = '';
			$nondollarstring = '';
			if (isset($_POST['submit']))
			{
				$this->template->body->policy_id = $post_data['id'];
			}
			elseif (isset($_POST['name2']))
			{
				if (!isset($_FILES['name1']))
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
						$uploadfile = $uploaddir.basename($_FILES['name1']['name']);

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

															$newvalue = $partone.$textvalue;
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

	public function action_ajax_get_import_documents()
	{

		$contact_id = $this->request->query('contact_id');

		$policy_ids = Model_Policy::extract_ids(Model_Policy::get_all_bycustomer($contact_id));

		// Get the list of documents and enter the contact id
		$this->template->body = View::factory('pop_list_import_docs');
		$this->template->body->policy_ids = $policy_ids;
		$this->template->body->functions = $this->Initialise_function_list($this->list_templates());
	}

	/**
	 * Moves an array of import documents
	 * @param $documents  - array of document ids
	 * @param $inner_dir  - the internal directory to store the documents
	 * @param $contact_id - contact id to be used
	 */
	public function import($documents, $inner_dir, $contact_id)
	{

		// Home dir
		$home = '/contacts/'.$contact_id.'/'.$inner_dir;
		// Move documents stored in $documents to $inner_dir/$contact_id
		foreach ($documents as $key => $doc)
		{
			try
			{
				// $this->move_document(self::createKTconnection(), $doc, $home);
			}
			catch (Exception $e)
			{
				return array('type' => 'error', 'message' => $e->getMessage());
			}

		}
		return array('type' => 'success', 'message' => 'Documents imported successfully.');
	}

	/**
	 * Move document from inbox to customer selected home
	 * @param $kt_connection
	 * @param $doc_id
	 * @param $home
	 * @throws Exception
	 */
	public function move_document($kt_connection, $doc_id, $home)
	{
		// Move document to contact directory
		$response = NULL;
		try
		{
			$response = $kt_connection->moveDocument($doc_id, $home, $reason = NULL);
		}
		catch (Exception $e)
		{
			// Is there a problem? - Set error cache
			$error = 'There was a problem moving your document: '.$doc_id.'Service Response: '.$response;
		}
		// Remove the documents from import directory - might not been needed

		// Throw error if one is found
		if (!empty($error))
		{
			throw new Exception($error);
		}
	}

	/**
	 * @param null $template_data 0 = no data attached ; 1 = data attached for elements array and query to fill
	 * @return $this
	 */
	public static function get_all_template_documents($template_data = NULL)
	{
		// Get id for templates folder
		$templates_folder_id = self::get_template_folder();

		// Get all files within the folders
		$documents = DB::select()
			->from(self::FILES_TABLE)
			->where('parent_id', '=', $templates_folder_id)
			->and_where('type', '=', 1)
			->and_where('deleted', '=', 0);
		if (!is_null($template_data))
		{
			$documents->and_where('template_data', '=', $template_data);
		}
		$documents = $documents->order_by('language', 'asc')
			->order_by('name', 'asc')
			->execute()
			->as_array();

		return $documents;
	}

	// Recursively go through all subfolders and build a list of folders
	public static function get_subfolders($folder_id)
	{
		$subfolders = DB::select()
			->from('plugin_files_file')
			->where('parent_id', '=', $folder_id)
			->and_where('type', '=', 0)
			->and_where('deleted', '=', 0)
			->execute()
			->as_array();

		foreach ($subfolders as $subfolder)
		{
			$new_subfolders = Model_Document::get_subfolders($subfolder['id']);
			$subfolders = array_merge($subfolders, $new_subfolders);
		}
		return $subfolders;
	}

//    /**
//     * List all custom letters in the customer letter directory
//     * @static
//     * @return array|null
//     */
//    public static function get_letters_for_generation()
//    {
//
//        // Acquire the settings for KT login and API URL
//        $knowledgetree_data = Kohana::$config->load('config')->get('KnowledgeTree');
//        // Create KT connection
//        $kt_connection = New KTClient($knowledgetree_data['url']);
//        $kt_connection->initSession($knowledgetree_data['username'], $knowledgetree_data['password']);
//        $import_docs = $kt_connection->browse('/templates/custom_letters/', 1);
//        // Return null if empty
//        if (empty($import_docs))
//        {
//            // no files
//            return NULL;
//        }
//        else
//        {
//            // Loop through each file
//            foreach ($import_docs as $key => $file)
//            {
//                $files[] = array(
//                    'name' => $file['filename'], 'id' => $file['id'], 'date' => $file['created_date'],
//                    'size' => $file['filesize'] / 1000
//                );
//            }
//            // Send files array
//            return $files;
//        }
//    }

	public static function format_function_name($function_name)
	{
		return str_replace('_', ' ', str_replace('template_', '', $function_name));
	}

	// Returns an array of all template generation functions in this class
	public static function list_templates()
	{

		$functions = get_class_methods('Controller_Admin_Documents');

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

		if (!empty($question_data))
		{
			// Creates an array for the datatable
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
				<td>'.$doc['id'].'</td>
				<td>'.$doc['name'].'</td>
				<td>'.$doc['created'].'</td>
				<td>'.$doc['last modified'].'</td>
				<td>'.$doc['size'].'</td>
				<td>'.pathinfo($doc['filename'], PATHINFO_EXTENSION).'</td>
				<td title="Location: '.$doc['path'].'">'.$doc['download'].'</td>
			</tr>';
			}

			$table_footer = '</tbody></table>';

			$html = $table_headder.$table_body.$table_footer;
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
		if (!empty($question_data))
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
			$table_headder = "  <div class='alert-area'></div><table class='table table-striped dataTable'>
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
				<td>'.$doc['id'].'</td>
				<td>'.$doc['name'].'</td>
				<td>'.$doc['created'].'</td>
				<td>'.$doc['last modified'].'</td>
				<td>'.$doc['size'].'</td>
				<td>'.pathinfo($doc['filename'], PATHINFO_EXTENSION).'</td>
				<td title="Location: '.$doc['path'].'">'.$doc['download'].'</td>
			</tr>';
			}

			$table_footer = '</tbody></table>';

			$html = $table_headder.$table_body.$table_footer;
		}
		echo $html;
	}

	// Generates HTML list of documents and document categories
	public function action_ajax_doc_as_option()
	{

		$option_list = NULL;
		$id = $this->request->query('c_id');
		$inner_directory = $this->request->query('inner_directory');
		$selected = $this->request->query('selected');
		$selected = explode(',', $selected);

		// Check to see if the directory structure is in place for this client
		self::quick_directory_check_autonomous($id);

		// Gets all the documents from KT for a given user
		$question_data = self::doc_acquire_documents($id, $inner_directory);

		if (!empty($question_data))
		{
			// Creates an array for the database
			$doc_array = self::document_table_builder($question_data);
		}
		else
		{
			$doc_array = NULL;
		}
		// Check to see if there are any documents
		if (empty($doc_array))
		{
			$option_list = "<option>No Documents</option>";
		}
		else
		{
			foreach ($doc_array['document'] as $id => $doc)
			{
				if (in_array($doc['id'], $selected))
				{
					$option_list .= '<option value="'.$doc['id'].'" selected>'.$doc['filename'].'</option>';
				}
				else
				{
					$option_list .= '<option value="'.$doc['id'].'">'.$doc['filename'].'</option>';
				}
			}
		}
		echo $option_list;
	}

	// Generates HTML list of documents and document categories
	public static function doc_list_documents($contact_ids, $inner_directory = NULL, $show_contact_id_column = FALSE, $show_share_link = FALSE, $show_shared_only = FALSE)
	{
		// $contact_id can be entered as a single ID or an array of IDs
		// Normalise this to an array
		if (is_numeric($contact_ids))
		{
			$contact_ids = array($contact_ids);
		}

		$doc_array = NULL;
		$question_data = array();

		if (is_array($contact_ids) AND count($contact_ids) > 0)
		{
			// Gets all the documents from KT for a given user
			foreach ($contact_ids as $contact_id)
			{
				$question_data[$contact_id] = self::doc_acquire_documents($contact_id, $inner_directory);
			}
		}
		else
		{
			return "We did not receive any ID to search for documents.";
		}

		$doc_array = array(
			'document' => array(),
			'counter' => 0
		);

		// Get the documents for each contact. Merge the results into one array
		foreach ($question_data as $contact_id => $contact_question_data)
		{
			if (!is_null($contact_question_data))
			{
				$new_doc_array = self::document_table_builder($contact_question_data, NULL, 0, NULL, $contact_id, $show_share_link, $show_shared_only);
				if ($new_doc_array['document']) {
					$doc_array['document'] = array_merge($doc_array['document'], $new_doc_array['document']);
					$doc_array['counter'] += (int) $new_doc_array['counter'];
				}
			}
		}

		// Check to see if there are any documents
		if ($doc_array['counter'] <= 0)
		{
            $html = "There are no documents yet.";
		}
		else
		{
			$doc_array = Model_Document::order_documents_by_last_updated($doc_array);
            $list_documents = Model_Plugin::is_enabled_for_role('Administrator', 'contacts3') ? '/documents/documents/list_documents' : 'list_documents';
			$html = View::factory($list_documents)
				->set('show_contact_id_column', $show_contact_id_column)
                ->set('show_share_link', $show_share_link)
				->set('doc_array', $doc_array)
				->set('pathinfo_extension', PATHINFO_EXTENSION);

		}
		return $html;
	}

	// Preforms all necessary operations to complete document generation
	public function doc_gen_and_storage($documentid, $template_data, $doc_prefix, $doc_postfix, $customer_id, $KT_location, $direct, $pdf = FALSE, $claim_id=NULL)
	{
		// Set file name formats
		$pre_extension_file_name = $doc_prefix.$doc_postfix;
		$final_pdf = $pre_extension_file_name.'.pdf';
		$final_docx = $pre_extension_file_name.'.docx';

		$path = self::get_destination_path();

		// Acquire the settings for KT login and API URL
		Log::instance(Log::NOTICE, "Dummy Connect to Knowledgetree.");
		$knowledgetree_data = Kohana::$config->load('config')->get('KnowledgeTree');

		try
		{
			// Create KT connection
			$kt_connection = New KTClient($knowledgetree_data['url']);
			$kt_connection->initSession($knowledgetree_data['username'], $knowledgetree_data['password']);
		}
		catch (Exception $e)
		{
			Log::instance()->add(Log::ERROR, $e->getMessage().$e->getTraceAsString());
			if ($this->external_error_handing)
			{
				throw new Exception($e);
			}
			else
			{
				return $e->getMessage();
			}

		}
		Log::instance(Log::NOTICE, "Connected.");

		// Generate Document - Load template from KT

		$template_location = Kohana::$cache_dir . '/' . Settings::instance()->get("doc_template_path");
		if (!file_exists($template_location)) {
			mkdir($template_location, 0777, true);
		}
		$temporary_folder = Kohana::$cache_dir . '/' . Settings::instance()->get("doc_temporary_path");
		if (!file_exists($temporary_folder)) {
			mkdir($temporary_folder, 0777, true);
		}

		$doc_config = Kohana::$config->load('config')->get('doc_config');
		$script_location = $template_location.$doc_config['script'];

		Log::instance(Log::NOTICE, "Download document to folder.");
		try
		{
			$template_file = $kt_connection->downloadDocumentToFolder($documentid, $template_location);
		}
		catch (Exception $e)
		{
			Log::instance()->add(Log::ERROR, $e->getMessage().$e->getTraceAsString());
			if ($this->external_error_handing)
			{
				throw new Exception($e);
			}
			else
			{
				return $e->getMessage();
			}
		}

		Log::instance(Log::NOTICE, "Document downloaded.");
		Log::instance(Log::NOTICE, "Create .doc file.");
        // Create document generator object - provide document generation base
		$docx = New Docgenerator($template_location, $script_location, $temporary_folder);
        $docx->add_template($template_file);
        $docx->initalise_document_template($template_data);
        $feedback = $docx->create($pre_extension_file_name);


        if ($feedback === TRUE)
		{
			self::set_activity($documentid, 'generate-docx');

		}
		else
		{
			Log::instance()->add(Log::ERROR, 'Error doc generation');
		}

		if ($pdf)
		{
			try
			{
				//Generate the PDF
				$input_file = $template_location.$final_docx;
				$output_file = $template_location.$final_pdf;

				if (Settings::instance()->get("word2pdf_active") == 1)
				{

					// convert DOCX to PDF
					self::doc_convert_to_pdf($input_file, $output_file);

					// save pdf to files?
					$pdf_save = Settings::instance()->get("word2pdf_savepdf");

					if (($pdf_save == 1) AND ($direct == 0))
					{
						$this->initialise_directory_structure($kt_connection, $customer_id);
						$kt_connection->addDocument($final_pdf, $template_location.$final_pdf, $path.'/contacts/'.$customer_id.$KT_location);
						$this->lastFileId = $kt_connection->lastFileId;
                        if($claim_id && $this->lastFileId){
                            Model_ContextualLinking::addObjectLinking($claim_id, Model_ContextualLinking::getTableId("pinsurance_claim"), $this->lastFileId, Model_ContextualLinking::getTableId("plugin_files_file"));
                        }
					}
				}
				else
				{
					Log::instance()->add(Log::ERROR, 'PDF is not enabled in your APP Settings');
				}

			}
			catch (Exception $e)
			{
				if ($this->external_error_handing)
				{
					throw new Exception($e);
				}
				else
				{
					return $e->getMessage();
				}
			}
		}

		// was direct option selected to download to desktop?
		if ($direct == 0)
		{
			// save docx to files?
			$save_docx = Settings::instance()->get("doc_save");
			if (($save_docx == 1) AND ($direct == 0))
			{
				$this->initialise_directory_structure($kt_connection, $customer_id);
				$kt_connection->addDocument($final_docx, $template_location.$final_docx, $path.'/contacts/'.$customer_id.$KT_location);
				$this->lastFileId = $kt_connection->lastFileId;
				if($claim_id && $this->lastFileId){
                    Model_ContextualLinking::addObjectLinking($claim_id, Model_ContextualLinking::getTableId("pinsurance_claim"), $this->lastFileId, Model_ContextualLinking::getTableId("plugin_files_file"));
                }

			}

		}
		else //direct download was chosen
		{
			$this->generated_documents['file'] = $final_docx;
			$this->generated_documents['url_docx'] = $template_location.$final_docx;
			$this->generated_documents['url_pdf'] = $template_location.$final_pdf;
		}

		// ***** SEND EMAIL IF MAIL IS SET *****/
		foreach ($this->mails as $mail)
		{

			$attached_doc[0] = $template_location.$final_pdf;
			$this->multi_attach_mail($mail['to'], $attached_doc, $mail['sender'], $mail['subject'], $mail['message']);
		}

		// ***** CLEAN UP PROCESS **** //
		Log::instance(Log::NOTICE, "Deleting temporary files.");
		system('rm '.$docx->temporary_folder.$template_file);

		//if we dont want to download cleanup otherwise leave for controller to pickup to download
		if ($direct == 0)
		{
			system('rm '.$template_location.$final_docx);
			system('rm '.$template_location.$final_pdf);
		}

	}

	public function document_gen_and_storage_and_download($documentid, $template_data, $doc_prefix, $doc_postfix, $customer_id, $KT_location)
	{
		//Set environment path
		if (Kohana::$environment == Kohana::PRODUCTION)
		{
			$path = '';
		}
		else
		{
			$path = '/test';
		}

		// Acquire the settings for KT login and API URL
		$knowledgetree_data = Kohana::$config->load('config')->get('KnowledgeTree');
		$dms_config = Kohana::$config->load('config')->get('DMS_config');

		try
		{
			// Create KT connection
			$kt_connection = New KTClient($knowledgetree_data['url']);
			$kt_connection->initSession($knowledgetree_data['username'], $knowledgetree_data['password']);
		}
		catch (Exception $e)
		{
			if ($this->external_error_handing)
			{
				throw new Exception($e);
			}
			else
			{
				return $e->getMessage();
			}

		}

		// Generate Document - Load template from KT
		$template_location = $dms_config['cache'];
		$script_location = $template_location.$dms_config['script'];
		$temporary_folder = Settings::instance()->get("doc_temporary_path");

		try
		{
			$template_file = $kt_connection->downloadDocumentToFolder($documentid, $template_location);
		}
		catch (Exception $e)
		{
			if ($this->external_error_handing)
			{
				throw new Exception($e);
			}
			else
			{
				return $e->getMessage();
			}
		}

		// Create document generator object - provide document generation base
		$docx = new Docgenerator($template_location, $script_location, $temporary_folder);
		$docx->add_template($template_file);
		$docx->initalise_document_template($template_data);
		$docx->create($doc_prefix.$doc_postfix);

		try
		{
			//Generate the PDF

			//New version with external API
			$input_file = $template_location.$doc_prefix.$doc_postfix.'.docx';
			$output_file = $template_location.$doc_prefix.$doc_postfix.'.pdf';
			self::doc_convert_to_pdf($input_file, $output_file);
		}
		catch (Exception $e)
		{
			if ($this->external_error_handing)
			{
				throw new Exception($e);
			}
			else
			{
				return $e->getMessage();
			}
		}

		//Set the variables for download or remove documents outside this function (controller and __destructor)
		$this->generated_documents['file'] = $doc_prefix.$doc_postfix.'.pdf';
		$this->generated_documents['url_docx'] = $template_location.$doc_prefix.$doc_postfix.'.docx';
		$this->generated_documents['url_pdf'] = $template_location.$doc_prefix.$doc_postfix.'.pdf';

		/*** Store document on KnowledgeTree ***/
		$this->initialise_directory_structure($kt_connection, $customer_id);

		// This value is set in settings - it allows you to specify if you want to store docx and PDF types
		$save_docx = Settings::instance()->get('store_docx');

		if ($save_docx)
		{
			// Upload document to KT
			$renewal_docx = $doc_prefix.$doc_postfix.'.docx';
			$kt_connection->addDocument($renewal_docx, $template_location.$renewal_docx, $path.'/contacts/'.$customer_id.$KT_location);
		}
		else
		{
			// Upload document to KT
			$renewal_pdf = $doc_prefix.$doc_postfix.'.pdf';
			$renewal_docx = $doc_prefix.$doc_postfix.'.docx';
			$kt_connection->addDocument($renewal_pdf, $template_location.$renewal_pdf, $path.'/contacts/'.$customer_id.$KT_location);
			$kt_connection->addDocument($renewal_docx, $template_location.$renewal_docx, $path.'/contacts/'.$customer_id.$KT_location);
		}

		// ***** CLEAN UP PROCESS **** //
		system('rm '.$template_location.$template_file);

	}

	//Function for generate the PDF with external API
	//@url http://www.convertapi.com/
	public function doc_convert_to_pdf($fileToConvert, $pathToSaveOutputFile, $message = '')
	{
		try
		{

			//what type of pdf service is activated
			$local_service = Settings::instance()->get("word2pdf_local_active");
			$third_party = Settings::instance()->get("word2pdf_thirdparty_active");

			if ($local_service)
			{
				$apiKey = Settings::instance()->get("word2pdf_local_api");
				$postdata = array('OutputFileName' => 'MyFile.pdf', 'ApiKey' => $apiKey, 'file' => "@".$fileToConvert);
				if (class_exists('CURLFile')) {
					$postdata['file'] = new CURLFile($fileToConvert);
				}
				$ch = curl_init(Settings::instance()->get("word2pdf_local_url"));
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_HEADER, 1);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
				$result = curl_exec($ch);
			}
			elseif ($third_party)
			{
				$apiKey = Settings::instance()->get("word2pdf_thirdparty_api");
				$postdata = array('OutputFileName' => 'MyFile.pdf', 'ApiKey' => $apiKey, 'File' => "@".$fileToConvert);
				if (class_exists('CURLFile')) {
					$postdata['File'] = new CURLFile($fileToConvert);
				}
				$ch = curl_init(Settings::instance()->get("word2pdf_thirdparty_url"));
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($ch, CURLOPT_HEADER, 1);
				curl_setopt($ch, CURLOPT_POST, 1);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $postdata);
				$result = curl_exec($ch);

			}
			else
			{

				$message = "PDF : Exception Message No settings set for PDF Generation. Please check your app settings. ";
				Log::instance()->add(Log::ERROR, $message);

				throw new Exception($message);

				return FALSE;
			}
			$headers = curl_getinfo($ch);
			$header = self::ParseHeader(substr($result, 0, $headers["header_size"]));
			$body = substr($result, $headers["header_size"]);
			curl_close($ch);
			if (0 < $headers['http_code'] && $headers['http_code'] < 400)
			{
				// Check for Result = true

				if (in_array('Result', array_keys($header)) ? !$header['Result'] == "True" : TRUE)
				{
					$message = "PDF : Something went wrong with request, did not reach ConvertApi service.<br />";
					Log::instance()->add(Log::ERROR, $message);
					return FALSE;
				}
				// Check content type
				if ($headers['content_type'] <> "application/pdf")
				{
					$message = "PDF : Exception Message : returned content is not PDF file.<br />";
					Log::instance()->add(Log::ERROR, $message);
					return FALSE;
				}
				$fp = fopen($pathToSaveOutputFile, "wbx");

				fwrite($fp, $body);

				self::set_activity(NULL, 'generate-pdf');

				return TRUE;

			}
			else
			{
				$message = "PDF : Exception Message : ".$result.".<br />Status Code :".$headers['http_code'].".<br />";
				Log::instance()->add(Log::ERROR, $message);
				throw new Exception($result.".<br />Status Code :".$headers['http_code'].".<br />");
				return FALSE;
			}
		}
		catch (Exception $e)
		{

			/**
			 * set message for email - include file, line and full message
			 */
			$message = "PDF : There was an error creating a PDF from this address : ".URL::base()." . The PDF has not been created. There was an error in ".$e->getFile()." on line ".$e->getLine()."  The full error message is ".$e->getMessage();

			Log::instance()->add(Log::ERROR, $message);
			return FALSE;
		}


	}

	/**
	 * Function needed fot generated PDF whith the external API
	 * @param string $header
	 * @return array
	 */
	public function ParseHeader($header = '')
	{
		$resArr = array();
		$headerArr = explode("\n", $header);
		foreach ($headerArr as $key => $value)
		{
			$tmpArr = explode(": ", $value);
			if (count($tmpArr) < 1) continue;
			$resArr = array_merge($resArr, array($tmpArr[0] => count($tmpArr) < 2 ? "" : $tmpArr[1]));
		}
		return $resArr;
	}

	public function multi_attach_mail($to, $files, $sendermail, $subject, $message)
	{
		// email fields: to, from, subject, and so on
		$from = "Files attach <".$sendermail.">";
		/*$subject = date("d.M H:i")." F=".count($files);
			$message = date("Y.m.d H:i:s")."\n".count($files)." attachments";*/
		$headers = "From: $from";

		// boundary
		$semi_rand = md5(time());
		$mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

		// headers for attachment
		$headers .= "\nMIME-Version: 1.0\n"."Content-Type: multipart/mixed;\n"." boundary=\"{$mime_boundary}\"";

		// multipart boundary
		$message = "--{$mime_boundary}\n"."Content-Type: text/plain; charset=\"iso-8859-1\"\n".
			"Content-Transfer-Encoding: 7bit\n\n".$message."\n\n";

		// preparing attachments
		for ($i = 0; $i < count($files); $i++)
		{
			if (is_file($files[$i]))
			{
				$message .= "--{$mime_boundary}\n";
				$fp = @fopen($files[$i], "rb");
				$data = @fread($fp, filesize($files[$i]));
				@fclose($fp);
				$data = chunk_split(base64_encode($data));
				$message .= "Content-Type: application/octet-stream; name=\"".basename($files[$i])."\"\n".
					"Content-Description: ".basename($files[$i])."\n".
					"Content-Disposition: attachment;\n"." filename=\"".basename($files[$i])."\"; size=".filesize($files[$i]).";\n".
					"Content-Transfer-Encoding: base64\n\n".$data."\n\n";
			}
		}
		$message .= "--{$mime_boundary}--";
		$returnpath = "-f".$sendermail;
		$ok = @mail($to, $subject, $message, $headers, $returnpath);
		if ($ok)
		{
			return $i;
		}
		else
		{
			return 0;
		}
	}

	public function __destruct()
	{
		// ***** CLEAN UP PROCESS **** //
		//Remove temporal files
		if (isset($this->generated_documents['url_docx']) AND file_exists($this->generated_documents['url_docx']))
		{
			system('rm '.$this->generated_documents['url_docx']);
		}
		if (isset($this->generated_documents['url_pdf']) AND file_exists($this->generated_documents['url_pdf']))
		{
			system('rm '.$this->generated_documents['url_pdf']);
		}
	}


	// Parse the browsed directory and see if provided exists
	public static function check_if_directory_exists($directories, $dir_id)
	{

		$result = NULL;
		if (!empty($directories))
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


	// Create directories for new contact
	public static function initialise_directory_structure($kt_connection, $folder_id)
	{

		if (Kohana::$environment == Kohana::PRODUCTION)
		{
			$path = self::get_destination_path();
		}
		else
		{
			$path = self::get_destination_path(); //' = /test'
			$cleanpath = str_replace('/', '', $path);

			$dir_array = $kt_connection->browse('/', 1);
			if (!self::check_if_directory_exists($dir_array, $cleanpath))
			{
				$kt_connection->addFolder($cleanpath, '/');
				$kt_connection->addFolder('contacts', '/'.$cleanpath.'/');
			}
		}

		//Get the list of all folder in the test folder
		$dir_array = $kt_connection->browse($path, 1);
		if (!self::check_if_directory_exists($dir_array, 'contacts'))
		{
			$kt_connection->addFolder('contacts', '/'.$cleanpath.'/');
		}

		//Get the list of all folder in the contacts folder
		try
		{
			$dir_array = $kt_connection->browse($path.'/contacts/'.$folder_id, 1);
		}
		catch (Exception $e)
		{
			$dir_array = NULL;
		}

		$exists = ($dir_array != NULL) ? 1 : 0;

		if ($exists == 0)
		{
			$kt_connection->addFolder($folder_id, $path.'/contacts/');
		}


	}

	public function document_save($filename, $tmp_file_path, $customer_id, $KT_location, $direct, $pdf = FALSE, $claim_id=NULL){
        $path = self::get_destination_path();
        // Acquire the settings for KT login and API URL
        Log::instance(Log::NOTICE, "Dummy Connect to Knowledgetree.");
        $knowledgetree_data = Kohana::$config->load('config')->get('KnowledgeTree');

        try
        {
            // Create KT connection
            $kt_connection = New KTClient($knowledgetree_data['url']);
            $kt_connection->initSession($knowledgetree_data['username'], $knowledgetree_data['password']);
        }
        catch (Exception $e)
        {
            Log::instance()->add(Log::ERROR, $e->getMessage().$e->getTraceAsString());
            if ($this->external_error_handing)
            {
                throw new Exception($e);
            }
            else
            {
                return $e->getMessage();
            }

        }
        Log::instance(Log::NOTICE, "Connected.");
        try {
            $this->initialise_directory_structure($kt_connection, $customer_id);
            $kt_connection->addDocument($filename, $tmp_file_path.$filename, $path.'/contacts/'.$customer_id.$KT_location, $filename);
            $this->lastFileId = $kt_connection->lastFileId;
            if($claim_id && $this->lastFileId){
                Model_ContextualLinking::addObjectLinking($claim_id, Model_ContextualLinking::getTableId("pinsurance_claim"), $this->lastFileId, Model_ContextualLinking::getTableId("plugin_files_file"));
            }
        } catch (Exception $e) {
            if ($this->external_error_handing)
            {
                throw new Exception($e);
            }
            else
            {
                return $e->getMessage();
            }
        }

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
			$dir_array = $kt_connection->browse('/contacts/'.$contact_id, 1);
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
	 * Check to see if directory is available for this clients documents
	 */
	public static function quick_directory_check($kt_connection, $contact_id)
	{
		try
		{
			$dir_array = $kt_connection->browse('/contacts/'.$contact_id, 1);
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

	// Should, recursively examine the provide array and create a document array suitable for a data table
	public static function document_table_builder($doc_array, $documents = NULL, $counter = 0, $path = NULL, $contact_id = NULL, $show_share_link = FALSE, $show_shared_only = FALSE)
	{

		foreach ($doc_array as $key => $value)
		{
		    if ($show_shared_only && !Model_Contacts3_Files::is_shared($contact_id, $value['id'])) {
                continue;
            }
            if ($value['item_type'] == 'D')
			{
				$documents[$counter] = array(
					'id' => $value['id'],
					'name' => $value['title'],
					'path' => $path,
					'created' => $value['created_date'],
					'last modified' => $value['modified_date'],
					'size' => number_format($value['filesize'] / 1024, 2, '.', ''),
					'type' => $value['mime_display'],
					'download' => self::create_download_link($value['id'], $value['title']),
					'filename' => $value['filename'],
					'contact_id' => $contact_id
				);
				if (Settings::instance()->get('share_document') && $show_share_link) {
                    $documents[$counter]['share'] = Model_Contacts3_Files::is_shared($contact_id, $value['id']) ? '' : self::create_share_link($value['id']);
                }
				$counter++;
			}
			else if ($value['item_type'] == 'F' && !empty($value['items']))
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

	// Creates a HTML link to the doc download doc function.
	private static function create_download_link($id, $title = NULL)
	{
		return '<a href="/admin/documents/doc_quick_download/'.$id.'" class="skip-save-warning"><i class="icon-download-alt"></i></a>';
	}
	private static function create_share_link($id) {
        return '<a href="#" id="share_document_' . $id . '"class="share-document"><i class="icon-share"></i></a>';
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

	/*	START :: TEMPLATE FUNCTIONS */
	public function generate_doc_from_template($contact_id, $document_id, $direct = 0, $pdf = FALSE)
	{

		// Get the template file info for the generation process
		$document = DB::select()->from('plugin_files_file')->where('id', '=', $document_id)->execute();
		$document_id = $document[0]['id'];
		$doc_prefix = $document[0]['name'].'_';
		$doc_postfix = $contact_id;
		$KT_location = '';

		$template_data = array(
			'COUNTRY' => 'Ireland',
			'Subagent' => 'SUB WHO! :)',
			'TodaysDate' => 'Wed 13th May 2015',
			'ContactID' => '443',
			'PolicyNumber' => '132456',
			'contactname' => 'Michael O Callaghan',
			'AddressLine1' => 'Limerick'
		);
		//        $docarrayhelper = new Docarrayhelper($policy_id);
		//        $template_data = $docarrayhelper->fill_template_array($template_data);

		// This function takes the document meta data, generates the document and stores it. It also returns a status message.
		$error = self::doc_gen_and_storage($document_id, $template_data, $doc_prefix, $doc_postfix, $contact_id, $KT_location, $direct, $pdf);

		if (!is_null($error))
		{
			Log::instance()->add(Log::ERROR, $error);
		}
	}

	public function auto_generate_receipt($data, $document_id, $direct = 0, $pdf = FALSE)
	{
		$status = TRUE;
		$document = DB::select()->from('plugin_files_file')->where('id', '=', $document_id)->execute();
		$document_id = $document[0]['id'];
		$doc_prefix = $document[0]['name'].'_';
		$KT_location = '';

		$contact = $data['contact'];
		$transaction = $data['transaction'];
		$payment = $data['payment'];


		$doc_postfix = 'C'.$contact['id'].'_T'.$transaction['id'].'_P'.$payment['id'];

		$template_data = array(
			'COUNTRY' => $contact['country'],
			'Subagent' => 'SUB WHO! :)',
			'TodaysDate' => $payment['created'],
			'ContactID' => $contact['id'],
			'PolicyNumber' => '132456',
			'contactname' => $contact['name'],
			'AddressLine1' => $contact['address1'],
			'AddressLine2' => $contact['address2'],
			'AddressLine3' => $contact['address3'],
			'AddressLine4' => $contact['town'],
			'COUNTY' => $contact['county'],
			'BookingID' => $transaction['booking_id'],
			'PAYMENTTYPE' => $payment['type'],
			'CURRENCYSYMBOL' => $payment['currency'],
			'TOTALDUE' => $transaction['total'],
			'Blah_Teat' => 'Test Blah Blah blah',
			'OUTSTANDING' => $transaction['outstanding']
		);
		//        $docarrayhelper = new Docarrayhelper($policy_id);
		//        $template_data = $docarrayhelper->fill_template_array($template_data);

		// This function takes the document meta data, generates the document and stores it. It also returns a status message.
		$error = self::doc_gen_and_storage($document_id, $template_data, $doc_prefix, $doc_postfix, $contact['id'], $KT_location, $direct, $pdf);

		if (!is_null($error))
		{
			Log::instance()->add(Log::ERROR, $error);
			$status = FALSE;
		}
		return $status;
	}


	public function set_mail($to = 'tasio@ideabubble.com', $subject = 'IBIS document', $message = '', $sender = 'ibis_sytem@ideabubble.ie')
	{
		$this->mails[] = array('to' => $to, 'subject' => $subject, 'message' => $message, 'sender' => $sender);
	}


	//Order the document array by last update date
	public static function order_documents_by_last_updated($response)
	{
		$length = count($response['document']);
		for ($start_item = 0; $start_item < $length; $start_item++)
		{
			$aux_date = 0;
			for ($i = $start_item; $i < $length; $i++)
			{
				if (strtotime($response['document'][$i]['last modified']) > $aux_date)
				{
					$aux_date = strtotime($response['document'][$i]['last modified']);
					$pos = $i;

				}
			}
			$aux_array = $response['document'][$start_item];
			$response['document'][$start_item] = $response['document'][$pos];
			$response['document'][$pos] = $aux_array;
		}
		return $response;
	}


// This function takes a client ID and produces a block of documents they own.
// $inner_directory needs to start with / e.g. /claims_documents
	public static function doc_acquire_documents($id, $inner_directory = NULL)
	{
		$path = self::get_destination_path();

		$contact_id = $id;
		// Acquire the settings for KT login and API URL
		$knowledgetree_data = Kohana::$config->load('config')->get('KnowledgeTree');

		// Create KT connection
		$kt_connection = New KTClient($knowledgetree_data['url']);
		$kt_connection->initSession($knowledgetree_data['username'], $knowledgetree_data['password']);

		/*
		$contacts = $kt_connection->browse($path . '/contacts/', 1);

		if ( ! empty($contacts))
		{
			// Check to see if there are files
			foreach ($contacts as $contact)
			{
				if ($contact['filename'] == $contact_id)
				{
					$dir_array = $kt_connection->browse($path . '/contacts/' . $contact_id . $inner_directory);
					return $dir_array;
				}
				else
				{
					$dir_array = null;
				}
			}
		}
		else
		{
			$dir_array = null;
		}
		*/
		try
		{
			$dir_array = $kt_connection->browse($path.'/contacts/'.$contact_id.$inner_directory);
		}
		catch (Exception $e)
		{
			$dir_array = array();
		}

		return $dir_array;
	}

	/**
	 * This function looks up settings and gets the correct path for saving docs.
	 */
	public static function get_destination_path()
	{
		// are we in test mode?
		if (Settings::instance()->get("doc_test_mode") == 1)
		{
			// change path to save in TEST path location other then live one
			$path = Settings::instance()->get("doc_test_destination_path");
		}
		else
		{
			// change path to save in LIVE path location other then live one
			if (Settings::instance()->get("doc_destination_path") === FALSE)
				$path = ''; //set a default to empty if nothing returns from setting
			else
				$path = Settings::instance()->get("doc_destination_path");
		}

		return $path;
	}

	/**
	 * This function wraps the call to file activities for cleaner use
	 * action_types = generate-pdf, generate-docx, convert, upload, download, delete, update, print
	 */
	public static function set_activity($doc_id = NULL, $action_type = NULL)
	{

		$user = Auth::instance()->get_user();

		$activity = new Model_Activity();
		$activity
			->set_item_type('files')
			->set_action($action_type)
			->set_item_id($doc_id)
			->set_user_id($user['id'])
			->save();
	}

	public static function getPaperTypeOptions($selected)
	{
		return HTML::optionsFromArray(
			array('HEADED' => __('HEADED'), 'PLAIN' => __('PLAIN')),
			$selected
		);
	}

    public static function getDocumentTypeById($id)
    {
        $table_id = DB::select('id')
            ->from('engine_contextual_linking_references')
            ->where('table_name', '=', 'pinsurance_claim_document_types')
            ->execute()
            ->as_array();

        $doc_type = DB::select('pinsurance_claim_document_types.name')
            ->from('engine_contextual_linking_data')
            ->join('pinsurance_claim_document_types')
            ->on('engine_contextual_linking_data.dst_id','=','pinsurance_claim_document_types.id')
            ->where('engine_contextual_linking_data.src_id', '=', $id)
            ->where('engine_contextual_linking_data.dst_type', '=', $table_id[0]['id'])
            ->execute()
            ->as_array();

        if($doc_type){
            return $doc_type[0]['name'];
        }else{
            return '';
        }
    }

    // If a multiline string is to remain multiline when inserted into a document, run it through this function
    public static function maintain_multiline($string)
    {
        if (strpos($string, "\n") !== false) {
            return array('type' => 'multiline', 'lines' => explode("\n", $string));
        }
        else {
            return $string;
        }
    }
}