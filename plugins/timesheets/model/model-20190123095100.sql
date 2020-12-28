/*
ts:2019-01-23 09:51:00
*/

/*clean up old permissions*/
delete engine_role_permissions from engine_role_permissions inner join engine_resources on engine_role_permissions.resource_id = engine_resources.id
	where engine_resources.alias like 'timesheets%';

delete engine_role_permissions from engine_role_permissions inner join engine_resources on engine_role_permissions.resource_id = engine_resources.id
	where engine_resources.alias = 'contacts3_frontend_timesheets';

delete from engine_resources where alias in ('contacts3_frontend_timesheets', 'timesheets_list', 'timesheets_index');

INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`) VALUES (0, 'timesheets', 'Timesheets Plugin', 'Timesheets Plugin');

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'timesheets_edit', 'timesheets Edit All', 'Timesheets Edit All', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'timesheets'));

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'timesheets_edit_limited', 'Timesheets Edit Limited', 'Timesheets Edit Limited', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'timesheets'));
