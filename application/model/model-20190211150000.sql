/*
ts:2019-02-11 15:00:00
*/

UPDATE
  `engine_users`
SET
  `email`     = CONCAT(`email`, '-disabled'),
  `password`  = '!disabled',
  `can_login` = 0,
  `deleted`   = 1
WHERE
  `email` IN (
        'yann@ideabubble.ie',
        'ayaz@ideabubble.ie',
      'gevorg@ideabubble.ie',
       'rabin@ideabubble.ie',
     'valeriy@ideabubble.ie',
    'himanshu@ideabubble.ie',
       'kamal@ideabubble.ie',
       'serge@ideabubble.ie',
     'jasmine@ideabubble.ie',
        'alex@ideabubble.ie',
       'peter@ideabubble.ie',
      'alexey@ideabubble.ie',
        'john@ideabubble.ie',
       'sasha@ideabubble.ie',
        'aman@ideabubble.ie',
       'davit@ideabubble.ie',
       'taron@ideabubble.ie',
        'jack@ideabubble.ie',
      'rupali@ideabubble.ie',
        'dave@ideabubble.ie',
    'alexandr@ideabubble.ie',
         'fei@ideabubble.ie'
  )
;

INSERT IGNORE INTO `engine_users`
  (`role_id`, `email`, `password`, `name`, `surname`, `registered`, `email_verified`, `can_login`, `deleted`, `status`)
VALUES (
  (SELECT IFNULL(`id`, 2) FROM `engine_project_role` WHERE `role` = 'Administrator' AND `deleted` = 0),
  'cormac@ideabubble.ie',
  'b1b41043c26e9d8458531e46bcd92d0f851fa6659d700d067ce723a0572b422d',
  'Cormac',
  'Gudge',
  CURRENT_TIMESTAMP(),
  1,
  1,
  0,
  1
);


INSERT IGNORE INTO `engine_users`
  (`role_id`, `email`, `password`, `name`, `surname`, `registered`, `email_verified`, `can_login`, `deleted`, `status`)
VALUES (
  (SELECT IFNULL(`id`, 2) FROM `engine_project_role` WHERE `role` = 'Administrator' AND `deleted` = 0),
  'adham@ideabubble.ie',
  'f69f9bb0e3013d947496c4f44ff60a3f63ed43ff439b5c061fb1ce11be077071',
  'Adham',
  'Salem',
  CURRENT_TIMESTAMP(),
  1,
  1,
  0,
  1
);