/*
ts:2015-01-01 00:00:09
*/
INSERT IGNORE INTO `plugins` (`name`, `friendly_name`, `show_on_dashboard`, `requires_media`, `media_folder`, `icon`, `order`)	VALUES ('messaging', 'Messaging', 1, 0, null, 'notifications.png', 90);

INSERT INTO `activities_item_types` (`stub`, `name`, `table_name`) values ('notification_template', 'Notification Template', 'plugin_messaging_notification_templates');
INSERT INTO `activities_item_types` (`stub`, `name`, `table_name`) values ('notification', 'Notification', 'plugin_messaging_notifications');
INSERT INTO `activities_item_types` (`stub`, `name`, `table_name`) values ('message', 'Message', 'plugin_messaging_messages');


CREATE TABLE IF NOT EXISTS plugin_messaging_drivers
(
	id	INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	driver		VARCHAR(20),
	provider	VARCHAR(20),
	is_default ENUM('YES', 'NO') NOT NULL,
	status		ENUM('ACTIVE', 'UNUSED') NOT NULL,

	UNIQUE KEY	(driver, provider)
)
ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS plugin_messaging_messages
(
	id			INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	driver_id	INT NOT NULL,
	sender		VARCHAR(100),
	subject		TEXT,
	message		MEDIUMTEXT,
	date_created	TIMESTAMP,
	schedule	DATETIME,
	status		ENUM('SENDING', 'SCHEDULED', 'SCHEDULE_MISSED', 'SENT', 'INTERRUPTED') NOT NULL,
	sent_started	DATETIME,
	send_interrupted	DATETIME,
	sent_completed	DATETIME,
	created_by	INT NOT NULL,

	KEY			(sender)
)
ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS plugin_messaging_message_targets
(
	id			INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	message_id	INT NOT NULL,
	target_type	ENUM ('CMS_USER', 'CMS_ROLE', 'CMS_CONTACT', 'CMS_CONTACT_LIST', 'CMS_CONTACT3', 'EMAIL', 'PHONE') NOT NULL,
	target		VARCHAR(100) NOT NULL,
	x_details	VARCHAR(50),
	custom_subject	TEXT,
	custom_message	MEDIUMTEXT,

	KEY	(message_id, target_type, target)
)
ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS plugin_messaging_message_final_targets
(
	id			INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	target_id	INT NOT NULL,
	target_type	ENUM('CMS_USER', 'EMAIL', 'PHONE') NOT NULL,
	target		VARCHAR(100) NOT NULL,
	driver_remote_id	VARCHAR(100),
	delivery_status	ENUM('UNKNOWN', 'SENT', 'QUEUED', 'DELIVERED', 'UNDELIVERED', 'READ', 'FAILED', 'ERROR') NOT NULL,
	date_received	DATETIME,
	delivery_status_details	TEXT,
	deleted		DATETIME,

	UNIQUE KEY	(target_id, target_type, target),
	KEY			(driver_remote_id)
)
ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS plugin_messaging_notification_types
(
	id		INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	icon	VARCHAR(100) NOT NULL DEFAULT '',
	title	VARCHAR(50) NOT NULL,
	summary	VARCHAR(250) NOT NULL DEFAULT '',

	KEY	(title)
)
ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS plugin_messaging_notification_templates
(
	id			INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	send_interval	VARCHAR(200),
	name		VARCHAR(50) NOT NULL,
	description	VARCHAR(250) NOT NULL DEFAULT '',
	driver		ENUM('CMS', 'EMAIL', 'SMS') NOT NULL,
	type_id		INT NOT NULL,
	subject		VARCHAR(100) NOT NULL DEFAULT '',
	sender		VARCHAR(100) NOT NULL DEFAULT '',
	message		MEDIUMTEXT,
	page_id		INT,
	header		TEXT,
	footer		TEXT,
	schedule	DATETIME,
	date_created	TIMESTAMP,
	created_by	INT NOT NULL,
	date_updated	DATETIME,
	last_sent	DATETIME,
	publish		TINYINT NOT NULL DEFAULT 1,
	deleted		TINYINT NOT NULL DEFAULT 0,

	KEY	(name)
)
ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS plugin_messaging_notification_template_targets
(
	id			INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	template_id	INT NOT NULL,
	target_type	ENUM ('CMS_USER', 'CMS_ROLE', 'CMS_CONTACT', 'CMS_CONTACT_LIST', 'CMS_CONTACT3', 'EMAIL', 'PHONE') NOT NULL,
	target		VARCHAR(100) NOT NULL,
	x_details	VARCHAR(50),
	date_created	TIMESTAMP,

	KEY			(template_id)
)
ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS plugin_messaging_notifications
(
	id			INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	template_id	INT NOT NULL,
	message_id	INT NOT NULL,

	KEY			(template_id)
)
ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS plugin_messaging_report_notifications
(
	id			INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	report_id	INT NOT NULL,
	message_id	INT NOT NULL,

	KEY			(report_id)
)
ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS plugin_messaging_activity_alerts
(
	id			INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	target_type	ENUM('CMS_USER', 'CMS_ROLE') NOT NULL,
	target		INT NOT NULL,
	action_id	INT NOT NULL,
	item_type_id	INT NOT NULL,

	KEY			(action_id, item_type_id)
)
ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS plugin_messaging_recipient_providers
(
	id	VARCHAR(25) NOT NULL PRIMARY KEY,
	`plugin`	VARCHAR(50) NOT NULL,
	class_name	VARCHAR(100) NOT NULL
)
ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `plugin_messaging_message_targets` MODIFY COLUMN `target_type` VARCHAR(25);
ALTER TABLE `plugin_messaging_notification_template_targets` MODIFY COLUMN `target_type` VARCHAR(25);

INSERT INTO plugin_messaging_notification_types (icon, title, summary) VALUES ('', 'email', 'Email'), ('', 'website', 'Website'), ('', 'system', 'System'), ('', 'sms', 'SMS'), ('', 'billing', 'Billing');

ALTER TABLE plugin_messaging_message_final_targets MODIFY COLUMN delivery_status	ENUM('UNKNOWN', 'SENT', 'QUEUED', 'DELIVERED', 'UNDELIVERED', 'READ', 'FAILED', 'ERROR', 'OTHER');

INSERT INTO plugin_messaging_recipient_providers  (id, `plugin`, class_name) values ('CMS_USER', 'messaging',  'Model_MessagingRecipientProviderUser');
INSERT INTO plugin_messaging_recipient_providers  (id, `plugin`, class_name) values ('CMS_ROLE', 'messaging',  'Model_MessagingRecipientProviderRole');

CREATE TABLE plugin_messaging_message_stars
(
	message_id	INT NOT NULL,
	user_id		INT NOT NULL,

	PRIMARY KEY	(message_id, user_id),
	KEY			(message_id)
)
ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER TABLE `plugin_messaging_messages` MODIFY COLUMN `status`  enum('SENDING','SCHEDULED','SCHEDULE_MISSED','SENT','INTERRUPTED','RECEIVED');

ALTER TABLE `plugin_messaging_messages` ADD COLUMN `is_draft` TINYINT DEFAULT 0;

ALTER TABLE `plugin_messaging_messages` MODIFY COLUMN `status`  enum('SENDING','SCHEDULED','SCHEDULE_MISSED','SENT','INTERRUPTED','RECEIVED', 'DRAFTED');

ALTER TABLE `plugin_messaging_messages` ADD COLUMN `deleted` TINYINT DEFAULT 0;
ALTER TABLE `plugin_messaging_message_targets` ADD COLUMN `deleted` TINYINT DEFAULT 0;
ALTER TABLE `plugin_messaging_message_final_targets` MODIFY COLUMN `deleted` TINYINT DEFAULT 0;

ALTER IGNORE TABLE `plugin_messaging_messages`
MODIFY COLUMN `date_created` TIMESTAMP NULL,
ADD    COLUMN `date_updated` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER `date_created` ;

-- Permission
INSERT IGNORE INTO `resources` (`type_id`, `alias`, `name`)
  VALUES ('0', 'messaging', 'Messaging');
INSERT IGNORE INTO `resources` (`type_id`, `alias`, `name`, `parent_controller`)
  SELECT '1', 'messaging_global_see_all', 'Messaging / Global see all', `id` FROM resources where alias = 'messaging';
INSERT IGNORE INTO `role_permissions` (`role_id`, `resource_id`)
  SELECT `project_role`.`id`, `resources`.`id` FROM `project_role` JOIN `resources`
  WHERE `project_role`.`role` IN ('Administrator') AND `resources`.`alias` = 'messaging_global_see_all';

INSERT IGNORE INTO `resources` (`type_id`, `alias`, `name`, `parent_controller`)
  SELECT '1', 'messaging_send_system_sms', 'Messaging / Send system SMS', `id` FROM resources where alias = 'messaging';
INSERT IGNORE INTO `resources` (`type_id`, `alias`, `name`, `parent_controller`)
  SELECT '1', 'messaging_send_system_email', 'Messaging / Send system email', `id` FROM resources where alias = 'messaging';
INSERT IGNORE INTO `role_permissions` (`role_id`, `resource_id`)
  SELECT `project_role`.`id`, `resources`.`id` FROM `project_role` JOIN `resources`
  WHERE `project_role`.`role` = 'Administrator' AND `resources`.`alias` IN ('messaging_send_system_sms', 'messaging_send_system_email');

INSERT IGNORE INTO `resources` (`type_id`, `alias`, `name`, `parent_controller`)
  SELECT '1', 'messaging_view_system_sms', 'Messaging / View system SMS', `id` FROM resources where alias = 'messaging';
INSERT IGNORE INTO `resources` (`type_id`, `alias`, `name`, `parent_controller`)
  SELECT '1', 'messaging_view_system_email', 'Messaging / View system email', `id` FROM resources where alias = 'messaging';
INSERT IGNORE INTO `role_permissions` (`role_id`, `resource_id`)
  SELECT `project_role`.`id`, `resources`.`id` FROM `project_role` JOIN `resources`
  WHERE `project_role`.`role` = 'Administrator' AND `resources`.`alias` IN ('messaging_view_system_sms', 'messaging_view_system_email');


INSERT INTO `plugin_messaging_notification_templates`
(`name`,                            `description`, `driver`, `type_id`, `subject`,                     `sender`,                `message`, `page_id`, `header`, `footer`, `date_created`,    `date_updated`,    `publish`, `deleted`)
SELECT 'successful_payment_seller', '',            'EMAIL',  `id`,      'A new order has been placed', 'testing@websitecms.ie', '',        '0',       '',       '',       CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '1',       '0'
FROM `plugin_messaging_notification_types` WHERE `title` = 'email';

INSERT INTO `plugin_messaging_notification_templates`
(`name`,                              `description`, `driver`, `type_id`, `subject`,                     `sender`,                `message`, `page_id`, `header`, `footer`, `date_created`,    `date_updated`,    `publish`, `deleted`)
SELECT 'successful_payment_customer', '',            'EMAIL',  `id`,      'Thank you for shopping',      'testing@websitecms.ie', '',        '0',       '',       '',       CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '1',       '0'
FROM `plugin_messaging_notification_types` WHERE `title` = 'email';


INSERT IGNORE INTO `plugin_messaging_notification_templates`
(        `name`,                `driver`, `type_id`, `subject`,      `sender`,                `message`, `page_id`, `header`, `footer`, `date_created`,    `date_updated`,    `publish`, `deleted`)
 SELECT 'contact-form',         'EMAIL',  `id`,      'Contact Form', 'testing@websitecms.ie', '',        '0',       '',       '',       CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '1',       '0'
FROM `plugin_messaging_notification_types` WHERE `title` = 'email';


INSERT IGNORE INTO `plugin_messaging_notification_templates`
(       `name`,                                `driver`, `type_id`, `subject`,                       `sender`,                `message`, `page_id`, `header`, `footer`, `date_created`,    `date_updated`,    `publish`, `deleted`)
 SELECT 'successful-payback-new-member-admin', 'EMAIL',  `id`,      'New Mailing List Subscription', 'testing@websitecms.ie', '',        '0',       '',       '',       CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '1',       '0'
FROM `plugin_messaging_notification_types` WHERE `title` = 'email';

-- IBCMS-697
insert into `plugin_messaging_notification_templates` set `send_interval` = null, `name` = 'user-email-verification', `description` = '', `driver` = 'EMAIL', `type_id` = '1', `subject` = 'IBCMS Email Verification', `sender` = '', `message` = 'Hello $name,\n\n<a href=\"$link\">click</a> to verify your email.\n\nThanks,\nIBCMS', `page_id` = '0', `header` = '', `footer` = '', `schedule` = null, `date_created` = NOW(), `created_by` = 1, `date_updated` = null, `last_sent` = null, `publish` = '1', `deleted` = '0';
	select last_insert_id() into @refid_plugin_messaging_notification_templates_1446052482;
	insert into `plugin_messaging_notification_template_targets` set `template_id` = @refid_plugin_messaging_notification_templates_1446052482, `target_type` = 'CMS_ROLE', `target` = '2', `x_details` = 'bcc', `date_created` = NOW();

-- IBCMS-409
insert into `ppages_layouts` (`layout`) VALUES ('Newsletter');

UPDATE IGNORE `engine_cron_tasks`
SET `title` = 'Messaging', `publish` = 0
WHERE `title` = '' AND `plugin_id` = (SELECT `id` FROM `plugins` WHERE `name` = 'messaging');

ALTER TABLE `plugin_messaging_notification_templates` MODIFY COLUMN `driver` ENUM('DASHBOARD', 'EMAIL', 'SMS') NOT NULL;

-- HPG-174
DELETE l
	FROM engine_cron_log l
		INNER JOIN engine_cron_tasks t ON l.cron_id = t.id
		INNER JOIN `plugins` p ON t.plugin_id = p.id
	WHERE p.name = 'messaging';
DELETE a
FROM `activities` a
  INNER JOIN `activities_item_types` t ON a.item_type_id = t.id
WHERE t.`stub`='engine_cron';

--
ALTER TABLE `plugin_messaging_notification_templates` ADD COLUMN `create_via_code` TEXT;
ALTER TABLE `plugin_messaging_notification_templates` ADD COLUMN `usable_parameters_in_template` TEXT;

ALTER TABLE plugin_messaging_recipient_providers ADD COLUMN `priority` TINYINT;
INSERT INTO plugin_messaging_recipient_providers  (`id`, `plugin`, `class_name`, `priority`) values ('POST_VAR', 'messaging',  'Model_MessagingRecipientProviderPost', 1);


INSERT INTO `plugin_messaging_notification_templates`
(`name`,                     `description`, `driver`, `type_id`, `subject`,                     `sender`,                `message`, `page_id`, `header`, `footer`, `date_created`,    `date_updated`,    `publish`, `deleted`)
SELECT 'reset_cms_password', '',            'EMAIL',  `id`,      'Password Reset Confirmation', 'testing@websitecms.ie', '',        '0',       '',       '',       CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '1',       '0'
FROM `plugin_messaging_notification_types` WHERE `title` = 'email';
