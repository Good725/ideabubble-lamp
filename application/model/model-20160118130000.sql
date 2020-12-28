/*
ts:2016-01-18 13:00:00
*/
INSERT IGNORE INTO `users`
  (`role_id`, `email`, `password`, `name`, `surname`, `timezone`, `registered`, `email_verified`, `can_login`, `deleted`, `status`)
SELECT
  `id`, 'ratko@ideabubble.ie', 'ec3a28142eae20e27a94c24fcba14941e452664f1a50ee2d90a603ffe4991524', 'Ratko', 'Bucic', 'Europe/Dublin', NOW(), 1, 1, 0, 1
FROM `engine_project_role` WHERE `role` = 'Administrator';

UPDATE IGNORE `users` SET
`role_id` = (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Administrator'),
`name` = 'Ratko',
`surname` = 'Bucic',
`password` = 'ec3a28142eae20e27a94c24fcba14941e452664f1a50ee2d90a603ffe4991524',
`timezone` = 'Europe/Dublin',
`email_verified` = 1,
`can_login` = 1,
`deleted` = 0,
`status` = 1
 WHERE `email` = 'ratko@ideabubble.ie';
