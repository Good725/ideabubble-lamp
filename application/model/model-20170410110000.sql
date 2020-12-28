/*
ts:2017-04-10 11:00:00
*/

CREATE TABLE IF NOT EXISTS `engine_external_providers`( `id` INT UNSIGNED NOT NULL AUTO_INCREMENT, `name` VARCHAR(100) NOT NULL, `disabled` TINYINT(1) NOT NULL DEFAULT 0, PRIMARY KEY (`id`) ) ENGINE=INNODB;
INSERT INTO `engine_external_providers` (`name`) VALUES ('facebook'),('google');

CREATE TABLE IF NOT EXISTS `external_provider_user_data`( `provider_id` INT UNSIGNED NOT NULL, `provider_user_id` VARCHAR(250) NOT NULL, `user_id` INT UNSIGNED NOT NULL, PRIMARY KEY (`provider_id`, `provider_user_id`, `user_id`), UNIQUE INDEX (`provider_id`, `provider_user_id`, `user_id`), FOREIGN KEY (`provider_id`) REFERENCES `engine_external_providers`(`id`), FOREIGN KEY (`user_id`) REFERENCES `engine_users`(`id`) ) ENGINE=INNODB;
