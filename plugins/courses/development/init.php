<?php defined('SYSPATH') or die('No Direct Script Access.');

if(
    Model_Plugin::is_enabled_for_role('Administrator', 'contacts2')
    &&
    Model_Plugin::is_enabled_for_role('Administrator', 'courses')
    &&
    !Model_Plugin::is_enabled_for_role('Administrator', 'contacts3')
) {
    if (Settings::instance()->get('courses_enable_bookings') == 1) {
        require_once __DIR__ . '/classes/contactcourseextention.php';
        Model_Contacts::registerExtention(new ContactCourseExtention());
        if (Auth::instance()->has_access('courses_limited_access')) {
            MenuArea::factory()->override_link('courses', array('name' => 'Bookings'));
        }
    }

    if (Settings::instance()->get('courses_enable_registrations') == 1) {
        require_once __DIR__ . '/classes/contactregistrationextention.php';
        Model_Contacts::registerExtention(new ContactRegistrationExtention());
    }


}
Controller_Page::addActionAlias('add-to-waitlist', 'Controller_Frontend_Courses', 'action_add_to_waitlist');


if(Model_Plugin::is_enabled_for_role('Administrator', 'courses')) {
    Model_Automations::add_action(new Model_Courses_AlertTrainerAction());
    Model_Automations::add_trigger(new Model_Courses_Coursesavetrigger());
    Model_Automations::add_trigger(new Model_Courses_Schedulesavetrigger());
    Model_Automations::add_trigger(new Model_Courses_Schedulestarttrigger());
    Model_Automations::add_trigger(new Model_Courses_Scheduleendtrigger());
    Model_Automations::add_trigger(new Model_Courses_Timeslotstarttrigger());
    Model_Automations::add_trigger(new Model_Courses_Timeslotendtrigger());
    Model_Automations::add_trigger(new Model_Courses_Schedulechangedtrigger());
    Model_Automations::add_trigger(new Model_Courses_Schedulespaceavailabletrigger());
    Model_Automations::add_trigger(new Model_Courses_Cancelledschedulesdigesttrigger());
    Model_Automations::add_trigger(new Model_Courses_Upcomingschedulesdigesttrigger());
}
