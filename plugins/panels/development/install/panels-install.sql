-- -----------------------------------------------------
-- Table `plugin_panels`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `plugin_panels` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `page_id` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `title` VARCHAR(200) NULL DEFAULT NULL ,
  `position` VARCHAR(45) NULL DEFAULT NULL ,
  `order_no` INT(3) UNSIGNED NULL DEFAULT '0' ,
  `image` VARCHAR(255) NULL DEFAULT NULL ,
  `text` TEXT NULL DEFAULT NULL ,
  `link_id` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `link_url` VARCHAR(255) NULL DEFAULT NULL ,
  `date_publish` DATETIME NULL DEFAULT NULL ,
  `date_remove` DATETIME NULL DEFAULT NULL ,
  `date_created` DATETIME NULL DEFAULT NULL ,
  `date_modified` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `created_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `modified_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `publish` TINYINT(1) UNSIGNED NULL DEFAULT '1' ,
  `deleted` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Views for the table: `plugin_panels`:
-- Front-End view: `ppanels_view_panels_list_front_end`
-- Back-End (Admin) View: `ppanels_view_panels_list_admin`
-- -----------------------------------------------------
CREATE OR REPLACE ALGORITHM = UNDEFINED DEFINER = `ib_test`@`%` SQL SECURITY DEFINER VIEW `ppanels_view_panels_list_front_end` AS SELECT
	`plugin_panels`.`id` AS `id`,
	`plugin_panels`.`page_id` AS `page_id`,
	`plugin_panels`.`title` AS `title`,
	`plugin_panels`.`position` AS `position`,
	`plugin_panels`.`order_no` AS `order_no`,
	`plugin_panels`.`image` AS `image`,
	`plugin_panels`.`text` AS `text`,
	`plugin_panels`.`link_id` AS `link_id`,
	`plugin_panels`.`link_url` AS `link_url`,
	`plugin_panels`.`date_publish` AS `date_publish`,
	`plugin_panels`.`date_remove` AS `date_remove`,
	`plugin_panels`.`publish` AS `publish`,
	`plugin_panels`.`deleted` AS `deleted`
FROM
	`plugin_panels`
WHERE
	(
		(`plugin_panels`.`publish` = 1)
		AND(`plugin_panels`.`deleted` = 0)
	);

CREATE OR REPLACE ALGORITHM = UNDEFINED DEFINER = `ib_test`@`%` SQL SECURITY DEFINER VIEW `ppanels_view_panels_list_admin` AS SELECT
	`plugin_panels`.`id` AS `id`,
	`plugin_panels`.`page_id` AS `page_id`,
	`plugin_panels`.`title` AS `title`,
	`plugin_panels`.`position` AS `position`,
	`plugin_panels`.`order_no` AS `order_no`,
	`plugin_panels`.`image` AS `image`,
	`plugin_panels`.`text` AS `text`,
	`plugin_panels`.`link_id` AS `link_id`,
	`plugin_panels`.`link_url` AS `link_url`,
	`plugin_panels`.`date_publish` AS `date_publish`,
	`plugin_panels`.`date_remove` AS `date_remove`,
	`plugin_panels`.`date_created` AS `date_created`,
	`plugin_panels`.`created_by` AS `created_by`,
	`users_create`.`name` AS `created_by_name`,
	`roles_created`.`name` AS `created_by_role`,
	`plugin_panels`.`date_modified` AS `date_modified`,
	`plugin_panels`.`modified_by` AS `modified_by`,
	`users_modify`.`name` AS `modified_by_name`,
	`roles_modified`.`name` AS `modified_by_role`,
	`plugin_panels`.`publish` AS `publish`
FROM
	(
		(
			(
				(
					`plugin_panels` left
					JOIN `users` `users_create` ON(
						(
							`plugin_panels`.`created_by` = `users_create`.`id`
						)
					)
				)left
				JOIN `roles` `roles_created` ON(
					(
						`users_create`.`role_id` = `roles_created`.`id`
					)
				)
			)left
			JOIN `users` `users_modify` ON(
				(
					`plugin_panels`.`modified_by` = `users_modify`.`id`
				)
			)
		)left
		JOIN `roles` `roles_modified` ON(
			(
				`users_modify`.`role_id` = `roles_modified`.`id`
			)
		)
	)
WHERE
	(`plugin_panels`.`deleted` = 0);