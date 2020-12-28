/*
ts:2016-03-21 14:45:00
*/
INSERT IGNORE INTO `plugin_news_categories` (`category`, `order`) VALUES ('2015', '-5'), ('2016', '-6');

UPDATE IGNORE `plugin_news_categories` SET `publish`='0' WHERE `category`='2016';
