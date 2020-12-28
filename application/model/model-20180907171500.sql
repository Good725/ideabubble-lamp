/*
ts:2018-09-07 17:15:00
*/

ALTER TABLE `engine_plugins` ADD COLUMN `svg` VARCHAR(64) NULL AFTER `flaticon`;

UPDATE `engine_plugins` SET `svg` = 'timeoff' WHERE `name` = 'timeoff';

