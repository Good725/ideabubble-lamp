/*
ts:2020-07-28 14:00:00
*/
CREATE TABLE `engine_dialcodes` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `dial_code` VARCHAR(45) NULL,
        `country_id` INT NULL,
        `alpha2_country_code` VARCHAR(3) NULL,
        `alpha3_country_code` VARCHAR(3) NULL,
        `area_code` VARCHAR(45) NULL,
        `type` ENUM('country', 'area', 'mobile') NULL,
        `publish` TINYINT NULL,
        `delete` TINYINT NULL,
        `date_created` DATETIME NULL,
        `date_modified` DATETIME NULL,
        `created_by` INT NULL,
        `modified_by` INT NULL,
    PRIMARY KEY (`id`));

ALTER TABLE `engine_dialcodes`
    ADD UNIQUE INDEX `uk_area_coutnry_code` (`dial_code` ASC, `country_id` ASC, `alpha2_country_code` ASC, `alpha3_country_code` ASC, `area_code` ASC, `type` ASC);

