<?php
defined('SYSPATH') OR die('No Direct Script Access');

/**
 *
 */
Class Controller_Admin_Presets extends Controller_Cms {

	//This one has the Role of a Constructor
	public function before() {
		parent::before();

		//Set the Media Plugin Specific Menu
		$this->template->sidebar = View::factory('sidebar');
		$this->template->sidebar->menus = array(
			'Media Uploader' => array(
				array(
                    'icon' => 'media',
					'name' => 'Media',
					'link' => '/admin/media'
				),
				array(
                    'icon' => 'Presets',
					'name' => 'Presets',
					'link' => '/admin/presets'
				)
			)
		);
        $this->template->sidebar->breadcrumbs = array(
            array('name' => 'Home',    'link' => '/admin'),
            array('name' => 'Media',   'link' => '/admin/media'),
            array('name' => 'Presets', 'link' => '/admin/presets')
        );
        $this->template->sidebar->tools = '<a href="#" class="media_presets_editor_trigger" id="media_presets_editor_trigger"><button type="button" class="btn">Add Preset</button></a>';

	}//end of before


	/**
	 * INDEX - Main Function called to render the Main Media View
	 */
	public function action_index() {

		if ( ! Auth::instance()->has_access('media'))
		{
			IbHelpers::set_message("You need access to the &quot;media&quot; permission to perform this action.", 'warning');
			$this->request->redirect('/admin');
		}

		$presets_model = new Model_Presets();

		//Load JavaScript for Rendering of Presets Items
		$this->template->scripts[] = '<script src="'. URL::get_engine_plugin_assets_base('media') .'js/presets_list.js"></script>';

		// main view
		$this->template->body = View::factory(
			'list_presets',
			array(
				'presets_items' => $presets_model->get_all_items_admin()
			)
		);

	}//end of function


	public function action_ajax_add_edit_item(){
//		echo "\nFunction: add_edit_item()\n";
//		IbHelpers::die_r($this->request->post());

		if ( ! Auth::instance()->has_access('media'))
		{
			IbHelpers::set_message("You need access to the &quot;media&quot; permission to perform this action.", 'warning');
			$response['err_msg'] = IbHelpers::get_messages();
		}
		else
		{
			$plugin_model = new Model_Presets();
			$response = array(
				'success_msg' => '',
				'err_msg' => ''
			);

			//Get data of the Preset to be edited
			$item_data = $this->request->post();

			//validate input and Add/Update Preset item
			if($plugin_model->validate($item_data)){
				//Update Preset Item
				if(isset($item_data['item_id']) AND !empty($item_data['item_id'])){
					//Update Preset
					$item_updated = $plugin_model->update($item_data);
					//Get corresponding Update Success/Error messages
					if($item_updated){
						$response['success_msg'] = IbHelpers::get_messages();
					}else{
						$response['err_msg'] = IbHelpers::get_messages();
					}

				//Add Preset Item
				}else{
					$item_created = $plugin_model->add($item_data);

					if($item_created){
						$response['success_msg'] = IbHelpers::get_messages();
					}else{
						$response['err_msg'] = IbHelpers::get_messages();
					}
				}
			//Passed Preset Data is NOT Valid
			}else{
				//Get Error Messages and Return
				$response['err_msg'] = IbHelpers::get_messages();
			}
		}

		//return
		$this->auto_render = false;
		$this->response->body(json_encode($response));

	}//end of function


	public function action_ajax_get_preset_details(){
//		echo "\nFunction: add_edit_item()\n";
//		IbHelpers::die_r($this->request->post());

		$plugin_model = new Model_Presets();
		$item_id = $this->request->post('item_id');
		$item_data = $plugin_model->get_all_items_admin($item_id);

		//return
		$this->auto_render = false;
		$this->response->body(json_encode($item_data[0]));

	}//end of function


	public function action_ajax_toggle_publish(){
//		echo "\nFunction ajax_toggle_publish():\n";
//		IbHelpers::die_r($this->request->post());

		$item_to_edit_id = $this->request->post('item_id');
		$item_publish_flag = $this->request->post('publish');

		// Set result body
		$result = array(
			'success_msg' => '',
			'err_msg' => ''
		);

		if ( ! Auth::instance()->has_access('media'))
		{
			IbHelpers::set_message("You need access to the &quot;media&quot; permission to perform this action.", 'warning');
			$toggle_result = false;
		}
		else
		{
			$toggle_result = Model_Presets::toggle_item_publish($item_to_edit_id, $item_publish_flag);
		}

		//Set return message
		if($toggle_result == 1){
			IbHelpers::set_message('Preset #'.$item_to_edit_id.' '.(($item_publish_flag == 1)? 'published' : 'unpublished'), 'success');
			$result['success_msg'] = IbHelpers::get_messages();
		}else{
			IbHelpers::set_message('Preset #'.$item_to_edit_id.' could not be '.(($item_publish_flag == 1)? 'published' : 'unpublished'), 'error');
			$result['err_msg'] = IbHelpers::get_messages();
		}

		//Return
		$this->auto_render = false;
		$this->response->body(json_encode($result));
	}//end of function


	public function action_ajax_toggle_delete(){
//		echo "\nFunction ajax_toggle_delete():\n";
//		IbHelpers::die_r($this->request->post());

		$item_to_delete_id = $this->request->post('item_id');

		// Set result body
		$result = array(
			'success_msg' => '',
			'err_msg' => ''
		);

		if ( ! Auth::instance()->has_access('media'))
		{
			IbHelpers::set_message("You need access to the &quot;media&quot; permission to perform this action.", 'warning');
			$delete_result = false;
		}
		else
		{
			//Delete the specified Preset
			$delete_result = Model_Presets::factory('Presets')->delete($item_to_delete_id);
		}

		//Set return message
		if($delete_result == 1){
			IbHelpers::set_message('Preset #'.$item_to_delete_id.' deleted', 'success');
			$result['success_msg'] = IbHelpers::get_messages();
		}else{
			IbHelpers::set_message('Preset #'.$item_to_delete_id.' could not be deleted', 'error');
			$result['err_msg'] = IbHelpers::get_messages();
		}

		//Return
		$this->auto_render = false;
		$this->response->body(json_encode($result));
	}//end of function



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

}//end of class