/*
ts:2018-03-07 19:20:00
*/

ALTER TABLE `plugin_courses_locations`
	MODIFY COLUMN `address1`  varchar(200) NULL,
	MODIFY COLUMN `address2`  varchar(200) NULL,
	MODIFY COLUMN `address3`  varchar(200) NULL,
	MODIFY COLUMN `county_id`  int(10) UNSIGNED NULL,
	MODIFY COLUMN `city_id`  int(10) UNSIGNED NULL;

