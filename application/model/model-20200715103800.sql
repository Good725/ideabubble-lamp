/*
ts:2020-07-15 10:38:00
*/

ALTER TABLE `engine_errorlog` MODIFY COLUMN `type`  ENUM('PHP','SQL','HTTP','FORM','SECURITY');


