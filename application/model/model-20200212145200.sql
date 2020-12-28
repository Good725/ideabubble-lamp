/*
ts:2020-02-12 14:52:00
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
        'slava@ideabubble.ie',
        'maja@ideabubble.ie',
      'cormac@ideabubble.ie',
       'rowan@ideabubble.ie',
     'engine@ideabubble.ie',
    'testscript@ideabubble.ie',
       'super@ideabubble.ie',
       'fanni@ideabubble.ie'
  );