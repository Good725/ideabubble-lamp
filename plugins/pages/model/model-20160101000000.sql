/*
ts:2016-01-01 00:00:00
*/
ALTER TABLE `plugin_pages_pages` MODIFY COLUMN `nocache` TINYINT(1) DEFAULT 1;
ALTER TABLE `plugin_pages_pages` MODIFY COLUMN `force_ssl` TINYINT(1) DEFAULT 0;
