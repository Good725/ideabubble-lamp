/*
ts:2015-01-01 00:00:04
*/
UPDATE `settings`
JOIN `ppages` ON `settings`.`value_dev` = `ppages`.`id`
SET `value_dev` = ''
WHERE `settings`.`variable` = 'default_home_page' AND `ppages`.`title` = 'about-us';

UPDATE `settings`
JOIN `ppages` ON `settings`.`value_test` = `ppages`.`id`
SET `value_test` = ''
WHERE `settings`.`variable` = 'default_home_page' AND `ppages`.`title` = 'about-us';

UPDATE `settings`
JOIN `ppages` ON `settings`.`value_stage` = `ppages`.`id`
SET `value_stage` = ''
WHERE `settings`.`variable` = 'default_home_page' AND `ppages`.`title` = 'about-us';

UPDATE `settings`
JOIN `ppages` ON `settings`.`value_live` = `ppages`.`id`
SET `value_live` = ''
WHERE `settings`.`variable` = 'default_home_page' AND `ppages`.`title` = 'about-us';

-- IBCMS-547
CREATE TABLE `engine_cron_log`
(
	`id`		INT AUTO_INCREMENT PRIMARY KEY,
	`cron_id`	INT NOT NULL,
	`started`	DATETIME NOT NULL,
	`finished`	DATETIME,
	`output`	MEDIUMTEXT,

	KEY		(`cron_id`)
) ENGINE = INNODB;

ALTER IGNORE TABLE `users` ADD COLUMN `timezone` VARCHAR(100) NULL DEFAULT 'Europe/Dublin'  AFTER `country` ;

UPDATE IGNORE `settings` SET `value_dev` = 'TRUE', `value_live` = 'TRUE', `value_stage` = 'TRUE',`value_test` = 'TRUE', `default` = 'TRUE' WHERE `variable` = 'show_footer_ib_engine';
UPDATE IGNORE `settings` SET `value_dev` = 'TRUE', `value_live` = 'TRUE', `value_stage` = 'TRUE',`value_test` = 'TRUE', `default` = 'TRUE' WHERE `variable` = 'show_footer_ib_project';

INSERT IGNORE INTO `settings`
(`variable`,           `name`,               `note`,                                       `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `type`, `group`, `options`) VALUES
('show_donate_button', 'Show Donate Button', '',                                           '0',          '0',           '0',          '0',         '0', 'toggle_button', 'Donations', 'Model_Settings,on_or_off'),
('donation_page',      'Donation Page',      'Page that the donation button will link to', '',           '',            '',           '',          '',   'combobox',     'Donations', 'Model_Pages,get_pages_as_options');

ALTER IGNORE TABLE `users` ADD COLUMN `datatable_length_preference` INT NULL DEFAULT NULL  AFTER `default_dashboard_id` ;

DELETE FROM `settings` WHERE `variable`='frontend_theme';
DELETE FROM `settings` WHERE `variable`='cms_theme';

ALTER IGNORE TABLE `settings` ADD COLUMN `linked_plugin_name` VARCHAR(64) AFTER `name`;
UPDATE IGNORE settings SET linked_plugin_name = 'cars' WHERE `group` = 'Cars';
UPDATE IGNORE settings SET linked_plugin_name = 'formbuilder' WHERE `group` = 'Forms';
UPDATE IGNORE settings SET linked_plugin_name = 'messaging' WHERE `group` = 'Mandrill Settings';
UPDATE IGNORE settings SET linked_plugin_name = 'news' WHERE `group` = 'News';
UPDATE IGNORE settings SET linked_plugin_name = 'paybackloyalty' WHERE `group` = 'Payback Loyalty';
UPDATE IGNORE settings SET linked_plugin_name = 'payments' WHERE `group` = 'Paypal Settings';
UPDATE IGNORE settings SET linked_plugin_name = 'products' WHERE `group` = 'Products';
UPDATE IGNORE settings SET linked_plugin_name = 'payments' WHERE `group` = 'Realex Settings';
UPDATE IGNORE settings SET linked_plugin_name = 'sagepay' WHERE `group` = 'SagePay Settings';
UPDATE IGNORE settings SET linked_plugin_name = 'payments' WHERE `group` = 'Shop Checkout';
UPDATE IGNORE settings SET linked_plugin_name = 'products' WHERE `group` = 'Sign Builder';
UPDATE IGNORE settings SET linked_plugin_name = 'payments' WHERE `group` = 'Stripe';
UPDATE IGNORE settings SET linked_plugin_name = 'messaging' WHERE `group` = 'Twilio Settings';
UPDATE IGNORE settings SET linked_plugin_name = 'Word to PDF Conversion' WHERE `group` = 'Word Template Generation';
UPDATE IGNORE settings SET linked_plugin_name = 'Word to PDF Conversion' WHERE `group` = 'Word to PDF Conversion';
UPDATE IGNORE settings SET linked_plugin_name = 'dashboards' WHERE `group` = 'Dashboard';


UPDATE IGNORE settings SET linked_plugin_name = 'documents' WHERE `group` = 'Word Template Generation';
UPDATE IGNORE settings SET linked_plugin_name = 'documents' WHERE `group` = 'Word to PDF Conversion';
