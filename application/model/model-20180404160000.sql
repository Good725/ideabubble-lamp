/*
ts:2018-04-04 16:00:00
*/

INSERT INTO `engine_settings` (`variable`, `name`, `note`, `type`, `group`, `options`) VALUES
('cms_logo', 'CMS Logo', 'The logo to be used in the header of the CMS side of the site.', 'select', 'Engine', 'Model_Media,get_logos_as_options');

INSERT INTO `engine_settings` (`variable`, `name`, `note`, `type`, `group`, `options`) VALUES
('login_form_logo', 'Log-In Form Logo', 'The logo to be used in log-in forms.', 'select', 'Engine', 'Model_Media,get_logos_as_options');
