/*
ts:2019-11-13 15:19:00
*/

ALTER IGNORE TABLE `plugin_courses_courses`
    ADD COLUMN `third_party_link` VARCHAR(250) NULL DEFAULT NULL AFTER `schedule_allow_price_override`;
