/*
ts:2020-02-27 12:04:00
*/

INSERT INTO `engine_resources`
    (`type_id`, `alias`, `name`, `description`, parent_controller)
VALUES (2, 'courses_schedule_amendable', 'Courses / Schedule / Toggle Amendable',
        'Toggle the amendable option to be shown in schedules',
        (SELECT id FROM `engine_resources` `o` WHERE `o`.`alias` = 'courses'));

UPDATE `engine_resources` SET `type_id` = '1' WHERE (`alias` = 'courses_schedule_amendable');
