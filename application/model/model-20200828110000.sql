/*
ts:2020-08-28 11:00:00
*/

-- Add accounts for new team members.
INSERT IGNORE INTO `engine_users` (`role_id`, `email`, `password`, `name`, `surname`, `timezone`, `registered`, `email_verified`, `can_login`, `deleted`, `status`)
(SELECT `id`, 'natalya@ideabubble.ie',      '56a4536f0f629c017506d725638dbe873f70104645a8e65595ba4b9b34a1f2ab', 'Natalya',   'Sidun', 'Europe/Dublin', CURRENT_TIMESTAMP, 1, 1, 0, 1 FROM `engine_project_role` WHERE `role` = 'Administrator');

INSERT IGNORE INTO `engine_users` (`role_id`, `email`, `password`, `name`, `surname`, `timezone`, `registered`, `email_verified`, `can_login`, `deleted`, `status`)
(SELECT `id`, 'annmarie.walsh@courseco.co', 'aa99a97136412fcea4aa1413fe550edfbe6b4f0838917217a27b8acf523e4ce2', 'Ann Marie', 'Walsh', 'Europe/Dublin', CURRENT_TIMESTAMP, 1, 1, 0, 1 FROM `engine_project_role` WHERE `role` = 'Administrator');

INSERT IGNORE INTO `engine_users` (`role_id`, `email`, `password`, `name`, `surname`, `timezone`, `registered`, `email_verified`, `can_login`, `deleted`, `status`)
(SELECT `id`, 'eimear.mahon@courseco.co',   'e25808d53efcfe7b3af007bcd68fe6861d9dc1a5f0421e173666c29847d7421e', 'Eimear',    'Mahon', 'Europe/Dublin', CURRENT_TIMESTAMP, 1, 1, 0, 1 FROM `engine_project_role` WHERE `role` = 'Administrator');

-- De-activate old accounts and wipe stored passwords.
UPDATE
  `engine_users`
SET
  `email`     = CONCAT(`email`, '-disabled'),
  `password`  = '!disabled',
  `can_login` = 0,
  `deleted`   = 1
WHERE
  `email` IN (
        'liam@ideabubble.ie',
       'nadia@ideabubble.ie',
      'ashley@ideabubble.ie',
      'bassam@ideabubble.ie',
      'rafael@ideabubble.ie',
         'ali@ideabubble.ie',
      'kostya@ideabubble.ie'
  );
 