<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Kosta
 * Date: 23/10/2012
 * Time: 10:23
 * To change this template use File | Settings | File Templates.
 */

class Controller_Admin_Panels extends Controller_Cms{


	/* Back-End (CMS) : Admin Functions  */

	public function before() {
		parent::before();

		if ( ! Auth::instance()->has_access('panels'))
		{
			IbHelpers::set_message("You need access to the &quot;panels&quot; permission to perform this action.", 'warning popup_box');
			$this->request->redirect('/admin');
		}

        // Menu items
        $this->template->sidebar = View::factory('sidebar');
        $this->template->sidebar->menus = array( array (
            array('icon' => 'panels', 'name' => 'Panels', 'link' => '/admin/panels')
        ));
        $this->template->sidebar->breadcrumbs = array(
            array('name' => 'Home',   'link' => '/admin'),
            array('name' => 'Panels', 'link' => '/admin/panels')
        );
        $this->template->sidebar->tools = '<a href="/admin/panels/add_edit_item"><button type="button" class="btn">Add Panel</button></a>';
	}


	//Builds the Main - Panels List HTML view
	public function action_index() {

		// Create an instance of the Panels Model
		$plugin_model = new Model_Panels();

		//Load Javascript
		$this->template->scripts[] = '<script src="'. URL::get_engine_plugin_assets_base('panels') .'js/panels_list.js"></script>';

		// Load the body here.
		$this->template->body = View::factory(
			'admin/list_panels',
			array('panels' => $plugin_model->get_all_items_admin())
		);
        $this->template->body->plugin = Model_Plugin::get_plugin_by_name('news');
	}//end of function


	public function action_add_edit_item($item_id = FALSE){
//		echo "\nFunction: add_edit_item()\n";
//		IbHelpers::die_r($this->request->param());

		//Get the ID from the request
		if($item_id === FALSE)
			$item_to_edit_id = $this->request->param('id');
		//The ID was passed from an internal function as a parameter
		else
			$item_to_edit_id = $item_id;

		$plugin_model = new Model_Panels();
		//Get data of the Panel to be edited
		$item_data = (!empty($item_to_edit_id))? $plugin_model->get_all_items_admin($item_to_edit_id) : NULL;

		//@TODO: Revise this code below and remove if not required or update so it will work as expected
		//The Specified Panel-ID was invalid, or there is not Panel with this ID to be Edited
//		if(empty($item_data)){
//			$this->request->redirect('admin/panels');
//			return FALSE;
//		}

        $panel_id  = (!empty($item_to_edit_id))?$item_data[0]['id']:$plugin_model->get_new_item_id();

        $panel_type_options = $plugin_model->get_panel_types_as_options($item_data[0]['type_id']);
        $predefined_options = $plugin_model->get_predefined_panels_as_options($item_data[0]['predefined_id']);

		//Loads the CSS and  javascript files
        $this->template->styles[URL::get_engine_plugin_assets_base('media').'css/smoothness/jquery-ui-1.8.18.custom.css'] = 'screen';
        $this->template->styles[URL::get_engine_plugin_assets_base('panels').'css/panel_edit.css'] = 'screen';

		$this->template->scripts[] = '<script src="'. URL::get_engine_plugin_assets_base('panels') .'js/panel_edit.js"></script>';
        $this->template->scripts[] = '<script src="'. URL::get_engine_plugin_assets_base('media') .'js/multiple_upload.js"></script>';
        $this->template->scripts[] = '<script src="'. URL::get_engine_plugin_assets_base('media') .'js/cropzoom.min.js"></script>';
        $this->template->scripts[] = '<script src="'. URL::get_engine_plugin_assets_base('media') .'js/image_edit.js"></script>';
		//Load the body
		$this->template->body =  View::factory(
			'admin/add_edit_panel_item',
			array(
                'panel_data' => $item_data[0],
                'panel_type_options' => $panel_type_options,
                'predefined_options' => $predefined_options,
                'panel_id' => $panel_id
            )
		);

	}//end of function


	public function action_process_editor(){
//		echo "\nFunction: process_editor()\n";
//		IbHelpers::die_r($this->request->post());

		$item_post_data = $this->request->post();
		$item_to_edit_id = NULL;
		//Default Redirects to: Panels - Editor View
		$redirect = '/admin/panels/add_edit_item';

		if (!empty($item_post_data)){

			$plugin_model = new Model_Panels();

			// Validate
			if ($plugin_model->validate($item_post_data)){
				//@TODO: remove the commented code if still here after 1st of January 2013
				//$editor_action = $item_post_data['editor_action'];

				//Update this Panel Item based on the specified $item_post_data['editor_action']
				switch($item_post_data['editor_action']){
					case 'delete': //Delete Item
						$item_to_edit_id = $item_post_data['panel_id'];
						$delete_panel_result = $plugin_model->delete($item_to_edit_id);
						//Set corresponding message for the Deleted Panel
						if($delete_panel_result == 1){
							IbHelpers::set_message('Panel #'.$item_to_edit_id.' deleted successfully.', 'success popup_box');
							$redirect = '/admin/panels';
						}else{
							IbHelpers::set_message('Panel #'.$item_to_edit_id.' could not be deleted.', 'error popup_box');
							//Redirect back to the Panel-Editor view
							$redirect .= $redirect.'/'.$item_to_edit_id;
						}
						break;

					case 'add': //Create Item
						// Add the Panel and get its ID
						$item_to_edit_id = $plugin_model->add($item_post_data);
						//Set corresponding message for the Created Panel
						if(!empty($item_to_edit_id)){
							IbHelpers::set_message('Panel #'.$item_to_edit_id.' Added.', 'success popup_box');
						}else{
							IbHelpers::set_message('There was a problem with the creation of this Panel.', 'error popup_box');
						}
						//Set the Redirect based on the specified by the Item Editor view - Redirect location
						$redirect = $item_post_data['editor_redirect'].
									(
										(strpos($item_post_data['editor_redirect'], 'add_edit_item', 0) !== FALSE)? '/'.$item_to_edit_id : ''
									);
						break;

					case 'edit': //Edit Item
						$item_to_edit_id = $item_post_data['panel_id'];
						$item_update_result = $plugin_model->update($item_post_data);
						if($item_update_result == 1)
							IbHelpers::set_message('Panel #'.$item_to_edit_id.' Updated.', 'success popup_box');
						else
							IbHelpers::set_message('There was a problem with the deletion of Panel #'.$item_to_edit_id.'.', 'error popup_box');
						$redirect = $item_post_data['editor_redirect'].((strpos($item_post_data['editor_redirect'], 'add_edit_item', 0) !== FALSE)? '/'.$item_to_edit_id : '');
						break;
				}//end of processing Panels Editor actions
			//Set to Redirect back tho this Item's Editor view
			}else{
				$redirect .= '/'.$item_post_data['panel_id'];
			}//end of inner if
		}else{
			IbHelpers::set_message('Please fill in the provided fields to create a NEW Panel.', 'alert popup_box');
		}//end of if/else

		//Redirect to the corresponding view specified in this request, POST-ed data: either Panels Editor or Panels List
		$this->request->redirect($redirect);
	}//end of function


	public function action_ajax_toggle_publish(){
//		echo "\nFunction ajax_toggle_publish():\n";
//		IbHelpers::die_r($this->request->post());

		$item_to_edit_id = $this->request->post('panel_id');
		$item_publish_flag = $this->request->post('publish');

		// Set result body
		$result = array(
			'success_msg' => '',
			'err_msg' => ''
		);

		$toggle_result = Model_Panels::toggle_item_publish($item_to_edit_id, $item_publish_flag);

		//Set return message
		if($toggle_result == 1){
			IbHelpers::set_message('Panel #'.$item_to_edit_id.' '.(($item_publish_flag == 1)? 'published' : 'unpublished'), 'success popup_box');
			$result['success_msg'] = IbHelpers::get_messages();
		}else{
			IbHelpers::set_message('Panel #'.$item_to_edit_id.' could not be '.(($item_publish_flag == 1)? 'published' : 'unpublished'), 'error popup_box');
			$result['err_msg'] = IbHelpers::get_messages();
		}

		//Return
		$this->auto_render = false;
		$this->response->body(json_encode($result));
	}//end of function


	public function action_ajax_toggle_delete(){
//		echo "\nFunction ajax_toggle_delete():\n";
//		IbHelpers::die_r($this->request->post());

		$item_to_delete_id = $this->request->post('panel_id');

		// Set result body
		$result = array(
			'success_msg' => '',
			'err_msg' => ''
		);

		//Delete the specified Panel
		$delete_result = Model_Panels::factory('Panels')->delete($item_to_delete_id);

		//Set return message
		if($delete_result == 1){
			IbHelpers::set_message('Panel #'.$item_to_delete_id.' deleted', 'success popup_box');
			$result['success_msg'] = IbHelpers::get_messages();
		}else{
			IbHelpers::set_message('Panel #'.$item_to_delete_id.' could not be deleted', 'error popup_box');
			$result['err_msg'] = IbHelpers::get_messages();
		}

		//Return
		$this->auto_render = false;
		$this->response->body(json_encode($result));
	}//end of function

    function action_panel_preview(){
        $id = $this->request->param('id');

        $this->template->body = Model_Panels::get_css_styles();
        $this->template->body .= Model_Panels::get_current_panel_feed($id);
    }

    // AJAX function for generating sublist in the plugins' dropdown
    public function action_ajax_get_submenu($data_only = false)
    {
        $model           = new Model_Panels;
        $items           = $model->get_all_items_admin();
        $return['link']  = 'add_edit_item';
        $return['items'] = array();

        for ($i = 0; $i < sizeof($items) && $i < 10; $i++) {
            $return['items'][] = array('id' => $items[$i]['id'],'title' => ($items[$i]['title']));
        }

        if ($data_only) {
            return $return;
        } else {
            $this->auto_render = false;
            $this->response->headers('Content-type', 'application/json; charset=utf-8');
            $this->response->body(json_encode($return));
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

	/* END of Back-End (CMS) : Admin Functions */


/* ########## - ########## */


	/* Front-End : APP Functions  */
		//@TODO: these are to be further PLANNED, DESIGNED and BUILT



	/* END of Front-End : APP Functions  */

}//end of class
