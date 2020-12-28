/*
ts:2016-10-25 10:09:00
*/

UPDATE plugin_reports_reports
  SET
    `sql` = REPLACE(`sql`, "CONCAT('/admin/courses/edit_schedule/?id=', `event`.`schedule_id`) AS `Link`", "'' AS `Link`")
  WHERE `name` = 'Calendar';
