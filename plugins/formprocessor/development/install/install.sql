CREATE VIEW `view_plugin_contacts` AS select
  `plugin_contacts_contact`.`id` AS `id`,
	`plugin_contacts_contact`.`first_name` AS `first_name`,
	`plugin_contacts_contact`.`last_name` AS `last_name`,
	`plugin_contacts_contact`.`email` AS `email`,
	`plugin_contacts_contact`.`mailing_list` AS `mailing_list`,
	`plugin_contacts_contact`.`phone` AS `phone`,
	`plugin_contacts_contact`.`mobile` AS `mobile`,
	`plugin_contacts_contact`.`notes` AS `notes`,
	`plugin_contacts_contact`.`last_modification` AS `last_modification`,
	`plugin_contacts_mailing_list`.`name` AS `mailing_list_name`
FROM
	(
		`plugin_contacts_contact`
		JOIN `plugin_contacts_mailing_list`
	)
WHERE
	(
		`plugin_contacts_contact`.`mailing_list` = `plugin_contacts_mailing_list`.`id`
	)