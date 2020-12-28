/*
ts: 2020-05-19 21:13:00
*/

INSERT IGNORE INTO `engine_settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`) VALUES
('share_document', 'Allow sharing documents', '0', '0', '0', '0', '0', 'Allow sharing  documents generated on backend', 'toggle_button', 'Documents', 'Model_Settings,on_or_off');

CREATE TABLE `plugin_ib_educate_contacts3_contact_has_files` (
    `id` INT NOT NULL AUTO_INCREMENT,
    `contact_id` INT NOT NULL,
    `document_id` INT NOT NULL,
    `shared` TINYINT(1) NOT NULL DEFAULT 0,
    `deleted` TINYINT(1) NULL DEFAULT 0,
PRIMARY KEY (`id`));


