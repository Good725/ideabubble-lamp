<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Model_DCS extends Model
{
    const SYNC_TABLE = 'plugin_dcs_sync';

    public $key;
    public $password;
    public $security;
    public $username;
    public $vendor;

    protected $base_url = 'https://training.vecnet.ie/musicapi';
    protected $tables = array(
        'parent' => 'plugin_contacts_contact',
        'student' => 'plugin_contacts_contact',
        'course' => 'plugin_courses_courses',
        'schedule' => 'plugin_courses_schedules',
        'family' => 'plugin_family_families',
        'registration' => 'plugin_courses_schedules_has_students'

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
                    ->on('sync.cms_id', '=', 'cms_table.id')
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
                    //var_dump($remote_family);exit;
                    continue;
                }

                $exists = $this->get_object_synced('parent', $remote_family->F_FAMILYA . '-' . $remote_family->F1_FNAME, 'remote');
                if (!$exists && $remote_family->F_EMERGNO != '') {
                    //check earlier csv imported students
                    $exists_csv_imported = Model_Contacts::search(array(
                        'notes' => 'Emergency:' . $remote_family->F_EMERGNO,
                        'first_name' => $remote_family->F1_FNAME,
                        'last_name' =>  $remote_family->F1_LNAME
                    ));
                    if (count($exists_csv_imported) == 1) {
                        $exists = array('cms_id' => $exists_csv_imported[0]['id']);
                    }
                }

                $family_cms_id = null;
                if ($exists) {
                    $p1_contact = new Model_Contacts($exists['cms_id']);
                } else {
                    if ($remote_family->F1_EMAIL != '') {
                        $exists = Model_Contacts::search(
                            array(
                                'first_name' => $remote_family->F1_FNAME,
                                'last_name' => $remote_family->F1_LNAME,
                                'email' => $remote_family->F1_EMAIL
                            )
                        );
                        if (count($exists) > 0) {
                            $p1_contact = new Model_Contacts($exists[0]['id']);
                            $family_cms = Model_Families::get_family_of($exists[0]['id']);
                            $family_cms_id = $family_cms['id'];
                        } else {
                            $p1_contact = new Model_Contacts();
                        }
                    } else {
                        $p1_contact = new Model_Contacts();
                    }
                }

                if ($family_cms_id == null) {
                    $family_exists = $this->get_object_synced('family', $remote_family->F_FAMILYA, 'remote');
                    if ($family_exists) {
                        $family_cms_id = $family_exists['cms_id'];
                    } else {
                        $family_cms_id = Model_Families::set_family(null, $remote_family->F_FAMILY, 1, 0);
                    }
                }
                $this->save_object_synced('family', $remote_family->F_FAMILYA, $family_cms_id);


                $p1_contact->set_first_name($remote_family->F1_FNAME);
                $p1_contact->set_last_name($remote_family->F1_LNAME);
                $p1_contact->set_address1($remote_family->F1_ADDR1);
                $p1_contact->set_address2($remote_family->F1_ADDR2);
                $p1_contact->set_address3($remote_family->F1_ADDR3);
                $p1_contact->set_address4($remote_family->F1_ADDR4);
                $p1_contact->set_country_id(1);
                $p1_contact->set_mailing_list('Parent/Guardian');
                if ($remote_family->F_EMERGNO != '') {
                    $p1_contact->set_notes('Emergency:' . $remote_family->F_EMERGNO);
                }
                if ($remote_family->F1_MOBILE != '') {
                    $p1_contact->set_mobile($remote_family->F1_MOBILE);
                }
                if ($remote_family->F1_PHONE != '') {
                    $p1_contact->set_phone($remote_family->F1_PHONE);
                }
                if ($remote_family->F1_EMAIL != '') {
                    $p1_contact->set_email($remote_family->F1_EMAIL);
                } else {
                    $p1_contact->set_email('');
                }
                if ($remote_family->F_ACTIVE == 'Y') {
                    $p1_contact->set_publish(1);
                } else {
                    $p1_contact->set_publish(0);
                }
                $p1_contact->save();
                $p1_cms_id = $p1_contact->get_id();
                Model_Families_Members::add_family_member($family_cms_id, $p1_cms_id, 'Parent');
                Model_Families::set_family($family_cms_id, $remote_family->F_FAMILY, 1, 0, $p1_cms_id);

                $this->save_object_synced('parent', $remote_family->F_FAMILYA . '-' . $remote_family->F1_FNAME, $p1_cms_id);

                if ($remote_family->F2_FNAME != null) {
                    $exists = $this->get_object_synced('parent', $remote_family->F_FAMILYA . '-' . $remote_family->F2_FNAME, 'remote');
                    if (!$exists && $remote_family->F_EMERGNO != '') {
                        //check earlier csv imported students
                        $exists_csv_imported = Model_Contacts::search(array(
                            'notes' => 'Emergency:' . $remote_family->F_EMERGNO,
                            'first_name' => $remote_family->F2_FNAME,
                            'last_name' =>  $remote_family->F2_LNAME
                        ));
                        if (count($exists_csv_imported) == 1) {
                            $exists = array('cms_id' => $exists_csv_imported[0]['id']);
                        }
                    }

                    if ($exists) {
                        $p2_contact = new Model_Contacts($exists['cms_id']);
                    } else {
                        if ($remote_family->F2_EMAIL != '') {
                            $exists = Model_Contacts::search(
                                array(
                                    'first_name' => $remote_family->F2_FNAME,
                                    'last_name' => $remote_family->F2_LNAME,
                                    'email' => $remote_family->F2_EMAIL
                                )
                            );
                            if (count($exists) > 0) {
                                $p2_contact = new Model_Contacts($exists[0]['id']);
                            } else {
                                $p2_contact = new Model_Contacts();
                            }
                        } else {
                            $p2_contact = new Model_Contacts();
                        }

                    }

                    $p2_contact->set_first_name($remote_family->F2_FNAME);
                    $p2_contact->set_last_name($remote_family->F2_LNAME);
                    $p2_contact->set_address1($remote_family->F2_ADDR1);
                    $p2_contact->set_address2($remote_family->F2_ADDR2);
                    $p2_contact->set_address3($remote_family->F2_ADDR3);
                    $p2_contact->set_address4($remote_family->F2_ADDR4);
                    $p2_contact->set_country_id(1);
                    $p2_contact->set_mailing_list('Parent/Guardian');
                    if ($remote_family->F_EMERGNO != '') {
                        $p2_contact->set_notes('Emergency:' . $remote_family->F_EMERGNO);
                    }
                    if ($remote_family->F2_MOBILE != '') {
                        $p2_contact->set_mobile($remote_family->F2_MOBILE);
                    }
                    if ($remote_family->F2_PHONE != '') {
                        $p2_contact->set_phone($remote_family->F2_PHONE);
                    }
                    if ($remote_family->F2_EMAIL != '') {
                        $p2_contact->set_email($remote_family->F2_EMAIL);
                    } else {
                        $p2_contact->set_email('');
                    }
                    if ($remote_family->F_ACTIVE == 'Y') {
                        $p2_contact->set_publish(1);
                    } else {
                        $p2_contact->set_publish(0);
                    }
                    $p2_contact->save();
                    $p2_cms_id = $p2_contact->get_id();
                    Model_Families_Members::add_family_member($family_cms_id, $p2_cms_id, 'Parent');

                    $this->save_object_synced('parent', $remote_family->F_FAMILYA . '-' . $remote_family->F2_FNAME, $p2_cms_id);
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
                if (!$exists) {
                    //check earlier csv imported students
                    $exists_csv_imported = Model_Contacts::search(array('notes' => 'Famstud: ' . $remote_student->F_FAMSTUD));
                    if (count($exists_csv_imported) == 1) {
                        $exists = array('cms_id' => $exists_csv_imported[0]['id']);
                    }
                }
                if ($exists) {
                    $contact = new Model_Contacts($exists['cms_id']);
                } else {
                    if ($remote_student->F1_EMAIL != '') {
                        $exists = Model_Contacts::search(
                            array(
                                'first_name' => $remote_student->F1_FNAME,
                                'last_name' => $remote_student->F1_LNAME,
                                'email' => $remote_student->F1_EMAIL
                            )
                        );
                        if (count($exists) > 0) {
                            $contact = new Model_Contacts($exists[0]['id']);
                        } else {
                            $contact = new Model_Contacts();
                        }
                    } else {
                        $contact = new Model_Contacts();
                    }

                }
                $contact->set_first_name($remote_student->F1_FNAME);
                $contact->set_last_name($remote_student->F1_LNAME);
                $contact->set_address1($remote_student->F1_ADDR1);
                $contact->set_address2($remote_student->F1_ADDR2);
                $contact->set_address3($remote_student->F1_ADDR3);
                $contact->set_address4($remote_student->F1_ADDR4);
                $contact->set_country_id(1);
                $contact->set_mailing_list('Student');
                if ($remote_student->F1_MOBILE != '') {
                    $contact->set_mobile($remote_student->F1_MOBILE);
                }
                if ($remote_student->F1_PHONE != '') {
                    $contact->set_phone($remote_student->F1_PHONE);
                }
                if ($remote_student->F1_EMAIL != '') {
                    $contact->set_email($remote_student->F1_EMAIL);
                } else {
                    $contact->set_email('');
                }
                if ($remote_student->F_ACTIVE == 'Y') {
                    $contact->set_publish(1);
                } else {
                    $contact->set_publish(0);
                }
                $contact->save();
                $cms_id = $contact->get_id();
                $family = $this->get_object_synced('family', $remote_student->F_FAMILYA, 'remote');
                Model_Families_Members::add_family_member($family['cms_id'], $cms_id, 'Student');
                $this->save_object_synced('student', $remote_student->F_FAMSTUD, $cms_id);
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
                if ($exists) {
                    $schedule_id = $exists['cms_id'];
                } else {
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
                }
                $this->save_object_synced('schedule', $course->F_CRSKEY, $schedule_id);
            }

            Database::instance()->commit();
        } catch (Exception $exc) {
            Database::instance()->rollback();
            throw $exc;
        }
    }

    public function import_applications()
    {
        try {
            Database::instance()->begin();
            $applications = $this->get('applications');
            //file_put_contents('/tmp/applications.txt', serialize($applications));
            //$applications = unserialize(file_get_contents('/tmp/applications.txt'));

            foreach ($applications->payload as $application) {
                $registered = $this->get_object_synced('registration', $application->DOCUMENT_KEY, 'remote');
                if (!$registered) {
                    if ($application->F_FSTNAME && $application->F_LSTNAME && $application->F1_FNAME) {
                        $child = $this->get_student_by_names($application->F_FSTNAME, $application->F_LSTNAME, $application->F1_FNAME);

                        if ($child) {
                            $schedule = $this->get_object_synced('schedule', $application->F_CRSKEY, 'remote');
                            if ($schedule) {
                                $schedule_id = $schedule['cms_id'];
                                $id = Model_SchedulesStudents::save('new', $child['id'], $schedule_id, 'Pending',
                                    'dcs imported: application');
                                $this->save_object_synced('registration', $application->DOCUMENT_KEY, $id);
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

            foreach ($registrations->payload as $registration) {
                $registered = $this->get_object_synced('registration', $registration->DOCUMENT_KEY, 'remote');
                if (!$registered) {
                    $family = $this->get_object_synced('family', $registration->F_FAMILYA, 'remote');
                    if ($family) {
                        $family_id = $family['cms_id'];
                        $child = DB::select('child.*')
                            ->from(array(Model_Contacts::TABLE_CONTACT, 'child'))
                                ->join(array(Model_Families_Members::TABLE, 'fm'), 'inner')
                                ->on('child.id', '=', 'fm.contact_id')
                                ->on('fm.role', '=', DB::expr("'Student'"))
                            ->where('child.first_name', '=', $registration->F_FSTNAME)
                            ->and_where('child.last_name', '=', $registration->F_LSTNAME)
                            ->and_where('fm.family_id', '=', $family_id)
                            ->and_where('child.deleted', '=', 0)
                            ->limit(1)
                            ->execute()
                            ->current();

                        if ($child) {
                            $schedule = $this->get_object_synced('schedule', $registration->F_CRSKEY, 'remote');
                            if ($schedule) {
                                $schedule_id = $schedule['cms_id'];
                                $id = Model_SchedulesStudents::save('new', $child['id'], $schedule_id, 'Registered', 'dcs imported');
                                $this->save_object_synced('registration', $registration->DOCUMENT_KEY, $id);
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
                                $id = Model_SchedulesStudents::save('new', $child['id'], $schedule_id, 'Pending',
                                    'dcs imported: waiting list');
                                $this->save_object_synced('registration', $w->DOCUMENT_KEY, $id);
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
            ->from(array(Model_Contacts::TABLE_CONTACT, 'child'))
            ->join(array(Model_Families_Members::TABLE, 'fm'), 'inner')
            ->on('child.id', '=', 'fm.contact_id')
            ->on('fm.role', '=', DB::expr("'Student'"))
            ->join(array(Model_Families::TABLE, 'f'), 'inner')
            ->on('fm.family_id', '=', 'f.id')
            ->join(array(Model_Families_Members::TABLE, 'fmp'), 'inner')
            ->on('f.id', '=', 'fmp.family_id')
            ->on('fmp.role', '=', DB::expr("'Parent'"))
            ->join(array(Model_Contacts::TABLE_CONTACT, 'parent'), 'inner')
            ->on('fmp.contact_id', '=', 'parent.id')
            ->where('child.first_name', '=', $first_name)
            ->and_where('child.last_name', '=', $last_name)
            ->and_where('parent.first_name', '=', $pfirst_name)
            ->and_where('child.deleted', '=', 0)
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
                 LEFT JOIN	plugin_contacts_contact	ON plugin_dcs_sync.cms_id = plugin_contacts_contact.id AND plugin_dcs_sync.type IN ('parent', 'student')
                   WHERE plugin_dcs_sync.type IN ('parent', 'student') AND plugin_contacts_contact.id IS NULL"
        )->execute();

        DB::query(
            Database::DELETE,
            "DELETE plugin_dcs_sync
               FROM plugin_dcs_sync
                 LEFT JOIN	plugin_family_families	ON plugin_dcs_sync.cms_id = plugin_family_families.id AND plugin_dcs_sync.type IN ('family')
                   WHERE plugin_dcs_sync.type IN ('family') AND plugin_family_families.id IS NULL"
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
                 LEFT JOIN	plugin_courses_schedules_has_students	ON plugin_dcs_sync.cms_id = plugin_courses_schedules_has_students.id AND plugin_dcs_sync.type IN ('registration')
                   WHERE plugin_dcs_sync.type IN ('registration') AND plugin_courses_schedules_has_students.id IS NULL"
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
}