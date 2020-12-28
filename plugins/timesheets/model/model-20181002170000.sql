/*
ts:2018-10-02 17:00:00
*/
ALTER TABLE `plugin_todos`
	CHANGE COLUMN `type_id` `type_id` ENUM('Task','Bug','Improvement','Internal') NULL DEFAULT 'Task' COLLATE 'utf8_bin' AFTER `priority_id`;
