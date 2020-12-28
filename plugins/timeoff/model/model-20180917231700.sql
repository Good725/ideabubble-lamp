/*
ts:2018-09-17 23:17:00
*/

ALTER TABLE `plugin_contacts3_contacts`
	ADD INDEX `linked_user_id` (`linked_user_id`);

insert into plugin_contacts3_contact_type (label) values ('Business'), ('Department');

