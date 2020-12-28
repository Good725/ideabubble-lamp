/*
ts:2016-08-29 17:45:00
*/

UPDATE `plugin_pages_pages`
SET `content` = CONCAT(`content`, '<p>{course_booking_data-}</p>')
WHERE `name_tag` IN ('thanks-for-shopping-with-us.html', 'thanks-for-shopping-with-us');