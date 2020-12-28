/*
ts:2016-12-19 15:00:00
*/

-- No columns for deleted, modification date, etc. These will all be covered by the main contacts table
CREATE TABLE `plugin_courses_contacts_table_extended` (
  `contact_id` VARCHAR(45) NOT NULL ,
  `flexi_student` INT(1) NOT NULL DEFAULT 0 ,
  `academic_year_id` INT(11) NULL ,
  `family_role` INT(11) NULL ,
  PRIMARY KEY (`contact_id`) ,
  UNIQUE INDEX `contact_id_UNIQUE` (`contact_id` ASC) );

ALTER TABLE `plugin_courses_contacts_table_extended`
  CHANGE COLUMN `family_role` `family_role_id` INT(11) NULL DEFAULT NULL;