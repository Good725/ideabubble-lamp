/*
ts:2017-01-03 11:24:00
*/

UPDATE plugin_courses_providers_types SET `type` = 'Business' WHERE `type` = 'Buisiness';
UPDATE plugin_courses_providers SET type_id = 1 WHERE type_id IS NULL AND `delete` = 0 AND `publish` = 1;
