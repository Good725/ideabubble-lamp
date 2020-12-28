/*
ts:2016-02-11 16:12:00
*/

INSERT IGNORE INTO `settings`
  (`variable`,`name`,`value_live`,`value_stage`,`value_test`,`value_dev`,`default`,`location`,`note`,`type`,`group`,`required`,`options`)
  VALUES
  ('schedule_default_rental_fee','Schedule Default Rental Fee(%)','50','50','50','50','50','both','Schedule Default Rental Fee','text','Courses',0,'');

UPDATE plugin_courses_categories ca
		INNER JOIN plugin_courses_courses co ON ca.id = co.category_id
		INNER JOIN plugin_courses_schedules sc ON co.id = sc.course_id
	SET sc.rental_fee = '50'
	WHERE ca.category='Grinds/Tutorials' AND (sc.rental_fee IS NULL OR sc.rental_fee <> '50');
