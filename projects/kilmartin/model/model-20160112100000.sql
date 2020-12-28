/*
ts:2016-01-12 10:00:00
*/

SELECT `id` FROM `plugin_courses_years` WHERE `year` LIKE '%Transition Year%' INTO @transyear_id;
SELECT `id` FROM `plugin_courses_years` WHERE `year` LIKE '%4th year%' INTO @fourthyear_id;
UPDATE `plugin_courses_courses` SET `year_id` = @transyear_id WHERE `year_id` = @fourthyear_id;
UPDATE `plugin_contacts3_contacts` SET `year_id` = @transyear_id WHERE `year_id` = @fourthyear_id;
UPDATE `plugin_courses_years` SET `publish` = 0 , `delete`=1 WHERE `id` = @fourthyear_id;
