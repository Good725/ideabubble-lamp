/*
ts:2020-04-23 17:29:00
*/

ALTER TABLE `engine_errorlog` MODIFY COLUMN `type`  ENUM('PHP','SQL','HTTP','FORM');
