<?php
class Model_GDPR
{
    const CLEANSE_REPORTS = 'plugin_contacts3_gdpr_cleanse_reports';
    public static function get_reports()
    {
        $reports = DB::select('reports.*')
            ->from(array(Model_Reports::MAIN_TABLE, 'reports'))
                ->join(array(self::CLEANSE_REPORTS, 'cleanse'), 'inner')
                    ->on('reports.name', '=', 'cleanse.report_name')
            ->where('reports.delete', '=', 0)
            ->and_where('reports.publish', '=', 1)
            ->execute()
            ->as_array();
        return $reports;
    }

    public static function cleanse_organisation($contact_id, $report_id, $options = array())
    {
        //cleanse emails 1
        $emails = DB::select('emails.id')
            ->from(array(Model_Contacts3::CONTACT_NOTIFICATION_RELATION_TABLE, 'emails'))
            ->join(array(Model_Contacts3::CONTACTS_TABLE, 'contacts'), 'inner')
            ->on('emails.group_id', '=', 'contacts.notifications_group_id')
            ->where('contacts.id', '=', $contact_id)
            ->execute()
            ->as_array('id');
        if (count($emails) > 0) {
            DB::query(
                null,
                "UPDATE plugin_messaging_messages m
                        inner join plugin_messaging_message_targets t on m.id = t.message_id
                        inner join plugin_messaging_message_final_targets f on t.id = f.target_id
                        inner join plugin_contacts3_contact_has_notifications emails on f.target = emails.`value`
                        inner join plugin_contacts3_contacts contacts on emails.group_id = contacts.notifications_group_id
                    set f.target = 'gdpr', m.`subject` = 'gdpr', m.message = 'gdpr'
                    where contacts.id = " . $contact_id
            )->execute();
            DB::query(
                null,
                "UPDATE plugin_messaging_messages m
                        inner join plugin_messaging_message_targets t on m.id = t.message_id
                        inner join plugin_messaging_message_final_targets f on t.id = f.target_id
                        inner join plugin_contacts3_contact_has_notifications emails on t.target = emails.`value`
                        inner join plugin_contacts3_contacts contacts on emails.group_id = contacts.notifications_group_id
                    set f.target = 'gdpr', t.target = 'gdpr', m.`subject` = 'gdpr', m.message = 'gdpr'
                    where contacts.id = " . $contact_id
            )->execute();
            DB::update(Model_Contacts3::CONTACT_NOTIFICATION_RELATION_TABLE)
                ->set(array('value' => '', 'country_dial_code' => '', 'dial_code' => ''))
                ->where('id', 'in', $emails)
                ->execute();
        }

        //cleanse emails 2
        $emails = DB::select('emails.id')
            ->from(array(Model_Contacts3::CONTACT_NOTIFICATION_RELATION_TABLE, 'emails'))
            ->join(array(Model_Contacts3::CONTACTS_TABLE, 'contacts'), 'inner')
            ->on('emails.contact_id', '=', 'contacts.id')
            ->where('contacts.id', '=', $contact_id)
            ->execute()
            ->as_array('id');
        if (count($emails) > 0) {
            DB::query(
                null,
                "UPDATE plugin_messaging_messages m
                        inner join plugin_messaging_message_targets t on m.id = t.message_id
                        inner join plugin_messaging_message_final_targets f on t.id = f.target_id
                        inner join plugin_contacts3_contact_has_notifications emails on f.target = emails.`value`
                        inner join plugin_contacts3_contacts contacts on emails.contact_id = contacts.id
                    set f.target = 'gdpr', m.`subject` = 'gdpr', m.message = 'gdpr'
                    where contacts.id = " . $contact_id
            )->execute();
            DB::query(
                null,
                "UPDATE plugin_messaging_messages m
                        inner join plugin_messaging_message_targets t on m.id = t.message_id
                        inner join plugin_messaging_message_final_targets f on t.id = f.target_id
                        inner join plugin_contacts3_contact_has_notifications emails on t.target = emails.`value`
                        inner join plugin_contacts3_contacts contacts on emails.contact_id = contacts.id
                    set f.target = 'gdpr', t.target = 'gdpr', m.`subject` = 'gdpr', m.message = 'gdpr'
                    where contacts.id = " . $contact_id
            )->execute();
            DB::update(Model_Contacts3::CONTACT_NOTIFICATION_RELATION_TABLE)
                ->set(array('value' => '', 'country_dial_code' => '', 'dial_code' => ''))
                ->where('id', 'in', $emails)
                ->execute();
        }
    }

    public static function cleanse_contact($contact_id, $report_id, $options = array())
    {
        //cleanse emails 1
        $emails = DB::select('emails.id')
            ->from(array(Model_Contacts3::CONTACT_NOTIFICATION_RELATION_TABLE, 'emails'))
                ->join(array(Model_Contacts3::CONTACTS_TABLE, 'contacts'), 'inner')
                    ->on('emails.group_id', '=', 'contacts.notifications_group_id')
            ->where('contacts.id', '=', $contact_id)
            ->execute()
            ->as_array('id');
        if (count($emails) > 0) {
            DB::query(
                null,
                "UPDATE plugin_messaging_messages m
                        inner join plugin_messaging_message_targets t on m.id = t.message_id
                        inner join plugin_messaging_message_final_targets f on t.id = f.target_id
                        inner join plugin_contacts3_contact_has_notifications emails on f.target = emails.`value`
                        inner join plugin_contacts3_contacts contacts on emails.group_id = contacts.notifications_group_id
                    set f.target = 'gdpr', m.`subject` = 'gdpr', m.message = 'gdpr'
                    where contacts.id = " . $contact_id
            )->execute();
            DB::query(
                null,
                "UPDATE plugin_messaging_messages m
                        inner join plugin_messaging_message_targets t on m.id = t.message_id
                        inner join plugin_messaging_message_final_targets f on t.id = f.target_id
                        inner join plugin_contacts3_contact_has_notifications emails on t.target = emails.`value`
                        inner join plugin_contacts3_contacts contacts on emails.group_id = contacts.notifications_group_id
                    set f.target = 'gdpr', t.target = 'gdpr', m.`subject` = 'gdpr', m.message = 'gdpr'
                    where contacts.id = " . $contact_id
            )->execute();
            DB::update(Model_Contacts3::CONTACT_NOTIFICATION_RELATION_TABLE)
                ->set(array('value' => '', 'country_dial_code' => '', 'dial_code' => ''))
                ->where('id', 'in', $emails)
                ->execute();
        }

        //cleanse emails 2
        $emails = DB::select('emails.id')
            ->from(array(Model_Contacts3::CONTACT_NOTIFICATION_RELATION_TABLE, 'emails'))
                ->join(array(Model_Contacts3::CONTACTS_TABLE, 'contacts'), 'inner')
                    ->on('emails.contact_id', '=', 'contacts.id')
            ->where('contacts.id', '=', $contact_id)
            ->execute()
            ->as_array('id');
        if (count($emails) > 0) {
            DB::query(
                null,
                "UPDATE plugin_messaging_messages m
                        inner join plugin_messaging_message_targets t on m.id = t.message_id
                        inner join plugin_messaging_message_final_targets f on t.id = f.target_id
                        inner join plugin_contacts3_contact_has_notifications emails on f.target = emails.`value`
                        inner join plugin_contacts3_contacts contacts on emails.contact_id = contacts.id
                    set f.target = 'gdpr', m.`subject` = 'gdpr', m.message = 'gdpr'
                    where contacts.id = " . $contact_id
            )->execute();
            DB::query(
                null,
                "UPDATE plugin_messaging_messages m
                        inner join plugin_messaging_message_targets t on m.id = t.message_id
                        inner join plugin_messaging_message_final_targets f on t.id = f.target_id
                        inner join plugin_contacts3_contact_has_notifications emails on t.target = emails.`value`
                        inner join plugin_contacts3_contacts contacts on emails.contact_id = contacts.id
                    set f.target = 'gdpr', t.target = 'gdpr', m.`subject` = 'gdpr', m.message = 'gdpr'
                    where contacts.id = " . $contact_id
            )->execute();
            DB::update(Model_Contacts3::CONTACT_NOTIFICATION_RELATION_TABLE)
                ->set(array('value' => '', 'country_dial_code' => '', 'dial_code' => ''))
                ->where('id', 'in', $emails)
                ->execute();
        }

        //cleanse address
        $addresses = DB::select('address_id')
            ->from(array(Model_Contacts3::ADDRESS_TABLE, 'addresses'))
                ->join(array(Model_Contacts3::CONTACTS_TABLE, 'contacts'), 'inner')
                    ->on('addresses.address_id', '=', 'contacts.residence')
            ->where('contacts.id', '=', $contact_id)
            ->execute()
            ->as_array('address_id');
        if (count($addresses) > 0) {
            DB::update(Model_Contacts3::ADDRESS_TABLE)
                ->set(array('address1' => '', 'address2' => '', 'address3' => '', 'postcode' => '', 'coordinates' => '', 'county' => null, 'country' => 'GDPR', 'town' => ''))
                ->where('address_id', 'in', $addresses)
                ->execute();
        }

        //cleanse billing address
        $addresses = DB::select('address_id')
            ->from(array(Model_Contacts3::ADDRESS_TABLE, 'addresses'))
            ->join(array(Model_Contacts3::CONTACTS_TABLE, 'contacts'), 'inner')
            ->on('addresses.address_id', '=', 'contacts.billing_residence_id')
            ->where('contacts.id', '=', $contact_id)
            ->execute()
            ->as_array('address_id');
        if (count($addresses) > 0) {
            DB::update(Model_Contacts3::ADDRESS_TABLE)
                ->set(array('address1' => '', 'address2' => '', 'address3' => '', 'postcode' => '', 'coordinates' => '', 'county' => null, 'country' => 'GDPR', 'town' => ''))
                ->where('address_id', 'in', $addresses)
                ->execute();
        }

        // cleanse contact
        DB::update(Model_Contacts3::CONTACTS_TABLE)
            ->set(
                array(
                    'first_name' => 'GDPR',
                    'last_name' => '',
                    'date_of_birth' => null,
                    'pps_number' => '',
                    'job_title' => '',
                    'job_function_id' => null,
                    'nationality' => '',
                    'gender' => '',
                    'gdpr_cleansed_datetime' => date::now(),
                    'gdpr_cleansed_by_report_id' => $report_id
                )
            )->where('id', '=', $contact_id)
            ->execute();

        //cleanse notes
        DB::query(
            Database::UPDATE,
            "UPDATE
	plugin_contacts3_notes notes
		INNER JOIN plugin_contacts3_contacts contacts ON notes.link_id = contacts.id AND notes.table_link_id=1
	SET notes.note = 'GDPR', notes.gdpr_cleansed_datetime=NOW(), notes.gdpr_cleansed_by_report_id=" . ((int)$report_id) . "
	WHERE notes.gdpr_cleansed_by_report_id IS NULL AND contacts.id = " . ((int)$contact_id)
        )->execute();
        DB::query(
            Database::UPDATE,
            "UPDATE
	plugin_contacts3_notes notes
		INNER JOIN plugin_ib_educate_bookings bookings on notes.link_id = bookings.booking_id AND notes.table_link_id = 4
		INNER JOIN plugin_contacts3_contacts contacts ON bookings.contact_id = contacts.id
	SET notes.note = 'GDPR', notes.gdpr_cleansed_datetime=NOW(), notes.gdpr_cleansed_by_report_id=" . ((int)$report_id) . "
	WHERE notes.gdpr_cleansed_by_report_id IS NULL AND contacts.id = " . ((int)$contact_id)
        )->execute();
        DB::query(
            Database::UPDATE,
            "UPDATE
	plugin_contacts3_notes notes
		INNER JOIN plugin_ib_educate_booking_items items ON notes.link_id = items.booking_item_id AND notes.table_link_id = 3
		INNER JOIN plugin_ib_educate_bookings_has_delegates has_delegates ON items.booking_id = has_delegates.booking_id
		INNER JOIN plugin_contacts3_contacts contacts ON has_delegates.contact_id = contacts.id
	SET notes.note = 'GDPR', notes.gdpr_cleansed_datetime=NOW(), notes.gdpr_cleansed_by_report_id=" . ((int)$report_id) . "
	WHERE notes.gdpr_cleansed_by_report_id IS NULL AND contacts.id = " . ((int)$contact_id)
        )->execute();



        // cleanse user
        $linked_user_id = DB::select('linked_user_id')
            ->from(Model_Contacts3::CONTACTS_TABLE)
            ->where('id', '=', $contact_id)
            ->execute()
            ->get('linked_user_id');

        DB::update(Model_Users::MAIN_TABLE)
            ->set(
                array(
                    'email' => $linked_user_id . '@gdpr.cc',
                    'name' => '',
                    'surname' => '',
                    'company' => '',
                    'address' => '',
                    'address_2' => '',
                    'address_3' => '',
                    'country' => '',
                    'county' => '',
                    'phone' => '',
                    'mobile' => '',
                    'avatar' => '',
                    'use_gravatar' => 0,
                    'can_login' => 0
                )

            )->where('id', '=', $linked_user_id)
            ->execute();

        DB::delete('plugin_contacts3_contact_has_preferences')->where('contact_id', '=', $contact_id)->execute();
        //DB::delete('plugin_contacts3_contact_has_subject_preferences')->where('contact_id', '=', $contact_id)->execute();
        //DB::delete('plugin_contacts3_contact_has_course_subject_preferences')->where('contact_id', '=', $contact_id)->execute();
        //DB::delete('plugin_contacts3_contact_has_course_type_preferences')->where('contact_id', '=', $contact_id)->execute();


        /*
        DB::query(
            null,
            "update
              plugin_survey_result survey_results
                inner join plugin_survey_answer_result survey_answers on survey_results.id = survey_answers.survey_result_id
                inner join plugin_survey_questions questions on survey_answers.question_id = questions.id
                inner join engine_users users on survey_results.survey_author = users.id
                left join plugin_contacts3_contacts contacts on users.id = contacts.linked_user_id
            set survey_answers.textbox_value = 'GDPR'
            where questions.title in ('Name', 'Email', 'Phone', 'Mobile') and from_unixtime(survey_results.starttime) < date_sub('2020-07-01', interval 1 month)"
        )->execute();*/

        $files = Model_Files::getDirectoryTree('/contacts/' . $contact_id);
        foreach ($files as $file) {
            @Model_Files::unlink_file($file['id']);
        }

        $bookings = DB::select('bookings.*')
            ->from(array(Model_KES_Bookings::BOOKING_TABLE, 'bookings'))
                ->join(array(Model_KES_Bookings::DELEGATES_TABLE, 'delegates'), 'left')
                    ->on('bookings.booking_id', '=', 'delegates.booking_id')
            ->where('bookings.contact_id', '=', $contact_id)
            ->or_where('delegates.contact_id', '=', $contact_id)
            ->execute()
            ->as_array();
        foreach ($bookings as $booking) {
            $files = Model_Files::getDirectoryTree('/bookings/' . $booking['booking_id']);
            foreach ($files as $file) {
                @Model_Files::unlink_file($file['id']);
            }

            if ($booking['extra_data'] != '') {
                $booking['extra_data'] = @json_decode($booking['extra_data'], true);
                if ($booking['extra_data']) {
                    $booking['extra_data']['special_requirements'] = 'GDPR';
                    $booking['extra_data'] = json_encode($booking['extra_data']);
                    DB::update(Model_KES_Bookings::BOOKING_TABLE)
                        ->set($booking)
                        ->where('booking_id', '=', $booking['booking_id'])
                        ->execute();
                }
            }
        }
    }

    public static function cleanse_survey($survey_answer_result_ids)
    {
        DB::update('plugin_survey_answer_result')
            ->set(array('textbox_value' => 'GDPR'))
            ->where('id', 'in', $survey_answer_result_ids)
            ->execute();
    }

    public static function cleanse_waitlist($waitlist_ids)
    {
        DB::update('plugin_courses_waitlist')
            ->set(
                array(
                    'email' => '',
                    'name' => 'GDPR',
                    'surname' => '',
                    'phone' => '',
                    'address' => '',
                    'message' => ''
                )
            )
            ->where('id', 'in', $waitlist_ids)
            ->execute();
    }

    public static function cleanse_application($application_id, $report_id)
    {
        $application = DB::select('*')
            ->from(Model_KES_Bookings::BOOKING_APPLICATIONS)
            ->where('id', '=', $application_id)
            ->execute()
            ->current();
        if ($application) {
            $application['data'] = @json_decode($application['data'], true);
            if ($application['data']) {
                $application['data'] = array(
                    'schedule_name' => $application['data']['schedule_name'],
                    'has_course_id' => $application['data']['has_course_id'],
                    'has_schedule_id' => $application['data']['has_schedule_id'],
                    'schedule_id' => $application['data']['schedule_id']
                );
                $application['data'] = json_encode($application['data']);
                $application['gdpr_cleansed_datetime'] = date::now();
                $application['gdpr_cleansed_by_report_id'] = $report_id;
                DB::update(Model_KES_Bookings::BOOKING_APPLICATIONS)
                    ->set($application)
                    ->where('id', '=', $application_id)
                    ->execute();
            }
        }
    }

    public static function cleanse($date = null, $report_name = null)
    {
        if ($date == null) {
            $date = date::today();
        }
        $reports = self::get_reports();
        foreach ($reports as $report) {
            try {
                $r = new Model_Reports($report['id']);
                $r->get(true);
                if ($report_name != null && $report_name != $r->get_name()) {
                    continue;
                }
                $r->set_parameters(
                    json_encode(
                        array(
                            array(
                                'parameter_id_',
                                'date',
                                'date',
                                $date,
                                0
                            )
                        )
                    )
                );
                $r->set_parameters($r->prepare_parameters());
                $data = $r->execute_sql();
                $survey_result_answer_ids = array();
                $waitlist_ids = array();
                foreach ($data as $row) {
                    if (isset($row['Application ID'])) {
                        self::cleanse_application($row['Application ID'], $report['id']);
                    } else {
                        if (isset($row['Contact ID'])) {
                            $contact = DB::select('contacts.*', array('types.name', 'ctype'))
                                ->from(array(Model_Contacts3::CONTACTS_TABLE, 'contacts'))
                                    ->join(array(Model_Contacts3::CONTACTS_TYPE_TABLE, 'types'), 'left')
                                        ->on('contacts.type', '=', 'types.contact_type_id')
                                ->where('id', '=', $row['Contact ID'])
                                ->execute()
                                ->current();
                            if ($contact['gdpr_cleansed_datetime'] == null) {
                                if ($contact['ctype'] == 'organisation') {
                                    self::cleanse_organisation($row['Contact ID'], $report['id']);
                                } else {
                                    self::cleanse_contact($row['Contact ID'], $report['id']);
                                }
                            }
                        }
                    }
                    if (isset($row['Survey Result Answer ID'])) {
                        //self::cleanse_survey($row['Survey ID'], $report['id']);
                        $survey_result_answer_ids[] = $row['Survey Result Answer ID'];
                    }
                    if (isset($row['Waitlist ID'])) {
                        $waitlist_ids[] = $row['Waitlist ID'];
                    }
                }
                if (count($survey_result_answer_ids) > 0) {
                    self::cleanse_survey($survey_result_answer_ids);
                }
                if (count($waitlist_ids) > 0) {
                    self::cleanse_waitlist($waitlist_ids);
                }
            } catch (Exception $exc) {
                Model_Errorlog::save($exc);
            }
        }
    }
}