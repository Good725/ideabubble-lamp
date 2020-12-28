<?php
/**
 * <pre>
 * Created by JetBrains PhpStorm.
 * User: Kosta
 * Date: 10/01/2013
 * Time: 09:15
 * To change this template use File | Settings | File Templates.
 *
 *
 * <h1>Main NEWS Plugin Controller.</h1>
 * Provides Control functions for the Back-End (Admin) News Management.
 *
 * User Guide Wiki Page: <a href="http://wiki.ideabubble.ie/confluence/display/WPP/News+Plugin">News - User's Guide</a>
 *
 * Developer's Guide Wiki Page: <a href="http://wiki.ideabubble.ie/confluence/display/WPP/News">News - Developer's Guide</a>
 * </pre>
 */
class Controller_Admin_Customscroller extends Controller_Cms {

	/* Back-End (CMS) : Admin Functions  */
	public function before ()
	{
		parent::before ();
	}

//@TODO NOT IN USE -=> REMOVE IF NOT REQUIRED
	//Builds the Main - News List HTML view
	public function action_index ()
	{
		// Get an instance of the This Plugin Model
		$plugin_model = new Model_Customscroller();

		//Load Javascript
		$this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base ('customscroller').'js/admin/customscroller_sequence_edit.js"></script>';

		// Load the body here.
		$this->template->body         = View::factory (
			'admin/list_news', array ('customcontroller' => $plugin_model->get_all_items_admin ())
		);
		$this->template->body->plugin = Model_Plugin::get_plugin_by_name ('customcontroller');
	}

//@TODO NOT IN USE -=> REMOVE IF NOT REQUIRED
	public function action_add_edit_item ($item_id = FALSE)
	{
//		echo "\nFunction: add_edit_item()\n";
//		IbHelpers::die_r($this->request->param());

		//Get the ID from the request
		if ($item_id === FALSE) $item_to_edit_id = $this->request->param ('id');
		//The ID was passed from an internal function as a parameter
		else
			$item_to_edit_id = $item_id;

		$plugin_model = new Model_Customscroller();
		//Get data of the News Story to be edited
		$item_data = (!empty($item_to_edit_id)) ? $plugin_model->get_all_items_admin ($item_to_edit_id) : NULL;

		//Loads the CSS and  javascript files
		$this->template->styles = array_merge (
			$this->template->styles, array (
										   URL::get_engine_plugin_assets_base ('customcontroller').'css/admin/customscroller_sequence_edit.css' => 'screen'
									 )
		);

		$this->template->scripts[] = '<script src="'.URL::get_engine_plugin_assets_base ('customscroller').'js/admin/customscroller_sequence_edit.js"></script>';
		//Load the body
		$this->template->body = View::factory (
			'admin/add_edit_custom_scroller_item', array (
														 'item_data' => $item_data[0]
												   )
		);

	}

//@TODO NOT IN USE -=> REMOVE IF NOT REQUIRED
	public function action_process_editor ()
	{
		echo "\nCustomScroller->process_editor():\n";
		IbHelpers::die_r ($this->request->post ());

		$item_post_data  = $this->request->post ();
		$item_to_edit_id = NULL;
		//Default Redirects to: News - Editor View
		$redirect = '/admin/news/add_edit_item';

		if (!empty($item_post_data))
		{

			$plugin_model = new Model_Customscroller();

			// Validate
			if ($plugin_model->validate ($item_post_data))
			{

				//Update this News Story Item based on the specified $item_post_data['editor_action']
				switch ($item_post_data['editor_action'])
				{
					case 'delete': //Delete Item
						$item_to_edit_id          = $item_post_data['item_id'];
						$delete_news_story_result = $plugin_model->delete ($item_to_edit_id);
						//Set corresponding message for the Deleted News Story
						if ($delete_news_story_result == 1)
						{
							IbHelpers::set_message ('News Story #'.$item_to_edit_id.' deleted successfully.', 'success');
							$redirect = '/admin/news';
						}
						else
						{
							IbHelpers::set_message ('News Story #'.$item_to_edit_id.' could not be deleted.', 'error');
							//Redirect back to the News Story-Editor view
							$redirect .= $redirect.'/'.$item_to_edit_id;
						}
						break;

					case 'add': //Create Item
						// Add the News Story and get its ID
						$item_to_edit_id = $plugin_model->add ($item_post_data);
						//Set corresponding message for the Created News Story
						if (!empty($item_to_edit_id))
						{
							IbHelpers::set_message ('News Story #'.$item_to_edit_id.' Added.', 'success');
						}
						else
						{
							IbHelpers::set_message ('There was a problem with the creation of this News Story.', 'error');
						}
						//Set the Redirect based on the specified by the Item Editor view - Redirect location
						$redirect = $item_post_data['editor_redirect'].((strpos ($item_post_data['editor_redirect'], 'add_edit_item', 0) !== FALSE) ? '/'.$item_to_edit_id : '');
						break;

					case 'edit': //Edit Item
						$item_to_edit_id    = $item_post_data['item_id'];
						$item_update_result = $plugin_model->update ($item_post_data);
						if ($item_update_result == 1) IbHelpers::set_message ('News Story #'.$item_to_edit_id.' Updated.', 'success');
						else
							IbHelpers::set_message ('There was a problem with the deletion of News Story #'.$item_to_edit_id.'.', 'error');
						$redirect = $item_post_data['editor_redirect'].((strpos ($item_post_data['editor_redirect'], 'add_edit_item', 0) !== FALSE) ? '/'.$item_to_edit_id : '');
						break;
				}
				//end of processing News Editor actions
				//Set to Redirect back tho this Item's Editor view
			}
			else
			{
				$redirect .= '/'.$item_post_data['item_id'];
			}
			//end of inner if
		}
		else
		{
			IbHelpers::set_message ('Please fill in the provided fields to create a NEW News Story.', 'alert');
		}
		//end of if/else

		//Redirect to the corresponding view specified in this request, POST-ed data: either News Editor or News List
		$this->request->redirect ($redirect);
	}

    public function action_publish()
    {
        $id = $this->request->param ('id');
        $publish = $this->request->post('publish');
        if (!isset($id) || (int)$id < 1)
        {
            $msg = "error";
        }
        else
        {
            $page = new Model_Customscroller();
            $msg  = $page->change_published_status ($id, $publish);
        }
        $this->auto_render = FALSE;
        $this->response->body ($msg);
    }

    public function action_deleteitem()
    {
        $id = $this->request->param ('id');
        if (!isset($id) || (int)$id < 1)
        {
            $msg = "error";
        }
        else
        {
            $page = new Model_Customscroller();
            $msg  = $page->delete_item($id);
        }
        $this->auto_render = FALSE;
        $this->response->body ($msg);
    }

//@TODO NOT IN USE -=> REMOVE IF NOT REQUIRED
	public function action_ajax_toggle_publish ()
	{
		echo "\nCustomScroller->ajax_toggle_publish():\n";
		IbHelpers::die_r($this->request->post());

		$item_to_edit_id   = $this->request->post ('item_id');
		$item_publish_flag = $this->request->post ('publish');

		// Get an instance of the This Plugin Model
		$plugin_model = new Model_Customscroller();

		// Set result body
		$result = array (
			'success_msg' => '', 'err_msg' => ''
		);

//		$toggle_result = Model_Customscroller::toggle_item_publish ($item_to_edit_id, $item_publish_flag);
		$toggle_result = $plugin_model->toggle_item_publish ($item_to_edit_id, $item_publish_flag);

		//Set return message
		if ($toggle_result == 1)
		{
			IbHelpers::set_message ('News Story #'.$item_to_edit_id.' '.(($item_publish_flag == 1) ? 'published' : 'unpublished'), 'success');
			$result['success_msg'] = IbHelpers::get_messages ();
		}
		else
		{
			IbHelpers::set_message ('News Story #'.$item_to_edit_id.' could not be '.(($item_publish_flag == 1) ? 'published' : 'unpublished'), 'error');
			$result['err_msg'] = IbHelpers::get_messages ();
		}

		// Return
		$this->auto_render = FALSE;
		$this->response->body (json_encode ($result));
	}


//@TODO NOT IN USE -=> REMOVE IF NOT REQUIRED
	public function action_ajax_toggle_delete ()
	{
		echo "\nCustomScroller->ajax_toggle_delete():\n";
		IbHelpers::die_r($this->request->post());

		$item_to_delete_id = $this->request->post ('item_id');

		// Get an instance of the This Plugin Model
		$plugin_model = new Model_Customscroller();

		// Set result body
		$result = array (
			'success_msg' => '', 'err_msg' => ''
		);

		//Delete the specified News Story
		$delete_result = $plugin_model->delete ($item_to_delete_id);

		//Set return message
		if ($delete_result == 1)
		{
			IbHelpers::set_message ('News Story #'.$item_to_delete_id.' deleted', 'success');
			$result['success_msg'] = IbHelpers::get_messages ();
		}
		else
		{
			IbHelpers::set_message ('News Story #'.$item_to_delete_id.' could not be deleted', 'error');
			$result['err_msg'] = IbHelpers::get_messages ();
		}

		// Return
		$this->auto_render = FALSE;
		$this->response->body (json_encode ($result));
	}


	public function action_ajax_get_custom_sequence_editor_view ()
	{
		// Get POST-ed Data
		$post_data = $this->request->post();

		// Get an instance of This Plugin Model
		$plugin_model = new Model_Customscroller();

		// Set result body
		$result = array (
			'cs_editor_view' => '',
			'err_msg' => ''
		);

		// Check Inputs
		if (!isset($post_data['plugin_item_id']) OR trim($post_data['plugin_item_id']) == '')
		{
			IbHelpers::set_message ('SYSTEM MESSAGE: "Plugin Item Id" - (plugin_item_id) is NOT PROVIDED', 'error');
			$result['err_msg'] .= IbHelpers::get_messages ();
		}
		if (!isset($post_data['plugin_name']) OR trim($post_data['plugin_name']) == '')
		{
			IbHelpers::set_message ('SYSTEM MESSAGE: "Plugin Name" - (plugin_name) is NOT PROVIDED', 'error');
			$result['err_msg'] .= IbHelpers::get_messages ();
		}

		// Render the Editor View
		if ($result['err_msg'] == '')
		{
			$result['cs_editor_view'] = $plugin_model->get_custom_sequence_editor_view($post_data['plugin_name'], $post_data['plugin_item_id'], $post_data['sequence_id']);
		}

		// Return
		$this->auto_render = FALSE;
		$this->response->body (json_encode ($result));
	}


	public function action_ajax_get_custom_sequence_item_editor_view ()
	{
		// Get POST-ed Data
		$post_data = $this->request->post();

		// Get an instance of This Plugin Model
		$plugin_model = new Model_Customscroller();

		// Set result body
		$result = array (
			'cs_item_editor_view' => '',
			'err_msg' => ''
		);
//@TODO: revise this validation and update if required
		// Check Inputs
		if (!isset($post_data['sequence_item_id']) OR trim($post_data['sequence_item_id']) == '')
		{
			IbHelpers::set_message ('SYSTEM MESSAGE: "Sequence Item Id" - (sequence_item_id) is NOT PROVIDED', 'error');
			$result['err_msg'] .= IbHelpers::get_messages ();
		}
		if (!isset($post_data['plugin_name']) OR trim($post_data['plugin_name']) == '')
		{
			IbHelpers::set_message ('SYSTEM MESSAGE: "Plugin Name" - (plugin_name) is NOT PROVIDED', 'error');
			$result['err_msg'] .= IbHelpers::get_messages ();
		}

		// Render the Editor View
		if ($result['err_msg'] == '')
		{
			$result['cs_item_editor_view'] = $plugin_model->get_custom_sequence_item_editor_view(
				$post_data['plugin_name'],
				$post_data['sequence_item_id'],
				$post_data['item_img'],
				NULL
			);
		}

		// Return
		$this->auto_render = FALSE;
		$this->response->body (json_encode ($result));
	}


	public function action_ajax_get_internal_links_as_select()
	{
		$result = array(
			'err_msg' 			 => '',
			'pages_links_select' => '',
		);

		// Get the Current Page (internal link) to set as selected in the returned drop-down list
		$current_link_id     = $this->request->post('current_link_id');
		$current_link_img_id = $this->request->post('link_image_id');

		// Get Instance of THIS Plugin Model
		$plugin_model = new Model_Customscroller();

		// Get the Select Drop Down with Internal Pages for the CustomScroller Editor Screen
		$result['pages_links_select'] = $plugin_model->get_pages_drop_down_for_editor($current_link_id, $current_link_img_id);

		// Return
		$this->auto_render = FALSE;
		$this->response->body (json_encode ($result));
	}

	public function action_ajax_get_img_html(){

		$result = array(
			'err_msg'  => '',
			'img_html' => '',
		);

		// Get details for the image for which HTML Tag will be rendered
		$img_location = $this->request->post('image_location');
		$img_filename = $this->request->post('image_filename');

		// Render the IMG HTML Tag
		if ($img_location != NULL AND $img_filename != NULL)
		{
			$result['img_html'] = '<img alt="'.$img_filename.'" src="'
								  .Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$img_filename, $img_location.DIRECTORY_SEPARATOR.'_thumbs_cms').'">';
		}// else leave the img_html - EMPTY_STRING

		// Return
		$this->auto_render = FALSE;
		$this->response->body (json_encode ($result));
	}

	public function action_ajax_add_update_custom_sequence_item(){
		$result = array(
			'err_msg'      => '',
			'item_tr_html' => '',
			'item_tr_data' => array(),
		);

		$sequence_item_post_data = $this->request->post();
		$plugin_model = new Model_Customscroller();

		// Validate and ADD / UPDATE Custom Sequence Item
		if ($plugin_model->validate_custom_sequence_item($sequence_item_post_data))
		{
			//@TODO: ADD / UPDATE Plugin Item
			/*
			 * RULES:
			 * 1. When: item_sequence_id = NEW and item_id = NEW -=> JUST generate the Item TR HTML Code if required: get_item_tr_html (should be ALWAYS set to TRUE in this case)
			 * 3. When: item_sequence_id = PROPER_ID_NUMBER and item_id = NEW
			 *    -=> ADD this Item to the DB and generate the Item TR HTML Code if required: get_item_tr_html (most cases should be always TRUE)
			 * 4. When: item_sequence_id = PROPER_ID_NUMBER and item_id = PROPER_ID_NUMBER
			 *    -=> UPDATE this Item in the DB and generate the Item TR HTML Code if required: get_item_tr_html (most cases should be always TRUE)
			 * 5. When: item_sequence_id = NEW and item_id = PROPER_ID_NUMBER -=> SHOULD NEVER HAPPEN or AT LEAST: CURRENTLY we cannot Assign Existent Sequence Items between Sequences
			 *
			 */
			// RULE #1: just return the corresponding Sequence Item TR HTML -=> the corresponding Sequence and Item will be ADDED later to the DB
			if ($sequence_item_post_data['sequence_id'] == 'new' AND $sequence_item_post_data['id'] == 'new')
			{
				$result['item_tr_data'] = $plugin_model->get_sequence_item_tr_data($sequence_item_post_data);
			}
            elseif ($sequence_item_post_data['id'] == 'new')
            {
                $sequence_item_post_data['id'] = $plugin_model->add_custom_sequence_item($sequence_item_post_data, $sequence_item_post_data['sequence_id']);
                $result['item_tr_data'] = $plugin_model->get_sequence_item_tr_data($sequence_item_post_data);
            }
			// RULE #2: Update Existing Sequence Item
			elseif (
					(is_integer((int)$sequence_item_post_data['sequence_id']) AND (int)$sequence_item_post_data['sequence_id'] > 0) AND
					(is_integer((int)$sequence_item_post_data['id'])          AND (int)$sequence_item_post_data['id'] > 0)
			)
			{
				if ($plugin_model->update_custom_sequence_item($sequence_item_post_data))
				{
					$updated_item_data      = $plugin_model->get_custom_sequence_item_details($sequence_item_post_data['id']);
					$result['item_tr_data'] = $plugin_model->get_sequence_item_tr_data($updated_item_data[0]);
				}
				else
				{
					// There was a Problem with Updating this Sequence Item
					IbHelpers::set_message('Sorry, there was a problem with UPDATING of Sequence Item ID: #'.$sequence_item_post_data['id'].'.', 'error');
					$result['err_msg'] = IbHelpers::get_messages();
				}
			}
		}
		else
		{
			// There was a problem in the Validation of the Data for this Sequence Item
			$result['err_msg'] = IbHelpers::get_messages();
		}

		// Return
		$this->auto_render = FALSE;
		$this->response->body (json_encode ($result));
	}

    public function action_ajax_get_sequence_items(){
        $sequence_id = $this->request->param('id');
        $customscroller = new Model_Customscroller();
        $sequence_items = $customscroller->get_custom_sequence_items_admin($sequence_id);

        $html = '';
        foreach ($sequence_items as $sequence_item)
        {
            $html .= $customscroller->get_sequence_item_tr_html($sequence_item);
        }

        echo $html;
        exit();
    }

    public function action_ajax_get_available_images(){
        // here
        $sequence_id = $this->request->param('id');
        $post_data = $this->request->post();

        $customscroller = new Model_Customscroller();
        $available_images = $customscroller->get_available_images_admin($sequence_id, $post_data['plugin']);

        $html ='';
        foreach ($available_images as $available_image)
        {
            $html .= $customscroller->get_available_image_html($available_image);
        }

        echo $html;
        exit();
    }


	//Prepare the last data to be sent to the Browser, before Return HTML Page to the Browser
	function after ()
	{
		// Load the messages from the IbHelper.
		$messages = IbHelpers::get_messages ();

		// If there are messages
		if ($messages)
		{
			// Add the message to the alert string if it exists
			if (isset($this->template->body->alert))
			{
				$this->template->body->alert = $this->template->body->alert.$messages;
			}
			// Else create an alert string
			else
			{
				$this->template->body->alert = $messages;
			}
		}

		parent::after ();
	}
	//end of function

	/* END of Back-End (CMS) : Admin Functions */


	/* ########## - ########## */


	/* Front-End : APP Functions  */
	//@TODO: these are to be further PLANNED, DESIGNED and BUILT

	//@TODO: NOTE!!!
	//@TODO: Currently Front-End Functions are going to be provided as STATIC HELPER FUNCTIONS, and WILL BE BUILT IN THE CORRESPONDING MODEL. In this CASE the Model_Customscroller

	//@TODO: we might come up with another solution, but at the moment it will work like this ;)

	/* END of Front-End : APP Functions  */

}//end of class