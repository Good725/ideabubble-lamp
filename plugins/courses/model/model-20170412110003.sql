/*
ts:2017-04-12 11:00:03
*/

CREATE TABLE IF NOT EXISTS `plugin_courses_rows`( `id` INT UNSIGNED NOT NULL AUTO_INCREMENT, `name` VARCHAR(250) NOT NULL, `seats` INT UNSIGNED NOT NULL, `location_id` INT UNSIGNED NOT NULL, PRIMARY KEY (`id`), FOREIGN KEY (`location_id`) REFERENCES `plugin_courses_locations`(`id`) ) ENGINE=INNODB;