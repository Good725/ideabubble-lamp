/*
ts:2019-12-05 08:22:00
*/

INSERT IGNORE INTO `engine_users`
  (`role_id`, `email`, `password`, `name`, `surname`, `timezone`, `registered`, `email_verified`, `can_login`, `deleted`, `status`)
  (SELECT `id`,
                  'trainer@courseco.co',
                  'b5a6123dbe6a28b25a68566c03194898ecccfc14d527c5bb3f14c7d96da7dd01',
                  'Trainer',
                  'Trainer',
                  'Europe/Dublin',
                  NOW(),
                  1,
                  1,
                  0,
                  1
           FROM `engine_project_role`
           WHERE `role` = 'Teacher');

INSERT IGNORE INTO `engine_users`
  (`role_id`, `email`, `password`, `name`, `surname`, `timezone`, `registered`, `email_verified`, `can_login`, `deleted`, `status`)
  (SELECT `id`,
                  'student@courseco.co',
                  '265229d73256167f894797824351a080d752718d8a84c9e67b92e6d792dc7400',
                  'Trainer',
                  'Trainer',
                  'Europe/Dublin',
                  NOW(),
                  1,
                  1,
                  0,
                  1
           FROM `engine_project_role`
           WHERE `role` = 'Student');

INSERT IGNORE INTO `engine_users`
  (`role_id`, `email`, `password`, `name`, `surname`, `timezone`, `registered`, `email_verified`, `can_login`, `deleted`, `status`)
  (SELECT `id`,
                  'parent@courseco.co',
                  '7c2f02db1c821db8fb943cc73462d457567ed00caf277fef56cef6053b966198',
                  'Trainer',
                  'Trainer',
                  'Europe/Dublin',
                  NOW(),
                  1,
                  1,
                  0,
                  1
           FROM `engine_project_role`
           WHERE `role` = 'Parent/Guardian');

INSERT INTO `plugin_contacts3_family` (`family_name`) VALUES ('Sample');

INSERT INTO `plugin_contacts3_contacts` (`type`, `first_name`, `last_name`, `family_id`, linked_user_id) VALUES (5, 'Parent', 'Sample', (select family_id from plugin_contacts3_family where family_name='Sample' order by family_id desc limit 1), (select id from engine_users where email='parent@courseco.co'));
INSERT INTO plugin_contacts3_contact_has_roles (contact_id, role_id) VALUES ((select id from plugin_contacts3_contacts where first_name='Parent' and last_name='Sample' order by id desc limit 1), 1);
INSERT INTO `plugin_contacts3_contacts` (`type`, `first_name`, `last_name`, `family_id`, linked_user_id) VALUES (5, 'Student', 'Sample', (select family_id from plugin_contacts3_family where family_name='Sample' order by family_id desc limit 1), (select id from engine_users where email='student@courseco.co'));
INSERT INTO plugin_contacts3_contact_has_roles (contact_id, role_id) VALUES ((select id from plugin_contacts3_contacts where first_name='Student' and last_name='Sample' order by id desc limit 1), 2);
