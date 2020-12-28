/*
ts:2020-06-01 16:46:00
*/

UPDATE  `engine_resources`
    SET parent_controller = (SELECT `parent`.`id` FROM (SELECT * FROM `engine_resources` `res` WHERE `alias` = 'reports') as `parent`)
    WHERE `alias` = 'reports_delete';