/*
ts:2017-11-09 18:01:00
*/

UPDATE
  `engine_settings`
SET
  `value_dev`   = '0',
  `value_test`  = '0',
  `value_stage` = '0',
  `value_live`  = '0'
WHERE
  `variable` = 'use_config_file'
;

UPDATE
  `engine_settings`
SET
  `value_dev`   = '03',
  `value_test`  = '03',
  `value_stage` = '03',
  `value_live`  = '03'
WHERE
  `variable` = 'template_folder_path'
;

UPDATE
  `engine_settings`
SET
  `value_dev`   = '21',
  `value_test`  = '21',
  `value_stage` = '21',
  `value_live`  = '21'
WHERE
  `variable` = 'assets_folder_path'
;

UPDATE
  `engine_settings`
SET
  `value_dev`   = '061 378116',
  `value_test`  = '061 378116',
  `value_stage` = '061 378116',
  `value_live`  = '061 378116'
WHERE
  `variable` = 'telephone'
;

UPDATE
  `engine_settings`
SET
  `value_dev`   = 'sales@culleninsurances.ie',
  `value_test`  = 'sales@culleninsurances.ie',
  `value_stage` = 'sales@culleninsurances.ie',
  `value_live`  = 'sales@culleninsurances.ie'
WHERE
  `variable` = 'email'
;

INSERT INTO
  `plugin_media_shared_media_photo_presets` (`title`, `directory`, `height_large`, `width_large`, `action_large`, `thumb`, `height_thumb`, `width_thumb`, `action_thumb`, `date_created`, `date_modified`, `publish`, `deleted`)
VALUES
  ('Banner', 'banners', '364', '1490', 'fit', '1', '91', '373', 'fit', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '1', '0');

INSERT INTO
  `plugin_media_shared_media_photo_presets` (`title`, `directory`, `height_large`, `width_large`, `action_large`, `thumb`, `height_thumb`, `width_thumb`, `action_thumb`, `date_created`, `date_modified`, `publish`, `deleted`)
VALUES
  ('Partner icon', 'menus', '300', '300', 'fit', '1', '150', '150', 'fit', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '1', '0');

INSERT INTO
  `plugin_media_shared_media_photo_presets` (`title`, `directory`, `height_large`, `width_large`, `action_large`, `thumb`, `height_thumb`, `width_thumb`, `action_thumb`, `date_created`, `date_modified`, `publish`, `deleted`)
VALUES
  ('Panel', 'panels', '207', '286', 'fit', '0', '', '', 'fit', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '1', '0');

INSERT INTO
  `plugin_media_shared_media_photo_presets` (`title`, `directory`, `height_large`, `width_large`, `action_large`, `thumb`, `height_thumb`, `width_thumb`, `action_thumb`, `date_created`, `date_modified`, `publish`, `deleted`)
VALUES
  ('News', 'news', '720', '1280', 'fit', '1', '180', '320', 'fit', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '1', '0');

INSERT INTO
  `plugin_media_shared_media_photo_presets` (`title`, `directory`, `height_large`, `width_large`, `action_large`, `thumb`, `height_thumb`, `width_thumb`, `action_thumb`, `date_created`, `date_modified`, `publish`, `deleted`)
VALUES
  ('Feature panels', 'panels', '150', '150', 'fit', '0', '', '', 'fit', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '1', '0');

UPDATE
  `plugin_pages_pages`
SET
  `content`='<h2>Welcome to Cullen Insurances</h2>
\n<p>Cullen Insurances provides a comprehensive insurance service to small businesses, sole traders and personal clients and has built up a large portfolio of clients nationally by word of mouth from it&#39;s huge base of satisfied clients.<br />
\nCullen Insurances was established in 1974 by Bill Cullen who returned to the family business in Newport after training and working in various branches of the Insurance industry in Dublin.</p>
\n
\n<p>The family have been providing business services to the people of Newport and surrounding areas since Bill&#39;s great-grandfather established a general store in 1863.<br />
\nWe pride ourselves in the level of expertise and friendliness of all our staff who have acquired vast knowledge and experience.</p>
\n
\n<p><a class=\"button\" href=\"/get-a-quote\">Get a Quote</a></p>',
  `last_modified` = CURRENT_TIMESTAMP,
  `modified_by` = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1)
WHERE
  `name_tag` IN ('home', 'home.html')
;

INSERT INTO
  `plugin_testimonials` (`category_id`, `title`, `summary`, `item_signature`, `item_company`, `content`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`)
VALUES (
  (SELECT IFNULL(`id`, '') FROM `plugin_testimonials_categories` WHERE `category` = 'Testimonials' LIMIT 1),
  'Age',
  'Your help was invaluable to me as I was unable to get commercial insurance due to my age , but...',
  'Bobby Byrnes',
  'Chairman and CEO', '<p>Your help was invaluable to me as I was unable to get commercial insurance due to my age , but am on the road now thanks to Cullens.</p>',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  '1',
  '0'
);
