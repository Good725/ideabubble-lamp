/*
ts:2018-02-06 18:00:00
*/

-- Setting for a second button in the CMS header
INSERT INTO `engine_settings` (`variable`, `name`, `note`, `type`, `group`) VALUES
('cms_heading_button_link_2', 'Action button link 2', 'Add an extra button in the CMS header, which links to this location', 'text', 'Engine'),
('cms_heading_button_text_2', 'Action button text 2', 'The text to display in the extra button in the CMS header',           'text', 'Engine');

-- Add permissions
INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, parent_controller) VALUES
(
  1,
  'cms_action_button_1',
  'Settings / Action Button 1',
  'Settings Action Button 1',
  (SELECT `id` FROM `engine_resources` `x` WHERE `alias` = 'settings')
),
(
  1,
  'cms_action_button_2',
  'Settings / Action Button 2',
  'Settings Action Button 2',
  (SELECT `id` FROM `engine_resources` `x` WHERE `alias` = 'settings')
);


-- Grant the permissions to user groups

SELECT `id` FROM `engine_resources` WHERE `alias` = 'cms_action_button_1' INTO @kes_3952_button_1_permission;

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
  SELECT `id`, @kes_3952_button_1_permission
  FROM `engine_project_role` `role`
  WHERE `role` IN ('Administrator', 'External User', 'Manager', 'Receptionist', 'Super User');

SELECT `id` FROM `engine_resources` WHERE `alias` = 'cms_action_button_2' INTO @kes_3952_button_2_permission;

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
  SELECT `id`, @kes_3952_button_2_permission
  FROM `engine_project_role` `role`
  WHERE `role` IN ('Mature Student', 'Parent/Guardian', 'Student');


UPDATE `engine_users` SET `default_home_page` = NULL WHERE `default_home_page` = '/dashboard.html';
