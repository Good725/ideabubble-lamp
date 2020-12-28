/*
ts:2020-08-31 15:00:00
*/

-- Add permissions for tabs on the contacts editor
INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, `parent_controller`)
VALUES (
  1,
  'contacts3_applications',
  'Contacts3 / Applications',
  'Access the applications tab in the contact editor',
  (SELECT `id` FROM `engine_resources` `r` WHERE `r`.`alias` = 'contacts3' LIMIT 1)
), (
  1,
  'contacts3_timetable',
  'Contacts3 / Timetable',
  'Access the timetable tab in the contact editor',
  (SELECT `id` FROM `engine_resources` `r` WHERE `r`.`alias` = 'contacts3' LIMIT 1)
), (
  1,
  'contacts3_attendance',
  'Contacts3 / Attendance',
  'Access the attendance tab in the contact editor',
  (SELECT `id` FROM `engine_resources` `r` WHERE `r`.`alias` = 'contacts3' LIMIT 1)
), (
  1,
  'contacts3_documents',
  'Contacts3 / Documents',
  'Access the documents tab in the contact editor',
  (SELECT `id` FROM `engine_resources` `r` WHERE `r`.`alias` = 'contacts3' LIMIT 1)
), (
  1,
  'contacts3_tasks',
  'Contacts3 / Tasks',
  'Access the tasks (todos) tab in the contact editor',
  (SELECT `id` FROM `engine_resources` `r` WHERE `r`.`alias` = 'contacts3' LIMIT 1)
), (
  1,
  'contacts3_notes',
  'Contacts3 / Notes',
  'Access the notes field and tab in the contact editor',
  (SELECT `id` FROM `engine_resources` `r` WHERE `r`.`alias` = 'contacts3' LIMIT 1)
), (
  1,
  'contacts3_messages',
  'Contacts3 / Messages',
  'Access the messages tab in the contact editor',
  (SELECT `id` FROM `engine_resources` `r` WHERE `r`.`alias` = 'contacts3' LIMIT 1)
), (
  1,
  'contacts3_activities',
  'Contacts3 / Activities',
  'Access the activities tab in the contact editor',
  (SELECT `id` FROM `engine_resources` `r` WHERE `r`.`alias` = 'contacts3' LIMIT 1)
);

-- Enable the permissions for administrators.
INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
  SELECT
    (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Administrator'),
    `id`
  FROM
    `engine_resources`
  WHERE
    `alias` in ('contacts3_applications', 'contacts3_timetable', 'contacts3_attendance', 'contacts3_documents', 'contacts3_tasks', 'contacts3_notes', 'contacts3_messages', 'contacts3_activities')
;

-- Add permission for actions menu in the contacts editor
INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, `parent_controller`)
VALUES (
  1,
  'contacts3_actions_menu',
  'Contacts3 / Actions menu',
  'Access the actions menu in the contact editor',
  (SELECT `id` FROM `engine_resources` `r` WHERE `r`.`alias` = 'contacts3' LIMIT 1)
);

-- Add permission for actions menu in the contacts editor
INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, `parent_controller`)
VALUES (
  1,
  'contacts3_tab_actions_menu',
  'Contacts3 / Tab actions menu',
  'Access the actions menu in the tabs within the contact editor',
  (SELECT `id` FROM `engine_resources` `r` WHERE `r`.`alias` = 'contacts3' LIMIT 1)
);


-- Enable the permissions for administrators.
INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
  SELECT
    (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Administrator'),
    `id`
  FROM
    `engine_resources`
  WHERE
    `alias` in ('contacts3_actions_menu');

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
  SELECT
    (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Administrator'),
    `id`
  FROM
    `engine_resources`
  WHERE
    `alias` in ('contacts3_tab_actions_menu');

-- Move the "show accounts tab bookings" permission to "contacts3"
UPDATE
 `engine_resources`
SET
  `parent_controller` = (SELECT `id` FROM (SELECT `id` FROM `engine_resources` `r1` WHERE `r1`.`alias` = 'contacts3' LIMIT 1) `r2`),
  `name` = 'Contacts3 / Show accounts data'
WHERE
  `alias` = 'show_accounts_tab_bookings';
