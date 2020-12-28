/*
ts:2019-01-24 10:47:00
*/

UPDATE `plugin_reports_parameters`
  SET `value`='SELECT DISTINCT t.id, CONCAT_WS(\' \', t.title, ifnull(t.first_name, \'No Trainer\'), t.last_name)\r\n	FROM plugin_courses_schedules s\r\n		INNER JOIN plugin_courses_schedules_events e ON s.id = e.schedule_id AND s.delete=0 AND e.delete=0\r\n		LEFT JOIN plugin_contacts3_contacts t ON t.id = IF(e.trainer_id > 0, e.trainer_id, s.trainer_id)\r\n		LEFT JOIN plugin_courses_locations  locations ON s.location_id = locations.id\r\n		LEFT JOIN plugin_courses_locations buildings ON locations.parent_id = buildings.id\r\n	WHERE e.datetime_start >= \'{!date!}\' AND e.datetime_start < DATE_ADD(\'{!date!}\',INTERVAL 1 DAY) AND (buildings.id = \'{!location!}\' or buildings.id is null)\r\n	ORDER BY t.first_name, t.last_name\r\n'
  WHERE (`name`='trainer_id' and report_id in (select id from plugin_reports_reports where name in ('Master Roll Call', 'Print Roll Call')));
