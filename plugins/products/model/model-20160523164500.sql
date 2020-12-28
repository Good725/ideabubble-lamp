/*
ts:2016-05-23 16:45:00
*/

ALTER TABLE `plugin_products_category`
ADD COLUMN `url_name`        VARCHAR(255) NULL  AFTER `theme` ,
ADD COLUMN `seo_title`       VARCHAR(255) NULL  AFTER `url_name` ,
ADD COLUMN `seo_keywords`    VARCHAR(255) NULL  AFTER `seo_title` ,
ADD COLUMN `seo_description` VARCHAR(255) NULL  AFTER `seo_keywords` ,
ADD COLUMN `seo_footer`      VARCHAR(255) NULL  AFTER `seo_description` ;

ALTER IGNORE TABLE `plugin_products_category` CHANGE `url_name` `url_title` VARCHAR(255) NULL;

