<?php defined('SYSPATH') or die('No direct script access.');

class Model_MessagingRecipientProviderContactlist implements Model_MessagingRecipientProvider
{
	public function pid()
	{
		return "CMS_CONTACT_LIST";
	}

	public function supports($driver)
	{
		return in_array($driver, array('sms', 'email'));
	}

	public function get_by_id($id)
	{
		$data = DB::select('id', DB::expr("name as label"))
					->from('plugin_contacts_mailing_list')
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
		$data = DB::select('id', DB::expr("name as label"))
					->from('plugin_contacts_mailing_list')
					->where('name', '=', $label)
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
		return DB::select('id', DB::expr("name as label"))
					->from('plugin_contacts_mailing_list')
					->where('name', 'like', '%' . $term . '%')
					->order_by('name', 'asc')
					->limit(100)
					->execute()
					->as_array();
	}
	
	public function to_autocomplete($term, &$data)
	{
		$category = $this->pid();
		foreach(DB::select('id', DB::expr("name as label"))
					->from('plugin_contacts_mailing_list')
					->where('name', 'like', '%' . $term . '%')
					->order_by('name', 'asc')
					->limit(5)
					->execute()
					->as_array() as $contact){
			$data[] = array('value' => $contact['id'],
								'label' => $contact['label'],
								'category' => $category);
		}
	}

	protected function search_contacts_by_listid($id)
	{
		return DB::select('plugin_contacts_contact.*')
					->from('plugin_contacts_contact')
					->where('plugin_contacts_contact.mailing_list', '=', $id)
					->and_where('plugin_contacts_contact.deleted', '=', 0)
					->execute()
					->as_array();
	}

    public function resolve_final_targets($target, &$target_list, &$warnings)
    {
		$driver       = isset($target['driver']) ? $target['driver'] : '';
        $item_id      = isset($target['target']) ? $target['target'] : '';
        $target_types = array(
            'sms'   => array('column' => 'mobile', 'target_type' => 'PHONE'),
            'email' => array('column' => 'email',  'target_type' => 'EMAIL')
        );

        if (array_key_exists($driver, $target_types)) {
            $target['target_type'] = $target_types[$driver]['target_type'];

            $contacts = $this->search_contacts_by_listid($item_id);
			foreach ($contacts as $contact) {
				if (!empty($contact[$target_types[$driver]['column']])) {
                    $target['target_type'] = $target_types[$driver]['target_type'];
                    $target['target']      = $contact[$target_types[$driver]['column']];
                    $target_list[]         = $target;
                } else {
                    $warnings[] = 'contact:' . $contact['id'] . ' does not have '.$target_types[$driver]['column'].' set for ' . $driver . ' messaging';
                }
            }
        } else {
            $warnings[] = $driver . ' messaging is not supported';
        }
    }

	public function message_details_column()
	{
		return "l1.name";
	}

	public function message_details_join($query)
	{
		$query->join(array('plugin_contacts_mailing_list', 'l1'), 'left')->on('t.target', '=', 'l1.id')->on('t.target_type', '=', DB::expr("'CMS_CONTACT_LIST'"));
	}
}