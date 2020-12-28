/*
ts:2019-11-22 13:19:00
*/
UPDATE
  `plugin_messaging_notification_templates`
SET
  `usable_parameters_in_template` = '$bookingid,$course,$deposit,$fee,$paymenttype,$schedule,$status,$total',
  `date_updated` = CURRENT_TIMESTAMP,
  `message` = "<h1>A new course booking has been made.</h1>
\nBooking ID: $bookingid<br />
\nCourse: $course<br />
\nSchedule: $schedule<br />
\nPayment type: $paymenttype<br />
\nDeposit: $deposit<br />
\nRegistration fee: $fee<br />
\nTotal: $total<br />
\nStatus: $status"
WHERE
  `name` = 'course-booking-admin'
;


UPDATE
  `plugin_messaging_notification_templates`
SET
  `usable_parameters_in_template` = '$bookingid,$course,$deposit,$paymenttype,$schedule,$status,$total'
WHERE
  `name` = 'course-booking-parent'
;

