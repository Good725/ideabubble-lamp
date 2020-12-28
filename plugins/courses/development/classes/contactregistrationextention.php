<?php defined('SYSPATH') or die('No Direct Script Access.');

class ContactRegistrationExtention extends  ContactsExtention
{
    public function required_js()
    {
        return array(
        );
    }

    public function getData($contact,  $request = null)
    {
        if (isset($contact['id']))
        {
            $data = array();
            $data['registrations'] = Model_SchedulesStudents::search(array('contact_id' => $contact['id']));
        }
        else
        {
            $data['registrations'] = array();
        }

        return $data;
    }

    public function saveData($contact_id, $post)
    {
    }

    public function getTabs($contact_details)
    {
        return array(
            array('name' => 'courseregistrations', 'title' => 'Registrations', 'view' => 'courseregistration_list_contact_extention')
        );
    }

    public function getFieldsets($contact_details)
    {
        return array(

        );
    }
}