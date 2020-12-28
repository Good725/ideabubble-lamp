/*
ts:2016-10-25 12:31:00
*/

INSERT IGNORE INTO `engine_localisation_messages` (`message`, `created_on`, `updated_on`) VALUES
('<strong>Tweets</strong> by', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Retweet',   CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Follow',    CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Following', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

INSERT IGNORE INTO `engine_localisation_translations` (`language_id`, `message_id`, `translation`) VALUES (
  (SELECT `id` FROM `engine_localisation_languages` WHERE `code` = 'ga'),
  (SELECT `id` FROM `engine_localisation_messages` WHERE `message` = '<strong>Tweets</strong> by'),
  '<strong>Tweetanna</strong> le'
);

INSERT IGNORE INTO `engine_localisation_translations` (`language_id`, `message_id`, `translation`) VALUES (
  (SELECT `id` FROM `engine_localisation_languages` WHERE `code` = 'ga'),
  (SELECT `id` FROM `engine_localisation_messages` WHERE `message` = 'Retweet'),
  'Atweetáil'
);

INSERT IGNORE INTO `engine_localisation_translations` (`language_id`, `message_id`, `translation`) VALUES (
  (SELECT `id` FROM `engine_localisation_languages` WHERE `code` = 'en'),
  (SELECT `id` FROM `engine_localisation_messages` WHERE `message` = 'Retweet'),
  'Retweet'
);

INSERT IGNORE INTO `engine_localisation_translations` (`language_id`, `message_id`, `translation`) VALUES (
  (SELECT `id` FROM `engine_localisation_languages` WHERE `code` = 'ga'),
  (SELECT `id` FROM `engine_localisation_messages` WHERE `message` = 'Follow'),
  'Lean'
);

INSERT IGNORE INTO `engine_localisation_translations` (`language_id`, `message_id`, `translation`) VALUES (
  (SELECT `id` FROM `engine_localisation_languages` WHERE `code` = 'en'),
  (SELECT `id` FROM `engine_localisation_messages` WHERE `message` = 'Follow'),
  'Follow'
);

INSERT IGNORE INTO `engine_localisation_translations` (`language_id`, `message_id`, `translation`) VALUES (
  (SELECT `id` FROM `engine_localisation_languages` WHERE `code` = 'ga'),
  (SELECT `id` FROM `engine_localisation_messages` WHERE `message` = 'Following'),
  'Á Leanúint'
);

INSERT IGNORE INTO `engine_localisation_translations` (`language_id`, `message_id`, `translation`) VALUES (
  (SELECT `id` FROM `engine_localisation_languages` WHERE `code` = 'en'),
  (SELECT `id` FROM `engine_localisation_messages` WHERE `message` = 'Following'),
  'Following'
);


INSERT IGNORE INTO `engine_localisation_messages` (`message`, `created_on`, `updated_on`) VALUES
('Jan', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Feb', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Mar', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Apr', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('May', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Jun', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Jul', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Aug', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Sep', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Oct', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Nov', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Dec', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);


INSERT IGNORE INTO `engine_localisation_translations` (`language_id`, `message_id`, `translation`) VALUES (
  (SELECT `id` FROM `engine_localisation_languages` WHERE `code` = 'ga'),
  (SELECT `id` FROM `engine_localisation_messages` WHERE `message` = 'Jan'),
  'Ean'
);

INSERT IGNORE INTO `engine_localisation_translations` (`language_id`, `message_id`, `translation`) VALUES (
  (SELECT `id` FROM `engine_localisation_languages` WHERE `code` = 'en'),
  (SELECT `id` FROM `engine_localisation_messages` WHERE `message` = 'Jan'),
  'Jan'
);

INSERT IGNORE INTO `engine_localisation_translations` (`language_id`, `message_id`, `translation`) VALUES (
  (SELECT `id` FROM `engine_localisation_languages` WHERE `code` = 'en'),
  (SELECT `id` FROM `engine_localisation_messages` WHERE `message` = 'Feb'),
  'Feb'
);

INSERT IGNORE INTO `engine_localisation_translations` (`language_id`, `message_id`, `translation`) VALUES (
  (SELECT `id` FROM `engine_localisation_languages` WHERE `code` = 'ga'),
  (SELECT `id` FROM `engine_localisation_messages` WHERE `message` = 'Feb'),
  'Fea'
);

INSERT IGNORE INTO `engine_localisation_translations` (`language_id`, `message_id`, `translation`) VALUES (
  (SELECT `id` FROM `engine_localisation_languages` WHERE `code` = 'en'),
  (SELECT `id` FROM `engine_localisation_messages` WHERE `message` = 'Mar'),
  'Mar'
);

INSERT IGNORE INTO `engine_localisation_translations` (`language_id`, `message_id`, `translation`) VALUES (
  (SELECT `id` FROM `engine_localisation_languages` WHERE `code` = 'ga'),
  (SELECT `id` FROM `engine_localisation_messages` WHERE `message` = 'Mar'),
  'Már'
);

INSERT IGNORE INTO `engine_localisation_translations` (`language_id`, `message_id`, `translation`) VALUES (
  (SELECT `id` FROM `engine_localisation_languages` WHERE `code` = 'en'),
  (SELECT `id` FROM `engine_localisation_messages` WHERE `message` = 'Apr'),
  'Apr'
);

INSERT IGNORE INTO `engine_localisation_translations` (`language_id`, `message_id`, `translation`) VALUES (
  (SELECT `id` FROM `engine_localisation_languages` WHERE `code` = 'ga'),
  (SELECT `id` FROM `engine_localisation_messages` WHERE `message` = 'Apr'),
  'Aib'
);

INSERT IGNORE INTO `engine_localisation_translations` (`language_id`, `message_id`, `translation`) VALUES (
  (SELECT `id` FROM `engine_localisation_languages` WHERE `code` = 'en'),
  (SELECT `id` FROM `engine_localisation_messages` WHERE `message` = 'May'),
  'May'
);

INSERT IGNORE INTO `engine_localisation_translations` (`language_id`, `message_id`, `translation`) VALUES (
  (SELECT `id` FROM `engine_localisation_languages` WHERE `code` = 'ga'),
  (SELECT `id` FROM `engine_localisation_messages` WHERE `message` = 'May'),
  'Bea'
);

INSERT IGNORE INTO `engine_localisation_translations` (`language_id`, `message_id`, `translation`) VALUES (
  (SELECT `id` FROM `engine_localisation_languages` WHERE `code` = 'en'),
  (SELECT `id` FROM `engine_localisation_messages` WHERE `message` = 'Jun'),
  'Jun'
);

INSERT IGNORE INTO `engine_localisation_translations` (`language_id`, `message_id`, `translation`) VALUES (
  (SELECT `id` FROM `engine_localisation_languages` WHERE `code` = 'ga'),
  (SELECT `id` FROM `engine_localisation_messages` WHERE `message` = 'Jun'),
  'Mei'
);

INSERT IGNORE INTO `engine_localisation_translations` (`language_id`, `message_id`, `translation`) VALUES (
  (SELECT `id` FROM `engine_localisation_languages` WHERE `code` = 'en'),
  (SELECT `id` FROM `engine_localisation_messages` WHERE `message` = 'Jul'),
  'Jul'
);

INSERT IGNORE INTO `engine_localisation_translations` (`language_id`, `message_id`, `translation`) VALUES (
  (SELECT `id` FROM `engine_localisation_languages` WHERE `code` = 'ga'),
  (SELECT `id` FROM `engine_localisation_messages` WHERE `message` = 'Jul'),
  'Iúl'
);

INSERT IGNORE INTO `engine_localisation_translations` (`language_id`, `message_id`, `translation`) VALUES (
  (SELECT `id` FROM `engine_localisation_languages` WHERE `code` = 'en'),
  (SELECT `id` FROM `engine_localisation_messages` WHERE `message` = 'Aug'),
  'Aug'
);

INSERT IGNORE INTO `engine_localisation_translations` (`language_id`, `message_id`, `translation`) VALUES (
  (SELECT `id` FROM `engine_localisation_languages` WHERE `code` = 'ga'),
  (SELECT `id` FROM `engine_localisation_messages` WHERE `message` = 'Aug'),
  'Lún'
);

INSERT IGNORE INTO `engine_localisation_translations` (`language_id`, `message_id`, `translation`) VALUES (
  (SELECT `id` FROM `engine_localisation_languages` WHERE `code` = 'en'),
  (SELECT `id` FROM `engine_localisation_messages` WHERE `message` = 'Sep'),
  'Sep'
);

INSERT IGNORE INTO `engine_localisation_translations` (`language_id`, `message_id`, `translation`) VALUES (
  (SELECT `id` FROM `engine_localisation_languages` WHERE `code` = 'ga'),
  (SELECT `id` FROM `engine_localisation_messages` WHERE `message` = 'Sep'),
  'Meá'
);

INSERT IGNORE INTO `engine_localisation_translations` (`language_id`, `message_id`, `translation`) VALUES (
  (SELECT `id` FROM `engine_localisation_languages` WHERE `code` = 'en'),
  (SELECT `id` FROM `engine_localisation_messages` WHERE `message` = 'Oct'),
  'Oct'
);

INSERT IGNORE INTO `engine_localisation_translations` (`language_id`, `message_id`, `translation`) VALUES (
  (SELECT `id` FROM `engine_localisation_languages` WHERE `code` = 'ga'),
  (SELECT `id` FROM `engine_localisation_messages` WHERE `message` = 'Oct'),
  'Dei'
);

INSERT IGNORE INTO `engine_localisation_translations` (`language_id`, `message_id`, `translation`) VALUES (
  (SELECT `id` FROM `engine_localisation_languages` WHERE `code` = 'en'),
  (SELECT `id` FROM `engine_localisation_messages` WHERE `message` = 'Nov'),
  'Nov'
);

INSERT IGNORE INTO `engine_localisation_translations` (`language_id`, `message_id`, `translation`) VALUES (
  (SELECT `id` FROM `engine_localisation_languages` WHERE `code` = 'ga'),
  (SELECT `id` FROM `engine_localisation_messages` WHERE `message` = 'Nov'),
  'Sam'
);

INSERT IGNORE INTO `engine_localisation_translations` (`language_id`, `message_id`, `translation`) VALUES (
  (SELECT `id` FROM `engine_localisation_languages` WHERE `code` = 'en'),
  (SELECT `id` FROM `engine_localisation_messages` WHERE `message` = 'Dec'),
  'Dec'
);

INSERT IGNORE INTO `engine_localisation_translations` (`language_id`, `message_id`, `translation`) VALUES (
  (SELECT `id` FROM `engine_localisation_languages` WHERE `code` = 'ga'),
  (SELECT `id` FROM `engine_localisation_messages` WHERE `message` = 'Dec'),
  'Nol'
);

UPDATE `plugin_panels` SET `text`='<p>{twitter_api_feed-}</p> ' WHERE `title`='Twitter';


INSERT IGNORE INTO `engine_localisation_messages` (`message`, `created_on`, `updated_on`) VALUES
('Retweeted',   CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

INSERT IGNORE INTO `engine_localisation_translations` (`language_id`, `message_id`, `translation`) VALUES (
  (SELECT `id` FROM `engine_localisation_languages` WHERE `code` = 'en'),
  (SELECT `id` FROM `engine_localisation_messages` WHERE `message` = 'Retweeted'),
  'Retweeted'
);

INSERT INTO `engine_localisation_messages` (`message`, `created_on`, `updated_on`) VALUES
(':subject <span>by :object</span>', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

INSERT INTO `engine_localisation_translations` (`language_id`, `message_id`, `translation`) VALUES (
  (SELECT `id` FROM `engine_localisation_languages` WHERE `code` = 'ga'),
  (SELECT `id` FROM `engine_localisation_messages` WHERE `message` = ':subject <span>by :object</span>'),
  ':subject <span>le :object</span>'
);

INSERT IGNORE INTO `engine_localisation_messages` (`message`, `created_on`, `updated_on`) VALUES
('Tweets', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

INSERT INTO `engine_localisation_translations` (`language_id`, `message_id`, `translation`) VALUES (
  (SELECT `id` FROM `engine_localisation_languages` WHERE `code` = 'ga'),
  (SELECT `id` FROM `engine_localisation_messages` WHERE `message` = 'Tweets'),
  'Tweetanna'
);