/*
ts:2015-01-01 00:00:27
*/
CREATE TABLE IF NOT EXISTS `plugin_projects_projects`(
    `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` varchar(255),
    `summary` varchar(255),
    `category` bit(1),
    `sub_category` varchar(255),
    `publish` text,
    `order` varchar(255),
    `description` bit(1),
    `date_created` DATETIME NULL DEFAULT NULL,
    `delete` varchar(1),
    `date_modified` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE INDEX `id_UNIQUE` (`id` DESC)
) ENGINE = InnoDB;

ALTER IGNORE TABLE `plugin_projects_projects` CHANGE `description` `description` TEXT NOT NULL;
ALTER IGNORE TABLE `plugin_projects_projects` CHANGE `category` `category` int(3) NOT NULL;

CREATE TABLE IF NOT EXISTS `plugin_projects_categories`(
    `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `name` varchar(255),
    `parent` int(4),
    `summary` varchar(255),
    `description` TEXT,
    `order` int(5),
    `image` varchar(255),
    `publish` bit(1),
    `deleted` bit(1)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `plugin_projects_images`(
    `id` int NOT NULL,
    `publish` bit(1),
    `deleted` bit(1)
) ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `plugin_projects_related`(
    `id` int NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `project_id` int(5),
    `related_to` int(5),
    `publish` bit(1),
    `deleted` bit(1)
) ENGINE = InnoDB;

ALTER TABLE `plugin_projects_images` ADD `project_id` int(5);

-- ----------------------------
-- WPPROD-300 CMS icons
-- ----------------------------
UPDATE `plugins` SET icon = 'projects2.png' WHERE name = 'projects';

ALTER TABLE `plugin_projects_images`
 CHANGE COLUMN `project_id` `project_id` INT(11)    NOT NULL ,
 CHANGE COLUMN `id`         `id`         INT(11)    NOT NULL AUTO_INCREMENT  ,
 CHANGE COLUMN `publish`    `publish`    TINYINT(1) NULL     DEFAULT 1  ,
 CHANGE COLUMN `deleted`    `deleted`    TINYINT(1) NULL     DEFAULT 0  ,
 ADD    COLUMN `image_id`                INT(11)    NOT NULL AFTER `id` ,
 ADD    COLUMN `created_by`              INT(11)    NULL     AFTER `project_id` ,
 ADD    COLUMN `modified_by`             INT(11)    NULL     AFTER `created_by` ,
 ADD    COLUMN `date_created`            TIMESTAMP  NULL     DEFAULT CURRENT_TIMESTAMP()  AFTER `modified_by` ,
 ADD    COLUMN `date_modified`           TIMESTAMP  NULL     AFTER `date_created` ,
 ADD PRIMARY KEY (`id`) ;

ALTER TABLE `plugin_projects_images` CHANGE COLUMN `project_id` `project_id` INT(11) NOT NULL  AFTER `id` ;
