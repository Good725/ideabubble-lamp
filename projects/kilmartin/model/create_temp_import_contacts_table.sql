DROP TABLE IF EXISTS `temp_import_contacts`;
CREATE TABLE `temp_import_contacts`(
`id` INT(11) AUTO_INCREMENT,
`full_name` VARCHAR(255),
`first_name` VARCHAR(255),
`address1` VARCHAR(255),
`address2` VARCHAR(255),
`address3` VARCHAR(255),
`address4` VARCHAR(255),
`address5` VARCHAR(255),
`phone_number` VARCHAR(127),
`email`VARCHAR(127),
`student_full_name` VARCHAR(255),
`student_mobile` VARCHAR(127),
`student_first_name` VARCHAR(255),
`relationship` VARCHAR(127),
`year` VARCHAR(127),
`school` VARCHAR(255),
`mother_mobile` VARCHAR(127),
`father_mobile` VARCHAR(127),
`father_first_name` VARCHAR(255),
`mother_first_name` VARCHAR(255),
PRIMARY KEY (`id`)
);
