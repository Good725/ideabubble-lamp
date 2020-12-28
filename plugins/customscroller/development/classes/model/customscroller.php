<?php
/**
 * <pre>
 * Created by JetBrains PhpStorm.
 * User: Kosta
 * Date: 04/10/2013
 * Time: 14:29
 * To change this template use File | Settings | File Templates.
 *
 * <h1>Main Custom Scroller Plugin Model.</h1>
 *
 * <p>Provides CRUD functions for the Back-End (Admin) Custom Scroller Management and Custom Scroller Listing Functions for the Front-End Custom Scroller display.</p>
 *
 * <p>User Guide Wiki Page: <a href="#">Custom Scroller - User's Guide</a> - @TODO: Add User's Guide WIKI Page</p>
 *
 * <p>Developer's Guide Wiki Page: <a href="http://wiki.ideabubble.ie/confluence/display/WPP/Custom+Scroller+or+Scroller">Custom Scroller - Developer's Guide</a></p>
 * </pre>
 */

class Model_Customscroller extends Model
{

    /*
     * @TODO: Add Documentation-Comments for each of the Public/Helper Functions
     */

    private static $plugins_table = 'engine_plugins';
    private static $model_sequences_table = 'plugin_custom_scroller_sequences';
    private static $model_sequence_items_table = 'plugin_custom_scroller_sequence_items';
    private static $model_plugins_sequences = 'plugin_custom_scroller_plugins_sequences';

    private static $scroller_sequence_animation_modes = array(
        'fade' => 'Fade',
        'horizontal' => 'Horizontal Slide',
        'vertical' => 'Vertical Slide'
    );

    private static $scroller_sequence_order_types = array(
        'ascending' => 'Ascending',
        'descending' => 'Descending',
        'random' => 'Random'
    );

    private static $scroller_item_link_types = array(
        'none' => 'None',
        'internal' => 'Page',
        'external' => 'External'
    );


    /* Back-End (CMS) : Admin Functions  */

    // Retrieve Custom Sequence Related Data for Specified (All) Sequence(s)
    public function get_custom_sequence_admin($sequence_id = NULL, $plugin = 'customscroller')
    {
        $sequence_data = array();

        if (!is_null($sequence_id)) {
            $sequence_data = DB::select()->from(self::$model_sequences_table)->where('id', '=', $sequence_id)->and_where('deleted', '=', '0')->execute()->as_array();
        } else {
            if ($plugin == 'banners') {
                $plugin = 'customscroller';
            }

            $plugin_id = DB::select()->from(self::$plugins_table)->where('name', '=', $plugin)->execute()->get('id', 0);
            $sequence_data = DB::select()->from(self::$model_sequences_table)->where('deleted', '=', '0')->and_where('plugin', '=', $plugin_id)->execute()->as_array();
        }

        // Return
        return (!is_null($sequence_id)) ? $sequence_data[0] : $sequence_data;
    }

    // Retrieve Custom Sequence Related Items for Specified Sequence
    public function get_custom_sequence_items_admin($sequence_id = NULL)
    {
        $sequence_items_data = array();

        // Get the Sequence - General Data
        if (is_null($sequence_id)) {
            $sequence_items_data = DB::select()->from(self::$model_sequence_items_table)->where('deleted', '=', '0')->execute()->as_array();
        } else {
            $sequence_items_data = DB::select()->from(self::$model_sequence_items_table)->where('sequence_id', '=', $sequence_id)->and_where('deleted', '=', '0')->execute()->as_array();
        }

        // Return
        return $sequence_items_data;
    }

    public function get_available_images_admin($sequence_id = NULL, $plugin = 'banners')
    {
        $plugin = ($plugin == 'customscroller' ? 'banners' : $plugin);

        $sequence_items = Model_Customscroller::get_custom_sequence_items_admin($sequence_id);

        $available_images = Model_Media::factory('Media')->get_all_items_based_on(
            'location', $plugin, 'details', '=', NULL
        );

        // Clear the ALREADY added images as items so we DON'T add duplicate images for the same sequence
        if (count($available_images) > 0) {
            // Loop through available images and remove the ones, which were already added to this sequence
            foreach ($available_images as $available_key => $available_image) {
                // Check if current available image is already added to this sequence and remove it from the list with available images
                if (count($sequence_items) > 0) {
                    foreach ($sequence_items as $sequence_item) {
                        if ($available_image['filename'] == $sequence_item['image'])
                            unset($available_images[$available_key]);
                    }
                }
            }
        }
        return $available_images;
    }


    public function get_custom_sequence_item_details($item_id)
    {
        $sequence_item_data = array();

        // Get the Details of the specified Sequence Item
        $sequence_item_data = DB::select()->from(self::$model_sequence_items_table)->where('id', '=', $item_id)->and_where('deleted', '=', '0')->execute()->as_array();

        // Return
        return $sequence_item_data;
    }

    // Retrieve Custom Sequence Details for the Front End
    public function get_custom_sequence_data_front_end($sequence_to_render_id)
    {
        $sequence_data = DB::select()->from(self::$model_sequences_table)
            ->where('id', '=', $sequence_to_render_id)
            ->and_where('publish', '=', '1')
            ->and_where('deleted', '=', '0')
            ->execute()
            ->as_array();
        //crashing kilmartin.
        return (count($sequence_data) > 0) ? $sequence_data[0] : 0;
    }

    // Retrieve Custom Sequence Details for the Front End
    public function get_custom_sequence_items_data_front_end($sequence_id, $order_type = 'ascending')
    {
        $order_by = 'item.order_no';
        switch ($order_type) {
            case 'ascending' :
                $order_type = 'ASC';
                break;
            case 'descending' :
                $order_type = 'DESC';
                break;
            case 'random' :
                $order_by = DB::expr('RAND()');
                $order_type = 'ASC';
                break;
            default:
                $order_type = 'ASC';
                break;
        }
        $sequence_items_data = DB::select(
            'item.*',
            array(DB::expr("IF(`item`.`link_url` REGEXP '[0-9]+', CONCAT('/', `page`.`name_tag`), `link_url`)"), 'link')
        )
            ->from(array(self::$model_sequence_items_table, 'item'))
            ->join(array(Model_Pages::PAGES_TABLE, 'page'), 'left')
                ->on('item.link_url', '=', 'page.id')
            ->where('item.sequence_id', '=', $sequence_id)
            ->and_where('item.publish', '=', '1')
            ->and_where('item.deleted', '=', '0')
            ->order_by($order_by, $order_type)
            ->execute()
            ->as_array();

        $image_path = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder, '', 'banners');
        $mobile_image_path = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder, '', 'mobile_banners');

        $max_height = null;
        foreach ($sequence_items_data as &$item_data) {
            if ($item_data['image']) {
                $item_data['src'] = (strpos($item_data['image'], 'http:') === 0 || strpos($item_data['image'], 'https:') === 0) ? $item_data['image'] : $image_path.$item_data['image'];
            } else {
                $item_data['src'] = null;
            }

            $image_data = Model_Media::get_by_filename($item_data['image'], 'banners');
            $item_data['width']  = $image_data['width'];
            $item_data['height'] = $image_data['height'];

            if ($item_data['mobile_image']) {
                $item_data['mobile_src'] = (strpos($item_data['mobile_image'], 'http:') === 0 || strpos($item_data['mobile_image'], 'https:') === 0) ? $item_data['mobile_image'] : $mobile_image_path.$item_data['mobile_image'];
            } else {
                $item_data['mobile_src'] = null;
            }

            if (!empty($item_data['link'])) {
                $item_data['href'] = (strpos($item_data['link'], 'http:') === 0 || strpos($item_data['link'], 'https:') === 0) ? $item_data['link'] : '/'.ltrim($item_data['link'], '/');
            } else {
                $item_data['href'] = null;
            }

            $item_data['html_parsed'] = !empty($item_data['html']) ? IbHelpers::expand_short_tags(Model_Localisation::get_ctag_translation($item_data['html'], I18n::$lang)) : '';

            if ($item_data['html_parsed']) {
                // Check if the entire content is wrapped in an .sr-only (screen-reader-only; content which is readable by machines, but not visible to humans)
                $dom = new DomDocument();
                libxml_use_internal_errors(true);
                $dom->strictErrorChecking = false;
                $dom->loadHTML($item_data['html_parsed']);
                $xpath = new DomXPath($dom);
                $selector = '//body/*[count(preceding-sibling::*)+count(following-sibling::*)=0][contains(concat(" ", @class, " "), " sr-only ")]'; // body > .sr-only:only-child

                $item_data['html_is_sr_only'] = (bool) $xpath->query($selector)->length;
            } else {
                $item_data['html_is_sr_only'] = false;
            }

            if (is_null($max_height) || $max_height < $image_data['height']) {
                $max_height = $image_data['height'];
            }
        }

        foreach ($sequence_items_data as &$item_data) {
            $item_data['max_height'] = $max_height;
        }

        return $sequence_items_data;
    }

    //Validate inputs for Custom Sequence
    public function validate_custom_sequence($item_data_to_validate)
    {
        $item_valid = TRUE;

        //check input data
        if (trim($item_data_to_validate['sequence_title']) == '') {
            IbHelpers::set_message('Please add "sequence name".', 'error');
            $item_valid = FALSE;
        }

        if (!isset($item_data_to_validate['rotating_speed']) OR is_null($item_data_to_validate['rotating_speed']) OR $item_data_to_validate['rotating_speed'] == '' OR $item_data_to_validate['rotating_speed'] == 0) {
            $item_data_to_validate['rotating_speed'] = 2000;
        }
        elseif (!is_numeric($item_data_to_validate['rotating_speed']) AND !is_integer($item_data_to_validate['rotating_speed'])) {
            IbHelpers::set_message('Rotating Speed: "' . $item_data_to_validate['rotating_speed'] . '" MUST be an INTEGER.', 'error');
            $item_valid = FALSE;
        } elseif ($item_data_to_validate['rotating_speed'] <= 0) {
            IbHelpers::set_message('Rotating Speed: "' . $item_data_to_validate['rotating_speed'] . '" MUST be a POSITIVE NUMBER.', 'error');
            $item_valid = FALSE;
        }

        if (!isset($item_data_to_validate['timeout']) OR is_null($item_data_to_validate['timeout']) OR $item_data_to_validate['timeout'] == '' OR $item_data_to_validate['timeout'] == 0) {
            $item_data_to_validate['timeout'] = 8000;
        }
        elseif (!is_numeric($item_data_to_validate['timeout']) AND !is_integer($item_data_to_validate['timeout'])) {
            IbHelpers::set_message('Timeout: "' . $item_data_to_validate['timeout'] . '" MUST be an INTEGER.', 'error');
            $item_valid = FALSE;
        } elseif ($item_data_to_validate['timeout'] <= 0) {
            IbHelpers::set_message('Timeout: "' . $item_data_to_validate['timeout'] . '" MUST be a POSITIVE NUMBER.', 'error');
            $item_valid = FALSE;
        }

        //return
        return $item_valid;
    }

    //Validate inputs for Custom Sequence - Item
    public function validate_custom_sequence_item($item_data_to_validate)
    {
        $item_valid = TRUE;

        //check input data
        if (!isset($item_data_to_validate['sequence_id']) OR trim($item_data_to_validate['sequence_id']) == '') {
            IbHelpers::set_message('Please Select a Sequence for This Sequence Item', 'error');
            $item_valid = FALSE;
        }

        // Check image - Custom Scroller Sequence - Item should have an Image to be displayed
        if (!isset($item_data_to_validate['image']) OR trim($item_data_to_validate['image']) == '') {
            IbHelpers::set_message('Please Select an Image for This Sequence Item', 'error');
            $item_valid = FALSE;
        }

        // Check if image is passed
        if (!isset($item_data_to_validate['image_location']) OR trim($item_data_to_validate['image_location']) == '') {
            IbHelpers::set_message('Image Location for This Sequence Item is REQUIRED.', 'error');
            $item_valid = FALSE;
        }

        // Check Links - NOTE: sequence_item_link_url is NOT Required but we will have to validate it when EXTERNAL Link is used
        if (trim($item_data_to_validate['link_type']) != '' AND in_array($item_data_to_validate['link_type'], array('internal', 'external'))) {
            switch ($item_data_to_validate['link_type']) {
                // For No Link and
                case 'none':
                case 'internal':
                    // @TODO: If any Validation is REQUIRED -=> add validation rules for this Case. 'none' should NOT have any validation on the: sequence_item_link_url
                    break;
                case 'external':
                    // item_link_url is NOT REQUIRED, BUT when provided we will have to VALIDATE if is in CORRECT Format
                    if (trim($item_data_to_validate['link_url']) != '' AND filter_var($item_data_to_validate['link_url'], FILTER_VALIDATE_URL) == FALSE) {
                        IbHelpers::set_message(
                            'The Specified External Link URL: "' . $item_data_to_validate['link_url'] . '" is NOT VALID URL!<br />'
                                . 'An EXAMPLE Valid URL is: "http://www.example.com".', 'error');
                        $item_valid = FALSE;
                    }
                    break;
                // There specified Link Type is NOT Maintained by the System
                default:
                    IbHelpers::set_message('The Specified Link Type: "' . $item_data_to_validate['link_type'] . '" is NOT MAINTAINED by the System', 'error');
                    $item_valid = FALSE;
                    break;
            }
        }

        // Return
        return $item_valid;
    }

    public function add_custom_sequence($item_input_data)
    {
        // 1. get the ID of the currently-logged in user, i.e. the user who is making the update
        $logged_in_user = Auth::instance()->get_user();

        /* 2. Create the NEW Custom Sequence Data */
        //Set the Custom Sequence data to be added to Database
        $item_to_add_data['title'] = $item_input_data['sequence_title'];
        $item_to_add_data['animation_type'] = $item_input_data['animation_type'];
        $item_to_add_data['order_type'] = (empty($item_input_data['order_type'])) ? 'ascending' : $item_input_data['order_type'];
        $item_to_add_data['rotating_speed'] = ($item_input_data['rotating_speed'] == 0) ? 2000 : $item_input_data['rotating_speed'];
        $item_to_add_data['timeout'] = ($item_input_data['timeout'] == 0) ? 8000 : $item_input_data['timeout'];
        $item_to_add_data['pagination'] = $item_input_data['pagination'];
        $item_to_add_data['controls'] = $item_input_data['controls'];
        // Format the required fields for mysql storage and tracking
        $item_to_add_data['date_created'] = date('Y-m-d H:i:s');
        $item_to_add_data['created_by'] = $logged_in_user['id'];
        $item_to_add_data['date_modified'] = $item_to_add_data['date_created'];
        $item_to_add_data['modified_by'] = $logged_in_user['id'];
        $item_to_add_data['publish'] = $item_input_data['publish'];
        $item_to_add_data['deleted'] = 0;

        // Get the plugin ID
        $plugin_name = $item_input_data['plugin'];
        if ($plugin_name == 'banners') {
            $plugin_name = 'customscroller';
        }
        $item_to_add_data['plugin'] = DB::select()->from(self::$plugins_table)->where('name', '=', $plugin_name)->execute()->get('id', 0);

        $validation = $this->validate_custom_sequence($item_input_data);

        if ($validation) {
            //add the Custom Sequence to DB
            $insert_result = DB::insert(self::$model_sequences_table)->values($item_to_add_data)->execute();
            $new_sequence_id = $insert_result[0];

            /* 3. If there is sequence set items -=> create them */
            if (isset($item_input_data['sequence_items']) AND is_array($item_input_data['sequence_items']) AND count($item_input_data['sequence_items']) > 0) {
                $created_sequence_items_ids = '';
                foreach ($item_input_data['sequence_items'] as $sequence_item_data) {
                    $created_sequence_items_ids .= $this->add_custom_sequence_item($sequence_item_data, $new_sequence_id) . '|';
                }
            }
//@TODO: Check if THERE IS A SEQUENCE ADDED TO THE SPECIFIED PLUGIN ITEM AND IF YES -=> REPLACE IT WITH THE CURRENT ONE
//@TODO: BUILD a FUNCTION For THIS
            /* 4. Link This Page to its corresponding Plugin Item  */
            if ($new_sequence_id) {
                $plugin_item_sequence_data = array(
                    'sequence_holder_id' => $item_input_data['sequence_holder_id'],
                    'holder_plugin_name' => $item_input_data['sequence_holder_plugin'],
                    'sequence_id' => $new_sequence_id
                );
                $sequence_plugin_item_link = DB::insert(self::$model_plugins_sequences)->values($plugin_item_sequence_data)->execute();
                unset($plugin_item_sequence_data);
            }

            // return new ID
            return $new_sequence_id;
        } else {
            return FALSE;
        }
    }


    public function add_custom_sequence_item($item_input_data, $sequence_id = NULL)
    {
        // 1. get the ID of the currently-logged in user, i.e. the user who is making the update
        $logged_in_user = Auth::instance()->get_user();

        /* 2. Create the NEW Custom Sequence Item Data */
        //Set the Custom Sequence Item data to be added to Database
        $item_to_add_data['sequence_id'] = ($sequence_id !== NULL) ? $sequence_id : 0;
        $item_to_add_data['title'] = $item_input_data['title'];
        $item_to_add_data['image'] = $item_input_data['image'];
        $item_to_add_data['mobile_image'] = $item_input_data['mobile_image'];
        $item_to_add_data['image_location'] = $item_input_data['image_location'];
        $item_to_add_data['order_no'] = $item_input_data['order_no'];
        $item_to_add_data['label'] = @$item_input_data['label'];
        $item_to_add_data['html'] = $item_input_data['html'];
        $item_to_add_data['link_type'] = isset($item_input_data['link_type']) ? $item_input_data['link_type'] : '';
        $item_to_add_data['link_url'] = $item_input_data['link_url'];
        $item_to_add_data['link_target'] = $item_input_data['link_target'];
        $item_to_add_data['overlay_position'] = $item_input_data['overlay_position'];
        // Format the required fields for mysql storage and tracking
        $item_to_add_data['date_created'] = date('Y-m-d H:i:s');
        $item_to_add_data['created_by'] = $logged_in_user['id'];
        $item_to_add_data['date_modified'] = $item_to_add_data['date_created'];
        $item_to_add_data['modified_by'] = $logged_in_user['id'];
        $item_to_add_data['publish'] = $item_input_data['publish'];
        $item_to_add_data['deleted'] = 0;
        //add the Custom Sequence Item to DB
        $insert_result = DB::insert(self::$model_sequence_items_table)->values($item_to_add_data)->execute();

        // return new ID
        return $insert_result[0];
    }


    public function update_custom_sequence($sequence_input_data)
    {
        // 1. get the ID of the currently-logged in user, i.e. the user who is making the update
        $logged_in_user = Auth::instance()->get_user();

        /* 2. Update specified Custom Sequence */
        //Set the Custom Sequence data to be Updated in Database
        $sequence_to_update_id = $sequence_input_data['id'];
        $sequence_to_update_data['title'] = $sequence_input_data['sequence_title'];
        $sequence_to_update_data['animation_type'] = $sequence_input_data['animation_type'];
        $sequence_to_update_data['order_type'] = (empty($sequence_input_data['order_type'])) ? 'ascending' : $sequence_input_data['order_type'];
        $sequence_to_update_data['rotating_speed'] = $sequence_input_data['rotating_speed'];
        $sequence_to_update_data['timeout'] = $sequence_input_data['timeout'];
        $sequence_to_update_data['pagination'] = $sequence_input_data['pagination'];
        $sequence_to_update_data['controls'] = $sequence_input_data['controls'];
        // Format the required fields for mysql storage
        $sequence_to_update_data['date_modified'] = date('Y-m-d H:i:s');
        $sequence_to_update_data['modified_by'] = $logged_in_user['id'];
        $sequence_to_update_data['publish'] = $sequence_input_data['publish'];

        if ($sequence_to_update_data['rotating_speed'] == '' OR is_null($sequence_to_update_data['rotating_speed']) OR $sequence_to_update_data['rotating_speed'] == 0)
        {
            $sequence_to_update_data['rotating_speed'] = 2000;
        }
        if ($sequence_to_update_data['timeout'] == '' OR is_null($sequence_to_update_data['timeout']) OR $sequence_to_update_data['timeout'] == 0)
        {
            $sequence_to_update_data['timeout'] = 8000;
        }

        // Update the Custom Sequence in DB
        $validation = $this->validate_custom_sequence($sequence_input_data);
        if (!$validation) {
            return FALSE;
        } else {
            $update_result = DB::update(self::$model_sequences_table)->set($sequence_to_update_data)->where('id', '=', $sequence_input_data['id'])->execute();

            /* 3. If the Sequence WAS Updated SUCCESS AND there is set Sequence Items -=> create / update them */
            if ($update_result == 1 AND isset($sequence_input_data['sequence_items']) AND is_array($sequence_input_data['sequence_items']) AND count($sequence_input_data['sequence_items']) > 0) {
                $actioned_sequence_items_ids = '';
                foreach ($sequence_input_data['sequence_items'] as $sequence_item_data) {
                    if (isset($sequence_item_data['id']) AND $sequence_item_data['id'] == 'new') {
                        // Add Sequence Item
                        $actioned_sequence_items_ids .= $this->add_custom_sequence_item($sequence_item_data, $sequence_to_update_id) . '|';
                    } else {
                        // Update Sequence Item
                        $actioned_sequence_items_ids .= ($this->update_custom_sequence_item($sequence_item_data) == TRUE) ? $sequence_item_data['id'] . '|' : '';
                    }
                }
                unset($actioned_sequence_items_ids);
            }
//@TODO: Check if THERE IS A SEQUENCE ADDED TO THE SPECIFIED PLUGIN ITEM AND IF YES -=> REPLACE IT WITH THE CURRENT ONE
//@TODO: BUILD a FUNCTION For THIS
            // @NOTE: There IS NO NEED for This Step as The Sequence Has been Linked to This Plugin Item Already
            /* 4. Link This Page to its corresponding Plugin Item  */
//		if ($update_result[0] == 1)
//		{
//			$plugin_item_sequence_data = array(
//				'sequence_holder_id' => $sequence_input_data['sequence_holder_id'],
//				'holder_plugin_name' => $sequence_input_data['sequence_holder_plugin'],
//				'sequence_id'		 => $updated_sequence_id
//			);
//			$sequence_plugin_item_link = DB::insert (self::$model_plugins_sequences)->values ($plugin_item_sequence_data)->execute ();
//			unset($plugin_item_sequence_data);
//		}

            // return TRUE / FALSE for the Updated Sequence
            return ($update_result == 1) ? TRUE : FALSE;
        }
    }


    public function update_custom_sequence_item($sequence_item_input_data)
    {
        // 1. get the ID of the currently-logged in user, i.e. the user who is making the update
        $logged_in_user = Auth::instance()->get_user();

        /* 2. Update Custom Sequence Item Data */
        // Set the Custom Sequence Item data to be updated in Database
        $sequence_item_to_update_data['sequence_id'] = $sequence_item_input_data['sequence_id'];
        $sequence_item_to_update_data['title'] = $sequence_item_input_data['title'];
        $sequence_item_to_update_data['image'] = $sequence_item_input_data['image'];
        $sequence_item_to_update_data['mobile_image'] = $sequence_item_input_data['mobile_image'];
        $sequence_item_to_update_data['image_location'] = $sequence_item_input_data['image_location'];
        $sequence_item_to_update_data['order_no'] = $sequence_item_input_data['order_no'];
        $sequence_item_to_update_data['label'] = @$sequence_item_input_data['label'];
        $sequence_item_to_update_data['html'] = $sequence_item_input_data['html'];
        $sequence_item_to_update_data['link_type'] = $sequence_item_input_data['link_type'];
        $sequence_item_to_update_data['link_url'] = $sequence_item_input_data['link_url'];
        $sequence_item_to_update_data['link_target'] = $sequence_item_input_data['link_target'];
        $sequence_item_to_update_data['overlay_position'] = $sequence_item_input_data['overlay_position'];
        // Format the required fields for mysql storage and tracking for this Update
        $sequence_item_to_update_data['date_modified'] = date('Y-m-d H:i:s');
        $sequence_item_to_update_data['modified_by'] = $logged_in_user['id'];
        $sequence_item_to_update_data['publish'] = $sequence_item_input_data['publish'];
        // Update the Custom Sequence Item in DB
        $update_result = DB::update(self::$model_sequence_items_table)->set($sequence_item_to_update_data)->where('id', '=', $sequence_item_input_data['id'])->execute();

        // return TRUE / FALSE
        return ($update_result == 1) ? TRUE : FALSE;
    }


    public function get_sequence_item_tr_html($sequence_item_data)
    {
        $item_tr_html = '';
        $item_tr_html = View::factory(
            'admin/snippets/sequence_item_tr_html_snippet',
            array(
                'sequence_item_data' => $sequence_item_data,
                'link_types' => self::$scroller_item_link_types
            )
        )->set('skip_comments_in_beginning_of_included_view_file', TRUE)->render();

        // Return
        return $item_tr_html;
    }

    public function get_available_image_html($available_image)
    {
        $image_html = '';
        $image_html = View::factory(
            'admin/snippets/sequence_available_image_html_snippet',
            array(
                'available_image' => $available_image
            )
        )->set('skip_comments_in_beginning_of_included_view_file', TRUE)->render();

        return $image_html;
    }

    public function get_sequence_item_tr_data($sequence_item_data)
    {
        // Initialize the data for a Sequence Item row listed in the:
        $item_tr_data = array(
            'number' => '',
            'image_html' => '',
            'image_name' => '',
            'title' => '',
            'order_no' => '',
            'label' => '',
            'html' => '',
            'link_type' => '',
            'link_url' => '',
            'link_target' => '',
            'overlay_position' => '',
            'publish' => '',
            'deleted' => ''
        );

        // Get the Image - filename part without the extension
        $image_parts = explode('.', $sequence_item_data['image']);
        $item_image = $image_parts[0];

        $item_tr_data['id'] = $sequence_item_data['id'];

        // Set Number
        $item_tr_data['number'] = $sequence_item_data['order_no']
            . '<input type="hidden" name="sequence_data[sequence_items][' . $sequence_item_data['id'] . $item_image . '][id]" value="' . $sequence_item_data['id'] . '" />'
            . '<input type="hidden" name="sequence_data[sequence_items][' . $sequence_item_data['id'] . $item_image . '][sequence_id]" value="' . $sequence_item_data['sequence_id'] . '" />';

        // Set Image Tag
        $item_tr_data['image_html'] = '<img class="span2" src="'
            . Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder, $sequence_item_data['image'], $sequence_item_data['image_location'] . DIRECTORY_SEPARATOR . '_thumbs_cms')
            . '" alt="' . $sequence_item_data['image'] . '">';

        // Set Image Name + hidden: image and image_location- @TODO: provide this as a DROP-down with images
        $item_tr_data['image_name'] = '<span id="item_' . $sequence_item_data['id'] . '_image">' . $sequence_item_data['image'] . '</span>'
            . '<input type="hidden" name="sequence_data[sequence_items][' . $sequence_item_data['id'] . $item_image . '][image]" value="' . $sequence_item_data['image'] . '" />'
            . '<input type="hidden" name="sequence_data[sequence_items][' . $sequence_item_data['id'] . $item_image . '][image_location]" value="' . $sequence_item_data['image_location'] . '" />';

        // Set mobile image name
        $item_tr_data['mobile_image_name'] = '<span id="item_' . $sequence_item_data['id'] . '_mobile_image">' . $sequence_item_data['mobile_image'] . '</span>'
            . '<input type="hidden" name="sequence_data[sequence_items][' . $sequence_item_data['id'] . $item_image . '][mobile_image]" value="' . $sequence_item_data['image'] . '" />';

        // Set Title
        $item_tr_data['title'] = '<input type="text" name="sequence_data[sequence_items][' . $sequence_item_data['id'] . $item_image . '][title]" id="item_' . $sequence_item_data['id'] . '_title" value="' . $sequence_item_data['title'] . '" size="10"/>';

        // Set Order Number
        $item_tr_data['order_no'] = '<input type="text" class="span1" name="sequence_data[sequence_items][' . $sequence_item_data['id'] . $item_image . '][order_no]" id="' . $item_image . '_title" value="' . $sequence_item_data['order_no'] . '" size="5" />'
            . '<input type="hidden" name="sequence_data[sequence_items][' . $sequence_item_data['id'] . $item_image . '][id]" value="' . $sequence_item_data['id'] . '" />'
            . '<input type="hidden" name="sequence_data[sequence_items][' . $sequence_item_data['id'] . $item_image . '][sequence_id]" value="' . $sequence_item_data['sequence_id'] . '" />';

        // Set Label
        $item_tr_data['label'] = '<input type="text" name="sequence_data[sequence_items][' . $sequence_item_data['id'] . $item_image . '][label]" id="item_' . $sequence_item_data['id'] . '_title" value="' . $sequence_item_data['label'] . '" size="10"/>';

        // Set HTML
        $item_tr_data['html'] = '<span class="sequence_item_edit link"'
            . ' onclick="get_sequence_scroller_item_editor(' . $sequence_item_data['id'] . ',\'' . $sequence_item_data['image'] . '\')">' . ((trim($sequence_item_data['html']) != '') ? 'Edit' : 'Add') . '</span>'
            . '<textarea  cols="30" rows="2" style="display:none;" name="sequence_data[sequence_items][' . $sequence_item_data['id'] . $item_image . '][html]" id="html_' . $item_image . '">'
            . $sequence_item_data['html'] . '</textarea>';

        // Set Link Type - provide this as a DROP-down with link_types
        $item_tr_data['link_type'] = '<select name="sequence_data[sequence_items][' . $sequence_item_data['id'] . $item_image . '][link_type]"'
            . ' id="link_type_' . $sequence_item_data['image'] . '"'
            . ' class="span2"'
            . ' onchange="'
            . 'if(typeof $(\'#sequence_item_' . $item_image . '_link_url_hidden\') !== \'undefined\') $(\'[id=&quot;sequence_item_' . $item_image . '_link_url_hidden&quot;]\').val(this.value);'
            . 'update_sequence_urls_feed_based_on_link_type(this.value, \'sequence_item_' . $item_image . '_link_url_holder\', \'' . $item_image . '\');'
            . '"'
            . ' >';
        $item_tr_data['link_type'] .= '<option value="">- Select Link Type -</option>';
        foreach (self::$scroller_item_link_types as $link_type_key => $link_type) {
            $item_tr_data['link_type'] .= '<option value="' . $link_type_key . '"'
                . ((isset($sequence_item_data['link_type']) AND $sequence_item_data['link_type'] == $link_type_key) ? ' selected="selected"' : '')
                . '>' . $link_type . '</option>';
        }
        $item_tr_data['link_type'] .= '</select>';

        // Set Link URL
        $item_tr_data['link_url'] = '<span class="sequence_item_' . $item_image . '_link_url_holder">';
        if ($sequence_item_data['link_type'] == 'internal') {
            // Provide this as a DROP-down with This Website Pages or just TEXT BOX
            $item_tr_data['link_url'] .= $this->get_pages_drop_down_for_editor($sequence_item_data['link_url'], $item_image);
        } else {
            // Provide this as an INPUT field for 'external' and 'none' Link Types
            $item_tr_data['link_url'] .= '<input type="text" name="sequence_item_link_url"'
                . ' id="sequence_item_' . $item_image . '_link_url"'
                . ' class="sequence_item_link_url form-control"'
                . ' value="' . $sequence_item_data['link_url'] . '"'
                . ' onkeyup="if(typeof $(\'#sequence_item_' . $item_image . '_link_url_hidden\') !== \'undefined\') $(\'#sequence_item_' . $item_image . '_link_url_hidden\').val(this.value);"'
                . ' size="5" />';

        }
        $item_tr_data['link_url'] .= '</span>';
        $item_tr_data['link_url'] .= '<input type="hidden" name="sequence_data[sequence_items][' . $sequence_item_data['id'] . $item_image . '][link_url]"'
            . ' id="sequence_item_' . $item_image . '_link_url_hidden" value="' . $sequence_item_data['link_url'] . '" />';
        $item_tr_data['link_url'] .= '<input type="hidden" name="tmp_item_ext_link_url" class="tmp_item_ext_link_url"'
            . ' value="'
            . ((isset($sequence_item_data['link_url']) AND isset($sequence_item_data['link_type']) AND $sequence_item_data['link_type'] == 'external') ? $sequence_item_data['link_url'] : '')
            . '" />';
        $item_tr_data['link_url'] .= '<input type="hidden" name="tmp_item_int_link_url" class="tmp_item_int_link_url"'
            . ' value="'
            . ((isset($sequence_item_data['link_url']) AND isset($sequence_item_data['link_type']) AND $sequence_item_data['link_type'] == 'internal') ? $sequence_item_data['link_url'] : '')
            . '" />';

		$same_tab = ((!isset($sequence_item_data['link_target']) OR $sequence_item_data['link_target'] == '0') ? ' active' : '');

        // Set Link Target  as a proper buttons / Yes / No Options
        $item_tr_data['link_target'] = '<div class="span3 controls">'
            . '<div class="btn-group" data-toggle="buttons">'
			. '    <label class="btn btn-plain'.($same_tab ? ' active' : '').'">'
			. '        <input type="radio" name="sequence_data[sequence_items][' . $sequence_item_data['id'] . $item_image . '][link_target]" value="0"'.($same_tab ? ' checked' : '').' />Same Tab'
			. '    </label>'
			. '    <label class="btn btn-plain'.(( ! $same_tab) ? ' active' : '').'">'
			. '        <input type="radio" name="sequence_data[sequence_items][' . $sequence_item_data['id'] . $item_image . '][link_target]" value="1"'.(( ! $same_tab) ? ' checked' : '').' />New Tab'
			. '    </label>'
            . '</div>';


        $item_tr_data['overlay_position'] = '<select name="sequence_data[sequence_items]['.$sequence_item_data['id'].$item_image.'][overlay_position]" id="item' .$sequence_item_data['id']. '_overlay_position">' .
						'<option value="center"'.(($sequence_item_data['overlay_position'] == 'center') ? ' selected="selected"' : '').'>Centre</option>' .
						'<option value="left"'.(($sequence_item_data['overlay_position'] == 'left') ? ' selected="selected"' : '').'>Left</option>' .
						'<option value="right"'.(($sequence_item_data['overlay_position'] == 'right') ? ' selected="selected"' : '').'>Right</option>' .
						'</select>';

        // Set Publish
        $item_tr_data['publish'] = '<i class="icon-'.(($sequence_item_data['publish'] == 1) ? 'ok' : 'remove').'" data-id="'.$sequence_item_data['id'].'"></i>'.
            '<input type="hidden" name="sequence_data[sequence_items][' . $sequence_item_data['id'] . $item_image . '][publish]" value="' . $sequence_item_data['publish'] . '" />';

        // Set Deleted -
        $item_tr_data['deleted'] = '<i class="icon-remove-circle" onclick="toggleDelete(' . $sequence_item_data['id'] . ');"></i>';

        // Return
        return $item_tr_data;
    }


    public function get_pages_drop_down_for_editor($current_page = NULL, $item_image_identifier = NULL)
    {
        // Initialize the $pages_drop_down
        $pages_drop_down = '';

        // Get instance of the Pages Model and get ALL available Pages
        $pages_mgr = new Model_Pages();
        $pages = $pages_mgr->get_all_pages();

        // Build the Select Drop Down with INTERNAL Links, i.e. Pages -=> ONLY the PUBLISHED Ones
        if ($pages) {
            // Start the Pages Drop Down
            $pages_drop_down = '<select name="sequence_item_link_url" type="text" class="sequence_item_link_url span3"'
                . ' id="sequence_item_' . (($item_image_identifier !== NULL) ? $item_image_identifier . '_' : '') . 'link_url"'
                . (
                ($item_image_identifier !== NULL) ?
                    ' onchange="if(typeof $(\'#sequence_item_' . $item_image_identifier . '_link_url_hidden\') !== \'undefined\') $(\'#sequence_item_' . $item_image_identifier . '_link_url_hidden\').val(this.value);"'
                    : ''
                )
                . '>';
            // Set DEFAULT Option
            $pages_drop_down .= '<option value="0">- Select Page -</option>';
            // Render Pages as Options -=> SKIP UN-Published
            foreach ($pages as $page) {
                // Skip NOT Published Pages
                if ($page['publish'] == 1) {
                    $pages_drop_down .= '<option value="' . $page['id'] . '"'
                        . (($current_page == $page['id']) ? ' selected="selected"' : '')
                        . '>' .
                        $page['name_tag'] . '</option>';
                }
            }
            // Complete the Drop Down
            $pages_drop_down .= '</select>';
        }

        return $pages_drop_down;
    }


    /*
     * @TODO: REMOVE UNNECESSARY CODE ONCE THIS PLUGIN IS DONE
     */

    //Update a News Story record
    public function update($item_update_data)
    {
//		echo "\nUpdate News Story: \n";
//		IbHelpers::die_r($item_update_data);

        // 1. get the ID of the currently-logged in user, i.e. the user who is making the update
        $logged_in_user = Auth::instance()->get_user();

        /* 2. Update the existent News Story */
        //Set the News Story data to be Updated to Database
        $item_to_update_data['category_id'] = $item_update_data['category_id'];
        $item_to_update_data['title'] = $item_update_data['title'];
        $item_to_update_data['summary'] = $item_update_data['summary'];
        $item_to_update_data['content'] = $item_update_data['content'];
        $item_to_update_data['image'] = $item_update_data['image'];
        $item_to_update_data['mobile_image'] = $item_update_data['mobile_image'];
        $item_to_update_data['event_date'] = ($item_update_data['event_date'] != '0000-00-00 00:00:00' AND !empty($item_update_data['event_date'])) ? date('Y-m-d H:i:s', strtotime($item_update_data['event_date'])) : NULL;
        $item_to_update_data['date_publish'] = ($item_update_data['date_publish'] != '0000-00-00 00:00:00' AND !empty($item_update_data['date_publish'])) ? date('Y-m-d H:i:s', strtotime($item_update_data['date_publish'])) : NULL;
        $item_to_update_data['date_remove'] = ($item_update_data['date_remove'] != '0000-00-00 00:00:00' AND !empty($item_update_data['date_publish'])) ? date('Y-m-d H:i:s', strtotime($item_update_data['date_remove'])) : NULL;
        $item_to_update_data['publish'] = $item_update_data['publish'];
        // Format the required fields for mysql storage
//		$item_to_update_data['date_created'] = date('Y-m-d H:i:s');
//		$item_to_update_data['created_by'] = $logged_in_user['id'];
        $item_to_update_data['date_modified'] = date('Y-m-d H:i:s');
        $item_to_update_data['modified_by'] = $logged_in_user['id'];
        $item_to_update_data['deleted'] = 0;

        //Update the News Story to DB
        $update_result = DB::update(self::$model_items_table)->set($item_to_update_data)->where('id', '=', $item_update_data['id'])->execute();

        // return
        return $update_result;
    }


    //Sets a specified News Story Record in the DB as Deleted
    public function delete($item_id)
    {
//		echo "\nDelete News Story: \n";
//		IbHelpers::die_r($item_id);

        // 1. get the ID of the currently-logged in user, i.e. the user who is making the update
        $logged_in_user = Auth::instance()->get_user();

        // 2. Mark the specified News Story as deleted - in this case result will be INT - holding the number of affected rows
        $delete_result = DB::update(self::$model_items_table)->set(
            array(
                'deleted' => 1, 'date_modified' => date('Y-m-d H:i:s'), 'modified_by' => $logged_in_user['id']
            )
        )->where('id', '=', $item_id)->execute();

        // 3. return
        return $delete_result;
    }

    public function change_published_status($id, $publish)
    {
        $logged_in_user = Auth::instance()->get_user();
        $total_rows = DB::update(self::$model_sequence_items_table)->set(array(
            'publish' => $publish,
            'date_modified' => date('Y-m-d H:i:s'),
            'modified_by' => $logged_in_user['id']
        ))->where('id', '=', $id)->execute();

        if ($total_rows > 0) {
            $str_res = 'success';
        } else {
            $str_res = 'Error: Could not update the database';
        }

        return $str_res;
    }

    public function delete_item($id)
    {
        $logged_in_user = Auth::instance()->get_user();
        $total_rows = DB::update(self::$model_sequence_items_table)->set(array(
            'deleted' => 1,
            'date_modified' => date('Y-m-d H:i:s'),
            'modified_by' => $logged_in_user['id']
        ))->where('id', '=', $id)->execute();

        if ($total_rows > 0)
            $str_res = 'success';
        else
            $str_res = 'Error: Could not update the database';

        return $str_res;
    }


    public static function toggle_item_publish($item_id, $publish_flag)
    {
//		echo "\nToggle Publish News Story: \n";
//		IbHelpers::pre_r(array('id' => $item_id, 'publish_flag' => $publish_flag));

        // 1. get the ID of the currently-logged in user, i.e. the user who is making the update
        $logged_in_user = Auth::instance()->get_user();

        // 2. Toggle the Publish flag of the specified News Story
        $toggle_publish_result = DB::update(self::$model_items_table)->set(
            array(
                'publish' => $publish_flag, 'date_modified' => date('Y-m-d H:i:s'), 'modified_by' => $logged_in_user['id']
            )
        )->where('id', '=', $item_id)->execute();

        // 3. return
        return $toggle_publish_result;
    }

    // Load the Editor View for a Custom Scroller Sequence. Could be defined by the Sequence ID ($sequence_id) or by $sequence_holder_id and $sequence_holder_plugin
    public function get_custom_sequence_editor_view($sequence_holder_plugin, $plugin_item_id = NULL, $sequence_id = NULL)
    {
        $sequence_id == '' ? $sequence_id = NULL : '';

        // Get all available sequences
        $existing_sequences = $this->get_custom_sequence_admin(NULL, $sequence_holder_plugin);

        // Get all available sequence items
        $existing_sequence_items = $this->get_custom_sequence_items_admin();

        // Get the Data for the Specified Sequence or Default it to empty Array
        $sequence_data = (!is_null($sequence_id)) ? $this->get_custom_sequence_admin($sequence_id) : array();

        // Get the Items, that belong to the Specified Sequence
        $sequence_data['sequence_items'] = (!is_null($sequence_id)) ? $this->get_custom_sequence_items_admin($sequence_id) : array();

        $all_images = Model_Media::factory('Media')->get_all_items_based_on('location', $sequence_holder_plugin, 'details', '=', NULL);


        if (count($all_images) > 0) {
            foreach ($all_images as $image_key => $image) {
                $image['sequences'] = '';
                $image['style'] = '';
                foreach ($existing_sequence_items as $existing_sequence_item) {
                    if ($image['filename'] == $existing_sequence_item['image']) {
                        $image['sequences'] .= ' seq_' . $existing_sequence_item['sequence_id'];
                        if (isset($sequence_data['id']) AND $existing_sequence_item['sequence_id'] == $sequence_data['id']) {
                            $image['style'] = 'display: none; ';
                        }
                    }
                }
                unset($all_images[$image_key]);
                $all_images[$image_key] = $image;
            }
        }

        // Get the available images
        $available_images        = Model_Media::factory('Media')->get_all_items_based_on('location', $sequence_holder_plugin, 'details', '=', null);
        $available_mobile_images = Model_Media::factory('Media')->get_all_items_based_on('location', 'mobile_'.$sequence_holder_plugin, 'details', '=', null);

        $sequence_data['available_images'] = [];
        $sequence_data['available_mobile_images'] = [];

        // Clear the already added images as items so we don't add duplicate images to the same sequence
        if (count($available_images) > 0 && count($sequence_data['sequence_items']) > 0) {
            foreach ($available_images as $available_key => $available_image) {
                foreach ($sequence_data['sequence_items'] as $sequence_item) {
                    if ($available_image['filename'] == $sequence_item['image']) {
                        unset($available_images[$available_key]);
                    }
                }
            }
        }

        if (count($available_mobile_images) > 0 && count($sequence_data['sequence_items']) > 0) {
            foreach ($available_mobile_images as $available_key => $available_image) {
                foreach ($sequence_data['sequence_items'] as $sequence_item) {
                    if ($available_image['filename'] == $sequence_item['mobile_image']) {
                        unset($available_mobile_images[$available_key]);
                    }
                }
            }
        }

        // Set the CSS and JS Files for this View
        $view_css[] = '<link href="' . URL::get_engine_plugin_assets_base('customscroller') . 'css/admin/customscroller_sequence_edit.css' . '" rel="stylesheet">';
        $view_css[] = '<link href="' . URL::get_engine_plugin_assets_base('customscroller') . 'css/admin/customscroller_sequence_item_edit.css' . '" rel="stylesheet">';
        $view_js[] = '<script src="' . URL::get_engine_plugin_assets_base('customscroller') . 'js/admin/customscroller_sequence_edit.js"></script>';

        // Return This Sequence Editor View
        return View::factory(
            'admin/add_edit_custom_sequence',
            array(
                'sequence_holder_plugin' => $sequence_holder_plugin,
                'sequence_holder_id' => $plugin_item_id,
                'sequence_data' => $sequence_data,
                'existing_sequences' => @$existing_sequences,
                'existing_sequence_items' => $existing_sequence_items,
                'all_images' => $all_images,
                'animation_types' => self::$scroller_sequence_animation_modes,
                'order_types' => self::$scroller_sequence_order_types,
                'link_types' => self::$scroller_item_link_types,
                'view_css_files' => $view_css,
                'view_js_files' => $view_js
            )
        )->render();
    }


    // Load the Editor View for a Custom Scroller Sequence Item.
    public function get_custom_sequence_item_editor_view($sequence_holder_plugin, $item_id = NULL, $item_image = '', $sequence_id = NULL)
    {
        // Get the Data for the Specified Sequence Item
        $sequence_item_data = ($item_id !== NULL) ? $this->get_custom_sequence_item_details($item_id) : array();

        // Get the available images
        $available_desktop_images = Model_Media::factory('Media')->get_all_items_based_on('location', $sequence_holder_plugin,           'details', '=', null);
        $available_mobile_images  = Model_Media::factory('Media')->get_all_items_based_on('location', 'mobile_'.$sequence_holder_plugin, 'details', '=', null);

        // Set the CSS and JS Files for this View
        $view_css[] = '<link href="' . URL::get_engine_plugin_assets_base('customscroller') . 'css/admin/customscroller_sequence_item_edit.css' . '" rel="stylesheet">';
        $view_js[] = '<script src="' . URL::get_engine_plugin_assets_base('customscroller') . 'js/admin/customscroller_sequence_item_edit.js"></script>';

        // Return This Sequence Editor View
        return View::factory(
            'admin/add_edit_custom_sequence_item',
            array(
                'sequence_holder_plugin' => $sequence_holder_plugin,
                'sequence_item_data' => ((isset($sequence_item_data[0])) ? $sequence_item_data[0] : array()),
                'image_to_add' => $item_image,
                'sequence_id' => $sequence_id,
                'available_images' => $available_desktop_images,
                'available_mobile_images' => $available_mobile_images,
                'link_types' => self::$scroller_item_link_types,
                'view_css_files' => $view_css,
                'view_js_files' => $view_js
            )
        )->render();
    }

    /* END of Back-End (CMS) : Admin Functions */


    /* ########## - ########## */


    /* Front-End : APP Functions  */

    /* These are some Front-End Related Functions that will facilitate the Display of Custom Sequences etc. for the Front End */

    public static function render_front_end_custom_sequence($sequence_holder_plugin_name, $sequence_holder_item_id, $sequence_id, $stylesheet = TRUE)
    {
        $rendered_sequence_html = '';
        $sequence_items_html = '';

        // Get an instance of this Model
        $plugin_model = new Model_Customscroller();

        // Get Details of the Specified Sequence ID
        $sequence_to_render_details = $plugin_model->get_custom_sequence_data_front_end($sequence_id);

        // Get The Items for the Specified Sequence
        $sequence_items = $plugin_model->get_custom_sequence_items_data_front_end($sequence_id, $sequence_to_render_details['order_type']);

        // Prepare the Sequence Items for Rendering
        foreach ($sequence_items as $item_to_render) $sequence_items_html .= View::factory(
            'frontend/snippets/custom_sequence_item_view',
            array(
                'item_data' => $item_to_render
            )
        )->set('skip_comments_in_beginning_of_included_view_file', TRUE)->render();

        // Prepare JS / CSS Files for the Front End
        // Set the CSS and JS Files for this View
		$view_css[] =  ($stylesheet) ? '<link type="text/css" href="' . URL::get_engine_plugin_assets_base('customscroller') . 'css/front_end/customscroller_front_end.css' . '" rel="stylesheet">' : '';
        $view_js[] = '<script type="text/javascript" src="' . URL::get_engine_plugin_assets_base('customscroller') . 'js/front_end/bxslider/jquery.bxslider.js"></script>';

        // Render the Sequence HTML View
        $rendered_sequence_html = View::factory(
            'frontend/custom_sequence_feed_view',
            array(
                'sequence_holder_plugin' => $sequence_holder_plugin_name,
                'sequence_data' => $sequence_to_render_details,
                'sequence_items' => $sequence_items_html,
                'view_css_files' => $view_css,
                'view_js_files' => $view_js
            )
        )->set('skip_comments_in_beginning_of_included_view_file', TRUE)->render();

        // Return
        return $rendered_sequence_html;
    }

    /* END of Front-End : APP Functions  */

}//end of class