/*
ts:2016-09-16 12:25:00
*/

INSERT IGNORE INTO `engine_users`
  (`role_id`, `email`, `password`, `name`, `surname`, `timezone`, `registered`, `email_verified`, `can_login`, `deleted`, `status`)
SELECT
  `id`, 'kamal@ideabubble.ie', '785b304e72ac0c40b1f33f76c017d6177fd45d949110abab91b9097cf2d95506', 'Kamal', '', 'Europe/Dublin', NOW(), 1, 1, 0, 1
FROM `engine_project_role` WHERE `role` = 'Administrator';

INSERT IGNORE INTO `engine_users`
  (`role_id`, `email`, `password`, `name`, `surname`, `timezone`, `registered`, `email_verified`, `can_login`, `deleted`, `status`)
SELECT
  `id`, 'serge@ideabubble.ie', '2002e7e748cd2b5525bbc9583e114367770b2c0badc6f3fe1f3f1b65f9bb4969', 'Sergey', '', 'Europe/Dublin', NOW(), 1, 1, 0, 1
FROM `engine_project_role` WHERE `role` = 'Administrator';



UPDATE IGNORE `engine_users`
SET
  `can_login` = 0,
  `deleted` = 1
WHERE `email` IN ('ayaz@ideabubble.ie','rabin@ideabubble.ie','serge@ideabubble.ie','yann@ideabubble.ie');


INSERT IGNORE INTO `engine_users`
  (`role_id`, `email`, `password`, `name`, `surname`, `timezone`, `registered`, `email_verified`, `can_login`, `deleted`, `status`)
SELECT
  `id`, 'jasmine@ideabubble.ie', 'e46f3f6d992b5bc66ad2181f634530669169fc0575f04edfaa73b0850109d1fa', 'Jasmine', '', 'Europe/Dublin', NOW(), 1, 1, 0, 1
FROM `engine_project_role` WHERE `role` = 'Administrator';


UPDATE IGNORE `engine_users`
SET
  `can_login` = 0,
  `deleted` = 1
WHERE `email` = ('jasmine@ideabubble.ie');


INSERT IGNORE INTO `engine_users`
  (`role_id`, `email`, `password`, `name`, `surname`, `timezone`, `registered`, `email_verified`, `can_login`, `deleted`, `status`)
SELECT
  `id`, 'alex@ideabubble.ie', '587c758fbc9641fb4e8758c2ae4e1ace0f5f3401b2e2df210f2bfd450f45f2c5', 'Alex', '', 'Europe/Dublin', NOW(), 1, 1, 0, 1
FROM `engine_project_role` WHERE `role` = 'Administrator';

INSERT IGNORE INTO `engine_users`
  (`role_id`, `email`, `password`, `name`, `surname`, `timezone`, `registered`, `email_verified`, `can_login`, `deleted`, `status`)
SELECT
  `id`, 'peter@ideabubble.ie', '3c2e477706eb43f7e75471d7514b978c3d0fec2d059eb87672ee553c5e28c16a', 'Peter', '', 'Europe/Dublin', NOW(), 1, 1, 0, 1
FROM `engine_project_role` WHERE `role` = 'Administrator';