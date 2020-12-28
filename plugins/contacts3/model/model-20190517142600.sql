/*
ts:2019-05-17 14:26:00
*/

ALTER TABLE `plugin_contacts3_contacts_subtypes`
ADD COLUMN `display_subtype` TINYINT(1) NOT NULL DEFAULT 0 AFTER `subtype`;

INSERT INTO `plugin_contacts3_contacts_subtypes` (`type_id`, `subtype`, `display_subtype`) VALUES ('1', 'Other Accommodation', '1');
INSERT INTO `plugin_contacts3_contacts_subtypes` (`type_id`, `subtype`, `display_subtype`) VALUES ('1', 'Host Family', '1');
INSERT INTO `plugin_contacts3_contacts_subtypes` (`type_id`, `subtype`, `display_subtype`) VALUES ('1', 'Agent', '1');
INSERT INTO `plugin_contacts3_contacts_subtypes` (`type_id`, `subtype`, `display_subtype`) VALUES ('1', 'Work Placement', '1');
INSERT INTO `plugin_contacts3_contacts_subtypes` (`type_id`, `subtype`, `display_subtype`) VALUES ('1', 'Coordinator', '1');
INSERT INTO `plugin_contacts3_contacts_subtypes` (`type_id`, `subtype`, `display_subtype`) VALUES ('1', 'Transport', '1');
INSERT INTO `plugin_contacts3_contacts_subtypes` (`type_id`, `subtype`, `display_subtype`) VALUES ('1', 'Schools', '1');
INSERT INTO `plugin_contacts3_contacts_subtypes` (`type_id`, `subtype`, `display_subtype`) VALUES ('1', 'Suppliers', '1');
INSERT INTO `plugin_contacts3_contacts_subtypes` (`type_id`, `subtype`, `display_subtype`) VALUES ('1', 'Tour Provider', '1');
