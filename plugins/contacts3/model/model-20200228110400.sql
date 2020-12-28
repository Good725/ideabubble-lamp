/*
ts:2020-02-28 11:04:00
*/

INSERT INTO `plugin_contacts3_contact_type_columns` (`name`, `label`, `table_column`)
VALUES ('organisation_name', 'Organisation', 'linked_organisation_contact.first_name');

INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `name` = 'org_rep' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'contact_id' LIMIT 1), 1);

INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `name` = 'org_rep' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'full_name' LIMIT 1), 2);

INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `name` = 'org_rep' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'organisation_name' LIMIT 1), 3);

INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `name` = 'org_rep' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'email' LIMIT 1), 4);

INSERT INTO `plugin_contacts3_contact_type_has_columns` (`contact_type_id`, `contact_type_column_id`, `priority`)
VALUES ((SELECT `contact_type_id` FROM plugin_contacts3_contact_type where `name` = 'org_rep' LIMIT 1),
        (SELECT `id` FROM plugin_contacts3_contact_type_columns where `name` = 'mobile' LIMIT 1), 5);