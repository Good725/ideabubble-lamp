/*
ts:2016-06-21 17:50:00
*/

-- If the drivers table has not specified a default email driver, update the table to set "phpmail" as the default
UPDATE `plugin_messaging_drivers` `drivers`
INNER JOIN (
	SELECT count(*) AS `count` FROM `plugin_messaging_drivers` WHERE `driver` = 'email' AND `is_default`='YES'
) AS `counter`
SET `drivers`.`is_default` = 'YES'
WHERE `drivers`.`provider`='phpmail' AND `counter`.`count` = 0;
