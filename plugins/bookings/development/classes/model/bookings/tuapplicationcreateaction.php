<?php

class Model_Bookings_Tuapplicationcreateaction extends Model_Automations_Action
{
    public function __construct()
    {
        $this->name = 'Create TU Application';
        $this->purpose = Model_Automations::PURPOSE_SAVE;
        $this->params = array('booking_id', 'schedule_id', 'contact_id');
    }

    public function run($params = array())
    {
        $this->message = null;
        try {
            $booking_id   = $params['bookingid'];
            $delegate_id  = $params['studentid'];
            if ($booking_id == null || $delegate_id == null) {
                throw new Exception("TU Applicaton Create Missing Parameters: bookingid =>" . $booking_id , '; studentid =>' . $delegate_id);
            }
            $booking      = new Model_Booking_Booking($booking_id);
            if ($booking->booking_status == 1 || $booking->booking_status == 3) {
                return;
            }
            $contact      = $delegate_id ? new Model_Contacts3_Contact($delegate_id) : $booking->applicant;
            $application = new Model_Booking_Application();
            $application->booking_id = $booking_id;
            $application->delegate_id = $delegate_id;
            $data = array();
            $schedule = $booking->schedules->find();
            $data['has_course_id']   = $schedule->course_id;
            $data['has_schedule_id'] = $schedule->id;
            $data['contact_id'] = $contact->id;
            $data['booking_id'] = $booking_id;
            $data['schedule_id'] = $schedule->id;
            $application->data = json_encode($data);
            $status_id = Model_KES_Bookings::ENQUIRY;
            if (@$data['has_course_id']) {
                $cinserted = DB::insert(Model_KES_Bookings::BOOKING_COURSES)
                    ->values(array(
                        'booking_id' => $booking_id,
                        'course_id' => $data['has_course_id'],
                        'deleted' => 0,
                        'booking_status' => 1
                    ))->execute();
            }
            $application->status_id = $status_id;
            $application->save_with_history('status_id', $status_id);
        } catch (Exception $exc) {
            Model_Errorlog::save($exc);
        }
    }
}