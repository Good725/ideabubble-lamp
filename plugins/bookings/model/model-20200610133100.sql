/*
ts:2020-06-10 13:31:00
*/

DELIMITER $$

DROP PROCEDURE IF EXISTS add_application_type $$
CREATE PROCEDURE add_application_type()
BEGIN

    IF NOT EXISTS( (SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE()
                                                               AND COLUMN_NAME='application_type' AND TABLE_NAME='plugin_bookings_discounts') ) THEN
        ALTER TABLE `plugin_bookings_discounts` ADD `application_type` ENUM('initial', 'latest') DEFAULT 'initial';
    END IF;
    IF NOT EXISTS( (SELECT * FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE()
                                                               AND COLUMN_NAME='application_type' AND TABLE_NAME='plugin_courses_discounts') ) THEN
        ALTER TABLE `plugin_courses_discounts` ADD `application_type` ENUM('initial', 'latest') DEFAULT 'initial';
    END IF;

END $$

CALL add_application_type() $$

DELIMITER ;
