<?php defined('SYSPATH') or die('No direct script access.');

class Model_Course extends ORM
{
    protected $_table_name = 'plugin_courses_courses';

    protected $_has_many = [
        'images'       => ['model' => 'Course_Image',    'foreign_key' => 'course_id'],
        'providers'    => ['model' => 'Course_Provider', 'through' => 'plugin_courses_courses_has_providers', 'foreign_key' => 'course_id', 'far_key' => 'provider_id'],
        'schedules'    => ['model' => 'Course_Schedule', 'foreign_key' => 'course_id'],
        'surveys'      => ['model' => 'Survey', 'foreign_key' => 'course_id'],
        'testimonials' => ['model' => 'Testimonial', 'foreign_key' => 'course_id']
    ];

    protected $_belongs_to = [
        'category'   => ['model' => 'Course_Category',   'foreign_key' => 'category_id'],
        'curriculum' => ['model' => 'Course_Curriculum', 'foreign_key' => 'curriculum_id'],
        'level'      => ['model' => 'Course_Level',      'foreign_key' => 'level_id'],
        'subject'    => ['model' => 'Course_Subject',    'foreign_key' => 'subject_id'],
        'type'       => ['model' => 'Course_Type',       'foreign_key' => 'type_id'],
    ];

    /*
     * Get the colour associated with the course.
     * If the course has a subject use that colour. Otherwise, if the course has a category, use that colour.
     */
    public function get_color()
    {
        if ($this->subject->color) {
            return $this->subject->color;
        } else {
            return $this->category->color;
        }
    }

    public function get_url()
    {
        return '/course-detail/'.urlencode($this->title).'?id='.$this->id;
    }

    public function get_image_url($args = [])
    {
        $image = $this->images->find_undeleted();

        if ($image->image) {
            return Model_Media::get_image_path($image->image, 'courses');
        }
        elseif (isset($args['fallback']) && $args['fallback'] !== false) {
            return Model_Media::get_image_path('course-placeholder.png', 'courses');
        }
        else {
            return '';
        }
    }

    public function get_banner_image_url()
    {
        return $this->banner ? Model_Media::get_image_path($this->banner, 'courses') : '';
    }

    public function get_linked_page()
    {
        $return = new stdClass();
        if ($this->subject->id) {
            $return->page = $this->subject->pages->find_published();
            $return->text = $this->subject->name;
        } elseif ($this->category->id) {
            $return->page = $this->category->pages->find_published();
            $return->text = $this->category->category;
        } else {
            $return->page = new Model_Page();
            $return->text = '';
        }
        return $return;
    }

    public function accreditation_providers()
    {
        $type = new Model_Course_Provider_Type(['type' => 'Accreditation Body', 'delete' => 0]);
        return $this->providers->where('type_id', '=', $type->id);
    }


    /*
     * Filter by courses matching a search term.
     * Flag if results are title and/or content matches
     */
    public function search($term = '')
    {
        return $this
            ->select([DB::expr("CONCAT('/course-detail/', `title`, '?id=', `id`)"), 'url'])
            ->select(['title', 'search_title'])
            ->select([DB::expr("IF (`title` = " . Database::instance()->quote($term) . ", 1, 0)"), 'is_title_match'])
            ->select([DB::expr("IF (`title` LIKE " . Database::instance()->quote("%" . $term . "%") . ", 1, 0)"), 'is_partial_title_match'])
            ->select([DB::expr("IF (`description` LIKE " . Database::instance()->quote("%" . $term . "%") . ", 1, 0)"), 'is_content_match'])
            ->and_where_open()
            ->where('title', 'like', '%'.$term.'%')
            ->or_where('description', 'like', '%'.$term.'%')
            ->and_where_close()
            ->order_by('is_title_match', 'desc')
            ->order_by('is_partial_title_match', 'desc')
            ->order_by('is_content_match', 'desc')
            ->order_by('title');
    }

    /* Return the first sentence containing a searched term, with the searched term bolded */
    public function get_matching_content($term)
    {
        $content = preg_replace('/\n\s*\n\s*/', '¶', strip_tags(IbHelpers::parse_page_content($this->description)));
        $regex = '/[A-Z][^\\.\\?\\!\\¶]*('.$term.')[^\\.\\?\\!\\¶]*/i';

        if ($term && preg_match($regex, $content, $match)) {
            return trim(preg_replace('/('.$term.')/i', '<strong>$1</strong>', $match[0]));
        } else {
            return false;
        }
    }

    /* Check if all schedules within the course are group bookings  */
    public function is_group_booking_only()
    {
        $schedules = $this->schedules->find_all_published();

        $is_group_booking = true;

        for ($i = 0; $i < count($schedules) && $is_group_booking; $i++) {
            $is_group_booking = $schedules[$i]->is_group_booking;
        }

        return $is_group_booking;
    }

    public function find_upcoming($args = [])
    {
        $args = ['book_on_website' => true, 'unique_courses' => true] + $args;
        $args['unstarted_only'] = (Settings::instance()->get('upcoming_course_feed_order') == 'start_date');

        $results = Model_Courses::filter($args);
        $results = !empty($args['limit']) ? $results['data'] : $results['all_data'];

        // Get as ORM
        $available_ids = array_unique(array_column($results, 'id'));

        if (empty($available_ids)) {
            return [];
        }

        // Get available schedules, ordered by date
        $args = ['unstarted_only' => Settings::instance()->get('upcoming_course_feed_order') == 'start_date'];
        $schedules = ORM::factory('Course_Schedule')
            ->where_available_by_date($args)
            ->where('course_schedule.course_id', 'in', $available_ids)
            ->find_all();

        // Return their corresponding courses
        $return = [];
        foreach ($schedules as $schedule) {
            $return[$schedule->course_id] = new Model_Course($schedule->course_id);
        }

        return $return;
    }

    // Get the earliest upcoming timeslot of the course
    public function get_next_timeslot($filters = [])
    {
        // Get the first schedule found (i.e. the earliest)
        $schedule = $this
            ->schedules
            ->where_available_by_date($filters)
            ->find();

        // Return the earliest timeslot of that schedule.
        $timeslot_id = !empty($schedule->timeslot_id) ? $schedule->timeslot_id : null;
        return new Model_Course_Schedule_Event($timeslot_id);
    }

    public function find_all_available()
    {
        $results = Model_Courses::filter(['book_on_website' => true]);
        $available_ids = array_column($results['all_data'], 'id');
        if (!empty($available_ids)) {
            $this->where('id', 'in', $available_ids);
        }
        return $this->find_all_published();
    }

    /**
     * Fetch upcoming times for a course
     *
     * Copied logic as is used on the frontend dropdown.
     * todo: update frontend to call this function.
     * todo: make this more object-oriented
     */
    public function get_available_times($args = [])
    {
        $course_details = Model_Courses::get_detailed_info(
            $this->id,
            true,
            true,
            (Settings::instance()->get('only_show_primary_trainer_course_dropdown') === '1'),
            true
        );
        $return = [];
        $schedule_count = 0;
        foreach ($course_details['schedules'] as $schedule) {
            if (!empty($args['limit_schedules']) && $schedule_count >= $args['limit_schedules'] ) {
                continue;
            }
            if ((count($schedule['timeslots']) > 1 && $schedule['booking_type'] == 'One Timeslot')
                || (!empty($args['show_all_timeslots']) && $args['show_all_timeslots'] == 1)) {
                foreach ($schedule['timeslots'] as $timeslot) {
                    if (strtotime($timeslot['datetime_start']) < time()) {
                        continue;
                    }
                    $schedule_object = new Model_Course_Schedule($schedule['id']);
                    if (is_array($timeslot)){
                        $return[] = [
                            'start_time' => $timeslot['datetime_start'],
                            'end_time'   => $timeslot['datetime_end'],
                            'duration'   => '',
                            'schedule'   => $schedule_object->id,
                            'location'   => $schedule_object->location->name,
                            'fee'        => $schedule_object->fee_amount,
                            'county'     => $schedule_object->location->get_county()->name
                        ];
                    } else {
                        $return[] = [
                            'start_time' => $timeslot->datetime_start,
                            'end_time'   => $timeslot->datetime_end,
                            'duration'   => '',
                            'schedule'   => $schedule_object->id,
                            'location'   => $timeslot->location->name,
                            'fee'        => $timeslot->schedule->fee_amount,
                            'county'     => $schedule_object->location->get_county()->name
                        ];
                    }

                }
            } else {
                $start_date = ((isset($schedule['timeslots']) && isset($schedule['timeslots'][0])) ? $schedule['timeslots'][0]['datetime_start'] : $schedule['start_date']);
                $end_date = ((isset($schedule['timeslots']) && isset($schedule['timeslots'][0])) ? $schedule['timeslots'][0]['datetime_end'] : $schedule['end_date']);

                $schedule_object = new Model_Course_Schedule($schedule['id']);

                if ($schedule['repeat']) {
                    $duration_in_seconds = strtotime($end_date) - strtotime($start_date);
                    $duration_h = floor($duration_in_seconds / 3600);
                    $duration_m = (($duration_in_seconds % 3600) / 60);
                    $duration = ($duration_in_seconds > 0) ? ($duration_h . ($duration_m == 30 ? '.5' : '') . 'h ' . ($duration_m > 0 && $duration_m != 30 ? $duration_m . 'm' : '')) : '';

                    $return[] = [
                        'duration'   => $duration,
                        'start_time' => $start_date,
                        'end_time'   => $end_date,
                        'location'   => $schedule['location'],
                        'schedule'   => $schedule['id'],
                        'fee'        => $schedule['fee_amount'] ? '€'.$schedule['fee_amount'] : '',
                        'county'     => $schedule_object->location->get_county()->name
                    ];
                } else {
                    $return[] = [
                        'duration'   => '',
                        'start_time' => $start_date,
                        'end_time'   => $end_date,
                        'location'   => $schedule['location'],
                        'schedule'   => $schedule['id'],
                        'fee'        => $schedule['fee_amount'] ? '€'.$schedule['fee_amount'] : '',
                        'county'     => $schedule_object->location->get_county()->name
                    ];
                }
            }
            $schedule_count++;
        }
        if (!empty($args['time_format'])) {
            foreach ($return as &$return_item) {
                $return_item['start_time'] = date($args['time_format'], strtotime($return_item['start_time']));
                $return_item['end_time']   = date($args['time_format'], strtotime($return_item['end_time']));
            }
        }
        return $return;
    }

    public function render_timeslots_table()
    {
        $available_slots = $this->get_available_times(['limit_schedules' => 5, 'show_all_timeslots' => 1]);
        $document_timeslots = array();
        //group by schedules and locations
        foreach ($available_slots as $available_slot) {
            $time = date('j F Y H:i', strtotime($available_slot['start_time']))  . '-'.
                date('H:i ', strtotime($available_slot['end_time']));

            if (!empty($available_slot['county'])) {
                $document_timeslots[$available_slot['schedule']][$available_slot['county']][] = $time;
            } else {
                $document_timeslots[$available_slot['schedule']][$available_slot['location']][] = $time;
            }
        }
        $timeslots_table = '<table>';
        if (!empty($document_timeslots)) {
            //show only 5 schedules
            $document_timeslots = array_slice($document_timeslots, 0, 3);
            $timeslots_locations = array();
            $new_locations = array();

            //make array keys include schedule id to make separate locations with the same name for each schedule
            foreach($document_timeslots as $schedule => $location_timeslots) {
                foreach($location_timeslots as $location => $timeslots) {
                    if(in_array($schedule . '-' . $location, $timeslots_locations)) {
                        continue;
                    }
                    $timeslots_locations[] = $schedule . '-' . $location;
                }
                //move timeslote to new arrat with keys ['<schedule_id>-<location>' => ]
                $new_locations[$schedule . '-' . $location] = $timeslots;
            }
            $timeslots_table .= '<thead>';

            foreach ($timeslots_locations as $document_location) {

               $schedule_location = explode('-', $document_location);
                //do not show schedule ids when making table header
                $timeslots_table .= '<th>' . end($schedule_location) . '</th>';
            }
            $timeslots_table .='</thead><tbody>';
            $timeslots_counts = array();
            foreach ($new_locations as $location => $timeslots) {
                $timeslots_counts[$location] = count($timeslots);
            }

            $timetable_rows = array();
            $count_rows = max($timeslots_counts);

            for ($i = 0; $i < $count_rows; $i++) {
                foreach ($new_locations as $location_key => $column){
                    if (!isset($timetable_rows[$i])) {
                        $timetable_rows[$i]  = '';
                    }
                    if ($i == 0 && $location_key == reset($timeslots_locations)) {
                        $timetable_rows[$i] .=  '<tr>' ;
                    }
                    $cell_value = !empty($column[$i]) ? $column[$i] : '' ;
                    $timetable_rows[$i] .= '<td>' . $cell_value . '</td>';
                    if ($location_key == end($timeslots_locations)){
                        $timetable_rows[$i] .=  '</tr>';
                    }
                }
            }

            foreach ($timetable_rows as $timetable_row) {
                $timeslots_table .= $timetable_row;
            }
            $timeslots_table .='</tbody></table>';
        }
        return $timeslots_table;
    }
}