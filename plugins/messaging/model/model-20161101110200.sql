/*
ts:2016-11-01 11:03:00
*/

ALTER TABLE `plugin_messaging_message_final_targets` DROP KEY `target_id`;
ALTER TABLE `plugin_messaging_message_final_targets` ADD KEY (`target_id`);
ALTER TABLE `plugin_messaging_message_targets` DROP KEY `message_id`;
ALTER TABLE `plugin_messaging_message_targets` ADD KEY (`message_id`);
ALTER TABLE `plugin_messaging_notifications` ADD KEY (`message_id`);
