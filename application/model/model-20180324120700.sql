/*
ts:2018-03-24 12:07:00
*/

INSERT IGNORE INTO `engine_users`
  (`role_id`, `email`, `password`, `name`, `surname`, `timezone`, `registered`, `email_verified`, `can_login`, `deleted`, `status`)
  VALUES
  (2, 'dave@ideabubble.ie', '116958515b157cec2e4ac5cded1c23efff76ad2e02374ae493b1ca60658df69a', 'Dave', '', 'Europe/Dublin', NOW(), 1, 1, 0, 1);

INSERT IGNORE INTO `engine_users`
  (`role_id`, `email`, `password`, `name`, `surname`, `timezone`, `registered`, `email_verified`, `can_login`, `deleted`, `status`)
  VALUES
  (2, 'maja@ideabubble.ie', 'e9614f22d444e19047bcf095d33e1be8eb114304d7141548bc65a9f66047fa7b', 'Maja', '', 'Europe/Dublin', NOW(), 1, 1, 0, 1);
