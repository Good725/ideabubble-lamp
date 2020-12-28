/*
ts:2018-11-05 09:33:00
*/

INSERT IGNORE INTO `engine_users`
  (`role_id`, `email`, `password`, `name`, `surname`, `timezone`, `registered`, `email_verified`, `can_login`, `deleted`, `status`)
  (SELECT `id`, 'robert@ideabubble.ie', 'ccd33b40308a27429db4ab695fc447fe0f42e66d37270924d27a4ba98d1c7d21', 'Robert', '', 'Europe/Dublin', NOW(), 1, 1, 0, 1 FROM `engine_project_role` WHERE `role` = 'Administrator');
UPDATE engine_users SET `password` = 'ccd33b40308a27429db4ab695fc447fe0f42e66d37270924d27a4ba98d1c7d21' WHERE `email` = 'robert@ideabubble.ie';
