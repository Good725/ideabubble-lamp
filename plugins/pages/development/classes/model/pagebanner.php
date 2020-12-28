<?php
defined ('SYSPATH')or die('No direct script access.');

class Model_PageBanner extends Model {

	/*
	 * @TODO: ADD PROPER DOCUMENTATION-COMMENTS TO EACH OF THE HELPER (PUBLIC) FUNCTIONS!!!
	 */

	public static $banner_types = array (0 => 'None', 1 => 'Static', 2 => 'Dynamic', 3 => 'Custom Sequence', 4 => 'Google Map');
	public static $banner_display_order = array ('asc' => 'Ascending', 'desc' => 'Descending', 'rand' => 'Random');
	/**
	 * @var string Holds the RELATIVE path to the MEDIA Folder for the current Project.<br />
	 *               The path is as follows: <em>www/media/photos/banners</em>,
	 *               and will be used to get access to the Folder with Banner Images.
	 */
	public static $media_banners_folder = 'www/media/photos/banners';
	private static $banners_images = array ();
	private static $banner_sequences = array ();
	private static $banners_sequences_images = array ();
    private static $scroller_sequence_table = 'plugin_custom_scroller_sequences';


	public static function get_banner_data ($page_banner_string = NULL, $back_end = FALSE)
	{
		// Initialize the banner list
		self::set_banner_lists_data ();

		// Initialize the banner data to be returned - Default to none, i.e. No banner
		$banner_data = array (
			'banner_type'            => 0, //None. Possible values: check documentation for self::$banner_types
			'static_image'           => NULL, //The image to be used for Static Banners
			'banner_sequence'        => NULL, //The Sequence to be used for Dynamic Banners
			'banner_first_image'     => NULL, //The First image to be used by the Dynamic Banner Type
			'banner_display_order'   => 'asc', //Display Order for Dynamic Banners. Default value: asc for Ascending
			'banner_sequence_images' => NULL, //The set of images, specified for a Dynamic Banner Sequence
			'link_to_page'           => NULL
		);

		// Retrieve the specified banner data, based on the corresponding $page_banner_string
		if (!empty($page_banner_string))
		{
			$page_banner_string = explode ('|', $page_banner_string);
            $banner_data['banner_type'] = $page_banner_string[0];

			// Set the Banner data for the Current Page, base on the specified Banner Type: $page_banner_string[0]
			switch ($page_banner_string[0])
			{
				case 1: // Static - BANNER_TYPE|BANNER_IMAGE
					$banner_data['static_image'] = (array_key_exists($page_banner_string[1],self::$banners_images)) ? self::$banners_images[$page_banner_string[1]]: NULL;
					$banner_data['link_to_page'] = isset($page_banner_string[2]) ? $page_banner_string[2] : NULL;
					break;

				case 2: // Dynamic - BANNER_TYPE|BANNER_SEQUENCE|BANNER_ORDER|BANNER_FIRST_IMAGE
					$banner_data['banner_sequence']        = $page_banner_string[1];
					$banner_data['banner_display_order']   = $page_banner_string[2];
					$banner_data['banner_first_image']     = $page_banner_string[3];
					$banner_data['banner_sequence_images'] = self::$banners_sequences_images[$banner_data['banner_sequence']];
					$banner_data['link_to_page']           = isset($page_banner_string[4]) ? $page_banner_string[4] : NULL;
					break;

				case 3: // Custom
					// Page Banner String for Banner of type: Custom Scroller is in the form: "banner_type|plugin_item_id|plugin_media_folder|custom_sequence_id"
					$banner_data['plugin_item_id'] = isset($page_banner_string[1]) ? $page_banner_string[1] : NULL;
					$banner_data['plugin_name']    = isset($page_banner_string[2]) ? $page_banner_string[2] : NULL;
					$banner_data['sequence_id']    = isset($page_banner_string[3]) ? $page_banner_string[3] : NULL;

                    $banner_data['publish']        = DB::select('publish')->from(self::$scroller_sequence_table)
                        ->where('id', '=', $banner_data['sequence_id'])->execute()->get('publish');

                    if (class_exists('Model_Customscroller'))
                    {
                        $cs_model                      = new Model_Customscroller();
                        // Get the Corresponding View from the Model_Customscroller for the back_end
                        $banner_data['banner_sequence_editor_view'] = ($back_end == TRUE) ?
                            $cs_model->get_custom_sequence_editor_view ('banners', $banner_data['plugin_item_id'], $banner_data['sequence_id']) : '';
                    }
					break;

				case 4: // Google Map Banner - Banner_type|map_id
                    $banner_data['map_id'] = $page_banner_string[1];
                    $banner_data['map_data'] = DB::select()
                        ->from('plugin_pages_maps')
                        ->where('id', '=', $banner_data['map_id'])
                        ->and_where('deleted', '=', 0)
                        ->execute();
                    $banner_data['map_data'] = $banner_data['map_data'][0];
                    break;

				default: // None
					// Leave it as it is
					break;
			}
		}

		// return banner-data
		return $banner_data;
	}


	public static function get_banner_types_as_options ($selected_type = 0)
	{
		//@TODO: set_banner_lists_data() might NOT be Required in this Function
		//Set the Lists with all related Banner Data
		self::set_banner_lists_data ();
		$banner_type_options = '';

        $customscroller_enabled = Model_Plugin::get_isplugin_enabled_foruser(2, 'customscroller');


		foreach (self::$banner_types as $key => $banner_type)
		{
			//@TODO Add the the excluded Banner Types when unavailable. Currently Exclude Banner-types: GoogleMaps: 4 at the moment
			if ($key < 5)
            {
                if ($banner_type != 'Custom Sequence' OR $customscroller_enabled)
                {
                    $banner_type_options .= '<option value="'.$key.'"'.(($selected_type == $key) ? ' selected="selected"' : '').'>'.$banner_type.'</option>';
                }
            }
		}

		return $banner_type_options;
	}

    public static function get_maps($id = NULL, $published_only = TRUE)
    {
        $query = DB::select()->from('plugin_pages_maps')->where('deleted', '=', 0);

        if ($published_only)
        {
            $query = $query->and_where('publish', '=', 1);
        }
        if (!is_null($id))
        {
            $query = $query->and_where('id', '=', $id);
        }
        $query = $query->execute();

        if (!is_null($id))
            return $query[0];
        else
            return $query;
    }

    public static function get_maps_as_options($selected_id = NULL)
    {
        $maps = self::get_maps(NULL, FALSE);
        $options ='<option value="new">New Map</option>';
        foreach ($maps as $map)
        {
            if ($map['id'] == $selected_id)
            {
                $selected = ' selected="selected"';
            }
            else
            {
                $selected = '';
            }
            $options .= '<option value="'.$map['id'].'"'.$selected.'>'.$map['name'].'</option>';
        }
        return $options;
    }


	//Function Used to get all available unique Banner Sequences ($BannerSequences) as drop-down options
	public static function get_banner_display_order_as_options ($selected_order = '')
	{
		//@TODO: set_banner_lists_data() might NOT be Required in this Function
		//Set the Lists with all related Banner Data
		self::set_banner_lists_data ();
		$banner_sequences_options = '';

		foreach (self::$banner_display_order as $key => $banner_disp_order)
		{
			//$key is holding the corresponding order key: asc -> Ascending, desc -> Descending and rand -> Random
			$banner_sequences_options .= '<option value="'.$key.'"'.(($selected_order == $key) ? ' selected="selected"' : '').'>'.$banner_disp_order.'</option>';
		}

		return $banner_sequences_options;
	}


	//Function Used to get all available unique Banner Images ($StaticImages) as drop-down options
	public static function get_banner_images_as_options ($selected_image = '')
	{
		//Set the Lists with all related Banner Data
		self::set_banner_lists_data ();
		$banner_images_options = '';

		foreach (self::$banners_images as $key => $banner_image)
		{

			$banner_images_options .= '<option value="'.$banner_image['filename'].'" '.' data-thumb="'.Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,
						$banner_image['filename'], $banner_image['location'].DIRECTORY_SEPARATOR.'_thumbs_cms'
					).'"'.(($selected_image == $banner_image['filename']) ? ' selected="selected"' : '').'> '.$banner_image['filename'].'</option>';
		}

		return $banner_images_options;
	}


	//Function Used to get all available unique Banner Sequences ($BannerSequences) as drop-down options
	public static function get_banner_sequences_as_options ($selected_sequence = '')
	{
		//Set the Lists with all related Banner Data
		self::set_banner_lists_data ();
		$banner_sequences_options = '';

		foreach (self::$banner_sequences as $sequence_key => $banner_sequence)
		{
			$banner_sequences_options .= '<option value="'.$sequence_key.'"'.(($selected_sequence == $sequence_key) ? ' selected="selected"' : '').'>'.$sequence_key.'</option>';
		}

		return $banner_sequences_options;
	}


	public static function get_banner_sequence_images_as_options ($sequence_to_list = '', $selected_sequence_image = '')
	{
		//Set the Lists with all related Banner Data
		self::set_banner_lists_data ();
		$sequence_images_options = '';

		if (!empty($sequence_to_list) AND !empty(self::$banners_sequences_images[$sequence_to_list]))
		{
			foreach (self::$banners_sequences_images[$sequence_to_list] as $sequence_image)
			{
				$sequence_images_options .= '<option value="'.$sequence_image['filename'].'" '.' data-thumb="'.Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,
							$sequence_image['filename'], $sequence_image['location'].DIRECTORY_SEPARATOR.'_thumbs_cms'
						).'"'.(($selected_sequence_image == $sequence_image['filename']) ? ' selected="selected"' : '').'> '.$sequence_image['filename'].'</option>';
			}
		}

		return $sequence_images_options;
	}


	/**
	 * Function used to list a Banner Image, or a List with images within a Banner Sequence, in an HTML specified format.
	 *
	 * @param string $banner_sequence_or_image - String holding the Banner Image to be "previewed" or the Banner Sequence, for which, a Banner Images are to be listed in a preview.<br />
	 *                                             <em>NOTE:</em> when a Banner Sequence is to be used, the <em>$preview_type</em>: <strong>sequence_list</strong> MUST be used.
	 * @param string $preview_type             - String Holding the <em>PREVIEW TYPE</em> which is to be used.<br />
	 *                                         Possible Values: <br />
	 *                                 - <strong>static_image</strong> = for a preview of just One Banner Image.<br />
	 *                                 - <strong>sequence_list</strong> = for a preview of a set of Banner Images, grouped in the specified Banner Sequence.<br />
	 *                                 <em>Default Value:</em> <strong>static_image</strong>
	 *
	 * @return string String holding the HTML - Banner Image preview for the specified  of the <em>$banner_sequence_or_image</em>, in the format:<br />
	 *                  <strong>IMAGE_NAME</strong><br />
	 *                  &lt;img src="PATH_TO_IMAGE" width="200" alt="IMAGE_NAME"/&gt;<br />
	 *                  <em>NOTE:</em> in the case when: <strong>$preview_type</strong> is: <strong><em>sequence_list</em></strong>, the same HTML structure,
	 *                  will be used for each of the Images, which belong to the specified Banner Sequence <em>$banner_sequence_or_image</em>.
	 */
	public static function get_banner_preview ($banner_sequence_or_image, $preview_type = 'static_image')
	{

		$banner_preview = '';

		switch ($preview_type)
		{
			case 'sequence_list':
				self::set_banner_lists_data ();
				if (isset(self::$banners_sequences_images[$banner_sequence_or_image]))
				{
					if (!empty(self::$banners_sequences_images[$banner_sequence_or_image]))
					{
						foreach (self::$banners_sequences_images[$banner_sequence_or_image] as $banner_seq_image)
						{
							$banner_preview .= '<strong>'.$banner_sequence_or_image.'</strong><br />
										<img
											src="'.Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$banner_seq_image['filename'], 'banners'.DIRECTORY_SEPARATOR.'_thumbs_cms').'"'.' width="200"'.' alt="'.$banner_seq_image['filename'].'"/><br /><br />';
						}
					}
					else $banner_preview = '<p>There is no available images for the specified Banner Sequence: '.$banner_sequence_or_image.'</p>';
				}
				else $banner_preview = '<p>There specified Banner Sequence: '.$banner_sequence_or_image.' is not valid</p>';
				break;
			case 'static_image':
			default:
				$banner_preview = '<strong>'.$banner_sequence_or_image.'</strong><br />
						<img
							src="'.Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$banner_sequence_or_image, 'banners'.DIRECTORY_SEPARATOR.'_thumbs_cms').'"'.' width="200"'.' alt="'.$banner_sequence_or_image.'"/><br /><br />';
				break;
		}

		return $banner_preview;
	}


	public static function set_page_banner_photo ($page_data)
	{
		//IbHelpers::die_r($page_data);

		$page_banner_string = '';

		/*
		 * Prepare the Page-Banner String, based on the specified Banner-Type
		 * The String is in the Form:
		 *
		 * Static Banner: BANNER_TYPE|BANNER_IMAGE
		 * Dynamic Banner: BANNER_TYPE|PLUGIN_ITEM_ID|PLUGIN_MEDIA_FOLDER|CUSTOM_SEQUENCE_ID
		 */
		switch ($page_data['banner_type'])
		{
            case 0: // None
                break;

            case 1: // Static
				$page_banner_string = $page_data['banner_type'].'|'.$page_data['banner_static_img'].'|'.$page_data['link_to_page'];
				break;

            case 2: // Dynamic
				$page_banner_string = $page_data['banner_type'].'|'.$page_data['banner_sequence'].'|'.$page_data['banner_display_order'].'|'.$page_data['banner_first_image'].'|'.$page_data['link_to_page'];
				break;

            case 3: // Custom
				// Default to the /banners Media Folder set up in: self::$media_banners_folder
				if (!isset($page_data['sequence_data']))
				{
					$banners_media_folder_structure = explode('/', self::$media_banners_folder);
					$custom_sequence_medial_folder = end($banners_media_folder_structure);
				}
				// Correct Media folder for This Custom Sequence Images is passed:
				if (isset($page_data['sequence_data']) AND (isset($page_data['sequence_data']['sequence_holder_plugin']) AND trim($page_data['sequence_data']['sequence_holder_plugin']) != ''))
				{
					$custom_sequence_medial_folder = $page_data['sequence_data']['sequence_holder_plugin'];
				}
				// Page Banner String for Banner of type: Custom Scroller is in the form: "banner_type|plugin_item_id|plugin_media_folder|custom_sequence_id
				$page_banner_string = $page_data['banner_type'].'|'
									  .$page_data['pages_id'].'|'
									  .$custom_sequence_medial_folder
									  .((isset($page_data['plugin_item_sequence_id']))? '|'.$page_data['plugin_item_sequence_id'] : '');
				unset($banners_media_folder_structure);
				break;

            case 4: // Google Maps
                if (isset($page_data['google_map_id']))
                {
                    $page_banner_string = $page_data['banner_type'].'|'.$page_data['google_map_id'];
                }

                break;

            default:
				$page_banner_string = NULL;
				break;
		}

		return $page_banner_string;
	}

	private static function set_banner_lists_data()
	{

		/* 1. Get All Images in the Media/Banners/ Folder */
		/*
		 * NOTE: Kohana::list_files() returns an array in the following Structure:
		 * 		 array(
		 *			'RELATIVE_PATH_TO_FILE' => 'FULL_PATH_TO_FILE'
		 * 		 )
		 * 		Example: array(
		 * 					'www/media/banners/banner_image.jpg' => '/WEB_ROOT/APP_ROOT/projects/PROJECT_WEBSITE/www/media/banner_image.jpg'
		 * 				 )
		 */
//		$banner_image_files = Kohana::list_files(self::$media_banners_folder);

		$media_mgr          = new Model_Media();
		$banner_image_files = $media_mgr->get_all_items_based_on (
			'location', 'banners', 'details', '=', NULL
		);

		/* 2. Process each image in the media/photos/banners/ folder and update the corresponding Banners Lists */
		foreach ($banner_image_files as $banner_image)
		{

			//Get the Banner-image name
//			$path_parts = explode(DIRECTORY_SEPARATOR, $banner_image);
//			$banner_image_name = end($path_parts);
			$banner_image_name     = $banner_image['filename'];
			$banner_img_file_parts = explode ('.', $banner_image_name);

			//Take only JPEGs at the moment
			$accepted_formats = array('jpg', 'jpeg', 'png');
			if (in_array(strtolower(end($banner_img_file_parts)), $accepted_formats))
			{

				//2.1 Add the Image to the Banner-images List
				self::$banners_images[$banner_image_name] = $banner_image;

				//get the NUMBER in the banner-image name if there is such
				$number_in_name = preg_replace('/[^0-9]/', '', $banner_img_file_parts[0]);
				// If the entire filename is a number that doesn't count
				$number_in_name = ($number_in_name == $banner_img_file_parts[0]) ? '' : $number_in_name;
				/*
				 * get the banner-image up to, but without the number PART, i.e. core_name - Will be used to define the corresponding Banner Sequence
				 * Example of an image name could be:
				 * 	-	example_image_01 		- $core_img_name -=> example_image
				 *  -	example_image_02 		- $core_img_name -=> example_image
				 *  -	example_image_01_edited - $core_img_name -=> example_image
				 * @NOTE: the previous CODE (bellow) was returning: example_image__edited for example_image_01_edited, which is completely WRONG
				 * 		  $core_img_name = str_replace($number_in_name, "", $banner_img_file_parts[0]);
				 */
				$core_img_name_parts = (trim($number_in_name) != '')? preg_split('@'.$number_in_name.'@', $banner_img_file_parts[0], NULL, PREG_SPLIT_NO_EMPTY) : $banner_img_file_parts[0];
				$core_img_name       = (is_array($core_img_name_parts))? $core_img_name_parts[0] : $core_img_name_parts;


				//Set up the Banner: Sequences and some other stuff based on the available Banner-images
				if (substr ($core_img_name, 0, 1) != '_')
				{
					// Taking a note which sequence would be the first in the list in case none is set
					//if (!isset($FirstSequence)) $FirstSequence = $core_img_name;

					// 2.2 Preparing a list of possible Banner-Sequences based on the core of the files
					if (!in_array ($core_img_name, self::$banner_sequences)) self::$banner_sequences[$core_img_name] = $banner_image;

					// 2.3 Preparing an image-list for the Banner-Sequence Set if any
					if (strpos ($banner_image['filename'], $core_img_name, 0) >= 0)
					{
						self::$banners_sequences_images[$core_img_name][$banner_image['filename']] = $banner_image;
					}

					// Preparing an image-list for the first selectable (default) sequence
//					if ($FirstSequence == $core_img_name) $FirstImages[$banner_image_name] = $banner_image_name;

					// Preparing an image-list for the Static option
//					$data['StaticImages'][$banner_image_name] = $banner_image_name;
				}
			}
		}
		//end foreach

//		if (count($data['StaticImages']) == 0) $data['StaticImages'][''] = 'None';
//
//		if ($data['Page']->staticImage == '') $data['Page']->staticImage = current($data['StaticImages']);
//		if (!isset($data['BannerImages'])) $data['BannerImages'] = $FirstImages;
	}


	/**
	 * @param $banners the banner array, from get_banner_data()
	 *
	 * @return Array Return the $banners ordered
	 */
	private static function order_array_elements_by_display_order ($banners)
	{
		//If order by is random
		if ($banners['banner_display_order'] == 'rand')
		{
			shuffle ($banners['banner_sequence_images']);
			$array_size = count ($banners['banner_sequence_images']);
			for ($i = 0; $i < $array_size; $i++)
			{
				if ($banners['banner_sequence_images'][0]['filename'] == $banners['banner_first_image'])
				{
					return $banners;
				}
				else
				{ //Move the first element to the end of the array
					array_push ($banners['banner_sequence_images'], array_shift ($banners['banner_sequence_images']));
				}
			}
		}
		//ASC order
		elseif ($banners['banner_display_order'] == 'asc')
		{
			$array_size = count ($banners['banner_sequence_images']);
			$array_keys = array_keys ($banners['banner_sequence_images']);
			for ($i = 0; $i < $array_size; $i++)
			{
				if ($banners['banner_sequence_images'][$array_keys[$i]]['filename'] == $banners['banner_first_image'])
				{
					return $banners;
				}
				else
				{ //Move the first element to the end of the array
					array_push ($banners['banner_sequence_images'], array_shift ($banners['banner_sequence_images']));
					//$array_keys = array_keys($banners['banner_sequence_images']);
				}
			}

			return $banners;
		}
		else
		{ //DESC order
			rsort ($banners['banner_sequence_images']);
			$array_size = count ($banners['banner_sequence_images']);
			$array_keys = array_keys ($banners['banner_sequence_images']);
			for ($i = 0; $i < $array_size; $i++)
			{
				if ($banners['banner_sequence_images'][0]['filename'] == $banners['banner_first_image'])
				{
					return $banners;
				}
				else
				{ //Move the first element to the end of the array
					array_push ($banners['banner_sequence_images'], array_shift ($banners['banner_sequence_images']));
				}
			}

			return $banners;
		}
	}

    public static function generate_maps_banner_html($page_map_data)
    {
        $map_data = explode('|', $page_map_data);
        $html = DB::select('html')
            ->from('plugin_pages_maps')
            ->where('id', '=', $map_data[1])
            ->and_where('publish', '=', 1)
            ->and_where('deleted', '=', 0)
            ->execute()
            ->get('html', 1);

        return $html;
    }

	private static function generate_static_banners_html ($banners)
	{
		$link_to_page = NULL;

		if ($banners['link_to_page'] !== NULL)
		{
			$page_model = new Model_Pages();
			$page_data  = $page_model->get_page_data ($banners['link_to_page']);

			if (count ($page_data) > 0)
			{
				$link_to_page = $page_data[0]['name_tag'];
			}
		}

		$data['count']  = 0;
		$data['first']  = 'first_banner';
		$data['last']   = '';
		$data['banner'] = $banners['static_image'];
		$data['url']    = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$banners['static_image']['filename'], $banners['static_image']['location']);

		$dimensions = explode ('x', $banners['static_image']['dimensions']);
		$width      = isset($dimensions[0])? $dimensions[0] : 0;
		$height     = isset($dimensions[1])? $dimensions[1] : 0;

		$data['width']  = $width;
		$data['height'] = $height;
        $data['link_to_page'] = $link_to_page;

		$html_items = Kohana_View::factory ('front_end/banner_item_details_html', $data);

		$feed_items['feed_items']   = $html_items;
		$feed_items['banner_type']  = $banners['banner_type'];
		$feed_items['link_to_page'] = $link_to_page;

		$html = Kohana_View::factory ('front_end/banner_feed_view', $feed_items);

		return $html;
	}

	private static function generate_banners_slider_html ($banners)
	{

		$banners_items = $banners['banner_sequence_images'];
		$amount        = count ($banners_items);
		$html_items    = '';
		$count         = 0;
		$link_to_page  = NULL;

		if ($banners['link_to_page'] !== NULL)
		{
			$page_model = new Model_Pages();
			$page_data  = $page_model->get_page_data ($banners['link_to_page']);

			if (count ($page_data) > 0)
			{
				$link_to_page = $page_data[0]['name_tag'];
			}
		}

		foreach ($banners_items as $key => $banner)
		{

			//The first one
			if ($count == 0)
			{
				$first = ' first_banner';
			}
			else
			{
				$first = '';
			}

			//Last
			if ($key == ($amount - 1))
			{
				$last = ' last_banner';
			}
			else
			{
				$last = '';
			}

			$dimensions = explode ('x', $banner['dimensions']);
			$width      = isset($dimensions[0])? $dimensions[0] : 0;
			$height     = isset($dimensions[1])? $dimensions[1] : 0;

			//Li elements
			$data['url']          = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,$banner['filename'], $banner['location']);
			$data['count']        = $count;
			$data['first']        = $first;
			$data['last']         = $last;
			$data['banner']       = $banner;
			$data['width']        = $width;
			$data['height']       = $height;
			$data['link_to_page'] = $link_to_page;

			$html_items .= Kohana_View::factory ('front_end/banner_item_details_html', $data);

			$count++;
		}
		$feed_items['feed_items']   = $html_items;
		$feed_items['banner_type']  = $banners['banner_type'];
		$feed_items['link_to_page'] = $link_to_page;

		$html = Kohana_View::factory ('front_end/banner_feed_view', $feed_items);

		return $html;
	}



	/*****  FRONT END  *******/

	public static function render_frontend_banners ($banner_photo, $stylesheet = TRUE)
	{
		$banners = self::get_banner_data ($banner_photo, FALSE);

        if (isset($banners['publish']) AND $banners['publish'] == 0)
            return '';
        else
        {
            switch ($banners['banner_type'])
            {
                case "4": // Google Map
                    $html = self::generate_maps_banner_html($banner_photo);
                    break;

                case "3": // Custom Scroller
                    /* CUSTOM SCROLLER DATA
                    $banner_data['banner_type']    = $page_banner_string[0];
                    WE NEED bellow FIELD to RENDER the Binder of TYPE: Custom Scroller
                    $banners['plugin_item_id'] = $page_banner_string[1];
                    $banners['plugin_name']    = $page_banner_string[2];
                    $banners['sequence_id']    = $page_banner_string[3];
                    $banners[''banner_sequence_editor_view']  = IS NOT AVAILABLE FOR THE FRONT-END
                    */
                    // Get an Instance of the Custom Scroller Model
                    $cs_model = new Model_Customscroller();
                    $html = $cs_model->render_front_end_custom_sequence($banners['plugin_name'], $banners['plugin_item_id'], $banners['sequence_id'], $stylesheet);

                    break;

                case "2": // Dynamic Banner
                    $banners = self::order_array_elements_by_display_order ($banners);
                    $html    = self::generate_banners_slider_html ($banners);
                    break;

                case "1": // Static Banner
                    $html = self::generate_static_banners_html ($banners);
                    break;

                default: // NO Binder
                    $html = '';
            }

            return $html;

        }
	}

    public static function render($page_name = null)
    {
        if (empty($page_name) ) {
            $url = trim($_SERVER['REQUEST_URI'], '/');
            $page_name = substr($url, strrpos($url, '/') + 1);
        }

        $page = Model_Pages::get_page($page_name.'.html', true);
        $image_path = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,'', 'banners');

        if (!empty($page[0])) {
            return View::factory('front_end/page_banner')
                ->set('image_path', $image_path)
                ->set('page_data', $page[0]);
        } else {
            return '';
        }
    }


}//end of class