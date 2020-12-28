/*
ts:2015-01-01 00:01:01
*/

INSERT IGNORE INTO `plugin_news_categories` (`category`) VALUES ('Upcoming Events');

INSERT IGNORE INTO `feeds` (`name`, `short_tag`, `function_call`, `publish`) VALUES ('Upcoming Events', 'events', 'Model_News,events_feed', '1');

INSERT IGNORE INTO `plugin_panels` (`title`, `position`, `type_id`, `image`, `text`, `publish`)
VALUES ('Upcoming Events', 'content_left', '2', '0', '<div class=\"upcoming-events-wrapper\">\n<h2>Upcoming Events</h2>\n<div>{events-}</div>\n</div>\n\n', '1');

