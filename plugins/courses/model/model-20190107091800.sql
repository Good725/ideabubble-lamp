/*
Ts:2019-01-25 18:00:00
*/
ALTER TABLE `plugin_courses_categories` 
ADD COLUMN `order` INT(3) NOT NULL DEFAULT 10 AFTER `checkout_alert`;