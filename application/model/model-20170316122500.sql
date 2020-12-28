/*
ts:2017-03-16 12:23:00
*/

INSERT IGNORE INTO `engine_users`
  (`role_id`, `email`, `password`, `name`, `surname`, `timezone`, `registered`, `email_verified`, `can_login`, `deleted`, `status`)
SELECT
  `id`, 'jack@ideabubble.ie', '852f6b1cdb3ddfd26c52d94979d97ed3dbcd31177487cc13633568e64080e83d', 'Jack', '', 'Europe/Dublin', CURRENT_TIMESTAMP(), 1, 1, 0, 1
FROM
  `engine_project_role`
WHERE
  `role` = 'Administrator';

INSERT IGNORE INTO `engine_users`
  (`role_id`, `email`, `password`, `name`, `surname`, `timezone`, `registered`, `email_verified`, `can_login`, `deleted`, `status`)
SELECT
  `id`, 'rupali@ideabubble.ie', 'd2e2a0a0d26b267602ea97b6a2630409b27916eee8a79251fa5453065ebe1dc0', 'Rupali', '', 'Europe/Dublin', CURRENT_TIMESTAMP(), 1, 1, 0, 1
FROM
  `engine_project_role`
WHERE
  `role` = 'Administrator';