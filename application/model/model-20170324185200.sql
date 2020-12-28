/*
ts:2017-03-24 18:52:00
*/
INSERT IGNORE INTO `engine_feeds` (`name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `short_tag`, `function_call`) VALUES
(
 'news feed',
 CURRENT_TIMESTAMP,
 CURRENT_TIMESTAMP,
 (SELECT `id` from `engine_users` where `email` = 'super@ideabubble.ie'),
 (SELECT `id` from `engine_users` where `email` = 'super@ideabubble.ie'),
 '1',
 '0',
 'newsfeed',
 'Model_News,get_plugin_items_front_end_feed'
 ),
 (
 'testimonials feed',
 CURRENT_TIMESTAMP,
 CURRENT_TIMESTAMP,
 (SELECT `id` from `engine_users` where `email` = 'super@ideabubble.ie'),
 (SELECT `id` from `engine_users` where `email` = 'super@ideabubble.ie'),
 '1',
 '0',
 'testimonialsfeed',
 'Model_Testimonials,get_plugin_items_front_end_feed'
 );
