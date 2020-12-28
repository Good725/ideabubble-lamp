/*
ts:2020-07-28 02:11:00
*/

CREATE TABLE `plugin_courses_countries` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(255) NOT NULL,
    `alpha2_code` VARCHAR(2) NULL,
    `alpha3_code` VARCHAR(3) NULL,
    `iso_code` INT NULL,
    `dial_code` INT NULL,
    `delete` TINYINT NULL,
    `publish` TINYINT NULL,
    `date_created` DATETIME NULL,
    `date_modified` DATETIME NULL,
    `created_by` INT NULL,
    `modified_by` INT NULL,
PRIMARY KEY (`id`));
