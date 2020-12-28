/*
ts:2018-11-02 13:38:00
*/

INSERT IGNORE INTO `engine_users`
  (`role_id`, `email`, `password`, `name`, `surname`, `timezone`, `registered`, `email_verified`, `can_login`, `deleted`, `status`)
  (SELECT `id`, 'liam@ideabubble.ie', '3ce0200e36d541934d222c5c6428f558755641b2b4a8874d0c4627b3ff7a5ada', 'Liam', '', 'Europe/Dublin', NOW(), 1, 1, 0, 1 FROM `engine_project_role` WHERE `role` = 'Administrator');
