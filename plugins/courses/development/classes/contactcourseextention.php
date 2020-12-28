<?php defined('SYSPATH') or die('No Direct Script Access.');

class ContactCourseExtention extends  ContactsExtention
{
    const MAIN_TABLE = 'plugin_courses_contacts_table_extended';

    public function required_js()
    {
        return array(
            URL::get_engine_plugin_assets_base('courses') . 'js/bookings_list.js'
        );
    }

    public function getData($contact,  $request = null)
    {
        if (isset($contact['id']))
        {
            $data = DB::select()->from(self::MAIN_TABLE)->where('contact_id', '=', $contact['id'])->execute()->current();
            $data['registrations'] = Model_SchedulesStudents::search(array('contact_id' => $contact['id']));
            $data['bookings']      = Model_CourseBookings::search(array('student_id' => $contact['id']));
        }
        else
        {
            $data['academic_year_id'] = NULL;
            $data['family_role_id']   = NULL;
            $data['flexi_student']    = NULL;
            $data['registrations']    = array();
            $data['bookings']         = array();
        }

        $data['academic_years'] = Model_AcademicYear::get_academic_years_options(TRUE);

        return $data;
    }

    public function saveData($contact_id, $post)
    {
        $data = array();

        if (isset($post['academic_year_id'])) $data['academic_year_id'] = $post['academic_year_id'];
        if (isset($post['family_role_id']))   $data['family_role_id']   = $post['family_role_id'];
        if (isset($post['flexi_student']))    $data['flexi_student']    = $post['flexi_student'];

        if (count($data)) // Don't need to continue, if there is no data
        {
            // Check if a record already exists in the table
            $exists = DB::select()
                ->from(self::MAIN_TABLE)
                ->where('contact_id', '=', $contact_id)
                ->execute()
                ->as_array();

            // If the record exists, update it. Otherwise, add a new record
            if (count($exists) > 0)
            {
                DB::update(self::MAIN_TABLE)->set($data)->execute();
            }
            else
            {
                $data['contact_id'] = $contact_id;
                DB::insert(self::MAIN_TABLE, array_keys($data))->values($data)->execute();
            }
        }
    }

    public function getTabs($contact_details)
    {
        return array(
            //array('name' => 'courses', 'title' => 'Courses', 'view' => 'course_list_contact_extention')
            array('name' => 'coursebookings', 'title' => 'Bookings', 'view' => 'coursebooking_list_contact_extention')
        );
    }

    public function getFieldsets($contact_details)
    {
        return array(
            array('name' => 'education', 'title' => 'Education', 'view' => 'contact_education_fields', 'position' => 'personal')
        );
    }
}