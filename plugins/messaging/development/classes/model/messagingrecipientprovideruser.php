<?php defined('SYSPATH') or die('No direct script access.');

class Model_MessagingRecipientProviderUser implements Model_MessagingRecipientProvider
{
	public function pid()
	{
		return "CMS_USER";
	}

	public function supports($driver)
	{
		return in_array($driver, array('sms', 'email', 'dashboard'));
	}

	public function get_by_id($id)
	{
		$data = DB::select('id', DB::expr("concat_ws(' ', name, surname) as label"), 'email',
            DB::expr( 'if(mobile, country_dial_code_mobile, country_dial_code_phone) as country_code') ,
            DB::expr( 'if(mobile, dial_code_mobile, dial_code_phone) as dial_code') ,
            DB::expr('if(mobile, mobile, phone) as sms'))
					->from('engine_users')
					->where('id', '=', $id)
					->execute()
					->as_array();
		if($data){
		    $data = reset($data);
		    $dial_code = !empty($data['dial_code'])  ? $data['dial_code'] : '';
		    $country_dial_code = !empty($data['country_code'])  ? $data['country_code'] : '';
		    $data['sms'] =  + $country_dial_code . $dial_code . $data['sms'];
			return $data;
		} else {
			return null;
		}
	}
	
	public function get_by_label($label)
	{
		$data = DB::select('id', DB::expr("concat_ws(' ', name, surname) as label"), 'email', DB::expr('if(mobile, mobile, phone) as sms'))
					->from('engine_users')
					->where(DB::expr("concat_ws(' ', name, surname)"), '=', $label)
					->execute()
					->as_array();
		if($data){
			return $data[0];
		} else {
			return null;
		}
	}
	
	public function search($term)
	{
		return DB::select('id', DB::expr("concat_ws(' ', name, surname) as label"), 'email', DB::expr('if(mobile, mobile, phone) as sms'))
					->from('engine_users')
					->where('email', 'like', '%' . $term . '%')
					->or_where('name', 'like', '%' . $term . '%')
					->or_where('surname', 'like', '%' . $term . '%')
					->execute()
					->as_array();
	}
	
	public function to_autocomplete($term, &$data)
	{
		foreach(DB::select('id', DB::expr("concat_ws(' ', name, surname) as label"), 'email', DB::expr('if(mobile, mobile, phone) as sms'))
					->from('engine_users')
					->where('deleted', '=', 0)
					->and_where_open()
						->where('email', 'like', '%' . $term . '%')
						->or_where('name', 'like', '%' . $term . '%')
						->or_where('surname', 'like', '%' . $term . '%')
					->and_where_close()
					->order_by('name', 'asc')
					->order_by('surname', 'asc')
					->limit(5)
					->execute()
					->as_array() as $user){
			$data[] = array('value' => $user['id'],
								'label' => $user['label'],
								'category' => $this->pid(),
								'email' => $user['email'],
								'sms' => $user['sms']);
		}
	}

    public function resolve_final_targets($target, &$target_list, &$warnings)
    {
        $driver    = isset($target['driver']) ? $target['driver'] : '';
        $target_id = isset($target['target']) ? $target['target'] : '';

        if ($driver == 'dashboard') {
            $target['target_type'] = 'CMS_USER';
            $target['target'] = (int)$target_id;
            $target_list[] = $target;
        } else if ($driver == 'sms') {
            $user = $this->get_by_id($target_id);
            if ($user['sms'] != '') {
                $target['target_type'] = 'PHONE';
                $target['target'] = $user['sms'];
                $target_list[] = $target;
            } else {
                $warnings[] = 'user:' . $user['id'] . ' does not have mobile set for ' . $driver . ' messaging';
            }
        } else if ($driver == 'email') {
            $user = self::get_by_id($target_id);
            if ($user['email'] != '') {
                $target['target_type'] = 'EMAIL';
                $target['target'] = $user['email'];
                $target_list[] = $target;
            } else {
                $warnings[] = 'user:' . $user['id'] . ' does not have email set for ' . $driver . ' messaging';
            }
        } else {
            $warnings[] = $driver . ' messaging is not supported';
        }
    }

	public function message_details_column()
	{
		return "concat_ws(' ', u.name, u.surname)";
	}

	public function message_details_join($query)
	{
		$query->join(array('engine_users', 'u'), 'left')->on('t.target', '=', 'u.id')->on('t.target_type', '=', DB::expr("'CMS_USER'"));
	}
}