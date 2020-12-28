/*
ts:2016-08-24 17:32:00
*/

INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`) VALUES (0, 'transactions', 'Transactions', 'Transactions / Full Access');

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'transactions_limited_access', 'Transactions / Limited Access', 'Transactions / Limited Access', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'transactions'));

INSERT INTO engine_role_permissions
  (role_id, resource_id)
  (SELECT r.id, e.id FROM `engine_project_role` r JOIN engine_resources e WHERE r.role = 'Administrator' AND e.alias = 'transactions');

INSERT INTO engine_role_permissions
  (role_id, resource_id)
  (SELECT r.id, e.id FROM `engine_project_role` r JOIN engine_resources e WHERE r.role IN ('External User', 'Parent/Guardian', 'Student') AND e.alias = 'transactions_limited_access');

UPDATE engine_plugins SET `friendly_name` = 'Accounts', `show_on_dashboard` = 1, `icon` = '', `flaticon` = 'coins' WHERE `name` = 'transactions';

