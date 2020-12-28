<?php
/**
 * Created by JetBrains PhpStorm.
 * User: kosta
 * Date: 06/01/2013
 * Time: 10:54
 * To change this template use File | Settings | File Templates.
 */

Class Model_Media extends Model
{
	const MEDIA_RELATIVE_PATH = 'media';
    const TABLE_MEDIA = 'plugin_media_shared_media';
	const SYNC_TABLE = 'plugin_media_external_sync';

	/* Back-End (CMS) : Admin Functions  */

	//@TODO: Add Docs Comments for each Main Function when the Plugin Model is Ready!!!!!
	/*
	 * @TODO: Model's Private/Public  Variables go here
	 */
	private static $model_items_table = 'plugin_media_shared_media';
	private static $default_image_folder = 'content';

	/**
	 * @var array - Array Holding the <strong>DEFAULT</strong> Structure of the Project Specific - Media Folder Structure.<br />
	 *        <em>Default Structure</em>  of the Media Folder is presented bellow:<br />
	 * <pre>
	 *    <strong>media/</strong>
	 *      <strong>docs/</strong>  <em>=> Used to store all Text Documents: txt., .doc, .pdf etc.</em>
	 *      <strong>photos/</strong> <em>=> Used to store all images displayed on the Front-End of this Website</em>
	 *        <strong>banners/</strong>  <em>=> Used for the Pages-Plugin: Banner images</em>
	 *           <strong>_thumbs/</strong> <em>=> stores the corresponding image - System (Admin) thumbnails</em>
	 *           <strong>_thumbs_cms/</strong> <em>=> stores the corresponding image - Front-End thumbnails</em>
	 *        <strong>content/</strong> <em>=> Used mainly for the Pages-Plugin, but can be accessed through the tinyBrowser on any other Plugin: item content editor</em>
	 *           <strong>_thumbs/</strong>
	 *           <strong>_thumbs_cms/</strong>
	 *       <em>... and other plugins specific folders, required for the Plugin Specific Presets for Image Uploads</em>
	 *      <strong>videos/</strong> <em>=> Used to store all Videos</em>
	 * </pre>
	 */
	private $default_media_structure = array(
		'media' => array(
			'docs' => array(),
			'photos' => array(
				'banners' => array(
					'_thumbs' => array(),
					'_thumbs_cms' => array()
				),
				'content' => array(
					'_thumbs' => array(),
					'_thumbs_cms' => array()
				)
			),
			'audios' => array(),
			'videos' => array(),
			'fonts' => array()
		)
	);
	/**
	 * @var array Array Holding the <strong>CURRENT</strong> or <strong>ACTUAL</strong> Media Folder Structure of the Current Project (Web App).<br />
	 *              Will be Set in the <strong>CONTROLLER</strong> of This Model.<br />
	 *              Will have Structure similar to: $default_media_structure, and will include all ADDITIONAL Plugins Related Photos Folders installed.<br />
	 *              <em>EXAMPLE:</em>
	 * <pre>
	 *    <strong>media/</strong>
	 *      <strong>docs/</strong>  <em>=> Used to store all Text Documents: txt., .doc, .pdf etc.</em>
	 *      <strong>photos/</strong> <em>=> Used to store all images displayed on the Front-End of this Website</em>
	 *        <strong>banners/</strong>  <em>=> Used for the Pages-Plugin: Banner images</em>
	 *           <strong>_thumbs/</strong> <em>=> stores the corresponding image - System (Admin) thumbnails</em>
	 *           <strong>_thumbs_cms/</strong> <em>=> stores the corresponding image - Front-End thumbnails</em>
	 *        <strong>content/</strong> <em>=> Used mainly for the Pages-Plugin, but can be accessed through the tinyBrowser on any other Plugin: item content editor</em>
	 *           <strong>_thumbs/</strong>
	 *           <strong>_thumbs_cms/</strong>
	 *      <strong>PLUGIN_1/</strong> <em>=> Plugin 1 specific Photos sub-folder</em>
	 *           <strong>_thumbs/</strong>
	 *           <strong>_thumbs_cms/</strong>
	 *      <strong>PLUGIN_2/</strong> <em>=> Plugin 2 specific Photos sub-folder</em>
	 *           <strong>_thumbs/</strong>
	 *           <strong>_thumbs_cms/</strong>
	 *       <em>... and other plugins specific folders, required for the Plugin Specific Presets for Image Uploads</em>
	 *      <strong>videos/</strong> <em>=> Used to store all Videos</em>
	 * </pre>
	 *
	 */
	private $current_media_structure = array();
	/**
	 * @var string - String holding the absolute path for the Media Folder of the current Project (Web Application).<br />
	 *                 Will be set up in the constructor of this Model. and will look like:<br />
	 *                 'www'.DIRECTORY_SEPARATOR.'media';<br />
	 */
	private $media_relative_path = '';

	/**
	 * @var string - String holding the absolute path for the Media Folder of the current Project (Web Application).<br />
	 *                    Will be set up in the constructor of this Model. and will look like:<br />
	 *                    ROOTPATH.'projects'.DIRECTORY_SEPARATOR.
	 *                    PROJECTNAME.DIRECTORY_SEPARATOR.
	 *                        'www'.DIRECTORY_SEPARATOR.'media';
	 *                    OR if shared media folder it will look like
	 *                    ROOTPATH/SHARED_MEDIA/PROJECT/MEDIA
	 */
	private $media_absolute_path = '';


	/**
	 * @var array - Array Holding all Types of Files which can be uploaded by this Media Plugin.<br />
	 *                In order to <strong>enable</strong> / <strong>disable</strong> the Uploads of some Files, just comment the Type to be disabled.<br />
	 *                <em>For Example:</em> check - Audio File Types<br />
	 *                <em>Structure:</strong></em><br />
	 * <pre>
	 * <strong>Array(</strong>
	 *      <strong>image/jpeg</strong>, <em>=> jpg image</em>
	 *      <strong>image/png</strong>,  <em>=>png image</em>
	 *    <strong>image/gif</strong>,  <em>=>gif image</em>
	 *      <strong>text/plain</strong>,  <em>=>txt files</em>
	 *      <strong>application/msword</strong>,  <em>=>doc files</em>
	 *      <strong>application/pdf</strong>,  <em>=>pdf files</em>
	 *      <strong>audio/mpeg</strong>, <em>=>m4a, mp2 or mp3 audio</em>
	 *      <strong>audio/x-ms-wma</strong> <em>=>Wma audio</em>
	 *      <strong>audio/midi</strong> <em>=>mid or midi audio</em>
	 *      <strong>audio/mp4</strong> <em>=>mp4 audio</em>
	 *      <strong>video/mp4</strong> <em>=>mp4 video</em>
	 *      <strong>video/x-xvid</strong> <em>=>x-vid video</em>
	 *      <strong>video/x-ms-wmv</strong> <em>=>wmv video</em>
	 *      <strong>video/x-ms-wm</strong> <em>=>wm video</em>
	 *      <strong>video/mpeg</strong> <em>=>mpeg video</em>
	 *      <strong>video/x-flv</strong> <em>=>flv video</em>
	 *      <em>... and other File-Types to be allowed for uploading to the Media Plugin.</em>
	 * <strong>)</strong>
	 * <em>NOTE: the commented File-Types are currently Disabled.</em>
	 * </pre>
	 *
	 */
	private $allowed_media_types = array(
		'image/jpeg',
		'image/png',
		'image/gif',
		'image/pdf',
		'image/vnd.microsoft.icon', // favicon
		'image/x-icon',
		'text/plain',
		'text/csv',
        'text/x-comma-separated-values',
		'application/msword',
		'application/pdf',
		'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
		'application/vnd.openxmlformats-officedocument.presentationml.presentation',
		'application/vnd.ms-powerpoint',
		'application/octet-stream',
		'audio/mpeg',
		'audio/x-ms-wma',
		'audio/midi',
		'audio/mp3',
		'audio/mp4',
		'video/mp4',
		'video/x-xvid',
		'video/x-ms-wmv',
		'video/x-ms-wm',
		'video/mpeg',
		'video/x-flv'
	);

	public static $allowed_extensions = array(
		'png',
		'jpg',
		'jpeg',
		'doc',
		'docx',
        'xls',
        'xlsx',
        'odt',
        'ods',
		'pdf',
		'csv',
		'ppt',
		'pptx',
		'mpg',
		'mp3',
		'mp4',
		'mpeg',
		'wmv',
		'flv',
		'ttf',
		'woff'
	);
	/**
	 * @var array Array Holding the Media Folders, which DO NOT Require THUMBS Sub-folders, i.e. (_thumbs and _thumbs_cms)
	 */
	private $folders_with_no_thumbs = array(
		'docs',
		'audios',
		'videos',
		'fonts'
	);
	/**
	 * @var int - Integer Holding the Allowed Size of the files to be uploaded to Media.<br />
	 *              <em>Value:</em> 1024 * 1024 * 10 = 10485760b = <strong>10Mb</strong>.
	 */
	private $allowed_file_size = 10485760;
	/**
	 * @var int - Integer Holding the Allowed Length of the File Name to be uploaded to Media.<br />
	 *              <em>Note:</em> this is the available Length of the File Name supported by the corresponding Database Table Field.<br />
	 *                             If changed, this change <strong>MUST</strong> be UPDATED to the corresponding Database Field.<br />
	 *              <em>Value:</em> <strong>200</strong> characters, including the file extension.
	 */
	private $allowed_length_file_name = 200;

	/* START OF setters and getters */
	/**
	 * @param string $media_relative_path
	 */
	public static function setMediaRelativePath($media_relative_path)
	{
		self::$media_relative_path = $media_relative_path;
	}

    public static function get($id)
    {
        $q = DB::select()
            ->from('plugin_media_shared_media')
            ->where('filename', '=', $id)
            ->execute()
            ->as_array();

        if (isset($q[0]))
        {
            // If a record is found, return it
            $return = $q[0];
        }
        else
        {
            // If no records are found, return an array of empty values for each column
            $return = array();
            $columns = Database::instance()->list_columns('plugin_media_shared_media');
            foreach ($columns as $column => $data) {
                $return[$column] = '';
            }
        }

        return $return;
    }

	public static function get_by_id($id)
	{
		$media = DB::select()
				->from('plugin_media_shared_media')
				->where('id', '=', $id)
				->execute()
				->current();
		return $media;
	}

	public static function get_by_filename($filename, $directory)
	{
		$filename = urldecode($filename);
		$directory = urldecode($directory);
		$q = DB::select(
            '*',
            [DB::expr("SUBSTRING_INDEX(`dimensions`, 'x',  1)"), 'width'],
            [DB::expr("SUBSTRING_INDEX(`dimensions`, 'x', -1)"), 'height']
        )->from('plugin_media_shared_media')
			->where('filename', '=', $filename)
			->and_where('location', '=', $directory)
			->execute()
			->as_array();

		return (count($q) > 0) ? $q[0] : NULL;
	}

	/**
	 * @return string
	 */
	public static function getMediaRelativePath()
	{
		return self::$media_relative_path;
	}

	public static function get_image_filename($id)
	{
		$q = DB::select('filename', 'location')->from('plugin_media_shared_media')->where('id', '=', $id)->execute()->as_array();
		return count($q) > 0 ? $q[0] : array();
	}


	/*END OF setters and getters*/

	//Constructor
	public function __construct()
	{
		//Commented out as throws an error - Cannot Call Constructor on the Parent model
		//parent::__construct();

		//1. Set up the Relative and Absolute Paths for the Media Folder for the Current Project (Web App), using the corresponding for this Project constants.
		//1b. Check for shared media is active for this project.
		if (Kohana::$config->load('config')->project_media_folder != '') //shared media exists for this project
		{
			$this->media_relative_path = 'shared_media'.DIRECTORY_SEPARATOR.Kohana::$config->load('config')->project_media_folder.DIRECTORY_SEPARATOR; //'www'.DIRECTORY_SEPARATOR.'shared_media'.DIRECTORY_SEPARATOR.PROJECT.DIRECTORY_SEPARATOR.'media';<br />
			$this->media_absolute_path = ENGINEPATH.'www'.DIRECTORY_SEPARATOR.'shared_media'.DIRECTORY_SEPARATOR.Kohana::$config->load('config')->project_media_folder.DIRECTORY_SEPARATOR;
		}
		else //else use standard media directory
		{
            // Deprecated. This should not be reached.
            $this->media_relative_path = 'shared_media'.DIRECTORY_SEPARATOR.PROJECTNAME.DIRECTORY_SEPARATOR;
			$this->media_absolute_path = ENGINEPATH.'www'.DIRECTORY_SEPARATOR.'shared_media'.DIRECTORY_SEPARATOR.PROJECTNAME.DIRECTORY_SEPARATOR;
		}

		//3. Get the Current Media -  Filesystem (Folder Structure), for the Current Project (Web App)
		$this->current_media_structure = $this->get_current_media_structure($this->media_absolute_path);

		//2. Check if Media Folders are set for this Project (Web App), and if NOT, CREATE/UPDATE, the Media folder Structure
		if (!is_dir($this->media_absolute_path))
		{
			//Rebuild the Basic Media Filesystem for this Project (Web App)
			$media_filesystem_created = $this->build_media_folder($this->default_media_structure, $this->media_absolute_path);

			//There was a Problem with the Creation of the Media Filesystem
			if (!$media_filesystem_created)
			{
				IbHelpers::set_message('System: There was a PROBLEM with Creation of Media Filesystem: '.$this->media_absolute_path.' for Project: "'.PROJECTNAME.'"!', 'error');
				//Media Filesystem Created SUCCESS
			}
			else
			{
				IbHelpers::set_message('System: Project: "'.PROJECTNAME.'" Media Filesystem: '.$this->media_absolute_path.' Created SUCCESS!', 'success');
			}
		}
		//else Media Filesystem exists in this Website
		//clear the file status cache - after using is_dir()
		clearstatcache();


	}

	//end of constructor


	// Retrieve all Media Items or the One - Specified by the $item_id, and are available to this Admin User
	public function get_all_items_admin($item_id = NULL)
	{
//		IbHelpers::die_r("\nModel_Media->get_all_items_admin($item_id) \n");

		//Reads items from the MEDIA DB Table
		if (is_null($item_id))
		{
			return DB::select()
				->from(self::$model_items_table)
				->execute()
				->as_array();
		}
		else
		{
			return DB::select()
				->from(self::$model_items_table)
				->where('id', '=', $item_id)
				->execute()
				->as_array();
		}

	}

	//end of function

	public static function get_presets_like($like)
	{
		return DB::select()->from('plugin_media_shared_media_photo_presets')
			->where('title', 'like', $like)->and_where('publish', '=', 1)->and_where('deleted', '=', 0)
			->execute()->as_array();
	}

	public static function get_images_by_preset($preset_id, $term = NULL, $presets_like = '')
	{
		$term = (is_null($term)) ? '' : $term;
		$q = DB::select('plugin_media_shared_media.*')->from('plugin_media_shared_media')->where('filename', 'like', '%'.$term.'%');
		if ($preset_id != '')
		{
			$q->where('preset_id', '=', $preset_id);
		}
		else
		{
			$q->join('plugin_media_shared_media_photo_presets')
				->on('plugin_media_shared_media.preset_id', '=', 'plugin_media_shared_media_photo_presets.id')
				->where('plugin_media_shared_media_photo_presets.title', 'LIKE', '%'.$presets_like.'%');
		}

		return $q->order_by('filename')->execute()->as_array();

	}

	public static function get_all_from_preset($preset, $label = 'title', $term = '')
	{
		return DB::select('media.id', 'media.filename', 'media.dimensions', 'media.location', array('preset.id', 'preset_id'), array('preset.title', 'preset'))
			->from(array('plugin_media_shared_media', 'media'))
			->join(array('plugin_media_shared_media_photo_presets', 'preset'))
			->on('media.preset_id', '=', 'preset.id')
			->where('preset.'.$label, '=', $preset)
			->where('media.filename', 'like', '%'.$term.'%')
			->order_by('preset.title', 'asc')
			->order_by('media.filename', 'asc')
			->execute()->as_array();
	}

	// Retrieve all Media Items or the One - Specified by the $item_id, and are available to this Admin User
	public function get_all_items_based_on($filter_field = '', $field_value = NULL, $return_type = 'details', $where_operator = '=', $selected_item = NULL, $order_by = NULL, $direction = 'asc')
	{
//		IbHelpers::pre_r("Model_Media->get_all_items__based_on() :");
//		IbHelpers::die_r(array('filter_field'=>$filter_field, 'field_value'=>$field_value, 'return_type'=>$return_type, 'where_operator'=>$where_operator, 'selected_item_id'=>$selected_item_id));

		$request_result = NULL;

		$items_list = DB::select(
            'media.*',
            array(DB::expr("SUBSTRING_INDEX(`dimensions`, 'x',  1)"), 'width'),
            array(DB::expr("SUBSTRING_INDEX(`dimensions`, 'x', -1)"), 'height'),
            array('modified_by.email', 'modified_by_email')
        )
			->from(array(self::$model_items_table, 'media'))
			->join(array('engine_users', 'modified_by'), 'left')
			->on('media.modified_by', '=', 'modified_by.id');

		//get all Media Items for the specified filter
		if ($filter_field != '' AND ! is_null($field_value))
		{
			//Get All Media Items, based on the specified Filter
			$items_list->where($filter_field, $where_operator, $field_value);
			//get all available Media Items
		}

		if ( ! is_null($order_by))
		{
			$items_list->order_by($order_by, $direction);
		}

		$items_list = $items_list->execute()->as_array();

		//Check flag and return as drop-down options if specified
		switch ($return_type)
		{
			case 'as_options':
				$request_result = '';
				//Build the drop-down options from the obtained result
				foreach ($items_list as $media_item)
				{
					$request_result .= '<option value="'.$media_item['filename'].'" '.
						' data-id="'.$media_item['id'].'"'.
						' data-thumb="'.
						self::get_path_to_media_item_admin(
							Kohana::$config->load('config')->project_media_folder,
							$media_item['filename'],
							$media_item['location'].DIRECTORY_SEPARATOR.'_thumbs_cms'
						).'"'.
						(($selected_item == $media_item['filename']) ? ' selected="selected"' : '').
						'> '.$media_item['filename'].'</option>';
				}
				break;

			case 'details':

			default:
				$request_result = array();
				foreach ($items_list as $media_item)
				{
					//Return ONLY the $selected_item_id - skip the remaining items
					if (!is_null($selected_item) AND $selected_item == $media_item['filename'])
					{
						$request_result = $media_item;
						//exit the foreach loop, as we found the Item we need
						break;
						//$selected_item_id was not set -=> return all available Items
					}
					else if (is_null($selected_item))
					{
						$request_result[$media_item['id']] = $media_item;
					}
				}
				break;

		}
		//end of switch

		//Return
		return $request_result;
	}

	//end of function

	public function get_presets($args)
	{
		$defaults = array(
			'id' => NULL,
			'published_only' => TRUE,
		);
		$args = array_merge($defaults, $args);

		$presets = DB::select()
			->from('plugin_media_shared_media_photo_presets')
			->where('deleted', '=', 0);

		if (!is_null($args['id']))
		{
			$presets = $presets->where('id', '=', $args['id']);
		}
		if ($args['published_only'])
		{
			$presets = $presets->where('publish', '=', 1);
		}
		$presets = $presets
			->order_by('title')
			->execute()
			->as_array();

		return (!is_null($args['id'])) ? $presets[0] : $presets;
	}


	/**
	 * Function used to validate a File to be uploaded to the Media Database and Filesystem.<br />
	 * The File structure is as follows:<br />
	 * <pre>
	 * Array(
	 *   [name] => FILE_NAME.EXT
	 *   [type] => FILE_MIME_TYPE
	 *   [tmp_name] => LOCATION_OF_THE_TEMPORARY_COPY_OF_THE_UPLOADED_FILE
	 *   [error] => 0 (0 for OK)
	 *   [size] => 47 (FILE_SIZE in BITES)
	 * )
	 * </pre>
	 * Currently the function will validate:<br />
	 * 1. If File was loaded correctly, i.e. File[error] = 0<br />
	 * 2. If the File[size] is within the specified for this Media ALLOWED_FILE_SIZE ($this->allowed_file_size)<br />
	 * 3. If the File[name] is within the specified for this Media ALLOWED_FILE_NAME ($this->allowed_length_file_name)<br />
	 * 4. If the File[type] is one of the allowed by this Media files ($this->allowed_media_types)<br />
	 * In the case when one of the above fails, the Function will set a corresponding ERROR_MESSAGE, using the <em>IbHelpers::set_message()</em>,
	 * and will return FALSE.
	 *
	 * @param $item_data_to_validate
	 * @param $item_preset_data
	 *
	 * @return bool - TRUE if the Passed Item is OK to be uploaded and FALSE otherwise.
	 */
	public function validate_media_item($item_data_to_validate, $item_preset_data, $return_error = FALSE)
	{

		$item_valid = TRUE;
		$errors = array();

		// Check if the file was uploaded correctly
		if ($item_data_to_validate['error'] != UPLOAD_ERR_OK)
		{
            $item_valid = FALSE;

			switch ($item_data_to_validate['error'])
			{
                case UPLOAD_ERR_INI_SIZE:
                    //Show the Filesize in Mbs
                    $errors[] = 'The file: "'.$item_data_to_validate['name'].'" '.
                        'Size: '.number_format(($item_data_to_validate['size'] / (1024 * 1024)), 2, '.', '').'Mb is too big. '.
                        'It exceeds the site\'s size limit. '.
                        'Maximum allowed size is: '.ini_get('upload_max_filesize').'b.';
                    break;

                case UPLOAD_ERR_FORM_SIZE:
                    $errors[] = 'The file: "'.$item_data_to_validate['name'].'" '.
                        'Size: '.number_format(($item_data_to_validate['size'] / (1024 * 1024)), 2, '.', '').'Mb is too big. It exceeds the size limit set by the form.';
                    break;

                case UPLOAD_ERR_PARTIAL:
                    $errors[] = 'The file: "'.$item_data_to_validate['name'].'" was only partially uploaded.';
                    break;

                case UPLOAD_ERR_NO_FILE:
                    $errors[] = 'No file was uploaded.';
                    break;

                case UPLOAD_ERR_NO_TMP_DIR:
                    $errors[] = 'The temporary folder for image uploads is missing.';
                    break;

                case UPLOAD_ERR_CANT_WRITE:
                    $errors[] = 'Failed to write the file to the disk.';
                    break;

                case UPLOAD_ERR_EXTENSION:
                    $errors[] = 'A PHP extension has prevented the file from uploading. An administrator can view the list of loaded extensions in phpinfo.';
                    break;

                default:
                    $errors[] = 'Unexpected error (#'.$item_data_to_validate['error'].') with the file upload. Please ask an administrator to check the PHP file errors manual for this reference number.';
                    break;
			}
		}
		else
		{
			//Validate Item

			//Check if File has been already Uploaded to The Media DB
			if (strpos($item_data_to_validate['type'], 'image/', 0) !== FALSE)
			{

				// Check if the filename is used in the content directory
				if (self::is_filename_used($item_data_to_validate['name'], 'content'))
				{
					$errors[] = 'The photo: "'.$item_data_to_validate['name'].'" already exists in the "content" directory.';
					$item_valid = FALSE;
				}

				// If a preset is used, check if the filename is used in the preset's directory
				// Ignore for "content", since that's already been checked
				if ($item_preset_data AND $item_preset_data['preset_directory'] != '' AND $item_preset_data['preset_directory'] != 'content')
				{
					if (self::is_filename_used($item_data_to_validate['name'], $item_preset_data['preset_directory']))
					{
						$errors[] = 'The photo: "'.$item_data_to_validate['name'].'" already exists in the "'.$item_preset_data['preset_directory'].'" directory.';
						$item_valid = FALSE;
					}

				}
			}
			else
			{
				//Validate other File-types: Documents, Audios and Videos

				//Validate Docs
				if (in_array($item_data_to_validate['type'], array('text/plain', 'application/pdf', 'image/pdf', 'application/msword', 'application/vnd.ms-excel', 'application/msexcel', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/vnd.ms-powerpoint', 'text/plain', 'text/csv')))
				{
					//set possible path for Original Photo
					$test_doc_path = $this->media_absolute_path.DIRECTORY_SEPARATOR.'docs'.DIRECTORY_SEPARATOR.$item_data_to_validate['name'];

					$test_doc_md5 = @md5_file($test_doc_path);
					if (self::get_all_items_based_on('hash', $test_doc_md5))
					{
						$errors[] = 'The Document: "'.$item_data_to_validate['name'].'" already exists in the: "media/docs" Media System.<br />'.
							'Please Rename and Re-Upload it or remove the existing one before to proceed.';
						$item_valid = FALSE;
					}
				}
				//end of validating Documents


				//Validate Audios
				if (strpos('audio/', $item_data_to_validate['type']) !== FALSE)
				{
					//set possible path for Original Photo
					$test_audio_path = $this->media_absolute_path.DIRECTORY_SEPARATOR.'audios'.DIRECTORY_SEPARATOR.$item_data_to_validate['name'];

					$test_audio_md5 = @md5_file($test_audio_path);
					if (self::get_all_items_based_on('hash', $test_audio_md5))
					{
						$errors[] = 'The Audio File: "'.$item_data_to_validate['name'].'" already exists in the: "media/audios" Media System.<br />'.
							'Please Rename and Re-Upload it or remove the existing one before to proceed.';
						$item_valid = FALSE;
					}
				}
				//end of validating Audios


				//Validate Videos
				if (strpos('video/', $item_data_to_validate['type']) !== FALSE)
				{
					//set possible path for Original Photo
					$test_video_path = $this->media_absolute_path.DIRECTORY_SEPARATOR.'videos'.DIRECTORY_SEPARATOR.$item_data_to_validate['name'];

					$test_video_md5 = @md5_file($test_video_path);
					if (self::get_all_items_based_on('hash', $test_video_md5))
					{
						$errors[] = 'The Video File: "'.$item_data_to_validate['name'].'" already exists in the: "media/videos" Media System.<br />'.
							'Please Rename and Re-Upload it or remove the existing one before to proceed.';
						$item_valid = FALSE;
					}
				}
				//end of validating Documents

			}
			//end of validating if File was previously uploaded to MEdia

			//Check File Type
			if (!in_array($item_data_to_validate['type'], $this->allowed_media_types))
			{
				$errors[] = 'Could not upload "'.$item_data_to_validate['name'].'". '.
					'"'.basename($item_data_to_validate['type']).'" files cannot be uploaded here.';
				$item_valid = FALSE;
			}

			//Check File Size
			if (($item_data_to_validate['size'] > $this->allowed_file_size) OR trim(ini_get('upload_max_filesize'), 'M') < ($item_data_to_validate['size'] / 1000000))
			{
				//Show the Filesize in Mbs
				$errors[] = 'The File: "'.$item_data_to_validate['name'].'" '.
					'Size: '.number_format(($item_data_to_validate['size'] / (1024 * 1024)), 2, '.', '').'Mb is too big. '.
					'Maximum allowed size is: '.($this->allowed_file_size / 1000000).'Mb.';
				$item_valid = FALSE;
			}

			//Check File Name
			if (strlen($item_data_to_validate['name']) > $this->allowed_length_file_name)
			{
				IbHelpers::set_message(
					'The File Name: "'.$item_data_to_validate['name'].'", length: '.strlen($item_data_to_validate['name']).' characters is too long. '.
						'Maximum allowed File Name Length is: '.$this->allowed_length_file_name.' characters, INCLUDING the File extension.',
					'error'
				);
				$item_valid = FALSE;
			}

			$file_ext = strtolower(substr($item_data_to_validate['name'], strrpos($item_data_to_validate['name'], '.') + 1));
			if (!in_array($file_ext, self::$allowed_extensions)) {
				$errors[] = $file_ext . ' files cannot be uploaded!';
				$item_valid = false;
                Model_Errorlog::save(null, "SECURITY");
			}
		}

		if ($return_error)
		{
			return $errors;
		}
		else
		{
			foreach ($errors as $error)
			{
				IbHelpers::set_message($error, 'error');
			}
			return $item_valid;
		}
	}

	//end of function


	/**
	 * Function to Validate & Update Media Folder<br />
	 * If the Specified Media Folder, does not Exists in the Media Filesystem -=> this function will try to build it,
	 * with its location relevant to the CURRENT_MEDIA_FOLDER
	 *
	 * @param $media_folder_to_validate
	 * @return bool <strong><TRUE></strong> on SUCCESS, <strong>FALSE</strong> otherwise.
	 */
	private function validate_and_prepare_media_folder($media_folder_to_validate)
	{
//		echo "\nModel_Media->validate_and_prepare_media_folder(): Validate and Update (Prepare) Media Folder: \n";
//		IbHelpers::die_r($media_folder_to_validate);

		$folder_valid = TRUE;

		//Check if Folder Exists
		if (!is_dir($this->media_absolute_path.DIRECTORY_SEPARATOR.$media_folder_to_validate))
		{
			//The Folder is not set -=> Build it
			//Set the Structure of the Missing Folder to be Added based on the $media_folder_to_validate string
			$folder_structure_to_build = $this->create_folder_structure_from_string($media_folder_to_validate);

			//Build the Missing Folder
			$folder_created = $this->build_media_folder(
				reset($folder_structure_to_build),
//				$this->media_absolute_path.((!in_array(key($folder_structure_to_build), $this->folders_with_no_thumbs))? DIRECTORY_SEPARATOR.key($folder_structure_to_build) : ''),
				$this->media_absolute_path.DIRECTORY_SEPARATOR.key($folder_structure_to_build)
//				((in_array(key($folder_structure_to_build), $this->folders_with_no_thumbs))? TRUE : FALSE)
			);
			//set the Result from the creation of the missing Media Folder
			$folder_valid = $folder_created;
		}
		//clear the file status cache - after using is_dir()
		clearstatcache();

		//Check if Folder is ready for Uploading of Files - ONLY if the Folder exists
		if ($folder_valid AND !$this->prepare_media_folder_for_upload($this->media_absolute_path.DIRECTORY_SEPARATOR.$media_folder_to_validate))
		{
			IbHelpers::set_message(
				'There was a problem with preparing of folder: '.$this->media_relative_path.DIRECTORY_SEPARATOR.$media_folder_to_validate.
					' for uploading. Please update the Folder\'s permissions before Upload.',
				'error'
			);
			$folder_valid = FALSE;
		}

		//return
		return $folder_valid;
	}

	//end of function


	/**
	 * Function used to Build The Media Filesystem.<br />
	 * This function will be called by this Model's Constructor, if the Media Folder is NOT present in the current Project (Web App).
	 *
	 *
	 * @param array $media_folder_to_build        - Array holding the DEFAULT_MEDIA_FILESYSTEM (Folder Structure) to be built.<br />
	 *                                            Has the Following Structure:
	 * <pre>
	 *    <strong>media/</strong>
	 *      <strong>docs/</strong>  <em>=> Used to store all Text Documents: txt., .doc, .pdf etc.</em>
	 *      <strong>photos/</strong> <em>=> Used to store all images displayed on the Front-End of this Website</em>
	 *        <strong>banners/</strong>  <em>=> Used for the Pages-Plugin: Banner images</em>
	 *           <strong>_thumbs/</strong> <em>=> stores the corresponding image - System (Admin) thumbnails</em>
	 *           <strong>_thumbs_cms/</strong> <em>=> stores the corresponding image - Front-End thumbnails</em>
	 *        <strong>content/</strong> <em>=> Used mainly for the Pages-Plugin, but can be accessed through the tinyBrowser on any other Plugin: item content editor</em>
	 *           <strong>_thumbs/</strong>
	 *           <strong>_thumbs_cms/</strong>
	 *       <em>... and other plugins specific folders, required for the Plugin Specific Presets for Image Uploads</em>
	 *      <strong>videos/</strong> <em>=> Used to store all Videos</em>
	 * </pre>
	 * <em>Please NOTE:</em> The Function can be used to build other Folder(s) within the Media Folder.<br />
	 *                                            In relation to this, the passed parameters should be as follows:<br />
	 * <strong>1.</strong> FOLDER_TO_BUILD:
	 * <pre>
	 *                                            Array(
	 *    [FOLDER_NAME] => Array(
	 *      [_thumbs] => Array()
	 *      [_thumbs_cms] => Array()
	 *    )
	 * )
	 * </pre>
	 * <strong>2.</strong> The MEDIA_FOLDER_PARENT_LOCATION, should be:
	 * <em>'ABSOLUTE_PATH_TO_THE_MEDIA_FOLDER'.DIRECTORY_SEPARATOR.'PATH_TO_THE_MEDIA_SUB_FOLDER_WHICH_WILL_HOLD_THE_NEW_ONE'</em>
	 *
	 * @param       $media_folder_parent_location - String holding the ABSOLUTE_PATH to the Folder, which will hold the corresponding Media Folder to be created.<br />
	 *                                          <em>DEFAULT VALUE:</em> Media folder for the current Project - Web App.<br />
	 *                                        <strong><em>Example:<em> ROOTPATH.'projects'.DIRECTORY_SEPARATOR.PROJECTNAME.DIRECTORY_SEPARATOR.'www'.DIRECTORY_SEPARATOR.'media'</strong><br />
	 *                                          <em>Please Note:</em> This Media Path is SET UP in this Model's Constructor.
	 * @param bool  $recursively_called           - Boolean used to set if this Function has been Called Recursively, i.e. if the function called itself.
	 *                                            Default Value: <strong>FALSE</strong> -=> indicating that the Function has been called from another function, i.e. this Model's Constructor
	 * @return bool <strong>TRUE</strong> on SUCCESS, <strong>FALSE</strong> otherwise.<br />
	 *                In the Case of Problem with creating of a Media Folder, the Function will set a corresponding Error Message and return <strong>FALSE</strong>.
	 */
	private function build_media_folder(array $media_folder_to_build, $media_folder_parent_location, $recursively_called = FALSE)
	{
//		echo "\nModel_Media->build_media_folder(): Build Default Media Filesystem: into: $media_folder_parent_location \n";
//		IbHelpers::die_r($media_folder_to_build);

		$media_folder_created = TRUE;
		$media_root = '';
		/*
		 * If the $media_folder_parent_location == $this->media_absolute_path => take the Generated in this Model's Constructor:  $this->media_absolute_path
		 * This is REQUIRED when creating the MAIN Media Folder, i.e. ABSOLUTE_PATH_TO_MEDIA/media
		 * Other Media Sub-folders will have the full path to their Parent-Folder specified in the: $media_folder_parent_location
		 */
		if (
			$media_folder_parent_location != '' AND
			$media_folder_parent_location === $this->media_absolute_path
			AND !$recursively_called
		)
		{
			//Take the Media Root folder, based on the Generated in this Model's Constructor: $this->media_absolute_path
			$media_root = substr($media_folder_parent_location, 0, strpos($media_folder_parent_location, DIRECTORY_SEPARATOR.'media', 0));
			//Check if the specified Media Root is Writable and prepare it for the building of the Media Filesystem
			$this->prepare_media_folder_for_upload($media_root);

			//A Sub-folder is to be Created -=> just prepare its Parent Folder for the building of its Sub-folders
		}
		else if ($media_folder_parent_location != '')
		{
			$this->prepare_media_folder_for_upload($media_folder_parent_location);
		}

		//Build the specified Media Folder
		if ($media_folder_parent_location != '' AND is_array($media_folder_to_build))
		{
			foreach ($media_folder_to_build as $folder_key => $folder_content)
			{

				//Check if the Folder to be Created Exists in the Current Media Filesystem
				if (!$this->check_if_media_folder_exists($folder_key))
				{

					//Set path to the Media Folder to be created
					$folder_to_create = (($media_root != '') ? $media_root : $media_folder_parent_location).DIRECTORY_SEPARATOR.$folder_key;

					if (!file_exists($folder_to_create)) {
						//create the folder now - mode: 0777 is "Full Access to Everybody"
						if (@mkdir($folder_to_create, 0777)) {
							//Set the Access Mode for the created Folder. The use of mkdir($folder_to_create, 0777) is not working for some reason
							chmod($folder_to_create, 0777);
							//Call This Function Recursively for the Remaining Sub-folders of This Folder, ideally: $folder_content size will be == 2: _thumbs and _thumbs_cms
							if (sizeof($folder_content) > 0) {
								$media_folder_created = $this->build_media_folder($folder_content, $folder_to_create,
										true);
							}
							//else that was the last sub-folder to create -=> continue to the next Media Folder and its Sub-folders to Create
						} else {
							IbHelpers::set_message('System Error: Failed to create folder: ' . $folder_to_create,
									'error');
							$media_folder_created = false;
						}
					}
				} //else this Folder has been built in the Current Media Filesystem -=> proceed with its Sub-Folders to be Created
				else
				{
					//Call This Function To build the Remaining - Sub-Folders This (Existent) Media Folder
					if (sizeof($folder_content) > 0)
					{
						$media_folder_created = $this->build_media_folder(
							$folder_content,
							(($media_root != '') ? $media_root : $media_folder_parent_location),
							TRUE);
					}
					//else that was the last sub-folder to create -=> continue to the next Media Folder and its Sub-folders to Create
				}

			}
			//end of for loop - building Media Filesystem

			//The Specified Folder is just a String - should be an Array
		}
		else if ($media_folder_parent_location != '' AND !is_array($media_folder_to_build))
		{
			IbHelpers::set_message(
				'System Error: "'.$media_folder_to_build.'",'.
					' MUST be presented as an Array where each Array Key is this Folder corresponding Sub-Folder(s) to create.<br />'.
					'If the passed Folder (Array) to create is empty, an empty Folder, will be created.',
				'error');

			//The passed $media_folder_parent_location is EMPTY_STRING -=> SHOULD NEVER HAPPEN, as this Function cannot be called outside this Model
		}
		else if ($media_folder_parent_location == '')
		{
			IbHelpers::set_message('System Error: MEDIA_FOLDER_PARENT_LOCATION, CANNOT be EMPTY_STRING: "'.$media_folder_parent_location.'".', 'error');
			$media_folder_created = FALSE;
		}

		//Return
		return $media_folder_created;
	}

	//end of function


	//Prepare a Media Folder/File for Upload
	/**
	 * Private Function used to check if a specified Media Folder is WRITABLE and if not to enable it for WRITING, i.e. uploading of Files (Docs/Photos/Videos etc.)
	 *
	 * @param $folder_to_update - Absolute Path to the Media Folder to be Updated.
	 * @return bool - <strong>TRUE</strong> - when folder's access was enabled, <strong>FALSE</strong> otherwise.
	 */
	private function prepare_media_folder_for_upload($folder_to_update)
	{
//		echo "\nModel_Media->prepare_media_folder_for_upload(): Prepare Media folder for Upload: \n";
//		IbHelpers::pre_r($folder_to_update);

		$folder_ready_for_upload = TRUE;

		// create folder if it doesnt exist
		if (!file_exists($folder_to_update))
		{
			@mkdir($folder_to_update, 0777, TRUE);
		}

		/*
		 * File/Folder Modes Variables
		 // Read and write for owner, nothing for everybody else - 0600
		 // Read and write for owner, read for everybody else - 0644
		 // Everything for owner, read and execute for others - 0755
		 // Everything for owner, read and write for others - 0766
		 // Everything for owner, read and execute for owner's group - 0750
		 */
		//Folder if NOT Writable
		if (is_dir($folder_to_update) AND !is_writable($folder_to_update))
		{
			//Change the mode of this folder to be available for Media Uploads
			$folder_ready_for_upload = chmod($folder_to_update, 0777);
		}
		//else Folder is OK for File Uploading

		//Return
		return $folder_ready_for_upload;
	}

	//end of function


	/*
	 * General Function Used to Add a File (Item) to the Media.
	 * Based on the provided File: File-Type it will call:
	 * $this->add_media_photo
	 * $this->add_media_file($file_info, $file_folder)
	 */
	public function add_item_to_media(array $file_info, $preset_details = NULL)
	{
//		echo "\nModel_Media->add_item_to_media(): Add Item to Media: \n";
//		IbHelpers::die_r(array('file_info'=>$file_info, 'preset_details'=>$preset_details));

		$file_uploaded = TRUE;

		/* 1. Call the corresponding PRIVATE add_media_(photo/document/audio/video)() function, based on the Uploaded File: $file_info['type'] */
		$directory = self::get_directory($file_info['type']);
        $is_svg    = ($file_info['type'] == 'image/svg+xml');
        $is_ico    = (pathinfo($file_info['name'], PATHINFO_EXTENSION) == 'ico');

		if ($directory == 'photos' && ($is_svg || $is_ico))
		{
			$file_uploaded = $this->add_image_without_resize($file_info, $preset_details);
		}
		elseif ($directory == 'photos')
		{
			$file_uploaded = $this->add_media_photo($file_info, $preset_details);
		}
		elseif ($directory == '')
		{
			IbHelpers::die_r('We should not get to this point');
		}
		else // documents, audio, videos, fonts
		{
			$file_uploaded = $this->add_media_file($file_info, $directory);
		}

		// Return
		return $file_uploaded;
	}

	//end of function


	//Update a Media Item record - @TODO: might not need this
	public function update($item_update_data)
	{
		echo "\nModel_Media->update(): Update Media Item: \n";
		IbHelpers::die_r(array('item_update_data' => $item_update_data, 'message' => 'UNDER CONSTRUCTION'));

		//@TODO: The Update Media Item Code Goes here

//		// 1. get the ID of the currently-logged in user, i.e. the user who is making the update
//		$logged_in_user = Auth::instance()->get_user();
//
//		/* 2. Create the NEW Panel */
//		//Set the Panel data to be added to Database
//		$item_to_update_data['id'] = $item_update_data['panel_id'];
//		$item_to_update_data['title'] = $item_update_data['panel_title'];
//		$item_to_update_data['position'] = $item_update_data['panel_position'];
//		$item_to_update_data['text'] = $item_update_data['panel_text'];
//		$item_to_update_data['image'] = $item_update_data['panel_image'];
//		$item_to_update_data['link_id'] = ($item_update_data['panel_link'] != '')? $item_update_data['panel_link'] : NULL;
//		/*
//		 * Set the link_id if it is not set (is null) and the $item_update_data['panel_link_url'] is set, i.e. not an EMPTY_STRING
//		 * If th eLink ID was set, i.e. was 0 or greater than zero, i.e. some inner page was set as link to this Item => use the preset Inner Page a sLink Id
//		 */
//		if($item_update_data['panel_link_url'] != '' AND is_null($item_to_update_data['link_id'])) $item_to_update_data['link_id'] = 0;
//		$item_to_update_data['link_url'] = $item_update_data['panel_link_url'];
//		$item_to_update_data['date_publish'] = $item_update_data['panel_date_publish'];
//		$item_to_update_data['date_remove'] = $item_update_data['panel_date_remove'];
//		$item_to_update_data['publish'] = $item_update_data['panel_publish'];
//		$item_to_update_data['order_no'] = $item_update_data['panel_order_no'];
//		// Format the required dates for mysql storage
////		$item_to_update_data['date_created'] = date('Y-m-d H:i:s');
////		$item_to_update_data['created_by'] = $logged_in_user['id'];
//		$item_to_update_data['date_modified'] = date('Y-m-d H:i:s');
//		$item_to_update_data['modified_by'] = $logged_in_user['id'];
//		$item_to_update_data['deleted'] = 0;
//		//Update the Panel to DB
//		$update_result = DB::update(self::$model_items_table)
//								->set($item_to_update_data)
//								->where('id', '=', $item_to_update_data['id'])
//								->execute();
//		// return new ID
//		return $update_result;
	}

	//end of function


	//Deletes a Specified Media Item - From the Filesystem and the Database
	public function delete_media_item($item_id, $media_folder)
	{
//		echo "\nModel_Media->delete_media_item(): Delete Media Item: \n";
//		IbHelpers::die_r(array('item_id'=>$item_id, 'media_folder'=>$media_folder));

		//initialize Delete result to FALSE by default - will be set to 1 if Deletion of item is successful
		$delete_result = FALSE;

		// 1. get the ID of the currently-logged in user, i.e. the user who is making the update
		$logged_in_user = Auth::instance()->get_user();

		// 2. Get details for the Media Item to be Deleted
		$item_to_delete = $this->get_all_items_admin($item_id);

		// 3. Set the path to the Media File to be Deleted
		$item_to_delete_path = '';
		if ($media_folder == 'photos')
		{
			$item_to_delete_path = $this->media_absolute_path.DIRECTORY_SEPARATOR.
				$media_folder.DIRECTORY_SEPARATOR.
				$item_to_delete[0]['location'].DIRECTORY_SEPARATOR.
				$item_to_delete[0]['filename'];
		}
		else
		{
			//Set paths for all remaining Media Items: Docs, Audios and Videos
			$item_to_delete_path = $this->media_absolute_path.DIRECTORY_SEPARATOR.
				$item_to_delete[0]['location'].DIRECTORY_SEPARATOR.
				$item_to_delete[0]['filename'];
		}

		// 4. Remove the Corresponding Item from the Media Filesystem
		if (
			@unlink($item_to_delete_path)
		)
		{
			//Remove Photo Thumbs for Photo Files to be deleted
			if ($media_folder == 'photos')
			{
				//set path to this File's System and Front-End Thumbs
				$item_system_thumb = $this->media_absolute_path.DIRECTORY_SEPARATOR.
					$media_folder.DIRECTORY_SEPARATOR.
					$item_to_delete[0]['location'].DIRECTORY_SEPARATOR.
					'_thumbs_cms'.DIRECTORY_SEPARATOR.
					$item_to_delete[0]['filename'];
				$item_thumb = $this->media_absolute_path.DIRECTORY_SEPARATOR.
					$media_folder.DIRECTORY_SEPARATOR.
					$item_to_delete[0]['location'].DIRECTORY_SEPARATOR.
					'_thumbs'.DIRECTORY_SEPARATOR.
					$item_to_delete[0]['filename'];

				//Remove the file System Thumb in the corresponding: _thumbs_cms
				if (file_exists($item_system_thumb)) @unlink($item_system_thumb);

				//Remove the file Thumb in the corresponding: _thumbs
				if (file_exists($item_thumb)) @unlink($item_thumb);
			}
			//else no need to remove Thumbs for other Media Files, as there are no such

			//Remove the DB record of this Media Item
			$delete_result = DB::delete(self::$model_items_table)
				->where('id', '=', $item_id)
				->execute();
			//The File could not be deleted
		}
		else
		{

		}

		// 5. Set corresponding Result Messages
		if ($delete_result == 1)
		{
			IbHelpers::set_message(
				'Media Item: "'.$item_to_delete[0]['filename'].'", with ID: #'.$item_id.' deleted',
				'success'
			);
		}
		else
		{
			IbHelpers::set_message(
				'The specified Media Item: "'.$item_to_delete[0]['filename'].'", with ID: #'.$item_id.' could not be deleted.',
				'error'
			);
		}

		// 6. return
		return $delete_result;
	}

	//end of function

	/**
	 * @param  $filename       string    the original filename
	 * @param  $directories    array     the directories to be checked (e.g. "content" and the preset's directory)
	 * @return                 string    the filename with a number appended
	 */
    public static function get_filename_suggestion($filename, $directories)
    {
        $name_used = TRUE;

        // Replace spaces with underscores
        $filename  = str_replace('%20', '_', str_replace(' ', '_', $filename));

        // Split the filename into name and extension
        $ext       = substr($filename, strrpos($filename, '.') + 1);
        $name      = substr($filename, 0, strrpos($filename, '.'));
        $name      = preg_replace('/[^a-z0-9\_\-]+/i', '-', $name); // replace all special characters with -
        $name      = preg_replace('/\-+/i', '-', $name); // replace multiple occurrences of -. e.g. --- => -

        // Check if the filename is used in any of the directories
        // If it in, add a number to the end of the filename, check again and repeat
        // 30 is an arbitrary number, to break any potential infinite loops
        for ($i = 0; $name_used AND $i < 30; $i++)
        {
            if ($i != 0) {
                $filename = $name . '_(' . $i . ').' . $ext;
            } else {
                $filename = $name . '.' . $ext;
            }

            $check = DB::select()
                ->from('plugin_media_shared_media')
                ->where('filename', '=', $filename)
                ->and_where('location', 'in', $directories)
                ->execute();

            $name_used = (sizeof($check) > 0);
        }

        return $filename;
    }

	// Validate an image edited with the cropzoom editor
	public function cropzoom_validate($post, $filename, $preset_id)
	{
		$errors = array();
		$preset = (is_numeric($preset_id)) ? $this->get_presets(array('id' => $preset_id)) : NULL;
		$name_used = DB::select()->from('plugin_media_shared_media')->where('filename', '=', $filename)->and_where('location', '=', $preset['directory'])->execute();

		$errors['name_used'] = (sizeof($name_used) > 0);

		return $errors;
	}


	// Save an image using the cropzoom editor
	public function cropzoom_save($post, $filename, $preset = NULL)
	{
		/* Create the image */
		$pWidth = $post["imageW"];
		$pHeight = $post["imageH"];

		$selector_h = $post['data']['selector_h'];
		$selector_w = $post['data']['selector_w'];

		$ext = explode(".", $post["imageSource"]);
		$ext = strtolower($ext[sizeof($ext) - 1]);
		if (!in_array($ext, array('jpg', 'jpeg', 'png', 'gif')))
		{
			$ext = explode(';', $post["imageSource"]);
			$ext = explode('/', $ext[0]);
			$ext = $ext[1];
		}
		$type = (($ext == 'jpg') ? 'jpeg' : $ext);

		$function = 'imagecreatefrom'.$type;
		$image = $function($post["imageSource"]);
		$width = imagesx($image);
		$height = imagesy($image);
		// Resample
		$image_p = imagecreatetruecolor($pWidth, $pHeight);
		//$white = imagecolorallocate($image_p, 255, 255, 255);
		//imagefill($image_p, 0, 0, $white);
		$this->setTransparency($image, $image_p, $ext);

		imagecopyresampled($image_p, $image, 0, 0, 0, 0, $pWidth, $pHeight, $width, $height);
		imagedestroy($image);
		$widthR = imagesx($image_p);
		$heightR = imagesy($image_p);

		$selectorX = $post["selectorX"];
		$selectorY = $post["selectorY"];

		if ($post["imageRotate"])
		{
			$angle = 360 - $post["imageRotate"];
			$image_p = imagerotate($image_p, $angle, 0);

			$pWidth = imagesx($image_p);
			$pHeight = imagesy($image_p);

			$diffW = abs($pWidth - $widthR) / 2;
			$diffH = abs($pHeight - $heightR) / 2;

			$post["imageX"] = ($pWidth > $widthR ? $post["imageX"] - $diffW : $post["imageX"] + $diffW);
			$post["imageY"] = ($pHeight > $heightR ? $post["imageY"] - $diffH : $post["imageY"] + $diffH);
		}

		$dst_x = $src_x = $dst_y = $src_y = 0;
		($post["imageX"] > 0) ? $dst_x = abs($post["imageX"]) : $src_x = abs($post["imageX"]);
		($post["imageY"] > 0) ? $dst_y = abs($post["imageY"]) : $src_y = abs($post["imageY"]);

		$viewport = imagecreatetruecolor($post["viewPortW"], $post["viewPortH"]);
		//$white = imagecolorallocate($viewport, 255, 255, 255);
		//imagefill($viewport, 0, 0, $white);
		$this->setTransparency($image_p, $viewport, $ext);
		imagecopy($viewport, $image_p, $dst_x, $dst_y, $src_x, $src_y, $pWidth, $pHeight);
		imagedestroy($image_p);

		// image in the crop selection area
		$selector = imagecreatetruecolor($selector_w, $selector_h);
		//$white = imagecolorallocate($selector, 255, 255, 255);
		//imagefill($selector, 0, 0, $white);
		$this->setTransparency($viewport, $selector, $ext);
		imagecopy($selector, $viewport, 0, 0, $selectorX, $selectorY, $post["viewPortW"], $post["viewPortH"]);


		// image set to preset
		if ( ! is_null($preset))
		{
			// If width or height is 0, use the width or height necessary to maintain the ratio of the original image
			if ($preset['width_large'] == 0)
			{
				$preset['width_large'] = round($preset['height_large'] * $pWidth / $pHeight);
			}

			if ($preset['height_large'] == 0)
			{
				$preset['height_large'] = round($preset['width_large'] * $pHeight / $pWidth);
			}

			$preset_image = imagecreatetruecolor($preset['width_large'], $preset['height_large']);
			//$white = imagecolorallocate($preset_image, 255, 255, 255);
			//imagefill($preset_image, 0, 0, $white);
			$this->setTransparency($viewport, $preset_image, $ext);
			imagecopyresampled($preset_image, $selector, 0, 0, 0, 0, $preset['width_large'], $preset['height_large'], $selector_w, $selector_h);
		}

		// thumbnail, if applicable
		if (isset($preset['thumb']) AND $preset['thumb'] != 0)
		{
			// If width or height is 0, use the width or height necessary to maintain the ratio of the original image
			if ($preset['width_thumb'] == 0)
			{
				$preset['width_thumb'] = round($preset['height_thumb'] * $pWidth / $pHeight);
			}

			if ($preset['height_thumb'] == 0)
			{
				$preset['height_thumb'] = round($preset['width_thumb'] * $pHeight / $pWidth);
			}

			$thumb_image = imagecreatetruecolor($preset['width_thumb'], $preset['height_thumb']);
			//$white = imagecolorallocate($thumb_image, 255, 255, 255);
			//imagefill($thumb_image, 0, 0, $white);
			$this->setTransparency($viewport, $thumb_image, $ext);
			if ($preset['action_thumb'] == 'crop') {
				$cimage = imagecreatetruecolor($preset['width_thumb'], $preset['height_thumb'] / ($preset['width_thumb'] / $preset['height_thumb']));
				$this->setTransparency($viewport, $thumb_image, $ext);
				$white = imagecolorallocate($cimage, 255, 255, 255);
				imagefill($cimage, 0, 0, $white);
				imagecopyresampled($cimage, $selector, 0, 0, 0, 0, $preset['width_thumb'], $preset['height_thumb'] / ($preset['width_thumb'] / $preset['height_thumb']), $selector_w, $selector_h);

				$dy = ($preset['height_thumb'] - ($preset['height_thumb'] / ($preset['width_thumb'] / $preset['height_thumb']))) / 2;
				imagecopy($thumb_image, $cimage, 0, $dy, 0, 0, $preset['width_thumb'], $preset['height_thumb']);
				imagefill($thumb_image, 0, $preset['height_thumb'] - $dy, $white);
				imagedestroy($cimage);

				$cimage = imagecreatetruecolor($preset['width_thumb'], $preset['height_thumb']);
				$white = imagecolorallocate($cimage, 255, 255, 255);
				imagefill($cimage, 0, 0, $white);

				imagecopyresampled($thumb_image, $preset_image, 0, 0, 0, 0, $preset['width_thumb'], $preset['height_thumb'], $preset['width_large'], $preset['height_large']);
			} else {

				imagecopyresampled($thumb_image, $selector, 0, 0, 0, 0, $preset['width_thumb'], $preset['height_thumb'], $selector_w, $selector_h);
			}
		}

		if (is_null($filename))
		{
			$filename = time().".".$ext;
		}
		$filename = preg_replace('/[^a-z0-9\.\_\-]/i', '_', $filename);

		$directory = (!empty($preset['directory'])) ? $preset['directory'] : 'content';
		$image = (!empty($preset_image)) ? $preset_image : $selector;

		$file_path = 'photos/'.$directory.'/';
		$this->validate_and_prepare_media_folder('photos/'.$directory);

		if (Kohana::$config->load('config')->project_media_folder != '')
		{
			$file_path = 'shared_media'.DIRECTORY_SEPARATOR.Kohana::$config->load('config')->project_media_folder.DIRECTORY_SEPARATOR.$file_path;
		}
		$file = $file_path.$filename;
		$file_thumb = $file_path.'_thumbs/'.$filename;

		switch ($ext)
		{
			case "png":
				imagepng($image, ($file != NULL ? $file : ''));
				break;
			case "jpeg":
			case "jpg":
				imagejpeg($image, ($file ? $file : ''), 90);
				break;
			case "gif":
				imagegif($image, ($file ? $file : ''));
				break;
		}

		if (isset($thumb_image))
		{
			switch ($ext)
			{
				case "png":
					imagepng($thumb_image, ($file_thumb != NULL ? $file_thumb : ''));
					break;
				case "jpeg":
				case "jpg":
					imagejpeg($thumb_image, ($file_thumb ? $file_thumb : ''), 90);
					break;
				case "gif":
					imagegif($thumb_image, ($file_thumb ? $file_thumb : ''));
					break;
			}
		}

		$this->create_system_thumbnail($filename, 'photos/'.$directory);

		imagedestroy($viewport);

		/* Save the image to the database */
		$logged_in_user = Auth::instance()->get_user();

		$item_data['size'] = filesize($file);
		$item_data['mime_type'] = 'image/'.$type;
		if (isset($preset['width_large']))
		{
			$item_data['dimensions'] = $preset['width_large'].'x'.$preset['height_large'];
		}
		else
		{
			$item_data['dimensions'] = $pWidth.'x'.$pHeight;
		}
		$item_data['hash'] = md5_file($file);
		$item_data['preset_id'] = $preset['id'];
		$item_data['date_modified'] = date('Y-m-d H:i:s');
		$item_data['modified_by'] = $logged_in_user['id'];

		// Check if this filename name has already been used
		$name_used = DB::select()->from('plugin_media_shared_media')->where('filename', '=', $filename)->and_where('location', '=', $directory)->execute()->as_array();

		if (sizeof($name_used) > 0)
		{
			$id = $name_used[0]['id'];
			$update_result = DB::update(self::$model_items_table)->set($item_data)->where('id', '=', $id)->execute();
		}
		else
		{
			$item_data['filename'] = $filename;
			$item_data['location'] = $directory;
			$item_data['date_created'] = $item_data['date_modified'];
			$item_data['created_by'] = $item_data['modified_by'];

			$insert_result = DB::insert(self::$model_items_table)->values($item_data)->execute();
            $id = $insert_result[0];
			$activity = new Model_Activity;
			$activity->set_action('upload')->set_item_type('media')->set_item_id($id);
			$activity->save();
		}

		return array('file' => $file, 'media_id' => $id);
	}

	private function setTransparency($imgSrc, $imgDest, $ext)
	{
		if ($ext == "png" OR $ext == "gif")
		{
			$trnprt_indx = imagecolortransparent($imgSrc);
			// If we have a specific transparent color
			if ($trnprt_indx >= 0 && $ext == "gif")
			{
				// Get the original image's transparent color's RGB values
				$trnprt_color = imagecolorsforindex($imgSrc, $trnprt_indx);
				// Allocate the same color in the new image resource
				$trnprt_indx = imagecolorallocate($imgDest, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
				// Completely fill the background of the new image with allocated color.
				imagefill($imgDest, 0, 0, $trnprt_indx);
				// Set the background color for new image to transparent
				imagecolortransparent($imgDest, $trnprt_indx);
			} // Always make a transparent background color for PNGs that don't have one allocated already
			elseif ($ext == "png")
			{
				// Turn off transparency blending (temporarily)
				imagealphablending($imgDest, TRUE);
				// Create a new transparent color for image
				$color = imagecolorallocatealpha($imgDest, 0, 0, 0, 127);
				// Completely fill the background of the new image with allocated color.
				imagecolortransparent($imgDest, $color);
				imagefill($imgDest, 0, 0, $color);
				// Restore transparency blending
				imagesavealpha($imgDest, TRUE);
			}
		} else {
			$white = imagecolorallocatealpha($imgDest, 255, 255, 255, 127);
			imagealphablending($imgDest, true);
			imagefill($imgDest, 0, 0, $white);
			imagesavealpha($imgDest, true);
		}
	}


	/*
	 * Private Function - Adds/Uploads Photos, Images to the Media Plugin.
	 * Folder Used: media/photos/PLUGIN_NAME/
	 * If No Plugin name is specified, the Function will Add the passed Image to the $default_media_photos_folder - media/photos/content/
	 */
	private function add_media_photo(array $photo_file_details, $preset_details = NULL)
	{
//		echo "\nModel_Media->add_media_photo(): Add Photo-Item to Media \n";
//		IbHelpers::die_r(array('file_info'=>$photo_file_details, 'preset_details'=>$preset_details));

		//flag used to check if a Photo File is OK to be Uploaded
		$ok_to_upload = TRUE;
		$photo_file_name = '';
		//will be set to TRUE when the Original Image is uploaded
		$original_photo_uploaded = FALSE;
		//will be set to TRUE, when a Preset is used and applied to the Uploaded Photo
		$preset_applied = FALSE;
		//will be set to the ID of the currently Uploaded Photo - after the Photo is added to the Media Filesystem and Recorder in the Media DB Table
		$original_photo_added_to_db = NULL;
		$preset_photo_added_to_db = NULL;
		$media_id = null;

		/* 1. Set the Original Image Folder and the Image Preset Folder, based on the passed $preset_details/preset? */
		$original_image_folder = 'photos'.DIRECTORY_SEPARATOR.self::$default_image_folder;
		$preset_image_folder = ($preset_details !== NULL AND $preset_details['preset_directory'] != '') ?
			'photos'.DIRECTORY_SEPARATOR.strtolower(str_replace(array('-', ' '), '_', $preset_details['preset_directory'])) : '';

		/* 2. Check if the folders: Default (content) and Preset specific folder are OK for uploading of images */
		if (!$this->validate_and_prepare_media_folder($original_image_folder))
		{
			//The media/photos/content/ folder is either missing (not created) or not-writable -=> This will be fixed AUTOMATICALLY by the: validate_and_prepare_media_folder() function
			//@TODO: NOTE that the $this->validate_and_prepare_media_folder will automatically Create the folder if it si missing in the MEDIA Filesystem or will make it Writable
		}
		if ($preset_image_folder !== '' AND !$this->validate_and_prepare_media_folder($preset_image_folder))
		{
			//The media/photos/preset folder is either missing (not created yet), or not-writable -=> This will be fixed AUTOMATICALLY by the: validate_and_prepare_media_folder() function
			//@TODO: NOTE that the $this->validate_and_prepare_media_folder will automatically Create the folder if it is missing in the MEDIA Filesystem
			//@TODO: Might be nicer if we refactor this - organize THIS CODE a bit better
		}
		//else Folders are OK or have been Updated to allow this photo upload  => proceed with photo upload

		/* 3. Get Photo (image) Details
		 * $upload_photo_details content:
		 *   array
		 *     0 => int 550 - width
		 *     1 => int 373 - height
		 *     2 => int 2 - image type
		 *     3 => string - 'width="550" height="373"' (length=24)
		 *     'bits' => int - number of bits for each color (8)
		 *     'channels' => int - 3 for RGB pictures and 4 for CMYK pictures
		 *     'mime' => string - 'image/jpeg'
		 */
		$upload_photo_details = getimagesize($photo_file_details['tmp_name']);
		if (!$upload_photo_details)
		{
			//There is some problem with the Image to be uploaded. Either - not an image or some other issue
			$ok_to_upload = FALSE;
		}
		//else OK to upload Photo File

		/* 4. Upload the Original Image to the $original_image_folder */
		if ($ok_to_upload)
		{
			$photo_file_name = basename($photo_file_details['name']);
			//Use the Media Absolute Path for This Project to set the Original Image - default folder
			$original_image_target_file = $this->media_absolute_path.DIRECTORY_SEPARATOR.$original_image_folder.DIRECTORY_SEPARATOR.$photo_file_name;

			//Add this Image to the Media Filesystem
			if (substr($photo_file_details['tmp_name'], 0, 5) === 'data:') // if the image is in a data URL
			{
				list($type, $photo_file_details['tmp_name']) = explode(';', $photo_file_details['tmp_name']);
				list(, $photo_file_details['tmp_name']) = explode(',', $photo_file_details['tmp_name']);
				$photo_file_details['tmp_name'] = base64_decode($photo_file_details['tmp_name']);
				$ok_to_upload = $original_photo_uploaded = (file_put_contents($original_image_target_file, $photo_file_details['tmp_name'])) ? TRUE : FALSE;
				$this->create_system_thumbnail($photo_file_details['name'], $original_image_folder);
			}
			elseif (!move_uploaded_file($photo_file_details['tmp_name'], $original_image_target_file))
			{

				//@TODO: Add code to return an ERROR_MESSAGE for File NOT Uploaded to Media Filesystem
				//@NOTE: The move_uploaded_file() function will issue a WARNING in this case
				//@TODO: Add a code to obtain this WARNING and return it to User

				//Disable Further checks and Uploads as the main Image File has not been added to the Media filesystem
				$ok_to_upload = FALSE;
				$original_photo_uploaded = FALSE;

				//file has been Uploaded Successfully
			}
			else
			{
				$original_photo_uploaded = TRUE;
				//Create System Thumb for Original Image
				$this->create_system_thumbnail($photo_file_details['name'], $original_image_folder);
				//no need to set the $ok_to_upload file to TRUE, as it is already TRUE
			}

		}
		//end of uploading Original Image fo Media Filesystem

		/* 5. Upload the Modified according to specified Preset copy of the Original Image to the $preset_image_folder */
		if ($ok_to_upload AND $original_photo_uploaded AND $preset_image_folder !== '')
		{
			$upload_photo_size = $upload_photo_details[0].'x'.$upload_photo_details[1];
			//Create a Modified, according to the specified Preset, copy of the Uploaded Original Image
			//the apply_photo_preset() will apply the Photo Preset and create corresponding Preset Thumbs, if specified in the Preset
			$preset_applied = $this->apply_photo_preset($photo_file_details, $preset_details, $original_image_folder);
		}

		/* 6. Add a Record of the just uploaded image to the Media Database Table - for both: Original and Modified Image */
		if ($ok_to_upload)
		{
			// Original Photo was added to Media Filesystem -=> add a record of it to the Media DB Table
			// If the preset is put in the default location, it will overwrite the original image. So, only one database record is necessary.
			if ($original_photo_uploaded AND $original_image_folder != $preset_image_folder)
			{
				//Prepare Photo details to be added to Media DB
				$uploaded_photo = $this->media_absolute_path.DIRECTORY_SEPARATOR.$original_image_folder.DIRECTORY_SEPARATOR.$photo_file_name;
				$uploaded_imagesize = getimagesize($uploaded_photo);
				$image_dimensions = $uploaded_imagesize[0].'x'.$uploaded_imagesize[1];

				//Add a Record for this Uploaded Photo in the Database
				$media_id = $original_photo_added_to_db = $this->add_media_item_to_database(
					$photo_file_name, //take ONLY Photo Filename
					self::$default_image_folder, //set corresponding FOLDER_NAME which will hold this File
					@filesize($uploaded_photo), //set FileSize of the uploaded Photo
					@md5_file($uploaded_photo), //set an md5 Hash string of the uploaded Photo
					$photo_file_details['type'], //set mime_type
					$image_dimensions, //set photo dimensions
					NULL //set Photo Preset ID -=> NULL, as it is not required for the Original Image @TODO: revise this and try to allow editing of This (DEFAULT) Preset
				);

				//If the Uploaded Photo was not Recorded in DB
				if (!$original_photo_added_to_db)
				{
					//-=> ROLL BACK THIS UPLOAD, i.e. DELETE THE UPLOADED Original Photo if it was not recorded to the Database
					//REASON is to keep Media Filesystem and DB in SYNC
					//@TODO: add code to complete this roll-back
					IbHelpers::die_r(
						'There was a problem with adding Uploaded Photo to DB. <br />'.
							'TODO: Add code to set corresponding message!!!<br />'.
							'1. Roll Back this Photo Upload<br />'.
							'2. Set corresponding message'
					);
				}
			}
			//end of recording info about Uploaded Original Photo to the Database

			//Specified Preset Photo was applied -=> add a record of it to the Media DB Table
			if ($preset_applied)
			{
				//Prepare Preset Photo details to be added to Media DB
				$created_preset_photo = $this->media_absolute_path.DIRECTORY_SEPARATOR.
					'photos'.DIRECTORY_SEPARATOR.
					$preset_details['preset_directory'].DIRECTORY_SEPARATOR.
					$photo_file_name;
				$created_preset_photo_imagesize = getimagesize($created_preset_photo);
				$created_preset_photo_dimensions = $created_preset_photo_imagesize[0].'x'.$created_preset_photo_imagesize[1];

				//Add a Record for this Uploaded Photo in the Database
				$media_id = $preset_photo_added_to_db = $this->add_media_item_to_database(
					$photo_file_name, //take ONLY Photo Filename
					$preset_details['preset_directory'], //set corresponding FOLDER_NAME which will hold this File
					@filesize($created_preset_photo), //set FileSize of the uploaded Photo
					@md5_file($created_preset_photo), //set an md5 Hash string of the uploaded Photo
					$photo_file_details['type'], //set mime_type
					$created_preset_photo_dimensions, //set photo dimensions
					$preset_details['preset_id']//set Photo Preset ID
				);

				//If the Uploaded Photo was not Recorded in DB
				if (!$preset_photo_added_to_db)
				{
					//-=> ROLL BACK THIS UPLOAD, i.e. DELETE THE UPLOADED Original Photo if it was not recorded to the Database
					//REASON is to keep Media Filesystem and DB in SYNC
					//@TODO: add code to complete this roll-back
					IbHelpers::die_r(
						'There was a problem with applying of Preset to Uploaded Photo to DB. <br />'.
							'TODO: Add code to set corresponding message!!!<br />'.
							'1. Roll Back this Photo Preset<br />'.
							'2. Set corresponding message'
					);
				}
			}
			//end of recording info about Created Preset Photo copy of the Original to the Database

			//The Passed Photo has NOT been Uploaded to the Media Filesystem
		}
		else
		{
			$original_photo_uploaded = FALSE;
		}

		/* 7. Set corresponding message for Uploaded image: NOT-SUCCESS/SUCCESS - both Original and Preset */
		//Set messages for the Uploaded Original Photo
		if ($ok_to_upload AND !$original_photo_added_to_db AND $original_image_folder != $preset_image_folder)
		{
			IbHelpers::set_message(
				'There was a problem with uploading of Photo: "'.$photo_file_name.'". Please try again later.',
				'error'
			);
		}
		else if ($ok_to_upload AND $original_photo_added_to_db)
		{
			//Set message for Uploaded Photo
			IbHelpers::set_message(
				'Photo: "'.$photo_file_name.'" uploaded to Media Filesystem.',
				'success'
			);
		}
		//end of setting Return messages for the Upload of the Original Photo

		//Set messages for the Applied Preset
		//NO NEED to do this, as these messages are set by the: $this->apply_photo_preset() function

		//Return
		return $media_id;
	}

	//end of function

	private function add_image_without_resize(array $file_details, $preset_details = NULL)
	{
		$file_name = '';
		$original_uploaded = FALSE;
		$preset_uploaded = $system_thumb_uploaded = $preset_uploaded = $system_preset_thumb_uploaded = FALSE;
		$original_added_to_db = NULL;
		$upload_details = getimagesize($file_details['tmp_name']);
		$use_preset = (!is_null($preset_details) AND $preset_details['preset_directory'] != '');
		$ok_to_upload = ($file_details['error'] == 0); // (bool) $upload_details;
		$media_id = null;


		// Upload the image to the main media directory
		if (TRUE) // ($ok_to_upload)
		{
			$file_name = $file_details['name'];
			$original_folder = 'photos'.DIRECTORY_SEPARATOR.self::$default_image_folder;
			$target_file = $this->media_absolute_path.DIRECTORY_SEPARATOR.$original_folder.DIRECTORY_SEPARATOR.$file_name;
			$ok_to_upload = (bool) ($this->validate_and_prepare_media_folder($original_folder));
			$ok_to_upload = $original_uploaded = ($ok_to_upload AND move_uploaded_file($file_details['tmp_name'], $target_file));
		}

		// Upload the image to the preset directory
		if ($ok_to_upload AND $use_preset)
		{
			$preset_folder = 'photos'.DIRECTORY_SEPARATOR.strtolower(str_replace(array('-', ' '), '_', $preset_details['preset_directory']));
			$preset_target_file = $this->media_absolute_path.DIRECTORY_SEPARATOR.$preset_folder.DIRECTORY_SEPARATOR.$file_name;
			$ok_to_upload = (bool) ($this->validate_and_prepare_media_folder($preset_folder));
			$ok_to_upload = $preset_uploaded = ($ok_to_upload AND copy($target_file, $preset_target_file));
		}

		// Add original image data to table
		if ($original_uploaded)
		{
			$uploaded_photo = $this->media_absolute_path.DIRECTORY_SEPARATOR.$original_folder.DIRECTORY_SEPARATOR.$file_name;
			$uploaded_imagesize = getimagesize($uploaded_photo);
			$media_id = $original_added_to_db = $this->add_media_item_to_database($file_name, self::$default_image_folder, @filesize($uploaded_photo), @md5_file($uploaded_photo), $file_details['type'], '', NULL);
		}

		// Add preset data to table
		if ($preset_uploaded)
		{
			$uploaded_photo = $this->media_absolute_path.DIRECTORY_SEPARATOR.$preset_folder.DIRECTORY_SEPARATOR.$file_name;
			$uploaded_imagesize = getimagesize($uploaded_photo);
			$media_id = $preset_added_to_db = $this->add_media_item_to_database($file_name, $preset_details['preset_directory'], @filesize($uploaded_photo), @md5_file($uploaded_photo), $file_details['type'], '', $preset_details['preset_id']);
		}
		return $media_id;

	}

	/*
	 * Private Function - Adds/Uploads Documents to the Media Plugin.
	 * Folder used: media/docs/
	 */
	public function add_media_file(array $media_file_details, $media_file_location)
	{
//		echo "\nModel_Media->add_media_file():Add Media File to Media: $media_file_location\n";
//		IbHelpers::die_r(array('media_file_details'=>$media_file_details, 'media_file_location'=>$media_file_location));

		$file_uploaded = FALSE;
		$file_type_label = 'Document';
		$media_id = null;

		switch ($media_file_location)
		{
			case 'audios':
				$file_type_label = 'Audio File';
				break;
			case 'videos':
				$file_type_label = 'Video File';
				break;
			case 'fonts':
				$file_type_label = 'Font File';
				break;
		}

		/* 2. Check/Update the specified Media Folder for this Upload */
		if ($this->validate_and_prepare_media_folder($media_file_location))
		{
			$media_file_name = basename($media_file_details['name']);
			$extension = substr($media_file_name, strpos($media_file_name, '.'));
			$is_image = in_array($extension, array('.png', '.jpg', '.jpeg', '.gif', '.bmp', '.tiff'));
			if ($is_image) {
				$rmedia_file_location = 'photos/' . $media_file_location;
			} else {
				$rmedia_file_location = $media_file_location;
			}
			//Use the Media Absolute Path for This Project to set the Original Documents Folder
			$media_target_file = $this->media_absolute_path.DIRECTORY_SEPARATOR.$rmedia_file_location.DIRECTORY_SEPARATOR.$media_file_name;

			if (isset($media_file_details['content'])) {
				if (!file_exists($this->media_absolute_path.DIRECTORY_SEPARATOR.$rmedia_file_location)) {
					mkdir($this->media_absolute_path . DIRECTORY_SEPARATOR . $rmedia_file_location, 0777, true);
				}
				file_put_contents($media_target_file, $media_file_details['content']);
				$uploaded_file_size = strlen($media_file_details['content']);

				$media_id = $file_uploaded_to_db = $this->add_media_item_to_database(
						$media_file_name,
						$media_file_location, //folder which will hold this File
						$uploaded_file_size,
						md5($media_file_details['content']), //set an md5 Hash string of the uploaded Document
						$media_file_details['type'], //set mime_type
						null,
						null
				);
			} else if (move_uploaded_file($media_file_details['tmp_name'], $media_target_file)) ///Add this Document to the Media Filesystem
			{
				//Prepare Document details to be added to Media DB
				$uploaded_media_file = $media_target_file;
				$uploaded_file_size = $media_file_details['size'];
				$uploaded_file_dimensions = NULL;

				//NO NEED FOR creating THUMBS

				//Add a Record for this Uploaded Document in the Database
				$media_id = $file_uploaded_to_db = $this->add_media_item_to_database(
					$media_file_name,
					$media_file_location, //folder which will hold this File
					$uploaded_file_size,
					md5_file($uploaded_media_file), //set an md5 Hash string of the uploaded Document
					$media_file_details['type'], //set mime_type
					$uploaded_file_dimensions,
					NULL //set Photo Preset ID -=> NULL, as it is not required for the Documents, Audios or Videos
				);

				//If the Uploaded Document was not Recorded in DB
				if (!$file_uploaded_to_db)
				{
					//-=> ROLL BACK THIS UPLOAD, i.e. DELETE THE UPLOADED File if it was not recorded to the Database
					//REASON is to keep Media Filesystem and DB in SYNC
					//@TODO: add code to complete this roll-back
					IbHelpers::die_r(
						'There was a problem with adding of '.$file_type_label.': '.$media_file_name.' to DB. <br />'.
							'TODO: Add code to set corresponding message!!!<br />'.
							'1. Roll Back this Photo Upload<br />'.
							'2. Set corresponding message'
					);

					$file_uploaded = FALSE;
				}
				else
				{
					//File Uploaded to Media Filesystem and DB - Success
					$file_uploaded = TRUE;
					//Set corresponding message
					IbHelpers::set_message(
						$file_type_label.': "'.$media_file_name.'", uploaded to Media Filesystem.',
						'success'
					);
				}
				//There was a problem with this Document Upload
			}
			else
			{
				IbHelpers::set_message(
					'There was a problem with the Uploading of '.$file_type_label.': "'.$media_file_name.'", please try again later.',
					'error'
				);
			}
		}
		//end of uploading Document

		//Return
		return $media_id;
	}

	//end of function


	/*
	 * Private Function - Adds/Uploads Audio Files to the Media Plugin.
	 * Folder used: media/audios/
	 */
	private function add_media_audio(array $audio_file_details)
	{
		echo "\nModel_Media->add_media_audio():Add Audio-Item to Media: \n";
		IbHelpers::pre_r($audio_file_details);
		IbHelpers::die_r('UNDER CONSTRUCTION!<br />Will be ready soon!');

		//@TODO: The Media - Add Audio code goes here

		//Return

	}

	//end of function


	/*
	 * Private Function - Adds/Uploads Video Files to the Media Plugin.
	 * Folder used: media/videos/
	 */
	private function add_media_video(array $video_file_details)
	{
		echo "\nModel_Media->add_media_video(): Add Video-Item to Media: \n";
		IbHelpers::pre_r($video_file_details);
		IbHelpers::die_r('UNDER CONSTRUCTION!<br />Will be ready soon!');

		//@TODO: The Media - Add Video code goes here

		//Return

	}

	//end of function


	// Add a Media Item to the database
	private function add_media_item_to_database($file_name, $media_folder, $file_size, $hash, $mime_type, $dimensions = NULL, $preset_id = NULL)
	{
//		echo "\nModel_Media->add_media_item_to_database(): Add Media Item to DB: \n";
//		IbHelpers::die_r(
//			array(
//				'filename' => $file_name,
//				'media_folder' => $media_folder,
//				'file_size' => $file_size,
//				'hash' => $hash,
//				'dimensions' => $dimensions,
//				'preset_id' => $preset_id
//			)
//		);

		$record_exists = DB::select()->from('plugin_media_shared_media')->where('filename', '=', $file_name)->where('location', '=', $media_folder)->execute()->as_array();

		if (count($record_exists) == 0)
		{
			$insert_result = FALSE;

			// 1. get the ID of the currently-logged in user, i.e. the user who is uploading this Media Item
			$logged_in_user = Auth::instance()->get_user();

			// 2. Prepare the New Media Item Record for DB
			//Set the Media Item to be added to Database
			$item_to_add_data['filename'] = $file_name;
			$item_to_add_data['location'] = $media_folder;
			$item_to_add_data['size'] = $file_size;
			$item_to_add_data['mime_type'] = $mime_type;
			$item_to_add_data['dimensions'] = $dimensions;
			$item_to_add_data['hash'] = $hash;
			$item_to_add_data['preset_id'] = $preset_id;
			// Format the required dates for mysql storage
			$item_to_add_data['date_created'] = date('Y-m-d H:i:s');
			$item_to_add_data['created_by'] = $logged_in_user['id'];
			$item_to_add_data['owner_id'] = $logged_in_user['id'];
			$item_to_add_data['date_modified'] = $item_to_add_data['date_created'];
			$item_to_add_data['modified_by'] = $logged_in_user['id'];

			// 3. add the Media Item Record to DB
			$insert_result = DB::insert(self::$model_items_table)->values($item_to_add_data)->execute();

			// 4. Return new ID - or NULL in case of fail to write to DB
			$activity = new Model_Activity;
			$activity->set_action('upload')->set_item_type('media')->set_item_id($insert_result[0]);
			$activity->save();
			return $insert_result[0];
		}
		else
		{
			return $record_exists[0];
		}

	}

	//end of function


	/**
	 * Crops previously uploaded file and save result as "crp_$photo_file_details"
	 *
	 * @param type $photo_file_details
	 * @param type $x
	 * @param type $y
	 * @param type $w
	 * @param type $h
	 * @param type $jcw - picture is scaled in browser - visible width
	 * @param type $jch - picture is scaled in browser - visible height
	 */
	private function apply_photo_preset($uploaded_photo_file_details, $preset_details, $original_image_location)
	{
//		echo "\nModel_Media->apply_photo_preset(): Create Media - System Thumb: \n";
//		IbHelpers::die_r(
//			array(
//				'uploaded_photo_file_details'=>$uploaded_photo_file_details,
//				'preset-details'=>$preset_details,
//				'original_image_location'=>$original_image_location
//			)
//		);


		//Preset Image is not created yet
		$preset_img_created = FALSE;
		$ok_to_create_preset = FALSE;

		//Take Photo name and specified Preset Details
		$photo_name = basename($uploaded_photo_file_details['name']);
		$preset_folder = trim($preset_details['preset_directory']);
		$preset_width_large = $preset_details['preset_width_large'];
		$preset_height_large = $preset_details['preset_height_large'];
		$preset_action_large = $preset_details['preset_action_large'];
		$preset_thumb = $preset_details['preset_thumb'];
		$preset_width_thumb = $preset_details['preset_width_thumb'];
		$preset_height_thumb = $preset_details['preset_height_thumb'];
		$preset_action_thumb = $preset_details['preset_action_thumb'];

		$options = array(
			'jpeg_quality' => 100,
			'png_quality' => 9
		);

		//Get the location of the Original Copy of the Uploaded File
		$original_image = $this->media_absolute_path.DIRECTORY_SEPARATOR.$original_image_location.DIRECTORY_SEPARATOR.$photo_name;
		//Set the Preset Location for the passed Photo
		$preset_target_folder = 'photos'.DIRECTORY_SEPARATOR.$preset_folder;
		$preset_target_img_location = $this->media_absolute_path.DIRECTORY_SEPARATOR.$preset_target_folder.DIRECTORY_SEPARATOR.$photo_name;

		//Take the actual WIDTH and HEIGHT of the Original Photo
		list($original_img_width, $original_img_height) = @getimagesize($original_image);
		//Update the flag, that Creation of System Thumb is allowed
		if ($original_img_width) $ok_to_create_preset = TRUE;


		if ($ok_to_create_preset)
		{
			//Set the WIDTH and HEIGHT of the Preset Image to be created
			if ($preset_height_large == 0) $preset_height_large = $preset_width_large * ($original_img_height / $original_img_width);
			if ($preset_width_large == 0) $preset_width_large = $preset_height_large * ($original_img_width / $original_img_height);
			$preset_source_img = @imagecreatetruecolor($preset_width_large, $preset_height_large);

			// Ensure the extra space added to "fit height only" and "fix width only" is white
			$white = imagecolorallocate($preset_source_img, 255, 255, 255);
			imagefill($preset_source_img, 0, 0, $white);

			//Set Image Resource Identifier to be used for the Creation of this Photo Preset Copy, based on the Photo Type (EXTENSION)
			switch (strtolower(substr(strrchr($photo_name, '.'), 1)))
			{
				case 'jpg':
				case 'jpeg':
					$original_source_img = @imagecreatefromjpeg($original_image);
					$write_image = 'imagejpeg';
					$image_quality = $options['jpeg_quality'];
					break;
				case 'gif':
					@imagecolortransparent($preset_source_img, @imagecolorallocate($preset_source_img, 0, 0, 0));
					$original_source_img = @imagecreatefromgif($original_image);
					$write_image = 'imagegif';
					$image_quality = NULL;
					break;
				case 'png':
					@imagecolortransparent($preset_source_img, @imagecolorallocate($preset_source_img, 0, 0, 0));
					@imagealphablending($preset_source_img, FALSE);
					@imagesavealpha($preset_source_img, TRUE);
					$original_source_img = @imagecreatefrompng($original_image);
					$write_image = 'imagepng';
					$image_quality = $options['png_quality'];
					break;
				default:
					$original_source_img = NULL;
			}//end of setting the based on the Photo to be "Presetted", corresponding PHP function

			// Calculate the right values for the next function @TODO: REFACTOR THIS CODE
			switch ($preset_action_large)
			{
				case 'fit':
					$dst_height = $preset_height_large;
					$dst_width = $preset_width_large;
					$src_width = $original_img_width;
					$src_height = $original_img_height;
					$dst_x = 0;
					$dst_y = 0;
					$src_x = 0;
					$src_y = 0;
					break;

				case 'fitw':
					$dst_height = ($preset_width_large / ($original_img_width / $original_img_height));
					$dst_width = $preset_width_large;
					$src_width = $original_img_width;
					$src_height = $original_img_height;
					$dst_x = 0;
					$dst_y = ($preset_height_large - $dst_height) / 2;
					$src_x = 0;
					$src_y = 0;
					break;

				case 'fith':
					$dst_height = $preset_height_large;
					$dst_width = ($preset_height_large * ($original_img_width / $original_img_height));
					$src_width = $original_img_width;
					$src_height = $original_img_height;
					$dst_x = ($preset_width_large - $dst_width) / 2;
					$dst_y = 0;
					$src_x = 0;
					$src_y = 0;
					break;

				case 'crop':
					$dst_height = $preset_height_large;
					$dst_width = $preset_width_large;
					$src_width = $preset_width_large;
					$src_height = $preset_height_large;
					$dst_x = 0;
					$dst_y = 0;
					$src_x = ($original_img_width - $preset_width_large) / 2;
					$src_y = ($original_img_height - $preset_height_large) / 2;
					break;

				case 'cropt':
					$dst_height = $preset_height_large;
					$dst_width = $preset_width_large;
					$src_width = $preset_width_large;
					$src_height = $preset_height_large;
					$dst_x = 0;
					$dst_y = 0;
					$src_x = 0;
					$src_y = 0;
					break;

				case 'cropb':
					$dst_height = $preset_height_large;
					$dst_width = $preset_width_large;
					$src_width = $preset_width_large;
					$src_height = $preset_height_large;
					$dst_x = 0;
					$dst_y = 0;
					$src_x = 0;
					$src_y = $original_img_height - $preset_height_large;
					break;

				default:
					$dst_height = $preset_height_large;
					$dst_width = $preset_width_large;
					$src_width = $original_img_width;
					$src_height = $original_img_height;
					$dst_x = 0;
					$dst_y = 0;
					$src_x = 0;
					$src_y = 0;
					break;
			}

			//Create the Preset Image. NOTE: $write_image is one of: imagejpeg / imagegif or imagepng, which is a corresponding PHP function
			$preset_img_created = (
				$original_source_img &&
					@imagecopyresampled(
						$preset_source_img, //Destination image link resource
						$original_source_img, //Source image link resource
						$dst_x, //x-coordinate of destination point
						$dst_y, //y-coordinate of destination point
						$src_x, //x-coordinate of source point
						$src_y, //y-coordinate of source point
						$dst_width, //Destination width
						$dst_height, //Destination height
						$src_width, //Source width
						$src_height //Source height
					) &&
						$write_image(
							$preset_source_img,
							$preset_target_img_location,
							$image_quality
						)
			);

			//clear the used temporary Img resources
			@imagedestroy($original_source_img);
			@imagedestroy($preset_source_img);
		}
		//else There is no available Image to be used for creation of the Specified Preset

		//If Preset Specifies the Creation of a Preset Thumb -=> Create it
		if ($preset_thumb != 0)
		{
			$created_preset_thumb = $this->create_preset_thumbnail($uploaded_photo_file_details, $preset_details);
		}

		//Create the corresponding System Thumb for the Applied Preset Image
		$created_preset_system_thumb = $this->create_system_thumbnail($photo_name, $preset_target_folder);

		//Prepare corresponding Messages for Created Preset Image
		if ($preset_img_created)
		{
			IbHelpers::set_message(
				'Preset: "'.$preset_details['preset_title'].'" applied to Photo: "'.$photo_name.'".',
				'success'
			);
		}
		else
		{
			IbHelpers::set_message(
				'There was a problem with applying of Preset: "'.$preset_details['preset_title'].'" to Photo: "'.$photo_name.'".',
				'error'
			);
		}

		//Return
		return $preset_img_created;
	}

	//end of function


	/*
	 * Creates a Thumbnail for a previously uploaded Image
	 * Folder Used: media/photos/PLUGIN_NAME/_thumbs_cms
	 * If No Plugin name is specified, the Function will Add the passed Image to the $default_media_photos_folder - media/photos/content/
	 */
	public function create_system_thumbnail($uploaded_file_name, $uploaded_file_location_folder)
	{
//		echo "\nModel_Media->create_system_thumbnail(): Create Media - System Thumb: \n";
//		IbHelpers::die_r(array('uploaded_file_name'=>$uploaded_file_name, 'uploaded_file_location_folder'=>$uploaded_file_location_folder));

		//System Thumb is not created yet
		$sys_thumb_created = FALSE;
		$ok_to_create_sys_thumb = FALSE;
		$system_thumb_location = '';

		//Get the location of the Original Copy of the Uploaded File
		$original_image = $this->media_absolute_path.DIRECTORY_SEPARATOR.$uploaded_file_location_folder.DIRECTORY_SEPARATOR.$uploaded_file_name;

		//Set this Media DEFAULT SYSTEM Thumbs Photo Dimensions
		$sys_thumb_scale_width = 75;
		$sys_thumb_scale_height = 75;

		//Take the actual WIDTH and HEIGHT of the Uploaded Photo
		list($original_img_width, $original_img_height) = @getimagesize($original_image);
		//Update the flag, that Creation of System Thumb is allowed
		if ($original_img_width) $ok_to_create_sys_thumb = TRUE;

		//Create the System Thumb now
		if ($ok_to_create_sys_thumb)
		{

			//DO NOT add the ABSOLUTE_PATH_TO_MEDIA - This will be used AUTOMATICALLY in the validate_and_prepare_media_folder() function
			$system_thumb_location = $uploaded_file_location_folder.DIRECTORY_SEPARATOR.'_thumbs_cms';

			//Check if the corresponding System Folder Exists and is ready for Upload
			$sys_thumbs_folder_ok = $this->validate_and_prepare_media_folder($system_thumb_location);


			if ($sys_thumbs_folder_ok)
			{
				//Calculate Scale - % of how much the Original Image needs to be Scaled down to get its System Thumb
				$scale_ratio = min($sys_thumb_scale_width / $original_img_width, $sys_thumb_scale_height / $original_img_height);

				//If The Original Image was Smaller than the Thumb to be created -=> just create a copy of it in the corresponding $system_thumb_location -> _thumbs_cms folder
				if ($scale_ratio >= 1)
				{
					$sys_thumb_created = copy($original_image, $this->media_absolute_path.'/'.$system_thumb_location.DIRECTORY_SEPARATOR.$uploaded_file_name);

					//Create a scaled copy of the Original Uploaded Image for its System Thumb
				}
				else
				{

					//Get the WIDTH and HEIGHT of the Thumb to be created
					$thumb_width = $original_img_width * $scale_ratio;
					$thumb_height = $original_img_height * $scale_ratio;

					//Set the WIDTH and HEIGHT of the Thumb to be created
					$thumb_img = @imagecreatetruecolor($thumb_width, $thumb_height);

					//Set Image Resource Identifier to be used for the Creation of this Thumb, based on the Uploaded Image Type (EXTENSION)
					switch (strtolower(substr(strrchr($uploaded_file_name, '.'), 1)))
					{
						case 'jpg':
						case 'jpeg':
							$thumb_src_img = @imagecreatefromjpeg($original_image);
							$write_image = 'imagejpeg';
							$image_quality = isset($options['jpeg_quality']) ?
								$options['jpeg_quality'] : 90;
							break;
						case 'gif':
							@imagecolortransparent($thumb_img, @imagecolorallocate($thumb_img, 0, 0, 0));
							$thumb_src_img = @imagecreatefromgif($original_image);
							$write_image = 'imagegif';
							$image_quality = NULL;
							break;
						case 'png':
							@imagecolortransparent($thumb_img, @imagecolorallocate($thumb_img, 0, 0, 0));
							@imagealphablending($thumb_img, FALSE);
							@imagesavealpha($thumb_img, TRUE);
							$thumb_src_img = @imagecreatefrompng($original_image);
							$write_image = 'imagepng';
							$image_quality = isset($options['png_quality']) ?
								$options['png_quality'] : 9;
							break;
						default:
							$thumb_src_img = NULL;
					}
					//end of switch

					//Create the required System Thumb Image. NOTE: $write_image is one of: imagejpeg / imagegif or imagepng, which is a corresponding PHP function
					$sys_thumb_created = (
						$thumb_src_img &&
							@imagecopyresampled(
								$thumb_img, //Destination image link resource
								$thumb_src_img, //Source image link resource
								0, //x-coordinate of destination point
								0, //y-coordinate of destination point
								0, //x-coordinate of source point
								0, //y-coordinate of source point
								$thumb_width, //Destination width
								$thumb_height, //Destination height
								$original_img_width, //Source width
								$original_img_height //Source height
							) &&
								$write_image(
									$thumb_img,
									$this->media_absolute_path.DIRECTORY_SEPARATOR.$system_thumb_location.DIRECTORY_SEPARATOR.$uploaded_file_name,
									$image_quality
								)
					);

					//clear the used temporary Img resources
					@imagedestroy($thumb_src_img);
					@imagedestroy($thumb_img);
				}
			}
			else
			{
				//@TODO: Add some code to handle this
				IbHelpers::die_r('Folder: '.$system_thumb_location.' not ready for creation of System Thumb for: '.$uploaded_file_name);
			}
		}
		//else There is no available Image to be used for creation of System Thumb

		//Prepare corresponding Messages for Created Thumb Image
		if ($sys_thumb_created)
		{
			//There is no need to Inform about the created System Thumb -> Return Message will be Created ONLY in case of Problem with creating of this Thumb
//			IbHelpers::set_message(
//				'System Thumb for: '.
//						$this->media_relative_path.DIRECTORY_SEPARATOR.$uploaded_file_location_folder.DIRECTORY_SEPARATOR.$uploaded_file_name.
//						' was created in: '.
//						$this->media_relative_path.DIRECTORY_SEPARATOR.$system_thumb_location.DIRECTORY_SEPARATOR.' folder',
//				'success'
//			);
		}
		else
		{
			IbHelpers::set_message(
				'There was a problem with the creation of System Thumb for: '.
					$this->media_relative_path.DIRECTORY_SEPARATOR.$uploaded_file_location_folder.DIRECTORY_SEPARATOR.$uploaded_file_name.
					' in folder: '.
					$this->media_relative_path.DIRECTORY_SEPARATOR.$system_thumb_location,
				'error'
			);
		}

		//Return
		return $sys_thumb_created;
	}

	//end of function

	private function create_preset_thumbnail($photo_file_details, $preset_details)
	{
//		echo "\nModel_Media->create_preset_thumbnail(): Create Media - Preset Thumb: \n";
//		IbHelpers::die_r(array('photo_file_details'=>$photo_file_details, 'plugin_name'=>$preset_details));


		//System Thumb is not created yet
		$preset_thumb_created = FALSE;
		$ok_to_create_preset_thumb = FALSE;

		//Get the location of the Original Copy of the Uploaded File
		$preset_large_image = $this->media_absolute_path.DIRECTORY_SEPARATOR.
			'photos'.DIRECTORY_SEPARATOR.
			$preset_details['preset_directory'].DIRECTORY_SEPARATOR.
			$photo_file_details['name'];
		$preset_thumb_folder = 'photos'.DIRECTORY_SEPARATOR.
			$preset_details['preset_directory'].DIRECTORY_SEPARATOR.
			'_thumbs';
		$preset_thumb_target_image = $this->media_absolute_path.DIRECTORY_SEPARATOR.$preset_thumb_folder.DIRECTORY_SEPARATOR.$photo_file_details['name'];

		//Take the actual WIDTH and HEIGHT of the Uploaded Photo
		list($preset_large_img_width, $preset_large_img_height) = @getimagesize($preset_large_image);
		//Update the flag, that Creation of System Thumb is allowed
		if ($preset_large_img_width) $ok_to_create_preset_thumb = TRUE;

		//Create the System Thumb now
		if ($ok_to_create_preset_thumb)
		{

			//Get the WIDTH and HEIGHT of the Thumb to be created
			$preset_thumb_width = $preset_details['preset_width_thumb'];
			$preset_thumb_height = $preset_details['preset_height_thumb'];
			$preset_thumb_action = $preset_details['preset_action_thumb'];

			//DO NOT add the ABSOLUTE_PATH_TO_MEDIA - This will be used AUTOMATICALLY in the validate_and_prepare_media_folder() function
			$system_thumb_location = $preset_details['preset_directory'].DIRECTORY_SEPARATOR.'_thumbs';

			//Check if the corresponding System Folder Exists and is ready for Upload
			$preset_thumbs_folder_ok = $this->validate_and_prepare_media_folder($preset_thumb_folder);


			if ($preset_thumbs_folder_ok)
			{

				//If The Preset Image was same as the Specified Preset Thumb dimensions -=> just create a copy of it in the corresponding $system_thumb_location -> _thumbs_cms folder
				if (
					($preset_thumb_width == $preset_large_img_width) AND
					($preset_thumb_height == $preset_large_img_height)
				)
				{
					$preset_thumb_created = copy($preset_large_image, $preset_thumb_target_image);

					//Create a scaled copy of the Original Uploaded Image for its System Thumb
				}
				else
				{

					//Set the WIDTH and HEIGHT of the Thumb to be created
					if ($preset_thumb_height == 0) $preset_thumb_height = $preset_thumb_width * ($preset_large_img_height / $preset_large_img_width);
					if ($preset_thumb_width == 0) $preset_thumb_width = $preset_thumb_height * ($preset_large_img_width / $preset_large_img_height);
					$preset_thumb_img = @imagecreatetruecolor($preset_thumb_width, $preset_thumb_height);

					// Ensure the extra space added to "fit height only" and "fix width only" is white
					$white = imagecolorallocate($preset_thumb_img, 255, 255, 255);
					imagefill($preset_thumb_img, 0, 0, $white);

					//Set Image Resource Identifier to be used for the Creation of this Thumb, based on the Uploaded Image Type (EXTENSION)
					switch (strtolower(substr(strrchr($photo_file_details['name'], '.'), 1)))
					{
						case 'jpg':
						case 'jpeg':
							$preset_thumb_src_img = @imagecreatefromjpeg($preset_large_image);
							$write_image = 'imagejpeg';
							$image_quality = isset($options['jpeg_quality']) ?
								$options['jpeg_quality'] : 90;
							break;
						case 'gif':
							@imagecolortransparent($preset_thumb_img, @imagecolorallocate($preset_thumb_img, 0, 0, 0));
							$preset_thumb_src_img = @imagecreatefromgif($preset_large_image);
							$write_image = 'imagegif';
							$image_quality = NULL;
							break;
						case 'png':
							@imagecolortransparent($preset_thumb_img, @imagecolorallocate($preset_thumb_img, 0, 0, 0));
							@imagealphablending($preset_thumb_img, FALSE);
							@imagesavealpha($preset_thumb_img, TRUE);
							$preset_thumb_src_img = @imagecreatefrompng($preset_large_image);
							$write_image = 'imagepng';
							$image_quality = isset($options['png_quality']) ?
								$options['png_quality'] : 9;
							break;
						default:
							$preset_thumb_src_img = NULL;
					}//end of switch

					// Calculate the right values for the next function @TODO: REFACTOR THIS CODE
					switch ($preset_thumb_action)
					{
						case 'fit':
							$dst_height = $preset_thumb_height;
							$dst_width = $preset_thumb_width;
							$src_width = $preset_large_img_width;
							$src_height = $preset_large_img_height;
							$dst_x = 0;
							$dst_y = 0;
							$src_x = 0;
							$src_y = 0;
							break;

						case 'fitw':
							$dst_height = ($preset_thumb_width / ($preset_large_img_width / $preset_large_img_height));
							$dst_width = $preset_thumb_width;
							$src_width = $preset_large_img_width;
							$src_height = $preset_large_img_height;
							$dst_x = 0;
							$dst_y = ($preset_thumb_height - $dst_height) / 2;
							$src_x = 0;
							$src_y = 0;
							break;

						case 'fith':
							$dst_height = $preset_thumb_height;
							$dst_width = ($preset_thumb_height * ($preset_large_img_width / $preset_large_img_height));
							$src_width = $preset_large_img_width;
							$src_height = $preset_large_img_height;
							$dst_x = ($preset_thumb_width - $dst_width) / 2;
							$dst_y = 0;
							$src_x = 0;
							$src_y = 0;
							break;

						case 'crop':
							$dst_height = $preset_thumb_height;
							$dst_width = $preset_thumb_width;
							$src_width = $preset_thumb_width;
							$src_height = $preset_thumb_height;
							$dst_x = 0;
							$dst_y = 0;
							$src_x = ($preset_large_img_width - $preset_thumb_width) / 2;
							$src_y = ($preset_large_img_height - $preset_thumb_height) / 2;
							break;

						case 'cropt':
							$dst_height = $preset_thumb_height;
							$dst_width = $preset_thumb_width;
							$src_width = $preset_thumb_width;
							$src_height = $preset_thumb_height;
							$dst_x = 0;
							$dst_y = 0;
							$src_x = 0;
							$src_y = 0;
							break;

						case 'cropb':
							$dst_height = $preset_thumb_height;
							$dst_width = $preset_thumb_width;
							$src_width = $preset_thumb_width;
							$src_height = $preset_thumb_height;
							$dst_x = 0;
							$dst_y = 0;
							$src_x = 0;
							$src_y = $preset_large_img_height - $preset_thumb_height;
							break;

						default:
							$dst_height = $preset_thumb_height;
							$dst_width = $preset_thumb_width;
							$src_width = $preset_large_img_width;
							$src_height = $preset_large_img_height;
							$dst_x = 0;
							$dst_y = 0;
							$src_x = 0;
							$src_y = 0;
							break;
					}

					//Create the required System Thumb Image. NOTE: $write_image is one of: imagejpeg / imagegif or imagepng, which is a corresponding PHP function
					$preset_thumb_created = (
						$preset_thumb_src_img &&
							@imagecopyresampled(
								$preset_thumb_img, //Destination image link resource
								$preset_thumb_src_img, //Source image link resource
								$dst_x, //x-coordinate of destination point
								$dst_y, //y-coordinate of destination point
								$src_x, //x-coordinate of source point
								$src_y, //y-coordinate of source point
								$dst_width, //Destination width
								$dst_height, //Destination height
								$src_width, //Source width
								$src_height //Source height
							) &&
								$write_image(
									$preset_thumb_img,
									$preset_thumb_target_image,
									$image_quality
								)
					);

					//clear the used temporary Img resources
					@imagedestroy($preset_thumb_src_img);
					@imagedestroy($preset_thumb_img);
				}
			}
			else
			{
				//@TODO: Add some code to handle this
				IbHelpers::die_r('Folder: '.$preset_thumb_folder.' not ready for creation of System Thumb for: '.$photo_file_details['name']);
			}
		}
		//else There is no available Image to be used for creation of System Thumb

		//Prepare corresponding Messages for Created Thumb Image
		if ($preset_thumb_created)
		{
			//There is no need to Inform about the created System Thumb -> Return Message will be Created ONLY in case of Problem with creating of this Thumb
//			IbHelpers::set_message(
//				'System Thumb for: '.
//						$this->media_relative_path.DIRECTORY_SEPARATOR.$preset_details['preset_directory'].DIRECTORY_SEPARATOR.$photo_file_details['name'].
//						' was created in: '.
//						$this->media_relative_path.DIRECTORY_SEPARATOR.$preset_thumb_folder.DIRECTORY_SEPARATOR.' folder',
//				'success'
//			);
		}
		else
		{
			IbHelpers::set_message(
				'There was a problem with the creation of Preset Thumb for: '.
					$this->media_relative_path.DIRECTORY_SEPARATOR.$preset_details['preset_directory'].DIRECTORY_SEPARATOR.$photo_file_details['name'].
					' in folder: '.
					$this->media_relative_path.DIRECTORY_SEPARATOR.$preset_thumb_folder,
				'error'
			);
		}

		//Return
		return $preset_thumb_created;


	}

	//end of function


	//Create a Folder Array Structure based on the passed String, which will be passed to the build_media_folder() function for further creation
	private function create_folder_structure_from_string($folder_structure_string)
	{
//		echo "\nModel_Media->create_folder_structure_from_string(): Create Media Folder form a String: \n";
//		IbHelpers::die_r($folder_structure_string);

		$structure_created = FALSE;
		$folder_structure = Array();


		//Split the Folder String into Array, for further processing
		$temp_folder_to_build = explode(DIRECTORY_SEPARATOR, $folder_structure_string);
		/*
		 * Prepare the Folder to be Build structure. Must be in the Form:
		 * Array(
		 * 	[folder_name] => Array(
		 * 		[sub_folder_name] => Array(
		 *			[_thumbs] => Array()
		 * 			[_thumbs_cms] => Array()
		 *		)
		 * 	)
		 * )
		 * when the Folder has some Sub-folders and:
		 * Array(
		 *	[folder_name] => Array(
		 *		[_thumbs] => Array()
		 * 		[_thumbs_cms] => Array()
		 * )
		 * when the Folder is just a Folder with the corresponding "required" Thumbs folders
		 *
		 * NOTE: Media Folders which DO NOT Require Sub-Folders are specified in: $this->$folders_with_no_thumbs
		 *       and will have the following Structure:
		 * Array(
		 *  [folder_name] => Array()
		 * )
		 */
		if (is_array($temp_folder_to_build) AND count($temp_folder_to_build) > 0)
		{
			//take the size of this folder to build
			$folder_size = count($temp_folder_to_build);

			//if the Size of this Folder is 1 -=> create the folder with its default Thumbs Folders. Skip Empty Folder Names, i.e. EMPTY_STRINGS
			if ($folder_size == 1 AND $temp_folder_to_build[0] != '')
			{
				//Set _thumbs and_thumbs_cms sub-folders
				if (!in_array($temp_folder_to_build[0], $this->folders_with_no_thumbs))
				{
					$folder_structure = Array(
						$temp_folder_to_build[0] => Array(
							'_thumbs' => Array(),
							'_thumbs_cms' => Array()
						)
					);
					//This Media Folder DOES NOT Require THUMBS Sub-folders, just set the Folder
				}
				else
				{
					$folder_structure = Array(
						$temp_folder_to_build[0] => Array()
					);
				}

				//This Folder has some Sub-folders -=> Call this function Recursively, for the remaining Sub-Folders
			}
			else if ($folder_size > 1)
			{
				$folder_name = $temp_folder_to_build[0];
				//Remove the first element in this array, as it will hold the remaining Sub-folders in this Folder
				unset($temp_folder_to_build[0]);
				$folder_structure = Array(
					$folder_name => $this->create_folder_structure_from_string(implode(DIRECTORY_SEPARATOR, $temp_folder_to_build))
				);
			}
			//The passed Folder name / path i snot valid
		}
		else
		{
			//@TODO: ADD SOME CODE TO HANDLE THIS
			IbHelpers::die_r(
				'Model_Media->create_folder_structure_from_string():<br />'.
					'System Error: The string: "'.$folder_structure_string.'" could not be converted into proper Folder Array Structure for further processing',
				'error'
			);
		}

		//Return
		return $folder_structure;
	}

	//end of function


	/**
	 * Private Function used to get the Current Structure of the Media Filesystem for current Project (Web App) into array.
	 *
	 * @param $path_to_media_folder
	 * @return array - Associative Array, representing the Actual (Current) Folder Structure of the Media Folder.<br />
	 *                   Example:<br />
	 * <pre>
	 *                   Array(
	 *   [media] => Array(
	 *       [docs] => Array()
	 *       [photos] => Array(
	 *           [content] => Array(
	 *              [_thumbs] => Array ()
	 *              [_thumbs_cms] => Array()
	 *           )
	 *           ...
	 *       )
	 *       [videos] => Array()
	 *   )
	 * )
	 * </pre>
	 *
	 */
	private function get_current_media_structure($path_to_media_folder)
	{
//		echo "\nModel_Media->get_current_media_structure(): Get Media Folder Structure: \n";
//		IbHelpers::die_r($path_to_media_folder);

		$folder_structure = array();

		//Get the Structure of this Folder - ONLY if the Specified path is a Folder, i.e. SKIP Files
		if (is_dir($path_to_media_folder))
		{
			//Get the Specified Folder Structure Initially
			$folder_content = ""; //scandir($path_to_media_folder);

			if ($folder_content)
			{
				//Get the Structure of this Folder
				foreach ($folder_content as $key => $folder_sub_item)
				{
					//skip This Folder's: current (.) and parent(..) directories, as not required
					if (
						in_array(
							$folder_sub_item,
							array('.', '..')
						)
					) continue;
					//Get the Structure of this Folder (Sub-Folder)
					if (is_dir($path_to_media_folder.DIRECTORY_SEPARATOR.$folder_sub_item))
					{
						//This is a folder -=> get its structure by calling this function recursively
						$folder_structure[$folder_sub_item] = $this->get_current_media_structure($path_to_media_folder.DIRECTORY_SEPARATOR.$folder_sub_item);
					}
					//else this is a File -=> skip it as not Required to be listed in the Media - Filesystem (Folder-Structure)
				}
			}
		}
		//end of outer if
		//clear the file status cache - after using is_dir()
		clearstatcache();

		//Return
		return $folder_structure;
	}

	//end of function


	//Find if a Folder exists in the Current Media Filesystem
	private function check_if_media_folder_exists($folder_to_search_for, array $media_folder_to_search_in = NULL, $recursively_called = FALSE)
	{
//		echo "\nModel_Media->check_if_media_folder_exists(): Check if Media Folder Exists: \n";
//		IbHelpers::die_r(array($folder_to_search_for, $media_folder_to_search_in, $recursively_called));

		$folder_found = FALSE;

		//Default the Media Folder to search for to: $this->current_media_structure - ONLY when called Externally
		//NOTE: This is a Multi-dimensional Array
		if (empty($media_folder_to_search_in) AND !$recursively_called) $media_folder_to_search_in = $this->current_media_structure;

		//Check if the specified $folder_to_search_for Exists in the passed $media_folder_to_search_in
		$folder_found = array_key_exists($folder_to_search_for, $media_folder_to_search_in);

		//Key (Folder) Not Found - continue to search within the Media Folder
		if (!$folder_found AND count($media_folder_to_search_in) > 0)
		{
			$folder_found = $this->check_if_media_folder_exists($folder_to_search_for, reset($media_folder_to_search_in), TRUE);
		}

		//Return
		return $folder_found;
	}

	//end of function

	/**
	 * Return a list of non empty directories.
	 *
	 * @return Kohana_Database_Query_Builder_Select
	 */
	public function get_location_list()
	{
		$location_list = DB::select('location')
			->from(self::$model_items_table)
			->distinct(TRUE)
			->order_by('location')
			->execute()
			->as_array();

		return $location_list;
	}

	/* END of Back-End (CMS) : Admin Functions */

	public static function get_fonts($filepaths = FALSE)
	{
		$model = new Model_Media;
		$filepath = ($filepaths) ? $model->get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder, '', 'fonts') : '';
		$font_files = $model->get_all_items_based_on('location', 'fonts', 'details', '=', NULL, 'filename');
		$fonts = array();
		$i = 0;
		foreach ($font_files as $file)
		{
			if ($filepaths)
			{
				$fonts[$i]['src'] = $filepath.$file['filename'];
				$fonts[$i]['name'] = preg_replace('/[-|\.].*/', '', preg_replace('/([a-z])([A-Z])/', '$1 $2', $file['filename']));
			}
			else
			{
				// Convert camel case to spaced words and take out content after "-" or ".".
				// e.g. "DancingScript-Regular.ttf" -> "Dancing Script"
				$fonts[] = preg_replace('/[-|\.].*/', '', preg_replace('/([a-z])([A-Z])/', '$1 $2', $file['filename']));
			}
			$i++;
		}
		return $fonts;
	}


	/* ########## - ########## */


	/* Front-End : APP Functions  */
	/*
	 * The Functions Bellow are HELPER Functions, which will be used to provide Functionalities for the Front-End rendering of THIS Plugin Items
	 */
	public static function get_path_to_media_item_admin($project_media_folder = NULL, $file_to_get, $file_location)
	{
		$file_to_get = str_replace(' ', '%20', $file_to_get);
		if ($project_media_folder != '')
		{
			//build shared path the media file as 'shared_media/tippecanoe/media' instead of 'media/photos/aaaa.jpg'
			$path_to_file = URL::base().
				'shared_media'.
				DIRECTORY_SEPARATOR.
				$project_media_folder.
				DIRECTORY_SEPARATOR.
				self::MEDIA_RELATIVE_PATH.
				DIRECTORY_SEPARATOR.
				((in_array($file_location, array('docs', 'audios', 'videos', 'fonts'))) ? '' : 'photos'.DIRECTORY_SEPARATOR). //if file location has docs | audios | videos then don't use photos/
				$file_location.
				DIRECTORY_SEPARATOR.
				$file_to_get;
		}
		else
		{ // Deprecated. This should not be reached.
		  //to output 'http://tippecanoe.websitecms.dev/media/photos/content/xxx.jpg'

			$path_to_file = URL::base().
				self::MEDIA_RELATIVE_PATH.
				DIRECTORY_SEPARATOR.
				((in_array($file_location, array('docs', 'audios', 'videos', 'fonts'))) ? '' : 'photos'.DIRECTORY_SEPARATOR). //if file location has docs | audios | videos then don't use photos/
				$file_location.
				DIRECTORY_SEPARATOR.
				$file_to_get;
		}

		$pathinfo = pathinfo($file_to_get);
		if (isset($pathinfo['extension']) AND ($pathinfo['extension'] == 'svg' || $pathinfo['extension'] == 'ico'))
		{
			$path_to_file = str_replace('_thumbs_cms/', '/', str_replace('/_thumbs/', '/', $path_to_file));
		}

		//Return Path to Media Item
		return $path_to_file;
	}

    public static function get_image_path($filename, $directory = 'content', $args = array())
    {
        $path = self::get_path_to_media_item_admin(
            Kohana::$config->load('config')->project_media_folder,
            $filename,
            $directory
        );

        if (!empty($args['cachebust'])) {
            $data = self::get_by_filename($filename, $directory);
            if (isset($data['date_modified'])) {
                $path .= '?ts='.date('U', strtotime($data['date_modified']));
            }
        }

        return $path;
    }

	//end of function

    // Get the URL path to media
	public static function get_path_to_id($id, $thumbs = false)
	{
		$media = DB::select('*')
				->from('plugin_media_shared_media')
				->where('id', '=', $id)
				->execute()
				->current();
		if ($media) {
			return Model_Media::get_path_to_media_item_admin(
				Kohana::$config->load('config')->project_media_folder,
				$media['filename'],
				$media['location'] . ($thumbs ? '/' . '_thumbs_cms' : '')
			);
		} else {
			return null;
		}
	}

    // Get the code path to media
    public static function get_localpath_to_id($id, $thumbs = false)
    {
        $media = DB::select('*')
            ->from('plugin_media_shared_media')
            ->where('id', '=', $id)
            ->execute()
            ->current();
        if ($media) {
            $project_media_folder = Kohana::$config->load('config')->project_media_folder;
            if ($media['location'] !== 'docs' && $media['location'] !== 'audios' && $media['location'] !== 'videos' && $media ['location'] !== 'fonts') {
                $media['location'] = "/photos/{$media['location']}";
            }

            $base_path = ENGINEPATH.'www/shared_media/'.$project_media_folder;
            $file_path = $media['location'].'/'.($thumbs ? '_thumbs_cms/' : '').$media['filename'];

            // Some sites use an extra "media" subfolder. Others don't.
            if (file_exists($base_path.'/media/'.$file_path)) {
                return $base_path.'/media/'.$file_path;
            } else {
                return $base_path.'/'.$file_path;
            }

        } else {
            return null;
        }
    }
	/* END of Front-End : APP Functions */


	/** Options for settings **/
	public static function get_background_images_as_options($selected_image)
	{
		$images = DB::select()->from('plugin_media_shared_media')->where('location', '=', 'bg_images')->execute()->as_array();
		$return = '<option value="">-- None Selected -- </option>';
		foreach ($images as $image)
		{
			$selected = ($selected_image == $image['filename']) ? ' selected="selected"' : '';
			$return .= '<option value="'.$image['filename'].'"'.$selected.'>'.$image['filename'].'</option>';
		}

		return $return;
	}

	public static function get_logos_as_options($selected_image)
	{
		$images = DB::select()->from('plugin_media_shared_media')->where('location', '=', 'logos')->execute()->as_array();
		$return = '<option value="">-- None Selected -- </option>';
		foreach ($images as $image)
		{
			$selected = ($selected_image == $image['filename']) ? ' selected="selected"' : '';
			$return .= '<option value="'.$image['filename'].'"'.$selected.'>'.$image['filename'].'</option>';
		}

		return $return;
	}

    public static function get_favicons_as_options($selected_image)
    {
        $images = DB::select()->from('plugin_media_shared_media')->where('location', '=', 'favicons')->execute()->as_array();
        $return = '<option value="">-- None Selected -- </option>';

        foreach ($images as $image) {
            $selected = ($selected_image == $image['filename']) ? ' selected="selected"' : '';
            $return .= '<option value="'.$image['filename'].'"'.$selected.'>'.$image['filename'].'</option>';
        }

        return $return;
    }

	public static function get_background_repeat_options()
	{
		return array(
			array('value' => 'no-repeat', 'label' => 'Don&apos;t tile'),
			array('value' => 'repeat', 'label' => 'Tile'),
			array('value' => 'repeat-x', 'label' => 'Tile horizontally'),
			array('value' => 'repeat-y', 'label' => 'Tile vertically')
		);
	}

	public static function get_background_attachment_options()
	{
		return array(
			array('value' => 'fixed', 'label' => 'Fixed'),
			array('value' => 'scroll', 'label' => 'Scroll')
		);
	}


	public static function get_for_datatable($filters)
	{
        $selection_dialog = $filters['selection_dialog'];
        unset($filters['selection_dialog']);

		$path = Model_Media::get_path_to_media_item_admin(Kohana::$config->load('config')->project_media_folder,'', '');

		$columns   = array();
		$columns[] = ''; // thumbnail
		$columns[] = 'media.filename';
		$columns[] = 'media.location';
		$columns[] = 'media.dimensions';
		$columns[] = 'media.size';
		$columns[] = 'media.date_modified';
		$columns[] = 'modified_by.email';


		$logged_in_user = Auth::instance()->get_user();
		$logged_user_id = $logged_in_user['id'];
		$can_edit_everything = Auth::instance()->has_access('media');

		$q = DB::select('media.*', array('modified_by.email', 'modified_by_email'))
			->from(array(self::$model_items_table, 'media'))
			->join(array('engine_users', 'modified_by'), 'LEFT')
			->on('media.modified_by', '=', 'modified_by.id');

		if (!$can_edit_everything) {
			$q->and_where('media.owner_id', '=', $logged_user_id);
		}

		// Global search
		if (isset($filters['sSearch']) AND $filters['sSearch'] != '')
		{
			$q->and_where_open();
			for ($i = 0; $i < count($columns); $i++)
			{
				if (isset($filters['bSearchable_'.$i]) AND $filters['bSearchable_'.$i] == "true" AND $columns[$i] != '')
				{
					$q->or_where($columns[$i],'like','%'.$filters['sSearch'].'%');
				}
			}
			$q->and_where_close();
		}
		// Individual column search
		for ($i = 0; $i < count($columns); $i++)
		{
			if (isset($filters['bSearchable_'.$i]) AND $filters['bSearchable_'.$i] == "true" AND $filters['sSearch_'.$i] != '')
			{
				$q->and_where($columns[$i],'like','%'.$filters['sSearch_'.$i].'%');
			}
		}

		// $q_all will be used to count the total number of records.
		// It's largely the same as the main query, but won't be paginated
		$q_all = clone $q;

		// Limit. Only show the number of records for this paginated page
		if (isset($filters['iDisplayLength']) AND $filters['iDisplayLength'] != -1)
		{
			$q->limit(intval($filters['iDisplayLength']));
			if (isset($filters['iDisplayStart']))
			{
				$q->offset(intval($filters['iDisplayStart']));
			}
		}
		// Order
		if (isset($filters['iSortCol_0']) AND $filters['iSortCol_0'])
		{
			for ($i = 0; $i < $filters['iSortingCols']; $i++)
			{
				if ($columns[$filters['iSortCol_'.$i]] != '')
				{
					$q->order_by($columns[$filters['iSortCol_'.$i]], $filters['sSortDir_'.$i]);
				}
			}
		}
		$q->order_by('media.date_modified', 'desc');

		$results = $q->execute()->as_array();

		$output['iTotalDisplayRecords'] = $q_all->execute()->count(); // total number of results
		$output['iTotalRecords']        = $q->execute()->count(); // displayed results
		$output['aaData']               = array();

		$media_folder = Kohana::$config->load('config')->project_media_folder;

		// Data to appear in the outputted table cells
		foreach ($results as $result)
		{
            if (!$can_edit_everything && $result['owner_id'] !== $logged_user_id) {
                continue;
            }

			$edit_link = '';

			if (current(explode('/', $result['mime_type'])) === 'image')
			{
				$is_image = TRUE;

                $edit_link = '<button type="button" class="btn-link edit-link" data-preset="' . $result['preset_id'] . '"><span class="icon-pencil"></span>&nbsp;Edit</button>';

                $is_svg = ($result['mime_type'] == 'image/svg+xml');
                $is_ico = (substr($result['filename'], strrpos($result['filename'], '.') + 1) == 'ico');

				$thumb_path = ($is_svg || $is_ico) ? $path.$result['location'].'/'.$result['filename'] : $path.$result['location'].'/_thumbs_cms/'.$result['filename'];
				$item_path = str_replace('/_thumbs_cms/', '/', $thumb_path);
				$thumb = '<a href="'.$item_path.'"><img src="'.$thumb_path.'" alt="" /></a>';
			}
			else
			{
				$is_image = FALSE;
				$item_path = Model_Media::get_path_to_media_item_admin($media_folder,$result['filename'], $result['location']);
				$thumb = ' <a href="'.$item_path.'" class="icon-file">&nbsp;</a>';
			}

            $delete_link = '<a href="#" class="delete_'.($is_image ? 'photos' : $result['location']).'" id="delete_'.$result['id'].'" onclick="delete_media_item(this);"><span class="icon-remove-circle"></span>&nbsp;Delete</a>';

			$row = array();
			$row[] = $thumb . '<br />' . $result['filename'];
			//$row[] = $result['filename'];
			$row[] = $result['location'];
			$row[] = $result['dimensions'];
			$row[] = $result['size'];
			$row[] = IbHelpers::relative_time_with_tooltip($result['date_modified']);
			$row[] = $result['modified_by_email'];
			$row[] = ($selection_dialog == TRUE ? '<a tabindex="0"
						data-url="'.$item_path.'"
					   	data-thumb-url="'.$item_path.'"
                        data-id="'.$result['id'].'"
                        data-filename="'.$result['filename'].'"><span class="icon-download"></span>&nbsp;select</a>' : '') .
					($is_image ? '<a href="'.$item_path.'" target="_blank"><span class="icon-eye-open"></span>&nbsp;View</a>' : '') .
					$edit_link .
                    '<a href="'.$item_path.'" download="' . $result['filename'] . '"><span class="icon-download"></span>&nbsp;Download</a>' .
					$delete_link;

			$output['aaData'][] = $row;
		}

		return json_encode($output);

	}


	// Save an image using result of cropperjs
	public function saveImageFromString($image, $filename, $mime, $presetId = 'custom')
	{
        if (is_numeric($presetId)) {
            $preset = DB::select('*')
                ->from('plugin_media_shared_media_photo_presets')
                ->where('id', '=', $presetId)
                ->execute()
                ->current();
        } else {
            $preset = NULL;
        }
        $directory = (!empty($preset['directory'])) ? $preset['directory'] : 'content';
        $file_path = 'photos/' . $directory . '/';
        $this->validate_and_prepare_media_folder('photos/' . $directory);
        if (Kohana::$config->load('config')->project_media_folder != '') {
            $file_path = 'shared_media' . DIRECTORY_SEPARATOR . Kohana::$config->load('config')->project_media_folder . DIRECTORY_SEPARATOR . $file_path;
        }
        $file = $file_path . basename($filename);
        $file_thumb = $file_path . '_thumbs/' . basename($filename);
        $file_thumb_cms = $file_path . '_thumbs_cms/' . basename($filename);

        $image_p = imagecreatefromstring($image);
        $pWidth = $width = imagesx($image_p);
        $pHeight = $height = imagesy($image_p);
        file_put_contents($file, $image);


        self::smart_resize_image($file, NULL, 75, 75, FALSE, $file_thumb_cms, FALSE, FALSE, 85);

        // thumbnail, if applicable
        if (isset($preset['thumb']) AND $preset['thumb'] != 0){
            // If width or height is 0, use the width or height necessary to maintain the ratio of the original image
            if ($preset['width_thumb'] == 0){
                $preset['width_thumb'] = round($pWidth * ($preset['height_thumb'] / $preset['height_large']));
            }

            if ($preset['height_thumb'] == 0){
                $preset['height_thumb'] = round($pHeight * ($preset['width_thumb'] / $preset['width_large']));
            }

            self::smart_resize_image($file, NULL, $preset['width_thumb'], $preset['height_thumb'], FALSE, $file_thumb, FALSE, FALSE, 85);
        }

        if (!is_null($preset)) {
			// If width or height is 0, use the width or height necessary to maintain the ratio of the original image
			if ($preset['width_large'] == 0){
				$preset['width_large'] = round($pWidth);
			}

			if ($preset['height_large'] == 0){
				$preset['height_large'] = $pHeight;
			}

            self::smart_resize_image($file, NULL, $preset['width_large'], $preset['height_large'], FALSE, $file, FALSE, FALSE, 85);
		}

		$logged_in_user = Auth::instance()->get_user();

        $item_data = array();
		$item_data['size'] = filesize($file);
		$item_data['mime_type'] = $mime;
		if (isset($preset['width_large'])) {
			$item_data['dimensions'] = max($preset['width_large'], $width) .'x'.max($preset['height_large'], $height);
		} else {
			$item_data['dimensions'] = $pWidth.'x'.$pHeight;
		}
		$item_data['hash'] = md5_file($file);
		$item_data['preset_id'] = $preset['id'];
		$item_data['date_modified'] = date('Y-m-d H:i:s');
		$item_data['modified_by'] = $logged_in_user['id'];

		// Check if this filename name has already been used
		$name_used = DB::select()->from('plugin_media_shared_media')->where('filename', '=', $filename)->and_where('location', '=', $directory)->execute()->as_array();

		if (sizeof($name_used) > 0){
			$id = $name_used[0]['id'];
			$update_result = DB::update(self::$model_items_table)->set($item_data)->where('id', '=', $id)->execute();
		} else {
			$item_data['filename'] = $filename;
			$item_data['location'] = $directory;
			$item_data['date_created'] = $item_data['date_modified'];
			$item_data['created_by'] = $item_data['modified_by'];

			$insert_result = DB::insert(self::$model_items_table)->values($item_data)->execute();
		}

		return $file;
	}

    /**
     * easy image resize function
     * @param  $file - file name to resize
     * @param  $string - The image data, as a string
     * @param  $width - new image width
     * @param  $height - new image height
     * @param  $proportional - keep image proportional, default is no
     * @param  $output - name of the new file (include path if needed)
     * @param  $delete_original - if true the original image will be deleted
     * @param  $use_linux_commands - if set to true will use "rm" to delete the image, if false will use PHP unlink
     * @param  $quality - enter 1-100 (100 is best quality) default is 100
     * @return boolean|resource
     */
    public static function smart_resize_image($file,
        $string             = NULL,
        $width              = 0,
        $height             = 0,
        $proportional       = FALSE,
        $output             = 'file',
        $delete_original    = TRUE,
        $use_linux_commands = FALSE,
        $quality = 100
    ) {

        if ( $height <= 0 && $width <= 0 ) return FALSE;
        if ( $file === NULL && $string === NULL ) return FALSE;

        # Setting defaults and meta
        $info                         = $file !== NULL ? getimagesize($file) : getimagesizefromstring($string);
        $image                        = '';
        $final_width                  = 0;
        $final_height                 = 0;
        list($width_old, $height_old) = $info;
        $cropHeight = $cropWidth = 0;

        # Calculating proportionality
        if ($proportional) {
            if      ($width  == 0)  $factor = $height/$height_old;
            elseif  ($height == 0)  $factor = $width/$width_old;
            else                    $factor = min( $width / $width_old, $height / $height_old );

            $final_width  = round( $width_old * $factor );
            $final_height = round( $height_old * $factor );
        }
        else {
            $final_width = ( $width <= 0 ) ? $width_old : $width;
            $final_height = ( $height <= 0 ) ? $height_old : $height;
            $widthX = $width_old / $width;
            $heightX = $height_old / $height;

            $x = min($widthX, $heightX);
            $cropWidth = ($width_old - $width * $x) / 2;
            $cropHeight = ($height_old - $height * $x) / 2;
        }

        # Loading image to memory according to type
        switch ( $info[2] ) {
            case IMAGETYPE_JPEG:  $file !== NULL ? $image = imagecreatefromjpeg($file) : $image = imagecreatefromstring($string);  break;
            case IMAGETYPE_GIF:   $file !== NULL ? $image = imagecreatefromgif($file)  : $image = imagecreatefromstring($string);  break;
            case IMAGETYPE_PNG:   $file !== NULL ? $image = imagecreatefrompng($file)  : $image = imagecreatefromstring($string);  break;
            default: return FALSE;
        }


        # This is the resizing/resampling/transparency-preserving magic
        $image_resized = imagecreatetruecolor( $final_width, $final_height );
        if ( ($info[2] == IMAGETYPE_GIF) || ($info[2] == IMAGETYPE_PNG) ) {
            $transparency = imagecolortransparent($image);
            $palletsize = imagecolorstotal($image);

            if ($transparency >= 0 && $transparency < $palletsize) {
                $transparent_color  = imagecolorsforindex($image, $transparency);
                $transparency       = imagecolorallocate($image_resized, $transparent_color['red'], $transparent_color['green'], $transparent_color['blue']);
                imagefill($image_resized, 0, 0, $transparency);
                imagecolortransparent($image_resized, $transparency);
            }
            elseif ($info[2] == IMAGETYPE_PNG) {
                imagealphablending($image_resized, FALSE);
                $color = imagecolorallocatealpha($image_resized, 0, 0, 0, 127);
                imagefill($image_resized, 0, 0, $color);
                imagesavealpha($image_resized, TRUE);
            }
        }
        imagecopyresampled($image_resized, $image, 0, 0, $cropWidth, $cropHeight, $final_width, $final_height, $width_old - 2 * $cropWidth, $height_old - 2 * $cropHeight);


        # Taking care of original, if needed
        if ( $delete_original ) {
            if ( $use_linux_commands ) exec('rm '.$file);
            else @unlink($file);
        }

        # Preparing a method of providing result
        switch ( strtolower($output) ) {
            case 'browser':
                $mime = image_type_to_mime_type($info[2]);
                header("Content-type: $mime");
                $output = NULL;
                break;
            case 'file':
                $output = $file;
                break;
            case 'return':
                return $image_resized;
                break;
            default:
                break;
        }

        # Writing image according to type to the output destination and image quality
        switch ( $info[2] ) {
            case IMAGETYPE_GIF:   imagegif($image_resized, $output);    break;
            case IMAGETYPE_JPEG:  imagejpeg($image_resized, $output, $quality);   break;
            case IMAGETYPE_PNG:
                $quality = 9 - (int)((0.9*$quality)/10.0);
                imagepng($image_resized, $output, $quality);
                break;
            default: return FALSE;
        }

        return TRUE;
    }

	public function is_filename_used($filename, $directory)
	{
		// Get the md5 hash of the filepath
		$md5 = @md5_file($this->media_absolute_path.'/photos/'.$directory.'/'.$filename);

		// Check if a file exists with that hash
		return (bool) (self::get_all_items_based_on('hash', $md5));
	}

	public static function get_directory($file_type)
	{
		switch ($file_type)
		{
			case 'image/jpeg':
			case 'image/png':
			case 'image/gif':
			case 'image/svg+xml':
			case 'image/vnd.microsoft.icon': // favicon
			case 'image/x-icon':
				return 'photos';
				break;

			case 'text/plain':
			case 'text/csv':
			case 'text/x-comma-separated-values':
			case 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet':
			case 'application/vnd.openxmlformats-officedocument.wordprocessingml.document':
			case 'application/vnd.openxmlformats-officedocument.presentationml.presentation':
			case 'application/vnd.ms-powerpoint':
			case 'application/pdf':
			case 'image/pdf':
			case 'application/msword':
				return 'docs';
				break;

			case 'application/octet-stream':
				return 'fonts';
				break;

			case 'audio/mpeg':
			case 'audio/x-ms-wma':
			case 'audio/midi':
			case 'audio/mp3':
			case 'audio/mp4':
				return 'audios';
				break;

			case 'video/mp4':
			case 'video/x-xvid':
			case 'video/x-ms-wmv':
			case 'video/x-ms-wm':
			case 'video/mpeg':
			case 'video/x-flv':
				return 'videos';
				break;

			default:
				return '';
				break;
		}
	}

	public static function set_external_sync($name, $settings)
	{
		$existing = DB::select('*')
				->from(self::SYNC_TABLE)
				->where('name', '=', $name)
				->execute()
				->current();
		if ($existing) {
			DB::update(self::SYNC_TABLE)->set(array('settings' => $settings))->execute();
		} else {
			DB::insert(self::SYNC_TABLE)->values(array('name' => $name, 'settings' => $settings))->execute();
		}
	}

	public static function get_external_sync($name)
	{
		$select = DB::select('*')
				->from(self::SYNC_TABLE);
		if ($name) {
			$select->where('name', '=', $name);
		}
		$settings = $select->execute()->current();
		$settings = @json_decode($settings['settings'], true);
		return $settings;
	}

	/** Options for app home banners **/
	public static function get_app_home_banners_as_options($selected_image)
	{
		$images = DB::select()->from('plugin_media_shared_media')->where('location', '=', 'app_home_banners')->execute()->as_array();
		$return = '<option value="">-- None Selected -- </option>';
		foreach ($images as $image)
		{
			$selected = ($selected_image == '/media/photos/app_home_banners/' . $image['filename']) ? ' selected="selected"' : '';
			$return .= '<option value="' . '/media/photos/app_home_banners/' . $image['filename'] . '"'.$selected.'>'.$image['filename'].'</option>';
		}

		return $return;
	}

    public function get_media_absolute_path()
    {
        return $this->media_absolute_path;
    }

}//end of class
