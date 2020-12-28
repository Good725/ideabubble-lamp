/*
ts:2019-08-28 12:48:00
*/

INSERT IGNORE INTO `engine_users`
  (`role_id`, `email`, `password`, `name`, `surname`, `registered`, `email_verified`, `can_login`, `deleted`, `status`)
VALUES (
  (SELECT IFNULL(`id`, 2) FROM `engine_project_role` WHERE `role` = 'Administrator' AND `deleted` = 0),
  'nadia@ideabubble.ie',
  '572831a3917b93fdb84a6946e5bfcd012f67838eb08b9105d760b7a9e14e116a',
  'Nadia',
  '',
  CURRENT_TIMESTAMP(),
  1,
  1,
  0,
  1
);

-- Update nadia's password so same password is used platform wide in case user was manually added before with different password
UPDATE engine_users
set `password` = '572831a3917b93fdb84a6946e5bfcd012f67838eb08b9105d760b7a9e14e116a'
where `email` = 'nadia@ideabubble.ie';

-- Update liam's password platform wide
UPDATE engine_users
set `password` = '36fb1e2b468b59980ef208f42678168951727e15a99ffe44676f6ce59d9d3fc2'
where `email` = 'liam@ideabubble.ie';

-- Update all users that do not have a linked contact id and the emails match
UPDATE plugin_contacts3_contacts `contact`
    inner join plugin_contacts3_contact_has_notifications `email` on contact.notifications_group_id = email.group_id
    inner join plugin_contacts3_notifications `types` on email.notification_id = types.id
    inner join engine_users `users` on email.value = users.email
set contact.linked_user_id = users.id
where types.name = 'Email'
  and contact.linked_user_id = 0
  and contact.date_created between users.registered - INTERVAL 1 MINUTE and users.registered + INTERVAL 1 MINUTE;
