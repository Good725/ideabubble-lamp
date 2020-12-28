/*
ts:2016-10-04 17:30:00
*/

INSERT IGNORE INTO `engine_users`
  (`role_id`, `email`, `password`, `name`, `surname`, `timezone`, `registered`, `email_verified`, `can_login`, `deleted`, `status`)
SELECT
  `id`, 'alexey@ideabubble.ie', 'd2c8afa725ad08bef2d49b3782809721f457577592da04e6136d02302bacef4d', 'Alexey', '', 'Europe/Dublin', NOW(), 1, 1, 0, 1
FROM `engine_project_role` WHERE `role` = 'Administrator';

INSERT IGNORE INTO `engine_users`
  (`role_id`, `email`, `password`, `name`, `surname`, `timezone`, `registered`, `email_verified`, `can_login`, `deleted`, `status`)
SELECT
  `id`, 'john@ideabubble.ie', '88a46222753816d31f871a0ef2d4fe2d3203aef873505c73f7ea962cdd476589', 'John', '', 'Europe/Dublin', NOW(), 1, 1, 0, 1
FROM `engine_project_role` WHERE `role` = 'Administrator';

INSERT IGNORE INTO `engine_users`
  (`role_id`, `email`, `password`, `name`, `surname`, `timezone`, `registered`, `email_verified`, `can_login`, `deleted`, `status`)
SELECT
  `id`, 'sasha@ideabubble.ie', '1ca0f43b0f9c7fbec88473d558d19e819c30ed9c4dc2b10a0a4b1d08246c4816', 'Sasha', '', 'Europe/Dublin', NOW(), 1, 1, 0, 1
FROM `engine_project_role` WHERE `role` = 'Administrator';

UPDATE IGNORE `engine_users`
SET
  `can_login` = 0,
  `deleted` = 1
WHERE `email` IN ('alex@ideabubble.ie');

INSERT IGNORE INTO `engine_users`
  (`role_id`, `email`, `password`, `name`, `surname`, `timezone`, `registered`, `email_verified`, `can_login`, `deleted`, `status`)
SELECT
  `id`, 'maja@ideabubble.ie', 'd8cc220d60d1e3ab6cbb97f29b3fb360b76df37b3ba22c6ee9c43d673cd7a648', 'Maja', '', 'Europe/Dublin', NOW(), 1, 1, 0, 1
FROM `engine_project_role` WHERE `role` = 'Administrator';

INSERT IGNORE INTO `engine_users`
  (`role_id`, `email`, `password`, `name`, `surname`, `timezone`, `registered`, `email_verified`, `can_login`, `deleted`, `status`)
SELECT
  `id`, 'aman@ideabubble.ie', 'bb75dbaeb9516a71e6c9cdf97ccaacf0fd86d611217cfecfef4d71ce0afac45b', 'Aman', '', 'Europe/Dublin', NOW(), 1, 1, 0, 1
FROM `engine_project_role` WHERE `role` = 'Administrator';
