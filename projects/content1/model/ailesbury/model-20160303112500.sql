/*
ts:2016-03-03 11:25:00
*/
INSERT IGNORE INTO `engine_feeds` (`name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `short_tag`, `function_call`) VALUES
(
  'news feed',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie'),
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie'),
  '1',
  '0',
  'newsfeed',
  'Model_News,get_plugin_items_front_end_feed'
),
(
  'testimonials feed',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie'),
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie'),
  '1',
  '0',
  'testimonialsfeed',
  'Model_Testimonials,get_plugin_items_front_end_feed'
);
