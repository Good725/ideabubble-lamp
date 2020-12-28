/*
ts:2018-10-05 15:00:00
*/

ALTER IGNORE TABLE `plugin_courses_categories` ADD COLUMN `checkout_alert` VARCHAR(1024) NULL;

UPDATE
  `plugin_courses_categories`
SET
  `checkout_alert` = 'Please be advised that your student has been booked in for all available study sessions. To edit this, please go to the attendance tab on <a href="/profile" target="_blank">your profile</a>.'
WHERE
  `category` = 'Supervised Study'
;