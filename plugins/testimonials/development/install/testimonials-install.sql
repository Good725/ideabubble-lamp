-- -----------------------------------------------------
-- Table `plugin_testimonials`
-- -----------------------------------------------------
CREATE TABLE `plugin_testimonials` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category_id` int(10) unsigned NOT NULL,
  `title` varchar(200) DEFAULT NULL,
  `summary` text,
  `item_signature` text,
  `item_company` text,
  `item_website` text,
  `content` longtext,
  `image` varchar(255) DEFAULT NULL,
  `event_date` datetime DEFAULT NULL,
  `date_created` datetime DEFAULT NULL,
  `date_modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(10) unsigned DEFAULT NULL,
  `modified_by` int(10) unsigned DEFAULT NULL,
  `publish` tinyint(1) unsigned DEFAULT '1',
  `deleted` tinyint(1) unsigned DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `fk_plugin_testimonials_plugin_testimonials_categories_idx` (`category_id`),
  CONSTRAINT `fk_plugin_testimonials_plugin_testimonials_categories` FOREIGN KEY (`category_id`) REFERENCES `plugin_testimonials_categories` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;


-- -----------------------------------------------------
-- Table `plugin_testimonials_categories`
-- -----------------------------------------------------
CREATE TABLE `plugin_testimonials_categories` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `category` varchar(200) NOT NULL,
  `date_created` datetime DEFAULT NULL,
  `date_modified` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `created_by` int(10) unsigned DEFAULT NULL,
  `modified_by` int(10) unsigned DEFAULT NULL,
  `publish` tinyint(1) unsigned DEFAULT '1',
  `delete` tinyint(1) unsigned DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- -----------------------------------------------------
-- Views for the table: `plugin_testimonials`:
-- Front-End view: `ptestimonials_view_testimonials_list_front_end`
-- Back-End (Admin) View: `ptestimonials_view_testimonials_list_admin`
-- -----------------------------------------------------
CREATE OR REPLACE ALGORITHM = UNDEFINED DEFINER = `ib_test`@`%` SQL SECURITY DEFINER VIEW `ptestimonials_view_testimonials_list_front_end` AS SELECT
	`plugin_testimonials`.`id` AS `id`,
	`plugin_testimonials`.`title` AS `title`,
	`plugin_testimonials_categories`.`category` AS `category`,
	`plugin_testimonials`.`summary` AS `summary`,
	`plugin_testimonials`.`content` AS `content`,
	`plugin_testimonials`.`image` AS `image`,
	`plugin_testimonials`.`event_date` AS `event_date`,
	`plugin_testimonials`.`item_company` AS `item_company`,
	`plugin_testimonials`.`item_signature` AS `item_signature`,
	`plugin_testimonials`.`item_website` AS `item_website`,
	`plugin_testimonials`.`date_modified` AS `date_modified`
FROM
	(
		`plugin_testimonials` left
		JOIN `plugin_testimonials_categories` ON(
			(
				`plugin_testimonials`.`category_id` = `plugin_testimonials_categories`.`id`
			)
		)
	)
WHERE
	(
		(
			`plugin_testimonials`.`publish` = 1
		)
		AND(
			`plugin_testimonials`.`deleted` = 0
		)
	);

CREATE OR REPLACE ALGORITHM = UNDEFINED DEFINER = `ib_test`@`%` SQL SECURITY DEFINER VIEW `ptestimonials_view_testimonials_list_admin` AS SELECT
	`plugin_testimonials`.`id` AS `id`,
	`plugin_testimonials`.`title` AS `title`,
	`plugin_testimonials`.`category_id` AS `category_id`,
	`plugin_testimonials_categories`.`category` AS `category`,
	`plugin_testimonials`.`summary` AS `summary`,
	`plugin_testimonials`.`content` AS `content`,
	`plugin_testimonials`.`image` AS `image`,
	`plugin_testimonials`.`event_date` AS `event_date`,
	`plugin_testimonials`.`item_signature` AS `item_signature`,
	`plugin_testimonials`.`item_company` AS `item_company`,
	`plugin_testimonials`.`item_website` AS `item_website`,
	`plugin_testimonials`.`date_created` AS `date_created`,
	`plugin_testimonials`.`created_by` AS `created_by`,
	`users_create`.`name` AS `created_by_name`,
	`roles_create`.`name` AS `created_by_role`,
	`plugin_testimonials`.`date_modified` AS `date_modified`,
	`plugin_testimonials`.`modified_by` AS `modified_by`,
	`users_modify`.`name` AS `modified_by_name`,
	`roles_modify`.`name` AS `modified_by_role`,
	`plugin_testimonials`.`publish` AS `publish`
FROM
	(
		(
			(
				(
					(
						`plugin_testimonials` left
						JOIN `plugin_testimonials_categories` ON(
							(
								`plugin_testimonials`.`category_id` = `plugin_testimonials_categories`.`id`
							)
						)
					)left
					JOIN `users` `users_create` ON(
						(
							`plugin_testimonials`.`created_by` = `users_create`.`id`
						)
					)
				)left
				JOIN `users` `users_modify` ON(
					(
						`plugin_testimonials`.`modified_by` = `users_modify`.`id`
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
		`plugin_testimonials`.`deleted` = 0
	);


