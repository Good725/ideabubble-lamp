/*
ts:2019-06-04 13:21:00
*/

ALTER TABLE `plugin_survey`
    ADD COLUMN `is_backend` TINYINT(1) NULL DEFAULT '0' AFTER `view_all`,
    ADD COLUMN `course_id` INT(11) NULL AFTER `is_backend`,
    ADD COLUMN `contact3_subtype_id` INT(11) NULL AFTER `course_id`;

CREATE TABLE `plugin_ib_educate_bookings_has_surveys`
(
    `booking_id` INT NOT NULL,
    `survey_result_id`  INT NOT NULL,
    PRIMARY KEY (`booking_id`, `survey_result_id`)
);

ALTER TABLE `plugin_survey_result`
    ADD COLUMN `survey_author` INT(11) NULL AFTER `user_ip`;
