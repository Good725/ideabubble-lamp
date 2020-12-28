/*
ts:2016-10-05 16:40:00
*/

DELETE FROM `engine_role_permissions`
WHERE
	`role_id` = (SELECT `id` FROM `engine_project_role` WHERE `role` = 'External User' LIMIT 1)
AND
	`resource_id` = (SELECT `id` FROM `engine_resources` WHERE `alias` = 'lookups' LIMIT 1)
;
