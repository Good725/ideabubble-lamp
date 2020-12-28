
SET @OLD_UNIQUE_CHECKS =@@UNIQUE_CHECKS,
 UNIQUE_CHECKS = 0;


SET @OLD_FOREIGN_KEY_CHECKS =@@FOREIGN_KEY_CHECKS,
 FOREIGN_KEY_CHECKS = 0;


SET @OLD_SQL_MODE =@@SQL_MODE,
 SQL_MODE = 'TRADITIONAL,ALLOW_INVALID_DATES';

CREATE SCHEMA
IF NOT EXISTS `mydb` DEFAULT CHARACTER
SET utf8 COLLATE utf8_general_ci;


-- -----------------------------------------------------
-- Table `ppages_layouts`
-- -----------------------------------------------------
CREATE TABLE
IF NOT EXISTS `ppages_layouts`(
	`id` INT(10)UNSIGNED NOT NULL AUTO_INCREMENT,
	`layout` VARCHAR(255)NULL DEFAULT NULL,
	PRIMARY KEY(`id`),
	INDEX `fk_layouts_pages`(`id` ASC)
)ENGINE = INNODB AUTO_INCREMENT = 1 DEFAULT CHARACTER
SET = utf8;

-- -----------------------------------------------------
-- Table `ppages_categorys`
-- -----------------------------------------------------
CREATE TABLE
IF NOT EXISTS `ppages_categorys`(
	`id` INT(10)UNSIGNED NOT NULL AUTO_INCREMENT,
	`category` VARCHAR(255)NULL DEFAULT NULL,
	PRIMARY KEY(`id`),
	INDEX `fk_categorys_pages1`(`id` ASC)
)ENGINE = INNODB AUTO_INCREMENT = 1 DEFAULT CHARACTER
SET = utf8;

-- -----------------------------------------------------
-- Table `ppages`
-- -----------------------------------------------------
CREATE TABLE
IF NOT EXISTS `ppages`(
	`id` INT(10)UNSIGNED NOT NULL AUTO_INCREMENT,
	`name_tag` VARCHAR(255)NULL DEFAULT NULL,
	`title` VARCHAR(255)NULL DEFAULT NULL,
	`content` TEXT NULL DEFAULT NULL,
	`seo_keywords` VARCHAR(500)NULL DEFAULT NULL,
	`seo_description` VARCHAR(500)NULL DEFAULT NULL,
	`footer` VARCHAR(500)NULL DEFAULT NULL,
	`date_entered` DATETIME NULL DEFAULT NULL,
	`last_modified` DATETIME NULL DEFAULT NULL,
	`created_by` INT(11)NULL DEFAULT NULL,
	`modified_by` INT(11)NULL DEFAULT NULL,
	`publish` TINYINT(1)NOT NULL DEFAULT '0',
	`deleted` TINYINT(1)NOT NULL DEFAULT '0',
	`include_sitemap` TINYINT(1)NOT NULL DEFAULT '0',
	`layout_id` INT(10)UNSIGNED NOT NULL,
	`category_id` INT(10)UNSIGNED NOT NULL,
	PRIMARY KEY(`id`),
	INDEX `fk_ppages_ppages_layouts_idx`(`layout_id` ASC),
	INDEX `fk_ppages_ppages_categorys1_idx`(`category_id` ASC),
	CONSTRAINT `fk_ppages_ppages_layouts` FOREIGN KEY(`layout_id`)REFERENCES `ppages_layouts`(`id`)ON DELETE NO ACTION ON UPDATE NO ACTION,
	CONSTRAINT `fk_ppages_ppages_categorys1` FOREIGN KEY(`category_id`)REFERENCES `ppages_categorys`(`id`)ON DELETE NO ACTION ON UPDATE NO ACTION
)ENGINE = INNODB AUTO_INCREMENT = 1 DEFAULT CHARACTER
SET = utf8;

-- -----------------------------------------------------
-- Placeholder table for view `view_ppages`
-- -----------------------------------------------------
CREATE TABLE
IF NOT EXISTS `view_ppages`(
	`id` INT,
	`name_tag` INT,
	`title` INT,
	`content` INT,
	`category_id` INT,
	`layout_id` INT,
	`seo_keywords` INT,
	`seo_description` INT,
	`date_entered` INT,
	`footer` INT,
	`last_modified` INT,
	`modied_by` INT,
	`created_by` INT,
	`publish` INT,
	`deleted` INT,
	`category` INT,
	`layout` INT,
	`include_sitemap` INT
);

-- -----------------------------------------------------
-- View `view_ppages`
-- -----------------------------------------------------
DROP TABLE
IF EXISTS `view_ppages`;

CREATE
OR REPLACE ALGORITHM = UNDEFINED SQL SECURITY DEFINER VIEW `view_ppages` AS SELECT
	`ppages`.`id` AS `id`,
	`ppages`.`name_tag` AS `name_tag`,
	`ppages`.`title` AS `title`,
	`ppages`.`content` AS `content`,
	`ppages`.`category_id` AS `category_id`,
	`ppages`.`layout_id` AS `layout_id`,
	`ppages`.`seo_keywords` AS `seo_keywords`,
	`ppages`.`seo_description` AS `seo_description`,
	`ppages`.`date_entered` AS `date_entered`,
	`ppages`.`footer` AS `footer`,
	`ppages`.`last_modified` AS `last_modified`,
	`ppages`.`modified_by` AS `modied_by`,
	`ppages`.`created_by` AS `created_by`,
	`ppages`.`publish` AS `publish`,
	`ppages`.`deleted` AS `deleted`,
	`ppages_categorys`.`category` AS `category`,
	`ppages_layouts`.`layout` AS `layout`,
	`ppages`.`include_sitemap` AS `include_sitemap`
FROM
	(
		(`ppages` JOIN `ppages_categorys`)
		JOIN `ppages_layouts`
	)
WHERE
	(
		(
			`ppages`.`category_id` = `ppages_categorys`.`id`
		)
		AND(
			`ppages`.`layout_id` = `ppages_layouts`.`id`
		)
	);


SET SQL_MODE =@OLD_SQL_MODE;


SET FOREIGN_KEY_CHECKS =@OLD_FOREIGN_KEY_CHECKS;


SET UNIQUE_CHECKS =@OLD_UNIQUE_CHECKS;


-- ------------------------
-- Updates for the `ppages` table:
-- Added new field for the Banner-Photo: banner_photo
-- ------------------------
ALTER TABLE `ppages` ADD COLUMN `banner_photo` VARCHAR(500) NULL DEFAULT NULL AFTER `content`;


-- ------------------------
-- Updated corresponding view: `view_ppages`
-- ------------------------
CREATE OR REPLACE ALGORITHM = UNDEFINED DEFINER = `ib_test`@`%` SQL SECURITY DEFINER VIEW `view_ppages` AS SELECT
	ppages.id,
	ppages.name_tag,
	ppages.title,
	ppages.content,
	ppages.banner_photo,
	ppages.category_id,
	ppages.layout_id,
	ppages.seo_keywords,
	ppages.seo_description,
	ppages.date_entered,
	ppages.footer,
	ppages.last_modified,
	ppages.modified_by AS modified_by,
	ppages.created_by,
	ppages.publish,
	ppages.deleted,
	ppages_categorys.category,
	ppages_layouts.layout,
	ppages.include_sitemap
FROM
	ppages LEFT
OUTER JOIN ppages_layouts ON ppages.layout_id = ppages_layouts.id LEFT
OUTER JOIN ppages_categorys ON ppages.category_id = ppages_categorys.id;
