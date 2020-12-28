<?php defined('SYSPATH') or die('No Direct Script Access.');

final class Controller_Frontend_DCS extends Controller
{
    public function action_cron()
    {
        set_time_limit(0);
        ignore_user_abort(true);
        
        $this->auto_render = false;
        $this->response->headers('Content-type', 'text/plain; charset=utf-8');
        try {

            $dcs = new Model_DCS2();
            $dcs->key = Settings::instance()->get('dcsapi_key');
            $dcs->password = Settings::instance()->get('dcsapi_password');
            $dcs->security = Settings::instance()->get('dcsapi_security');
            $dcs->username = Settings::instance()->get('dcsapi_username');
            $dcs->vendor = Settings::instance()->get('dcsapi_vendor');

            //print_r($dcs);
            //$dcs->import_locations();
            $dcs->clean_sync();
            $dcs->import_courses();
            $dcs->import_families();
            $dcs->import_students();
            $dcs->import_applications();
            $dcs->import_registrations();
            $dcs->import_waitinglist();
            echo 'dcs import cron completed';
        } catch (Exception $exc) {
            throw $exc;
            //echo $exc->getMessage();
        }
    }

    public function action_fix_notifs()
    {
        $dcs = new Model_DCS2();
        $dcs->key = Settings::instance()->get('dcsapi_key');
        $dcs->password = Settings::instance()->get('dcsapi_password');
        $dcs->security = Settings::instance()->get('dcsapi_security');
        $dcs->username = Settings::instance()->get('dcsapi_username');
        $dcs->vendor = Settings::instance()->get('dcsapi_vendor');

        $dcs->fix_notifs();
    }

    public function action_import_old()
    {
        $registrations = DB::select('has.*', 'schedules.course_id')
            ->from(array(Model_SchedulesStudents::REGISTRATION, 'has'))
            ->join(array(Model_Schedules::TABLE_SCHEDULES, 'schedules'))->on('schedules.id', '=', 'has.schedule_id')
            ->where('has.deleted', '=', 0)
            ->execute()
            ->as_array();

        foreach ($registrations as $registration) {
            $booking_id = Model_DCS2::create_booking(
                $registration['contact_id'],
                $registration['course_id'],
                $registration['schedule_id'],
                null,
                1,
                $registration['id']
            );
        }
        echo 'imported';
    }
}