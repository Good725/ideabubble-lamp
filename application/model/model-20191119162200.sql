/*
ts:2019-11-19 16:22:00
*/

INSERT IGNORE INTO `engine_users`
  (`role_id`, `email`, `password`, `name`, `surname`, `timezone`, `registered`, `email_verified`, `can_login`, `deleted`, `status`)
  (SELECT `id`, 'kosti@ideabubble.ie', 'a2e3cbbbc8dc840494479939893b1e729ee554026d1a71d3069ce3b5b627659c', 'Kosti', '', 'Europe/Dublin', NOW(), 1, 1, 0, 1 FROM `engine_project_role` WHERE `role` = 'Administrator');

UPDATE IGNORE `engine_users`
SET `email` = 'kosti@ideabubble.ie-disabled',
    `deleted` = 1,
    `can_login` = 0
WHERE (`email` = 'kosti@ideabubble.ie');
