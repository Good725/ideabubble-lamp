/*
ts:2017-05-10 09:45:00
*/

INSERT INTO `plugin_messaging_read_by` (`message_id`, `user_id`)
	(SELECT `target`.`message_id`, `user`.`id` as `user_id`
	FROM   `plugin_messaging_message_final_targets` `ftarget`
	JOIN   `plugin_messaging_message_targets`       `target` ON `ftarget`.`target_id` = `target`.`id`
	JOIN   `engine_users`                           `user`   ON `user`   .`email`     = `ftarget`.`target`
	WHERE  `ftarget`.`delivery_status` = 'READ')
ON DUPLICATE KEY
  UPDATE `plugin_messaging_read_by`.`message_id` = `plugin_messaging_read_by`.`message_id`;