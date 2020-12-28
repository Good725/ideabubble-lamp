/*
ts:2020-01-24 08:43:00
*/
DELIMITER ;;

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
VALUES ((SELECT `id` FROM `engine_project_role` WHERE `role` = 'Teacher'),
        (SELECT `id` FROM `engine_resources` WHERE `alias` = 'reports'));;

INSERT IGNORE INTO `plugin_reports_report_sharing` (`report_id`, `group_id`)
select rr.id, pr.id
from plugin_reports_reports `rr`
         left join engine_project_role `pr` on `pr`.role = 'Administrator';;

INSERT IGNORE INTO `plugin_reports_report_sharing` (`report_id`, `group_id`)
select rr.id, pr.id
from plugin_reports_reports `rr`
         left join engine_project_role `pr` on `pr`.role = 'Super User';;