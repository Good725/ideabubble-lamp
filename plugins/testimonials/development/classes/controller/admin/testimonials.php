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
 * <h1>Main testimonials Plugin Controller.</h1>
 * Provides Control functions for the Back-End (Admin) News Management.
 *
 * User Guide Wiki Page: <a href="http://wiki.ideabubble.ie/confluence/display/WPP/News+Plugin">News - User's Guide</a>
 *
 * Developer's Guide Wiki Page: <a href="http://wiki.ideabubble.ie/confluence/display/WPP/News">News - Developer's Guide</a>
 * </pre>
 */
class Controller_Admin_Testimonials extends Controller_Cms{

	/* Back-End (CMS) : Admin Functions  */

	public function before() {
		parent::before();

		if ( ! Auth::instance()->has_access('testimonials'))
		{
			IbHelpers::set_message("You need access to the &quot;testimonials&quot; permission to perform this action.", 'warning popup_box');
			$this->request->redirect('/admin');
		}

        // Menu items
        $this->template->sidebar = View::factory('sidebar');
        $this->template->sidebar->menus = array( array (
            array('icon' => 'testimonial', 'name' => 'Testimonials', 'link' => '/admin/testimonials')
        ));
        $this->template->sidebar->breadcrumbs = array(
            array('name' => 'Home',         'link' => '/admin'),
            array('name' => 'Testimonials', 'link' => '/admin/testimonials')
        );
        $this->template->sidebar->tools = '<a href="/admin/testimonials/add_edit_item"><button type="button" class="btn">Add Testimonial</button></a>';
	}


	//Builds the Main - testimonials List HTML view
	public function action_index() {

		// Create an instance of the testimonials Model
		$plugin_model = new Model_Testimonials();
        $plugin_model->plugin = Model_Plugin::get_plugin_by_name('testimonials');;
		//Load Javascript
		$this->template->scripts[] = '<script src="'. URL::get_engine_plugin_assets_base('testimonials') .'js/testimonials_list.js"></script>';

		// Load the body here.
		$this->template->body = View::factory(
			'admin/list_testimonials',
			array('testimonials' => $plugin_model->get_all_items_admin(),'plugin_details' => $plugin_model->plugin)
		);
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

		$plugin_model = new Model_Testimonials();
		//Get data of the testimonials to be edited
		$item_data = (!empty($item_to_edit_id))? $plugin_model->get_all_items_admin($item_to_edit_id) : NULL;

		//Loads the CSS and  javascript files
		$this->template->styles = array_merge(
					$this->template->styles,
					array(
						URL::get_engine_plugin_assets_base('testimonials') .'css/testimonials_edit.css' => 'screen'
					)
				);

		$this->template->scripts[] = '<script src="'. URL::get_engine_plugin_assets_base('testimonials') .'js/testimonials_edit.js"></script>';
		//Load the body
		$this->template->body =  View::factory(
			'admin/add_edit_testimonials_item',
			array(
				'item_data' => $item_data[0],
                'course_categories' => ORM::factory('Course_Category')->order_by('category')->find_all_undeleted(),
                'courses'   => ORM::factory('Course')->order_by('title')->find_all_undeleted(),
                'subjects'  => ORM::factory('Course_Subject')->order_by('name')->find_all_undeleted()
			)
		);

	}//end of function


	public function action_process_editor(){
//		echo "\ntestimonials->process_editor():\n";
//		IbHelpers::die_r($this->request->post());

		$item_post_data = $this->request->post();
		$item_to_edit_id = NULL;
		//Default Redirects to: testimonials - Editor View
		$redirect = '/admin/testimonials/add_edit_item';

		if (!empty($item_post_data)){

			$plugin_model = new Model_Testimonials();

			// Validate
			if ($plugin_model->validate($item_post_data)){

				//Update this testimonials Item based on the specified $item_post_data['editor_action']
				switch($item_post_data['editor_action']){
					case 'delete': //Delete Item
						$item_to_edit_id = $item_post_data['item_id'];
						$delete_testimonials_story_result = $plugin_model->delete($item_to_edit_id);
						//Set corresponding message for the Deleted testimonials
						if($delete_testimonials_story_result == 1){
							IbHelpers::set_message('Testimonials #'.$item_to_edit_id.' deleted successfully.', 'success popup_box');
							$redirect = '/admin/testimonials';
						}else{
							IbHelpers::set_message('Testimonials #'.$item_to_edit_id.' could not be deleted.', 'error popup_box');
							//Redirect back to the testimonials-Editor view
							$redirect .= $redirect.'/'.$item_to_edit_id;
						}
						break;

					case 'add': //Create Item
						// Add the testimonials and get its ID
						$item_to_edit_id = $plugin_model->add($item_post_data);
						//Set corresponding message for the Created testimonials
						if(!empty($item_to_edit_id)){
							IbHelpers::set_message('Testimonials #'.$item_to_edit_id.' Added.', 'success popup_box');
						}else{
							IbHelpers::set_message('There was a problem with the creation of this Testimonial.', 'error popup_box');
						}
						//Set the Redirect based on the specified by the Item Editor view - Redirect location
						$redirect = $item_post_data['editor_redirect'].
									(
										(strpos($item_post_data['editor_redirect'], 'add_edit_item', 0) !== FALSE)? '/'.$item_to_edit_id : ''
									);
						break;

					case 'edit': //Edit Item
						$item_to_edit_id = $item_post_data['item_id'];
						$item_update_result = $plugin_model->update($item_post_data);
						if($item_update_result == 1)
							IbHelpers::set_message('Testimonials #'.$item_to_edit_id.' Updated.', 'success popup_box');
						else
							IbHelpers::set_message('There was a problem with the deletion of Testimonial #'.$item_to_edit_id.'.', 'error popup_box');
						$redirect = $item_post_data['editor_redirect'].((strpos($item_post_data['editor_redirect'], 'add_edit_item', 0) !== FALSE)? '/'.$item_to_edit_id : '');
						break;
				}//end of processing testimonials Editor actions
			//Set to Redirect back tho this Item's Editor view
			}else{
				$redirect .= '/'.$item_post_data['item_id'];
			}//end of inner if
		}else{
			IbHelpers::set_message('Please fill in the provided fields to create a NEW Testimonial.', 'alert popup_box');
		}//end of if/else

		//Redirect to the corresponding view specified in this request, POST-ed data: either testimonials Editor or testimonials List
		$this->request->redirect($redirect);
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

		$toggle_result = Model_Testimonials::toggle_item_publish($item_to_edit_id, $item_publish_flag);

		//Set return message
		if($toggle_result == 1){
			IbHelpers::set_message('Testimonial #'.$item_to_edit_id.' '.(($item_publish_flag == 1)? 'published' : 'unpublished'), 'success popup_box');
			$result['success_msg'] = IbHelpers::get_messages();
		}else{
			IbHelpers::set_message('Testimonial #'.$item_to_edit_id.' could not be '.(($item_publish_flag == 1)? 'published' : 'unpublished'), 'error popup_box');
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

		//Delete the specified testimonials
		$delete_result = Model_Testimonials::factory('Testimonials')->delete($item_to_delete_id);

		//Set return message
		if($delete_result == 1){
			IbHelpers::set_message('Testimonial #'.$item_to_delete_id.' deleted', 'success popup_box');
			$result['success_msg'] = IbHelpers::get_messages();
		}else{
			IbHelpers::set_message('Testimonial #'.$item_to_delete_id.' could not be deleted', 'error popup_box');
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

	/* END of Back-End (CMS) : Admin Functions */


/* ########## - ########## */


	/* Front-End : APP Functions  */
		//@TODO: these are to be further PLANNED, DESIGNED and BUILT

		//@TODO: NOTE!!!
		//@TODO: Currently Front-End Functions are going to be provided as STATIC HELPER FUNCTIONS, and WILL BE BUILT IN THE CORRESPONDING MODEL. In this CASE the Model_News

		//@TODO: we might come up with another solution, but at the moment it will work like this ;)

	/* END of Front-End : APP Functions  */

}//end of class
