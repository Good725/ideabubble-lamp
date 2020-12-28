/*
ts:2020-06-24 00:13:00
*/
CREATE TABLE IF NOT EXISTS `plugin_contacts3_blacklist` (
        `id` INT NOT NULL AUTO_INCREMENT,
        `domain_name` VARCHAR(255) NULL,
        `delete` TINYINT NULL,
    PRIMARY KEY (`id`));
