<?php

defined('SYSPATH') OR die('No Direct Script Access');

Class Controller_Admin_Media extends Controller_Cms {

    private $session;
    private $sharedmedia;

	//Not used like that as DISABLES the AUTO-COMPLETE (browsing) to Model Functions and Docs (Ctrl+j)
	//@TODO: to be implemented once we complete the Media Plugin
//	private $plugin_model;

	//This one has the Role of a Constructor
    public function before() {
        parent::before();

		//Get an Instance of the Session
		$this->session = Session::instance();
		//Get an instance of the Model_Sharedmedia
		$this->sharedmedia = new Model_Sharedmedia();

		//Not used like that as breaks the AUTO-COMPLETE (browsing) to Model Functions and Docs (Ctrl+j)
		//Get an instance of the Model_Media
//		$this->plugin_model = new Model_Media();

		//Set the Media Plugin Specific Menu
        $this->template->sidebar = View::factory('sidebar');
        $this->template->sidebar->menus = array(
            'Media Uploader' => array(
                array(
                    'icon' => 'media',
                    'name' => 'Media',
                    'link' => '/admin/media'
				)
            )
        );

		if (Auth::instance()->has_access('media'))
		{
			$this->template->sidebar->menus['Media Uploader'][] = array(
                'icon' => 'Presets',
				'name' => 'Presets',
				'link' => '/admin/presets'
			);
		}

        $this->template->sidebar->breadcrumbs = array(
            array('name' => 'Home',  'link' => '/admin'),
            array('name' => 'Media', 'link' => '/admin/media')
        );

        if (Model_Plugin::is_enabled_for_role('Administrator', 'messaging')) {
            $this->template->sidebar->menus['Media Uploader'][] = array(
                'name' => 'Imap Sync Settings',
                'link' => '/admin/media/imap_sync_settings'
            );
        }

    }//end of before


    /**
     * INDEX - Main Function called to render the Main Media View
     */
    public function action_index()
    {
        // check if user have permissions to access media
        if (!Auth::instance()->has_access('media') && !Auth::instance()->has_access('media_limited')) {
            IbHelpers::set_message("You don't have permission!", 'warning');
            $this->request->redirect('/admin');
        }

		if (Auth::instance()->has_access('media'))
		{
			$this->template->sidebar->tools = '<a href="/admin/media/multiple_upload" id="upload_link"><button type="button" class="btn">Upload Image</button></a>';
		}

        // Get the current Tab
		if (($current_tab = $this->session->get('current_tab')) != NULL) {
			$this->session->delete('current_tab');
			switch($current_tab){
				case 'docs':
					$active_tab = 1;
					break;

				case 'audios':
					$active_tab = 2;
					break;

				case 'videos':
					$active_tab = 3;
					break;

				case 'photos':
				default:
					$active_tab = 0;
					break;
			}
        } else {
			//Default active_tab to: photos
			$active_tab = 0;
        }


		$plugin_model = new Model_Media();

		//Load JavaScript for Rendering of Media Items
		$this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('media').'js/h5utils.min.js"></script>';
		$this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('media').'js/multiple_upload.js"></script>';
		$this->template->scripts[] = '<script src="'. URL::get_engine_plugin_assets_base('media') .'js/cropzoom.min.js"></script>';
        $this->template->scripts[] = '<script src="'. URL::get_engine_plugin_assets_base('media') .'js/image_edit.js"></script>';
        $this->template->scripts[] = '<script src="'. URL::get_engine_plugin_assets_base('media') .'js/media_list.js"></script>';

        // main view 
        $this->template->body = View::factory('media_main_view');
        $this->template->body->active_tab = $active_tab;
        $this->template->body->msg_error = $this->session->get_once('msg_error');

        //render subview with list of media Photos and include in main view
        $media_photos_view = View::factory('media_all_list');
        $media_photos_view->media_all = $plugin_model->get_all_items_based_on('', '', 'details', '', NULL, 'date_modified', 'desc');
        $this->template->body->media_all_list = $media_photos_view->render();

	}//end of function

    public function action_dialog()
    {
        $this->template = View::factory('cms_templates/empty/template');
        $this->template->scripts = array();
        $this->template->title = '';
        $this->template->sidebar = View::factory('sidebar');
        $this->template->sidebar->menus = array();
        $this->template->header = new stdClass();
        $this->template->sidebar->tools = '';

        // Get the current Tab
        if (($current_tab = $this->session->get('current_tab')) != NULL) {
            $this->session->delete('current_tab');
            switch($current_tab){
                case 'docs':
                    $active_tab = 1;
                    break;

                case 'audios':
                    $active_tab = 2;
                    break;

                case 'videos':
                    $active_tab = 3;
                    break;

                case 'photos':
                default:
                    $active_tab = 0;
                    break;
            }
        } else {
            //Default active_tab to: photos
            $active_tab = 0;
        }


        $plugin_model = new Model_Media();

        //Load JavaScript for Rendering of Media Items
        $this->template->scripts[] = '<script src="'. URL::get_engine_plugin_assets_base('media') .'js/cropzoom.min.js"></script>';
        $this->template->scripts[] = '<script src="'. URL::get_engine_plugin_assets_base('media') .'js/image_edit.js"></script>';
        $this->template->scripts[] = '<script src="'. URL::get_engine_plugin_assets_base('media') .'js/media_list.js"></script>';

        // main view
        $this->template->body = View::factory('media_main_view');
        $this->template->body->active_tab = $active_tab;
        $this->template->body->msg_error = $this->session->get_once('msg_error');

        if ($this->request->query('photos')) {
            //render subview with list of media Photos and include in main view
            $media_photos_view = View::factory('media_all_list');
            $media_photos_view->selectionDialog = true;
            $media_photos_view->media_photos = $plugin_model->get_all_items_based_on('mime_type', 'image%', 'details',
                'LIKE', null, 'date_modified', 'desc');
            $this->template->body->media_all_list = $media_photos_view->render();
        }

        if ($this->request->query('docs')) {
            //render subview with list of media Docs and include in main view
            $media_docs_view = View::factory('media_docs_list');
            $media_docs_view->selectionDialog = true;
            //All Photo Items will have a PReset ID assigned to them -> Take only Media Items which have a Preset
            $media_docs_view->media_docs = $plugin_model->get_all_items_based_on('location', 'docs', 'details', '=',
                null, 'date_modified', 'desc');
            $this->template->body->media_docs_list = $media_docs_view->render();
        }

        if ($this->request->query('fonts')) {
            //render subview with list of media Docs and include in main view
            $media_fonts_view = View::factory('media_fonts_list');
            $media_fonts_view->selectionDialog = true;
            //All Photo Items will have a PReset ID assigned to them -> Take only Media Items which have a Preset
            $media_fonts_view->media_fonts = $plugin_model->get_all_items_based_on('location', 'fonts', 'details', '=',
                null, 'date_modified', 'desc');
            $this->template->body->media_fonts_list = $media_fonts_view->render();
        }

        if ($this->request->query('audios')) {
            //render subview with list of media Audios and include in main view
            $media_audios_view = View::factory('media_audios_list');
            $media_audios_view->selectionDialog = true;
            //All Photo Items will have a PReset ID assigned to them -> Take only Media Items which have a Preset
            $media_audios_view->media_audios = $plugin_model->get_all_items_based_on('location', 'audios', 'details',
                '=', null, 'date_modified', 'desc');
            $this->template->body->media_audios_list = $media_audios_view->render();
        }

        if ($this->request->query('videos')) {
            //render subview with list of media Videos and include in main view
            $media_videos_view = View::factory('media_videos_list');
            $media_videos_view->selectionDialog = true;
            //All Photo Items will have a PReset ID assigned to them -> Take only Media Items which have a Preset
            $media_videos_view->media_videos = $plugin_model->get_all_items_based_on('location', 'videos', 'details',
                '=', null, 'date_modified', 'desc');
            $this->template->body->media_videos_list = $media_videos_view->render();
        }

        $this->template->body->plugin = Model_Plugin::get_plugin_by_name('media');
        $this->template->body->selectionDialog = true;
    }//end of function

    public function action_multiple_upload()
    {
        if ($this->request->query('dialog')) {
            $this->template = View::factory('cms_templates/empty/template');
            $this->template->scripts = array();
            $this->template->title = '';
            $this->template->sidebar = View::factory('sidebar');
            $this->template->sidebar->menus = array();
            $this->template->header = new stdClass();
            $this->template->sidebar->tools = '';
        } else {
            $this->template->sidebar->breadcrumbs[] = array(
                'name' => 'Upload Images',
                'link' => '/admin/media/multiple_upload'
            );
        }

        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('media').'js/multiple_upload.js"></script>';
        $this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base('media').'js/h5utils.min.js"></script>';
        $this->template->body = View::factory('multiple_upload');
        if ($this->request->query('dialog')) {
            $this->template->body->selectionDialog = true;
        }
    }

    public function action_browse_images()
    {
		$location = $this->request->post('location');
        $model  = new Model_Media();
        $images = $model->get_all_items_based_on('location', $location, 'details', '=', NULL, 'date_modified', 'DESC');
        $view   = View::factory('browse_uploads')->set('images', $images)->set('location', $location);

        $this->auto_render = FALSE;
        echo $view;
    }

    public function action_image_editor()
    {
        $this->auto_render = FALSE;
        echo View::factory('image_edit_dialog');
    }


	//Add Media Item to the Media System (Filesystem and DB)
	/**
	 * Function used to Process an "Upload Media Item" Form.<br />
	 * Will Use the corresponding Media Model to:<br />
	 * 1. Validate the received from the Form POST-ed data<br />
	 * 2. Ad the specified File to the Media Filesystem an dDatabase, by calling the corresponding Model function.
	 */
	public function action_upload_media_item() {
//		echo "\nUpload Media Item:\n";
//		IbHelpers::pre_r($_FILES);
//		IbHelpers::die_r($this->request->post());

		//Preset Data is passed with the Form POST
		$media_preset = $this->request->post();
		$file_uploaded = FALSE;
		$current_tab = $media_preset['media_tab_preview'];
		unset($media_preset['media_tab_preview']);

		$plugin_model = new Model_Media();

		//Validate Input File
		if($plugin_model->validate_media_item($_FILES['file_to_upload'], $media_preset)){
			//Add the Item to the Media
			$file_uploaded = $plugin_model->add_item_to_media(
				$_FILES['file_to_upload'],
				$media_preset
			);
		}//else return -=> Error Messages will be picked up automatically in This Controller: after() function

		//Set current - Active Media Tab PReview
		$this->session->set('current_tab', $current_tab);

		//Return
		return $this->request->redirect('admin/media');

	}//end of function


	/**
	 * Public Function used to Delete a Media Item.<br />
	 * Triggered by an AJAX Request.<br />
	 * Returns an array:
	 * <pre>
	 * Array(
	 *     'success_msg' => 'MESSAGE FOR SUCCESSFULLY DELETED ITEM',
	 *     'err_msg' => 'ERROR MESSAGE'
	 * )
	 * </pre>
	 * in a JSON Formatted string.
	 */
	public function action_ajax_toggle_delete(){
//		echo "\nFunction ajax_toggle_delete():\n";
//		IbHelpers::die_r($this->request->post());

		$item_to_delete_id = $this->request->post('item_id');
		$item_to_delete_media_type = trim($this->request->post('item_type'));

		// Set result body
		$result = array(
			'success_msg' => '',
			'err_msg' => ''
		);

		//Delete the specified News Story
		$delete_result = Model_Media::factory('Media')->delete_media_item($item_to_delete_id, $item_to_delete_media_type);

		//Set return message
		if($delete_result == 1){
			//Success message is set in the: delete_media_item() function
			$result['success_msg'] = IbHelpers::get_messages();
		}else{
			//Error Message is set in the: delete_media_item() function
			$result['err_msg'] = IbHelpers::get_messages();
		}

		//Return
		$this->auto_render = false;
		$this->response->body(json_encode($result));
	}//end of function



    /**
	 * @TODO: Used by the Drop-here to upload: save_form form - might not be used in the future
     * Save previously uploaded image and crop if necessary
     * 
     */
    public function action_save() {
        if ($this->request->post('save_cropped')) {

            $this->sharedmedia->crop_file($this->request->post('filename')
                    , $this->request->post('x')
                    , $this->request->post('y')
                    , $this->request->post('w')
                    , $this->request->post('h')
                    , $this->request->post('jcw')
                    , $this->request->post('jch'));
            $this->sharedmedia->save_file('crp_' . $this->request->post('filename'));

            //remove preview image info from session
            $this->session->delete('picname');
            return $this->request->redirect('admin/media');
            
        } elseif ($this->request->post('save_full')) {

            $this->sharedmedia->save_file($this->request->post('filename'));

            //remove preview image info from session
            $this->session->delete('picname');
            return $this->request->redirect('admin/media');
            
        } else {
            throw new Exception('unknown post method');
        }

    }
    /**
     * NOT USED: Upload confirmation
     */
    public function action_uploadok() {
        $msg_ok = $this->session->get_once('msg_ok');
        $this->template->body = View::factory('upload_ok');
        $this->template->body->$msg_ok = $msg_ok;
    }
	/**
	 * @TODO: Not Used
	 * Action for automatic file upload after drop - element 'pic' (views/upload_form.php)
	 * Called by the JS code in: uploadform.js
	 * @TODO: Not used at the moment and Might NOT be used in the future
	 *
	 * pic - is used for automatic uploads after drop
	 * expected return json encoded status info
	 *
	 */
	public function action_postfile() {
		try {
			$imageinfo = $this->sharedmedia->receive_file($_FILES['file_to_upload']);
			echo json_encode(array('status' => 'File was uploaded successfully'));
		} catch (Exception $exc) {
			echo json_encode(array('status' => $exc->getMessage()));
		}
		exit;
	}
	/**
	 * @TODO: Not Used as replaced by the: action_upload_media_item() function
	 * Action for manual file upload element 'pic' (views/upload_form.php)
	 *
	 * expected return - if Ok redirect to same page with set session param "picname" (will be used to load uploaded image for preview)
	 *
	 */
	public function action_postfile_manual() {
		try {
			$imageinfo = $this->sharedmedia->receive_file($_FILES['file_to_upload']);
			$this->session->set('picname', $imageinfo['filename']);
			return $this->request->redirect('admin/media');
		} catch (Exception $exc) {
			$this->session->set('msg_error', 'There was an error uploading the file, please try again! ' . $exc->getMessage());
			return $this->action_index();
		}
	}


	//Prepare the last data to be sent to the Browser, before Return HTML Page to the Browser
	function after() {
		// Load the messages from the IbHelper.
		$messages = IbHelpers::get_messages();

		// If there are messages
		if($messages){
			// Add the message to the alert string if it exists
			if(isset($this->template->body->alert)){
				$this->template->body->alert = $this->template->body->alert . $messages;
			}
			// Else create an alert string
			else{
				$this->template->body->alert = $messages;
			}
		}

		parent::after();
	}//end of function

    /**
     * Return a list of images.
     *
     * @return The list of all images.
     */
    function action_ajax_get_image_list() {
        // Get the media model
        $plugin_model = new Model_Media();

        // Get the complete list of images
        $list = array();

        foreach ($plugin_model->get_all_items_based_on('mime_type', 'image%', 'details', 'LIKE', NULL, 'date_modified', 'DESC') as $item) {
            array_push($list, $item);
        }

        // Return
        $this->auto_render = false;
        $this->response->body(json_encode($list));
    }

    /**
     * Return a list of non empty directories.
     *
     * @return The list of all locations.
     */
    function action_ajax_get_location_list() {
        // Get the media model
        $plugin_model = new Model_Media();

        // Return
        $this->auto_render = false;
        $this->response->body(json_encode($plugin_model->get_location_list()));
    }


	/**
	 * Return a list of documents.
	 *
	 * @return The list of all documents.
	 */
	function action_ajax_get_docs_list() {
		// Get the media model
		$plugin_model = new Model_Media();

		// Get the complete list of images
		$list = array();
		// 'text/plain', 'application/pdf', 'application/msword'
		foreach ($plugin_model->get_all_items_based_on('location', 'docs', 'details', '=') as $item) {
			array_push($list, $item);
		}

		// Return
		$this->auto_render = false;
		$this->response->body(json_encode($list));
	}

    function action_ajax_check_shared_media()
    {
        $this->auto_render = false;
        $media_project_folder = Kohana::$config->load('config')->project_media_folder;
        $response = (!empty($media_project_folder) AND is_string($media_project_folder) AND $media_project_folder !== "") ? "/shared_media/".Kohana::$config->load('config')->project_media_folder : "";
        $this->response->body($response);
    }

    function action_ajax_upload()
    {
        $alert_danger_extentions = array(
            '.php',
            '.exe',
            '.phtml'
        );
        $plugin_model      = new Model_Media();
        reset($_FILES);
        $file              = current($_FILES);
        foreach ($alert_danger_extentions as $alert_danger_extention) {
            if (stripos($file['name'], $alert_danger_extention)) {
                $this->auto_render = false;
                $this->response->status(403);
                return;
            }
        }
        $errors            = array();
        $media_preset      = $this->request->post();
		$directory         = Model_Media::get_directory($file['type']);
		$directories       = array($directory);
		if ($directory == 'photos')
		{
			$directories = array('content', $this->request->post('preset_directory'));
		}
		$original_filename = $file['name'];
		$file['name']      = Model_Media::get_filename_suggestion($file['name'], $directories);

        $check_duplicate = $this->request->post('check_duplicate');
        if (!$check_duplicate) {
            $file['name'] = date('YmdHis') . '-' . $file['name'];
        }
        $validation_errors = $plugin_model->validate_media_item($file, $media_preset, $ajax = TRUE);

        $media_id = null;
        if (sizeof($validation_errors) == 0)
        {
            $media_id = @$plugin_model->add_item_to_media($file, $media_preset);
        }
        else
        {
            $errors[] = $validation_errors;
        }

        $media_project_folder = Kohana::$config->load('config')->project_media_folder;
        $shared_media = (!empty($media_project_folder) AND is_string($media_project_folder) AND $media_project_folder !== "") ? "/shared_media/".Kohana::$config->load('config')->project_media_folder : "";

        $data = array(
            'files' => array($file['name']),
            'original_filenames' => array($original_filename),
            'errors' => $errors,
            'shared_media' => $shared_media,
            'media_id' => $media_id,
            'fullpaths' => array(Model_Media::get_localpath_to_id($media_id))
        );
        $this->auto_render = FALSE;
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        echo json_encode($data);
    }

    function action_ajax_get_filename_suggestion()
    {
		$filename    = $this->request->post('name').'.'.$this->request->post('ext');
		$directories = array($this->request->post('directory'));
        $filename    = Model_Media::get_filename_suggestion($filename, $directories);

        $this->auto_render = FALSE;
        echo json_encode($filename);
    }

    function action_ajax_show_upload_details()
    {
        $post = $this->request->post();
        $model = new Model_Media();
        $image_details = $model->get_all_items_based_on(DB::expr('CONCAT(`location`,"/", `filename`)'), 'content/'.$post['image']);
        $view = (String) View::factory('uploaded_image_details')->set('data', current($image_details));

        $this->auto_render = FALSE;
        echo json_encode($view);
    }

    /* CropZoom functions */
    public function action_ajax_cropzoom_validate()
    {
        $model     = new Model_Media();
        $post      = $this->request->post();
        $filename  = $this->request->query('filename');
        $preset_id = $this->request->query('preset_id');
        $errors    = $model->cropzoom_validate($post, $filename, $preset_id);

        $this->auto_render = FALSE;
        echo json_encode($errors);
    }

    public function action_ajax_cropzoom_upload()
    {
        $model     = new Model_Media();
        $post      = $this->request->post();
        $filename  = $this->request->query('filename');

        $preset_id = $this->request->query('preset_id');
        $json      = $this->request->query('json');
        $preset    = (is_numeric($preset_id)) ? $model->get_presets(array('id' => $preset_id)) : NULL;
        if (stripos($post["imageSource"], 'http://' . $_SERVER['HTTP_HOST'] . '/') !== 0 && stripos($post["imageSource"], 'https://' . $_SERVER['HTTP_HOST'] . '/') !== 0) {
            Model_Errorlog::save(null, "SECURITY");
            exit;
        }
        $this->auto_render = false;
        $media     = null;
        $result = $model->cropzoom_save($post, $filename, $preset);
        if ($json) {
            $this->response->headers('Content-type', 'application/json; charset=utf-8');
            echo json_encode($result);
        } else {
            echo $result['file'];
        }
    }

    public function action_ajax_get_fonts()
    {
        $this->response->headers('Content-type', 'application/json; charset=utf-8');
        $this->auto_render = FALSE;
        $this->response->headers('Expires', gmdate('D, d M Y H:i:s \G\M\T', time() + 86400));
        $default_fonts     = array(
            'Arial/Arial, Helvetica, sans-serif',
            'Comic Sans MS/Comic Sans MS, cursive',
            'Courier New/Courier New, Courier, monospace;',
            'Georgia/Georgia, serif',
            'Lucida Sans Unicode/Lucida Sans Unicode, Lucida Grande, sans-serif',
            'Tahoma/Tahoma, Geneva, sans-serif',
            'Times New Roman/Times New Roman, Times, serif',
            'Trebuchet MS/Trebuchet MS, Helvetica, sans-serif',
            'Verdana/Verdana, Geneva, sans-serif'
        );
        $new_fonts = Model_Media::get_fonts();
        $fonts     = array_merge($default_fonts, array_unique($new_fonts));
        echo json_encode($fonts);
    }

    public function action_fonts()
    {
        header("Content-type: text/css; charset: UTF-8");
        header('Expires:' . gmdate('D, d M Y H:i:s \G\M\T', time() + 86400));
        $fonts = Model_Media::get_fonts(TRUE);
        $return = '';
        foreach ($fonts as $font)
        {
            $return .= '@font-face{'."\n";
            $return .= 'font-family:"'.$font['name'].'";'."\n";
            $return .= 'src:url(\''.$font['src'].'\')'."\n";
            $return .= '}'."\n";
        }
        echo $return;
        exit();
    }

	// Return datatable results
	public function action_ajax_get_datatable()
	{
        $query = $this->request->query();
        $selection_dialog = $this->request->param('id');
        if ($selection_dialog == 'selection_dialog') {
            $query['selection_dialog'] = true;
        } else {
            $query['selection_dialog'] = false;
        }

		$this->auto_render = FALSE;
		$this->response->body(Model_Media::get_for_datatable($query));
	}


    public function action_image_edit_frame()
    {
        $this->auto_render = false;
        $view = View::factory('image_edit_frame');
        $view->image = $this->request->query('image');
        if (stripos($view->image, 'http://' . $_SERVER['HTTP_HOST'] . '/') !== 0 && stripos($view->image, 'https://' . $_SERVER['HTTP_HOST'] . '/') !== 0) {
            Model_Errorlog::save(null, "SECURITY");
            exit;
        }
		$presetId = $this->request->query('preset');
        $presets = new Model_Presets();
        $view->presets = $presets->get_available_photo_presets();
        $view->presetId = $presetId;
		$view->lockPreset = $this->request->query('lock_preset');
        $this->response->body($view);
    }

    public function action_save_image()
    {
        $this->auto_render = false;
        $media = new Model_Media();
        $post = $this->request->post();
        $imageData = $post['imageData'];
        $filename = $post['filename'];
        $presetId = $post['presetId'];
        $mime = '';
        $result = array();
        if (preg_match('/^data:(.*?);(.*?),(.*)/', $imageData, $image)){
            unset($image[0]);
            $mime = $image[1];
            if ($image[2] == 'base64') {
                $image = base64_decode($image[3]);
            } else {
                throw new Exception('unknown encoding');
            }
            $result['file'] = $media->saveImageFromString($image, basename($filename), $mime, $presetId);
        }
        //print_r($post);
        $this->response->headers('Content-Type', 'application/json; charset=utf-8');
        echo json_encode($result);
    }

    public function action_imap_sync_settings()
    {
        $post = $this->request->post();
        if (@$post['action'] == 'save') {
            $settings = array(
                'file_types' => $post['settings']['file_types'],
                'sync_accounts' => $post['settings']['accounts']
            );
            Model_Media::set_external_sync('imap', json_encode($settings));
        }
        $mm = new Model_Messaging();
        $drivers = $mm->get_drivers();
        $accounts = $drivers['email']['imap']->load_settings();
        $settings = Model_Media::get_external_sync('imap');
        $view = View::factory('imap_sync_settings');
        $view->accounts = $accounts;
        $view->settings = $settings;
        $this->template->body = $view;
    }
}//end of class