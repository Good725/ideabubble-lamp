/*
ts:2016-01-12 16:48:00
*/

DELETE FROM `engine_user_tokens`;
UPDATE `users` SET `deleted` = 1
  WHERE (`email` LIKE '%@ideabubble.com' OR `email` LIKE '%@ideabubble.ie') AND
  `email` NOT IN ('tempy@ideabubble.ie', 'michael@ideabubble.ie', 'stephen@ideabubble.ie', 'mehmet@ideabubble.ie', 'ayaz@ideabubble.ie', 'gevorg@ideabubble.ie', 'super@ideabubble.ie');

UPDATE `users` SET `email` = 'tempy@ideabubble.ie' WHERE `email` = 'tempy@ideabubble.com';
SELECT COUNT(*) INTO @mike_2016011216 from `users` where `email` = 'michael@ideabubble.ie';
UPDATE `users` SET `email` = 'michael@ideabubble.ie' WHERE `email` = 'michael@ideabubble.com' AND @mike_2016011216 = 0;
UPDATE `users` SET `email` = 'stephen@ideabubble.ie' WHERE `email` = 'stephen@ideabubble.com';
UPDATE `users` SET `email` = 'mehmet@ideabubble.ie' WHERE `email` = 'mehmet@ideabubble.com';
UPDATE `users` SET `email` = 'ayaz@ideabubble.ie' WHERE `email` = 'ayaz@ideabubble.com';
UPDATE `users` SET `email` = 'gevorg@ideabubble.ie' WHERE `email` = 'gevorg@ideabubble.com';
UPDATE `users` SET `email` = 'super@ideabubble.ie' WHERE `email` = 'super@ideabubble.com';

INSERT IGNORE INTO `users`
  (`role_id`, `email`, `password`, `name`, `surname`, `timezone`, `registered`, `email_verified`, `can_login`, `deleted`, `status`)
  VALUES
  (1, 'super@ideabubble.ie', '97f37725f639494e19a511fc01c6d2f9f83cc1fe9a75e932af81cb49efac1fa9', 'Super User', '', 'Europe/Dublin', NOW(), 1, 1, 0, 1);

INSERT IGNORE INTO `users`
  (`role_id`, `email`, `password`, `name`, `surname`, `timezone`, `registered`, `email_verified`, `can_login`, `deleted`, `status`)
  VALUES
  (2, 'tempy@ideabubble.ie', '99c11e9f45a1c58b587a3819bb1c121743289a5a1bc9f507f4b820f9233a2c6a', 'Tempy', '', 'Europe/Dublin', NOW(), 1, 1, 0, 1);

INSERT IGNORE INTO `users`
  (`role_id`, `email`, `password`, `name`, `surname`, `timezone`, `registered`, `email_verified`, `can_login`, `deleted`, `status`)
  VALUES
  (2, 'michael@ideabubble.ie', 'a6d06e24e867f727bb1d8298df7792fe2d56cd54afce27d85627623bfc1f68dc', 'Tempy', '', 'Europe/Dublin', NOW(), 1, 1, 0, 1);

INSERT IGNORE INTO `users`
  (`role_id`, `email`, `password`, `name`, `surname`, `timezone`, `registered`, `email_verified`, `can_login`, `deleted`, `status`)
  VALUES
  (2, 'stephen@ideabubble.ie', '03aebb2c56e24c29570d5839d07c0e2bcddabb71a133d26cee36361e278fa127', 'Tempy', '', 'Europe/Dublin', NOW(), 1, 1, 0, 1);

INSERT IGNORE INTO `users`
  (`role_id`, `email`, `password`, `name`, `surname`, `timezone`, `registered`, `email_verified`, `can_login`, `deleted`, `status`)
  VALUES
  (2, 'mehmet@ideabubble.ie', 'e18cd0783dec80f93589cf610de4323b96b74e9a32e3cd3cad5f32bacbe0f42b', 'Tempy', '', 'Europe/Dublin', NOW(), 1, 1, 0, 1);

INSERT IGNORE INTO `users`
  (`role_id`, `email`, `password`, `name`, `surname`, `timezone`, `registered`, `email_verified`, `can_login`, `deleted`, `status`)
  VALUES
  (2, 'ayaz@ideabubble.ie', '9690fd54804fdb91382e147904868ce04d9b5ca217a5872c4748dd10dcc2ce2d', 'Tempy', '', 'Europe/Dublin', NOW(), 1, 1, 0, 1);

INSERT IGNORE INTO `users`
  (`role_id`, `email`, `password`, `name`, `surname`, `timezone`, `registered`, `email_verified`, `can_login`, `deleted`, `status`)
  VALUES
  (2, 'gevorg@ideabubble.ie', 'b0ec06272ebbc9c21961053dcbbb67981805995eb3e1c7aeee81f4ecea91b85c', 'Tempy', '', 'Europe/Dublin', NOW(), 1, 1, 0, 1);

INSERT IGNORE INTO `users`
  (`role_id`, `email`, `password`, `name`, `surname`, `timezone`, `registered`, `email_verified`, `can_login`, `deleted`, `status`)
  VALUES
  (2, 'yann@ideabubble.ie', '0665f61070593cdab22a0e92510b9c1c9fd887e6e19616989ca833e507983808', 'Yann', 'Coussot', 'Europe/Dublin', NOW(), 1, 1, 0, 1);

UPDATE `users` SET `deleted` = 0, `role_id` = 1, `name` = 'Super', `surname` = '', `password` = '97f37725f639494e19a511fc01c6d2f9f83cc1fe9a75e932af81cb49efac1fa9' WHERE `email` = 'super@ideabubble.ie';
UPDATE `users` SET `deleted` = 0, `role_id` = 2, `name` = 'Tempy', `surname` = 'Allen', `password` = '99c11e9f45a1c58b587a3819bb1c121743289a5a1bc9f507f4b820f9233a2c6a' WHERE `email` = 'tempy@ideabubble.ie';
UPDATE `users` SET `deleted` = 0, `role_id` = 2, `name` = 'Michael', `surname` = 'O''Callaghan', `password` = 'a6d06e24e867f727bb1d8298df7792fe2d56cd54afce27d85627623bfc1f68dc' WHERE `email` = 'michael@ideabubble.ie';
UPDATE `users` SET `deleted` = 0, `role_id` = 2, `name` = 'Stephen', `surname` = 'Byrne', `password` = '03aebb2c56e24c29570d5839d07c0e2bcddabb71a133d26cee36361e278fa127' WHERE `email` = 'stephen@ideabubble.ie';
UPDATE `users` SET `deleted` = 0, `role_id` = 2, `name` = 'Mehmet', `surname` = 'Akyuz', `password` = 'e18cd0783dec80f93589cf610de4323b96b74e9a32e3cd3cad5f32bacbe0f42b' WHERE `email` = 'mehmet@ideabubble.ie';
UPDATE `users` SET `deleted` = 0, `role_id` = 2, `name` = 'Ayaz', `surname` = 'Ashrapov', `password` = '9690fd54804fdb91382e147904868ce04d9b5ca217a5872c4748dd10dcc2ce2d' WHERE `email` = 'ayaz@ideabubble.ie';
UPDATE `users` SET `deleted` = 0, `role_id` = 2, `name` = 'Gevorg', `surname` = 'Mansuryan', `password` = 'b0ec06272ebbc9c21961053dcbbb67981805995eb3e1c7aeee81f4ecea91b85c' WHERE `email` = 'gevorg@ideabubble.ie';
UPDATE `users` SET `deleted` = 0, `role_id` = 2, `name` = 'Yann', `surname` = 'Coussot', `password` = '0665f61070593cdab22a0e92510b9c1c9fd887e6e19616989ca833e507983808' WHERE `email` = 'yann@ideabubble.ie';
