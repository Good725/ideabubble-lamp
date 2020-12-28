/*
ts:2020-04-15 22:23:00
*/
CREATE TABLE  `plugin_courses_waitlist` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `course_id` INT NOT NULL,
    `schedule_id` INT NOT NULL,
    `timeslot_id` INT NOT NULL,
    `email` VARCHAR(254) NULL,
    `name` VARCHAR(50) NULL,
    `surname` VARCHAR(50) NULL,
    `phone` VARCHAR(50) NULL,
    `address` TINYTEXT NULL,
    `message` LONGTEXT NULL,
     PRIMARY KEY (`id`));
