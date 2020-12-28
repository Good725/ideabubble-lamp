/*
ts:2016-01-12 15:25:00
*/
ALTER IGNORE TABLE `engine_user_tokens`
CHANGE COLUMN `type` `type` VARCHAR(100) NULL  ,
CHANGE COLUMN `created` `created` INT(10) UNSIGNED NULL  ;
