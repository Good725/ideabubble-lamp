/*
ts:2019-06-14 13:00:00
*/

DELIMITER ;;
INSERT IGNORE INTO `engine_settings` (`variable`, `name`, `default`, `location`, `type`, `group`, `required`, `note`) VALUES
('body_html', 'Body HTML', NULL, 'both', 'textarea', 'SEO', '0', 'Content to be added immediately after the opening <code>&lt;body&gt;</code> tag.');;
