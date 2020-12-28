/*
ts:2019-10-28 08:27:00
*/

ALTER TABLE plugin_courses_wishlist ADD COLUMN course_id INT;
ALTER TABLE plugin_courses_wishlist ADD KEY (course_id);
ALTER TABLE plugin_courses_wishlist MODIFY COLUMN schedule_id  INT NULL;
