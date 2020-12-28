/*
ts:2017-03-09 14:57:00
*/

INSERT IGNORE INTO `engine_users`
  (`role_id`, `email`, `password`, `name`, `surname`, `timezone`, `registered`, `email_verified`, `can_login`, `deleted`, `status`)
  VALUES
  (2, 'davit@ideabubble.ie', '92acb4d44d3c27a56626292dace561a5f8ead06ec773e7737bc061396a13ea99', 'Davit', '', 'Europe/Dublin', NOW(), 1, 1, 0, 1);

INSERT IGNORE INTO `engine_users`
  (`role_id`, `email`, `password`, `name`, `surname`, `timezone`, `registered`, `email_verified`, `can_login`, `deleted`, `status`)
  VALUES
  (2, 'taron@ideabubble.ie', '92acb4d44d3c27a56626292dace561a5f8ead06ec773e7737bc061396a13ea99', 'Davit', '', 'Europe/Dublin', NOW(), 1, 1, 0, 1);
