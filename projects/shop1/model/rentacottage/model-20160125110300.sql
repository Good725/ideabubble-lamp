/*
ts:2016-01-25 11:03:00
*/

UPDATE IGNORE `settings` SET `value_dev` = 0, `value_test` = 0, `value_stage` = 0, `value_live` = 0
WHERE `variable` = 'use_config_file';

UPDATE IGNORE `settings` SET `value_dev` = 'accommodation', `value_test` = 'accommodation', `value_stage` = 'accommodation', `value_live` = 'accommodation'
WHERE `variable` = 'template_folder_path';

UPDATE IGNORE `settings` SET `value_dev` = '23', `value_test` = '23', `value_stage` = '23', `value_live` = '23'
WHERE `variable` = 'assets_folder_path';

INSERT IGNORE INTO `plugin_pages_layouts` (`layout`, `date_created`, `date_modified`, `created_by`, `modified_by`)
SELECT 'searchresults', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, `id`, `id` FROM `users` WHERE `email` = 'super@ideabubble.ie';

INSERT IGNORE INTO `plugin_pages_layouts` (`layout`, `date_created`, `date_modified`, `created_by`, `modified_by`)
SELECT 'propertydetails', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, `id`, `id` FROM `users` WHERE `email` = 'super@ideabubble.ie';

INSERT IGNORE INTO `plugin_pages_layouts` (`layout`, `date_created`, `date_modified`, `created_by`, `modified_by`)
SELECT 'bookingpage', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, `id`, `id` FROM `users` WHERE `email` = 'super@ideabubble.ie';


INSERT INTO `plugin_pages_pages` (`name_tag`, `title`, `date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`)
SELECT 'search-results.html', 'Results', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, `users`.`id`, `users`.`id`, '1', '0', '1', `layout`.`id`, `category`.`id`
FROM `plugin_pages_layouts` `layout`
LEFT JOIN `plugin_pages_categorys` `category` ON `category`.`category` = 'Default'
LEFT JOIN `users` ON `users`.`email` = 'super@ideabubble.ie'
WHERE `layout`.`layout` = 'searchresults';

INSERT INTO `plugin_pages_pages` (`name_tag`, `title`, `date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`)
SELECT 'property-details.html', 'Details', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, `users`.`id`, `users`.`id`, '1', '0', '1', `layout`.`id`, `category`.`id`
FROM `plugin_pages_layouts` `layout`
LEFT JOIN `plugin_pages_categorys` `category` ON `category`.`category` = 'Default'
LEFT JOIN `users` ON `users`.`email` = 'super@ideabubble.ie'
WHERE `layout`.`layout` = 'propertydetails';

INSERT INTO `plugin_pages_pages` (`name_tag`, `title`, `date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`)
SELECT 'booking.html', 'Booking', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, `users`.`id`, `users`.`id`, '1', '0', '1', `layout`.`id`, `category`.`id`
FROM `plugin_pages_layouts` `layout`
LEFT JOIN `plugin_pages_categorys` `category` ON `category`.`category` = 'Default'
LEFT JOIN `users` ON `users`.`email` = 'super@ideabubble.ie'
WHERE `layout`.`layout` = 'bookingpage';

