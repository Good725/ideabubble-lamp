/*
ts:2020-06-19 15:00:00
*/

-- Allow bookings for particular schedules to be sales quotes.

ALTER TABLE `plugin_courses_schedules`
ADD COLUMN `allow_sales_quote` INT(1) NOT NULL DEFAULT 0 AFTER `is_group_booking`;

INSERT INTO `plugin_ib_educate_bookings_status` (`title`, `publish`, `delete`) VALUES ('Sales Quote', '1', '0');
