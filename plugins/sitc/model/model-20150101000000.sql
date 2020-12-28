/*
ts:2015-01-01 00:00:00
*/

-- ----------------------------
-- IBCMS-250 Stock in the Channel Integration (SITC) (back-end part)
-- ----------------------------

INSERT IGNORE INTO `plugins` (
  `id` ,
  `name` ,
  `friendly_name` ,
  `show_on_dashboard` ,
  `requires_media` ,
  `media_folder` ,
  `icon` ,
  `order`
)
VALUES (NULL , 'sict', 'SICT', '1', '0', NULL , NULL , NULL);

INSERT IGNORE INTO `settings` (
  `id` ,
  `variable` ,
  `name` ,
  `value_live` ,
  `value_stage` ,
  `value_test` ,
  `value_dev` ,
  `default` ,
  `location` ,
  `note` ,
  `type` ,
  `group` ,
  `required` ,
  `options`
)
VALUES (
  NULL , 'sict_ftp', 'FTP', NULL , NULL , NULL , NULL , NULL , 'both', 'FTP URL', 'text', 'SICT', '0', ''
), (
  NULL , 'sict_user_name', 'User Name', NULL , NULL , NULL , NULL , NULL , 'both', 'User Name for FTP', 'text', 'SICT', '0', ''
), (
  NULL , 'sict_password', 'Password', NULL , NULL , NULL , NULL , NULL , 'both', 'Password for FTP', 'text', 'SICT', '0', ''
);

UPDATE `plugins` SET `name` = 'sitc', `friendly_name` = 'SITC' WHERE `plugins`.`name` = 'sict';
UPDATE `settings` SET `group` = 'SITC' WHERE `settings`.`group` = 'SICT';

-- ----------------------------------------------------
-- IBIS-219, IBCMS-265 - Missing icons IBIS Inactive features
-- ----------------------------------------------------
UPDATE `plugins` SET `icon` = 'products.png' WHERE `name` = 'sitc';

