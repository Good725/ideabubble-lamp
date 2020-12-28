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
class Controller_Admin_News extends Controller_Cms{

	/* Back-End (CMS) : Admin Functions  */

	public function before() {
		parent::before();

		if ( ! Auth::instance()->has_access('news'))
		{
			IbHelpers::set_message("You need access to the &quot;news&quot; permission to perform this action.", 'warning popup_box');
			$this->request->redirect('/admin');
		}

        // Menu items
        $this->template->sidebar = View::factory('sidebar');
        $this->template->sidebar->menus = array( array (
            array('icon' => 'news', 'name' => 'News', 'link' => '/admin/news')
        ));
        $this->template->sidebar->breadcrumbs = array(
            array('name' => 'Home', 'link' => '/admin'),
            array('name' => 'News', 'link' => '/admin/news')
        );
        $this->template->sidebar->tools = '<a href="/admin/news/add_edit_item"><button type="button" class="btn">Add News Story</button></a>';
	}


	//Builds the Main - News List HTML view
	public function action_index() {

		// Create an instance of the News Model
		$plugin_model = new Model_News();

		//Load Javascript
		$this->template->scripts[] = '<script src="'. URL::get_engine_plugin_assets_base('news') .'js/news_list.js"></script>';

        $user = Auth::instance()->get_user();
		// Load the body here.
		$this->template->body = View::factory(
			'admin/list_news',
			array('news' => $plugin_model->get_all_items_admin(null, $user['role_id']))
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

		$plugin_model = new Model_News();
		//Get data of the News Story to be edited
		$item_data = (!empty($item_to_edit_id))? $plugin_model->get_all_items_admin($item_to_edit_id) : NULL;
		$shared_with_groups = Model_News::get_shared_with($item_to_edit_id);
        $role_model            = new Model_Roles;
        $roles                 = $role_model->get_all_roles();

        // Get news items occurring on the same date
        $same_date_news = array();
        if (isset($item_data[0])) {
            $item = $item_data[0];
            $same_date_news = Model_News::getItemsAdminSelect(array('event_date' => date('Y-m-d', strtotime($item['event_date']))))
                ->where('news.id', '!=', $item['id'])->execute()->as_array();
        }

		//Loads the CSS and  javascript files
		$this->template->styles = array_merge(
					$this->template->styles,
					array(
						URL::get_engine_plugin_assets_base('news') .'css/news_edit.css' => 'screen'
					)
				);
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('media') . 'js/multiple_upload.js"></script>';
        $this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('media') . 'js/image_edit.js"></script>';
		$this->template->scripts[] = '<script src="' . URL::get_engine_plugin_assets_base('news') .'js/news_edit.js"></script>';
		//Load the body
		$this->template->body =  View::factory(
			'admin/add_edit_news_item',
			array(
                'item_data'          => $item_data[0],
                'course_categories'  => ORM::factory('Course_Category')->order_by('category')->find_all_undeleted(),
                'courses'            => ORM::factory('Course')->order_by('title')->find_all_undeleted(),
                'media_types'        => ORM::factory('News_Item')->get_enum_options('media_type'),
                'roles'              => $roles,
                'same_date_news'     => $same_date_news,
                'shared_with_groups' => $shared_with_groups,
                'subjects'           => ORM::factory('Course_Subject')->order_by('name')->find_all_undeleted(),
			)
		);

	}//end of function


	public function action_process_editor(){
//		echo "\nNews->process_editor():\n";
//		IbHelpers::die_r($this->request->post());

		$item_post_data = $this->request->post();
		$item_to_edit_id = NULL;
		//Default Redirects to: News - Editor View
		$redirect = '/admin/news/add_edit_item';

		if ($item_post_data['shared_with'] == "0") {
			unset ($item_post_data['shared_with_roles']);
		}

		if (!empty($item_post_data)){

			$plugin_model = new Model_News();

			// Validate
			if ($plugin_model->validate($item_post_data)){

				//Update this News Story Item based on the specified $item_post_data['editor_action']
				switch($item_post_data['editor_action']){
					case 'delete': //Delete Item
						$item_to_edit_id = $item_post_data['item_id'];
						$delete_news_story_result = $plugin_model->delete($item_to_edit_id);
						//Set corresponding message for the Deleted News Story
						if($delete_news_story_result == 1){
							IbHelpers::set_message('News Story #'.$item_to_edit_id.' deleted successfully.', 'success popup_box');
							$redirect = '/admin/news';
						}else{
							IbHelpers::set_message('News Story #'.$item_to_edit_id.' could not be deleted.', 'error popup_box');
							//Redirect back to the News Story-Editor view
							$redirect .= $redirect.'/'.$item_to_edit_id;
						}
						break;

					case 'add': //Create Item
						// Add the News Story and get its ID
						$item_to_edit_id = $plugin_model->add($item_post_data);
						//Set corresponding message for the Created News Story
						if(!empty($item_to_edit_id)){
							IbHelpers::set_message('News Story #'.$item_to_edit_id.' Added.', 'success popup_box');
						}else{
							IbHelpers::set_message('There was a problem with the creation of this News Story.', 'error popup_box');
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
							IbHelpers::set_message('News Story #'.$item_to_edit_id.' Updated.', 'success popup_box');
						else
							IbHelpers::set_message('There was a problem with the deletion of News Story #'.$item_to_edit_id.'.', 'error popup_box');
						$redirect = $item_post_data['editor_redirect'].((strpos($item_post_data['editor_redirect'], 'add_edit_item', 0) !== FALSE)? '/'.$item_to_edit_id : '');
						break;
				}//end of processing News Editor actions

			//Set to Redirect back tho this Item's Editor view
			}else{
				$redirect .= '/'.$item_post_data['item_id'];
			}//end of inner if
		}else{
			IbHelpers::set_message('Please fill in the provided fields to create a NEW News Story.', 'alert popup_box');
		}//end of if/else

		//Redirect to the corresponding view specified in this request, POST-ed data: either News Editor or News List
		$this->request->redirect($redirect);
	}//end of function


    // Get news articles for the same day as the current item
    public function action_ajax_get_news()
    {
        $this->auto_render = false;
        $return = array();
        try {
            $date              = $this->request->query('event_date');
            $current_id        = $this->request->query('current_id');
            $news_items        = Model_News::getItemsAdminSelect(array('event_date' => $date))->where('news.id', '!=', $current_id)->execute()->as_array();

            $return['success'] = true;
            $return['items']   = $news_items;
        }
        catch(Exception $e) {
            Log::instance()->add(Log::ERROR, "Error fetching news items.\n".$e->getMessage()."n".$e->getTraceAsString());
            $return['success'] = false;
            $return['message'] = __('Error saving the news items. If this problem continues, please ask the administration to check the error logs.');
        }

        echo json_encode($return);
    }


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

		$toggle_result = Model_News::toggle_item_publish($item_to_edit_id, $item_publish_flag);

		//Set return message
		if($toggle_result == 1){
			IbHelpers::set_message('News Story #'.$item_to_edit_id.' '.(($item_publish_flag == 1)? 'published' : 'unpublished'), 'success popup_box');
			$result['success_msg'] = IbHelpers::get_messages();
		}else{
			IbHelpers::set_message('News Story #'.$item_to_edit_id.' could not be '.(($item_publish_flag == 1)? 'published' : 'unpublished'), 'error popup_box');
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

		//Delete the specified News Story
		$delete_result = Model_News::factory('News')->delete($item_to_delete_id);

		//Set return message
		if($delete_result == 1){
			IbHelpers::set_message('News Story #'.$item_to_delete_id.' deleted', 'success popup_box');
			$result['success_msg'] = IbHelpers::get_messages();
		}else{
			IbHelpers::set_message('News Story #'.$item_to_delete_id.' could not be deleted', 'error popup_box');
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
