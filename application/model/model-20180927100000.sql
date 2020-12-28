/*
ts:2018-09-27 10:00:00
*/

INSERT IGNORE INTO `engine_users`
  (`role_id`, `email`, `password`, `name`, `surname`, `timezone`, `registered`, `email_verified`, `can_login`, `deleted`, `status`)
VALUES (
  (SELECT IFNULL(`id`, 2) FROM `engine_project_role` WHERE `role` = 'Administrator' AND `deleted` = 0),
  'alexandr@ideabubble.ie',
  '66974ef8f2e454096d43761bb4f7024f333ddd1e4044278aff5753381b725b31',
  'Alexandr',
  '',
  'Europe/Dublin',
  CURRENT_TIMESTAMP(),
  1,
  1,
  0,
  1
);