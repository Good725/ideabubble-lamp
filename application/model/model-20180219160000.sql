/*
ts:2018-02-19 16:00:00
*/
INSERT INTO `engine_users`
  (`role_id`, `email`, `password`, `name`, `surname`, `timezone`, `registered`, `email_verified`, `can_login`, `deleted`, `status`)
VALUES (
  (SELECT IFNULL(`id`, 1) FROM `engine_project_role` WHERE `role` = 'Super User' AND `deleted` = 0),
  'mary@ideabubble.ie',
  '1d1d820c949b0d754649eb4f71acc54a481f542b8b1391e8a73029e9bb9a9410',
  'Mary',
  '',
  'Europe/Dublin',
  CURRENT_TIMESTAMP(),
  1,
  1,
  0,
  1
);

UPDATE
  `engine_users`
SET
  `can_login` = 0,
  `deleted`   = 1
WHERE
  `email` IN (
    'alexey@ideabubble.ie',
    'aman@ideabubble.ie',
    'davit@ideabubble.ie',
    'himanshu@ideabubble.ie',
    'jack@ideabubble.ie',
    'john@ideabubble.ie',
    'peter@ideabubble.ie',
    'rupali@ideabubble.ie',
    'sasha@ideabubble.ie',
    'taron@ideabubble.ie'
  );