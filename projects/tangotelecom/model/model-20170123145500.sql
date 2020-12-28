/*
ts:2017-01-23 14:55:00
*/

UPDATE `plugin_news_categories` SET `publish`='1' WHERE `category`='2016';

INSERT INTO `plugin_news_categories` (`category`, `order`) VALUES ('2017', '-7');

UPDATE `plugin_news_categories` SET `publish`='0' WHERE `category`='2017';