/*
ts:2019-07-24 07:49:00
*/

ALTER TABLE plugin_courses_schedules ADD COLUMN trial_timeslot_free_booking TINYINT NOT NULL DEFAULT 0;
ALTER TABLE plugin_courses_schedules_has_paymentoptions ADD COLUMN start_after_first_timeslot TINYINT NOT NULL DEFAULT 0;

ALTER TABLE `plugin_courses_schedules`
  MODIFY COLUMN `fee_per`  ENUM('Timeslot','Schedule','Day','Week','Month'),
  MODIFY COLUMN `booking_type`  ENUM('One Timeslot','Whole Schedule','Subscription'),
  MODIFY COLUMN `payg_period`  ENUM('timeslot','week','month');

ALTER TABLE plugin_bookings_transactions ADD COLUMN failed_auto_payment_attempts TINYINT NOT NULL DEFAULT 0;
