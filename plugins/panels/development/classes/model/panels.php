<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Kosta
 * Date: 23/10/2012
 * Time: 10:24
 * To change this template use File | Settings | File Templates.
 */

class Model_Panels extends Model{

	/*
	 * @TODO: Add Documentation-Comments for each of the Public/Helper Functions
	 */

    private static $scroller_sequence_animation_modes = array (
        'fade' 			=> 'Fade',
        'horizontal' 	=> 'Horizontal Slide',
        'vertical' 		=> 'Vertical Slide'
    );

    private static $scroller_sequence_order_types = array (
        'ascending' 	=> 'Ascending',
        'descending' 	=> 'Descending',
        'random' 		=> 'Random'
    );

    private static $scroller_item_link_types = array (
        'none' 		=> 'None',
        'internal'	=> 'Page',
        'external'	=> 'External'
    );
    // End of unnecessary stuff.

    private static $model_items_table = 'plugin_panels';
    private static $model_items_list_admin = 'ppanels_view_panels_list_admin';
    private static $plugin_template_positions = array(
        'default'         => 'Default',
        'home_content'    => 'Home content',
        'home_left'       => 'Home left',
        'home_right'      => 'Home right',
        'content_content' => 'Content centre',
        'content_left'    => 'Content left',
        'content_right'   => 'Content right',
        'footer'          => 'Footer',
        'footer_bottom'   => 'Footer bottom'
    );

    public static function getItemsAdminSelect()
    {
        $select = DB::select(
            'panels.*',
            array('type.name', 'type'),
            array('type.friendly_name', 'type_friendly_name'),
            array('users_create.name', 'created_by_name'),
            array('roles_create.role', 'created_by_role'),
            array('users_modify.name', 'modified_by_name'),
            array('roles_modify.role', 'modified_by_role')
        )
            ->from(array('plugin_panels', 'panels'))
                ->join(array('plugin_panels_types', 'type'), 'left')
                    ->on('panels.type_id', '=', 'type.id')
                ->join(array('plugin_panels_predefined', 'predefined'), 'left')
                    ->on('panels.predefined_id', '=', 'predefined.id')
                ->join(array('engine_users', 'users_create'), 'left')->on('panels.created_by', '=', 'users_create.id')
                    ->join(array('engine_users', 'users_modify'), 'left')->on('panels.modified_by', '=', 'users_modify.id')
                ->join(array('engine_project_role', 'roles_create'), 'left')
                    ->on('users_create.role_id', '=', 'roles_create.id')
                ->join(array('engine_project_role', 'roles_modify'), 'left')
                    ->on('users_modify.role_id', '=', 'roles_modify.id')
            ->where('panels.deleted', '=', 0);

        return $select;
    }

    public static function getItemsFrontendSelect()
    {
        $select = DB::select('panels.*')
            ->from(array(self::$model_items_table, 'panels'))
            ->where('panels.deleted', '=', 0)
            ->and_where('panels.publish', '=', 1);
        return $select;
    }

	// Retrieve all Panels or the One - Specified by the $item_id, and are available to this admin user
	public function get_all_items_admin($item_id = null) {

		if (is_null($item_id))
		{
			$item_data = self::getItemsAdminSelect()
					->execute()
					->as_array();
		}
		else
		{
			$item_data = self::getItemsAdminSelect()
                ->where('panels.id', '=', $item_id)
                ->execute()
                ->as_array();
		}

        for ($i = 0; $i < count($item_data); $i++) {
            if (is_numeric(@$item_data[$i]['image'])) {
                $item_data[$i]['sequence_id'] = $item_data[$i]['image'];
            }
        }
        return $item_data;

	}//end of function


	//Validate input Panel - information
	public function validate($item_data_to_validate) {
//		echo "\nValidate Panel: \n";
//		IbHelpers::die_r($item_data_to_validate);

		$item_valid = true;

		//check input data
		if(empty($item_data_to_validate['panel_title'])){
			IbHelpers::set_message('Please add "Panel Title".', 'error popup_box');
			$item_valid = false;
		}
		if(empty($item_data_to_validate['panel_position'])){
			IbHelpers::set_message('Please select "Panel Position".', 'error popup_box');
			$item_valid = false;
		}
//		if($item_data_to_validate['panel_link'] == 0 AND $item_data_to_validate['panel_link_url'] == ''){
//			IbHelpers::set_message('Please add "Panel External Link URL".', 'error');
//			$item_valid = false;
//		}
		if($item_data_to_validate['panel_link_url'] != ''){
			//Validate the External Link URL if this is provided
			if(!preg_match("#^http(s)?://[a-z0-9-_.]+\.[a-z]{2,4}#i", $item_data_to_validate['panel_link_url'])){
				IbHelpers::set_message(
					'The URL: "'.$item_data_to_validate['panel_link_url'].'" that was specified is NOT Valid.<br />'.
					'A VALID URL is in the form: "http://www.domain_name.com" or "https://www.domain_name.com"', 'error popup_box');
				$item_valid = false;
			}
		}
		//return
		return $item_valid;
	}//end of function


    public function get_new_item_id()
    {
        $id = DB::select(DB::expr('MAX(id) AS id'))->from(self::$model_items_table)->execute()->as_array();
        $id = (int)$id[0]['id']+1;
        return $id;
    }
	// Add a Panel to the database
	public function add($item_input_data)
    {
		// 1. get the ID of the currently-logged-in user, i.e. the user who is making the update
		$logged_in_user = Auth::instance()->get_user();

		/* 2. Create the NEW panel */
		//Set the panel data to be added to database
        $item_to_add_data['id']            = @$item_input_data['id'];
		$item_to_add_data['title']         = @$item_input_data['panel_title'];
		$item_to_add_data['position']      = @$item_input_data['panel_position'];
        $item_to_add_data['order_no']      = @$item_input_data['panel_order_no'];
        $item_to_add_data['type_id']       = @$item_input_data['type_id'];
        $item_to_add_data['predefined_id'] = @$item_input_data['predefined_id'];
        $item_to_add_data['image']         = @$item_input_data['panel_image'];
        $item_to_add_data['text']          = @$item_input_data['panel_text'];
        $item_to_add_data['view']          = @$item_input_data['view'];
		$item_to_add_data['link_id']       = @$item_input_data['panel_link'];
		$item_to_add_data['link_url']      = @$item_input_data['panel_link_url'];
		$item_to_add_data['date_publish']  = ( $item_input_data['panel_date_publish'] != '0000-00-00 00:00:00' AND !empty($item_input_data['panel_date_publish']))?
            date('Y-m-d H:i:s', strtotime($item_input_data['panel_date_publish'])) :
            NULL;
		$item_to_add_data['date_remove'] = ( $item_input_data['panel_date_remove'] != '0000-00-00 00:00:00' AND !empty($item_input_data['panel_date_remove']))?
            date('Y-m-d H:i:s', strtotime($item_input_data['panel_date_remove'])) :
            NULL;
		$item_to_add_data['publish'] = $item_input_data['panel_publish'];
		// Format the required dates for mysql storage
		$item_to_add_data['date_created'] = date('Y-m-d H:i:s');
		$item_to_add_data['created_by'] = $logged_in_user['id'];
		$item_to_add_data['date_modified'] = $item_to_add_data['date_created'];
		$item_to_add_data['modified_by'] = $logged_in_user['id'];
		$item_to_add_data['deleted'] = 0;
		//add the Panel to DB
		$insert_result = DB::insert(self::$model_items_table)->values($item_to_add_data)->execute();
		// return new ID
		return $insert_result[0];
	}//end of function


	//Update a Panel record
	public function update($item_update_data) {
//		echo "\nUpdate Panel: \n";
//		IbHelpers::die_r($item_update_data);

		// 1. get the ID of the currently-logged in user, i.e. the user who is making the update
		$logged_in_user = Auth::instance()->get_user();

		/* 2. Create the NEW Panel */
		//Set the Panel data to be added to Database
		$item_to_update_data['id']            = @$item_update_data['panel_id'];
		$item_to_update_data['title']         = @$item_update_data['panel_title'];
		$item_to_update_data['position']      = @$item_update_data['panel_position'];
        $item_to_update_data['order_no']      = @$item_update_data['panel_order_no'];
        $item_to_update_data['type_id']       = @$item_update_data['type_id'];
        $item_to_update_data['predefined_id'] = @$item_update_data['predefined_id'];

        if (isset($item_update_data['sequence_data']['id'])) {
            $item_to_update_data['image']         = $item_update_data['sequence_data']['id'];
        }
        else {
		    $item_to_update_data['image']         = $item_update_data['panel_image'];
        }

        $item_to_update_data['text']          = @$item_update_data['panel_text'];
        $item_to_update_data['view']          = @$item_update_data['view'];
		$item_to_update_data['link_id']       = ($item_update_data['panel_link'] != '')? $item_update_data['panel_link'] : NULL;
		/*
		 * Set the link_id if it is not set (is null) and the $item_update_data['panel_link_url'] is set, i.e. not an EMPTY_STRING
		 * If th eLink ID was set, i.e. was 0 or greater than zero, i.e. some inner page was set as link to this Item => use the preset Inner Page a sLink Id
		 */
		if($item_update_data['panel_link_url'] != '' AND is_null($item_to_update_data['link_id'])) $item_to_update_data['link_id'] = 0;
		$item_to_update_data['link_url'] = $item_update_data['panel_link_url'];
        $item_to_update_data['date_publish'] = ( $item_update_data['panel_date_publish'] != '0000-00-00 00:00:00' AND !empty($item_update_data['panel_date_publish']))?
            date('Y-m-d H:i:s', strtotime($item_update_data['panel_date_publish'])) :NULL;
        $item_to_update_data['date_remove'] = ( $item_update_data['panel_date_remove'] != '0000-00-00 00:00:00' AND !empty($item_update_data['panel_date_remove']))?
            date('Y-m-d H:i:s', strtotime($item_update_data['panel_date_remove'])) : NULL;
		$item_to_update_data['publish'] = $item_update_data['panel_publish'];
//		$item_to_update_data['order_no'] = $item_update_data['panel_order_no'];
		// Format the required dates for mysql storage
//		$item_to_update_data['date_created'] = date('Y-m-d H:i:s');
//		$item_to_update_data['created_by'] = $logged_in_user['id'];
		$item_to_update_data['date_modified'] = date('Y-m-d H:i:s');
		$item_to_update_data['modified_by'] = $logged_in_user['id'];
		$item_to_update_data['deleted'] = 0;
		//Update the Panel to DB
		$update_result = DB::update(self::$model_items_table)
								->set($item_to_update_data)
								->where('id', '=', $item_to_update_data['id'])
								->execute();

		// Update the sequence data
        if (isset($item_update_data['sequence_data']))
        {
            $cs_model = new Model_Customscroller();
            if ($item_update_data['sequence_data']['id'] == 'new')
            {
                $item_update_data['sequence_data']['plugin'] = 'panels';
                $result = $cs_model->add_custom_sequence($item_update_data['sequence_data']);

                DB::update(self::$model_items_table)
                    ->set(array('image' => $result))
                    ->where('id', '=', $item_to_update_data['id'])
                    ->execute();
            }
            else if (trim($item_update_data['sequence_data']['id']) != '' AND (int)$item_update_data['sequence_data']['id'] > 0)
            {
                $cs_model->update_custom_sequence($item_update_data['sequence_data']);
            }
        }

		// return new ID
		return $update_result;
	}//end of function


	//Sets a specified Panel Record in the DB as Deleted
	public function delete($item_id) {
//		echo "\nDelete Panel: \n";
//		IbHelpers::die_r($item_id);

		// 1. get the ID of the currently-logged in user, i.e. the user who is making the update
		$logged_in_user = Auth::instance()->get_user();

		// 2. Mark the specified Panel as deleted - in this case result will be INT - holding the number of affected rows
		$delete_result = DB::update(self::$model_items_table)
				->set(
						array(
							'deleted' => 1,
							'date_modified' => date('Y-m-d H:i:s'),
							'modified_by' => $logged_in_user['id']
						)
				)
				->where('id', '=', $item_id)
				->execute();

		// 3. return
		return $delete_result;
	}//end of function


    // Might be unnecessary.
    // Get the panel types as options for a select list
    public static function get_panel_types_as_options ($selected = '')
    {
        $panel_type_options = '';
        $panel_types = DB::select()->from('plugin_panels_types')->where('deleted', '=', '0')->and_where('publish', '=', '1')->execute();

        foreach ($panel_types as $panel_type)
        {
            $panel_type_options .= '<option value="'.$panel_type['id'].'"'.(($selected == $panel_type['id']) ? ' selected="selected"' : '').' data-name="'.$panel_type['name'].'">'.$panel_type['friendly_name'].'</option>';
        }

        return $panel_type_options;
    }

    public static function get_predefined_panels_as_options($selected = '')
    {
        $predefined_options = '<option>-- Select Option --</option>';
        $predefined_panels = DB::select()->from('plugin_panels_predefined')->where('deleted', '=', '0')->and_where('publish', '=', '1')->execute();

        foreach ($predefined_panels as $predefined_panel)
        {
            $predefined_options .= '<option value="'.$predefined_panel['id'].'"'.(($selected == $predefined_panel['id']) ? ' selected="selected"' : '').'>'.$predefined_panel['friendly_name'].'</option>';
        }

        return $predefined_options;
    }

    // Unnecessary. Remove later
    /*
    public function get_custom_sequence_editor_view ($sequence_holder_plugin, $plugin_item_id = NULL, $sequence_id = NULL)
    {
        return View::factory (
            'admin/add_edit_custom_sequence',
            array (
                'sequence_holder_plugin'  => $sequence_holder_plugin,
                'sequence_holder_id'  	  => $plugin_item_id,
                'animation_types' 	 	  => self::$scroller_sequence_animation_modes,
                'order_types'        	  => self::$scroller_sequence_order_types,
                'link_types'         	  => self::$scroller_item_link_types,
            )
        )->render ();
    }*/


    /**
	 * Function used to Return all Panel Positions based on the specified <em>$return_type</em>.
	 *
	 * @param string $return_type String holding the return type of the data to be returned.<br />
	 * 							  <em>Possible Values:</em><br />
	 * 							  - <strong>details</strong> - will return the Panels-Positions as an associative array in the form:<br />
	 * 								array('position_key' => 'Position')<br />
	 * 							  - <strong>options</strong> - will return all Panel-Positions listed as Select drop-down options. in the form:<br />
	 * 							  &lt;option value="<em>POSITION_KEY</em>"&gt;<em>POSITION</em>&lt;/option&gt;<br />
	 * 							  <em>DEFAULT VALUE</em>: <strong>options</strong>
	 *
	 * @param NULL $selected_position String holding the Option to be set to: <em>selected="selected"</em> by default.<br />
	 * 								  <em>DEFAULT VALUE</em>: <strong>NULL</strong>
	 * @return array|string Array or String, based on the specified by the <em>$return_type</em> value.
	 */
	public static function get_template_positions_as($return_type='options', $selected_position=NULL)
	{
		$return_plugin_positions = NULL;

		// Horrible hardcode. Panel locations should ideally be moved to the database
		$content2 = ORM::factory('Engine_Layout')->where('layout', '=', 'content2')->find_published();
		if ($content2->id)
		{
			self::$plugin_template_positions['content2_right'] = 'Content2 Right';
		}

		switch($return_type){
			case 'details':
				$return_plugin_positions = self::$plugin_template_positions;
				break;

			case 'options':
			default:
				$return_plugin_positions = '';
				foreach(self::$plugin_template_positions as $key=>$position)
					$return_plugin_positions .= '<option value="'.$key.'"'.
							((!empty($selected_position) AND $selected_position == $key)? ' selected="selected"' : '').'>'.
							$position.'</option>';
				break;

		}//end of generating result with Panels-Positions

		return $return_plugin_positions;
	}//end of function


	public static function toggle_panel_publish($item_id, $publish_flag){
//		echo "\nToggle Publish Panel: \n";
//		IbHelpers::pre_r(array('panel_id' => $item_id, 'publish_flag' => $publish_flag));

		// 1. get the ID of the currently-logged in user, i.e. the user who is making the update
		$logged_in_user = Auth::instance()->get_user();

		// 2. Toggle the Publish flag of the specified Panel
		$toggle_publish_result = DB::update(self::$model_items_table)
				->set(
						array(
							'publish' => $publish_flag,
							'date_modified' => date('Y-m-d H:i:s'),
							'modified_by' => $logged_in_user['id']
						)
				)
				->where('id', '=', $item_id)
				->execute();

		// 3. return
		return $toggle_publish_result;
	}//end of function


    /** FRONT END FUNCTIONS */

    public static function get_panels_feed($position){
        $panel_model = new Model_Panels();
        $localisation_content_active = Settings::instance()->get('localisation_content_active') == '1';
        $panels = $panel_model->get_panels($position, $localisation_content_active);
        $html = $panel_model->generate_panels_html($panels) . '<!-- debug:' . $position . ' -->';
        return $html;
    }

    public static function get_panels_feed_view($position)
    {
        $data['position'] = $position;
        return View::factory('front_end/panels_feed_view', $data);
    }

    public static function get_panels_feed_li($position){
        $panel_model = new Model_Panels();
        $localisation_content_active = Settings::instance()->get('localisation_content_active') == '1';
        $panels = $panel_model->get_panels($position, $localisation_content_active);
        $html = $panel_model->generate_panels_html_li($panels);
        return $html;
    }

	public static function get_panel_by_name($panel_name){
        $panel_model = new Model_Panels();
        $localisation_content_active = Settings::instance()->get('localisation_content_active') == '1';
        $panels = $panel_model->get_panel_by_title($panel_name, $localisation_content_active);
        $html = $panel_model->generate_panels_html($panels);
        return $html;
 	}

    public static function get_current_panel_feed($panel_id){
        $panel_model = new Model_Panels();
        $localisation_content_active = Settings::instance()->get('localisation_content_active') == '1';
        $panels = $panel_model->get_panel($panel_id, $localisation_content_active);
        $html = $panel_model->generate_panels_html($panels);
        return $html;
    }

	public static function get_panel_by_title($title, $translate = false){
		$panels = self::getItemsFrontendSelect()
		    ->where('panels.title', '=', $title)
		    ->and_where('panels.publish', '=', '1')
    	    ->and_where('panels.deleted', '=', '0')
		    ->execute()
		    ->as_array();

        if ($translate) {
            foreach ($panels as $i => $panel) {
                $panels[$i]['text'] = Model_Localisation::get_ctag_translation($panel['text'], I18n::$lang);
            }
        }

        return $panels;
	}
    public function get_panels($position, $translate = false){
        //@todo query order by
        if (is_null($position))
        {
            $panels = self::getItemsFrontendSelect()
                ->execute()
                ->as_array();
        }
        else
        {
            $query = DB::select(
                array('panel.id', 'id'),
                array('panel.page_id', 'page_id'),
                array(DB::expr("SUBSTRING_INDEX(`panel`.`title`, '--', 1)"), 'title'),
                array('panel.position', 'position'),
                array('panel.order_no', 'order_no'),
                array('panel.type_id', 'type_id'),
                array('type.name', 'type'),
                array('type.friendly_name', 'type_friendly_name'),
                array('panel.predefined_id', 'predefined_id'),
                array('predefined.name', 'predefined'),
                array('predefined.friendly_name', 'predefined_friendly_name'),
                array('predefined.content', 'predefined_content'),
                array('panel.image', 'image'),
                array('panel.text', 'text'),
                array('panel.view', 'view'),
                array('panel.link_id', 'link_id'),
                array('panel.link_url', 'link_url'),
                array('panel.publish', 'publish'),
                array('panel.deleted', 'deleted')
            )
            ->from(array('plugin_panels', 'panel'))
            ->join(array('plugin_panels_types', 'type'), 'left')
            ->on('panel.type_id', '=', 'type.id')
            ->join(array('plugin_panels_predefined', 'predefined'), 'left')
            ->on('panel.predefined_id', '=', 'predefined.id')
            ->where('panel.position', '=', $position)
            ->and_where('panel.publish', '=', '1')
            ->and_where('panel.deleted', '=', '0')
            ->order_by('panel.order_no', 'ASC')
            ->and_where_open()
                ->where('panel.date_publish', 'IS', NULL)
                ->or_where('panel.date_publish', '<', DB::expr('NOW()'))
            ->and_where_close()
            ->and_where_open()
                ->where('panel.date_remove', 'IS', NULL)
                ->or_where('panel.date_remove', '>', DB::expr('NOW()'))
            ->and_where_close();


            $microsite_suffix = isset(Kohana::$config->load('config')->project_suffix) ? Kohana::$config->load('config')->project_suffix : '';

            // If microsites are being used, only show panels that are prefixed with the microsite ID...
            // ... or panels with no prefix
            if (!empty($microsite_suffix)) {
                $query
                    ->and_where_open()
                        ->where('panel.title', 'like', '%--'.$microsite_suffix)
                        ->or_where('panel.title', 'not like', '%--%')
                    ->and_where_close();
            }

            $panels = $query->execute()->as_array();
        }

		$pages_model = new Model_Pages;

		foreach ($panels as $i => $panel)
		{
			if (empty($panel['link_id']))
			{
				$url = $panel['link_url'];
				if ( ! empty($url)){
					if( strpos($url, 'http://') === false AND strpos($url, 'https://') === false  AND strpos($url, '/') !== 0){
						$url = 'http://'. $url;
					}
				}
			}
			else
			{
				$page = $pages_model->get_page_data( $panel['link_id'] );
				$url = (isset($page[0]['name_tag'])) ? URL::site() . $page[0]['name_tag'] : $panel['link_url'];
			}

			$panels[$i]['link_url'] = $url;

			if ($translate) {
				$panels[$i]['text'] = Model_Localisation::get_ctag_translation($panel['text'], I18n::$lang);
			}

            if (strpos($panels[$i]['text'], '<span>[CAPTCHA]</span>') !== false) {
                if (Settings::instance()->get('captcha_enabled') == 1) {
                    require_once ENGINEPATH . '/plugins/formprocessor/development/classes/model/recaptchalib.php';
                    $captcha_public_key = Settings::instance()->get('captcha_public_key');
                    $catpcha_html = recaptcha_get_html($captcha_public_key, null, 2);
                    $panels[$i]['text'] = str_replace('<span>[CAPTCHA]</span>', $catpcha_html, $panels[$i]['text']);
                } else {
                    $panels[$i]['text'] = str_replace('<span>[CAPTCHA]</span>', '', $panels[$i]['text']);
                }
            }
		}

        return $panels;
    }

    public function get_panel($panel_id, $translate = false){
        //@todo query order by
        if (is_null($panel_id))
        {
            $panels = self::getItemsFrontendSelect()
                ->execute()
                ->as_array();
        }
        else
        {
            $panels = self::getItemsFrontendSelect()
                ->where('id', '=', $panel_id)
                ->execute()
                ->as_array();
        }

        if ($translate) {
            foreach ($panels as $i => $panel) {
                $panels[$i]['text'] = Model_Localisation::get_ctag_translation($panel['text'], I18n::$lang);
            }
        }

        return $panels;
    }

    public function generate_panels_html($panels)
	{
        $pages_model = new Model_Pages();
        $amount = count($panels);
        $html = '';
        foreach ($panels as $key => $panel)
		{
            $panels[$key]['text'] = $panel['text'] = IbHelpers::expand_short_tags($panel['text']);
            if ($panel['link_id'] == '0' OR  empty($panel['link_id']))
			{
                $url = $panel['link_url'];
                if(!empty($url)){
                    if( strpos($url, 'http://') === false AND strpos($url, 'https://') === false  AND strpos($url, '/') !== 0){
                        $url = 'http://'. $url;
                    }
                }
            }
            else{
                $page = $pages_model->get_page_data( $panel['link_id'] );
                $url = (isset($page[0]['name_tag'])) ? URL::site() . $page[0]['name_tag'] : $panel['link_url'];
            }

            //The first one
            if($key == 0){
                $html .= '<div class="panels" >';
                $html .= '<ul >';
                $first = 'first_panel';
            }
            else{
                $first = '';
            }

            //Last
            if($key == ($amount - 1)){
                $last = 'last_panel';
            }
            else{
                $last = '';
            }

            //Li elements
            $data['key'] = $key;
            $data['url'] = $url;
            $data['first'] = $first;
            $data['last'] = $last;
            $data['panel'] = $panel;

            if (isset($panel['type']) AND $panel['type'] == 'predefined')
            {
                $html .= '<li><div class="'.$panel['predefined'].'_panel">';
                if (substr($panel['predefined_content'], 0, 6) == 'Model_')
                {
                    $function = explode(',', $panel['predefined_content']);
                    $html .= call_user_func($function[0].'::'.$function[1]);
                }
                else
                {
                    $html .= $panel['predefined_content'];
                }
                $html .= '</div></li>';
            }
            elseif (isset($panel['type']) AND $panel['type'] == 'view')
            {
                $view = substr($panel['view'], 0, -4);
                $html .= '<li>'.View::factory($view).'</li>';
            }
            elseif (is_numeric($panel['image']) AND $panel['image'] != 0)
            {
                $cs_model = new Model_Customscroller();
                $html .= '<li><section class="banner">'.$cs_model->render_front_end_custom_sequence('panels', 1, $panel['image']).'</section></li>';
            }
            else
            {
                $html .= View::factory('front_end/panel_snippet', $data);
            }
			$html .= "\n";

            //Last
            if($key == ($amount - 1)){
                $html .= '</ul>';
                $html .= '</div>';
            }
        }

        if (strpos($html, '<span>[CAPTCHA]</span>') !== false) {
            if (Settings::instance()->get('captcha_enabled') == 1) {
                require_once ENGINEPATH . '/plugins/formprocessor/development/classes/model/recaptchalib.php';
                $captcha_public_key = Settings::instance()->get('captcha_public_key');
                $catpcha_html = recaptcha_get_html($captcha_public_key, null, 2);
                $html = str_replace('<span>[CAPTCHA]</span>', $catpcha_html, $html);
            } else {
                $html = str_replace('<span>[CAPTCHA]</span>', '', $html);
            }
        }
        return $html;
    }

    public function generate_panels_html_li($panels){
        $pages_model = new Model_Pages();
        $amount = count($panels);
        $html = '';
        foreach ($panels as $key => $panel) {

            if ( $panel['link_id'] == '0' ){
                $url = $panel['link_url'];
                if(!empty($url)){
                    if( strpos($url, 'http://') === false AND strpos($url, 'https://') === false){
                        $url = 'http://'. $url;
                    }
                }
            }
            else{
                $page = $pages_model->get_page_data( $panel['link_id'] );
                $url = $page[0]['name_tag'];
            }

            //The first one
            if($key == 0){
                $first = 'first_panel';
            }
            else{
                $first = '';
            }

            //Last
            if($key == ($amount - 1)){
                $last = 'last_panel';
            }
            else{
                $last = '';
            }

            //Li elements
            $data['key'] = $key;
            $data['url'] = $url;
            $data['first'] = $first;
            $data['last'] = $last;
            $data['panel'] = $panel;
            $html .= View::factory('front_end/panel_snippet', $data);
        }
        return $html;
    }


    public static function get_css_styles(){
        $html = '';
        if ($handle = opendir(DOCROOT . 'assets' . DIRECTORY_SEPARATOR .'default' . DIRECTORY_SEPARATOR . 'css')) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != "..") {
                    $html .= '<link href="'. url::site() . 'assets' . DIRECTORY_SEPARATOR .'default' . DIRECTORY_SEPARATOR . 'css' . DIRECTORY_SEPARATOR . $entry .'" rel="stylesheet" type="text/css" media="all" />';
                }
            }
            closedir($handle);
        }
        return $html;
    }

    public static function toggle_item_publish($item_to_edit_id, $item_publish_flag)
    {
        //return 1 for published, 0 for unpublished.
        return (DB::update('plugin_panels')->set(array('publish'=>$item_publish_flag))->where('id','=',$item_to_edit_id)->execute()) ? 1 : 0;
    }

    public static function render($identifier, $view = 'standard_panel')
    {
        if (is_numeric($identifier)) {
            $panels = self::get_panel($identifier);
        }
        else {
            $panels = self::get_panel_by_title($identifier, true);
        }

        if (isset($panels[0]))
        {
            $panel = $panels[0];
            if (!empty($panel['link_id'])) {
                $pages_model = new Model_Pages;
                $page = $pages_model->get_page_data( $panel['link_id'] );
                $panel['link_url'] = '/'.$page[0]['name_tag'];
            }
            else if (!empty($panel['link_url'])) {
                $url = $panel['link_url'];
                if (strpos($url, 'http://') === false AND strpos($url, 'https://') === false) {
                    $panel['link_url'] = 'http://'. $url;
                }
            }

            $project_folder = Kohana::$config->load('config')->project_media_folder;
            $media          = new Model_Media();
            $media_path     = Model_Media::get_path_to_media_item_admin($project_folder, '', '');
            $overlay_exists = $media->is_filename_used('panel-overlay.png', 'content');

            echo View::factory('front_end/'.$view)
                ->set('media_path',     $media_path)
                ->set('overlay_exists', $overlay_exists)
                ->set('panel',          $panel);
        }
        else
        {
            echo '';
            Log::instance()->add(Log::ERROR, 'Error rendering panel.'."\n".'Panel "'.$identifier.'" could not be found.');
        }
    }

}//end of class
