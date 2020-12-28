/*
ts:2016-12-15 13:00:00
*/

ALTER TABLE `plugin_messaging_messages` CHANGE COLUMN `form_data` `form_data` TEXT NULL DEFAULT NULL  ;

UPDATE `plugin_messaging_messages`
SET
	`form_data` = replace(
	   `form_data`,
	   CONCAT('ccNum\":\"', substring_index(substring_index(form_data, 'ccNum\":\"', -1),'\"', 1), '\"'),
	   'ccNum\":\"****************\"'
	);

UPDATE `plugin_messaging_messages`
SET
	`form_data` = replace(
	   `form_data`,
	   CONCAT('ccv\":\"', substring_index(substring_index(form_data, 'ccv\":\"', -1),'\"', 1), '\"'),
	   'ccv\":\"***\"'
	);

UPDATE `plugin_messaging_messages`
SET
	`form_data` = replace(
	   `form_data`,
	   CONCAT('ccExpMM\":\"', substring_index(substring_index(form_data, 'ccExpMM\":\"', -1),'\"', 1), '\"'),
	   'ccExpMM\":\"**\"'
	);

UPDATE   `plugin_messaging_messages`
SET
	`form_data` = replace(
	   `form_data`,
	   CONCAT('ccExpYY\\":\\"', substring_index(substring_index(form_data, 'ccExpYY\\":\\"', -1),'\\"', 1), '\\"'),
	   'ccExpYY\\":\\"**\\"'
	);

UPDATE    `plugin_messaging_messages`
SET
	`form_data` = replace(
	   `form_data`,
	   CONCAT('ccNum\\":\\"', substring_index(substring_index(form_data, 'ccNum\\":\\"', -1),'\\"', 1), '\\"'),
	   'ccNum\\":\\"****************\\"'
	);

UPDATE    `plugin_messaging_messages`
SET
	`form_data` = replace(
	   `form_data`,
	   CONCAT('ccv\\":\\"', substring_index(substring_index(form_data, 'ccv\\":\\"', -1),'\\"', 1), '\\"'),
	   'ccv\\":\\"***\\"'
	);

UPDATE  `plugin_messaging_messages`
SET
	`form_data` = replace(
	   `form_data`,
	   CONCAT('ccExpMM\\":\\"', substring_index(substring_index(form_data, 'ccExpMM\\":\\"', -1),'\\"', 1), '\\"'),
	   'ccExpMM\\":\\"**\\"'
	);

UPDATE   `plugin_messaging_messages`
SET
	`form_data` = replace(
	   `form_data`,
	   CONCAT('ccExpYY\\":\\"', substring_index(substring_index(form_data, 'ccExpYY\\":\\"', -1),'\\"', 1), '\\"'),
	   'ccExpYY\\":\\"**\\"'
	);