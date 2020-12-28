-- -----------------------------------------------------
-- Table `plugin_news`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `plugin_news` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `category_id` INT(10) UNSIGNED NOT NULL ,
  `title` VARCHAR(200) NULL DEFAULT NULL ,
  `summary` TEXT NULL DEFAULT NULL ,
  `content` LONGTEXT NULL DEFAULT NULL ,
  `image` VARCHAR(255) NULL DEFAULT NULL ,
  `date_publish` DATETIME NULL DEFAULT NULL ,
  `date_remove` DATETIME NULL DEFAULT NULL ,
  `date_created` DATETIME NULL DEFAULT NULL ,
  `date_modified` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `created_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `modified_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `publish` TINYINT(1) UNSIGNED NULL DEFAULT '1' ,
  `deleted` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_plugin_news_plugin_news_categories_idx` (`category_id` ASC) ,
  CONSTRAINT `fk_plugin_news_plugin_news_categories`
    FOREIGN KEY (`category_id` )
    REFERENCES `plugin_news_categories` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Table `plugin_news_categories`
-- -----------------------------------------------------
CREATE  TABLE IF NOT EXISTS `plugin_news_categories` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `category` VARCHAR(200) NOT NULL ,
  `date_created` DATETIME NULL DEFAULT NULL ,
  `date_modified` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `created_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `modified_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `publish` TINYINT(1) UNSIGNED NULL DEFAULT '1' ,
  `delete` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) )
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_general_ci;


-- -----------------------------------------------------
-- Views for the table: `plugin_news`:
-- Front-End view: `pnews_view_news_list_front_end`
-- Back-End (Admin) View: `pnews_view_news_list_admin`
-- -----------------------------------------------------
CREATE OR REPLACE ALGORITHM = UNDEFINED DEFINER = `ib_test`@`%` SQL SECURITY DEFINER VIEW `pnews_view_news_list_front_end` AS SELECT plugin_news.id,
	plugin_news.title,
	plugin_news_categories.category,
	plugin_news.summary,
	plugin_news.content,
	plugin_news.image,
	plugin_news.date_publish,
	plugin_news.date_remove
FROM plugin_news LEFT OUTER JOIN plugin_news_categories ON plugin_news.category_id = plugin_news_categories.id
WHERE ((`plugin_news`.`publish` = 1) and (`plugin_news`.`deleted` = 0));

CREATE OR REPLACE ALGORITHM = UNDEFINED DEFINER = `ib_test`@`%` SQL SECURITY DEFINER VIEW `pnews_view_news_list_admin` AS SELECT plugin_news.id,
	plugin_news.title,
	plugin_news.category_id,
	plugin_news_categories.category,
	plugin_news.summary,
	plugin_news.content,
	plugin_news.image,
	plugin_news.date_publish,
	plugin_news.date_remove,
	plugin_news.date_created,
	plugin_news.created_by,
	users_create.name AS created_by_name,
	roles_create.name AS created_by_role,
	plugin_news.date_modified,
	plugin_news.modified_by,
	users_modify.name AS modified_by_name,
	roles_modify.name AS modified_by_role,
	plugin_news.publish
FROM plugin_news LEFT OUTER JOIN plugin_news_categories ON plugin_news.category_id = plugin_news_categories.id
	 LEFT OUTER JOIN users users_create ON plugin_news.created_by = users_create.id
	 LEFT OUTER JOIN users users_modify ON plugin_news.modified_by = users_modify.id
	 LEFT OUTER JOIN roles roles_modify ON users_modify.role_id = roles_modify.id
	 LEFT OUTER JOIN roles roles_create ON users_create.role_id = roles_create.id
WHERE (`plugin_news`.`deleted` = 0);


-- -----------------------------------------------------
-- Update Table `plugin_news`
-- Add new field: event_date
-- -----------------------------------------------------
ALTER TABLE plugin_news ADD COLUMN event_date DATETIME NULL DEFAULT NULL AFTER image;


-- -----------------------------------------------------
-- Update Views for the table: `plugin_news` to load the newly added field: event_date
-- Front-End view: `pnews_view_news_list_front_end`
-- Back-End (Admin) View: `pnews_view_news_list_admin`
-- -----------------------------------------------------
CREATE OR REPLACE ALGORITHM = UNDEFINED DEFINER = `ib_test`@`%` SQL SECURITY DEFINER VIEW `pnews_view_news_list_front_end` AS SELECT plugin_news.id,
	plugin_news.title,
	plugin_news_categories.category,
	plugin_news.summary,
	plugin_news.content,
	plugin_news.image,
	plugin_news.event_date,
	plugin_news.date_publish,
	plugin_news.date_remove
FROM plugin_news LEFT OUTER JOIN plugin_news_categories ON plugin_news.category_id = plugin_news_categories.id
WHERE ((`plugin_news`.`publish` = 1) and (`plugin_news`.`deleted` = 0));

CREATE OR REPLACE ALGORITHM = UNDEFINED DEFINER = `ib_test`@`%` SQL SECURITY DEFINER VIEW `pnews_view_news_list_admin` AS SELECT plugin_news.id,
	plugin_news.title,
	plugin_news.category_id,
	plugin_news_categories.category,
	plugin_news.summary,
	plugin_news.content,
	plugin_news.image,
	plugin_news.event_date,
	plugin_news.date_publish,
	plugin_news.date_remove,
	plugin_news.date_created,
	plugin_news.created_by,
	users_create.name AS created_by_name,
	roles_create.name AS created_by_role,
	plugin_news.date_modified,
	plugin_news.modified_by,
	users_modify.name AS modified_by_name,
	roles_modify.name AS modified_by_role,
	plugin_news.publish
FROM plugin_news LEFT OUTER JOIN plugin_news_categories ON plugin_news.category_id = plugin_news_categories.id
	 LEFT OUTER JOIN users users_create ON plugin_news.created_by = users_create.id
	 LEFT OUTER JOIN users users_modify ON plugin_news.modified_by = users_modify.id
	 LEFT OUTER JOIN roles roles_modify ON users_modify.role_id = roles_modify.id
	 LEFT OUTER JOIN roles roles_create ON users_create.role_id = roles_create.id
WHERE (`plugin_news`.`deleted` = 0);


-- -----------------------------------------------------
-- Update Front-End view: `pnews_view_news_list_front_end`:
-- included Field: date_modified -=> TO BE USED for the Default Ordering of News Items
-- -----------------------------------------------------
CREATE OR REPLACE ALGORITHM = UNDEFINED DEFINER = `ib_test`@`%` SQL SECURITY DEFINER VIEW `pnews_view_news_list_front_end` AS SELECT plugin_news.id,
	plugin_news.title,
	plugin_news_categories.category,
	plugin_news.summary,
	plugin_news.content,
	plugin_news.image,
	plugin_news.event_date,
	plugin_news.date_publish,
	plugin_news.date_remove,
	plugin_news.date_modified
FROM plugin_news LEFT OUTER JOIN plugin_news_categories ON plugin_news.category_id = plugin_news_categories.id
WHERE ((`plugin_news`.`publish` = 1) and (`plugin_news`.`deleted` = 0));