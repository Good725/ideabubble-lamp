<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Model_DCS2 extends Model
{
    const SYNC_TABLE = 'plugin_dcs_sync';

    public $key;
    public $password;
    public $security;
    public $username;
    public $vendor;

    protected $base_url = 'https://training.vecnet.ie/musicapi';
    protected $tables = array(
        'parent' => 'plugin_contacts3_contacts',
        'student' => 'plugin_contacts3_contacts',
        'course' => 'plugin_courses_courses',
        'schedule' => 'plugin_courses_schedules',
        'family' => 'plugin_contacts3_family',
        'registration' => 'plugin_ib_educate_bookings'

    );
    protected $table_ids = array(
        'parent' => 'id',
        'student' => 'id',
        'course' => 'id',
        'schedule' => 'id',
        'family' => 'family_id',
        'registration' => 'booking_id'

    );

    public function build_auth_params()
    {
        $params = array(
            'apikey' => $this->key,
            'PASSWORD' => $this->password,
            'security' => $this->security,
            'USERNAME' => $this->username,
            'vendor' => $this->vendor
        );
        return $params;
    }

    public function get($action)
    {
        $url = $this->base_url . '/' . $action . '?' . http_build_query($this->build_auth_params());

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
        //curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        if (!defined('CURL_SSLVERSION_TLSv1_2')) {
            define('CURL_SSLVERSION_TLSv1_2', 6);
        }
        //curl_setopt($ch, CURLOPT_SSLVERSION, CURL_SSLVERSION_TLSv1_2);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, array('Connection: Close'));
        curl_setopt($ch, CURLOPT_URL, $url);

        $response = curl_exec($ch);
        //echo $url;
        $inf = curl_getinfo($ch);
        //print_r($inf);
        //echo curl_error($ch);
        curl_close($ch);

        return json_decode($response);
    }

    public function get_object_synced($type, $id, $id_type = 'cms')
    {
        $selectq = DB::select('sync.*')
            ->from(array(self::SYNC_TABLE, 'sync'))
                ->join(array($this->tables[$type], 'cms_table'), 'inner')
                    ->on('sync.cms_id', '=', 'cms_table' . '.' . $this->table_ids[$type])
            ->and_where('sync.type', '=', $type);
        if ($id_type == 'remote') {
            $selectq->and_where('sync.remote_id', '=', $id);
        } else {
            $selectq->and_where('sync.cms_id', '=', $id);
        }
        $selectq->order_by('sync.id', 'desc');
        $exists = $selectq->execute()->current();
        return $exists;
    }

    public function save_object_synced($type, $remote_id, $cms_id)
    {
        $exists = DB::select('*')
            ->from(self::SYNC_TABLE)
            ->where('remote_id', '=', $remote_id)
            ->and_where('cms_id', '=', $cms_id)
            ->and_where('type', '=', $type)
            ->execute()
            ->current();
        if ($exists) {
            DB::update(self::SYNC_TABLE)
                ->set(array('synced' => date('Y-m-d H:i:s')))
                ->where('id', '=', $exists['id'])
                ->execute();
            $id = $exists['id'];
        } else {
            $inserted = DB::insert(self::SYNC_TABLE)
                ->values(array(
                    'synced' => date('Y-m-d H:i:s'),
                    'remote_id' => $remote_id,
                    'cms_id' => $cms_id,
                    'type' => $type
                ))
                ->execute();
            $id = $inserted[0];
        }

        return $id;
    }

    public function import_families()
    {
        try {
            Database::instance()->begin();
            $families = $this->get('families');
            //file_put_contents('/tmp/families.txt', serialize($families));
            //$families = unserialize(file_get_contents('/tmp/families.txt'));
            //print_r($families);
            foreach ($families->payload as $remote_family) {
                if ($remote_family->F_FAMILYA == null) {
                    continue;
                }
                $family_cms_id = null;
                $exists = $this->get_object_synced('parent', $remote_family->F_FAMILYA . '-' . $remote_family->F1_FNAME, 'remote');
                if (!$exists) {
                    if ($remote_family->F1_EMAIL != '') {
                        $exists = Model_Contacts3::search(
                            array(
                                'first_name' => $remote_family->F1_FNAME,
                                'last_name' => $remote_family->F1_LNAME,
                                'email' => $remote_family->F1_EMAIL
                            )
                        );
                        if (count($exists) > 0) {
                            $p1_contact = new Model_Contacts3($exists[0]['id']);
                            $family_cms_id = $p1_contact->get_family_id();
                        } else {
                            $p1_contact = new Model_Contacts3();
                        }
                    } else {
                        $p1_contact = new Model_Contacts3();
                    }
                } else {
                    $p1_contact = new Model_Contacts3($exists['cms_id']);
                    $family_cms_id = $p1_contact->get_family_id();
                }


                if ($family_cms_id == null) {
                    $family_exists = $this->get_object_synced('family', $remote_family->F_FAMILYA, 'remote');
                    if ($family_exists) {
                        $family_cms_id = $family_exists['cms_id'];
                        $family = new Model_Family($family_cms_id);
                    } else {
                        $family = new Model_Family();
                        $family->set_family_name(trim($remote_family->F_FAMILY));
                        $family->save();
                        $family_cms_id = $family->get_id();
                        $this->save_object_synced('family', $remote_family->F_FAMILYA, $family_cms_id);
                        //Database::instance()->commit();var_dump($family);exit;
                    }
                }

                if ($p1_contact->get_id() == null) {
                    $p1_contact->set_type(Model_Contacts3::find_type('Family')['contact_type_id']);
                    $p1_contact->set_subtype_id(0);
                    $p1_contact->add_role(1);
                    $p1_contact->set_is_primary(1);
                    $p1_contact->set_first_name($remote_family->F1_FNAME);
                    $p1_contact->set_last_name($remote_family->F1_LNAME);
                    /*$p1_contact->set_address1($remote_family->F1_ADDR1);
                    $p1_contact->set_address2($remote_family->F1_ADDR2);
                    $p1_contact->set_address3($remote_family->F1_ADDR3);
                    $p1_contact->set_address4($remote_family->F1_ADDR4);
                    $p1_contact->set_country_id(1);
                    $p1_contact->set_mailing_list('Parent/Guardian');*/

                    if ($remote_family->F_ACTIVE == 'Y') {
                        $p1_contact->set_publish(1);
                    } else {
                        $p1_contact->set_publish(0);
                    }
                    $p1_contact->set_family_id($family_cms_id);
                    $p1_contact->save(false);
                    $p1_cms_id = $p1_contact->get_id();
                    if ($remote_family->F_EMERGNO != '') {
                        $notes = array('note' => 'Emergency:' . $remote_family->F_EMERGNO, 'link_id' => $p1_cms_id, 'table_link_id' => 1);
                        DB::insert('plugin_contacts3_notes',array_keys($notes))->values($notes)->execute();
                    }
                    if ($remote_family->F1_MOBILE != '') {
                        $p1_contact->insert_notification(
                            array(
                                'contact_id' => 0,
                                'notification_id' => 2,
                                'value' => $remote_family->F1_MOBILE
                            )
                        );
                    }
                    if ($remote_family->F1_PHONE != '') {
                        $p1_contact->insert_notification(
                            array(
                                'contact_id' => 0,
                                'notification_id' => 3,
                                'value' => $remote_family->F1_PHONE
                            )
                        );
                    }
                    if ($remote_family->F1_EMAIL != '') {
                        $p1_contact->insert_notification(
                            array(
                                'contact_id' => 0,
                                'notification_id' => 1,
                                'value' => $remote_family->F1_EMAIL
                            )
                        );
                    }
                    $this->save_object_synced('parent', $remote_family->F_FAMILYA . '-' . $remote_family->F1_FNAME, $p1_cms_id);
                }

                if ($remote_family->F2_FNAME != null) {
                    $exists = $this->get_object_synced('parent', $remote_family->F_FAMILYA . '-' . $remote_family->F2_FNAME, 'remote');
                    if (!$exists) {
                        if ($remote_family->F2_EMAIL != '') {
                            $exists = Model_Contacts3::search(
                                array(
                                    'first_name' => $remote_family->F2_FNAME,
                                    'last_name' => $remote_family->F2_LNAME,
                                    'email' => $remote_family->F2_EMAIL
                                )
                            );
                            if (count($exists) > 0) {
                                $p2_contact = new Model_Contacts3($exists[0]['id']);
                            } else {
                                $p2_contact = new Model_Contacts3();
                            }
                        } else {
                            $p2_contact = new Model_Contacts3();
                        }

                        if ($p2_contact->get_id() == null) {
                            $p2_contact->set_type(Model_Contacts3::find_type('Family')['contact_type_id']);
                            $p2_contact->set_subtype_id(0);
                            $p2_contact->add_role(1);
                            $p2_contact->set_first_name($remote_family->F2_FNAME);
                            $p2_contact->set_last_name($remote_family->F2_LNAME);
                            /*$p2_contact->set_address1($remote_family->F2_ADDR1);
                            $p2_contact->set_address2($remote_family->F2_ADDR2);
                            $p2_contact->set_address3($remote_family->F2_ADDR3);
                            $p2_contact->set_address4($remote_family->F2_ADDR4);
                            $p2_contact->set_country_id(1);
                            $p2_contact->set_mailing_list('Parent/Guardian');*/
                            if ($remote_family->F_ACTIVE == 'Y') {
                                $p2_contact->set_publish(1);
                            } else {
                                $p2_contact->set_publish(0);
                            }
                            $p2_contact->set_family_id($family_cms_id);
                            $p2_contact->save(false);
                            $p2_cms_id = $p2_contact->get_id();
                            if ($remote_family->F_EMERGNO != '') {
                                $notes = array('note' => 'Emergency:' . $remote_family->F_EMERGNO, 'link_id' => $p2_cms_id, 'table_link_id' => 1);
                                DB::insert('plugin_contacts3_notes',array_keys($notes))->values($notes)->execute();
                            }
                            if ($remote_family->F2_MOBILE != '') {
                                $p2_contact->insert_notification(
                                    array(
                                        'contact_id' => 0,
                                        'notification_id' => 2,
                                        'value' => $remote_family->F2_MOBILE
                                    )
                                );
                            }
                            if ($remote_family->F2_PHONE != '') {
                                $p2_contact->insert_notification(
                                    array(
                                        'contact_id' => 0,
                                        'notification_id' => 3,
                                        'value' => $remote_family->F2_PHONE
                                    )
                                );
                            }
                            if ($remote_family->F2_EMAIL != '') {
                                $p2_contact->insert_notification(
                                    array(
                                        'contact_id' => 0,
                                        'notification_id' => 1,
                                        'value' => $remote_family->F2_EMAIL
                                    )
                                );
                            }
                            $this->save_object_synced('parent', $remote_family->F_FAMILYA . '-' . $remote_family->F2_FNAME, $p2_cms_id);
                        }
                    }
                }
            }
            //print_r($families);
            Database::instance()->commit();
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }
    }

    public function import_students()
    {
        try {
            Database::instance()->begin();
            $students = $this->get('students');
            //file_put_contents('/tmp/students.txt', serialize($students));
            //$students = unserialize(file_get_contents('/tmp/students.txt'));

            foreach ($students->payload as $remote_student) {

                $exists = $this->get_object_synced('student', $remote_student->F_FAMSTUD, 'remote');
                if ($exists) {
                    continue;
                }
                if ($remote_student->F1_EMAIL != '') {
                    $exists = Model_Contacts3::search(
                        array(
                            'first_name' => $remote_student->F1_FNAME,
                            'last_name' => $remote_student->F1_LNAME,
                            'email' => $remote_student->F1_EMAIL
                        )
                    );
                    if (count($exists) > 0) {
                        $contact = new Model_Contacts3($exists[0]['id']);
                    } else {
                        $contact = new Model_Contacts3();
                    }
                } else {
                    $contact = new Model_Contacts3();
                }

                if ($contact->get_id() == null) {
                    $contact->set_type(Model_Contacts3::find_type('Family')['contact_type_id']);
                    $contact->set_subtype_id(0);
                    $contact->add_role(2);
                    $contact->set_first_name($remote_student->F1_FNAME);
                    $contact->set_last_name($remote_student->F1_LNAME);
                    /*$contact->set_address1($remote_student->F1_ADDR1);
                    $contact->set_address2($remote_student->F1_ADDR2);
                    $contact->set_address3($remote_student->F1_ADDR3);
                    $contact->set_address4($remote_student->F1_ADDR4);
                    $contact->set_country_id(1);
                    $contact->set_mailing_list('Student');*/
                    if ($remote_student->F_ACTIVE == 'Y') {
                        $contact->set_publish(1);
                    } else {
                        $contact->set_publish(0);
                    }
                    $family = $this->get_object_synced('family', $remote_student->F_FAMILYA, 'remote');
                    $contact->set_family_id($family['cms_id']);
                    $contact->save(false);
                    if ($remote_student->F1_MOBILE != '') {
                        $contact->insert_notification(
                            array(
                                'contact_id' => 0,
                                'notification_id' => 2,
                                'value' => $remote_student->F1_MOBILE
                            )
                        );
                    }
                    if ($remote_student->F1_PHONE != '') {
                        $contact->insert_notification(
                            array(
                                'contact_id' => 0,
                                'notification_id' => 3,
                                'value' => $remote_student->F1_PHONE
                            )
                        );
                    }
                    if ($remote_student->F1_EMAIL != '') {
                        $contact->insert_notification(
                            array(
                                'contact_id' => 0,
                                'notification_id' => 1,
                                'value' => $remote_student->F1_EMAIL
                            )
                        );
                    }
                    $cms_id = $contact->get_id();
                    $this->save_object_synced('student', $remote_student->F_FAMSTUD, $cms_id);
                }
            }
            //print_r($students);
            Database::instance()->commit();
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }
    }

    public function import_locations()
    {
        try {
            Database::instance()->begin();
            /*$locations = $this->get('locations');
            file_put_contents('/tmp/locations.txt', serialize($locations));
            $locations = unserialize(file_get_contents('/tmp/locations.txt'));

            foreach ($locations->payload as $location) {
                $exists = $this->get_object_synced('location', $location->F_LOCATIO, 'remote');
                if ($exists) {
                    $cms_id = $exists['cms_id'];
                } else {

                }
            }

            print_r($locations);*/

            Database::instance()->commit();
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }
    }

    public function import_courses()
    {
        try {
            Database::instance()->begin();
            $courses = $this->get('courses');
            //file_put_contents('/tmp/courses.txt', serialize($courses));
            //$courses = unserialize(file_get_contents('/tmp/courses.txt'));

            foreach ($courses->payload as $course) {
                $exists = $this->get_object_synced('course', $course->F_LCLCODE, 'remote');
                if ($exists) {
                    $course_id = $exists['cms_id'];
                } else {
                    $course_inserted = DB::insert(Model_Courses::TABLE_COURSES)
                        ->values(
                            array(
                                'title' => $course->F_COURSE,
                                'date_created' => date::now(),
                                'publish' => 0,
                                'deleted' => 0,
                                'book_button' => 0,
                                'description_button' => 0
                            )
                        )->execute();
                    $course_id = $course_inserted[0];
                }
                $this->save_object_synced('course', $course->F_LCLCODE, $course_id);

                $exists = $this->get_object_synced('schedule', $course->F_CRSKEY, 'remote');
                if (!$exists) {
                    $teacher = DB::select('*')
                        ->from(Model_Contacts::TABLE_CONTACT)
                        ->where(DB::expr("REPLACE(REPLACE(CONCAT_WS('', last_name, first_name), '\\'', ''), ' ', '')"), "=", DB::expr("REPLACE('" . $course->F_TEACHER . "', ' ', '')"))
                        ->execute()
                        ->current();
                    if (count(explode('.', $course->F_STRTIME)) == 2) {

                    } else {

                    }
                    $start_time = explode('.', $course->F_STRTIME);
                    $start_time[0] = str_pad($start_time[0], 2, '0', STR_PAD_LEFT);
                    $start_time[1] = str_pad(@$start_time[1], 2, '0', STR_PAD_RIGHT);
                    $start_time[2] = '00';
                    $start_date =
                        date('Y-m-d', strtotime($course->F_STRDATE)) .
                        ' ' .
                        implode(':', $start_time);

                    $schedule_inserted = DB::insert(Model_Schedules::TABLE_SCHEDULES)
                        ->values(
                            array(
                                'name' => $course->F_CRSKEY,
                                'course_id' => $course_id,
                                'date_created' => date::now(),
                                'publish' => 0,
                                'delete' => 0,
                                'trainer_id' => $teacher ? $teacher['id'] : null,
                                'fee_per' => 'Schedule',
                                'booking_type' => 'Whole Schedule',
                                'start_date' => $start_date,
                                //'repeat' => 6 // custom
                            )
                        )->execute();
                    $schedule_id = $schedule_inserted[0];
                    $this->save_object_synced('schedule', $course->F_CRSKEY, $schedule_id);
                }
            }

            Database::instance()->commit();
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }
    }

    public static function create_booking($student_id, $course_id, $schedule_id, $instrument, $status = 1, $booking_id = null)
    {
        $booking = array(
            'contact_id' => $student_id,
            'booking_status' => $status,
            'created_date' => date::now(),
            'modified_date' => date::now(),
            'publish' => 1,
            'delete' => 0,
            'extra_data' => json_encode(array('subject_id' => $instrument))
        );
        if ($booking_id) {
            $booking['booking_id'] = $booking_id;
        }
        $inserted = DB::insert(Model_KES_Bookings::BOOKING_TABLE)
            ->values(
                $booking
            )
            ->execute();
        $booking_id = $inserted[0];
        if ($booking_id) {
            DB::insert(Model_KES_Bookings::BOOKING_COURSES)
                ->values(
                    array(
                        'course_id' => $course_id,
                        'booking_id' => $booking_id,
                        'booking_status' => $status
                    )
                )
                ->execute();
            DB::insert(Model_KES_Bookings::BOOKING_SCHEDULES)
                ->values(
                    array(
                        'schedule_id' => $schedule_id,
                        'booking_id' => $booking_id,
                        'booking_status' => $status,
                        'publish' => 1
                    )
                )
                ->execute();
            DB::insert(Model_KES_Bookings::BOOKING_APPLICATIONS)
                ->values(
                    array(
                        'booking_id' => $booking_id,
                        'status_id' => $status,
                        'data' => json_encode(array('subject_id' => $instrument)),
                        'application_status' => 1,
                        'delegate_id' => $student_id
                    )
                )
                ->execute();
        }
        return $booking_id;
    }

    public function import_applications()
    {
        try {
            Database::instance()->begin();
            $applications = $this->get('applications');
            //file_put_contents('/tmp/applications.txt', serialize($applications));
            //$applications = unserialize(file_get_contents('/tmp/applications.txt'));

            $esubjects = DB::select('*')
                ->from(Model_Subjects::TABLE_SUBJECTS)
                ->where('deleted', '=', 0)
                ->execute()
                ->as_array();
            $subjects = array();
            foreach ($esubjects as $esubject) {
                $subjects[$esubject['name']] = $esubject['id'];
            }

            foreach ($applications->payload as $application) {
                $registered = $this->get_object_synced('registration', $application->DOCUMENT_KEY, 'remote');
                if (!$registered) {
                    if ($application->F_FSTNAME && $application->F_LSTNAME && $application->F1_FNAME) {
                        $child = $this->get_student_by_names($application->F_FSTNAME, $application->F_LSTNAME, $application->F1_FNAME);

                        if ($child) {
                            $schedule = $this->get_object_synced('schedule', $application->F_CRSKEY, 'remote');
                            if ($schedule) {
                                $schedule_id = $schedule['cms_id'];
                                $schedule = Model_Schedules::get_schedule($schedule_id);
                                $course_id = $schedule['course_id'];
                                $instrument = null;
                                if ($application->F_INSTRUMA != '') {
                                    if (!isset($subjects[$application->F_INSTRUMA])) {
                                        $subject_inserted = DB::insert(Model_Subjects::TABLE_SUBJECTS)
                                            ->values(
                                                array(
                                                    'name' => $application->F_INSTRUMA,
                                                    'deleted' => 0
                                                )
                                            )
                                            ->execute();
                                        $subjects[$application->F_INSTRUMA] = $subject_inserted[0];
                                    }
                                    $instrument = $subjects[$application->F_INSTRUMA];
                                }
                                $booking_id = self::create_booking($child['id'], $course_id, $schedule_id, $instrument, 1);
                                $this->save_object_synced('registration', $application->DOCUMENT_KEY, $booking_id);
                            }
                        } else {
                            //echo $childq . "\n";
                        }
                    }
                }
            }

            //print_r($applications);
            Database::instance()->commit();
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }
    }

    public function import_registrations()
    {
        try {
            Database::instance()->begin();
            $registrations = $this->get('registrations');
            //file_put_contents('/tmp/registrations.txt', serialize($registrations));
            //$registrations = unserialize(file_get_contents('/tmp/registrations.txt'));

            $esubjects = DB::select('*')
                ->from(Model_Subjects::TABLE_SUBJECTS)
                ->where('deleted', '=', 0)
                ->execute()
                ->as_array();
            $subjects = array();
            foreach ($esubjects as $esubject) {
                $subjects[$esubject['name']] = $esubject['id'];
            }

            foreach ($registrations->payload as $registration) {
                $registered = $this->get_object_synced('registration', $registration->DOCUMENT_KEY, 'remote');

                if (!$registered) {
                    $family = $this->get_object_synced('family', $registration->F_FAMILYA, 'remote');

                    if ($family) {
                        $family_id = $family['cms_id'];
                        $childs = Model_Contacts3::search(
                            array(
                                'first_name' => $registration->F_FSTNAME,
                                'last_name' => $registration->F_LSTNAME,
                                'family_id' => $family_id
                            )
                        );
                        $child = @$childs[0];
                        if ($child) {
                            $schedule = $this->get_object_synced('schedule', $registration->F_CRSKEY, 'remote');
                            if (!$schedule) {
                                $exists = $this->get_object_synced('course', $registration->F_LCLCODE, 'remote');
                                if ($exists) {
                                    $course_id = $exists['cms_id'];
                                } else {
                                    $course_inserted = DB::insert(Model_Courses::TABLE_COURSES)
                                        ->values(
                                            array(
                                                'title' => $registration->F_LCLCODE,
                                                'date_created' => date::now(),
                                                'publish' => 0,
                                                'deleted' => 0,
                                                'book_button' => 0,
                                                'description_button' => 0
                                            )
                                        )->execute();
                                    $course_id = $course_inserted[0];
                                }
                                $this->save_object_synced('course', $registration->F_LCLCODE, $course_id);

                                $schedule_inserted = DB::insert(Model_Schedules::TABLE_SCHEDULES)
                                    ->values(
                                        array(
                                            'name' => $registration->F_CRSKEY,
                                            'course_id' => $course_id,
                                            'date_created' => date::now(),
                                            'publish' => 0,
                                            'delete' => 0,
                                            'trainer_id' => null,
                                            'fee_per' => 'Schedule',
                                            'booking_type' => 'Whole Schedule',
                                            'start_date' => null,
                                            //'repeat' => 6 // custom
                                        )
                                    )->execute();
                                $schedule_id = $schedule_inserted[0];
                                $this->save_object_synced('schedule', $registration->F_CRSKEY, $schedule_id);
                                $schedule = $this->get_object_synced('schedule', $registration->F_CRSKEY, 'remote');
                            }

                            if ($schedule) {
                                $schedule_id = $schedule['cms_id'];
                                $schedule = Model_Schedules::get_schedule($schedule_id);
                                $course_id = $schedule['course_id'];
                                $instrument = null;
                                if ($registration->F_INSTRUMA != '') {
                                    if (!isset($subjects[$registration->F_INSTRUMA])) {
                                        $subject_inserted = DB::insert(Model_Subjects::TABLE_SUBJECTS)
                                            ->values(
                                                array(
                                                    'name' => $registration->F_INSTRUMA,
                                                    'deleted' => 0
                                                )
                                            )
                                            ->execute();
                                        $subjects[$registration->F_INSTRUMA] = $subject_inserted[0];
                                    }
                                    $instrument = $subjects[$registration->F_INSTRUMA];
                                }
                                $booking_id = self::create_booking($child['id'], $course_id, $schedule_id, $instrument, 1);
                                $this->save_object_synced('registration', $registration->DOCUMENT_KEY, $booking_id);
                            }
                        }
                    }
                }
            }

            Database::instance()->commit();
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }
    }

    public function import_waitinglist()
    {
        try {
            Database::instance()->begin();
            $waitinglist = $this->get('waitinglist');
            //file_put_contents('/tmp/waitinglist.txt', serialize($waitinglist));
            //$waitinglist = unserialize(file_get_contents('/tmp/waitinglist.txt'));

            $esubjects = DB::select('*')
                ->from(Model_Subjects::TABLE_SUBJECTS)
                ->where('deleted', '=', 0)
                ->execute()
                ->as_array();
            $subjects = array();
            foreach ($esubjects as $esubject) {
                $subjects[$esubject['name']] = $esubject['id'];
            }

            $n = 0;
            $m = 0;
            foreach ($waitinglist->payload as $w) {
                $registered = $this->get_object_synced('registration', $w->DOCUMENT_KEY, 'remote');
                if (!$registered) {
                    if ($w->F_FSTNAME && $w->F_LSTNAME && $w->F1_FNAME) {
                        $child = $this->get_student_by_names($w->F_FSTNAME, $w->F_LSTNAME, $w->F1_FNAME);

                        if ($child) {
                            $schedule = $this->get_object_synced('schedule', $w->F_CRSKEY, 'remote');
                            if ($schedule) {
                                $schedule_id = $schedule['cms_id'];
                                $schedule = Model_Schedules::get_schedule($schedule_id);
                                $course_id = $schedule['course_id'];
                                $instrument = null;

                                if ($w->F_INSTRUMA != '') {
                                    if (!isset($subjects[$w->F_INSTRUMA])) {
                                        $subject_inserted = DB::insert(Model_Subjects::TABLE_SUBJECTS)
                                            ->values(
                                                array(
                                                    'name' => $w->F_INSTRUMA,
                                                    'deleted' => 0
                                                )
                                            )
                                            ->execute();
                                        $subjects[$w->F_INSTRUMA] = $subject_inserted[0];
                                    }
                                    $instrument = $subjects[$w->F_INSTRUMA];
                                }
                                $booking_id = self::create_booking($child['id'], $course_id, $schedule_id, $instrument, 1);
                                $this->save_object_synced('registration', $w->DOCUMENT_KEY, $booking_id);
                                ++$n;
                            }
                        } else {
                            //echo $childq . "\n";
                        }
                    }
                }
                if (trim($w->F_FAMILYA) && $w->F_STUDIDA) {
                    //++$n;
                } else {
                    ++$m;
                }
            }
            Database::instance()->commit();
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }
    }

    public function get_student_by_names($first_name, $last_name, $pfirst_name)
    {
        $childq = DB::select('child.*')
            ->from(array(Model_Contacts3::CONTACTS_TABLE, 'child'))
                ->join(array(Model_Contacts3::FAMILY_TABLE, 'f'), 'inner')
                    ->on('child.family_id', '=', 'f.family_id')
                ->join(array(Model_Contacts3::CONTACTS_TABLE, 'parent'), 'inner')
                    ->on('parent.family_id', '=', 'f.family_id')
            ->where('child.first_name', '=', $first_name)
            ->and_where('child.last_name', '=', $last_name)
            ->and_where('parent.first_name', '=', $pfirst_name)
            ->and_where('child.delete', '=', 0)
            ->limit(1);
        $child = $childq->execute()
            ->current();
        return $child;
    }

    public function clean_sync()
    {
        DB::query(
            Database::DELETE,
            "DELETE plugin_dcs_sync
               FROM plugin_dcs_sync
                 LEFT JOIN	plugin_contacts3_contacts	ON plugin_dcs_sync.cms_id = plugin_contacts3_contacts.id AND plugin_dcs_sync.type IN ('parent', 'student')
                   WHERE plugin_dcs_sync.type IN ('parent', 'student') AND plugin_contacts3_contacts.id IS NULL"
        )->execute();

        DB::query(
            Database::DELETE,
            "DELETE plugin_dcs_sync
               FROM plugin_dcs_sync
                 LEFT JOIN	plugin_contacts3_family	ON plugin_dcs_sync.cms_id = plugin_contacts3_family.family_id AND plugin_dcs_sync.type IN ('family')
                   WHERE plugin_dcs_sync.type IN ('family') AND plugin_contacts3_family.family_id IS NULL"
        )->execute();

        DB::query(
            Database::DELETE,
            "DELETE plugin_dcs_sync
               FROM plugin_dcs_sync
                 LEFT JOIN	plugin_courses_courses	ON plugin_dcs_sync.cms_id = plugin_courses_courses.id AND plugin_dcs_sync.type IN ('course')
                   WHERE plugin_dcs_sync.type IN ('course') AND plugin_courses_courses.id IS NULL"
        )->execute();

        DB::query(
            Database::DELETE,
            "DELETE plugin_dcs_sync
               FROM plugin_dcs_sync
                 LEFT JOIN	plugin_courses_schedules	ON plugin_dcs_sync.cms_id = plugin_courses_schedules.id AND plugin_dcs_sync.type IN ('schedule')
                   WHERE plugin_dcs_sync.type IN ('schedule') AND plugin_courses_schedules.id IS NULL"
        )->execute();

        DB::query(
            Database::DELETE,
            "DELETE plugin_dcs_sync
               FROM plugin_dcs_sync
                 LEFT JOIN	plugin_ib_educate_bookings	ON plugin_dcs_sync.cms_id = plugin_ib_educate_bookings.booking_id AND plugin_dcs_sync.type IN ('registration')
                   WHERE plugin_dcs_sync.type IN ('registration') AND plugin_ib_educate_bookings.booking_id IS NULL"
        )->execute();
    }

    public static function link_existing_dcs_contact_to_user($post, $user_id)
    {
        $dcs_family_id = $post['dcs_family_id'];
        $contact = DB::select('contacts.*')
            ->from(array(Model_Families::TABLE, 'families'))
                ->join(array(self::SYNC_TABLE, 'sync'), 'inner')
                    ->on('families.id', '=', 'sync.cms_id')
                    ->on('sync.type', '=', DB::expr("'family'"))
                ->join(array('plugin_family_members', 'members'), 'inner')
                    ->on('families.id', '=', 'members.family_id')
                ->join(array(Model_Contacts::TABLE_CONTACT, 'contacts'), 'inner')
                    ->on('members.contact_id', '=', 'contacts.id')
                ->join(array(Model_Contacts::TABLE_COMMS, 'emails'), 'left')
                    ->on('contacts.id', '=', 'emails.contact_id')
            ->where('sync.remote_id', '=', $dcs_family_id)
            ->and_where_open()
                ->or_where('contacts.email', '=', $post['email'])
                ->or_where('emails.value', '=', $post['email'])
            ->and_where_close()
            ->execute()
            ->current();

        if ($contact) {
            DB::insert(Model_Contacts::TABLE_PERMISSION_LIMIT)
                ->values(array('user_id' => $user_id, 'contact_id' => $contact['id']))
                ->execute();
            return true;
        }
        return false;
    }

    public static function check_existing_dcs_contact($post)
    {
        $contact = DB::select('contacts.*')
            ->from(array(Model_Families::TABLE, 'families'))
                ->join(array(self::SYNC_TABLE, 'sync'), 'inner')
                    ->on('families.id', '=', 'sync.cms_id')
                    ->on('sync.type', '=', DB::expr("'family'"))
                ->join(array('plugin_family_members', 'members'), 'inner')
                    ->on('families.id', '=', 'members.family_id')
                ->join(array(Model_Contacts::TABLE_CONTACT, 'contacts'), 'inner')
                    ->on('members.contact_id', '=', 'contacts.id')
                ->join(array(Model_Contacts::TABLE_COMMS, 'emails'), 'left')
                    ->on('contacts.id', '=', 'emails.contact_id')
            ->where('sync.remote_id', '=', $post['dcs_family_id'])
            ->and_where_open()
            ->or_where('contacts.email', '=', $post['email'])
            ->or_where('emails.value', '=', $post['email'])
            ->and_where_close()
            ->execute()
            ->current();

        return $contact;
    }

    public function fix_notifs()
    {
        try {
            Database::instance()->begin();
            $families = $this->get('families');

            foreach ($families->payload as $remote_family) {
                if ($remote_family->F_FAMILYA == null) {
                    continue;
                }
                $exists = $this->get_object_synced('parent', $remote_family->F_FAMILYA . '-' . $remote_family->F1_FNAME, 'remote');
                if ($exists) {
                    $p1_contact = new Model_Contacts3($exists['cms_id']);
                    $family_cms_id = $p1_contact->get_family_id();

                    if ($p1_contact->get_notifications_group_id() == null) {
                        if ($remote_family->F1_MOBILE != '') {
                            $p1_contact->insert_notification(
                                array(
                                    'contact_id' => 0,
                                    'notification_id' => 2,
                                    'value' => $remote_family->F1_MOBILE
                                )
                            );
                        }
                        if ($remote_family->F1_PHONE != '') {
                            $p1_contact->insert_notification(
                                array(
                                    'contact_id' => 0,
                                    'notification_id' => 3,
                                    'value' => $remote_family->F1_PHONE
                                )
                            );
                        }
                        if ($remote_family->F1_EMAIL != '') {
                            $p1_contact->insert_notification(
                                array(
                                    'contact_id' => 0,
                                    'notification_id' => 1,
                                    'value' => $remote_family->F1_EMAIL
                                )
                            );
                        }
                    }
                }
            }

            Database::instance()->commit();
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }
    }
}