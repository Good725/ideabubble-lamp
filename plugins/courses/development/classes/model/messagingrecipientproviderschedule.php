<?php defined('SYSPATH') or die('No direct script access.');

class Model_MessagingRecipientProviderSchedule implements Model_MessagingRecipientProvider
{
	public function pid()
	{
		return "CMS_SCHEDULE";
	}

	public function supports($driver)
	{
		return in_array($driver, array('sms', 'email'));
	}

	public function get_by_id($id)
	{
		$data = DB::select('id', DB::expr("name as label"))
					->from(Model_Schedules::TABLE_SCHEDULES)
					->where('id', '=', $id)
					->execute()
					->current();
		return $data;
	}
	
	public function get_by_label($label)
	{
		$data = DB::select('id', DB::expr("name as label"))
				->from(Model_Schedules::TABLE_SCHEDULES)
				->where('name', '=', $label)
				->execute()
				->current();
		return $data;
	}
	
	public function search($term)
	{
		$data = DB::select('schedules.id', DB::expr("CONCAT(courses.title, ' ', schedules.name) as label"))
				->from(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'))
					->join(array(Model_Courses::TABLE_COURSES, 'courses'), 'inner')
						->on('schedules.course_id', '=', 'courses.id')
				->where('schedules.name', 'like', '%' . $term . '%')
				->or_where('courses.title', 'like', '%' . $term . '%')
				->order_by('schedules.name')
				->execute()
				->as_array();
		return $data;
	}
	
	public function to_autocomplete($term, &$data)
	{
		$category = $this->pid();
		$schedules = $this->search($term);
		foreach($schedules as $schedule){
			$data[] = array('value' => $schedule['id'],
								'label' => $schedule['label'],
								'category' => $category);
		}
	}

    public function resolve_final_targets($target, &$target_list, &$warnings)
    {
        $driver       = isset($target['driver']) ? $target['driver'] : '';
        $item_id      = isset($target['target']) ? $target['target'] : '';
        $check_fields = array('sms' => 'mobile', 'email' => 'email');
        $target_types = array(
            'sms'   => array('column' => 'mobile', 'target_type' => 'PHONE'),
            'email' => array('column' => 'email',  'target_type' => 'EMAIL')
        );

        if (array_key_exists($driver, $target_types)) {
            $students = Model_CourseBookings::search(array('schedule_id' => $item_id, 'status' => array('Pending', 'Confirmed')));

            foreach ($students as $student) {
                if (!empty($student[$target_types[$driver]['column']])) {
                    $target['target_type'] = $student[$target_types[$driver]['column']];
                    $target['target']      = $student[$check_fields[$driver]];
                    $target_list[]         = $target;
                } else {
                    $warnings[] = 'Contact #' . $student['id'] . ' does not have '.$check_fields[$driver].' set for ' . $driver . ' messaging';
                }
            }
        } else {
            $warnings[] = $driver . ' messaging is not supported';
        }
    }

	public function message_details_column()
	{
		return "schedules1.name";
	}

	public function message_details_join($query)
	{
		$query->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules1'), 'left')
				->on('t.target', '=', 'schedules1.id')
				->on('t.target_type', '=', DB::expr("'CMS_SCHEDULE'"));
	}
}