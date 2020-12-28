<?php defined('SYSPATH') or die('No direct script access.');

class Model_Contacts3_Contact extends ORM
{
    /* Inappropriate position, but necessary to avoid a conflict */
    public function has_wishlisted($schedule_id)
    {
        return ORM::factory('Course_Wishlist')->where('contact_id', '=', $this->id)->where('schedule_id', '=', $schedule_id)->find_undeleted()->loaded();
    }

    protected $_table_name = 'plugin_contacts3_contacts';
    protected $_deleted_column = 'delete';

    protected $_has_many = [
        'bookings' => ['model' => 'Booking_Booking', 'foreign_key' => 'contact_id'],
        'parents'  => ['model' => 'Contacts3_Contact', 'through' => 'plugin_contacts3_relations', 'foreign_key' => 'child_id', 'far_key' => 'parent_id'],
        'results'  => ['model' => 'Todo_Result',     'foreign_key' => 'student_id'],
    ];

    protected $_belongs_to = [
        'academic_year'   => ['model' => 'Course_Academicyear', 'foreign_key' => 'academic_year_id'],
        'address'         => ['model' => 'Contacts3_Residence', 'foreign_key' => 'residence'],
        'billing_address' => ['model' => 'Contacts3_Residence', 'foreign_key' => 'billing_residence_id'],
        'family'          => ['model' => 'Contacts3_Family',    'foreign_key' => 'family_id'],
        'notifications_group' => ['model' => 'Contacts3_NotificationGroup', 'foreign_key' => 'notifications_group_id'],
        'school'          => ['model' => 'Course_Provider',     'foreign_key' => 'school_id'],
        'year'            => ['model' => 'Course_Year',         'foreign_key' => 'year_id'],
    ];


    // Ensure the "full name" aggregate is also included in results
    public function find_all_undeleted()
    {
        $this->select([DB::expr("CONCAT(`contacts3_contact`.`first_name`, ' ', `contacts3_contact`.`last_name`)"), 'full_name']);
        return parent::find_all_undeleted();
    }

    public function get_full_name()
    {
        return trim($this->first_name.' '.$this->last_name);
    }

    public function get_name_and_details()
    {
        $name = $this->get_full_name();
        $email = $this->get_notification('email');
        $mobile = $this->get_notification('mobile');

        $return = $name;
        $return .= $email ? ' - '.$email : '';
        $return = $mobile ? $mobile . '- '.$return : $return;

        return $return;
    }

    public function get_email()
    {
        return $this->notifications_group->contact_notifications->where('notification_id', '=', '1')->find()->as_array();
    }

    // Get the mobile number components in an array
    public function get_mobile()
    {
        return $this->notifications_group->contact_notifications->where('deleted', '=' , 0)->and_where_open()->or_where('notification_id', '=', '2')->and_where_close()->find()->as_array();
    }

    /**
     * Get the full mobile number, including area codes and formatting.
     *
     * @param $separator string - Text to appear between segments of the number e.g. hyphen or space
     * @return string
     */
    public function get_mobile_number($separator = '')
    {
        $number = $this->get_mobile();

        $return = '';

        if (!empty($number['country_dial_code'])) {
            // Add the country code, prefixed with a "+"
            $return .= '+' . $number['country_dial_code'];
        }

        if (!empty($number['dial_code'])) {
            if (!empty($number['country_dial_code'])) {
                // If the country code has been specified, trim the leading zero from the local area code
                $return .= $separator . ltrim($number['dial_code'], '0');
            } else {
                // Otherwise, use the local area code, including the leading zero.
                $return .= $number['dial_code'];
            }
        }

        $return .= $separator . $number['value'];

        return trim($return, $separator);
    }

    public function where_email_like($email, $join = 'left')
    {
        return $this->join(array(Model_Contacts3::CONTACT_NOTIFICATION_RELATION_TABLE, 'contacts3_notification_email'), $join)
                    ->on('contacts3_notification_email.group_id', '=', 'contacts3_contact.notifications_group_id')
                    ->on('contacts3_notification_email.notification_id', '=', DB::expr("
                    (SELECT 
                        id
                    FROM
                    plugin_contacts3_notifications
                    WHERE
                        stub = 'email'
                    LIMIT 1)
                "))
                ->where('contacts3_notification_email.value', 'like', "%{$email}%");
    }

    public function where_mobile_like($mobile, $join = 'left')
    {
        $landline_mobile = DB::select('id')->from('plugin_contacts3_notifications')
            ->where('stub', 'in', ['landline', 'mobile']);
        return $this->join(array(Model_Contacts3::CONTACT_NOTIFICATION_RELATION_TABLE, 'contacts3_notification_mobile'), $join)
                ->on('contacts3_notification_mobile.group_id', '=', 'contacts3_contact.notifications_group_id')
                ->on('contacts3_notification_mobile.notification_id', 'in', $landline_mobile)
            ->where('contacts3_notification_mobile.value', 'like', "%{$mobile}%");
    }

    public function where_is_current_user()
    {
        $user = Auth::instance()->get_user();

        return $this
            ->join(['plugin_contacts3_users_has_permission', 'uhp'])
            ->on('uhp.contact3_id', '=', 'booking_applicant.id')
            ->where('uhp.user_id', '=', $user['id']);
    }

    /**
     * Check if a contact has a certain tag
     *
     * @param $tag  - The name of the tag
     * @return bool
     */
    public function has_tag($tag)
    {
        $tag_object = Model_Contacts3_Tag::get_tag_by_name($tag);
        $c3 = new Model_Contacts3($this->id);
        return $c3->has_tag($tag_object);
    }

    // We could replace this with some separate linked ORM model files
    public function get_notification($type)
    {
        // Get contact detail linked to the contact
        $q = DB::select('chn.*')
            ->from(['plugin_contacts3_contact_has_notifications', 'chn'])
            ->join(['plugin_contacts3_notifications', 'notification'])->on('chn.notification_id', '=', 'notification.id')
            ->where('chn.group_id', '=', $this->notifications_group_id)
            ->where('notification.stub', '=', $type)
            ->execute();

        $value = isset($q[0]) ? $q[0]['value'] : null;

        // If no value was found, checked the linked user
        if (!$value && in_array($type, ['email', 'mobile'])) {
            $user  = Model_Contacts3::get_user_by_contact_id($this->id);
            $value = isset($user[$type]) ? $user[$type] : $value;
        }

        return $value;
    }

    public function get_role_in($department_id)
    {
        return DB::select('role')
            ->from('plugin_contacts3_relations')
            ->where('child_id', '=', $this->id)
            ->where('parent_id', '=', $department_id)
            ->execute()->get('role', 0);
    }

    public function get_timeoff_hours()
    {
        if (!$this->id) {
            return [];
        }

        $results = DB::select()->from('plugin_timeoff_config')->where('item_id', '=', $this->id)->execute()->as_array();

        $return = [];
        foreach ($results as $result) {
            $label = strtolower(str_replace('timeoff.', '', $result['name']));
            if (strpos($label, 'time_preferences_') === 0) {
                $label = str_replace('time_preferences_', '', $label) . '_hours';
            }

            $return[$label] = $result;
        }

        return $return;
    }

    // Only fetch contacts who have bookings
    public function where_has_bookings()
    {
        $contact_ids_with_bookings = ORM::factory('Booking_Booking')->find_all_undeleted()->as_array('booking_id', 'contact_id');
        $contact_ids_with_bookings = count($contact_ids_with_bookings) ? $contact_ids_with_bookings : [];

        return $this->where('id', 'in', $contact_ids_with_bookings);
    }

    public function bookings()
    {
        return ORM::factory('Booking_Booking')
            ->join(['plugin_ib_educate_booking_items',         'item'], 'left')
                ->on('item.booking_id', '=', 'booking_booking.booking_id')
                ->on('item.delete', '=', DB::expr("0"))
                ->on('item.booking_status', '<>', DB::expr(Model_KES_Bookings::CANCELLED))
                ->on('item.delete', '=', DB::expr("0"))
                ->on('item.booking_status', '<>', DB::expr(Model_KES_Bookings::CANCELLED))
            ->join(['plugin_ib_educate_bookings_has_delegates', 'bhd'], 'left')
                ->on('bhd.booking_id', '=', 'booking_booking.booking_id')

            ->and_where_open()
                // If this is not a group booking, get the lead booker.
                ->and_where_open()
                    ->and_where('booking_booking.contact_id', '=', $this->id)
                    ->and_where('bhd.id', 'is', null)
                ->and_where_close()

                // If this is a group booking, get all delegates.
                // The delegates table will include the lead booker (only if they choose to be a delegate)
                ->or_where_open()
                    ->where('bhd.contact_id', '=', $this->id)
                    ->and_where('bhd.cancelled', '<>', 1)
                ->or_where_close()
            ->and_where_close()
            ->group_by('booking_booking.booking_id');
    }

    public function booking_items($filters = [])
    {
        return ORM::factory('Booking_Item')
            ->apply_filters($filters)
            ->where('booking.contact_id', '=', $this->id);
    }

    public function get_booked_classes($args = [])
    {
        $q = DB::select()
            ->from(['plugin_ib_educate_bookings',      'booking' ])
            ->join(['plugin_ib_educate_booking_items', 'item'    ])->on('item.booking_id', '=', 'booking.booking_id')
            ->join(['plugin_courses_schedules_events', 'timeslot'])->on('item.period_id',  '=', 'timeslot.id')
            ->where('booking.delete', '=', 0)
            ->where('booking.contact_id', '=', $this->id)
        ;

        if (!empty($args['academic_year_id'])) {
            $academic_year = new Model_Course_Academicyear($args['academic_year_id']);
            $q->where('timeslot.datetime_start', '>=', $academic_year->start_date);
            $q->where('timeslot.datetime_start', '<=', $academic_year->end_date);
        }

        if (!empty($args['attendance_status'])) {
            $q->where('item.timeslot_status', '=', $args['attendance_status']);
        }

        return $q->execute();
    }

    public function save_data($data)
    {
        $this->values($data);
        $this->save_with_moddate();

        if ($data['email']) {
            $this->notifications_group->save();
            $this->notifications_group_id = $this->notifications_group->id;
            $notification = new Model_Contacts3_Notification();
            $notification->set('group_id', $this->notifications_group_id);
            $notification->notification_id = 1;
            $notification->value = $data['email'];
            $notification->save_with_moddate();
        }

        if ($data['mobile']) {
            $this->notifications_group->save();
            $this->notifications_group_id = $this->notifications_group->id;
            $notification = new Model_Contacts3_Notification();
            $notification->set('group_id', $this->notifications_group_id);
            $notification->notification_id = 2;
            $notification->value = $data['mobile'];
            $notification->save_with_moddate();
        }

        $this->save_with_moddate();
    }

    /**
     * Add a preference to a contact
     * @param $preference_name  string  Name of the preference
     */
    public function add_preference($preference_name)
    {
        // Load the preference as an object
        $preference = new Model_Contacts3_Preference(['stub' =>  $preference_name]);

        // Create contact-preference link or load existing one, if it already exists.
        $has_preference = new Model_Contacts3_HasPreference([
            'contact_id' => $this->id,
            'preference_id' => $preference->id,
            'deleted' => 0
        ]);

        // If the contact already has the preference, no need to continue
        if (!$has_preference->id) {
            // Set values and save
            $has_preference->set('contact_id', $this->id);
            $has_preference->set('preference_id', $preference->id);
            $has_preference->save_with_moddate();
        }
    }

    /**
     * Add a tag to a contact
     * @param $tag_name string Name of the tag
     */
    public function add_tag($tag_name)
    {
        $tag = Model_Contacts3_Tag::get_tag_by_name($tag_name);

        // Directly insert, until an ORM has been set up
        if (!is_null($tag) && !empty($tag->get_id())) {

            // Check if contact already has this tag.
            $already_tagged = DB::select()
                ->from(Model_Contacts3_Tag::CONTACT_HAS_TAG_TABLE)
                ->where('contact_id', '=', $this->id)
                ->where('tag_id', '=', $tag->get_id())
                ->execute()->count();

            // Add the tag, if not already there.
            if (empty($already_tagged)) {
                DB::insert(Model_Contacts3_Tag::CONTACT_HAS_TAG_TABLE)
                    ->values(['contact_id' => $this->id, 'tag_id' => $tag->get_id()])
                    ->execute();
            }
        }
    }


}
