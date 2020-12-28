<?php defined('SYSPATH') or die('No direct script access.');

class Model_Providers extends Model
{
    const TABLE_PROVIDERS = 'plugin_courses_providers';

    public static function count_providers($search = false)
    {
        $_search = '';
        if ($search)
        {
            $_search = " AND (`plugin_courses_providers`.`name` like '%".$search."%' OR `summary` like '%".$search."%' OR `address1` like '%".$search."%' OR `address2` like '%".$search."%'  OR `address3` like '%".$search."%')";
        }
        $query = DB::query(Database::SELECT,
            "SELECT count(*) as `count` FROM `plugin_courses_providers` WHERE `delete` = 0".$_search.";")
            ->execute()
            ->as_array();
        return $query['0']['count'];
    }

    public static function get_all_providers()
    {
        $query = DB::query(Database::SELECT,
            "SELECT * FROM `plugin_courses_providers` 
                        WHERE `delete` = 0 
                          AND `publish`= 1 
                          AND type_id NOT IN 
                                (SELECT id FROM `plugin_courses_providers_types` WHERE `type` = 'Accreditation Body') 
                    ORDER By `name`;")
            ->execute()
            ->as_array();
        return $query;
    }

    public static function get_accreditation_bodies()
    {
        $query = DB::query(Database::SELECT,
            "SELECT * FROM `plugin_courses_providers` 
                        WHERE `delete` = 0 
                          AND `publish`= 1 
                          AND type_id = 
                                (SELECT id FROM `plugin_courses_providers_types` 
                                    WHERE `type` = 'Accreditation Body' LIMIT 1) 
                    ORDER By `name`;")
            ->execute()
            ->as_array();
        return $query;
    }

    public static function get_all_schools()
    {
        $query = DB::select('p.*','p1.type')
            ->from(array('plugin_courses_providers','p'))
            ->join(array('plugin_courses_providers_types','p1'))
            ->on('p1.id','=','p.type_id')
            ->where('p1.type','=','School')
            ->where('p.delete','=',0)
            ->where('p.publish','=',1)
            ->order_by('p.name')
            ->execute()
            ->as_array();
        return $query;
    }

    public static function get_providers($limit, $offset, $sort, $dir, $search = false)
    {
        $_search = '';
        if ($search)
        {
            $_search = " AND (`plugin_courses_providers`.`name` like '%".$search."%' OR `summary` like '%".$search."%' OR `address1` like '%".$search."%' OR `address2` like '%".$search."%'  OR `address3` like '%".$search."%')";
        }
		$_limit = ($limit != -1) ? ' LIMIT '.$offset.','.$limit : '';
        $query = DB::query(Database::SELECT,
            "SELECT `plugin_courses_providers`.`id`,`plugin_courses_providers`.`type_id`, `plugin_courses_providers`.`name`, `plugin_courses_providers`.`publish` as `pbl`, `plugin_courses_counties`.`name` as `county`, `plugin_courses_cities`.`name` as `city` , `plugin_courses_providers_types`.`type` as `type`
            FROM `plugin_courses_providers`
            LEFT JOIN `plugin_courses_cities` ON `plugin_courses_providers`.`city_id` = `plugin_courses_cities`.`id`
            LEFT JOIN `plugin_courses_counties` ON `plugin_courses_providers`.`county_id` = `plugin_courses_counties`.`id`
            LEFT JOIN `plugin_courses_providers_types` ON `plugin_courses_providers_types`.`id` = `plugin_courses_providers`.`type_id`
            WHERE `plugin_courses_providers`.`delete` = 0".$_search." ORDER BY ".$sort." ".$dir." ".$_limit)
            ->execute()
            ->as_array();
        $return = array();
        if (count($query) > 0)
        {
            $i=0;
            foreach ($query as $elem => $sub)
            {
                $return[$i]['name'] = '<a href="/admin/courses/edit_provider/?id='.$sub['id'].'">'.$sub['name'].'</a>';
                $return[$i]['type'] = '<a href="/admin/courses/edit_provider/?id='.$sub['id'].'">'.$sub['type'].'</a>';
                $return[$i]['type_id'] = '<a href="/admin/courses/edit_provider/?id='.$sub['id'].'">'.$sub['type_id'].'</a>';
                $return[$i]['city'] = '<a href="/admin/courses/edit_provider/?id='.$sub['id'].'">'.$sub['city'].'</a>';
                $return[$i]['county'] = '<a href="/admin/courses/edit_provider/?id='.$sub['id'].'">'.$sub['county'].'</a>';
                $return[$i]['edit'] = '<a href="/admin/courses/edit_provider/?id='.$sub['id'].'">Edit</a>';
                if ($sub['pbl'] == '1')
                {
                    $return[$i]['publish'] = '<a href="#" class="publish" data-publish="1" data-id="'.$sub['id'].'"><i class="icon-ok"></i></a>';
                }
                else
                {
                    $return[$i]['publish'] = '<a href="#" class="publish" data-publish="0" data-id="'.$sub['id'].'"><i class="icon-ban-circle"></i></a>';
                }
                $return[$i]['remove'] = '<a href="#" class="delete" data-id="'.$sub['id'].'">Delete</a>';
                $i++;
            }
        }

        return $return;
    }

    public static function get_provider($id)
    {
        $data = DB::query(Database::SELECT,
            "SELECT  `plugin_courses_providers`.*, `plugin_courses_counties`.`name` as `county`, `plugin_courses_cities`.`name` as `city` , `plugin_courses_providers_types`.`type` as `type`
            FROM `plugin_courses_providers`
            LEFT JOIN `plugin_courses_cities` ON `plugin_courses_providers`.`city_id` = `plugin_courses_cities`.`id`
            LEFT JOIN `plugin_courses_counties` ON `plugin_courses_providers`.`county_id` = `plugin_courses_counties`.`id`
            LEFT JOIN `plugin_courses_providers_types` ON `plugin_courses_providers_types`.`id` = `plugin_courses_providers`.`type_id`
            WHERE `plugin_courses_providers`.`delete` = 0 AND `plugin_courses_providers`.`id`=".$id)
            ->execute()
            ->as_array();

        return $data[0];
    }

    public static function set_publish_provider($id, $state)
    {
        if ($state == '1')
        {
            $published = 0;
        }
        else
        {
            $published = 1;
        }
        $logged_in_user = Auth::instance()->get_user();
        $query = DB::update("plugin_courses_providers")
            ->set(array(
                'publish' => $published,
                'modified_by' => $logged_in_user['id'],
                'date_modified' => date('Y-m-d H:i:s')
            ))
            ->where('id', '=', $id)
            ->execute();
        $response = array();
        if ($query > 0)
        {
            $response['message'] = 'success';
        }
        else
        {
            $response['message'] = 'error';
            $response['error_msg'] = 'An error occurred! Please contact with support!';
        }
        return $response;
    }


    public static function remove_provider($id)
    {
        $logged_in_user = Auth::instance()->get_user();
        DB::delete('plugin_courses_courses_has_providers')
            ->where('provider_id', '=', $id);

        $ret = DB::update('plugin_courses_providers')
            ->set(array(
                'modified_by' => $logged_in_user['id'],
                'date_modified' => date('Y-m-d H:i:s'),
                'delete' => 1
            ))
            ->where('id', '=', $id)
            ->execute();
        if ($ret > 0)
        {
            $response['message'] = 'success';
        }
        else
        {
            $response['message'] = 'error';
            $response['error_msg'] = 'An error occurred! Please contact with support!';
        }
        return $response;
    }

    public static function save_provider($data)
    {
		// add / update
		$save_action = 'add';
		$item_id = 0;
        unset($data['redirect']);
        //Add the necessary values to the $data array for update
        $logged_in_user = Auth::instance()->get_user();
        if ((int)$data['id'] > 0)
        {
            $id = (int)$data['id'];
            unset($data['id']);
            foreach ($data as $dat => $v)
            {
                if ($v == '')
                {
                    $data[$dat] = NULL;
                }
            }
            $data['modified_by'] = $logged_in_user['id'];
            $data['date_modified'] = date('Y-m-d H:i:s');
            $query = DB::update('plugin_courses_providers')
                ->set($data)
                ->where('id', '=', $id)
                ->execute();

			$save_action = 'update';
			$item_id = $id;
        }
        else
        {
            foreach ($data as $dat => $v)
            {
                if ($v == '')
                {
                    $data[$dat] = NULL;
                }
            }
            $data['created_by'] = $logged_in_user['id'];
            $data['date_created'] = date('Y-m-d H:i:s');
            $data['delete'] = 0;
            $query = DB::insert('plugin_courses_providers', array_keys($data))
                ->values($data)
                ->execute();

			$save_action = 'add';
			$item_id = (isset($query[0]) AND $query[0] > 0)? $query[0] : 0;
        }

		// Set Successful / Not Successful Insert / Update Message
		if(
			($save_action == 'add' AND $query[0] > 0) OR
			($save_action == 'update' AND $query == 1)
		)
		{
			IbHelpers::set_message (
				'Provider ID #'.$item_id.':  "'.$data['name'].'" has been '.(($save_action == 'add')? 'CREATED' : 'UPDATED' ).'.',
				'success popup_box'
			);
		}
		else
		{
			IbHelpers::set_message (
				'Sorry! There was a problem with '.(($save_action == 'add')? 'CREATION' : 'UPDATE' )
				.' of '.( ($item_id > 0)? 'Provider ID #'.$item_id : 'Provider' ).': "'.$data['name'].'".<br />'
				.'Please make sure, that form is filled properly and Try Again!',
				'error popup_box'
			);
		}

        return $item_id;
    }
    public static function validate_provider($data)
    {
        //create empty errors array
        $errors = array();
        //check name must be min 3 chars
        if (@strlen($data['name']) < 3)
        {
            $errors[] = "Provider name must contains min 3 characters";
        }
        if (@strlen($data['address1']) < 3)
        {
            $errors[] = "Provider address must contains min 3 characters";
        }
        return $errors;

    }

    public static function get_all_types()
    {
        $query = DB::select()->from('plugin_courses_providers_types')->execute()->as_array();
        return $query;
    }
	
	public static function is_same_school($school1a, $school2a)
	{
		$school1a = str_ireplace(array('x000B', '_'), '', $school1a);
		$school1a = preg_replace('/\s+/', ' ', trim($school1a, '" '));
		$school2a = str_ireplace(array('x000B', '_'), '', $school2a);
		$school2a = preg_replace('/\s+/', ' ', trim($school2a, '" '));
			
		$school1a = strtolower($school1a);
		$school2a = strtolower($school2a);
		$common_words = array('comp', 'comprehensive', 'street', 'town', 'college', 'comm', 'community', 'school', 'national', 'hospital', 'st', 's', '.', "'", ',', '_', 'x000b');
		$school1 = str_replace($common_words, '', $school1a);
		$school2 = str_replace($common_words, '', $school2a);
		similar_text($school1, $school2, $similarity1);
		similar_text($school1, $school2, $similarity2);
		$similarity = max($similarity1, $similarity2);
		
		if($similarity > 85){
			//echo "similarity:$similarity:" . $school1, " : ",$school2,"\n";
			return true;
		}
		
		$ldiff = levenshtein($school1, $school2);
		if($ldiff < (strlen($school1) / 4)){
			//echo "ldiff:$ldiff:" . $school1, " : ",$school2,"\n";
			return true;
		}

		$common_words = array('st', 's', '.', "'", ',', '_', 'x000b');
		$school1 = str_replace($common_words, '', $school1a);
		$school2 = str_replace($common_words, '', $school2a);
		
		$s1 = preg_split('/\s+/', strtolower($school1));
		$s2 = preg_split('/\s+/', strtolower($school2));
		
		// remove common words;
		foreach($s1 as $i => $word){
			if(in_array($word, $common_words)){
				unset($s1[$i]);
			}
		}
		$s1 = array_values($s1);
		foreach($s2 as $i => $word){
			if(in_array($word, $common_words)){
				unset($s2[$i]);
			}
		}
		$s2 = array_values($s2);
		
		sort($s1);
		sort($s2);
		
		$soundex1 = array();
		$soundex2 = array();
		
		foreach($s1 as $word){
			$soundex1[] = soundex($word);
		}
		foreach($s2 as $word){
			$soundex2[] = soundex($word);
		}
		if($soundex1 == $soundex2){
			//echo "soundex:" . $school1, " : ",$school2, ":", implode("", $soundex1), ":", implode("", $soundex2),"\n";
			return true;
		}

		if(count($s2) > count($s1)){
			foreach($s2 as $i => $word){
				
			}
		}
		return false;
	}

	public static function cleanup_schools_get_suggestions()
	{
		$suggestions = array();
		$schools_ = DB::select('*')->from('plugin_courses_providers')->order_by(DB::expr('length(name)'), 'desc')->execute()->as_array();
		/*$schools_ = array( array('id' => 1, 'name' => 'Castletoy College', 'correct_name' => 1),
							array('id' => 2, 'name' => 'Castletroy College'),
							array('id' => 3, 'name' => 'Castletoy'));*/
		$schools = array();
		foreach($schools_ as $school){
			$schools[$school['id']] = $school;
		}
		unset($schools_);
		$replaced = array();
		foreach($schools as $i => $school1){
			if(isset($replaced[$school1['id']]))continue;
			
			foreach($schools as $j => $school2){
				if($school1['id'] == $school2['id'] || isset($replaced[$school2['id']]))continue;
				
				if($school1['name'] != $school2['name'] && self::is_same_school($school1['name'], $school2['name'])){
					if(@$school1['correct_name'] || strlen($school1['name']) > strlen($school2['name'])){
						if(isset($replaced[$school1['id']])){
							$replaced[$school2['id']] = $replaced[$school1['id']];
						} else {
							$replaced[$school2['id']] = $school1['id'];
						}
						$suggestions[$school2['name']] = $school1['name'];
					} else {
						if(isset($replaced[$school2['id']])){
							$replaced[$school1['id']] = $replaced[$school2['id']];
						} else {
							$replaced[$school1['id']] = $school2['id'];
						}
						$suggestions[$school1['name']] = $school2['name'];
					}
					break;
				}
			}
		}
		
		//header('content-type: text/plain');
		//print_r($schools);
		//print_r($replaced);
		ksort($suggestions);
		//print_r($suggestions);
		foreach($replaced as $from => $to){
			//echo $schools[$from]['name'] . ' => ' . $schools[$to]['name'] . "\n";
		}
		
		return array('schools' => $schools, 'replaced' => $replaced);
	}

	public static function cleanup_schools($post)
	{
		//header('content-type: text/plain');print_r($post);die();
        try {
            Database::instance()->begin();
            foreach($post['name'] as $id => $name){
                DB::update('plugin_courses_providers')->set(array('name' => $name))->where('id', '=', $id)->execute();
            }
            foreach($post['replace'] as $from => $to){
                if($to){
                    DB::update('plugin_contacts3_contacts')->set(array('school_id' => $to))->where('school_id', '=', $from)->execute();
                    DB::delete('plugin_courses_providers')->where('id', '=', $from)->execute();
                }
            }
            Database::instance()->commit();
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }
	}

    public static function get_providers_for_host()
    {
        $microsite_suffix = isset(Kohana::$config->load('config')->project_suffix) ? Kohana::$config->load('config')->project_suffix : '';
        $ids = [];

        $providers = DB::select()
            ->from(self::TABLE_PROVIDERS)
            ->where('name', 'like', '%--'.$microsite_suffix)
            ->where('publish', '=', 1)
            ->where('delete', '=', 0)
            ->execute();

        foreach ($providers as $provider) {
            $ids[] = $provider['id'];
        }

        return $ids;
    }
}
