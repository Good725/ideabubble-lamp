/*
ts:2019-08-15 12:30:00
*/
INSERT IGNORE INTO `plugin_messaging_notification_categories` (`name`) VALUES
('Assessments'),
('Auth'),
('Bookings'),
('Contacts'),
('Courses'),
('Dashboards'),
('Donations'),
('Events'),
('Logistics'),
('Messaging'),
('Inventory'),
('iOS'),
('Payments'),
('Products'),
('Reports'),
('Surveys'),
('System'),
('Timeoff'),
('Timesheets'),
('Timetables'),
('Todos');

UPDATE `plugin_messaging_notification_templates`
SET    `category_id` = (SELECT `id` FROM `plugin_messaging_notification_categories` WHERE `name` = 'Auth')
WHERE  `name` IN ('new_user_no_password', 'register_account_admin', 'register_account_user', 'reset_cms_password', 'user-email-verification');

UPDATE `plugin_messaging_notification_templates`
SET    `category_id` = (SELECT `id` FROM `plugin_messaging_notification_categories` WHERE `name` = 'Bookings')
WHERE  `name` IN (
    'course-booking-cancelled-parent-email', 'course-booking-cancelled-parent-sms', 'course-booking-parent', 'course-interview-admin',
    'course-interview-schedule', 'course-payment-plan-due', 'course-timeslot-changed', 'course-timeslots-full', 'fulltime-course-application-admin',
    'fulltime-course-application-approved-customer', 'fulltime-course-application-customer', 'fulltime-course-application-approved-customer',
    'host_application_admin', 'host_application_applicant', 'recurring-payment-failed-admin', 'recurring-payment-failed-customer',
    'recurring-payment-succeeded-admin', 'recurring-payment-succeeded-customer', 'rollcall-absent-sms', 'rollcall-left-early-sms',
    'rollcall-signed-late-sms', 'student-attendance-edit-send-auth-code', 'teacher-booking-create-notification', 'wishlist-added'
  );

UPDATE `plugin_messaging_notification_templates`
SET    `category_id` = (SELECT `id` FROM `plugin_messaging_notification_categories` WHERE `name` = 'Contacts')
WHERE  `name` IN ('contact-invite-family-member', 'student-timetable');

UPDATE `plugin_messaging_notification_templates`
SET    `category_id` = (SELECT `id` FROM `plugin_messaging_notification_categories` WHERE `name` = 'Courses')
WHERE   `name` IN ('course-booking-admin', 'course-booking-parent');

UPDATE `plugin_messaging_notification_templates`
SET    `category_id` = (SELECT `id` FROM `plugin_messaging_notification_categories` WHERE `name` = 'Payments')
WHERE  `name` IN ('successful_payment_customer', 'successful_payment_seller');

