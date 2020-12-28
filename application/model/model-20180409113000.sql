/*
ts:2018-04-09 11:30:00
*/

SELECT `id` INTO @bc80_super_id FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1;

-- If "learn" and "info" menus exist, insert "learn" and "info" as actual items to their respective menus
INSERT INTO
  `plugin_menus` (`category`, `title`, `has_sub`, `menu_order`, `publish`, `deleted`, `date_modified`, `date_entered`, `created_by`, `modified_by`)
SELECT
  'learn', 'Learn', '1', '1', '1', '0', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, @bc80_super_id, @bc80_super_id
  FROM
    `plugin_menus`
  WHERE EXISTS
    (SELECT `id` FROM `plugin_menus` WHERE `category` = 'learn' AND `deleted` = 0)
  LIMIT 1
;

INSERT INTO
  `plugin_menus` (`category`, `title`, `has_sub`, `menu_order`, `publish`, `deleted`, `date_modified`, `date_entered`, `created_by`, `modified_by`)
SELECT
  'info', 'Info', '1', '1', '1', '0', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, @bc80_super_id, @bc80_super_id
  FROM
    `plugin_menus`
  WHERE EXISTS
    (SELECT `id` FROM `plugin_menus` WHERE `category` = 'info' AND `deleted` = 0)
  LIMIT 1
;

-- Update other items within the menus to be children of "learn" and "info"
UPDATE
  `plugin_menus`
SET
  `parent_id` = (SELECT `id` FROM (SELECT `id` FROM `plugin_menus` WHERE `title` = 'Learn' AND `category` = 'learn' AND `deleted` = 0 LIMIT 1) AS `m`)
WHERE
  `category` = 'learn' AND `parent_id` = 0 AND `deleted` = 0 AND `title` != 'Learn'
;

UPDATE
  `plugin_menus`
SET
  `parent_id` = (SELECT `id` FROM (SELECT `id` FROM `plugin_menus` WHERE `title` = 'Info' AND `category` = 'info' AND `deleted` = 0 LIMIT 1) AS `m`)
WHERE
  `category` = 'info' AND `parent_id` = 0 AND `deleted` = 0 AND `title` != 'Info'
;

-- Rename the menus to "header 1" and "header 2"
UPDATE `plugin_menus` SET `category` = 'header 1' WHERE `category` = 'learn' AND `deleted` = 0;
UPDATE `plugin_menus` SET `category` = 'header 2' WHERE `category` = 'info'  AND `deleted` = 0;
