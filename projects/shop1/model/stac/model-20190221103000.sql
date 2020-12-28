/*
ts:2019-02-21 10:30:00
*/

UPDATE `engine_settings`
SET `value_live` = 'list', `value_stage` = 'list', `value_test` = 'list', `value_dev` = 'list'
WHERE (`variable` = 'course_search_default_layout');