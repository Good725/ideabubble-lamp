/*
ts:2019-04-16 08:49:21
*/

INSERT INTO `plugin_pages_layouts` (`layout`, `template_id`, `use_db_source`, `publish`, `deleted`, `date_created`, `date_modified`, `created_by`, `modified_by`)
VALUES ('home_page_content_top', (SELECT `id`  FROM `engine_site_templates` WHERE `stub` = '04' LIMIT 1), '0', '1', '0',  CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1), (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1));;

INSERT INTO `plugin_pages_layouts` (`layout`, `template_id`, `use_db_source`, `publish`, `deleted`, `date_created`, `date_modified`, `created_by`, `modified_by`)
VALUES ('home_page_content_above', (SELECT `id`  FROM `engine_site_templates` WHERE `stub` = '04' LIMIT 1), '0', '1', '0',  CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1), (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1));;
