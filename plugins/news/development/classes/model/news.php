<?php
/**
 * <pre>
 * Created by JetBrains PhpStorm.
 * User: Kosta
 * Date: 10/01/2013
 * Time: 09:15
 * To change this template use File | Settings | File Templates.
 *
 * <h1>Main NEWS Plugin Model.</h1>
 * Provides CRUD functions for the Back-End (Admin) News Management and News Listing Functions for the Front-End News display.
 *
 * User Guide Wiki Page: <a href="http://wiki.ideabubble.ie/confluence/display/WPP/News+Plugin">News - User's Guide</a>
 *
 * Developer's Guide Wiki Page: <a href="http://wiki.ideabubble.ie/confluence/display/WPP/News">News - Developer's Guide</a>
 * </pre>
 */

class Model_News extends Model {

    /*
     * @TODO: Add Documentation-Comments for each of the Public/Helper Functions
     */

    private static $model_items_table = 'plugin_news';
    private static $model_item_categories_table = 'plugin_news_categories';
    private static $plugin_media_folder = 'www/media/photos/news';


    /* Back-End (CMS) : Admin Functions  */

    public static function getItemsAdminSelect($params = array())
    {
        $select = DB::select(
            'news.*',
            'categories.category',
            array('users_create.name', 'created_by_name'),
            array('roles_create.role', 'created_by_role'),
            array('users_modify.name', 'modified_by_name'),
            array('roles_modify.role', 'modified_by_role')
        )
            ->from(array('plugin_news', 'news'))
                ->join(array('plugin_news_categories', 'categories'), 'left')
                    ->on('news.category_id', '=', 'categories.id')
                ->join(array('engine_users', 'users_create'), 'left')->on('news.created_by', '=', 'users_create.id')
                ->join(array('engine_users', 'users_modify'), 'left')->on('news.modified_by', '=', 'users_modify.id')
                ->join(array('engine_project_role', 'roles_create'), 'left')
                    ->on('users_create.role_id', '=', 'roles_create.id')
                ->join(array('engine_project_role', 'roles_modify'), 'left')
                    ->on('users_modify.role_id', '=', 'roles_modify.id')
            ->where('news.deleted', '=', 0);

        if (!empty($params['event_date'])) {
            // Remove the hours, minutes and seconds from the item date and compare that to the supplied date
            $select->where(DB::expr("DATE_FORMAT(`news`.`event_date`, '%Y-%m-%d')"), '=', $params['event_date']);
        }

        if (isset($params['role_id'])) {
            $user = Auth::instance()->get_user();
            $shared_select = DB::select('shared.news_id', DB::expr("count(*) as share_count"))
                ->from(array(Model_News_Shared::TABLE, 'shared'))
                ->where('shared.deleted', '=', 0)
                ->group_by('shared.news_id');

            $select->join(array($shared_select, 'share_counts'), 'left')->on('news.id', '=', 'share_counts.news_id')
                ->join(array(Model_News_Shared::TABLE, 'shared'), 'left')->on('news.id', '=', 'shared.news_id')
                ->and_where_open()
                    ->or_where('shared.role_id', '=', $params['role_id'])
                    ->or_where('share_counts.share_count', 'is', null)
                    ->or_where('news.created_by', '=', $user['id'])
                ->and_where_close();
        }
        return $select;
    }

    public static function getItemsFrontendSelect()
    {
        $microsite_suffix = isset(Kohana::$config->load('config')->project_suffix) ? Kohana::$config->load('config')->project_suffix : '';

        $select = DB::select(
            'news.*',
            array(DB::expr("REPLACE(`news`.`title`, '--".$microsite_suffix."', '')"), 'title'),
            array(DB::expr("CONCAT(`authors`.`name`, ' ', `authors`.`surname`)"), 'author'),
            'categories.category'
        )
            ->from(array('plugin_news', 'news'))
                ->join(array('plugin_news_categories', 'categories'), 'left')
                    ->on('news.category_id', '=', 'categories.id')
                ->join(array('engine_users', 'authors'), 'left')
                    ->on('news.created_by', '=', 'authors.id')
            ->where('news.publish', '=', 1)
            ->and_where('news.deleted', '=', 0);
        return $select;
    }
    // Retrieve all News or the One - Specified by the $item_id, and are available to this Admin User
    public function get_all_items_admin ($item_id = NULL, $role_id = null)
    {

        if (is_null ($item_id))
        {
            return self::getItemsAdminSelect(array('role_id' => $role_id))->order_by('news.date_modified', 'desc')->execute ()->as_array ();
        }
        else
        {
            return self::getItemsAdminSelect(array('role_id' => $role_id))
                ->where('news.id', '=', $item_id)
                ->order_by('news.date_modified')
                ->execute()
                ->as_array();
        }
    }

    //end of function


    //Validate input News Story - information
    public function validate ($item_data_to_validate)
    {
//		echo "\nValidate News Story: \n";
//		IbHelpers::die_r($item_data_to_validate);

        $item_valid = TRUE;

        //check input data
        if (empty($item_data_to_validate['item_title']))
        {
            IbHelpers::set_message ('Please add "News Story Title".', 'error');
            $item_valid = FALSE;
        }
        if ($item_data_to_validate['item_category_id'] == 0)
        {
            IbHelpers::set_message ('Please select "News Story Category".', 'error');
            $item_valid = FALSE;
        }

        //return
        return $item_valid;
    }

    //end of function


    // Add a News Story to the database
    public function add ($item_input_data)
    {
//		echo "\nAdd News Story: \n";
//		IbHelpers::die_r($item_input_data);

        // 1. get the ID of the currently-logged in user, i.e. the user who is making the update
        $logged_in_user = Auth::instance ()->get_user ();

        /* 2. Create the NEW News Story */
        //Set the News Story data to be added to Database
        $item_to_add_data['category_id']  = $item_input_data['item_category_id'];
        $item_to_add_data['title']        = $item_input_data['item_title'];
        $item_to_add_data['summary']      = $item_input_data['item_summary'];
        $item_to_add_data['content']      = $item_input_data['item_content'];
        $item_to_add_data['author']       = $item_input_data['author'];
        $item_to_add_data['image']        = $item_input_data['item_image'];
        $item_to_add_data['alt_text']     = (isset($item_input_data['item_alt_text']))   ? $item_input_data['item_alt_text']   : '';
        $item_to_add_data['title_text']   = (isset($item_input_data['item_title_text'])) ? $item_input_data['item_title_text'] : '';
        $item_to_add_data['seo_title']    = $item_input_data['item_seo_title'];
        $item_to_add_data['seo_keywords'] = $item_input_data['item_seo_keywords'];
        $item_to_add_data['seo_description']   = $item_input_data['item_seo_description'];
        $item_to_add_data['seo_footer']   = $item_input_data['item_seo_footer'];
        $item_to_add_data['event_date']   = ($item_input_data['item_event_date'] != '0000-00-00 00:00:00' AND !empty($item_input_data['item_event_date'])) ?
            date ('Y-m-d H:i:s', strtotime ($item_input_data['item_event_date'])) : NULL;
        $item_to_add_data['date_publish'] = ($item_input_data['item_date_publish'] != '0000-00-00 00:00:00' AND !empty($item_input_data['item_date_publish'])) ?
            date ('Y-m-d H:i:s', strtotime ($item_input_data['item_date_publish'])) : NULL;
        $item_to_add_data['date_remove']  = ($item_input_data['item_date_remove'] != '0000-00-00 00:00:00' AND !empty($item_input_data['item_date_publish'])) ?
            date ('Y-m-d H:i:s', strtotime ($item_input_data['item_date_remove'])) : NULL;
        $item_to_add_data['order']        = $item_input_data['item_order'];
        $item_to_add_data['media_type']   = $item_input_data['item_media_type'];
        $item_to_add_data['publish']      = $item_input_data['item_publish'];

        if (isset($item_input_data['item_course_item_id']) && Model_Plugin::is_enabled_for_role('Administrator', 'courses')) {
            // Only one of these can be set at a time
            $item_to_add_data['course_id'] = '';
            $item_to_add_data['course_category_id'] = '';
            $item_to_add_data['subject_id'] = '';

            if (strpos($item_input_data['item_course_item_id'], '-') > 0) {
                $item_to_add_data[explode('-', $item_input_data['item_course_item_id'])[0].'_id']
                    = explode('-', $item_input_data['item_course_item_id'])[1];
            }
        }

        // Format the required dates for mysql storage
        $item_to_add_data['date_created']  = date ('Y-m-d H:i:s');
        $item_to_add_data['created_by']    = $logged_in_user['id'];
        $item_to_add_data['date_modified'] = $item_to_add_data['date_created'];
        $item_to_add_data['modified_by']   = $logged_in_user['id'];
        $item_to_add_data['deleted']       = 0;
        //add the News Story to DB
        $insert_result = DB::insert (self::$model_items_table)->values ($item_to_add_data)->execute ();

        self::set_shared($insert_result[0], @$item_input_data['shared_with_roles']);

        // return new ID
        return $insert_result[0];
    }

    //end of function


    //Update a News Story record
    public function update ($item_update_data)
    {
//		echo "\nUpdate News Story: \n";
//		IbHelpers::die_r($item_update_data);

        // 1. get the ID of the currently-logged in user, i.e. the user who is making the update
        $logged_in_user = Auth::instance ()->get_user ();

        /* 2. Update the existent News Story */
        //Set the News Story data to be Updated to Database
        $item_to_update_data['category_id']  = $item_update_data['item_category_id'];
        $item_to_update_data['title']        = $item_update_data['item_title'];
        $item_to_update_data['summary']      = $item_update_data['item_summary'];
        $item_to_update_data['content']      = $item_update_data['item_content'];
        $item_to_update_data['author']       = $item_update_data['item_author'];
        $item_to_update_data['image']        = $item_update_data['item_image'];
        $item_to_update_data['alt_text']     = (isset($item_update_data['item_alt_text']))   ? $item_update_data['item_alt_text']   : '';
        $item_to_update_data['title_text']   = (isset($item_update_data['item_title_text'])) ? $item_update_data['item_title_text'] : '';
        $item_to_update_data['seo_title']    = $item_update_data['item_seo_title'];
        $item_to_update_data['seo_keywords'] = $item_update_data['item_seo_keywords'];
        $item_to_update_data['seo_description']   = $item_update_data['item_seo_description'];
        $item_to_update_data['seo_footer']   = $item_update_data['item_seo_footer'];
        $item_to_update_data['event_date']   = ($item_update_data['item_event_date'] != '0000-00-00 00:00:00' AND !empty($item_update_data['item_event_date'])) ?
            date ('Y-m-d H:i:s', strtotime ($item_update_data['item_event_date'])) : NULL;
        $item_to_update_data['date_publish'] = ($item_update_data['item_date_publish'] != '0000-00-00 00:00:00' AND !empty($item_update_data['item_date_publish'])) ?
            date ('Y-m-d H:i:s', strtotime ($item_update_data['item_date_publish'])) : NULL;
        $item_to_update_data['date_remove']  = ($item_update_data['item_date_remove'] != '0000-00-00 00:00:00' AND !empty($item_update_data['item_date_publish'])) ?
            date ('Y-m-d H:i:s', strtotime ($item_update_data['item_date_remove'])) : NULL;
        $item_to_update_data['order']        = $item_update_data['item_order'];
        $item_to_update_data['media_type']   = $item_update_data['item_media_type'];
        $item_to_update_data['publish']      = $item_update_data['item_publish'];
        // Format the required dates for mysql storage
//		$item_to_update_data['date_created'] = date('Y-m-d H:i:s');
//		$item_to_update_data['created_by'] = $logged_in_user['id'];
        $item_to_update_data['date_modified'] = date ('Y-m-d H:i:s');
        $item_to_update_data['modified_by']   = $logged_in_user['id'];
        $item_to_update_data['deleted']       = 0;

        if (isset($item_update_data['item_course_item_id']) && Model_Plugin::is_enabled_for_role('Administrator', 'courses')) {
            // Only one of these can be set at a time
            $item_to_update_data['course_id'] = '';
            $item_to_update_data['course_category_id'] = '';
            $item_to_update_data['subject_id'] = '';

            if (strpos($item_update_data['item_course_item_id'], '-') > 0) {
                $item_to_update_data[explode('-', $item_update_data['item_course_item_id'])[0].'_id']
                    = explode('-', $item_update_data['item_course_item_id'])[1];
            }
        }

        //Update the News Story to DB
        $update_result = DB::update (self::$model_items_table)->set ($item_to_update_data)->where ('id', '=', $item_update_data['item_id'])->execute ();

        self::set_shared($item_update_data['item_id'], @$item_update_data['shared_with_roles']);
        // return
        return $update_result;
    }

    //end of function


    //Sets a specified News Story Record in the DB as Deleted
    public function delete ($item_id)
    {
//		echo "\nDelete News Story: \n";
//		IbHelpers::die_r($item_id);

        // 1. get the ID of the currently-logged in user, i.e. the user who is making the update
        $logged_in_user = Auth::instance ()->get_user ();

        // 2. Mark the specified News Story as deleted - in this case result will be INT - holding the number of affected rows
        $delete_result = DB::update (self::$model_items_table)->set (
            array (
                'deleted' => 1, 'date_modified' => date ('Y-m-d H:i:s'), 'modified_by' => $logged_in_user['id']
            )
        )->where ('id', '=', $item_id)->execute ();

        // 3. return
        return $delete_result;
    }

    //end of function


    public static function set_shared($news_id, $shared_roles)
    {
        if (empty($shared_roles)) {
            DB::update(Model_News_Shared::TABLE)->set(array('deleted' => 1))->where('news_id', '=', $news_id)->execute();
        } else {
            foreach ($shared_roles as $role_id) {
                $exists = DB::select('*')
                    ->from(Model_News_Shared::TABLE)
                    ->where('news_id', '=', $news_id)
                    ->and_where('role_id', '=', $role_id)
                    ->execute()
                    ->current();
                if ($exists) {
                    if ($exists['deleted'] == 1) {
                        DB::update(Model_News_Shared::TABLE)
                            ->set(array('deleted' => 0))
                            ->where('news_id', '=', $news_id)
                            ->and_where('role_id', '=', $role_id)
                            ->execute();
                    }
                } else {
                    DB::insert(Model_News_Shared::TABLE)
                        ->values(
                            array(
                                'news_id' => $news_id,
                                'role_id' => $role_id,
                                'deleted' => 0
                            )
                        )
                        ->execute();
                }
            }
        }

        if (!empty($shared_roles) && count($shared_roles)) {
            DB::update(Model_News_Shared::TABLE)
                ->set(array('deleted' => 1))
                ->where('news_id', '=', $news_id)
                ->and_where('role_id', 'not in', $shared_roles)
                ->execute();
        }
    }

    /**
     * Function used to Return all News Story Categories based on the specified <em>$return_type</em>.
     *
     * @param string $return_type       String holding the return type of the data to be returned.<br />
     *                              <em>Possible Values:</em><br />
     *                              - <strong>details</strong> - will return the News-Categories as an associative array in the form:<br />
     *                                  array('category_id' => 'Category')<br />
     *                              - <strong>options</strong> - will return all News Story-Categories listed as Select drop-down options. in the form:<br />
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

        //Get a list with All News Categories
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

        //end of generating result with News-Categories

        return $return_item_categories;
    }

    //end of function


    public static function toggle_item_publish ($item_id, $publish_flag)
    {
//		echo "\nToggle Publish News Story: \n";
//		IbHelpers::pre_r(array('item_id' => $item_id, 'publish_flag' => $publish_flag));

        // 1. get the ID of the currently-logged in user, i.e. the user who is making the update
        $logged_in_user = Auth::instance ()->get_user ();

        // 2. Toggle the Publish flag of the specified News Story
        $toggle_publish_result = DB::update (self::$model_items_table)->set (
            array (
                'publish' => $publish_flag, 'date_modified' => date ('Y-m-d H:i:s'), 'modified_by' => $logged_in_user['id']
            )
        )->where ('id', '=', $item_id)->execute ();

        // 3. return
        return $toggle_publish_result;
    }

    //end of function

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
    // end of function

    /* END of Back-End (CMS) : Admin Functions */


    /* ########## - ########## */


    /* Front-End : APP Functions  */
    /* These are some Front-End Related Functions that will facilitate the Display of News Items etc for the Front End*/

    public static function get_all_items_front_end ($item_id = NULL, $item_category = 'News', $amount = NULL, $offset = NULL, $args = array())
    {
        $args['placeholder'] = isset($args['placeholder']) ? $args['placeholder'] : false;

        //Get specific Item
        if ( ! is_null ($item_id))
        {
            $results = self::getItemsFrontendSelect()->where('news.id', '=', $item_id)->execute()->as_array();
        }
        //Get All available Items
        else
        {
            if (!is_array($item_category)) {
                $item_category = [$item_category];
            }

            //@TODO: query ORDER BY - TO BE SPECIFIED IN A SETTINGS for the NEWS FEED
            $query = self::getItemsFrontendSelect()
				->where('categories.category', 'in', $item_category)
				->and_where_open()
					->where('news.date_publish', 'IS', NULL)
					->or_where('news.date_publish', '<', DB::expr('NOW()'))
				->and_where_close()
				->and_where_open()
					->where('news.date_remove', 'IS', NULL)
					->or_where('news.date_remove', '>', DB::expr('NOW()'))
				->and_where_close()
			    // Put numbers 1, 2, 3, ... first. Then list 0 and NULL.
				->order_by(DB::expr("CASE WHEN news.`order`='0' THEN NULL ELSE -news.`order` END"), 'DESC')
				->order_by('news.event_date', 'DESC');

            $microsite_suffix = isset(Kohana::$config->load('config')->project_suffix) ? Kohana::$config->load('config')->project_suffix : '';

            if (!empty($microsite_suffix)) {
                // Only get news items that end in the microsite suffix or do not use any suffix
                $query
                    ->and_where_open()
                        ->where('news.title', 'like', '%--'.$microsite_suffix)
                        ->or_where('news.title', 'not like', '%--%')
                    ->and_where_close();
            }

            if (is_numeric($amount)) {
                $query->limit($amount);
                if (is_numeric($offset)) {
                    $query->offset($offset);
                }
            }

			$results = $query->execute()->as_array();
        }
		$pages_model = new Model_Pages;


        $media_path = Model_Media::get_image_path(null, 'news');

        foreach ($results as $key => $value) {
            $results[$key]['news_url'] = $pages_model->filter_name_tag($value['title']).'.html';
            $results[$key]['summary'] = IbHelpers::expand_short_tags($value['summary']);
            $results[$key]['content'] = IbHelpers::expand_short_tags($value['content']);

            $image = ($value['image'] == '' && $args['placeholder'])
                ? 'news-placeholder.png'
                : $value['image'];

            $results[$key]['image_url'] = $media_path.$image;
        }

		return $results;
    }

    //end of function

    public static function get_item_front_end($item_title = NULL, $item_category = 'News')
    {
        $return = null;

        // Get specific item based on string
        if (!is_null ($item_title)) {
            $microsite_suffix = isset(Kohana::$config->load('config')->project_suffix) ? Kohana::$config->load('config')->project_suffix : '';

            // First check if a news title specific to the microsite exists
            if ($microsite_suffix) {
                $return = self::getItemsFrontendSelect()
                    ->where(DB::expr('lower(news.title)'), '=', $item_title.'--'.$microsite_suffix)
                    ->execute()
                    ->as_array();
            }

            // Otherwise get a regular news item by the specified name
            if (empty($return)) {
                $return = self::getItemsFrontendSelect()
                    ->where(DB::expr('lower(news.title)'), '=', $item_title)
                    ->execute()
                    ->as_array();
            }
        }

        return $return;
    }


    /***
     * The Functions Below are HELPER Functions, which will be used to provide Functionalities for the Front-End rendering of THIS Plugin Items
     *
     * @param string $items_category
     * @param string $feed_holder_view OPTINAL, only need to be provided if we are ussign a diferent view for the same helper
     * @param string $feed_item_view   OPTINAL, only need to be provided if we are ussign a diferent view for the same helper
     *
     * @return string HTML
     *
     */
    //@TODO: Add PHP Docs Comments
    //Get all available Items and list the as a Feed with summaries (snippets)
    public static function get_plugin_items_front_end_feed ($items_category = 'News', $feed_holder_view = '', $feed_item_view = '')
    {

        $plugin_feed_html = '';

        //Get All Available for the Front-End News Items for the specified Category
        $front_end_items = self::get_all_items_front_end (
            NULL, //item_id - left NUL so we pick all items under the specified Category
            $items_category
        );

        //Generate the HTML code for this plugin feed
        if ($front_end_items AND count ($front_end_items) > 0)
        {
            $feed_items_html = '';

            //Maximum length of a news summary, before it gets cut (...)
            $max_length = Settings::instance()->get('news_truncation');
            if ($max_length == '') {
                $max_length = 100;
            }

            //Number of news items to display
            $feed_item_count = Settings::instance()->get('news_feed_item_count');
            if ($feed_item_count == '') {
                $feed_item_count = 3;
            }
            $front_end_items = array_slice($front_end_items, 0, $feed_item_count);

            //Generate a feed with all items properly rendered in HTML formatted snippets for the feed
            foreach ($front_end_items as $feed_item_data)
            {
                // When the "summary" is empty, the first paragraph of the "content" is set as the summary.
                // This is an old rule, which should be removed, but doing so will affect old sites that have come to depend on it.
                // "original_summary" (the summary un-overwritten is used instead) on newer sites.
                $feed_item_data['original_summary'] = $feed_item_data['summary'];

                //Prepare a summary for this Feed Item, if no Summary is present
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

                //If is set a view we take this view, else we use the default view
                if (!empty($feed_item_view))
                {
                    $snippet_view = 'front_end'.DIRECTORY_SEPARATOR.'snippets'.DIRECTORY_SEPARATOR.$feed_item_view;
                }
                else
                {
                    //Set the corresponding to this Feed Item - Feed_item_snippet, based on its Category. DEFAULT to: news_feed_item_html
                    $snippet_view = 'front_end'.DIRECTORY_SEPARATOR.'snippets'.DIRECTORY_SEPARATOR.((!empty($feed_item_data['category'])) ? strtolower ($feed_item_data['category']) : 'news').'_feed_item_html';
                }

                //URL CONVERSION: ID to Title
                $pages_module               = new Model_Pages();
                $feed_item_data['news_url'] = $pages_module->filter_name_tag ($feed_item_data['title']).'.html';

                //Get the HTML code for this Feed Item
                $feed_items_html .= View::factory (
                    $snippet_view, array (
                        'feed_item_data' => $feed_item_data
                    )
                );
            }
            //end of generating snippets with feed items

            //Animation type
            $animation_type = Settings::instance()->get('news_animation_type');
            if ($animation_type == '') {
                $animation_type = 'vertical';
            }
            //Timeout speed
            $timeout = intval(Settings::instance()->get('news_feed_timeout'));
            if ($timeout == 0) {
                $timeout = 8000;
            }

            //Return the Final Feed View
            if (empty($feed_holder_view))
            {
                $feed_holder_view = 'news_feed_view';
            }
            $plugin_feed_html = View::factory (
                'front_end/'.$feed_holder_view, array (
                    'feed_items' => $feed_items_html,
                    'animation_type' => $animation_type,
                    'timeout' => $timeout
                )
            );
        }

        //Return the final Feed HTML Code
        return $plugin_feed_html;

    }

    //end of function

    //Change the ID for the news title
    public static function get_news_id ($item_id)
    {
        $microsite_suffix = isset(Kohana::$config->load('config')->project_suffix) ? Kohana::$config->load('config')->project_suffix : '';

        //The title will be filtered using the same logic as in the pages plugin
        $page    = new Model_Pages();
        $item_id = $page->filter_name_tag ($item_id);

        //if is set html
        if ($found_position = strrpos ($item_id, '.html')) {
            $item_id = substr ($item_id, 0, $found_position);
        }

        $sql = DB::select ('title', 'id')->from (self::$model_items_table)->where ('deleted', '=', '0')->and_where ('publish', '=', '1')->execute ()->as_array ();

        foreach ($sql as $news) {
            $title = $page->filter_name_tag ($news['title']);

            if ($title == $item_id || $title == $item_id.'--'.$microsite_suffix) {
                return $news['id'];
            }
        }

        return 0;
    }

    //@TODO: Add PHP Docs Comments
    public static function get_plugin_items_front_end_list($item_id, $item_category = 'News', $ajax = FALSE, $amount = NULL, $offset = NULL)
    {
//		IbHelpers::pre_r("\nModel_News->get_plugin_items_front_end_list(): \n");
//		IbHelpers::die_r(array('item_id'=>$item_id, 'item_category'=>$item_category));

        //Default to News Category if $item_category is NULL
        if (is_null ($item_category)) $item_category = 'News';

        $front_end_items_data = NULL;
        $items_list_html      = '';
        //Get the News Items to be listed for the Front-end
        if ( ! is_null ($item_id) AND ! empty($item_id))
        {
            //If is title get the ID
            if ((string)(int)$item_id != $item_id)
            {
                $item_id = Model_News::get_news_id ($item_id);
            }

            //Get just this Item
            $front_end_items_data = self::get_all_items_front_end($item_id, $item_category);
        }
        else
        {
            // Get all items for the specified category
            $categories = ($item_category == 'News') ? ['News', 'Offers'] : $item_category;
            $front_end_items_data = self::get_all_items_front_end(NULL, $categories, $amount, $offset);
        }

        // List Items Details
        if (is_array ($front_end_items_data) AND count ($front_end_items_data) == 1 AND ! $ajax)
        {
            //Set this Item - details view
            $item_details_view = 'front_end'.DIRECTORY_SEPARATOR.'news_item_details_html';

            /*
             * Get the "previous" and "next" items.
             * (Items in the same category that come immediately before and after this item)
             */

            // The date of this item, is the "publish date". If that is empty, use the "event date".
            $current_item_date = false;
            if ( ! empty($front_end_items_data[0]['event_date']))   $current_item_date = $front_end_items_data[0]['event_date'];
            if ( ! empty($front_end_items_data[0]['date_publish'])) $current_item_date = $front_end_items_data[0]['date_publish'];

            $x = $front_end_items_data[0];

            if ($current_item_date)
            {
                // Similarly the dates of all other events, is the publish date, if it exists. Otherwise, it's the event date
                $date_column = DB::expr("IFNULL(`date_publish`, `event_date`)");

                // Get the news item occurring before the current one
                $front_end_items_data[0]['prev'] = ORM::factory('News_Item')
                    ->where($date_column, '<=', $current_item_date)
                    ->where('id', '!=', $front_end_items_data[0]['id'])
                    ->where('category_id', '=', $front_end_items_data[0]['category_id'])
                    ->order_by($date_column,  'desc')
                    ->find_published();

                // Get the news item occurring after the current one
                $front_end_items_data[0]['next'] = ORM::factory('News_Item')
                    ->where($date_column, '>=', $current_item_date)
                    ->where('id', '!=', $front_end_items_data[0]['id'])
                    ->where('category_id', '=', $front_end_items_data[0]['category_id'])
                    ->order_by($date_column,  'asc')
                    ->find_published();
            }

            //List One Item -=> details_view
            $items_list_html = View::factory (
                $item_details_view, array (
                    'item_data' => $front_end_items_data[0]
                )
            );
        }
        else if (is_array ($front_end_items_data) AND (count ($front_end_items_data) > 1 OR $ajax))
        {
            //List Multiple Items -> short_details
            foreach ($front_end_items_data as $item_data)
            {
                // When the "summary" is empty, the first paragraph of the "content" is set as the summary.
                // This is an old rule, which should be removed, but doing so will affect old sites that have come to depend on it.
                // "original_summary" (the summary un-overwritten is used instead) on newer sites.
                $item_data['original_summary'] = $item_data['summary'];

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

                $pages_module          = new Model_Pages();
                $item_data['news_url'] = $pages_module->filter_name_tag ($item_data['title']).'.html';

                // Check if news summary as an <img> in it
                $item_data['summary_has_image'] = strpos($item_data['summary'], '<img') ? true : false;

                //set the snippet for this Item
                $item_summary_view = 'front_end'.DIRECTORY_SEPARATOR.'snippets'.DIRECTORY_SEPARATOR.'news_item_summary_html';

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

		if ( ! $ajax AND Settings::instance()->get('news_infinite_scroller') == 1)
		{
			$items_list_html .= '<script src="'.URL::get_engine_assets_base().'js/frontend/infinitescroll.js"></script>';
			$items_list_html .= "<script>
				$('.news-feed-item, .summary_item_tile').parent().infinite_scroll({
					footer: '#footer',
					feed_item: '.news-feed-item, .summary_item_tile',
					ajax_url: '/frontend/news/ajax_get_news_items',
					custom_params: {category: '".$item_category."'}
				});
			</script>";
		}

        //Return Feed
        return $items_list_html;

    }

    //end of function

    /* END of Front-End : APP Functions  */


    public static function get_feed_for_courses_plugin_frontend ($item_category = 'News', $limit = 5, $offset = 0, $before = null, $after = null, $keywordq = null)
    {

        //Get specific Item
        $select = self::getItemsFrontendSelect()
            ->where('categories.category', '=', $item_category)
            ->order_by('news.date_modified', 'DESC')
            ->and_where_open()
                ->where('news.date_publish', 'IS', NULL)
                ->or_where('news.date_publish', '<', DB::expr ('NOW()'))
            ->and_where_close()
            ->and_where_open()
                ->where ('news.date_remove', 'IS', NULL)
                ->or_where ('news.date_remove', '>', DB::expr ('NOW()'))
            ->and_where_close ();
        if (is_numeric($limit)) {
            $select->limit($limit);
        }
        if ($offset) {
            $select->offset($offset);
        }
        if ($before) {
            $select->and_where('news.date_publish', '<=', $before);
        }
        if ($after) {
            $select->and_where('news.date_publish', '>=', $after);
        }

        if ($keywordq) {
            $keywords = preg_split('/[\ ,]+/i', trim(preg_replace('/[^a-z0-9\ ]/i', '', $keywordq)));
            $match1 = array();
            $match2 = array();
            foreach ($keywords as $i => $keyword) {
                if (strlen($keyword) < 3) { // remove too short things like "at" "'s" "on" ...
                    unset($keywords[$i]);
                } else {
                    if (substr($keyword, -3) == 'ies'){
                        $match2[] = '+' . substr($keyword, 0, -3) . 'y' . '*';
                    } else if (substr($keyword, -3) == 'ses' || substr($keyword, -3) == 'xes'){
                        $match2[] = '+' . substr($keyword, 0, -2) . '*';
                    } else if ($keyword[strlen($keyword) - 1] == 's') {
                        $match2[] = '+' . substr($keyword, 0, -1) . '*'; /*'+' . $keyword . '* */
                    } else {
                        $match2[] = '+' . $keyword . '*';
                    }
                    $match1[] = '+' . $keyword . '*';
                }
            }

            $select->and_where_open();

            if (!empty($keywords)) {
                $match1 = Database::instance()->escape(implode(' ', $match1));
                $match2 = Database::instance()->escape(implode(' ', $match2));
                // Separate terms, enclose in quotes to stop special characters causing problems, "+" before each term
                $select->or_where(DB::expr('match(`news`.`title`)'), 'against', DB::expr("(" . $match1 . " IN BOOLEAN MODE)"));
                $select->or_where(DB::expr('match(`news`.`title`)'), 'against', DB::expr("(" . $match2 . " IN BOOLEAN MODE)"));
            } else {
                $select->or_where('news.title', 'like', '%' . $keywordq . '%');
            }
            $select->and_where_close();
        }

        $result = $select->execute()->as_array();
        return $result;
    }

    //end of function

    /*
  * Please use this function to generate a list of news for the sites html sitemap
  * @return String content
  */

    public static function get_news_html_sitemap ()
    {

        $sitemap = '<ul id="news-sitemap"> <li>News Articles: <ul>';

        $news = self::factory ('News')->get_all_items_front_end ();

        foreach ($news as $new)
        {
            //assumed that view filters all deleted and publish flags correctly
            $sitemap .= '<li><a href="./news/'.$new['category'].'/'.Model::factory ('Pages')->filter_name_tag ($new['title']).'.html">'.$new['title'].'</a></li>';

        }
        //end foreach

        $sitemap .= '</ul></li></ul>';

        return $sitemap;
    }

    //end of function

    public static function get_news_for_calendar_feed ($month, $year)
    {
        $query = DB::query (
            Database::SELECT, "SELECT
        `title`,
        `summary`,
        `date_publish`
        FROM
        `plugin_news`
        WHERE
        `deleted` = 0
        AND
        `publish` = 1
        AND
        DATE_FORMAT(`date_publish`, '%Y-%m') = '".$year."-".$month."'"
        )->execute ()->as_array ();

        return $query;
    }

    public static function get_news_xml_sitemap()
    {
        //<url>
        //<loc>https://www.readerswriters.ie/home.html</loc>
        //</url>

        $sitemap = "";


        $news = self::factory('News')->get_all_items_front_end();

        foreach($news as $new){
            //assumed that view filters all deleted and publish flags correctly
            $url = './news/'.$new['category'].'/'.Model::factory('Pages')->filter_name_tag($new['title']).'.html';
            $sitemap .= '<url><loc>'. URL::site(htmlentities($url)).'</loc></url>';

        }//end foreach

        return $sitemap;
    }

	public static function events_feed()
	{
		return self::get_plugin_items_front_end_feed('Upcoming Events', 'upcoming_events_feed', 'upcoming_events_feed_item_html');
	}

	public static function get_calender_items_json()
	{
		$news_items = DB::select('title','event_date')
			->from('plugin_news')
			->and_where('publish', '=', 1)
			->and_where('deleted', '=', 0)
			->and_where('event_date','IS NOT',NULL)
			->order_by('event_date')
			->execute()
			->as_array();

		$result = '[';
		foreach($news_items AS $news)
		{
			$result.='  { "date": "'.$news['event_date'].'", "type": "Schedule", "title": "'.$news['title'].'", "description": "test description","url": "'.URL::site().'/news/News/'.urlencode(str_replace(" ","-",$news['title'])).'.html" },';
		}
		$result = rtrim($result, ",");
		$result.="]";
		return $result;
	}

    public static function get_localisation_messages()
    {
        $messages = array();
        $news_categories = DB::select('category')->from('plugin_news_categories')->execute()->as_array();
        foreach($news_categories as $news_category){
            $messages[] = $news_category['category'];
        }
        $news = DB::select('*')->from('plugin_news')->execute()->as_array();
        foreach($news as $new){
            $messages[] = $new['title'];
            $messages[] = $new['summary'];
            $messages[] = $new['content'];
        }
        return $messages;
    }

    public static function get_shared_with($news_id)
    {
        $shared_with = DB::select('*')
            ->from(Model_News_Shared::TABLE)
            ->where('news_id', '=', $news_id)
            ->and_where('deleted', '=', 0)
            ->execute()
            ->as_array();
        $result = array();
        foreach ($shared_with as $item) {
            $result[] = $item['role_id'];
        }
        return $result;
    }

    public static function get_for_eventcalendar()
    {
        $results = [];
        $news_items = DB::select('title','event_date')
            ->from('plugin_news')
            ->and_where('publish', '=', 1)
            ->and_where('deleted', '=', 0)
            ->and_where('event_date','IS NOT',NULL)
            ->order_by('event_date')
            ->execute()
            ->as_array();

        foreach ($news_items as $news_item => $news) {
            $results[] = [
                'date' => $news['event_date'],
                'type' => 'Schedule',
                'title' => $news['title'],
                'description' => '',
                'url' => URL::site().'/news/News/'.urlencode(str_replace(' ', '-', $news['title'])).'.html'
            ];
        }

        return $results;
    }

}//end of class
