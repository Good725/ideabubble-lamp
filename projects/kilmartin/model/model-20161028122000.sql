/*
ts:2016-10-28 12:20:00
*/

UPDATE `engine_settings` SET `value_dev` = 'kes1', `value_test` = 'kes1', `value_stage` = 'kes1' WHERE `variable` = 'template_folder_path';
UPDATE `engine_settings` SET `value_dev` = 'kes1', `value_test` = 'kes1', `value_stage` = 'kes1' WHERE `variable` = 'assets_folder_path';
UPDATE `engine_settings` SET `value_dev` = '0',    `value_test` = '0',    `value_stage` =  '0'   WHERE `variable` = 'use_config_file';


INSERT INTO `plugin_pages_layouts` (`layout`, `use_db_source`, `publish`, `deleted`, `date_created`, `date_modified`, `created_by`, `modified_by`) VALUES
(
  'contact',
  '0',
  '1',
  '0',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1)
);

/*add contact-us if not exists*/
INSERT INTO plugin_pages_pages
  (`name_tag`, `title`, `content`, `publish`, `deleted`, layout_id, category_id)
  (select 'contact-us', 'Contact Us', '', 1, 0, (select id from plugin_pages_layouts where layout='contact'), 1 FROM `plugin_pages_pages` WHERE `name_tag` IN ('contact-us.html', 'contact-us') AND `deleted` = 0 HAVING COUNT(*) = 0);

INSERT INTO `plugin_menus` (`category`, `title`, `link_tag`, `menu_order`, `publish`, `deleted`, `date_modified`, `date_entered`, `created_by`, `modified_by`) VALUES
(
  'Bars',
  'Contact Us',
  (SELECT IFNULL(`id`, 0) FROM `plugin_pages_pages` WHERE `name_tag` IN ('contact-us.html', 'contact-us') AND `deleted` = 0 LIMIT 1),
  '1',
  '1',
  '0',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1)
);

/*add additionalsubjects if not exists*/
INSERT INTO plugin_pages_pages
  (`name_tag`, `title`, `content`, `publish`, `deleted`, layout_id, category_id)
  (select 'additionalsubjects', 'Additional Subjects', '', 1, 0, (select id from plugin_pages_layouts where layout='content'), 1 FROM `plugin_pages_pages` WHERE `name_tag` IN ('additionalsubjects.html', 'additionalsubjects') AND `deleted` = 0 HAVING COUNT(*) = 0);

INSERT INTO `plugin_menus` (`category`, `title`, `link_tag`, `menu_order`, `publish`, `deleted`, `date_modified`, `date_entered`, `created_by`, `modified_by`) VALUES
(
  'Bars',
  'Subjects We Teach',
  (SELECT IFNULL(`id`, 0) FROM `plugin_pages_pages` WHERE `name_tag` IN ('additionalsubjects.html', 'additionalsubjects') AND `deleted` = 0 LIMIT 1),
  '2',
  '1',
  '0',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1)
);

/*add additional-services if not exists*/
INSERT INTO plugin_pages_pages
  (`name_tag`, `title`, `content`, `publish`, `deleted`, layout_id, category_id)
  (select 'additional-services', 'Additional Services', '', 1, 0, (select id from plugin_pages_layouts where layout='content'), 1 FROM `plugin_pages_pages` WHERE `name_tag` IN ('additional-services.html', 'additional-services') AND `deleted` = 0 HAVING COUNT(*) = 0);

INSERT INTO `plugin_menus` (`category`, `title`, `link_tag`, `menu_order`, `publish`, `deleted`, `date_modified`, `date_entered`, `created_by`, `modified_by`) VALUES
(
  'Bars',
  'Additional Services',
  (SELECT IFNULL(`id`, 0) FROM `plugin_pages_pages` WHERE `name_tag` IN ('additional-services.html', 'additional-services') AND `deleted` = 0 LIMIT 1),
  '3',
  '1',
  '0',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1)
);

UPDATE `plugin_media_shared_media_photo_presets`
SET
  `width_large`   = '210',
  `height_large`  = '160',
  `thumb`         = '0',
  `width_thumb`   = '',
  `height_thumb`  = '',
  `date_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1)
WHERE
  `title` = 'Courses';


UPDATE `plugin_media_shared_media_photo_presets`
SET
  `width_large`   = '1040',
  `height_large`  = '500',
  `thumb`         = '1',
  `width_thumb`   = '520',
  `height_thumb`  = '250',
  `date_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1)
WHERE
  `title` = 'Course Banners';

UPDATE `plugin_pages_pages`
SET
  `content`       = '<h2>Contact Us</h2>  <p>For enquiries regarding tuition/supervised study in Limerick &amp; Ennis please contact our Limerick office on <strong>061-444989</strong> or e-mail us at <a href=\"mailto:info@kes.ie\">info@kes.ie</a>.</p>  <p><span style=\"font-size:22px\">Make an Online Enquiry</span></p>',
  `layout_id`     = (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'contact' AND `deleted` = 0 LIMIT 1),
  `last_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1)
WHERE
  `name_tag` IN ('contact-us', 'contact-us.html');
