INSERT INTO `plugins`
(`name`, `version`, `folder`, `menu`, `is_frontend`, `is_backend`, `enabled`)
VALUES
('media', 'development', 'media', 'Media Uploader', 0, 1, 1);

CREATE TABLE `shared_media` (
  `shared_media_id` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(200) NOT NULL,
  `thumbnail` varchar(2000) DEFAULT NULL,
  `preset` varchar(50) DEFAULT NULL,
  `size` int(11) NOT NULL,
  `hash` varchar(32) DEFAULT NULL,
  `modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`shared_media_id`)
);


-- ----------------------------
-- Rebuild Table `shared_media`:
-- ----------------------------
DROP TABLE IF EXISTS `shared_media`;
CREATE TABLE `shared_media` (
	`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
	`filename` VARCHAR(200) NOT NULL,
	`dimensions` varchar(50) DEFAULT NULL,
	`location` VARCHAR(100) NOT NULL,
	`size` int(20) NOT NULL,
	`hash` varchar(32) DEFAULT NULL,
	`preset_id` INT(10) UNSIGNED NULL DEFAULT NULL,
	`date_created` DATETIME NULL DEFAULT NULL ,
	`date_modified` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
	`created_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
	`modified_by` INT(10) UNSIGNED NULL DEFAULT NULL,
	PRIMARY KEY (`id`)
)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- ----------------------------
--  Table structure for `shared_media_photo_presets`
-- ----------------------------
CREATE TABLE IF NOT EXISTS `shared_media_photo_presets` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL,
  `directory` varchar(100) NOT NULL DEFAULT 'content',
  `height_large` int(10) unsigned NOT NULL,
  `width_large` int(10) unsigned NOT NULL,
  `action_large` varchar(10) NOT NULL DEFAULT 'fit',
  `thumb` TINYINT(1) NOT NULL DEFAULT '0',
  `height_thumb` int(10) unsigned NOT NULL,
  `width_thumb` int(10) unsigned NOT NULL,
  `action_thumb` varchar(10) NOT NULL DEFAULT 'crop',
  `date_created` timestamp NULL DEFAULT NULL,
  `date_modified` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `created_by` int(10) unsigned NOT NULL,
  `modified_by` int(10) unsigned NOT NULL,
  `publish` TINYINT(1) UNSIGNED NULL DEFAULT '1',
  `deleted` TINYINT(1) UNSIGNED NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- ----------------------------
-- Update Table `shared_media`:
-- Add field: mime_type
-- ----------------------------
ALTER TABLE `shared_media` ADD COLUMN `mime_type` VARCHAR(50) NOT NULL AFTER `size`;


-- ----------------------------
-- Create View: `pmedia_view_media_presets_list_admin` for  Table `shared_media_photo_presets`:
-- ----------------------------
CREATE OR REPLACE ALGORITHM = UNDEFINED DEFINER = `ib_test`@`%` SQL SECURITY DEFINER VIEW `pmedia_view_media_presets_list_admin` AS SELECT
	`shared_media_photo_presets`.`id` AS `id`,
	`shared_media_photo_presets`.`title` AS `title`,
	`shared_media_photo_presets`.`directory` AS `directory`,
	`shared_media_photo_presets`.`height_large` AS `height_large`,
	`shared_media_photo_presets`.`width_large` AS `width_large`,
	`shared_media_photo_presets`.`action_large` AS `action_large`,
	`shared_media_photo_presets`.`thumb` AS `thumb`,
	`shared_media_photo_presets`.`height_thumb` AS `height_thumb`,
	`shared_media_photo_presets`.`width_thumb` AS `width_thumb`,
	`shared_media_photo_presets`.`action_thumb` AS `action_thumb`,
	`shared_media_photo_presets`.`date_created` AS `date_created`,
	`shared_media_photo_presets`.`created_by` AS `created_by`,
	`users_create`.`name` AS `created_by_name`,
	`roles_create`.`name` AS `created_by_role`,
	`shared_media_photo_presets`.`date_modified` AS `date_modified`,
	`shared_media_photo_presets`.`modified_by` AS `modified_by`,
	`users_modify`.`name` AS `modified_by_name`,
	`roles_modify`.`name` AS `modified_by_role`,
	`shared_media_photo_presets`.`publish` AS `publish`
FROM
	(
		(
			(
				(
					`shared_media_photo_presets` left
					JOIN `users` `users_create` ON(
						(
							`shared_media_photo_presets`.`created_by` = `users_create`.`id`
						)
					)
				)left
				JOIN `users` `users_modify` ON(
					(
						`shared_media_photo_presets`.`modified_by` = `users_modify`.`id`
					)
				)
			)left
			JOIN `roles` `roles_modify` ON(
				(
					`users_modify`.`role_id` = `roles_modify`.`id`
				)
			)
		)left
		JOIN `roles` `roles_create` ON(
			(
				`users_create`.`role_id` = `roles_create`.`id`
			)
		)
	)
WHERE
	(
		`shared_media_photo_presets`.`deleted` = 0
	);