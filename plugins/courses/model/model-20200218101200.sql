/*
ts:2020-02-18 10:12:00
*/

ALTER TABLE `plugin_courses_schedules`
    ADD COLUMN `schedule_status` INT(11) NOT NULL DEFAULT 1 AFTER `course_id`;

CREATE TABLE `plugin_courses_schedules_status`
(
    `id`        INT(11)      NOT NULL,
    `title`     VARCHAR(150) NOT NULL,
    `published` TINYINT(1)   NOT NULL DEFAULT '1',
    `deleted`   TINYINT(1)   NOT NULL DEFAULT 0,
    PRIMARY KEY (`id`)
);

INSERT IGNORE INTO `plugin_courses_schedules_status` (`id`, `title`, `published`, `deleted`)
VALUES (1, 'Confirmed', 1, 0);
INSERT IGNORE INTO `plugin_courses_schedules_status` (`id`, `title`, `published`, `deleted`)
VALUES (2, 'Cancelled', 1, 0);
INSERT IGNORE INTO `plugin_courses_schedules_status` (`id`, `title`, `published`, `deleted`)
VALUES (3, 'In Progress', 1, 0);
INSERT IGNORE INTO `plugin_courses_schedules_status` (`id`, `title`, `published`, `deleted`)
VALUES (4, 'Completed', 1, 0);
