/*
ts:2019-02-28 12:00:00
*/

INSERT INTO `engine_settings`
  (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`)
VALUES
(
  'home_page_feed_1',
  'Home page feed 1',
  'news',
  'news',
  'news',
  'news',
  'news',
  'What content should be shown in the first feed on the home page.',
  'dropdown',
  'Website',
  '{"none":"None","news":"News","testimonials":"Testimonials"}'
);

INSERT INTO `engine_settings`
  (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`)
VALUES
(
  'home_page_feed_2',
  'Home page feed 2',
  'none',
  'none',
  'none',
  'none',
  'none',
  'What content should be shown in the second feed on the home page.',
  'dropdown',
  'Website',
  '{"none":"None","news":"News","testimonials":"Testimonials"}'
);

-- Update the value of "home_page_feed_1" to suit the value that was selected for "home_page_news_feed"
UPDATE
  `engine_settings`
SET
  `value_dev`   = IF ((SELECT `value_dev`   FROM (SELECT * FROM `engine_settings`) `settings_temp` WHERE `variable` = 'home_page_news_feed') = 0, 'none', 'news'),
  `value_test`  = IF ((SELECT `value_test`  FROM (SELECT * FROM `engine_settings`) `settings_temp` WHERE `variable` = 'home_page_news_feed') = 0, 'none', 'news'),
  `value_stage` = IF ((SELECT `value_stage` FROM (SELECT * FROM `engine_settings`) `settings_temp` WHERE `variable` = 'home_page_news_feed') = 0, 'none', 'news'),
  `value_live`  = IF ((SELECT `value_live`  FROM (SELECT * FROM `engine_settings`) `settings_temp` WHERE `variable` = 'home_page_news_feed') = 0, 'none', 'news')
WHERE
  `variable` = 'home_page_feed_1'
;

-- Previous setting is now obsolete
DELETE FROM `engine_settings` WHERE `variable` = 'home_page_news_feed';

