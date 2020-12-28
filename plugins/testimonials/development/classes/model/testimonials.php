<?php
/**
 * <pre>
 * Created by JetBrains PhpStorm.
 * User: Kosta
 * Date: 10/01/2013
 * Time: 09:15
 * To change this template use File | Settings | File Templates.
 *
 * <h1>Main testimonials Plugin Model.</h1>
 * Provides CRUD functions for the Back-End (Admin) testimonials Management and testimonials Listing Functions for the Front-End testimonials display.
 *
 * User Guide Wiki Page: <a href="http://wiki.ideabubble.ie/confluence/display/WPP/testimonials+Plugin">testimonials - User's Guide</a>
 *
 * Developer's Guide Wiki Page: <a href="http://wiki.ideabubble.ie/confluence/display/WPP/testimonials">testimonials - Developer's Guide</a>
 * </pre>
 */

class Model_Testimonials extends Model {

	/*
	 * @TODO: Add Documentation-Comments for each of the Public/Helper Functions
	 */

	private static $model_items_table = 'plugin_testimonials';
	private static $model_item_categories_table = 'plugin_testimonials_categories';

	/* Back-End (CMS) : Admin Functions  */


	// Retrieve all testimonials or the One - Specified by the $item_id, and are available to this Admin User
	public function get_all_items_admin ($item_id = NULL)
	{
		$select = DB::select(
			'testimonials.*',
			'categories.category',
			array('users_create.name', 'created_by_name'),
			array('roles_create.role', 'created_by_role'),
			array('users_modify.name', 'modified_by_name'),
			array('roles_modify.role', 'modified_by_role')
		)
			->from(array(self::$model_items_table, 'testimonials'))
				->join(array(self::$model_item_categories_table, 'categories'))
					->on('testimonials.category_id', '=', 'categories.id')
				->join(array('engine_users', 'users_create'), 'left')->on('testimonials.created_by', '=', 'users_create.id')
				->join(array('engine_users', 'users_modify'), 'left')->on('testimonials.modified_by', '=', 'users_modify.id')
				->join(array('engine_project_role', 'roles_create'), 'left')
					->on('users_create.role_id', '=', 'roles_create.id')
				->join(array('engine_project_role', 'roles_modify'), 'left')
					->on('users_modify.role_id', '=', 'roles_modify.id')
				->where('testimonials.deleted', '=', 0);
		if (is_null ($item_id))
		{
			return $select->execute()->as_array();
		}
		else
		{
			return $select->and_where('testimonials.id', '=', $item_id)->execute()->as_array();
		}
	}

	//end of function


	//Validate input testimonial - information
	public function validate ($item_data_to_validate)
	{
//		echo "\nValidate testimonial: \n";
//		IbHelpers::die_r($item_data_to_validate);

		$item_valid = TRUE;

		//check input data
		if (empty($item_data_to_validate['item_title']))
		{
			IbHelpers::set_message ('Please add "Testimonial Title".', 'error popup_box');
			$item_valid = FALSE;
		}
		if ($item_data_to_validate['item_category_id'] == 0)
		{
			IbHelpers::set_message ('Please select "Testimonial Category".', 'error popup_box');
			$item_valid = FALSE;
		}

		//return
		return $item_valid;
	}

	//end of function


	// Add a testimonial to the database
	public function add ($item_input_data)
	{
//		echo "\nAdd testimonial: \n";
//		IbHelpers::die_r($item_input_data);

		// 1. get the ID of the currently-logged in user, i.e. the user who is making the update
		$logged_in_user = Auth::instance ()->get_user ();

		/* 2. Create the NEW testimonial */
		//Set the testimonial data to be added to Database
		$item_to_add_data['category_id']    = $item_input_data['item_category_id'];
		$item_to_add_data['title']          = $item_input_data['item_title'];
		$item_to_add_data['summary']        = $item_input_data['item_summary'];
		$item_to_add_data['content']        = $item_input_data['item_content'];
		$item_to_add_data['image']          = $item_input_data['item_image'];
		$item_to_add_data['banner_image']   = $item_input_data['item_banner_image'];
		$item_to_add_data['event_date']     = ($item_input_data['item_event_date'] != '0000-00-00 00:00:00' AND !empty($item_input_data['item_event_date'])) ?
				date ('Y-m-d H:i:s', strtotime ($item_input_data['item_event_date'])) : NULL;
        $item_to_add_data['item_signature'] = $item_input_data['item_signature'];
        $item_to_add_data['item_website']   = $item_input_data['item_website'];
        $item_to_add_data['item_position']   = $item_input_data['item_position'];
        $item_to_add_data['item_company']   = $item_input_data['item_company'];
        $item_to_add_data['publish']        = $item_input_data['item_publish'];

        if (isset($item_input_data['item_course_item_id']) && Model_Plugin::is_enabled_for_role('Administrator', 'courses')) {
            // Only one of these can be set at a time
            $item_to_update_data['course_id'] = '';
            $item_to_update_data['course_category_id'] = '';
            $item_to_update_data['subject_id'] = '';

            if (strpos($item_input_data['item_course_item_id'], '-') > 0) {
                $item_to_update_data[explode('-', $item_input_data['item_course_item_id'])[0].'_id'] = explode('-', $item_input_data['item_course_item_id'])[1];
            }
        }
		// Format the required dates for mysql storage
		$item_to_add_data['date_created']  = date ('Y-m-d H:i:s');
		$item_to_add_data['created_by']    = $logged_in_user['id'];
		$item_to_add_data['date_modified'] = $item_to_add_data['date_created'];
		$item_to_add_data['modified_by']   = $logged_in_user['id'];
		$item_to_add_data['deleted']       = 0;
		//add the testimonial to DB
		$insert_result = DB::insert (self::$model_items_table)->values ($item_to_add_data)->execute ();

		// return new ID
		return $insert_result[0];
	}

	//end of function


	//Update a testimonial record
	public function update ($item_update_data)
	{
        // 1. get the ID of the currently-logged in user, i.e. the user who is making the update
        $logged_in_user = Auth::instance ()->get_user ();

        /* 2. Update the existent testimonial */
        //Set the testimonial data to be Updated to Database
        $item_to_update_data['category_id']    = $item_update_data['item_category_id'];
        $item_to_update_data['title']          = $item_update_data['item_title'];
        $item_to_update_data['summary']        = $item_update_data['item_summary'];
        $item_to_update_data['content']        = $item_update_data['item_content'];
        $item_to_update_data['image']          = $item_update_data['item_image'];
        $item_to_update_data['banner_image']   = $item_update_data['item_banner_image'];
        $item_to_update_data['event_date']     = ($item_update_data['item_event_date'] != '0000-00-00 00:00:00' AND !empty($item_update_data['item_event_date'])) ?
            date ('Y-m-d H:i:s', strtotime ($item_update_data['item_event_date'])) : NULL;
        $item_to_update_data['item_signature'] = $item_update_data['item_signature'];
        $item_to_update_data['item_website']   = $item_update_data['item_website'];
        $item_to_update_data['item_position']  = $item_update_data['item_position'];
        $item_to_update_data['item_company']   = $item_update_data['item_company'];
        $item_to_update_data['publish']        = $item_update_data['item_publish'];

        if (isset($item_update_data['item_course_item_id']) && Model_Plugin::is_enabled_for_role('Administrator', 'courses')) {
            // Only one of these can be set at a time
            $item_to_update_data['course_id'] = '';
            $item_to_update_data['course_category_id'] = '';
            $item_to_update_data['subject_id'] = '';

            if (strpos($item_update_data['item_course_item_id'], '-') > 0) {
                $item_to_update_data[explode('-', $item_update_data['item_course_item_id'])[0].'_id'] = explode('-', $item_update_data['item_course_item_id'])[1];
            }
        }

		// Format the required dates for mysql storage
//		$item_to_update_data['date_created'] = date('Y-m-d H:i:s');
//		$item_to_update_data['created_by'] = $logged_in_user['id'];
		$item_to_update_data['date_modified'] = date ('Y-m-d H:i:s');
		$item_to_update_data['modified_by']   = $logged_in_user['id'];
		$item_to_update_data['deleted']       = 0;

		//Update the testimonial to DB
		$update_result = DB::update (self::$model_items_table)->set ($item_to_update_data)->where ('id', '=', $item_update_data['item_id'])->execute ();

		// return
		return $update_result;
	}

	//end of function


	//Sets a specified testimonial Record in the DB as Deleted
	public function delete ($item_id)
	{
//		echo "\nDelete testimonial: \n";
//		IbHelpers::die_r($item_id);

		// 1. get the ID of the currently-logged in user, i.e. the user who is making the update
		$logged_in_user = Auth::instance ()->get_user ();

		// 2. Mark the specified testimonial as deleted - in this case result will be INT - holding the number of affected rows
		$delete_result = DB::update (self::$model_items_table)->set (
					array (
						  'deleted' => 1, 'date_modified' => date ('Y-m-d H:i:s'), 'modified_by' => $logged_in_user['id']
					)
				)->where ('id', '=', $item_id)->execute ();

		// 3. return
		return $delete_result;
	}

	//end of function


	/**
	 * Function used to Return all testimonial Categories based on the specified <em>$return_type</em>.
	 *
	 * @param string $return_type       String holding the return type of the data to be returned.<br />
	 *                              <em>Possible Values:</em><br />
	 *                              - <strong>details</strong> - will return the testimonials-Categories as an associative array in the form:<br />
	 *                                  array('category_id' => 'Category')<br />
	 *                              - <strong>options</strong> - will return all testimonial-Categories listed as Select drop-down options. in the form:<br />
	 *                              &lt;option value="<em>CATEGORY_ID</em>"&gt;<em>CATEGORY</em>&lt;/option&gt;<br />
	 *                              <em>DEFAULT VALUE</em>: <strong>options</strong>
	 *
	 * @param NULL   $selected_category String holding the Option to be set to: <em>selected="selected"</em> by default.<br />
	 *                                  <em>DEFAULT VALUE</em>: <strong>NULL</strong>
	 *
	 * @return array|string Array or String, based on the specified by the <em>$return_type</em> value.
	 */
	public static function get_item_categories_as ($return_type = 'options', $selected_category = NULL)
	{
		$return_item_categories = NULL;

		//Get a list with All testimonials Categories
		$item_categories = DB::select ()->from (self::$model_item_categories_table)->execute ()->as_array ();

		switch ($return_type)
		{
			case 'details':
				foreach ($item_categories as $item_category) $return_item_categories[$item_category['id']] = $item_category['category'];
				break;

			case 'options':
			default:
				$return_item_categories = '';
				foreach ($item_categories as $category) $return_item_categories .= '<option value="'.$category['id'].'"'.((!empty($selected_category) AND $selected_category == $category['id']) ? ' selected="selected"' : '').'>'.$category['category'].'</option>';
				break;

		}

		//end of generating result with testimonials-Categories

		return $return_item_categories;
	}

	//end of function



	public static function toggle_item_publish ($item_id, $publish_flag)
	{
//		echo "\nToggle Publish testimonial: \n";
//		IbHelpers::pre_r(array('item_id' => $item_id, 'publish_flag' => $publish_flag));

		// 1. get the ID of the currently-logged in user, i.e. the user who is making the update
		$logged_in_user = Auth::instance ()->get_user ();

		// 2. Toggle the Publish flag of the specified testimonial
		$toggle_publish_result = DB::update (self::$model_items_table)->set (
					array (
						  'publish' => $publish_flag, 'date_modified' => date ('Y-m-d H:i:s'), 'modified_by' => $logged_in_user['id']
					)
				)->where ('id', '=', $item_id)->execute ();

		// 3. return
		return $toggle_publish_result;
	}

    public static function get_animation_types($current)
    {
        $options = array(
            array(
                'value' => 'horizontal',
                'name' => 'Horizontal scroll'
            ),
            array(
                'value' => 'vertical',
                'name' => 'Vertical scroll'
            ),
            array(
                'value' => 'fade',
                'name' => 'Fade'
            ),
            array(
                'value' => 'fixed',
                'name' => 'Fixed'
            )
        );
        $return = '';
        foreach ($options AS $option) {
            $selected = '';
            if ($option['value'] == $current) {
                $selected = ' selected="selected"';
            }
            $return .= '<option value="'.$option['value'].'"'.$selected.'>'.$option['name'].'</option>';
        }
        return $return;
    }

	//end of function


	/* END of Back-End (CMS) : Admin Functions */


	/* ########## - ########## */


	/* Front-End : APP Functions  */
	/* These are some Front-End Related Functions that will facilitate the Display of testimonials Items etc for the Front End*/

	public static function get_all_items_front_end ($item_id = null, $item_category = null, $args = array())
	{
        $args['placeholder'] = isset($args['placeholder']) ? $args['placeholder'] : false;

        $media_path = Model_Media::get_image_path(null, 'testimonials');

        $select = DB::select(
            'testimonials.*',
            'categories.category'
        )
            ->from(array(self::$model_items_table, 'testimonials'))
            ->join(array(self::$model_item_categories_table, 'categories'))
            ->on('testimonials.category_id', '=', 'categories.id')
            ->where('testimonials.deleted', '=', 0)
            ->where('testimonials.publish', '=', 1);

        if (!empty($item_category)) {
            $select->and_where('categories.category', '=', $item_category);
        }

		// Get specific item
		if (!is_null ($item_id)) {
			$select->and_where('testimonials.id', '=', $item_id);
		}
        // Get all available items
		else {
			//@TODO: query ORDER BY - TO BE SPECIFIED IN A SETTINGS for the testimonials FEED
			$select->order_by ('date_modified', 'DESC');
		}

        $testimonials = $select->execute()->as_array();

        foreach ($testimonials as &$testimonial) {
            $image = ($testimonial['image'] == '' && $args['placeholder'])
                ? 'testimonials-placeholder.png'
                : $testimonial['image'];

            $testimonial['image_url'] = $media_path.$image;
        }

        return $testimonials;

	}

	//end of function


	/*
	 * The Functions Bellow are HELPER Functions, which will be used to provide Functionalities for the Front-End rendering of THIS Plugin Items
	 */

	//@TODO: Add PHP Docs Comments
	//Get all available Items and list the as a Feed with summaries (snippets)
	public static function get_plugin_items_front_end_feed ($items_category = 'Testimonials')
	{

		$plugin_feed_html = '';

		//Get All Available for the Front-End testimonials Items for the specified Category
		$front_end_items = self::get_all_items_front_end (
			NULL, //item_id - left NUL so we pick all items under the specified Category
			$items_category
		);

		//Generate the HTML Code for this Plugin Feed
		if ($front_end_items AND count ($front_end_items) > 0)
		{
			$feed_items_html = '';

            //Maximum length of a testimonial, before it gets cut (...)
            $max_length = Settings::instance()->get('testimonials_truncation');
            if ($max_length == '') {
                $max_length = 200;
            }

            //Number of testimonials to display
            $feed_item_count = Settings::instance()->get('testimonials_feed_item_count');
            if ($feed_item_count == '') {
                $feed_item_count = 3;
            }
            $front_end_items = array_slice($front_end_items, 0, $feed_item_count);

			//Generate a feed with all items properly rendered in HTML formatted snippets for the feed
			shuffle ($front_end_items);
			foreach ($front_end_items as $feed_item_data)
			{
				//Prepare a summary for this feed item, if no summary is present
				if (empty($feed_item_data['summary']))
				{
					//Take 1st two paragraphs from this item's content
					$feed_item_data['summary'] = substr (
						$feed_item_data['content'], //string
						0, //start
						(strpos ($feed_item_data['content'], '</p>', 0) + 4) //length
					);
				}
				//else -=> use the Summary as it is

                $feed_item_data['summary'] = substr($feed_item_data['summary'], 0, $max_length);

                //Set the corresponding to this Feed Item - Feed_item_snippet, based on its Category. DEFAULT to: testimonials_feed_item_html
				$snippet_view = 'front_end'.DIRECTORY_SEPARATOR.'snippets'.DIRECTORY_SEPARATOR.((!empty($feed_item_data['category'])) ? strtolower ($feed_item_data['category']) : 'testimonials').'_feed_item_html';

				//Get the HTML code for this feed item
				$feed_items_html .= View::factory (
					$snippet_view, array (
						'feed_item_data' => $feed_item_data
					)
				);
			}
			//end of generating snippets with feed items

            //Animation type
            $animation_type = Settings::instance()->get('testimonials_animation_type');
            if ($animation_type == '') {
                $animation_type = 'horizontal';
            }
            //Timeout speed
            $timeout = intval(Settings::instance()->get('testimonials_feed_timeout'));
            if ($timeout == 0) {
                $timeout = 8000;
            }

			//Return the final feed view
			$plugin_feed_html = View::factory (
				'front_end/testimonials_feed_view', array (
					'feed_items' => $feed_items_html,
                    'animation_type' => $animation_type,
                    'testimonials' => $front_end_items,
                    'timeout' => $timeout
				)
			);
		}

		//Return the final Feed HTML Code
		return $plugin_feed_html;

	}

	//end of function

	//@TODO: Add PHP Docs Comments
	public static function get_plugin_items_front_end_list ($item_id, $item_category = 'Testimonials')
	{
//		IbHelpers::pre_r("\nModel_Testimonials->get_plugin_items_front_end_list(): \n");
//		IbHelpers::die_r(array('item_id'=>$item_id, 'item_category'=>$item_category));

		//Default to testimonials Category if $item_category is NULL
		if (is_null ($item_category)) $item_category = 'Testimonials';

		$front_end_items_data = NULL;
		$items_list_html      = '';
		//Get the testimonials Items to be listed for the Front-end
		if (!is_null ($item_id) AND !empty($item_id) AND $item_id > 0)
		{
			//Get just this Item
			$front_end_items_data = self::get_all_items_front_end ($item_id, $item_category);
		}
		else
		{
			//Get All Items for the specified Category
			$front_end_items_data = self::get_all_items_front_end (NULL, $item_category);
		}

		//List Items Details
		if (is_array ($front_end_items_data) AND count ($front_end_items_data) == 1)
		{
			//Set this Item - details view
			$item_details_view = 'front_end'.DIRECTORY_SEPARATOR.((!empty($front_end_items_data[0]['category'])) ? strtolower ($front_end_items_data[0]['category']) : 'testimonials').'_item_details_html';
			//List One Item -=> details_view
			$items_list_html = View::factory (
				$item_details_view, array (
										  'item_data' => $front_end_items_data[0]
									)
			);
		}
		else if (is_array ($front_end_items_data) AND count ($front_end_items_data) > 1)
		{
			//List Multiple Items -> short_details
			foreach ($front_end_items_data as $item_data)
			{
				//Prepare a summary for this Feed Item, if no Summary is present
				if (empty($item_data['summary']))
				{
					//Take 1st 2 Paragraphs from this item's content
					$item_data['summary'] = substr (
						$item_data['content'], //string
						0, //start
						(strpos ($item_data['content'], '</p>', 0) + 4) //length
					);
				}
				//else -=> use the Summary as it is

				//set the snippet for this Item
				$item_summary_view = 'front_end'.DIRECTORY_SEPARATOR.'snippets'.DIRECTORY_SEPARATOR.((!empty($item_data['category'])) ? strtolower ($item_data['category']) : 'testimonials').'_item_summary_html';

				$items_list_html .= View::factory (
					$item_summary_view, array (
											  'item_data' => $item_data
										)
				);
			}
		}
		else
		{
			//There was no available items to be listed
			//@TODO: SET a corresponding message
		}

		//Return Feed
		return $items_list_html;

	}
	//end of function

	/* END of Front-End : APP Functions  */

}//end of class
