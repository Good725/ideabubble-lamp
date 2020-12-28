/*
ts:2018-09-27 10:00:00
*/

INSERT IGNORE INTO `engine_users`
  (`role_id`, `email`, `password`, `name`, `surname`, `timezone`, `registered`, `email_verified`, `can_login`, `deleted`, `status`)
VALUES (
  (SELECT IFNULL(`id`, 2) FROM `engine_project_role` WHERE `role` = 'Administrator' AND `deleted` = 0),
  'adam@ideabubble.ie',
  'a7416dbf259b73697ef1941abd50fb1b0660f91ba5ccd1c9c1a8269bfa6ae361',
  'Adam',
  '',
  'Europe/Dublin',
  CURRENT_TIMESTAMP(),
  1,
  1,
  0,
  1
);