<?php defined('SYSPATH') or die('No direct script access.');

class Model_MessagingRecipientProviderContact3list implements Model_MessagingRecipientProvider
{
	public function pid()
	{
		return "CMS_CONTACT3_LIST";
	}

	public function supports($driver)
	{
		return in_array($driver, array('sms', 'email'));
	}

	public function get_by_id($id)
	{
		$data = DB::select('id', DB::expr("name as label"))
					->from(Model_Contacts3::ROLE_TABLE)
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
					->from(Model_Contacts3::ROLE_TABLE)
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
					->from(Model_Contacts3::ROLE_TABLE)
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
					->from(Model_Contacts3::ROLE_TABLE)
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
		return DB::select('contacts.*', array('has_email.value', 'email'), array('has_mobile.value', 'mobile'))
					->from(array(Model_Contacts3::ROLE_TABLE, 'roles'))
						->join(array(Model_Contacts3::CONTACT_ROLE_RELATION_TABLE, 'has_role'), 'left')
							->on('roles.id', '=', 'has_role.role_id')
						->join(array(Model_Contacts3::CONTACTS_TABLE, 'contacts'), 'left')
							->on('has_role.contact_id', '=', 'contacts.id')
						->join(array(Model_Contacts3::CONTACT_NOTIFICATION_RELATION_TABLE, 'has_email'), 'left')
							->on('contacts.notifications_group_id', '=', 'has_email.group_id')
							->on('has_email.notification_id', '=', DB::expr(1)) // 1 email
						->join(array(Model_Contacts3::CONTACT_NOTIFICATION_RELATION_TABLE, 'has_mobile'), 'left')
							->on('contacts.notifications_group_id', '=', 'has_mobile.group_id')
							->on('has_email.notification_id', '=', DB::expr(2)) // 1 mobile
					->where('roles.id', '=', $id)
					->and_where('contacts.delete', '=', 0)
					->execute()
					->as_array();
	}
	
	public function resolve_final_targets($target, &$target_list, &$warnings)
	{
        $driver    = isset($target['driver']) ? $target['driver'] : '';
        $target_id = isset($target['target']) ? $target['target'] : '';

        if ($driver == 'sms') {
			$contacts = $this->search_contacts_by_listid($target_id);
			foreach ($contacts as $contact) {
				if ($contact['mobile']) {
					$target['target_type'] = 'PHONE';
					$target['target'] = $contact['mobile'];
					$target_list[] = $target;
				} else {
					$warnings[] = 'contact:' . $contact['id'] . ' does not have mobile set for ' . $driver . ' messaging';
				}
			}
		} else if ($driver == 'email') {
			$contacts = $this->search_contacts_by_listid($target_id);
			foreach ($contacts as $contact) {
				if ($contact['email']) {
					$target['target_type'] = 'EMAIL';
					$target['target'] = $contact['email'];
					$target_list[] = $target;
				} else {
					$warnings[] = 'contact:' . $contact['id'] . ' does not have email set for ' . $driver . ' messaging';
				}
			}
		} else {
			$warnings[] = $driver . ' messaging is not supported';
		}
	}
	
	public function message_details_column()
	{
		return "c3r.name";
	}

	public function message_details_join($query)
	{
		$query->join(array(Model_Contacts3::ROLE_TABLE, 'c3r'), 'left')->on('t.target', '=', 'c3r.id')->on('t.target_type', '=', DB::expr("'CMS_CONTACT3_LIST'"));
	}
}