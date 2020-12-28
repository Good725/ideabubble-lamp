/*
ts: 2020-07-27 14:24:00
*/

CREATE TABLE IF NOT EXISTS `plugin_ib_educate_bookings_has_files`  (
    `id` INT NOT NULL AUTO_INCREMENT,
    `booking_id` INT NOT NULL,
    `transaction_ud` INT NULL,
    `document_id` INT NOT NULL,
    `shared` TINYINT NULL DEFAULT 0,
    `deleted` TINYINT NULL DEFAULT 0,
    PRIMARY KEY (`id`));