/*
ts:2019-11-22 13:20:00
*/
DELIMITER ;;

UPDATE
  `plugin_messaging_notification_templates`
SET
  `date_updated` = CURRENT_TIMESTAMP,
  `message` = "<h1>A new course booking has been made</h1>
\nBooking ID: $bookingid<br />
\nCourse: $course<br />
\nSchedule: $schedule<br />
\nPayment type: $paymenttype<br />
\nRegistration fee: $deposit<br />
\nSubscription fee: $fee<br />
\nTotal: $total<br />
\nStatus: $status
"
WHERE
  `name` = 'course-booking-admin'
;;

UPDATE
  `plugin_messaging_notification_templates`
SET
  `date_updated` = CURRENT_TIMESTAMP,
  `message` = "Welcome to Voiceworks Studio!
\n
\n<h1>Your course booking details</h1>
\nBooking ID: $bookingid<br />
\nCourse: $course<br />
\nSchedule: $schedule<br />
\nPayment Type: $paymenttype<br />
\nRegistration fee: $deposit<br />
\nSubscription fee: $fee<br />
\nTotal: $total<br />
\n
\nWe can&#39;t wait to help you smash your musical goals!
"
WHERE
  `name` = 'course-booking-parent'
;;

