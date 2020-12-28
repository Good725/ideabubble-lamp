/*
ts:2016-08-15 10:00:00
*/

UPDATE `engine_settings` SET `value_live`='modern', `value_stage`='modern', `value_test`='modern', `value_dev`='modern' WHERE `variable`='cms_template';
UPDATE `engine_settings` SET `value_live`='black',  `value_stage`='black',  `value_test`='black',  `value_dev`='black'  WHERE `variable`='cms_skin';
