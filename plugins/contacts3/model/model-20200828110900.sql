/*
ts:2020-08-28 11:09:00
*/

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'contacts3_edit', 'Contacts / Edit', 'Contacts / Edit', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'contacts3'));

INSERT INTO engine_role_permissions
  (role_id, resource_id)
  (SELECT r.id, e.id FROM `engine_project_role` r JOIN engine_resources e WHERE r.role IN ('Administrator') AND e.alias = 'contacts3_edit');

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'contacts3_view', 'Contacts / View', 'Contacts / view', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'contacts3'));

INSERT INTO engine_role_permissions
  (role_id, resource_id)
  (SELECT r.id, e.id FROM `engine_project_role` r JOIN engine_resources e WHERE r.role IN ('Administrator') AND e.alias = 'contacts3_view');

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'messaging_edit', 'Messaging / Edit', 'Messaging / Edit', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'messaging'));

INSERT INTO engine_role_permissions
  (role_id, resource_id)
  (SELECT r.id, e.id FROM `engine_project_role` r JOIN engine_resources e WHERE r.role IN ('Administrator') AND e.alias = 'messaging_edit');

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'messaging_view', 'Messaging / View', 'Messaging / View', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'messaging'));

INSERT INTO engine_role_permissions
  (role_id, resource_id)
  (SELECT r.id, e.id FROM `engine_project_role` r JOIN engine_resources e WHERE r.role IN ('Administrator') AND e.alias = 'messaging_view');

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'messaging_send', 'Messaging / Send', 'Messaging / Send', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'messaging'));

INSERT INTO engine_role_permissions
  (role_id, resource_id)
  (SELECT r.id, e.id FROM `engine_project_role` r JOIN engine_resources e WHERE r.role IN ('Administrator') AND e.alias = 'messaging_send');

-- no globally shared dashboards. make them admin only
insert into plugin_dashboards_sharing
(dashboard_id, group_id)
(select id, 2 from (select d.id, d.title, sum(if(s.group_id is not null, 1, 0)) as share_count from plugin_dashboards d
left join plugin_dashboards_sharing s on d.id = s.dashboard_id
group by d.id
having share_count = 0) s);
