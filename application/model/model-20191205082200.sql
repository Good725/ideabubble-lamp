/*
ts:2019-12-05 08:22:00
*/


INSERT IGNORE INTO `engine_users`
(`role_id`, `email`, `password`, `name`, `surname`, `timezone`, `registered`, `email_verified`, `can_login`, `deleted`,
 `status`)(SELECT `id`,
                  'ashley@ideabubble.ie',
                  '229ba51e5075433422204405fe508d94e4c1e55d8ad72cb9b89d59690e83180e',
                  'Ashley',
                  '',
                  'Europe/Dublin',
                  NOW(),
                  1,
                  1,
                  0,
                  1
           FROM `engine_project_role`
           WHERE `role` = 'Administrator');