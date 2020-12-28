/*
ts:2019-11-05 08:59:00
*/

INSERT IGNORE INTO `engine_users`
(`role_id`, `email`, `password`, `name`, `surname`, `timezone`, `registered`, `email_verified`, `can_login`, `deleted`,
 `status`)
    (SELECT `id`,
            'slava@ideabubble.ie',
            '4b7ba764c32af50348c6c907c993dbc1fed76ab05a4079d4c104c105c8e9b419',
            'Slava',
            '',
            'Europe/Dublin',
            NOW(),
            1,
            1,
            0,
            1
     FROM `engine_project_role`
     WHERE `role` = 'Super User');

UPDATE IGNORE `engine_users`
SET `email`    = 'kamal@ideabubble.ie'
WHERE (`email` = 'kamal@ideabubble.ie-disabled');

UPDATE `engine_users`
SET `role_id` = (select id from `engine_project_role` where `role` = 'Administrator'),
    `password` = '99e60756f24383fc42e9c41d56b2083e91df81f62b0457892fdd77f89e9efbc6'
WHERE (`email` = 'kamal@ideabubble.ie');


UPDATE `engine_users`
SET `role_id`  = (select id from `engine_project_role` where `role` = 'Administrator')
WHERE (`email` = 'slava@ideabubble.ie');