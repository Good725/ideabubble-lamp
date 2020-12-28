/*
ts:2016-08-18 14:20:00
*/
INSERT IGNORE INTO `engine_settings` (`variable`, `name`, `note`, `type`, `group`) VALUES
('cms_heading_button_link', 'Action button link', 'Add an extra button in the CMS header, which links to this location', 'text', 'Engine'),
('cms_heading_button_text', 'Action button text', 'The text to display in the extra button in the CMS header',           'text', 'Engine');

UPDATE IGNORE `engine_plugins` SET `show_on_dashboard`='0' WHERE `name`='settings';
