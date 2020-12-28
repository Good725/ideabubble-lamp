/*
ts:2020-03-24 21:00:00
*/

INSERT IGNORE INTO `engine_resources`
(`type_id`, `alias`, `name`, `description`, parent_controller)
VALUES
(2, 'surveys_old', 'Surveys / Old', 'Access the old version of the surveys plugin',    (SELECT id FROM `engine_resources` `o` WHERE `o`.`alias` = 'surveys'));
