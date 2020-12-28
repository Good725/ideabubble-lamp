<?php defined('SYSPATH') or die('No direct script access.');

class Model_MessagingRecipientProviderContact implements Model_MessagingRecipientProvider
{
	public function pid()
	{
		return "CMS_CONTACT";
	}

	public function supports($driver)
	{
		return in_array($driver, array('sms', 'email'));
	}

	public function get_by_id($id)
	{
		$data = DB::select('id', DB::expr("concat_ws(' ',first_name, last_name) as label"), 'email', DB::expr('mobile as sms'))
					->from('plugin_contacts_contact')
					->where('id', '=', $id)
					->execute()
					->as_array();
		if($data){
			return $data[0];
		} else {
			return null;
		}
	}
	
	public function get_by_label($label)
	{
		$data = DB::select('id', DB::expr("concat_ws(' ',first_name, last_name) as label"), 'email', DB::expr('mobile as sms'))
					->from('plugin_contacts_contact')
					->where(DB::expr("concat_ws(' ',first_name, last_name)"), '=', $label)
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
		return DB::select('id', DB::expr("concat_ws(' ',first_name, last_name) as label"), 'email', DB::expr('mobile as sms'))
					->from('plugin_contacts_contact')
					->where('deleted', '=', 0)
					->and_where_open()
					->where('email', 'like', '%' . $term . '%')
					->or_where('first_name', 'like', '%' . $term . '%')
					->or_where('last_name', 'like', '%' . $term . '%')
					->or_where('phone', 'like', '%' . $term . '%')
					->or_where('mobile', 'like', '%' . $term . '%')
					->and_where_close()
					->order_by('first_name', 'asc')
					->order_by('last_name', 'asc')
					->limit(100)
					->execute()
					->as_array();
	}
	
	public function to_autocomplete($term, &$data)
	{
		$category = $this->pid();
		foreach(DB::select('id', DB::expr("concat_ws(' ',first_name, last_name) as label"), 'email', DB::expr('mobile as sms'))
					->from('plugin_contacts_contact')
					->where('deleted', '=', 0)
					->and_where_open()
					->where('email', 'like', '%' . $term . '%')
					->or_where('first_name', 'like', '%' . $term . '%')
					->or_where('last_name', 'like', '%' . $term . '%')
					->or_where('phone', 'like', '%' . $term . '%')
					->or_where('mobile', 'like', '%' . $term . '%')
					->and_where_close()
					->order_by('first_name', 'asc')
					->order_by('last_name', 'asc')
					->limit(5)
					->execute()
					->as_array() as $contact){
			$data[] = array('value' => $contact['id'],
								'label' => $contact['label'],
								'category' => $category,
								'email' => $contact['email'],
								'sms' => $contact['sms']);
		}
	}

    public function resolve_final_targets($target, &$target_list, &$warnings)
    {
        $driver       = isset($target['driver']) ? $target['driver'] : '';
        $item_id      = isset($target['target']) ? $target['target'] : '';
        $target_types = array('sms' => 'PHONE', 'email' => 'EMAIL');

        if (array_key_exists($driver, $target_types)) {
            $target['target_type'] = $target_types[$driver];
            $contact = $this->get_by_id($item_id);

            if (!empty($contact[$driver])) {
                $target['target'] = $contact[$driver];
                $target_list[]    = $target;
            } else {
                $warnings[] = 'user:' . $contact['id'] . ' does not have ' . $driver . ' set for ' . $driver . ' messaging';
            }
        }
        else {
            $warnings[] = $driver . ' messaging is not supported';
        }
	}
	
	public function message_details_column()
	{
		return "concat_ws(' ', c1.first_name, c1.last_name)";
	}

	public function message_details_join($query)
	{
		$query->join(array('plugin_contacts_contact', 'c1'), 'left')->on('t.target', '=', 'c1.id')->on('t.target_type', '=', DB::expr("'CMS_CONTACT'"));
	}
}