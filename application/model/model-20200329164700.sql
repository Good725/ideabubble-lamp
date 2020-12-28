/*
ts:2020-03-29 16:48:00
*/

INSERT IGNORE INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, `parent_controller`)
VALUES (1, 'enable_layout_add', 'Add Layout Enabled', 'enabling to add layout',
        (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'pages'));

INSERT IGNORE INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, `parent_controller`)
VALUES (1, 'enable_newsletter_add', 'Add Newsletter Enabled', 'enabling to add newsletter',
        (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'pages'));