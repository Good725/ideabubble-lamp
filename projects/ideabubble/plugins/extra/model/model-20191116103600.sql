/*
ts:2019-12-06 11:16:00
*/

DELIMITER ;
DROP PROCEDURE IF EXISTS alter_extra_projects_sprints2;

DELIMITER $$
CREATE PROCEDURE alter_extra_projects_sprints2()
BEGIN
    DECLARE _count INT;
    SET _count = (SELECT COUNT(*)
                  FROM INFORMATION_SCHEMA.COLUMNS
                  WHERE TABLE_NAME = 'plugin_extra_projects_sprints2'
                    AND COLUMN_NAME = 'deleted' AND TABLE_SCHEMA = DATABASE());
    IF _count = 0 THEN
        ALTER TABLE `plugin_extra_projects_sprints2`
            ADD COLUMN `deleted` TINYINT(1) NULL DEFAULT 0;
    END IF;
END $$
DELIMITER ;

CALL alter_extra_projects_sprints2();

DROP PROCEDURE IF EXISTS alter_extra_projects_sprints2;