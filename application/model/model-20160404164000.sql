/*
ts:2016-04-04 16:40:00
*/

INSERT IGNORE INTO `users` (`role_id`, `email`, `password`, `name`, `surname`, `registered`, `email_verified`, `can_login`, `deleted`) VALUES
(
  (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Administrator'),
  'engine@ideabubble.ie',
  'a95fbf39977b8e0cca3c55d47918c840b6751942715e2dc42a3bec0ef614c50a',
  'Engine',
  '',
  CURRENT_TIMESTAMP,
  '1',
  '0',
  '0'
);
