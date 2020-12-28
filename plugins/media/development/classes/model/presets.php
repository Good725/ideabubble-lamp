<?php
/**
 * Created by JetBrains PhpStorm.
 * User: kosta
 * Date: 15/01/2013
 * Time: 11:30
 * To change this template use File | Settings | File Templates.
 */
Class Model_Presets extends Model{

	//@TODO: NOTE - This Model Serves ONLY the Admin (Back-End) Funcitonalities related to the Media Plugin
	private static $model_items_table = 'plugin_media_shared_media_photo_presets';

	public static function getItemsAdminSelect()
	{
		$select = DB::select(
			'presets.*',
			array('users_create.name', 'created_by_name'),
			array('roles_create.role', 'created_by_role'),
			array('users_modify.name', 'modified_by_name'),
			array('roles_modify.role', 'modified_by_role')
		)
			->from(array('plugin_media_shared_media_photo_presets', 'presets'))
				->join(array('engine_users', 'users_create'), 'left')->on('presets.created_by', '=', 'users_create.id')
				->join(array('engine_users', 'users_modify'), 'left')->on('presets.modified_by', '=', 'users_modify.id')
				->join(array('engine_project_role', 'roles_create'), 'left')
					->on('users_create.role_id', '=', 'roles_create.id')
				->join(array('engine_project_role', 'roles_modify'), 'left')
					->on('users_modify.role_id', '=', 'roles_modify.id')
			->where('presets.deleted', '=', 0);
		return $select;
	}

    /**
     * @var array - Array holding default media preset directories
     */
    private static $model_default_categories = array(
        'banners'             => 'Banners',
        'mobile_banners'      => 'Banners (mobile)',
        'bg_images'           => 'Background images',
        'content'             => 'Default (content)',
        'logos'               => 'Logos',
        'products_categories' => 'Products categories',
        'testimonial_banners' => 'Testimonial banners',
    );

	/**
	 * @var array - Array Holding all Default Preset Actions
	 */
	private static $model_default_preset_actions = array(
		'fit'=>'Fit both',
		'fitw'=>'Fit width only',
		'fith'=>'Fit height only',
		'crop'=>'Crop - keep middle',
		'cropt'=>'Crop - keep top',
		'cropb'=>'Crop - keep bottom'
	);

/* Back-End (CMS) : Admin Functions  ONLY -=> there is no need for Front-End Presets Functions */

	// Retrieve all Presets or the One - Specified by the $item_id, and are available to this Admin User
	public function get_all_items_admin($item_id = null) {

		if (is_null($item_id))
		{
			return self::getItemsAdminSelect()
					->execute()
					->as_array();
		}
		else
		{
			return self::getItemsAdminSelect()
					->where('presets.id', '=', $item_id)
					->execute()
					->as_array();
		}
	}//end of function


	//Retrieve all Available: Published and Not Deleted Presets - Deleted ones are filtered in the used view
	public function get_available_photo_presets(){
		return self::getItemsAdminSelect()
				->where('presets.publish', '=', 1)
				->execute()
				->as_array();
	}//end of function

    public static function get_preset_details($preset_name)
    {
        $q = DB::select()
            ->from('plugin_media_shared_media_photo_presets')
            ->where('title', '=', $preset_name)
            ->where('publish', '=', 1)
            ->where('deleted', '=', 0)
            ->execute()->as_array();

        return (count($q) > 0) ? $q[0] : NULL;
    }


	//Validate input Preset - information
	public function validate($item_data_to_validate) {
//		echo "\nValidate Preset: \n";
//		IbHelpers::die_r($item_data_to_validate);

		$item_valid = TRUE;

		//validate Preset Title
		if(empty($item_data_to_validate['item_title'])){
			IbHelpers::set_message('Please add "Preset Title".', 'error');
			$item_valid = FALSE;
		}
		//validate Preset Directory
		if(empty($item_data_to_validate['item_directory'])){
			IbHelpers::set_message('Please select "Preset Directory".', 'error');
			$item_valid = FALSE;
		}
		//validate Preset Action
		if(empty($item_data_to_validate['item_action_large'])){
			IbHelpers::set_message('Please select "Preset Action".', 'error');
			$item_valid = FALSE;
		}
		//validate: item_height_large -=> @NOTE: Preset Action: FIT_WIDTH_ONLY: fitw -=> will allow HEIGHT to be 0
		if(
			empty($item_data_to_validate['item_height_large']) &&
			$item_data_to_validate['item_action_large'] != 'fitw'
		){
			IbHelpers::set_message('Please add "Preset Height".', 'error');
			$item_valid = FALSE;
		}else if(preg_match('/[a-zA-Z]/', $item_data_to_validate['item_height_large'])){
			IbHelpers::set_message('The Preset Height: "'.$item_data_to_validate['item_height_large'].'" must consist only of numbers..', 'error');
			$item_valid = FALSE;
		}else if($item_data_to_validate['item_height_large'] < 0){
			IbHelpers::set_message('The Preset Height: "'.$item_data_to_validate['item_height_large'].'" CANNOT be a NEGATIVE value.', 'error');
			$validate = FALSE;
		}
		//validate: item_width_large -=> @NOTE: Preset Action: FIT_HEIGHT_ONLY: fith -=> will allow WIDTH to be 0
		if(
			empty($item_data_to_validate['item_width_large']) &&
			$item_data_to_validate['item_action_large'] != 'fith'
		){
			IbHelpers::set_message('Please add "Preset Width".', 'error');
			$item_valid = FALSE;
		}else if(preg_match('/[a-zA-Z]/', $item_data_to_validate['item_width_large'])){
			IbHelpers::set_message('The Preset Width: "'.$item_data_to_validate['item_width_large'].'" must consist only of numbers..', 'error');
			$item_valid = FALSE;
		}else if($item_data_to_validate['item_width_large'] < 0){
			IbHelpers::set_message('The Preset Width: "'.$item_data_to_validate['item_width_large'].'" CANNOT be a NEGATIVE value.', 'error');
			$validate = FALSE;
		}

		//check if Thumb is Selected and do the corresponding validations
		if($item_data_to_validate['item_thumb'] == 1){
			//validate item_action_thumb - is compulsory
			if(empty($item_data_to_validate['item_action_thumb'])){
				IbHelpers::set_message('Please select "Thumb Action".', 'error');
				$item_valid = FALSE;
			}
			//validate: item_height_thumb -=> @NOTE: Preset Action: FIT_WIDTH_ONLY: fitw -=> will allow HEIGHT to be 0
			if(
				empty($item_data_to_validate['item_height_thumb']) &&
				$item_data_to_validate['item_action_thumb'] != 'fitw'
			){
				IbHelpers::set_message('Please add "Thumb Height".', 'error');
				$item_valid = FALSE;
			}else if(preg_match('/[a-zA-Z]/', $item_data_to_validate['item_height_thumb'])){
				IbHelpers::set_message('The Thumb Height: "'.$item_data_to_validate['item_height_thumb'].'" must consist only of numbers..', 'error');
				$item_valid = FALSE;
			}else if($item_data_to_validate['item_height_thumb'] < 0){
				IbHelpers::set_message('The Thumb Height: "'.$item_data_to_validate['item_height_thumb'].'" CANNOT be a NEGATIVE value.', 'error');
				$validate = FALSE;
			}else if($item_data_to_validate['item_height_thumb'] > $item_data_to_validate['item_height_large']){
				IbHelpers::set_message(
					'The Thumb Height: "'.$item_data_to_validate['item_height_thumb'].
					'" CANNOT be GREATER than Preset Height: "'.$item_data_to_validate['item_height_large'].'".',
					'error'
				);
				$validate = FALSE;
			}
			//validate: item_width_thumb -=> @NOTE: Preset Action: FIT_HEIGHT_ONLY: fith -=> will allow WIDTH to be 0
			if(
				empty($item_data_to_validate['item_width_thumb']) &&
				$item_data_to_validate['item_action_thumb'] != 'fith'
			){
				IbHelpers::set_message('Please add "Thumb Width".', 'error');
				$item_valid = FALSE;
			}else if(preg_match('/[a-zA-Z]/', $item_data_to_validate['item_width_thumb'])){
				IbHelpers::set_message('The Thumb Width: "'.$item_data_to_validate['item_width_thumb'].'" must consist only of numbers..', 'error');
				$item_valid = FALSE;
			}else if($item_data_to_validate['item_width_thumb'] < 0){
				IbHelpers::set_message('The Thumb Width: "'.$item_data_to_validate['item_width_thumb'].'" CANNOT be a NEGATIVE value.', 'error');
				$validate = FALSE;
			}else if($item_data_to_validate['item_width_thumb'] > $item_data_to_validate['item_width_large']){
				IbHelpers::set_message(
					'The Thumb Height: "'.$item_data_to_validate['item_width_thumb'].
					'" CANNOT be GREATER than Preset Height: "'.$item_data_to_validate['item_width_large'].'".',
					'error'
				);
				$validate = FALSE;
			}
		}//else no need to validate Thumb details

		//return
		return $item_valid;
	}//end of function


	// Add a Preset to the database
	public function add($item_input_data) {
//		echo "\nAdd Preset: \n";
//		IbHelpers::die_r($item_input_data);


		// 1. get the ID of the currently-logged in user, i.e. the user who is making the update
		$logged_in_user = Auth::instance()->get_user();

		/* 2. Create the NEW Preset */
		//Set the Preset data to be added to Database
		$item_to_add_data['directory'] = $item_input_data['item_directory'];
		$item_to_add_data['title'] = $item_input_data['item_title'];
		$item_to_add_data['height_large'] = $item_input_data['item_height_large'];
		$item_to_add_data['width_large'] = $item_input_data['item_width_large'];
		$item_to_add_data['action_large'] = $item_input_data['item_action_large'];
		$item_to_add_data['thumb'] = (($item_input_data['item_thumb'] == '0')? '' : $item_input_data['item_thumb']);
		$item_to_add_data['height_thumb'] = $item_input_data['item_height_thumb'];
		$item_to_add_data['width_thumb'] = $item_input_data['item_width_thumb'];
		$item_to_add_data['action_thumb'] = (($item_input_data['item_action_thumb'] == '0')? '' : $item_input_data['item_action_thumb'] );
		$item_to_add_data['publish'] = $item_input_data['item_publish'];
		// Format the required dates for mysql storage
		$item_to_add_data['date_created'] = date('Y-m-d H:i:s');
		$item_to_add_data['created_by'] = $logged_in_user['id'];
		$item_to_add_data['date_modified'] = $item_to_add_data['date_created'];
		$item_to_add_data['modified_by'] = $logged_in_user['id'];
		$item_to_add_data['deleted'] = 0;

		//add the Preset to DB
		$insert_result = DB::insert(self::$model_items_table)->values($item_to_add_data)->execute();

		//Set corresponding messages
		if($insert_result){
			IbHelpers::set_message('Preset: "'.$item_to_add_data['title'].'" with ID: #'.$insert_result[0].' created.', 'success');
		}else{
			IbHelpers::set_message('There was a problem with creation of Preset: "'.$item_to_add_data['title'].'".', 'error');
		}

		// return new ID
		return $insert_result[0];
	}//end of function


	//Update a Preset record
	public function update($item_update_data) {
//		echo "\nUpdate Preset: \n";
//		IbHelpers::die_r($item_update_data);

		// 1. get the ID of the currently-logged in user, i.e. the user who is making the update
		$logged_in_user = Auth::instance()->get_user();

		/* 2. Update the existent Preset */
		//Set the Preset data to be Updated to Database
		$item_to_update_data['directory'] = $item_update_data['item_directory'];
		$item_to_update_data['title'] = $item_update_data['item_title'];
		$item_to_update_data['height_large'] = $item_update_data['item_height_large'];
		$item_to_update_data['width_large'] = $item_update_data['item_width_large'];
		$item_to_update_data['action_large'] = $item_update_data['item_action_large'];
		$item_to_update_data['thumb'] = (($item_update_data['item_thumb'] == '0')? 0 : $item_update_data['item_thumb']);
		$item_to_update_data['height_thumb'] = $item_update_data['item_height_thumb'];
		$item_to_update_data['width_thumb'] = $item_update_data['item_width_thumb'];
		$item_to_update_data['action_thumb'] = (($item_update_data['item_action_thumb'] == '0')? '' : $item_update_data['item_action_thumb'] );
		$item_to_update_data['publish'] = $item_update_data['item_publish'];
		// Format the required dates for mysql storage
//		$item_to_update_data['date_created'] = date('Y-m-d H:i:s');
//		$item_to_update_data['created_by'] = $logged_in_user['id'];
		$item_to_update_data['date_modified'] = date('Y-m-d H:i:s');
		$item_to_update_data['modified_by'] = $logged_in_user['id'];
		$item_to_update_data['deleted'] = 0;

		//Update the Preset to DB
		$update_result = DB::update(self::$model_items_table)
								->set($item_to_update_data)
								->where('id', '=', $item_update_data['item_id'])
								->execute();

		//Set corresponding messages
		if($update_result == 1){
			IbHelpers::set_message('Preset: "'.$item_to_update_data['title'].'" with ID: #'.$item_update_data['item_id'].' updated.', 'success');
		}else{
			IbHelpers::set_message('There was a problem with update of Preset: "'.$item_to_update_data['title'].'" with ID: #'.$item_update_data['item_id'].'.', 'error');
		}

		// return
		return $update_result;
	}//end of function


	//Sets a specified Preset Record in the DB as Deleted
	public function delete($item_id) {
//		echo "\nDelete Preset: \n";
//		IbHelpers::die_r($item_id);

		// 1. get the ID of the currently-logged in user, i.e. the user who is making the update
		$logged_in_user = Auth::instance()->get_user();

		// 2. Mark the specified Preset as deleted - in this case result will be INT - holding the number of affected rows
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


	public static function toggle_item_publish($item_id, $publish_flag){
//		echo "\nToggle Publish Preset: \n";
//		IbHelpers::pre_r(array('item_id' => $item_id, 'publish_flag' => $publish_flag));

		// 1. get the ID of the currently-logged in user, i.e. the user who is making the update
		$logged_in_user = Auth::instance()->get_user();

		// 2. Toggle the Publish flag of the specified Preset
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


	public static function get_preset_directories_as($return_type='details'){
//		echo "\nGet Preset Folders as: \n";
//		IbHelpers::die_r('return_type: '.$return_type);

		$result = '';

		$current_preset_folders = self::$model_default_categories;

		// Get all Folders, specified by the Plugins, which Require Media Folders
		$plugins_media_folders = DB::select('media_folder')
				->from('engine_plugins')
				->where('requires_media', '=', '1')
				->and_where('name', '!=', 'media')
				->execute()
				->as_array();

		// Add the specified by the Installed to this Project (Web App) Plugins - Media Specific Folders
		if($plugins_media_folders){
			foreach($plugins_media_folders as $plugin_folder)
				$current_preset_folders[trim(strtolower($plugin_folder['media_folder']))] = trim(ucfirst($plugin_folder['media_folder']));
		}

		// If lookbook is a page layout, add a directory for that
		if (class_exists('Engine_Layout') AND ORM::factory('Engine_Layout')->where('layout', '=', 'campaign')->find()->id)
		{
			$current_preset_folders['campaign'] = 'Campaign';
		}

		//Generate the Result to be Returned
		switch($return_type){
			case 'options':
				foreach($current_preset_folders as $folder_key=>$media_folder)
					$result .= '<option value="'.$folder_key.'">'.$media_folder.'</option>';
				break;

			case 'details':
			default:
				$result = $current_preset_folders;
				break;
		}//end of switch

		//Return
		return $result;

	}//end of function


	public static function get_preset_actions_as($return_type='details'){
//		echo "\nGet Preset Actions as: \n";
//		IbHelpers::die_r('return_type: '.$return_type);

		$result = '';

		//Generate the Result to be Returned
		switch($return_type){
			case 'options':
				foreach(self::$model_default_preset_actions as $action_key=>$action)
					$result .= '<option value="'.$action_key.'">'.$action.'</option>';
				break;

			case 'details':
			default:
				$result = self::$model_default_preset_actions;
				break;
		}//end of switch

		//Return
		return $result;

	}//end of function


	public static function get_presets_items_as($return_type='details'){
//		echo "\nGet Preset Items as: \n";
//		IbHelpers::die_r('return_type: '.$return_type);

		$result = '';

		//Get All Available Presets (Published and Not Deleted)
		$available_presets = self::factory('Presets')->get_available_photo_presets();

		//Generate the Result to be Returned
		switch($return_type){
			case 'options':
				foreach($available_presets as $photo_preset)
					$result .= '<option value="'.$photo_preset['id'].'" '.
							'data-title="'.$photo_preset['title'].'" '.
							'data-directory="'.$photo_preset['directory'].'" '.
							'data-height_large="'.$photo_preset['height_large'].'" '.
							'data-width_large="'.$photo_preset['width_large'].'" '.
							'data-action_large="'.$photo_preset['action_large'].'" '.
							'data-thumb="'.$photo_preset['thumb'].'" '.
							'data-height_thumb="'.$photo_preset['height_thumb'].'" '.
							'data-width_thumb="'.$photo_preset['width_thumb'].'" '.
							'data-action_thumb="'.$photo_preset['action_thumb'].'"'.
							'>'.$photo_preset['title'].'</option>';
				break;

			case 'details':
			default:
				foreach($available_presets as $photo_preset) $result[$photo_preset['id']] = $photo_preset;
				break;
		}//end of switch

		//Return
		return $result;

	}//end of function get_presets_as


/* END of Back-End (CMS) : Admin Functions */


}//end of class