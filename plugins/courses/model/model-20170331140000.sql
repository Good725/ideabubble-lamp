/*
ts:2017-03-31 14:00:00
*/

CREATE TABLE IF NOT EXISTS `plugin_courses_topics`( `id` INT UNSIGNED NOT NULL AUTO_INCREMENT, `name` VARCHAR(250) NOT NULL, `deleted` TINYINT(1) NOT NULL DEFAULT 0, PRIMARY KEY (`id`) ) ENGINE=INNODB;
CREATE TABLE IF NOT EXISTS `plugin_courses_courses_has_topics`( `id` INT UNSIGNED NOT NULL AUTO_INCREMENT, `course_id` INT UNSIGNED NOT NULL, `topic_id` INT UNSIGNED NOT NULL, `deleted` TINYINT(1) NOT NULL DEFAULT 0, PRIMARY KEY (`id`)) ENGINE=INNODB;