<?php defined('SYSPATH') or die('No direct script access.');

class Model_MessagingRecipientProviderContact3 implements Model_MessagingRecipientProvider
{
	public function pid()
	{
		return "CMS_CONTACT3";
	}

	public function supports($driver)
	{
		return in_array($driver, array('sms', 'email'));
	}

	public function get_by_id($id)
	{
		$data = DB::select('contact3.id', DB::expr('CONCAT_WS(" ", contact3.first_name, contact3.last_name) AS label'), DB::expr('rnotifications_e.value as email'), DB::expr('rnotifications_m.value as sms'))
					->from(array('plugin_contacts3_contacts', 'contact3'))
					->join(array('plugin_contacts3_contact_type', 'type'), 'left')->on('contact3.type',      '=', 'type.contact_type_id')
					->join(array('plugin_contacts3_family', 'family'), 'left')->on('contact3.family_id', '=', 'family.family_id')
					->join(array('plugin_contacts3_contact_has_notifications', 'rnotifications_e'), 'LEFT')->on('contact3.notifications_group_id', '=', 'rnotifications_e.group_id')->on('rnotifications_e.notification_id', '=', DB::expr('1'))
					->join(array('plugin_contacts3_contact_has_notifications', 'rnotifications_m'), 'LEFT')->on('contact3.notifications_group_id', '=', 'rnotifications_m.group_id')->on('rnotifications_m.notification_id', '=', DB::expr('2'))
					->where('contact3.id', '=', $id)
					->group_by('contact3.id')
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
		$data = DB::select('contact3.id', DB::expr('CONCAT_WS(" ", contact3.first_name, contact3.last_name) AS label'), DB::expr('rnotifications_e.value as email'), DB::expr('rnotifications_m.value as sms'))
					->from(array('plugin_contacts3_contacts', 'contact3'))
					->join(array('plugin_contacts3_contact_type', 'type'), 'left')->on('contact3.type',      '=', 'type.contact_type_id')
					->join(array('plugin_contacts3_family', 'family'), 'left')->on('contact3.family_id', '=', 'family.family_id')
					->join(array('plugin_contacts3_contact_has_notifications', 'rnotifications_e'), 'LEFT')->on('contact3.notifications_group_id', '=', 'rnotifications_e.group_id')->on('rnotifications_e.notification_id', '=', DB::expr('1'))
					->join(array('plugin_contacts3_contact_has_notifications', 'rnotifications_m'), 'LEFT')->on('contact3.notifications_group_id', '=', 'rnotifications_m.group_id')->on('rnotifications_m.notification_id', '=', DB::expr('2'))
					->where(DB::expr("CONCAT_WS(' ',contact3.first_name, contact3.last_name)"), '=', $label)
					->group_by('contact3.id')
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
		return DB::select('contact3.id', DB::expr('CONCAT_WS(" ", contact3.first_name, contact3.last_name) AS label'), DB::expr('rnotifications_e.value as email'), DB::expr('rnotifications_m.value as sms'))
					->from(array('plugin_contacts3_contacts', 'contact3'))
					->join(array('plugin_contacts3_contact_type', 'type'), 'left')->on('contact3.type',      '=', 'type.contact_type_id')
					->join(array('plugin_contacts3_family', 'family'), 'left')->on('contact3.family_id', '=', 'family.family_id')
					->join(array('plugin_contacts3_contact_has_notifications', 'rnotifications_e'), 'LEFT')->on('contact3.notifications_group_id', '=', 'rnotifications_e.group_id')->on('rnotifications_e.notification_id', '=', DB::expr('1'))
					->join(array('plugin_contacts3_contact_has_notifications', 'rnotifications_m'), 'LEFT')->on('contact3.notifications_group_id', '=', 'rnotifications_m.group_id')->on('rnotifications_m.notification_id', '=', DB::expr('2'))
					->group_by('contact3.id')
					->group_by('contact3.id')
					->where('contact3.delete', '=', 0)
					->and_where_open()
					->or_where('rnotifications_e.value', 'like', '%' . $term . '%')
					->or_where('rnotifications_m.value', 'like', '%' . $term . '%')
					->or_where('contact3.first_name', 'like', '%' . $term . '%')
					->or_where('contact3.last_name', 'like', '%' . $term . '%')
					->and_where_close()
					->order_by('contact3.first_name', 'asc')
					->order_by('contact3.last_name', 'asc')
					->limit(100)
					->execute()
					->as_array();
	}
	
	public function to_autocomplete($term, &$data, $driver = '')
	{
		$category = $this->pid();
		$contacts = DB::select('contact3.id', DB::expr('CONCAT_WS(" ", contact3.first_name, contact3.last_name) AS label'), DB::expr('rnotifications_e.value as email'), DB::expr('rnotifications_m.value as sms'))
					->from(array('plugin_contacts3_contacts', 'contact3'))
					->join(array('plugin_contacts3_contact_type', 'type'), 'left')->on('contact3.type',      '=', 'type.contact_type_id')
					->join(array('plugin_contacts3_family', 'family'), 'left')->on('contact3.family_id', '=', 'family.family_id')
					->join(array('plugin_contacts3_contact_has_notifications', 'rnotifications_e'), 'LEFT')->on('contact3.notifications_group_id', '=', 'rnotifications_e.group_id')->on('rnotifications_e.notification_id', '=', DB::expr('1'))
					->join(array('plugin_contacts3_contact_has_notifications', 'rnotifications_m'), 'LEFT')->on('contact3.notifications_group_id', '=', 'rnotifications_m.group_id')->on('rnotifications_m.notification_id', '=', DB::expr('2'))
					->group_by('contact3.id')
					->where('contact3.delete', '=', 0)
					->and_where_open()
					->or_where('rnotifications_e.value', 'like', '%' . $term . '%')
					->or_where('rnotifications_m.value', 'like', '%' . $term . '%')
					->or_where('contact3.first_name', 'like', '%' . $term . '%')
					->or_where('contact3.last_name', 'like', '%' . $term . '%')
					->and_where_close()
					->order_by('contact3.first_name', 'asc')
					->order_by('contact3.last_name', 'asc')
					->limit(5)
					->execute()
					->as_array();
		foreach ($contacts as $contact3)
		{
			$label = $contact3['label'];
			$applicable_preferences = array('emergency', 'accounts', 'absentee', 'reminders');

			if ($driver == 'sms')
			{
				$label .= ($contact3['sms'] != '') ? ' ('.$contact3['sms'].')' : '';
				$applicable_preferences[] = 'marketing_updates';
			}
			elseif ($driver == 'email')
			{
				$label .= ($contact3['email'] != '') ? ' ('.$contact3['email'].')' : '';
				$applicable_preferences[] = 'marketing_updates';
			}

			// Performing this as a join in the above query severely slows things down
			$preferences = DB::select('preference.label')
				->from(array('plugin_contacts3_contact_has_preferences', 'has_preference'))
				->join(array('plugin_contacts3_preferences', 'preference'))
				->on('has_preference.preference_id', '=', 'preference.id')
				->where('has_preference.contact_id', '=', $contact3['id'])
				->where('preference.label', 'in', $applicable_preferences)
				->execute()
				->as_array()
			;
			if (count($preferences) > 0)
			{
				foreach ($preferences as $preference)
				{
					$label .= ' ⟨'.$preference['label'].'⟩';
				}
			}


			$data[] = array(
				'value'       => $contact3['id'],
				'label'       => $label,
				'category'    => $category,
				'email'       => $contact3['email'],
				'sms'         => $contact3['sms']
			);
		}
	}

	public function search_for_message_by_id($id, $stub)
	{
		$contact3 = DB::select(DB::expr('contact3.*, rnotifications.value, rnotifications.country_dial_code, rnotifications.dial_code'))
							->from(array('plugin_contacts3_contacts', 'contact3'))
							->join(array('plugin_contacts3_contact_type', 'type'), 'left')->on('contact3.type',      '=', 'type.contact_type_id')
							->join(array('plugin_contacts3_family', 'family'), 'left')->on('contact3.family_id', '=', 'family.family_id')
							->join(array('plugin_contacts3_contact_has_notifications', 'rnotifications'), 'LEFT')->on('contact3.notifications_group_id', '=', 'rnotifications.group_id')
							->join(array('plugin_contacts3_notifications', 'notifications'), 'left')->on('rnotifications.notification_id', '=', 'notifications.id')
							->where('contact3.delete', '=', 0)
							->and_where('notifications.stub', '=', $stub)
							->and_where('contact3.id', '=', $id)
                            ->and_where('rnotifications.deleted', '=', 0)
							->order_by('rnotifications.preferred', 'desc')
							->limit(1)
							->execute()
							->as_array();
		if($contact3){
			return $contact3[0];
		} else {
			return false;
		}
	}

	public function resolve_final_targets($target, &$target_list, &$warnings)
	{
        $driver    = isset($target['driver']) ? $target['driver'] : '';
        $target_id = isset($target['target']) ? $target['target'] : '';

        if ($driver == 'sms') {
			$contact = $this->search_for_message_by_id((int)$target_id, 'mobile');
			if ($contact && $contact['value']) {
				$target['target_type'] = 'PHONE';
				if (!empty($contact['dial_code'])) {
                    $target['target'] = '+' . $contact['country_dial_code'] . $contact['dial_code'] . $contact['value'];
                } else {
                    $target['target'] = $contact['value'];
                }
				$target_list[] = $target;
			} else {
				$warnings[] = 'contact3:' . $contact['id'] . ' does not have mobile set for ' . $driver . ' messaging';
			}
		} else if ($driver == 'email') {
			$contact = $this->search_for_message_by_id((int)$target_id, 'email');
				if ($contact && $contact['value']) {
					$target['target_type'] = 'EMAIL';
					$target['target'] = $contact['value'];
					$target_list[] = $target;
				} else {
					$warnings[] = 'contact3:' . $contact['id'] . ' does not have email set for ' . $driver . ' messaging';
				}
		} else {
			$warnings[] = $driver . ' messaging is not supported';
		}
	}
	
	public function message_details_column()
	{
		return "CONCAT_WS(' ', c3.first_name, c3.last_name)";
	}

	public function message_details_join($query)
	{
		$query->join(array('plugin_contacts3_contacts', 'c3'), 'left')->on('t.target', '=', 'c3.id')->on('t.target_type', '=', DB::expr("'CMS_CONTACT3'"));
	}
}