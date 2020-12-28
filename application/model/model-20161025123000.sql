/*
ts:2016-10-25 12:30:00
*/

INSERT IGNORE INTO `engine_feeds` (`name`, `summary`, `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`, `deleted`, `short_tag`, `function_call`) VALUES
(
  'Twitter API Feed',
  '',
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  CURRENT_TIMESTAMP(),
  CURRENT_TIMESTAMP(),
  '1',
  '0',
  'twitter_api_feed',
  'IbTwitterApi,embed_feed'
);
