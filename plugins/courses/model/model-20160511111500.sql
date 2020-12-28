/*
ts:2016-05-11 11:15:00
*/

INSERT IGNORE INTO `engine_settings`
    (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`)
VALUES
    ('course_enquiry_button', 'Enquiry Button', '0', '0', '0', '0', '0', 'Display an enquiry button on course pages.', 'toggle_button', 'Courses', 'Model_Settings,on_or_off');

UPDATE IGNORE `engine_settings`
SET `value_live` = 1, `value_stage` = 1, `value_test` = 1, `value_dev` = 1, `default` = 1
WHERE `variable` = 'course_enquiry_button';