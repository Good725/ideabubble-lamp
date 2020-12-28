/*
ts:2016-05-11 11:30:00
*/
UPDATE IGNORE `engine_settings`
SET `value_live` = '0', `value_stage` = '0', `value_test` = '0', `value_dev` = '0'
WHERE `variable` = 'course_enquiry_button';