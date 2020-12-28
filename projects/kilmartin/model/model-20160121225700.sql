/*
ts:2016-01-21 22:57:00
*/

insert ignore into `users`
  set `role_id` = '4', `group_id` = null, `discount_format_id` = null, `email` = 'office@kes.ie', `password` = 'a345510c4d7d7e954e515bf7212bc32144d1cceee4ee935bdc28a608d9e06809', `logins` = '1', `last_login` = '1400771464', `logins_fail` = '0', `last_fail` = null, `name` = 'Office', `surname` = 'Administrator', `country` = null, `timezone` = 'Europe/Dublin', `county` = null, `address` = '', `eircode` = null, `address_2` = null, `address_3` = null, `phone` = '', `mobile` = null, `company` = null, `registered` = '2014-05-22 16:09:48', `email_verified` = '1', `trial_start_date` = null, `can_login` = '1', `deleted` = '0', `validation_code` = null, `status` = '1', `role_other` = null, `heard_from` = null, `credit_account` = '0';


-- KES-1342
INSERT INTO `plugin_messaging_notification_templates` 
  SET
    `name`='teacher-booking-create-notification',
    `description`='Teacher Grinds/Tutorials Automatic Notifications',
    `driver`='EMAIL',
    `type_id`=1,
    `subject`='Teacher Grinds/Tutorials Automatic Notifications',
    `message`='Teacher Grinds/Tutorials Automatic Notifications',
    `date_created`=NOW(),
    `created_by`=1,
    `date_updated`=NOW(),
    `publish`=1,
    `deleted`=0,
    `create_via_code`='Automatic Notifications',
    `usable_parameters_in_template`='$transaction_id',
    `doc_generate`=1,
    `doc_helper`='teacher_booking_confirmation',
    `doc_template_path`='/templates/Teacher_Booking_Confirmation',
    `doc_type`='PDF';
SELECT LAST_INSERT_ID() INTO @tbcn_id_1453425321;
INSERT INTO plugin_messaging_notification_template_targets
  SET
    `template_id`=@tbcn_id_1453425321,
    `target_type`='CMS_USER',
    `target`=(SELECT `id` FROM `users` WHERE `email`='office@kes.ie'),
    `x_details`='bcc',
    `date_created`=NOW();
