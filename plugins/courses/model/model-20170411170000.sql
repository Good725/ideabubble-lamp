/*
ts:2017-04-11 17:00:00
*/

CREATE TABLE IF NOT EXISTS `plugin_courses_zones`( `id` INT UNSIGNED NOT NULL AUTO_INCREMENT, `name` VARCHAR(250) NOT NULL, `price` DECIMAL(10,2) NOT NULL, `deleted` TINYINT(1) NOT NULL DEFAULT 0, PRIMARY KEY (`id`) ) ENGINE=INNODB;