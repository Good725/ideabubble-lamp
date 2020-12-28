/*
ts:2019-11-20 11:16:00
*/
DELIMITER ;
DROP PROCEDURE IF EXISTS alter_payments_log_table_check_implementation;

DELIMITER $$
CREATE PROCEDURE alter_payments_log_table_check_implementation()
BEGIN
    DECLARE _count INT;
    SET _count = (SELECT COUNT(*)
                  FROM INFORMATION_SCHEMA.COLUMNS
                  WHERE TABLE_NAME = 'plugin_payments_log'
                    AND COLUMN_NAME = 'student' AND TABLE_SCHEMA = DATABASE());
    IF _count = 0 THEN
        ALTER TABLE `plugin_payments_log`
            ADD COLUMN `student` varchar(100) NULL DEFAULT NULL;
    END IF;
END $$
DELIMITER ;

CALL alter_payments_log_table_check_implementation();

DROP PROCEDURE IF EXISTS alter_payments_log_table_check_implementation;