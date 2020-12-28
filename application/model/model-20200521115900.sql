/*
ts:2020-05-21 11:59:00
*/

INSERT IGNORE INTO `engine_feeds` (`name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `short_tag`, `function_call`) VALUES
(
 'audio',
 CURRENT_TIMESTAMP,
 CURRENT_TIMESTAMP,
 (SELECT `id` from `engine_users` where `email` = 'super@ideabubble.ie'),
 (SELECT `id` from `engine_users` where `email` = 'super@ideabubble.ie'),
 '1',
 '0',
 'audio',
 'IbHelpers,render_audio'
);
