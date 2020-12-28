/*
ts:2020-03-25 17:31:00
*/

INSERT IGNORE INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, `parent_controller`)
VALUES (1, 'family_tab', 'User / Profile / Family Tab', 'Show/Hide family tab for students',
        (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'user'));