/*
ts:2015-12-04 12:41:00
*/

ALTER TABLE `engine_localisation_messages` MODIFY COLUMN `message`  text CHARACTER SET utf8 COLLATE utf8_bin NULL;

INSERT IGNORE INTO `settings`
(`variable`,        `name`,              `note`,                                                     `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `type`, `group`) VALUES
('twitter_feed_id', 'Twitter Feed ID',   'Create a widget, while logged in to Twitter to get this.', '',           '',            '',           '',          '',        'text', 'Social Media');

