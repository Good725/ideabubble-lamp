/*
ts:2016-08-11 17:45:00
*/

CREATE TABLE IF NOT EXISTS `engine_lookup_fields`
(
 `id` INT (11) UNSIGNED NOT NULL AUTO_INCREMENT,
 `name` VARCHAR (255) NOT NULL,
  PRIMARY KEY (`id`)
)
ENGINE = INNODB CHARSET = UTF8;

CREATE TABLE IF NOT EXISTS `engine_lookup_values` (
`id` INT (11) UNSIGNED NOT NULL AUTO_INCREMENT,
`field_id` INT (11) NOT NULL,
`label` VARCHAR (765) NULL DEFAULT NULL,
`value` INT (11) NULL DEFAULT NULL ,
`is_default` TINYINT (4) DEFAULT '0',
`created` DATETIME NULL DEFAULT NULL,
`updated` DATETIME NULL DEFAULT NULL,
`autor` INT (11) NOT NULL,
`public` TINYINT (1) DEFAULT '0',
PRIMARY KEY (`id`))
ENGINE = INNODB CHARSET = UTF8;

INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`) VALUES (0, 'lookups', 'Lookups', 'Lookups');
INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`) VALUES
(
  (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Administrator'),
  (SELECT `id` FROM `engine_resources` WHERE `alias` = 'lookups')
);
