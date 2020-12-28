/*
ts:2017-04-13 11:00:00
*/

ALTER TABLE `plugin_courses_schedules` ADD COLUMN `zone_management` TINYINT DEFAULT 0 NOT NULL AFTER `payg_period`;

CREATE TABLE `plugin_courses_schedules_have_zones`( `row_id` INT UNSIGNED NOT NULL, `zone_id` INT UNSIGNED NOT NULL, `schedule_id` INT UNSIGNED NOT NULL, `price` DECIMAL(10,2) UNSIGNED NOT NULL DEFAULT 0, UNIQUE INDEX (`row_id`, `zone_id`, `schedule_id`), FOREIGN KEY (`row_id`) REFERENCES `plugin_courses_rows`(`id`), FOREIGN KEY (`zone_id`) REFERENCES `plugin_courses_zones`(`id`), FOREIGN KEY (`schedule_id`) REFERENCES `plugin_courses_schedules`(`id`) ) ENGINE=INNODB;