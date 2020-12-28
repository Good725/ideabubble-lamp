/*
ts:2015-01-01 00:00:00
*/
-- -----------------------------------------------------
-- Table `loginlogs`
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS `loginlogs` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `ip_address` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `email` VARCHAR(254) NOT NULL ,
  `time` INT(11) NOT NULL ,
  `success` TINYINT NOT NULL ,
  `user_agent` VARCHAR(254) NOT NULL ,
  `session` VARCHAR(32) NULL DEFAULT NULL ,
  PRIMARY KEY (`id`) )
  ENGINE = InnoDB;

--
-- Table `Project Role

CREATE TABLE IF NOT EXISTS `project_role` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `role` VARCHAR(45) NOT NULL ,
  `description` VARCHAR(45) NULL DEFAULT NULL ,
  `publish` TINYINT NOT NULL DEFAULT '1' ,
  `deleted` TINYINT NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) ,
  UNIQUE `project_role_idx_1` (`role` ASC) )
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `permissions`
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS `permissions` (
  `id` INT(16) NOT NULL AUTO_INCREMENT ,
  `role_id` INT(16) NOT NULL ,
  `plugin_name` VARCHAR(64) NULL DEFAULT NULL ,
  `permission_code` TEXT NOT NULL ,
  PRIMARY KEY (`id`) )
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `settings`
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS `settings` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `variable` VARCHAR(64) NULL DEFAULT NULL ,
  `name` VARCHAR(64) NULL DEFAULT NULL ,
  `value_live` LONGTEXT NULL DEFAULT NULL ,
  `value_stage` LONGTEXT NULL DEFAULT NULL ,
  `value_test` LONGTEXT NULL DEFAULT NULL ,
  `value_dev` LONGTEXT NULL DEFAULT NULL ,
  `default` LONGTEXT NULL DEFAULT NULL ,
  `location` VARCHAR(64) NOT NULL DEFAULT 'both' ,
  `note` TEXT NULL DEFAULT NULL ,
  `type` VARCHAR(64) NOT NULL ,
  `group` VARCHAR(64) NOT NULL ,
  `required` TINYINT NOT NULL DEFAULT '0' ,
  `options` LONGTEXT NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE `settings_idx_1` (`variable` ASC) )
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `user_group`
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS `user_group` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `user_group` VARCHAR(45) NOT NULL ,
  `description` VARCHAR(120) NULL DEFAULT NULL ,
  `publish` TINYINT(1) NOT NULL DEFAULT '1' ,
  `deleted` TINYINT(1) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) )
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `users`
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS `users` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `role_id` INT(16) NOT NULL ,
  `group_id` INT(10) UNSIGNED ZEROFILL NULL DEFAULT NULL ,
  `email` VARCHAR(254) NOT NULL ,
  `password` VARCHAR(64) NOT NULL ,
  `logins` INT(10) UNSIGNED NOT NULL DEFAULT '0' ,
  `last_login` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `logins_fail` INT(10) NOT NULL DEFAULT '0' ,
  `last_fail` INT(10) NULL DEFAULT NULL ,
  `name` VARCHAR(50) NULL DEFAULT NULL ,
  `surname` VARCHAR(50) NULL DEFAULT NULL ,
  `address` TINYTEXT NULL DEFAULT NULL ,
  `phone` VARCHAR(50) NULL DEFAULT NULL ,
  `registered` DATETIME NULL DEFAULT NULL ,
  `can_login` TINYINT NOT NULL DEFAULT '1' ,
  `deleted` TINYINT NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `uniq_email` (`email` ASC) ,
  INDEX `can_login` (`can_login` ASC) ,
  INDEX `deleted` (`deleted` ASC) )
  ENGINE = InnoDB;

ALTER IGNORE TABLE `users` MODIFY `can_login` TINYINT NOT NULL DEFAULT '1';
ALTER IGNORE TABLE `users` MODIFY `deleted` TINYINT NOT NULL DEFAULT '0';

-- -----------------------------------------------------
-- Table `user_tokens`
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS `user_tokens` (
  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `user_id` INT(11) UNSIGNED NOT NULL ,
  `user_agent` VARCHAR(40) NOT NULL ,
  `token` VARCHAR(64) NOT NULL ,
  `type` VARCHAR(100) NOT NULL ,
  `created` INT(10) UNSIGNED NOT NULL ,
  `expires` INT(10) UNSIGNED NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `uniq_token` (`token` ASC) ,
  INDEX `fk_user_id` (`user_id` ASC) ,
  CONSTRAINT `user_tokens_ibfk_1`
  FOREIGN KEY (`user_id` )
  REFERENCES `users` (`id` )
    ON DELETE CASCADE)
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table `plugins_per_role`
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS `plugins_per_role` (
  `plugin_id` INT NOT NULL ,
  `role_id` INT NOT NULL ,
  `enabled` TINYINT NOT NULL ,
  PRIMARY KEY (`role_id`, `plugin_id`) )
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Redefinition of table `plugins`
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS `plugins` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(64) NOT NULL ,
  `friendly_name` VARCHAR(64) NOT NULL ,
  `show_on_dashboard` TINYINT(1) NOT NULL DEFAULT '1',
  `requires_media` TINYINT(1) NOT NULL DEFAULT '0' ,
  `media_folder` VARCHAR(64) NULL DEFAULT NULL ,
  `icon` VARCHAR(256) NULL DEFAULT NULL,
  `order` INT(11) NULL DEFAULT NULL,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `name` (`name` ASC) )
  ENGINE = InnoDB;

-- -----------------------------------------------------
-- Roles
-- -----------------------------------------------------

INSERT IGNORE INTO `project_role`(`role`,`description`,`publish`,`deleted`) VALUES
  ('Super User', 'System-wide access to engine properties.', '1', '0'),
  ('Administrator', 'This is the top level user.', '1', '0'),
  ('External User', 'This user has no access to Settings.', '1', '0');

-- -----------------------------------------------------
-- Permissions
-- -----------------------------------------------------

INSERT INTO `permissions`(`role_id`,`plugin_name`,`permission_code`)
  SELECT * FROM (SELECT `id`, NULL, 'super_level' FROM `project_role` WHERE `role` LIKE 'Super User') AS `t`
  WHERE NOT EXISTS (
      SELECT `id` FROM `permissions` WHERE `permission_code` = 'super_level' AND `role_id` = `t`.id
  ) LIMIT 1;

INSERT INTO `permissions`(`role_id`,`plugin_name`,`permission_code`)
  SELECT * FROM (SELECT `id`, NULL, 'access_settings' FROM `project_role` WHERE `role` LIKE 'Administrator') AS `t`
  WHERE NOT EXISTS (
      SELECT `id` FROM `permissions` WHERE `permission_code` = 'access_settings' AND `role_id` = `t`.id
  ) LIMIT 1;

-- -----------------------------------------------------
-- Users
-- -----------------------------------------------------

INSERT IGNORE INTO `users`(`role_id`,`group_id`,`email`,`password`,`logins`,`last_login`,`logins_fail`,`last_fail`,`name`,`surname`,`address`,`phone`,`registered`,`can_login`,`deleted`) VALUES
  ('0', NULL, 'super@ideabubble.com', 'c31c224ca045584b1a57abc0adc9cc8fd5019eaf3db2b591c973dd3f10273a63', '0', null, '0', null, null, null, null, null, NOW(), '1', '0'),
  ('0', NULL, 'admin@ideabubble.com', 'c31c224ca045584b1a57abc0adc9cc8fd5019eaf3db2b591c973dd3f10273a63', '0', null, '0', null, null, null, null, null, NOW(), '1', '0'),
  ('0', NULL, 'test@ideabubble.com', 'c31c224ca045584b1a57abc0adc9cc8fd5019eaf3db2b591c973dd3f10273a63', '0', null, '0', null, null, null, null, null, NOW(), '1', '0');

UPDATE IGNORE `users` SET `role_id` = (SELECT `id` FROM `project_role` WHERE `role` LIKE 'Super User') WHERE `email` = 'super@ideabubble.com';
UPDATE IGNORE `users` SET `role_id` = (SELECT `id` FROM `project_role` WHERE `role` LIKE 'Administrator') WHERE `email` = 'admin@ideabubble.com';
UPDATE IGNORE `users` SET `role_id` = (SELECT `id` FROM `project_role` WHERE `role` LIKE 'External User') WHERE `email` = 'test@ideabubble.com';

-- -----------------------------------------------------
-- Plugins/Role
-- -----------------------------------------------------

INSERT IGNORE INTO `plugins_per_role` (`plugin_id`, `role_id`, `enabled`)
  SELECT `plugins`.`id`, `project_role`.`id`, 1 FROM `plugins`, `project_role`;

-- -----------------------------------------------------
-- Settings
-- -----------------------------------------------------

INSERT IGNORE INTO `settings`(`variable`,`name`,`value_live`,`value_stage`,`value_test`,`value_dev`,`default`,`location`,`note`,`type`,`group`,`required`,`options`) VALUES
  ('media_url', 'Media Url', '', '', '', '', '', 'both', 'The URL where your static media content will be stored from.', 'text', 'General Settings', '0', ''),
  ('project_name', 'Project Name', '', '', '', '', '', 'both', 'The name of the Project', 'text', 'General Settings', '0', ''),
  ('cms_theme', 'CMS Theme', '', '', '', '', '', 'cms', 'The theme to use for the CMS', 'text', 'General Settings', '0', ''),
  ('frontend_theme', 'Frontend Theme', '', '', '', '', '', 'site', 'the theme to use for the website fontend.', 'text', 'General Settings', '0', ''),
  ('google_map_key', 'Google Maps API Key', '', '', '', '', '', 'both', 'This key is need to show Google Maps on your site. New keys can be requested from http://code.google.com/apis/maps/signup.html', 'text', 'Google', '0', ''),
  ('google_analitycs_code', 'Analitycs Code', '', '', '', '', '', 'both', '', 'text', 'Google', '0', ''),
  ('google_webmaster_code', 'Google Webmaster Code', '', '', '', '', '', 'both', '', 'text', 'Google', '0', ''),
  ('bing_webmaster_code', 'Bing Webmaster Code', '', '', '', '', '', 'both', '', 'text', 'Google', '0', ''),
  ('google_conversion_id', 'Google Conversion ID', '', '', '', '', '', 'both', '', 'text', 'Google', '0', ''),
  ('google_conversion_label', 'Google Conversion Label', '', '', '', '', '', 'both', '', 'text', 'Google', '0', ''),
  ('enable_frontend', 'Enable Frontend', '', '', '', '', 'FALSE', 'site', 'Tick to enable a web frontend or a public website', 'checkbox', 'General Settings', '1', ''),
  ('cms_footer_html', 'Website Platform Footer HTML', '', '', '', '', '', 'both', 'Allows for custom html footer injection', 'textarea', 'Website Platform Settings', '0', ''),
  ('realex_username', 'Realex Username', '', '', '', '', '', 'both', 'Please add Realex Username', 'text', 'Realex Settings', '1', ''),
  ('realex_secret_key', 'Realex API Key', '', '', '', '', '', 'both', 'Please add Realex API key', 'text', 'Realex Settings', '1', ''),
  ('realex_mode', 'Realex Mode', '', '', '', '', '', 'both', 'Either: internettest for Testing, or internet for LIVE', 'text', 'Realex Settings', '1', ''),
  ('paypal_email', 'Email address', '', '', '', '', '', 'both', 'Please add Paypal Email', 'text', 'Paypal Settings', '0', ''),
  ('paypal_api_key', 'API Key', '', '', '', '', '', 'both', 'Please add Paypal API Key', 'text', 'Paypal Settings', '0', ''),
  ('addres_line_1', 'Address line 1', '', '', '', '', '', 'both', '', 'text', 'Contact Us', '0', ''),
  ('addres_line_2', 'Address line 2', '', '', '', '', '', 'both', '', 'text', 'Contact Us', '0', ''),
  ('addres_line_3', 'Address line 3', '', '', '', '', '', 'both', '', 'text', 'Contact Us', '0', ''),
  ('telephone', 'Telephone', '', '', '', '', '', 'both', '', 'text', 'Contact Us', '0', ''),
  ('fax', 'Fax', '', '', '', '', '', 'both', '', 'text', 'Contact Us', '0', ''),
  ('email', 'Email', '', '', '', '', '', 'both', '', 'text', 'Contact Us', '0', ''),
  ('cookie_enabled', 'Cookies Enabled', 'FALSE', 'FALSE', 'FALSE', 'FALSE', 'FALSE', 'both', 'Enable cookies on the front end.', 'checkbox', 'Cookie Notice', '0', ''),
  ('cookie_page', 'Cookies Policy Page', 'SELECT', 'SELECT', 'SELECT', 'SELECT', 'SELECT', 'both', 'Select the page that holds your site cookie policy.', 'select', 'Cookie Notice', '0', 'Model_Pages,get_pages_as_options'),
  ('link_text', 'Link Text', '', '', '', '', '', 'both', 'Text to display on anchor tag link to policy page.', 'text', 'Cookie Notice', '0', ''),
  ('cookie_text', 'Cookie Banner Text', '', '', '', '', '', 'both', 'Text to display on the cookie banner.', 'text', 'Cookie Notice', '0', ''),
  ('hide_notice_message', 'Hide message button text', '', '', '', '', '', 'both', 'Button to hide text and create a policy cookie.', 'text', 'Cookie Notice', '0', ''),
  ('admin_fee_toggle','Admin Fee','FALSE','FALSE','FALSE','FALSE','Turn on administration/booking fees.','both','','checkbox','admin_fee','0',''),
  ('admin_fee_price','Admin Fee Price','0','0','0','0','Set administration/booking fee.','both','','text','admin_fee','0',''),
  ('admin_fee_currency','Admin Fee Currency','€','€','€','€','Administration fee currency.','both','','select','admin_fee','0','Model_Settings,get_currency_list');


 INSERT IGNORE INTO `settings`(`variable`,`name`,`value_live`,`value_stage`,`value_test`,`value_dev`,`default`,`location`,`note`,`type`,`group`,`required`,`options`) VALUES
  ('smtp_server', 'Server', '', '', '', '', '', 'both', '', 'text', 'SMTP Authentication', '0', ''),
  ('smtp_user', 'User', '', '', '', '', '', 'both', '', 'text', 'SMTP Authentication', '0', ''),
  ('smtp_default_sender', 'Default Sender', '', '', '', '', 'support@ideabubble.ie', 'both', '', 'text', 'SMTP Authentication', '0', ''),
  ('smtp_password', 'Password', '', '', '', '', '', 'both', '', 'text', 'SMTP Authentication', '0', ''),
  ('smtp_port', 'Port', '', '', '', '', '', 'both', 'Note port changes based on Encryption Type', 'text', 'SMTP Authentication', '0', ''),
  ('smtp_auth_mode', 'Authentication Mode', '', '', '', '', '', 'both', 'The encryption mode to use when using smtp as the transport. Valid values are tls, ssl, or null (indicating no encryption).', 'text', 'SMTP Authentication', '0', ''),
  ('smtp_encryption', 'Encryption', '', '', '', '', '', 'both', ' The authentication mode to use when using smtp as the transport. Valid values are plain, login, cram-md5, or null', 'text', 'SMTP Authentication', '0', '');

-- --------------------------------------------------------------------------------
--  WPPROD-230
--  Social Media settings
-- --------------------------------------------------------------------------------

INSERT IGNORE INTO `settings` (`variable`, `name`, `value_dev`, `location`, `note`, `type`, `group`, `required`, `options`) VALUES
	('addthis_id', 'AddThis ID', '', 'both', 'Your AddThis username', 'text', 'Social Media', '0', ''),
	('facebook_url', 'Facebook URL', '', 'both', 'The full URL to your Facebook page or just the  portion that comes after &quot;facebook.com/&quot;', 'text', 'Social Media', '0', ''),
	('twitter_url', 'Twitter URL', '', 'both', 'The full URL to your Twitter page or simply your user name', 'text', 'Social Media', '0', ''),
	('linkedin_url', 'LinkedIn URL', '', 'both', 'The full URL to your LinkedIn page', 'text', 'Social Media', '0', ''),
	('flickr_url', 'Flickr URL', '', 'both', 'The full URL to your your Flickr page or simply your username', 'text', 'Social Media', '0', '');


-- --------------------------------------------------------------------------------
--  WPPROD-93
--  updates icon colunm with icon image names
-- --------------------------------------------------------------------------------


UPDATE `plugins` SET icon = 'files.png' WHERE friendly_name = 'Files';

















-- --------------------------------------------------------------------------------
--  WPPROD-99
--  Toggle search engine indexing
-- --------------------------------------------------------------------------------
INSERT IGNORE INTO `settings`(`variable`,`name`,`value_live`,`value_stage`,`value_test`,`value_dev`,`default`,`location`,`note`,`type`,`group`,`required`,`options`) VALUES
('search_engine_indexing','Toggle Search Engine Indexing','TRUE','TRUE','TRUE','TRUE','Choose whether Search Engine indexing is switched on or off on your website','both','','checkbox','Search Engine','0','');

-- --------------------------------------------------------------------------------
-- WPPROD-101
--  update order of icons on dashboard
-- --------------------------------------------------------------------------------


UPDATE `plugins` SET `plugins`.`order` = 13 WHERE friendly_name = 'Files';















UPDATE `plugins` SET `plugins`.`order` = 1 WHERE friendly_name = 'Articles';

-- ---------------------------------------------------------------------------------
-- Set dummy images for icons with no images
-- ---------------------------------------------------------------------------------
UPDATE `plugins` SET icon = 'news.png' WHERE friendly_name = 'Payback Loyalty';
UPDATE `plugins` SET icon = 'news.png' WHERE friendly_name = 'Uploader';
UPDATE `plugins` SET icon = 'news.png' WHERE friendly_name = 'Form Processor';

-- ---------------------------------------------------------------------------------
-- rename admin_ to  a nicer name :)
-- ---------------------------------------------------------------------------------
UPDATE `settings` SET `settings`.`group` = 'Shop Checkout' WHERE variable in ('admin_fee_toggle','admin_fee_price','admin_fee_currency');

-- Update settings to set front-end enabled by default.

UPDATE `settings` SET `default` = 'TRUE' WHERE `variable` = 'enable_frontend';

INSERT IGNORE INTO `settings`(`variable`,`name`,`value_live`,`value_stage`,`value_test`,`value_dev`,`default`,`location`,`note`,`type`,`group`,`required`,`options`) VALUES
  ('cart_logging','Cart Logging','FALSE','TRUE','TRUE','TRUE','Turn on/off shopping cart logging.','both','','checkbox','Shop Checkout','0','');

-- add company name, slogan, mobile number for template sites to use dynamically
INSERT IGNORE INTO `settings`(`variable`,`name`,`value_live`,`value_stage`,`value_test`,`value_dev`,`default`,`location`,`note`,`type`,`group`,`required`,`options`) VALUES
  ('mobile','Mobile','','','','','','both','','text','Contact Us','0',''),
  ('company_slogan','Company Slogan','Company Slogan','Company Slogan','Company Slogan','Company Slogan','Company Slogan','both','','text','Contact Us','0',''),
  ('company_title','Company Title','Company Title','Company Title','Company Title','Company Title','Company Title','both','','text','Contact Us','0','');

-- --------------------------------------------------------------------------------
--  DALM setting to force it to keep going if errors
-- --------------------------------------------------------------------------------
INSERT IGNORE INTO `settings`(`variable`,`name`,`value_live`,`value_stage`,`value_test`,`value_dev`, `default`,`location`,`note`,`type`,`group`,`required`,`options`) VALUES
  ('dalm_stop_on_error','DALM Force Continue', 'FALSE','TRUE','TRUE','TRUE','Turn on/off to force dalm to continue if error found, YES forces it to continue.','both','','checkbox','DALM','0','');

-- --------------------------------------------------------------------------------
--  ALB-65 Setting for switching column positions
-- --------------------------------------------------------------------------------
INSERT IGNORE INTO `settings` (`id`, `variable`, `name`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) VALUES ('', 'content_location', 'Main column location', NULL, 'right', 'both', 'Position of the main content area. The sidebar will appear on the other side.', 'select', 'Home Layout', '0', 'Model_Settings,left_or_right');

-- add company footer signature and copyright as settings for further flexibility
INSERT IGNORE INTO `settings`(`variable`,`name`,`value_live`,`value_stage`,`value_test`,`value_dev`,`default`,`location`,`note`,`type`,`group`,`required`,`options`) VALUES
  ('company_signature','Company Signature','Company Signature','Company Signature','Company Signature','Company Signature','This can appear on the company details panel on the site','both','','text','Contact Us','0',''),
  ('company_copyright','Company Copyright','&copy; Company Name','&copy; Company Name','&copy; Company Name','&copy; Company Name','This can appear on the company copyright section on the site','both','','text','Contact Us','0','');


INSERT IGNORE INTO `settings`(`variable`,`name`,`value_live`,`value_stage`,`value_test`,`value_dev`,`default`,`location`,`note`,`type`,`group`,`required`,`options`) VALUES
('captcha_enabled','Enabled','','','','','false','both','Check this box to enable the site captcha.','checkbox','Captcha','0',''),
('captcha_public_key','Public Key','','','','','false','both','Enter your reCAPTCHA public key here.','text','Captcha','0',''),
('captcha_private_key','Private Key','','','','','false','both','Enter your reCAPTCHA private key here.','text','Captcha','0',''),
('captcha_fail_page', 'Failure Page', 'SELECT', 'SELECT', 'SELECT', 'SELECT', 'SELECT', 'both', 'Select the page that holds your captcha failure page.', 'select', 'Captcha', '0', 'Model_Pages,get_pages_as_options');

-- ----------------------------------
-- WPPROD-263 Feeds Manager
-- ----------------------------------
CREATE TABLE IF NOT EXISTS `feeds` (
`id` INT(11) NOT NULL AUTO_INCREMENT ,
`name` VARCHAR(255) NOT NULL ,
`summary` VARCHAR(255) NULL ,
`code_path` VARCHAR(255) NULL ,
`order` INT(11) NULL ,
`date_created` DATETIME NULL ,
`date_modified` DATETIME NULL ,
`created_by` INT(11) NULL ,
`modified_by` INT(11) NULL ,
`publish` TINYINT(1) NOT NULL ,
`deleted` TINYINT(1) NOT NULL ,
PRIMARY KEY (`id`) );

CREATE TABLE IF NOT EXISTS `plugin_feeds` (
`id` INT NOT NULL AUTO_INCREMENT ,
`plugin_id` INT(11) NOT NULL ,
`feed_id` INT(11) NOT NULL ,
`date_created` DATETIME NULL ,
`date_modified` DATETIME NULL ,
`created_by` INT(11) NULL ,
`modified_by` INT(11) NULL ,
`publish` TINYINT(1) NOT NULL ,`deleted` TINYINT(1) NOT NULL ,
PRIMARY KEY (`id`) );

INSERT INTO `feeds` (`name`, `summary`, `code_path`, `order`, `date_created`, `date_modified`, `publish`, `deleted`) VALUES
('Testimonials', 'Latest Testimonials feed', 'engine/plugins/testimonials/development/views/front_end/testimonials_feed_view', '0', CURDATE(), CURDATE(), '1', '0'),
('News',         'Latest News feed',         'engine/plugins/news/development/views/front_end/news_feed_view',                 '0', CURDATE(), CURDATE(), '1', '0'),
('Panels',       'Panels feed',              'engine/plugins/panels/development/views/front_end/panels_feed_view',             '0', CURDATE(), CURDATE(), '1', '0');

INSERT INTO `plugin_feeds` (`plugin_id`, `feed_id`, `publish`, `deleted`)
VALUES
(
 (SELECT `id` FROM `plugins` WHERE name = 'news' LIMIT 1),
 (SELECT `id` FROM `feeds` WHERE name = 'News' LIMIT 1),
 1, 0
),
(
 (SELECT `id` FROM `plugins` WHERE name = 'testimonials' LIMIT 1),
 (SELECT `id` FROM `feeds` WHERE name = 'Testimonials' LIMIT 1),
 1, 0
),
(
 (SELECT `id` FROM `plugins` WHERE name = 'panels' LIMIT 1),
 (SELECT `id` FROM `feeds` WHERE name = 'Panels' LIMIT 1),
 1, 0
);


INSERT IGNORE INTO `settings`(`variable`,`name`,`value_live`,`value_stage`,`value_test`,`value_dev`,`default`,`location`,`note`,`type`,`group`,`required`,`options`) VALUES
  ('responsive_enabled', 'Responsive Site', 'false', 'false', 'false', 'false', 'false', 'both', 'Tick to enable a responsive website.', 'checkbox', 'General Settings', '0', '');

-- ----------------------------------
-- WPPROD-326 SMTP password masking
-- ----------------------------------
UPDATE `settings` SET `type`='password' WHERE `variable`='smtp_password';

-- ----------------------------------
-- WPPROD-291 Setting to add project name to title tags
-- ----------------------------------
INSERT IGNORE INTO `settings` (`variable`, `name`, `default`, `location`, `type`, `group`, `required`, `options`, `note`) VALUES
('seo_title_text',           'Title',           NULL,   'both', 'text',     'SEO', '0', NULL,                           'The text to appear in the browser toolbar, tabs, bookmarks and search engine results.\\n\\nThe text entered here will appear in addition to the name of each individual page.'),
('seo_title_text_position',  'Title Position',  'left', 'both', 'select',   'SEO', '0', 'Model_Settings,left_or_right', 'Do you want this text to be appended (left) or prepended (right) to the page title?'),
('seo_title_text_separator', 'Title Separator', '|',    'both', 'text',     'SEO', '0', NULL,                           'The separator you wish to use between the pagename and the above text'),
('seo_keywords',             'Keywords',        NULL,   'both', 'textarea', 'SEO', '0', NULL,                           'A comma separated list of keywords'),
('seo_description',          'Description',     NULL,   'both', 'textarea', 'SEO', '0', NULL,                           'A description of the site to appear in search engine results.'),
('head_html',                'Head HTML',       NULL,   'both', 'textarea', 'SEO', '0', NULL,                           'Content to be added to the HTML head tags. Do not put text in here, unless you fully understand what you are doing.');

UPDATE `settings` SET `name`='Search Engine Indexing', `group`='SEO', `note`='Check this box to allow the site to appear in search engine results' WHERE `variable` = 'search_engine_indexing';

-- WPPROD-375  Setting needed for tickets
INSERT IGNORE INTO `settings` (`variable`, `name`, `default`, `location`, `type`, `group`, `required`, `options`, `note`) VALUES
('jira_url',           'Jira URL',           'http://jira.ideabubble.ie/',   'both', 'text',     'JIRA', '0', NULL,'The Jira Web Application address.'),
('jira_username',      'User Name',         'Michael',   'both', 'text',     'JIRA', '0', NULL,                         'The user name for the JIRA API'),
('jira_password',      'Password',          'atlasmjdoc1978',   'both', 'password',     'JIRA', '0', NULL,                     'The user password for the JIRA API'),
('jira_project_id',    'Project ID',        NULL,   'both', 'text',     'JIRA', '0', NULL,                         'The project id for the Customer JIRA project'),
('jira_dashboard_show','Show on Dashboard', true,   'both', 'checkbox',     'JIRA', '0', NULL,                     'Tick to show JIRA feed on dashboard');

-- WPPROD-395 Add setting to control Pinterest link
INSERT IGNORE INTO `settings` (`variable`, `name`, `location`, `note`, `type`, `group`, `required`, `options`) VALUES ('pinterest_button', 'Pinterest Button', 'both', 'The HTML for a Pinterest button', 'textarea', 'Social Media', '0', '');


-- ----------------------------------
-- WPPROD-419, TOS-214 - County drop down options
-- ----------------------------------

CREATE TABLE IF NOT EXISTS `counties` (
`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
`name` VARCHAR(255) NOT NULL ,
`region` TINYINT(1) NOT NULL ,
`created_by` INT(11) UNSIGNED NULL DEFAULT NULL ,
`modified_by` INT(11) UNSIGNED NULL DEFAULT NULL ,
`date_created` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
`date_modified` TIMESTAMP NULL ,
`publish` TINYINT(1) UNSIGNED NOT NULL DEFAULT '1' ,
`deleted` TINYINT(1) UNSIGNED NOT NULL DEFAULT '0' ,
PRIMARY KEY (`id`) ,
UNIQUE INDEX `name_UNIQUE` (`name` ASC) );

INSERT IGNORE INTO `counties` (`name`, `region`, `date_created`, `date_modified`) VALUES
('Antrim', 1, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP() ),
('Armagh', 1, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP() ),
('Carlow', 0, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP() ),
('Cavan', 0, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP() ),
('Clare', 0, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP() ),
('Cork', 0, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP() ),
('Derry', 1, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP() ),
('Donegal', 0, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP() ),
('Down', 1, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP() ),
('Dublin', 0, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP() ),
('Fermanagh', 1, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP() ),
('Galway', 0, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP() ),
('Kerry', 0, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP() ),
('Kildare', 0, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP() ),
('Kilkenny', 0, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP() ),
('Laois', 0, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP() ),
('Leitrim', 0, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP() ),
('Limerick', 0, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP() ),
('Longford', 0, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP() ),
('Louth', 0, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP() ),
('Mayo', 0, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP() ),
('Meath', 0, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP() ),
('Monaghan', 0, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP() ),
('Offaly', 0, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP() ),
('Roscommon', 0, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP() ),
('Sligo', 0, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP() ),
('Tipperary', 0, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP() ),
('Tyrone', 1, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP() ),
('Waterford', 0, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP() ),
('Westmeath', 0, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP() ),
('Wexford', 0, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP() ),
('Wicklow', 0, CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP() );

ALTER IGNORE TABLE `feeds` ADD `short_tag` VARCHAR(255);
ALTER IGNORE TABLE `feeds` ADD `function_call` VARCHAR(255);

-- ----------------------------------
-- WPPROD-372 Header and footer HTML settings
-- ----------------------------------
INSERT IGNORE INTO `settings` (`variable`,`name`,`value_live`,`value_stage`,`value_test`,`value_dev`,`default`,`location`,`note`,`type`,`group`,`required`,`options`) VALUES
('footer_html',   'Footer HTML',                '', '', '', '', '', 'both', 'Content to the added to end of the HTML body tags',    'textarea', 'SEO',                       '0', ''),
('cms_head_html', 'Website Platform Head HTML', '', '', '', '', '', 'both', 'Content to be added to the HTML head tags in the CMS', 'textarea', 'Website Platform Settings', '0', '');

INSERT IGNORE INTO `plugins` (`name`,`friendly_name`,`show_on_dashboard`) VALUES('reports','Reports',1),('projects','Projects',1);

CREATE TABLE IF NOT EXISTS `page_redirects` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `from` VARCHAR(255) NOT NULL ,
  `to` VARCHAR(255) NULL DEFAULT NULL ,
  `type` INT NOT NULL DEFAULT '301' ,
  `has_redirect` TINYINT NOT NULL DEFAULT '0' ,
  `delete` TINYINT NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`))
  ENGINE = InnoDB;

-- ----------------------------------
-- WPPROD-443 CMS Footer fix
-- WPPROD-371 Header and footer HTML settings
-- ----------------------------------

INSERT IGNORE INTO `settings` (`variable`,`name`,`value_live`,`value_stage`,`value_test`,`value_dev`,`default`,`location`,`note`,`type`,`group`,`required`,`options`)
VALUES ('cms_copyright', 'CMS Copyright Notice', '', '', '', '', 'Powered by <a href="https://ideabubble.ie">Idea Bubble</a> <a href="https://ideabubble.ie/website-cms-solutions.html">CMS</a>', 'both', 'Copyright notice for the CMS, displayed in the frontend footer',    'text', 'Contact Us', '0', '');

INSERT IGNORE INTO `settings` (`variable`,`name`,`value_live`,`value_stage`,`value_test`,`value_dev`,`default`,`location`,`note`,`type`,`group`,`required`,`options`) VALUES
('google_application_name', 'Analytics Application Name', '', '', '', '', '', 'both', 'This is the Google Application Name.', 'text', 'Google API', '0', ''),
('google_project_id', 'Analytics Project ID', '', '', '', '', '', 'both', 'This is the Google API Project ID.', 'text', 'Google API', '0', ''),
('google_client_id', 'Google Client ID', '', '', '', '', '', 'both', 'This is the Google Client ID.', 'text', 'Google API', '0', ''),
('google_access_type', 'Analytics Access Type', 'offline_access', 'offline_access', 'offline_access', 'offline_access', 'offline_access', 'both', 'This is the Google analytics access type.', 'text', 'Google API', '0', '');

UPDATE `settings` SET `value_live`='analytics' WHERE `value_live` = '' AND `variable` = 'google_application_name';
UPDATE `settings` SET `value_stage`='analytics' WHERE `value_stage` = '' AND `variable` = 'google_application_name';
UPDATE `settings` SET `value_test`='analytics' WHERE `value_test` = '' AND `variable` = 'google_application_name';
UPDATE `settings` SET `value_dev`='analytics' WHERE `value_dev` = '' AND `variable` = 'google_application_name';
UPDATE `settings` SET `value_live`='706680743683-rsidcfg9n6ljk8qb0dkq5oghkg628sho.apps.googleusercontent.com' WHERE `value_live` = '' AND `variable` = 'google_client_id';
UPDATE `settings` SET `value_stage`='706680743683-rsidcfg9n6ljk8qb0dkq5oghkg628sho.apps.googleusercontent.com' WHERE `value_stage` = '' AND `variable` = 'google_client_id';
UPDATE `settings` SET `value_test`='706680743683-rsidcfg9n6ljk8qb0dkq5oghkg628sho.apps.googleusercontent.com' WHERE `value_test` = '' AND `variable` = 'google_client_id';
UPDATE `settings` SET `value_dev`='706680743683-rsidcfg9n6ljk8qb0dkq5oghkg628sho.apps.googleusercontent.com' WHERE `value_dev` = '' AND `variable` = 'google_client_id';

INSERT IGNORE INTO `settings` (`variable`,`name`,`value_live`,`value_stage`,`value_test`,`value_dev`,`default`,`location`,`note`,`type`,`group`,`required`,`options`)
VALUES ('stock_enabled', 'Stock Management', 'FALSE', 'FALSE', 'FALSE', 'FALSE', 'FALSE', 'both', 'Check to Activate Stock Management', 'checkbox', 'Products', '0', '');

INSERT IGNORE INTO `settings` (`variable`,`name`,`value_live`,`value_stage`,`value_test`,`value_dev`,`default`,`location`,`note`,`type`,`group`,`required`,`options`)
VALUES ('override_stock', 'Override Unlimited Stock', 'FALSE', 'FALSE', 'FALSE', 'FALSE', 'FALSE', 'both', 'Override Unlimited Stock', 'checkbox', 'Products', '0', '');

INSERT IGNORE INTO `settings` (`variable`,`name`,`value_live`,`value_stage`,`value_test`,`value_dev`,`default`,`location`,`note`,`type`,`group`,`required`,`options`)
VALUES ('serp_category', 'Project Category', '', '', '', '', '', 'both', 'This is the grouping/category to use when getting SERP results', 'text', 'SERP', '0', ''),
('serp_token', 'API Token', '', '', '', '', '', 'both', 'This is the API Token to use when getting SERP results', 'text', 'SERP', '0', '');

INSERT IGNORE INTO `settings` (`variable`,`name`,`value_live`,`value_stage`,`value_test`,`value_dev`,`default`,`location`,`note`,`type`,`group`,`required`,`options`)
VALUES ('serp_email', 'SERP Email', '', '', '', '', '', 'both', 'This is the email address associated with the SERP account', 'text', 'SERP', '0', '');

INSERT IGNORE INTO `settings` (`variable`,`name`,`value_live`,`value_stage`,`value_test`,`value_dev`,`default`,`location`,`note`,`type`,`group`,`required`,`options`)
VALUES ('serp_category_viewkey', 'Category Viewkey', '', '', '', '', '', 'both', 'This is the category Viewkey associated with the SERP category', 'text', 'SERP', '0', '');


ALTER IGNORE TABLE `users` ADD COLUMN `email_verified` TINYINT(1) NULL DEFAULT 1  AFTER `registered` ;
ALTER IGNORE TABLE `users` ADD COLUMN `trial_start_date` TIMESTAMP NULL  AFTER `email_verified` ;

ALTER IGNORE TABLE `users` ADD COLUMN `validation_code` VARCHAR(255);
ALTER IGNORE TABLE `users` ADD COLUMN `status` INT(2) DEFAULT 1;


INSERT IGNORE INTO `settings` (`variable`,`name`,`value_live`,`value_stage`,`value_test`,`value_dev`,`default`,`location`,`note`,`type`,`group`,`required`,`options`)
VALUES ('vat_rate', 'VAT Rate', '0.23', '0.23', '0.23', '0.23', '0.23', 'both', 'Set the VAT rate for products.', 'text', 'Products', '0', '');

INSERT IGNORE INTO `settings` (`variable`,`name`,`value_live`,`value_stage`,`value_test`,`value_dev`,`default`,`location`,`note`,`type`,`group`,`required`,`options`) VALUES
 ('robots_txt', 'Robots.txt Content', '', '', '', '', '', 'both', 'Used to specify to robots which pages to not crawl.', 'textarea', 'Website Platform Settings', '0', '');


UPDATE `settings` SET `value_live`='http://jira.ideabubble.ie/' WHERE `variable` = 'jira_url';
UPDATE `settings` SET `value_live`='Customer' WHERE `variable` = 'jira_username';
UPDATE `settings` SET `value_live`='customer951' WHERE `variable` = 'jira_password';

-- ----------------------------------------------------
-- WPPROD-1397, RW-168 - Payment Provide Stripe Integration
-- ----------------------------------------------------
INSERT IGNORE INTO `settings`
(`variable`,                `name`,                  `default`, `location`, `note`, `type`, `group`, `required`) VALUES
('stripe_enabled',          'Enabled',               'FALSE',   'both',     'Check this box to make checkout payments be handled by <a href=\"https://stripe.com/\" target=\"_blank\">Stripe</a>', 'checkbox', 'Stripe', '0'),
('stripe_test_private_key', 'Test Secret Key',       '',        'both',     'Enter your Stripe testing secret key here.',                                                                          'text',     'Stripe', '0'),
('stripe_test_public_key',  'Test Publishable Key',  '',        'both',     'Enter your Stripe testing publishable key here.',                                                                     'text',     'Stripe', '0'),
('stripe_private_key',      'Live Secret Key',       '',        'both',     'Enter your Stripe live secret key here.',                                                                             'text',     'Stripe', '0'),
('stripe_public_key',       'Live Publishable Key',  '',        'both',     'Enter your Stripe live publishable key here.',                                                                        'text',     'Stripe', '0'),
('stripe_test_mode',        'Test Mode',             'FALSE',   'both',     'Check this box when you wish to use the test keys. No real payments will be made.',                                   'checkbox', 'Stripe', '0');

UPDATE settings set value_live = 'TRUE',value_stage = 'False' WHERE variable = 'search_engine_indexing';

INSERT IGNORE INTO `settings` (`variable`,`name`,`value_live`,`value_stage`,`value_test`,`value_dev`,`default`,`location`,`note`,`type`,`group`,`required`,`options`)
VALUES ('single_product_redirect', 'Single Product Redirect', 'FALSE', 'FALSE', 'FALSE', 'FALSE', 'FALSE', 'both', 'Set categories with 1 product to autoload that product.', 'checkbox', 'Products', '0', '');

CREATE TABLE IF NOT EXISTS `labels` (
 `id` INT(11) NOT NULL AUTO_INCREMENT ,
 `label` VARCHAR(255),
 `plugin_id` INT(11) NULL DEFAULT NULL,
 `delete` INT(1) DEFAULT 0,
 PRIMARY KEY (`id`))
 ENGINE = InnoDB;

 INSERT IGNORE INTO `settings` (`variable`, `name`, `default`, `location`, `type`, `group`, `required`, `options`, `note`) VALUES
('jira_project_code','Jira Project Code','','both', 'text','JIRA','0', NULL,'The Jira Project Code.');

CREATE TABLE IF NOT EXISTS `cron_tasks`(
`id` INT(11) NOT NULL AUTO_INCREMENT,
`title` VARCHAR(255),
`frequency` VARCHAR(255),
`plugin_id` INT(11) NULL DEFAULT NULL,
`publish` INT(11),
`delete` INT(1) DEFAULT 0,
PRIMARY KEY (`id`))
ENGINE = InnoDB;

CREATE TABLE IF NOT EXISTS `cron_frequencies`(
`id` INT(11) NOT NULL AUTO_INCREMENT,
`frequency` VARCHAR(255),
`publish` INT(11),
`delete` INT(1) DEFAULT 0,
PRIMARY KEY (`id`))
ENGINE = InnoDB;

INSERT IGNORE INTO `cron_frequencies` (`id`,`frequency`,`publish`,`delete`) VALUES(1,'Hourly',1,0),(2,'Daily',1,0),(3,'Weekly',1,0),(4,'Monthly',1,0),(5,'Custom',1,0);

CREATE TABLE IF NOT EXISTS `plugin_settings_relationship`(
`id` INT(11) NOT NULL AUTO_INCREMENT,
`plugin_name` INT(11),
`settings_group` VARCHAR(255),
`delete` INT(11) DEFAULT 0,
PRIMARY KEY (`id`))
ENGINE = InnoDB;

INSERT IGNORE INTO `plugin_settings_relationship` (`plugin_name`,`settings_group`) VALUES('products','Products');

CREATE TABLE IF NOT EXISTS `csv`(
`id` INT(11) NOT NULL AUTO_INCREMENT,
`title` VARCHAR(255),
`columns` BLOB,
`publish` INT(1) DEFAULT 1,
`delete` INT(1) DEFAULT 0,
PRIMARY KEY (`id`))
ENGINE = InnoDB;

INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) VALUES ('cms_template', 'CMS Template', 'default', 'default', 'default', 'default', 'default', 'both', 'The structure template for the CMS', 'select', 'General Settings', '0', 'Model_Settings,cms_theme_options');
INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `note`, `type`, `group`, `required`, `options`) VALUES ('cms_skin', 'CMS Skin', '', 'The skin theme for the CMS', 'select', 'General Settings', '0', 'Model_Settings,cms_skin_options');

-- ----------------------------------
-- IBCMS-218 - kamil updates for ibis dec sprint permussions
-- ----------------------------------
CREATE TABLE IF NOT EXISTS `resources` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_id` int(11) NOT NULL,
  `alias` varchar(128) NOT NULL,
  `name` varchar(128) NOT NULL,
  `parent_controller` int(11) DEFAULT NULL,
  `description` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=5 ;

INSERT INTO `resources` (`id`, `type_id`, `alias`, `name`, `parent_controller`, `description`) VALUES (2, 0, 'settings', 'Settings', NULL, ''), (3, 1, 'settings_index', 'Settings / Index', 2, 'Index action for settings controller.'),(4, 2, 'settings_users_edit_delete_btn', 'Settings / Users / Edit / Delete Btn', 0, 'Button for deleting user');

CREATE TABLE IF NOT EXISTS `role_permissions` (
  `role_id` int(11) NOT NULL,
  `resource_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

INSERT INTO `role_permissions` (`role_id`, `resource_id`) VALUES (2, 3);

-- --------------------------------------------------------------
-- REG-45 change email sender
-- --------------------------------------------------------------
INSERT IGNORE INTO `settings` (`variable`, `name`, `location`, `note`, `type`, `group`) VALUES ('account_verification_sender', 'Account Verification Sender', 'both', 'When a user registers to have an account created, the verification email is sent from this email address.', 'text', 'User Registration');

-- --------------------------------------------------------------
-- IBCMS-215 browser sniffer test mode : on or off
-- --------------------------------------------------------------
INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) VALUES ('browser_sniffer_testmode', 'Always On (test mode)', '0', '0', '0', '0', '0', 'both', 'This will force the Browser sniffer to display always if set to ON, otherwise it will.', 'toggle_button', 'Browser Sniffer', '0', 'Model_Settings,on_or_off');


-- IBIS-127
INSERT IGNORE INTO `settings` (`variable`, `name`, `location`, `note`, `type`, `group`, `options`) VALUES ('dashboard_date_filter', 'Start and End Date Filter', 'both', 'Display start and end date filter for widgets on the dashboard', 'toggle_button', 'Dashboard', 'Model_Settings,on_or_off');


-- ----------------------------------------------------
-- IBIS-146 Activities develop
-- ----------------------------------------------------
CREATE TABLE IF NOT EXISTS `activities` (
  `id`           INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `user_id`      INT(11) UNSIGNED NULL ,
  `action_id`    INT(11) UNSIGNED NULL ,
  `item_type_id` INT(11) UNSIGNED NULL ,
  `item_id`      INT(11) UNSIGNED NULL ,
  `scope_id`     INT(11) UNSIGNED NULL ,
  `timestamp`    TIMESTAMP        NULL ,
  `deleted`      INT(1)           NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`) );

CREATE TABLE IF NOT EXISTS `activities_item_types` (
  `id`         INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `stub`       VARCHAR(127) NOT NULL ,
  `name`       VARCHAR(127) NOT NULL ,
  `table_name` VARCHAR(127) NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `stub_UNIQUE` (`stub` ASC) ,
  UNIQUE INDEX `table_name_UNIQUE` (`table_name` ASC) );

CREATE TABLE IF NOT EXISTS `activities_actions` (
  `id`   INT(11) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `stub` VARCHAR(127)     NOT NULL ,
  `name` VARCHAR(127)     NOT NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `name_UNIQUE` (`stub` ASC) );

INSERT IGNORE INTO `activities_actions` (`stub`, `name`) VALUES
('create',   'Create'),
('read',     'Read'),
('update',   'Update'),
('delete',   'Delete'),
('move',     'Move'),
('upload',   'Upload'),
('download', 'Download'),
('print',    'Print'),
('send',     'Send'),
('login',    'Log in'),
('logout',   'Lot out');

INSERT IGNORE INTO `activities_item_types` (`stub`, `name`, `table_name`) VALUES
('user',         'User',        'users'),
('files',        'Document',    'plugin_files_file'),
('report',       'Report',      'plugin_reports_reports');

ALTER IGNORE TABLE `resources` ADD UNIQUE INDEX `alias_UNIQUE` (`alias` ASC) ;

INSERT IGNORE INTO `resources` (`type_id`, `alias`, `name`, `parent_controller`, `description`)
(SELECT '1', 'settings_activities', 'Settings / Activities', `id`, 'Activities action in the settings controller' FROM `resources` WHERE `alias` = 'settings' LIMIT 1);

INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `options`) VALUES
('track_activities', 'Track Activities', '0', '0', '0', '0', '0', 'both', 'Keep record of certain actions carried out by users', 'toggle_button', 'General Settings', 'Model_Settings,on_or_off');

INSERT IGNORE INTO `activities_actions` (`stub`, `name`) VALUES
('timeout',  'Time out'),
('cancel',   'Cancel');

ALTER IGNORE TABLE `activities` ADD COLUMN `status_id` INT(11) UNSIGNED NULL  AFTER `item_id` ;

ALTER IGNORE TABLE `activities`
ADD COLUMN `level2_item_type_id` INT(11) UNSIGNED NULL  AFTER `item_id` ,
ADD COLUMN `level2_item_id`      INT(11) UNSIGNED NULL  AFTER `level2_item_type_id` ,
ADD COLUMN `level3_item_type_id` INT(11) UNSIGNED NULL  AFTER `level2_item_id` ,
ADD COLUMN `level3_item_id`      INT(11) UNSIGNED NULL  AFTER `level3_item_type_id` ;

-- --------------------------------------------------------------
-- IBCMS-243 Members Registration, Approval & Management (back end part)
-- --------------------------------------------------------------
ALTER IGNORE TABLE `users` ADD `discount_format_id` INT NULL DEFAULT NULL COMMENT 'plugin_products_discount_format_id' AFTER `group_id`;
ALTER IGNORE TABLE `project_role` ADD `access_type` TEXT NULL DEFAULT NULL;

-- --------------------------------------------------------------
-- IBCMS-252 Members Registration, Approval & Management (front end part)
-- --------------------------------------------------------------
INSERT IGNORE INTO `project_role` (`id`,`role`,`description`,`access_type`,`publish`,`deleted`)VALUES (NULL,'Basic','This is standard registered user','Front end','1','0');
ALTER IGNORE TABLE `users` ADD `address_2` TINYTEXT NULL DEFAULT NULL AFTER `address`;
ALTER IGNORE TABLE `users` ADD `address_3` TINYTEXT NULL DEFAULT NULL AFTER `address_2`;
ALTER IGNORE TABLE `users` ADD `country` TINYTEXT NULL DEFAULT NULL AFTER `surname`;
ALTER IGNORE TABLE `users` ADD `county` TINYTEXT NULL DEFAULT NULL AFTER `country`;
ALTER IGNORE TABLE `users` ADD `mobile` TINYTEXT NULL DEFAULT NULL AFTER `phone`;
ALTER IGNORE TABLE `users` ADD `company` TINYTEXT NULL DEFAULT NULL AFTER `mobile`;

-- -----------------------------------------------------
-- Paypal settings
-- -----------------------------------------------------

INSERT IGNORE INTO `settings` (
  `id` ,
  `variable` ,
  `name` ,
  `value_live` ,
  `value_stage` ,
  `value_test` ,
  `value_dev` ,
  `default` ,
  `location` ,
  `note` ,
  `type` ,
  `group` ,
  `required` ,
  `options`
)
VALUES (
  NULL , 'paypal_payment_mode', 'Redirect', NULL , NULL , NULL , NULL , NULL , 'both', 'Redirect or onsite payment', 'toggle_button', 'Paypal Settings', '0', 'Model_Settings,on_or_off'
), (
  NULL , 'paypal_success_page', 'Success Page', NULL , NULL , NULL , NULL , NULL , 'both', 'Choose from active pages', 'select', 'Paypal Settings', '0', 'Model_Pages,get_pages_as_options'
), (
  NULL , 'paypal_error_page', 'Error Page', NULL , NULL , NULL , NULL , NULL , 'both', 'Choose from active pages', 'select', 'Paypal Settings', '0', 'Model_Pages,get_pages_as_options'
);

-- -----------------------------------------------------
-- IBCMS-261 Welcome text to be driven off a setting
-- -----------------------------------------------------
INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`) VALUES ('dashboard_welcome_text', 'Welcome Text', '<h2>Welcome to your CMS</h2>  <p>Another Powerfully Simple Solution</p> ', '<h2>Welcome to your CMS</h2>  <p>Another Powerfully Simple Solution</p> ', '<h2>Welcome to your CMS</h2>  <p>Another Powerfully Simple Solution</p> ', '<h2>Welcome to your CMS</h2>\n\n<p>Another Powerfully Simple Solution</p>\n', '<h2>Welcome to your CMS</h2>  <p>Another Powerfully Simple Solution</p> ', 'both', 'Text to be displayed at the top of the dashboard', 'wysiwyg', 'Dashboard');

-- -----------------------------------------------------
-- IBIS-199 Merge cms to ibis dashboard
-- -----------------------------------------------------
INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `options`)
VALUES ('ib_twitter_feed', 'CMS Twitter Feed', '1', '1', '1', '1', '1', 'both', 'Display the <a href=&quot;https://twitter.com/ideabubble&quot;>Idea Bubble Twitter Feed</a> in the CMS dashboard.', 'toggle_button', 'Social Media', 'Model_Settings,on_or_off');

-- -----------------------------------------------------
-- IBIS-245 Reports: As a superuser I should always have access to all reports
-- -----------------------------------------------------
ALTER IGNORE TABLE `project_role` ADD `master_group` TINYINT( 4 ) NOT NULL DEFAULT '0';
-- -----------------------------------------------------

-- IBCMS-265, IBIS-219 - Missing icons IBIS Inactive features
-- -----------------------------------------------------
UPDATE `plugins` SET `icon` = 'reports2.png' WHERE `name` = 'reports';
UPDATE `plugins` SET `icon` = 'reports2.png' WHERE `name` = 'reports2';

-- -----------------------------------------------------
-- IBIS-302 Activities » Item » Transaction - show transaction type as part of Item
-- -----------------------------------------------------
ALTER IGNORE TABLE `activities`            ADD COLUMN `item_subtype_id` INT(11) UNSIGNED NULL AFTER `item_type_id` ;
ALTER IGNORE TABLE `activities_item_types` ADD COLUMN `parent_id`       INT(11) UNSIGNED NULL AFTER `table_name` ;

ALTER IGNORE TABLE `activities_item_types` CHANGE COLUMN `table_name` `table_name` VARCHAR(127) NULL DEFAULT NULL, DROP INDEX `table_name_UNIQUE` ;

-- ----------------------------------------------------
-- IBIS-382 - typo "Log out" not "Lot out"
-- ----------------------------------------------------
update activities_actions set name='Log out' where name='Lot out';

-- ----------------------------------------------------
-- IBIS-383 - Setting to turn off Active Features and Inactive features on dashboard
-- ----------------------------------------------------
insert ignore into settings (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options` ) values ( 'feature_dashboard_labels', 'Feature Dashboard Labels', 1, 1, 1, 1, 1, 'both', 'Active Features and Inactive features on dashboard ', 'toggle_button', 'General Settings', 0, 'Model_Settings,on_or_off' );

-- ----------------------------------------------------
-- HPG-98 - Date formats
-- ----------------------------------------------------
INSERT IGNORE INTO `settings` (`variable`, `name`, `note`, `type`, `group`, `options`) VALUES ('date_format', 'Date Format', 'Display system dates in this format', 'select', 'General Settings', 'Model_Settings,get_date_formats');

-- ----------------------------------------------------
-- IBCMS-157 - Always set to ON the Responsive Site option and verify if the setting is active
-- ----------------------------------------------------
UPDATE IGNORE `settings` SET `settings`.`value_live`='TRUE',`settings`.`value_dev`='TRUE',`settings`.`value_stage`='TRUE',`settings`.`value_test`='TRUE',`settings`.`default`='TRUE'WHERE variable='responsive_enabled';

-- ----------------------------------------------------
-- IBCMS-280 - Forms Menu option does not show in CMS when enabled
-- ----------------------------------------------------
UPDATE IGNORE `plugins` SET `show_on_dashboard`=1 WHERE `friendly_name`='Forms';

-- ----------------------------------------------------
-- HPG-75 - Change background image for site setting
-- ----------------------------------------------------
INSERT IGNORE INTO `settings` (`variable`, `name`, `note`, `type`, `group`, `options`) VALUES
('background_image',               'Image',               'Image to be used in the site background.',                   'select',        'Background', 'Model_Media,get_background_images_as_options');
INSERT IGNORE INTO `settings` (`variable`, `name`, `note`, `type`, `group`) VALUES
('background_color',               'Colour',              'Colour to be used in the site background',                   'color_picker',  'Background');
INSERT IGNORE INTO `settings` (`variable`, `name`, `note`, `type`, `group`, `options`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`) VALUES
('background_horizontal_position', 'Horizontal Position', 'Horizontal position of background image',                    'toggle_button', 'Background', 'Model_Settings,get_horizontal_positions_options', 'center',    'center',    'center',    'center',    'center'),
('background_vertical_position',   'Vertical Position',   'Vertical position of background image',                      'toggle_button', 'Background', 'Model_Settings,get_vertical_positions_options',   'top',       'top',       'top',       'top',       'top'),
('background_repeat',              'Tile',                'Have the background image tile or not',                      'toggle_button', 'Background', 'Model_Media,get_background_repeat_options',       'no-repeat', 'no-repeat', 'no-repeat', 'no-repeat', 'no-repeat'),
('background_attachment',          'Attachment',          'Should the background image move when the page is scrolled', 'toggle_button', 'Background', 'Model_Media,get_background_attachment_options',   'scroll',    'scroll',    'scroll',    'scroll',    'scroll');
INSERT IGNORE INTO `settings` (`variable`, `name`, `note`, `type`, `group`) VALUES
('background_css',                 'CSS',                 'Use raw CSS to set the background',                          'textarea',      'Background');

-- ----------------------------------------------------
-- MC-2 Remove Realex
-- ----------------------------------------------------
INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`) VALUES ('enable_realex', 'Enable Realex Payments', '1', '1', '1', '1', '1', 'Allow Credit Card payments to be made using Realex', 'toggle_button', 'Realex Settings', 'Model_Settings,on_or_off');
INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`) VALUES ('enable_paypal', 'Enable PayPal Payments', '1', '1', '1', '1', '1', 'Allow payments to be made using Paypal',             'toggle_button', 'Paypal Settings', 'Model_Settings,on_or_off');

-- IBCMS-377
INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) VALUES ('show_footer_ib_engine', 'Show engine version in footer', 1, 1, 1, 1, 1, 'cms', '', 'checkbox', 'Website Platform Settings', 0, '' );
INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) VALUES ('show_footer_ib_project', 'Show project version in footer', 1, 1, 1, 1, 1, 'cms', '', 'checkbox', 'Website Platform Settings', 0, '' );

-- ----------------------------------------------------
-- STAC-58 Payments to be logged to the database for reporting
-- ----------------------------------------------------
INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`) VALUES ('paypal_test_mode', 'Test Mode', '0', '0', '0', '0', '0', 'Test payments are made using the PayPal sandbox, if this is on.', 'toggle_button', 'Paypal Settings', 'Model_Settings,on_or_off');

-- ----------------------------------------------------
-- PCSYS-72 - add new fields to store Registration Type Other, Where did you hear about us
-- ----------------------------------------------------
ALTER IGNORE TABLE `users` ADD COLUMN `role_other` VARCHAR(100);
ALTER IGNORE TABLE `users` ADD COLUMN `heard_from` VARCHAR(50);

-- ----------------------------------------------------
-- IBCMS-402 add new default page setting
-- ----------------------------------------------------
INSERT IGNORE INTO `settings`(`variable`,`name`,`value_live`,`value_stage`,`value_test`,`value_dev`,`default`,`location`,`note`,`type`,`group`,`required`,`options`) VALUES
('default_home_page', 'Default Home Page', null, null, null, null, '', 'both', 'Select the page that will load by default as the home page', 'select', 'General Settings', '0', 'Model_Pages,get_pages_as_options');

-- ----------------------------------------------------
-- PCSYS-108 Checkout option: credit account
-- ----------------------------------------------------
ALTER IGNORE TABLE `users` ADD COLUMN `credit_account` INT(1) NOT NULL DEFAULT 0 ;

-- ----------------------------------------------------
-- GP-25 Footer> Please google plus as a social media link
-- ----------------------------------------------------
INSERT IGNORE INTO `settings` (`variable`, `name`, `note`, `type`, `group`) VALUES
('googleplus_url', 'Google Plus URL', 'The ID for your Google Plus URL', 'text', 'Social Media');

-- resetting vat rate so it wont effect checkout totals
UPDATE `settings` set `value_live` = 0,  `value_stage` = 0 ,`value_test` = 0,`value_dev` = 0 WHERE variable = 'vat_rate';

-- ----------------------------------------------------
-- PCSYS-134 Flat rate shipping option
-- ----------------------------------------------------
INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `options`)
VALUES ('shipping_flat_rate', 'Shipping Flat Rate', '1', '1', '1', '1', '1', 'both', 'Activates the postal rates kicking in if product has no weight set', 'toggle_button', 'Shop Checkout', 'Model_Settings,on_or_off');

-- ----------------------------------------------------
-- added default page loading
-- ----------------------------------------------------
ALTER IGNORE TABLE `users` ADD COLUMN `default_home_page` VARCHAR(255);

INSERT IGNORE INTO `settings` (`variable`,`name`,`value_live`,`value_stage`,`value_test`,`value_dev`,`default`,`location`,`note`,`type`,`group`,`required`,`options`) VALUES
('sugar_api_rest_url', 'Rest URL', '', '', '', '', '', 'both', 'This is the REST URL for the API', 'text', 'Sugar CRM API', '0', ''),
('sugar_api_username', 'User Name', '', '', '', '', '', 'both', 'This is the User name for the API', 'text', 'Sugar CRM API', '0', ''),
('sugar_api_password', 'Password', '', '', '', '', '', 'both', 'This is the password for the API.', 'password', 'Sugar CRM API', '0', '');

INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `options`)
VALUES ('sugar_api_cert_validation', 'Certificate Validation', '1', '1', '1', '1', '1', 'both', 'Toggles the certificate validation check', 'toggle_button', 'Sugar CRM API', 'Model_Settings,on_or_off');

INSERT IGNORE INTO `settings` (`variable`, `name`, `note`, `type`, `group`, `options`) VALUES
('site_logo', 'Site Logo', 'The logo to be used on the frontend of the site.', 'select', 'General Settings', 'Model_Media,get_logos_as_options');

INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `options`)
VALUES ('debug_mode', 'Debug Mode', '0', '0', '0', '0', '0', 'both', 'Toggles debug mode for outputs in code statements', 'toggle_button', 'Website Platform Settings', 'Model_Settings,on_or_off');

UPDATE IGNORE `settings` SET `settings`.`value_live`='1',`settings`.`value_dev`='1',`settings`.`value_stage`='1',`settings`.`value_test`='1',`settings`.`default`='1' WHERE variable='track_activities';

ALTER IGNORE TABLE `user_tokens` DROP FOREIGN KEY `user_tokens_ibfk_1` ;
ALTER IGNORE TABLE `user_tokens`
  ADD CONSTRAINT `user_tokens_ibfk_1`
  FOREIGN KEY (`user_id` )
  REFERENCES `users` (`id` )
  ON DELETE NO ACTION;

INSERT IGNORE INTO `activities_actions` (`stub`, `name`) VALUES
('add',  'Add'),
('publish', 'Publish'),
('unpublish', 'Un Publish'),
('email', 'E-mail'),
('sms', 'SMS'),
('run', 'Run'),
('export', 'Export'),
('click', 'Click');

ALTER IGNORE TABLE `activities` ADD COLUMN `file_id` INT(11) UNSIGNED NULL DEFAULT NULL  AFTER `scope_id` ;

UPDATE IGNORE `settings` SET `value_dev` = null, `value_live` = null, `value_stage` = NULL, `value_test` = null where `variable` = 'cms_footer_html';

INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
VALUES ('footer_facebook_feed', 'Footer Facebook Feed', '0', '0', '0', '0', '0', 'both', 'Display a facebook feed in the site footer', 'toggle_button', 'Social Media', '0', 'Model_Settings,on_or_off');

INSERT IGNORE INTO `settings` (`variable`, `name`, `note`, `type`, `group`, `options`) VALUES ('currency_format', 'Currency Format', 'Display System Currency amounts in this format', 'select', 'General Settings', 'Model_Settings,get_currency_formats');


INSERT IGNORE INTO `feeds` (`name`, `date_created`, `date_modified`, `publish`, `deleted`, `short_tag`, `function_call`) VALUES
('Form', CURRENT_TIMESTAMP(), CURRENT_TIMESTAMP(), '1', '0', 'form', 'Model_Formbuilder,render');
INSERT IGNORE INTO `plugin_feeds` (`plugin_id`, `feed_id`, `date_created`, `date_modified`, `publish`, `deleted`) VALUES
((SELECT `id` FROM `plugins` WHERE `name` = 'formbuilder'), LAST_INSERT_ID(), CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, 1, 0);

INSERT IGNORE INTO `plugins` (`name`, `friendly_name`, `show_on_dashboard`, `requires_media`) VALUES ('prototypes', 'Prototypes', '0', '0');
INSERT IGNORE INTO `plugins_per_role` (`plugin_id`, `role_id`, `enabled`)
	SELECT `plugins`.`id`, `project_role`.`id`, 1
	FROM `plugins`
	JOIN `project_role`
	WHERE `plugins`.`name` = 'prototypes'
	AND `project_role`.`role` IN ('Super User', 'Administrator');

UPDATE IGNORE `settings` SET `value_dev` = 'modern', `value_live` = 'modern', `value_stage` = 'modern',`value_test` = 'modern' where `variable` = 'cms_template';
UPDATE IGNORE `settings` SET `value_dev` = '02', `value_live` ='02', `value_stage` = '02', `value_test` = '02' where `variable` = 'cms_skin';

-- IBCMS-449
-- password is equal to hash_hmac('sha256', 'password2015', 'b31542ZE3 Always code as if the guy who ends up maintaining your code will be a violent psychopath who knows where you live. a1919e716a02');
-- mysql does not have hmac function so it's hardcoded.
UPDATE users SET password='2b236870842d127c64265a595a88c23994bd7792f7989f004900c635653b2f0c' WHERE email LIKE '%@ideabubble.com';


UPDATE IGNORE `settings` SET `value_dev` = 'default', `value_live` = 'default', `value_stage` = 'default',`value_test` = 'default' where `variable` = 'cms_template';
UPDATE IGNORE `settings` SET `value_dev` = '01', `value_live` ='01', `value_stage` = '01', `value_test` = '01' where `variable` = 'cms_skin';

UPDATE IGNORE `settings` SET `value_dev` = TRUE, `value_live` = TRUE, `value_stage` = TRUE,`value_test` = TRUE where `variable` = 'show_footer_ib_engine';
UPDATE IGNORE `settings` SET `value_dev` = TRUE, `value_live` = TRUE, `value_stage` = TRUE,`value_test` = TRUE where `variable` = 'show_footer_ib_project';

-- IBCMS-559
DELETE FROM `plugins` WHERE `name` = 'ipwatcher';

INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) values ('ipwatcher_active', 'Active', '', '', '', '', '', 'both', '', 'toggle_button', 'IP Watcher Settings', 0, 'Model_Settings,on_or_off');
INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) values ('ipwatcher_blacklist_file', 'IP Blacklist File', '/etc/fail2ban/ip.blacklist', '/etc/fail2ban/ip.blacklist', '/etc/fail2ban/ip.blacklist', '/etc/fail2ban/ip.blacklist', '/etc/fail2ban/ip.blacklist', 'both', '', 'text', 'IP Watcher Settings', 0, '');
INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) values ('ipwatcher_treshold_day', 'Threshold (Requests/Day)', '10000', '10000', '10000', '10000', '10000', 'both', '', 'text', 'IP Watcher Settings', 0, '');
INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) values ('ipwatcher_treshold_hour', 'Threshold (Requests/Hour)', '1000', '1000', '1000', '1000', '1000', 'both', '', 'text', 'IP Watcher Settings', 0, '');
INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) values ('ipwatcher_treshold_minute', 'Threshold (Requests/Minute)', '200', '200', '200', '200', '200', 'both', '', 'text', 'IP Watcher Settings', 0, '');

DROP TABLE IF EXISTS plugin_ipwatcher_log;
CREATE TABLE settings_ipwatcher_log
(
	id		INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	ip		INT UNSIGNED NOT NULL,
	uri		VARCHAR(10000),
	requested	INT NOT NULL,
	gethostbyaddr	VARCHAR(250),
	location_by_ip	VARCHAR(250),

	KEY		(ip)
)
ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- this table is going to be used to make bulk inserts to actual log table to prevent too much disk io under heavy load.
DROP TABLE IF EXISTS plugin_ipwatcher_log_tmp;
CREATE TABLE settings_ipwatcher_log_tmp
(
	id		INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	ip		INT UNSIGNED NOT NULL,
	uri		VARCHAR(10000),
	requested	INT NOT NULL,
	gethostbyaddr	VARCHAR(250),
	location_by_ip	VARCHAR(250),

	KEY		(ip)
)
ENGINE=MEMORY DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS plugin_ipwatcher_blacklist;
CREATE TABLE settings_ipwatcher_blacklist
(
	id	INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	ip	INT UNSIGNED NOT NULL,
	blocked	DATETIME,
	blocked_by	INT,
	reason	VARCHAR(100),
	gethostbyaddr	VARCHAR(250),
	location_by_ip	VARCHAR(250),

	KEY	(ip)
)
ENGINE=InnoDB DEFAULT CHARSET=utf8;

DELIMITER ;;

CREATE EVENT `ipwatcher_log_write`
ON SCHEDULE EVERY 1 MINUTE
ON COMPLETION PRESERVE
ENABLE
DO
BEGIN
	CREATE TEMPORARY TABLE tmp_iplog LIKE settings_ipwatcher_log_tmp;
	INSERT INTO tmp_iplog (SELECT * FROM settings_ipwatcher_log_tmp);
	DELETE settings_ipwatcher_log_tmp FROM settings_ipwatcher_log_tmp INNER JOIN tmp_iplog ON settings_ipwatcher_log_tmp.id = tmp_iplog.id;
	INSERT INTO settings_ipwatcher_log (ip, uri, requested, gethostbyaddr, location_by_ip) (SELECT ip, uri, requested, gethostbyaddr, location_by_ip FROM tmp_iplog);
	DROP TEMPORARY TABLE tmp_iplog;
END;;

DELIMITER ;

-- IBCMS-551
DELETE FROM `plugins` WHERE `name` = 'keyboardshortcut';
DELETE FROM `activities_item_types` WHERE `stub` = 'keyboardshortcut';
INSERT INTO `activities_item_types` (`stub`, `name`, `table_name`) values ('keyboardshortcut', 'Keyboard Shortcut', 'settings_keyboardshortcut_list');

DROP TABLE IF EXISTS plugin_keyboardshortcut_list;
CREATE TABLE IF NOT EXISTS settings_keyboardshortcut_list
(
	id		int not null auto_increment primary key,
	name	varchar(100) not null,
	url		varchar(250) not null,
	keysequence	varchar(100) not null,

	UNIQUE KEY	(name)
)
ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- SAMPLE Keyboard Shortcuts
INSERT INTO settings_keyboardshortcut_list(name, url, keysequence) values ('Home', '/admin', 'CTRL+H');
INSERT INTO settings_keyboardshortcut_list(name, url, keysequence) values ('Courses', '/admin/courses', 'G,U');
INSERT INTO settings_keyboardshortcut_list(name, url, keysequence) values ('Contacts', '/admin/contacts3', 'G,N');
INSERT INTO settings_keyboardshortcut_list(name, url, keysequence) values ('Files', '/admin/files', 'G,F');
INSERT INTO settings_keyboardshortcut_list(name, url, keysequence) values ('Pages', '/admin/pages', 'G,P');

-- IBCMS-559
DELETE FROM `settings` WHERE `variable` = 'ipwatcher_active';
INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) values ('ipwatcher_frontend_active', 'Frontend Active', '', '', '', '', '', 'both', '', 'toggle_button', 'IP Watcher Settings', 0, 'Model_Settings,on_or_off');
INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) values ('ipwatcher_backend_active', 'Backend Active', '', '', '', '', '', 'both', '', 'toggle_button', 'IP Watcher Settings', 0, 'Model_Settings,on_or_off');

CREATE TABLE settings_ipwatcher_whitelist
(
	id	INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	ip	INT UNSIGNED NOT NULL,
	allowed	DATETIME,
	allowed_by	INT,
	reason	VARCHAR(100),

	KEY	(ip)
)
ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT IGNORE INTO settings_ipwatcher_whitelist (ip, allowed, reason) values (INET_ATON('192.168.2.254'), NOW(), 'LAN');
INSERT IGNORE INTO settings_ipwatcher_whitelist (ip, allowed, reason) values (INET_ATON('192.168.2.111'), NOW(), 'LAN');

INSERT IGNORE INTO `plugins` (`name`, `friendly_name`, `show_on_dashboard`, `requires_media`) VALUES ('todos', 'Todos', '0', '0');

INSERT IGNORE INTO `plugins` (`name`,`friendly_name`,`show_on_dashboard`) VALUES('dashboards','Dashboards',1);

-- IBCMS-559 user_agent whitelist
CREATE TABLE settings_ipwatcher_ua_whitelist
(
	id	INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	user_agent	VARCHAR(100),
	allowed	DATETIME,
	allowed_by	INT,
	reason	VARCHAR(100),

	KEY	(user_agent)
)
ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- IBCMS-572
INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) values ('localisation_content_active', 'Content Localisation', '', '', '', '', '', 'both', '', 'toggle_button', 'Localisation Settings', 0, 'Model_Settings,on_or_off');
INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) values ('localisation_system_active', 'System Localisation', '', '', '', '', '', 'both', '', 'toggle_button', 'Localisation Settings', 0, 'Model_Settings,on_or_off');

CREATE TABLE IF NOT EXISTS settings_localisation_languages
(
	id		INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	code	VARCHAR(10) not null,
	title	VARCHAR(100) not null,
	created_on	DATETIME,
	created_by	INT,
	updated_on	DATETIME,
	updated_by	INT,

	UNIQUE KEY	(code)
)
ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS settings_localisation_ctags
(
	id		INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	ctag		VARCHAR(10) not null,
	language	VARCHAR(100) not null,
	created_on	DATETIME,
	created_by	INT,
	updated_on	DATETIME,
	updated_by	INT,

	UNIQUE KEY	(ctag)
)
ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS settings_localisation_messages
(
	id		INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	message		TEXT,
	created_on	DATETIME,
	created_by	INT,
	updated_on	DATETIME,
	updated_by	INT,

	UNIQUE	KEY	(message(250))
)
ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS settings_localisation_translations
(
	id			INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	language_id	INT NOT NULL,
	message_id	INT NOT NULL,
	translation	TEXT,

	UNIQUE KEY (language_id, message_id)
)
ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS settings_localisation_custom_scanners
(
	scanner	VARCHAR(250) PRIMARY KEY
)
ENGINE=InnoDB DEFAULT CHARSET=utf8;

UPDATE IGNORE `plugins` SET `icon`='panels.png' WHERE `name`='dashboards';

ALTER IGNORE TABLE `users` ADD COLUMN `default_dashboard_id` INT(11) NULL  AFTER `default_home_page` ;

ALTER IGNORE TABLE `role_permissions` ADD PRIMARY KEY (`role_id`, `resource_id`) ;

INSERT IGNORE INTO `resources` (`type_id`, `alias`, `name`) VALUES ('0', 'dashboards', 'Dashboards');
INSERT IGNORE INTO `resources` (`type_id`, `alias`, `name`, `parent_controller`) SELECT '1', 'edit_all_dashboards','Dashboards / Edit all', `id` FROM resources where alias = 'dashboards';
INSERT IGNORE INTO `role_permissions` (`role_id`, `resource_id`) SELECT `project_role`.`id`, `resources`.`id` FROM `project_role` JOIN `resources` WHERE `project_role`.`role` IN ('Super User', 'Administrator') AND `resources`.`alias` = 'edit_all_dashboards';

-- IBCMS-572 tags no longer needed
DROP TABLE IF EXISTS settings_localisation_ctags;

UPDATE IGNORE `plugins` SET `show_on_dashboard`='0' WHERE `name` in ('documents', 'documents2');

-- IBCMS-551/IBCMS-407
DELETE `engine_dalm_statement` FROM `engine_dalm_statement` INNER JOIN `engine_dalm_model` ON `engine_dalm_statement`.model_id = `engine_dalm_model`.id WHERE `engine_dalm_model`.`name` = 'keyboardshortcut';
DELETE FROM `engine_dalm_model` WHERE `name` = 'keyboardshortcut';

-- IBCMS-466
ALTER TABLE `settings_localisation_languages` RENAME TO `engine_localisation_languages`;
ALTER TABLE `settings_localisation_messages` RENAME TO `engine_localisation_messages`;
ALTER TABLE `settings_localisation_translations` RENAME TO `engine_localisation_translations`;
ALTER TABLE `settings_localisation_custom_scanners` RENAME TO `engine_localisation_custom_scanners`;
ALTER TABLE `settings_keyboardshortcut_list` RENAME TO `engine_keyboardshortcut_list`;
ALTER TABLE `settings_ipwatcher_blacklist` RENAME TO `engine_ipwatcher_blacklist`;
ALTER TABLE `settings_ipwatcher_log` RENAME TO `engine_ipwatcher_log`;
ALTER TABLE `settings_ipwatcher_log_tmp` RENAME TO `engine_ipwatcher_log_tmp`;
ALTER TABLE `settings_ipwatcher_ua_whitelist` RENAME TO `engine_ipwatcher_ua_whitelist`;
ALTER TABLE `settings_ipwatcher_whitelist` RENAME TO `engine_ipwatcher_whitelist`;
DELIMITER ;;
CREATE EVENT `ipwatcher_log_write`
ON SCHEDULE EVERY 1 MINUTE
ON COMPLETION PRESERVE
ENABLE
DO
BEGIN
	CREATE TEMPORARY TABLE tmp_iplog LIKE engine_ipwatcher_log_tmp;
	INSERT INTO tmp_iplog (SELECT * FROM engine_ipwatcher_log_tmp);
	DELETE engine_ipwatcher_log_tmp FROM engine_ipwatcher_log_tmp INNER JOIN tmp_iplog ON engine_ipwatcher_log_tmp.id = tmp_iplog.id;
	INSERT INTO engine_ipwatcher_log (ip, uri, requested, gethostbyaddr, location_by_ip) (SELECT ip, uri, requested, gethostbyaddr, location_by_ip FROM tmp_iplog);
	DROP TEMPORARY TABLE tmp_iplog;
END;;
DELIMITER ;
ALTER TABLE `cron_frequencies` RENAME TO `engine_cron_frequencies`;
ALTER TABLE `cron_tasks` RENAME TO `engine_cron_tasks`;

CREATE TABLE IF NOT EXISTS `engine_calendar_types`
(
    id              INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    title           VARCHAR(227) ,
    `publish`       TINYINT NOT NULL DEFAULT '1' ,
    `deleted`       TINYINT NOT NULL DEFAULT '0' ,
	created_on	    DATETIME,
	created_by	    INT,
	updated_on	    TIMESTAMP NOT NULL ON UPDATE CURRENT_TIMESTAMP(),
	updated_by	    INT
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `engine_calendar_rules`
(
    id              INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    title           VARCHAR(227) ,
    plugin_id       INT NULL,
    description     BLOB NULL ,
    `publish`       TINYINT NOT NULL DEFAULT '1' ,
    `deleted`       TINYINT NOT NULL DEFAULT '0' ,
	created_on	    DATETIME,
	created_by	    INT,
	updated_on	    TIMESTAMP NOT NULL ON UPDATE CURRENT_TIMESTAMP(),
	updated_by	    INT
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `engine_calendar_events`
(
    id              INT(10) NOT NULL AUTO_INCREMENT PRIMARY KEY,
    title           VARCHAR(227) ,
    type_id         INT(10) NOT NULL,
    rule_id         INT(10) NOT NULL,
    start_date      DATETIME NOT NULL,
    end_date        DATETIME NOT NULL,
    `publish`       TINYINT NOT NULL DEFAULT '1' ,
    `deleted`       TINYINT NOT NULL DEFAULT '0' ,
	created_on	    DATETIME,
	created_by	    INT,
	updated_on	    TIMESTAMP NOT NULL ON UPDATE CURRENT_TIMESTAMP(),
	updated_by	    INT
)ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER IGNORE TABLE `engine_calendar_events` MODIFY COLUMN `updated_on` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP();
ALTER IGNORE TABLE `engine_calendar_types` MODIFY COLUMN `updated_on` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP();
ALTER IGNORE TABLE `engine_calendar_rules` MODIFY COLUMN `updated_on` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP();

ALTER IGNORE TABLE engine_calendar_rules CHANGE `plugin_id` `plugin_name` VARCHAR(100);

INSERT INTO `engine_calendar_types` (title, publish, deleted, created_on, created_by, updated_on, updated_by)
VALUES ('Bank Holiday', '1', '0', '2015-09-09 10:36:36', '2', '2015-09-09 10:36:36', '2');

ALTER IGNORE TABLE `engine_calendar_types` ADD COLUMN `color` VARCHAR(50) NULL  AFTER `title` ;

INSERT IGNORE INTO `settings` (`variable`, `name`, `location`, `note`, `type`, `group`) VALUES
('sharethis_id',      'ShareThis ID',      'both', 'Your public key from <a href=&quot;http://www.sharethis.com/account/&quot;>ShareThis</a>', 'text', 'Social Media'),
('sharethis_buttons', 'ShareThis Buttons', 'both', 'Button code generated using <a href=&quot;http://www.sharethis.com/get-sharing-tools/#/&quot;>ShareThis&#39; Sharing Tools</a>', 'textarea', 'Social Media');

ALTER IGNORE TABLE `users` ADD COLUMN `eircode` VARCHAR(127) NULL AFTER `address` ;

INSERT IGNORE `users` (email,`password`, `name`, surname, role_id) VALUES
('yann@ideabubble.ie','2b236870842d127c64265a595a88c23994bd7792f7989f004900c635653b2f0c','Yann','Ideabubble','1'),
('tempy@ideabubble.ie','2b236870842d127c64265a595a88c23994bd7792f7989f004900c635653b2f0c','Tempy','Ideabubble','1'),
('michael@ideabubble.ie','2b236870842d127c64265a595a88c23994bd7792f7989f004900c635653b2f0c','Michael','Ideabubble','1'),
('stephen@ideabubble.ie','2b236870842d127c64265a595a88c23994bd7792f7989f004900c635653b2f0c','Stephen','Ideabubble','1'),
('qa@ideabubble.ie','2b236870842d127c64265a595a88c23994bd7792f7989f004900c635653b2f0c','QA','Ideabubble','1'),
('mehmet@ideabubble.ie','2b236870842d127c64265a595a88c23994bd7792f7989f004900c635653b2f0c','Mehmet','Ideabubble','1'),
('ratko@ideabubble.ie','2b236870842d127c64265a595a88c23994bd7792f7989f004900c635653b2f0c','Ratko','Ideabubble','1'),
('ayaz@ideabubble.ie','2b236870842d127c64265a595a88c23994bd7792f7989f004900c635653b2f0c','Ayaz','Ideabubble','1');

INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
VALUES  ('use_config_file', 'Use Config File', '1', '1', '1', '1', '1', 'both', 'Get config settings from the code (on) or the CMS settings (off)', 'toggle_button', 'General Settings', '0', 'Model_Settings,on_or_off');

ALTER IGNORE TABLE `settings` ADD COLUMN `config_overwrite` INT(1) NOT NULL DEFAULT 0  AFTER `options` ;

INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `options`) VALUES ('payment_mailing_list', 'Payment Mailing List', 'Admins', 'Admins', 'Admins', 'Admins', 'Admins', 'both', 'Default mailing list used for payment processing', 'select', 'General Settings', 'Model_Contacts,get_mailing_lists_as_options');
UPDATE IGNORE `settings` SET `config_overwrite`='1' WHERE `variable`='payment_mailing_list';

UPDATE IGNORE `settings` SET `group` = 'Engine' WHERE `variable` IN (
  'project_name',
  'cms_theme',
  'cms_template',
  'cms_skin',
  'track_activities',
  'feature_dashboard_labels',
  'date_format'
) ;

UPDATE IGNORE `settings` SET `group` = 'Website' WHERE `variable` IN (
  'media_url',
  'frontend_theme',
  'enable_frontend',
  'responsive_enabled',
  'default_home_page',
  'site_logo',
  'currency_format',
  'enable_caching',
  'payment_mailing_list',
  'content_location',
  'column_menu',
  'row_bottom',
  'use_config_file'
) ;

UPDATE IGNORE `settings` SET `name` = 'Theme' WHERE `variable` in ('cms_theme','frontend_theme','cms_skin');
UPDATE IGNORE `settings` SET `name` = 'Template' WHERE `variable` = 'cms_template';

-- theme switcher
CREATE TABLE IF NOT EXISTS `engine_site_templates` (
  `id`            INT(11)      NOT NULL AUTO_INCREMENT ,
  `title`         VARCHAR(100) NOT NULL ,
  `stub`          VARCHAR(100) NOT NULL ,
  `type`          VARCHAR(100) NOT NULL ,
  `publish`       INT(1)           NULL DEFAULT 1 ,
  `deleted`       INT(1)           NULL DEFAULT 0 ,
  `created_by`    INT(1)           NULL ,
  `modified_by`   INT(1)           NULL ,
  `date_created`  TIMESTAMP        NULL DEFAULT CURRENT_TIMESTAMP ,
  `date_modified` TIMESTAMP        NULL ,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `uniq_title` (`title` ASC) ,
  UNIQUE INDEX `uniq_stub` (`stub` ASC)
);
INSERT IGNORE INTO `engine_site_templates` (`title`, `stub`, `type`, `date_created`, `date_modified`) VALUES ('Default', 'default', 'website', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

CREATE TABLE IF NOT EXISTS `engine_site_themes` (
  `id`            INT(11)      NOT NULL AUTO_INCREMENT ,
  `title`         VARCHAR(100) NOT NULL ,
  `stub`          VARCHAR(100) NOT NULL ,
  `template_id`   INT(11)      NOT NULL ,
  `publish`       INT(1)           NULL DEFAULT 1 ,
  `deleted`       INT(1)           NULL DEFAULT 0 ,
  `created_by`    INT(1)           NULL ,
  `modified_by`   INT(1)           NULL ,
  `date_created`  TIMESTAMP        NULL DEFAULT CURRENT_TIMESTAMP ,
  `date_modified` TIMESTAMP        NULL ,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `uniq_title` (`title` ASC) ,
  UNIQUE INDEX `uniq_stub` (`stub` ASC)
);
INSERT IGNORE INTO `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
 SELECT 'Default', 'default', `id`, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP FROM `engine_site_templates` WHERE `stub` = 'default';

INSERT IGNORE INTO `settings` (`variable`, `name`, `note`, `type`, `group`, `options`, `config_overwrite`, `value_dev`, `value_test`, `value_stage`, `value_live`, `default`) VALUES
('template_folder_path', 'Template', 'Template, which determines the layout of the site.',   'select', 'Website', 'Model_Settings,get_site_templates_as_options', 1, 'default', 'default', 'default', 'default', 'default'),
('assets_folder_path',   'Theme',    'Theme folder for styling the appearance of the site.', 'select', 'Website', 'Model_Settings,get_site_themes_as_options',    1, 'default', 'default', 'default', 'default', 'default');

INSERT IGNORE INTO `settings` (`variable`, `name`, `note`, `type`, `group`, `options`, `config_overwrite`, `value_dev`, `value_test`, `value_stage`, `value_live`, `default`) VALUES
('available_themes', 'Available Themes', 'Themes, which are to be available for selection throughout the site.', 'multiselect', 'Website', 'Model_Settings,get_site_themes_as_options', 0, 'a:0:{}', 'a:0:{}', 'a:0:{}', 'a:0:{}', 'a:0:{}');

UPDATE IGNORE `settings` SET `name` = 'Default Theme' WHERE `variable` = 'assets_folder_path';

-- LH-8
UPDATE `settings` SET `name`='Frontend Localisation' WHERE `variable`='localisation_content_active';
UPDATE `settings` SET `name`='Backend Localisation' WHERE `variable`='localisation_system_active';
INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) values ('localisation_content_default_language', 'Frontend Default Language', '', '', '', '', '', 'both', '', 'select', 'Localisation Settings', 0, 'Model_Localisation,get_languages_list_options');
INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) values ('localisation_system_default_language', 'Backend Default Language', '', '', '', '', '', 'both', '', 'select', 'Localisation Settings', 0, 'Model_Localisation,get_languages_list_options');

-- IBCMS-547
INSERT INTO `activities_item_types` (`stub`, `name`, `table_name`) values ('engine_cron', 'Cron Task', 'engine_cron_tasks');

INSERT IGNORE INTO `settings` (`variable`, `name`, `location`, `note`, `type`, `group`, `required`, `options`) VALUES
('pinterest_url', 'Pinterest URL', 'both', 'The portion of the URL to your Pinterest profile that comes after &quot;pinterest.com/&quot; ', 'text', 'Social Media', '0', ''),
('instagram_url', 'Instagram URL', 'both', 'The portion of the URL to your Instagram profile that comes after &quot;instagram.com/&quot; ', 'text', 'Social Media', '0', ''),
('vimeo_url',     'Vimeo URL',     'both', 'The portion of the URL to your Vimeo profile that comes after &quot;vimeo.com/&quot; ',         'text', 'Social Media', '0', '');

UPDATE IGNORE `users` SET `name` = 'Yann' WHERE email = 'yann@ideabubble.ie' AND ISNULL(`name`);
UPDATE IGNORE `users` SET `name` = 'Tempy' WHERE email = 'tempy@ideabubble.ie' AND ISNULL(`name`);
UPDATE IGNORE `users` SET `name` = 'Michael' WHERE email = 'michael@ideabubble.ie' AND ISNULL(`name`);
UPDATE IGNORE `users` SET `name` = 'Stephen' WHERE email = 'stephen@ideabubble.ie' AND ISNULL(`name`);
UPDATE IGNORE `users` SET `name` = 'Administrator' WHERE email = 'admin@ideabubble.com' AND ISNULL(`name`);
UPDATE IGNORE `users` SET `name` = 'Super User' WHERE email = 'super@ideabubble.com' AND ISNULL(`name`);
