CREATE TABLE IF NOT EXISTS `plugin_contacts3_temporary_signup_data` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `signup_id` VARCHAR(250) NOT NULL,
        `signup_data` TEXT(2000) NULL,
        `date_created` DATETIME NULL,
        `date_modified` DATETIME NULL,
    PRIMARY KEY (`id`),
UNIQUE INDEX `signup_id_UNIQUE` (`signup_id` ASC));
