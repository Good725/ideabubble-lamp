/*
ts:2020-01-24 12:21:00
*/

truncate plugin_reports_report_sharing;

INSERT IGNORE INTO `plugin_reports_report_sharing` (`report_id`, `group_id`)
VALUES ((SELECT id FROM plugin_reports_reports where name = 'My Roll Call' LIMIT 1), (SELECT id
                                                                                      FROM engine_project_role
                                                                                      WHERE role = 'Teacher'
                                                                                      LIMIT 1));

