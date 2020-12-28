/*
ts:2015-01-01 00:01:01
*/

CREATE TABLE IF NOT EXISTS `plugin_extra_domain_types` (
  `id` INT(11) NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(45) NULL DEFAULT NULL ,
  `friendly_name` VARCHAR(45) NULL DEFAULT NULL ,
  `created_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `modified_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `date_created` DATETIME,
  `date_modified` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ,
  `publish` INT(1) NOT NULL DEFAULT '1' ,
  `deleted` INT(1) NOT NULL DEFAULT '0' );

CREATE TABLE IF NOT EXISTS `plugin_extra_service_types` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NULL DEFAULT NULL ,
  `friendly_name` VARCHAR(45) NULL DEFAULT NULL ,
  `created_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `modified_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `date_created` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `date_modified` TIMESTAMP NULL ,
  `publish` INT(1) NOT NULL DEFAULT '1' ,
  `deleted` INT(1) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) );

  CREATE TABLE IF NOT EXISTS `plugin_extra_hosts` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NULL DEFAULT NULL ,
  `friendly_name` VARCHAR(45) NULL DEFAULT NULL ,
  `created_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `modified_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `date_created` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `date_modified` TIMESTAMP NULL ,
  `publish` INT(1) NOT NULL DEFAULT '1' ,
  `deleted` INT(1) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) );

  CREATE TABLE IF NOT EXISTS `plugin_extra_control_panels` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NULL DEFAULT NULL ,
  `friendly_name` VARCHAR(45) NULL DEFAULT NULL ,
  `created_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `modified_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `date_created` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `date_modified` TIMESTAMP NULL ,
  `publish` INT(1) NOT NULL DEFAULT '1' ,
  `deleted` INT(1) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) );

  CREATE TABLE IF NOT EXISTS `plugin_extra_status` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NULL DEFAULT NULL ,
  `friendly_name` VARCHAR(45) NULL DEFAULT NULL ,
  `created_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `modified_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `date_created` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `date_modified` TIMESTAMP NULL ,
  `publish` INT(1) NOT NULL DEFAULT '1' ,
  `deleted` INT(1) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) );

  CREATE TABLE IF NOT EXISTS `plugin_extra_billing_frequency` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NULL DEFAULT NULL ,
  `friendly_name` VARCHAR(45) NULL DEFAULT NULL ,
  `created_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `modified_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `date_created` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `date_modified` TIMESTAMP NULL ,
  `publish` INT(1) NOT NULL DEFAULT '1' ,
  `deleted` INT(1) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) );

  CREATE TABLE IF NOT EXISTS `plugin_extra_customers` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `company_title` VARCHAR(200) NULL DEFAULT NULL ,
  `industry` VARCHAR(200) NULL DEFAULT NULL ,
  `first_name` VARCHAR(45) NOT NULL ,
  `last_name` VARCHAR(45) NULL DEFAULT NULL ,
  `email` VARCHAR(255) NOT NULL ,
  `mailing_list` INT(11) NOT NULL ,
  `phone` VARCHAR(15) NULL DEFAULT NULL ,
  `mobile` VARCHAR(15) NULL DEFAULT NULL ,
  `notes` VARCHAR(255) NULL DEFAULT NULL ,
  `summary` TEXT NULL DEFAULT NULL ,
  `date_publish` DATETIME NULL DEFAULT NULL ,
  `date_remove` DATETIME NULL DEFAULT NULL ,
  `date_created` DATETIME NULL DEFAULT NULL ,
  `date_modified` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `created_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `modified_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `publish` TINYINT(1) UNSIGNED NULL DEFAULT '1' ,
  `deleted` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_plugin_extra_customers_mailing_list_idx` (`mailing_list` ASC) ,
  CONSTRAINT `fk_plugin_extra_customers_mailing_list_idx`
  FOREIGN KEY (`mailing_list` )
  REFERENCES `plugin_contacts_mailing_list` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
  ENGINE = InnoDB;

    CREATE TABLE IF NOT EXISTS `plugin_extra_services` (
  `id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT ,
  `type_id` INT(10) UNSIGNED NOT NULL ,
  `company_id` INT(10) UNSIGNED NOT NULL ,
  `url` VARCHAR(200) NOT NULL ,
  `date_start` DATETIME NULL DEFAULT NULL ,
  `date_end` DATETIME NULL DEFAULT NULL ,
  `domain_type_id` INT(10) UNSIGNED NOT NULL ,
  `ip_address` VARCHAR(200) NOT NULL ,
  `years_paid` VARCHAR(500) NOT NULL ,
  `note` VARCHAR(500) NOT NULL ,
  `date_created` DATETIME NULL DEFAULT NULL ,
  `date_modified` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `created_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `modified_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `publish` TINYINT(1) UNSIGNED NULL DEFAULT '1' ,
  `delete` TINYINT(1) UNSIGNED NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) ,
  INDEX `fk_plugin_extra_services_list_idx` (`company_id` ASC) ,
  CONSTRAINT `fk_plugin_extra_services_company_id`
  FOREIGN KEY (`company_id` )
  REFERENCES `plugin_extra_customers` (`id` )
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
  ENGINE = InnoDB;

INSERT INTO `plugin_extra_domain_types` (`name`, `friendly_name`, `publish`, `deleted`) VALUES
('primary', 'Primary', '1', '0'),
('alias', 'Alias', '1', '0'),
('parked', 'Parked', '1', '0'),
('test', 'Test', '1', '0'),
('uat', 'UAT', '1', '0'),
('staging', 'Staging', '1', '0');

INSERT INTO `plugin_extra_service_types` (`name`, `friendly_name`) VALUES
('domain', 'Domain'),
('certificate', 'Certificate'),
('support', 'Support');

INSERT INTO `plugin_extra_hosts` (`name`, `friendly_name`) VALUES
('webhost', 'Webhost'),
('fasthost', 'Fasthost');

INSERT INTO `plugin_extra_control_panels` (`name`, `friendly_name`) VALUES
('cpanel', 'Cpanel'),
('helm', 'Helm');

ALTER TABLE `plugin_extra_services`
ADD COLUMN `host_id` INT(10) UNSIGNED NULL AFTER `domain_type_id` ,
ADD COLUMN `control_panel_id` INT(10) UNSIGNED NULL AFTER `host_id` ,
ADD COLUMN `price` DECIMAL(10,2) NULL AFTER `ip_address` ,
ADD COLUMN `discount` DECIMAL(10,2) NULL AFTER `price` ,
ADD COLUMN `billing_frequency_id` INT(10) UNSIGNED NULL AFTER `discount` ,
ADD COLUMN `status_id` INT(10) UNSIGNED NULL AFTER `billing_frequency_id` ,
ADD COLUMN `referrer` VARCHAR(200) NULL AFTER `status_id` ;

INSERT INTO `plugin_extra_status` (`name`, `friendly_name`) VALUES
('active', 'Active'),
('pending', 'Pending');

INSERT INTO `plugin_extra_billing_frequency` (`name`, `friendly_name`) VALUES
('yearly', 'Yearly'),
('monthly', 'Monthly'),
('quarterly', 'Quarterly'),
('once_off', 'Once off');

ALTER TABLE `plugin_extra_customers` ADD COLUMN `address1` VARCHAR(255);
ALTER TABLE `plugin_extra_customers` ADD COLUMN `address2` VARCHAR(255);
ALTER TABLE `plugin_extra_customers` ADD COLUMN `address3` VARCHAR(255);

ALTER TABLE `plugin_extra_customers` ADD COLUMN `county` VARCHAR(255);
ALTER TABLE `plugin_extra_customers` ADD COLUMN `contact` INT(6) DEFAULT NULL;
ALTER TABLE `plugin_extra_customers` ADD COLUMN `billing_contact` INT(6) DEFAULT NULL;
ALTER TABLE `plugin_extra_customers` MODIFY `phone` VARCHAR(30) ;

-- -------------------------------------
-- WPPROD-367
-- Extra - Services - improve save of notes to show date and ascending order
-- -------------------------------------
CREATE  TABLE IF NOT EXISTS `plugin_extra_notes` (
  `id` INT(11) NOT NULL AUTO_INCREMENT ,
  `table_link_id` INT(11) NOT NULL ,
  `link_id` INT(11) NOT NULL ,
  `note` BLOB NULL DEFAULT NULL ,
  `added_by` INT(11) NULL DEFAULT NULL ,
  `edited_by` INT(11) NULL DEFAULT NULL ,
  `date_added` TIMESTAMP NULL DEFAULT NULL ,
  `date_edited` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `deleted` TINYINT(1) NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) );

-- -------------------------------------
-- IB-1036 Customer Account Service Payment
-- -------------------------------------
ALTER IGNORE TABLE `plugin_extra_customers` ADD COLUMN `user_id` INT(11) NULL DEFAULT NULL AFTER `last_name` ;

INSERT IGNORE INTO `project_role`(`role`, `description`) VALUES ('Extra User', 'This user can access the service payment form');

UPDATE `plugins` SET `icon`='extra.png' WHERE `name`='extra';

INSERT IGNORE INTO `plugin_extra_status` (`name`, `friendly_name`) VALUES
('trial',   'Trial'),
('expired', 'Expired'),
('pending', 'Pending');
ALTER IGNORE TABLE `plugin_extra_customers` ADD COLUMN `bullethq_id` INT(11) NULL DEFAULT NULL;

INSERT IGNORE INTO `plugin_extra_service_types` (`name`, `friendly_name`) VALUES
('hosting', 'Hosting');

-- IBCMS-391
INSERT IGNORE INTO `plugin_extra_service_types` (`name`, `friendly_name`) VALUES
('scanning', 'Scanning');

  CREATE TABLE IF NOT EXISTS `plugin_extra_payment_type` (
  `id` INT(10) NOT NULL AUTO_INCREMENT ,
  `name` VARCHAR(45) NULL DEFAULT NULL ,
  `friendly_name` VARCHAR(45) NULL DEFAULT NULL ,
  `created_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `modified_by` INT(10) UNSIGNED NULL DEFAULT NULL ,
  `created` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `modified` TIMESTAMP NULL ,
  `publish` INT(1) NOT NULL DEFAULT '1' ,
  `deleted` INT(1) NOT NULL DEFAULT '0' ,
  PRIMARY KEY (`id`) );

  INSERT INTO `plugin_extra_payment_type` (`name`, `friendly_name`) VALUES
('credit_card', 'Credit Card'),
('debit_card', 'Debit Card'),
('standing_order', 'Standing Order'),
('cheque', 'Cheque');

  ALTER TABLE `plugin_extra_services`
ADD COLUMN `payment_id` INT(10) UNSIGNED NULL AFTER `billing_frequency_id` ;


INSERT INTO `plugin_extra_hosts` (`name`, `friendly_name`) VALUES ('swoopscan', 'Swoopscan');
INSERT INTO `plugin_extra_control_panels` (`name`, `friendly_name`) VALUES ('scan2', 'Scan2');
DELETE FROM `plugin_extra_hosts` where name = '';

-- IBCMS-671
CREATE TABLE IF NOT EXISTS `plugin_extra_payments`
(
	`id`		INT AUTO_INCREMENT PRIMARY KEY,
	`type_id`	TINYINT NOT NULL,
	`amount`	DECIMAL(10,2) NOT NULL,
	`service_id`	INT NOT NULL,
	`payment_log_id`	INT NOT NULL,
	`date`		DATE,
	`publish`	BIT(1) NOT NULL,
	`deleted`	BIT(1) NOT NULL,

	KEY			(`service_id`)
) ENGINE = InnoDB;
UPDATE `plugin_extra_status` SET publish=0, deleted=1 WHERE `name`='pending' ORDER BY `id` DESC LIMIT 1;

-- IBOC-218
ALTER IGNORE TABLE `plugin_products_product` ADD COLUMN `featured` TINYINT(1) NOT NULL DEFAULT 0;
ALTER IGNORE TABLE `plugin_products_product` ADD COLUMN `postal_format_id` INT NULL;
INSERT INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) values ('bullethq_bank_account_id', 'Bullethq Bank Account Id', '2631', '2631', '2631', '2631', '2631', 'both', '', 'text', 'Extra', 0, '');

CREATE TABLE IF NOT EXISTS `plugin_extra_invoices`
(
	`id`		INT AUTO_INCREMENT PRIMARY KEY,
	`amount`	DECIMAL(10,2) NOT NULL,
	`service_id`	INT NOT NULL,
	`created`	DATETIME,
	`due_date`	DATE,
	`bullethq_id`	INT,
	`bullethq_token` VARCHAR(100),
	`publish`	BIT(1) NOT NULL,
	`deleted`	BIT(1) NOT NULL,

	KEY			(`service_id`)
) ENGINE = InnoDB;

INSERT INTO `plugin_products_category`
	(`category`, `Description`, `information`, `publish`, `deleted`)
	VALUES
	('Services', 'Ideabubble Services', '', 1, 0);
SELECT LAST_INSERT_ID() INTO @ib_service_cat_id_1444823955;
INSERT INTO `plugin_products_product`
	(`title`, `url_title`, `price`, `brief_description`, `description`, `product_code`, `seo_title`, `seo_keywords`, `seo_description`, `seo_footer`, `publish`, `deleted`, `order`, `date_modified`, `date_entered`, `category_id`, `over_18`)
	VALUES
	('Domain', 'domain', 0, 'Domain Service', 'Domain Service', 'ib-domain', 'Domain Service', 'Domain Service', 'Domain Service', '', 1, 0, 1, NOW(), NOW(), @ib_service_cat_id_1444823955, 0);
	INSERT INTO `plugin_products_product_categories` (`product_id`, `category_id`) VALUES (LAST_INSERT_ID(), @ib_service_cat_id_1444823955);
INSERT INTO `plugin_products_product`
	(`title`, `url_title`, `price`, `brief_description`, `description`, `product_code`, `seo_title`, `seo_keywords`, `seo_description`, `seo_footer`, `publish`, `deleted`, `order`, `date_modified`, `date_entered`, `category_id`, `over_18`)
	VALUES
	('Certificate', 'certificate', 0, 'Certificate Service', 'Certificate Service', 'ib-certificate', 'Certificate Service', 'Certificate Service', 'Certificate Service', '', 1, 0, 1, NOW(), NOW(), @ib_service_cat_id_1444823955, 0);
	INSERT INTO `plugin_products_product_categories` (`product_id`, `category_id`) VALUES (LAST_INSERT_ID(), @ib_service_cat_id_1444823955);
INSERT INTO `plugin_products_product`
	(`title`, `url_title`, `price`, `brief_description`, `description`, `product_code`, `seo_title`, `seo_keywords`, `seo_description`, `seo_footer`, `publish`, `deleted`, `order`, `date_modified`, `date_entered`, `category_id`, `over_18`)
	VALUES
	('Support', 'support', 0, 'Support Service', 'Support Service', 'ib-support', 'Support Service', 'Support Service', 'Support Service', '', 1, 0, 1, NOW(), NOW(), @ib_service_cat_id_1444823955, 0);
	INSERT INTO `plugin_products_product_categories` (`product_id`, `category_id`) VALUES (LAST_INSERT_ID(), @ib_service_cat_id_1444823955);
INSERT INTO `plugin_products_product`
	(`title`, `url_title`, `price`, `brief_description`, `description`, `product_code`, `seo_title`, `seo_keywords`, `seo_description`, `seo_footer`, `publish`, `deleted`, `order`, `date_modified`, `date_entered`, `category_id`, `over_18`)
	VALUES
	('Hosting', 'hosting', 0, 'Hosting Service', 'Hosting Service', 'ib-hosting', 'Hosting Service', 'Hosting Service', 'Hosting Service', '', 1, 0, 1, NOW(), NOW(), @ib_service_cat_id_1444823955, 0);
	INSERT INTO `plugin_products_product_categories` (`product_id`, `category_id`) VALUES (LAST_INSERT_ID(), @ib_service_cat_id_1444823955);
INSERT INTO `plugin_products_product`
	(`title`, `url_title`, `price`, `brief_description`, `description`, `product_code`, `seo_title`, `seo_keywords`, `seo_description`, `seo_footer`, `publish`, `deleted`, `order`, `date_modified`, `date_entered`, `category_id`, `over_18`)
	VALUES
	('Scanning', 'scanning', 0, 'Scanning Service', 'Scanning Service', 'ib-scanning', 'Scanning Service', 'Scanning Service', 'Scanning Service', '', 1, 0, 1, NOW(), NOW(), @ib_service_cat_id_1444823955, 0);
	INSERT INTO `plugin_products_product_categories` (`product_id`, `category_id`) VALUES (LAST_INSERT_ID(), @ib_service_cat_id_1444823955);

CREATE TABLE IF NOT EXISTS `plugin_extra_realvault_payers`
(
	customer_id	INT NOT NULL PRIMARY KEY,
	realvault_id	VARCHAR(20) NOT NULL
) ENGINE = INNODB;

CREATE TABLE IF NOT EXISTS `plugin_extra_realvault_cards`
(
	id			INT AUTO_INCREMENT PRIMARY KEY,
	customer_id	INT NOT NULL,
	card_number	VARCHAR(20) NOT NULL,
	expdate		DATE NOT NULL,
	realvault_id	VARCHAR(30) NOT NULL,
	cv			VARCHAR(4),

	KEY			(`customer_id`)
) ENGINE = INNODB;

ALTER TABLE `plugin_extra_services` ADD COLUMN auto_renew TINYINT(1);

INSERT INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) values ('bullethq_company_id', 'Bullethq Company Id', '2311', '2311', '2311', '2311', '2311', 'both', '', 'text', 'Extra', 0, '');

-- IBCMS-649
CREATE TABLE IF NOT EXISTS `plugin_extra_projects`
(
	`id`		INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`jira_id`	VARCHAR(20) NOT NULL UNIQUE,
	`jira_key`	VARCHAR(20) NOT NULL UNIQUE,
	`title`	VARCHAR(100) NOT NULL,
	`synced`	DATETIME
) ENGINE = INNODB;

CREATE TABLE IF NOT EXISTS `plugin_extra_projects_issues`
(
	`id`		INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`project_id`	INT NOT NULL,
	`jira_id`	VARCHAR(20) NOT NULL,
	`jira_key`	VARCHAR(20) NOT NULL,
	`title`	VARCHAR(200),
	`description` TEXT,
	`status`	VARCHAR(10),
	`time_spent`	INT,
	`updated`	DATETIME,

	KEY		(`jira_key`)
) ENGINE = INNODB;

CREATE TABLE IF NOT EXISTS `plugin_extra_projects_worklog`
(
	`id`		INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`issue_id`	INT NOT NULL,
	`author`	VARCHAR(50),
	`time_spent`	INT,
	`started`	DATETIME,

	KEY		(`issue_id`)
) ENGINE = INNODB;

CREATE TABLE IF NOT EXISTS `plugin_extra_projects_rapidviews`
(
	`id`		INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`jira_id`	VARCHAR(20) NOT NULL,
	`name`	VARCHAR(200),

	KEY		(`jira_id`)
) ENGINE = INNODB;

CREATE TABLE IF NOT EXISTS `plugin_extra_projects_rapidviews_sprints`
(
	`id`		INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	`rapidview_id`	VARCHAR(20) NOT NULL,
	`jira_id`	VARCHAR(20),
	`name`	VARCHAR(200),
	`state`	VARCHAR(20),

	KEY		(`jira_id`)
) ENGINE = INNODB;

CREATE TABLE IF NOT EXISTS `plugin_extra_projects_rapidviews_sprints_has_issues`
(
	`sprint_id`	INT NOT NULL,
	`issue_id` INT NOT NULL,

	PRIMARY KEY (`sprint_id`, `issue_id`)
) ENGINE = INNODB;

INSERT INTO `plugin_extra_payment_type` (`name`, `friendly_name`) VALUES ('bank_transfer', 'Bank Transfer');

INSERT IGNORE INTO `plugin_extra_status` (`name`, `friendly_name`) VALUES ('transferred_away',   'Transferred Away');
-- IBCMS-649
insert into `plugin_reports_reports` set `name` = 'Projects(JIRA) Spent Time', `summary` = '', `sql` = 'SELECT \n    __TITLES__ ROUND(SUM(`worklog`.`time_spent`) / 3600, 2) AS `time_spent`\nFROM `plugin_extra_projects_worklog` worklog\n INNER JOIN plugin_extra_projects_issues issues ON worklog.issue_id = issues.id\n INNER JOIN plugin_extra_projects projects ON issues.project_id = projects.id\n__WHERE__\n__GROUP_BY__', `widget_sql` = '', `category` = '0', `sub_category` = '0', `dashboard` = '0', `created_by` = null, `modified_by` = null, `date_created` = NOW(), `date_modified` = NOW(), `publish` = '1', `delete` = '0', `widget_id` = '20', `chart_id` = '15', `link_url` = '', `link_column` = '', `report_type` = 'sql', `autoload` = '0', `checkbox_column` = '0', `action_button_label` = '', `action_button` = '0', `action_event` = '																																																																																																			', `checkbox_column_label` = '', `autosum` = '0', `column_value` = '', `autocheck` = '0', `custom_report_rules` = '', `bulk_message_sms_number_column` = '', `bulk_message_email_column` = '', `bulk_message_subject_column` = '', `bulk_message_subject` = '', `bulk_message_body_column` = '', `bulk_message_body` = '', `bulk_message_interval` = '', `rolledback_to_version` = null, `php_modifier` = '$titles = $this->get_parameter(\'titles\');\nif(count($titles) == 0){\n	$titles[] = \'Project\';\n}\n\n$titles__ = \'\';\n\n$where = array();\n$project_id = $this->get_parameter(\'project_id\');\nif(is_numeric($project_id)){\n	$where[] = \'projects.id = \' . $project_id;\n}\n$author = $this->get_parameter(\'author\');\nif($author){\n	$where[] = \"worklog.author = \'\" . $author . \"\'\";\n}\n$month = $this->get_parameter(\'month\');\nif($month){\n	$where[] = \"DATE_FORMAT(worklog.started, \'%Y-%m-01\') = \'\" . $month . \"\'\";\n}\nif(count($where)){\n	$where = \' WHERE \' . implode(\' AND \', $where);\n} else {\n	$where = \'\';\n}\n$group_by = array();\nif(@array_search(\'Project\', $titles) !== false){\n	$titles__ .= \" `projects`.`title` AS `Project`,\";\n	$group_by[] = \'projects.id\';\n}\nif(@array_search(\'Month\', $titles) !== false){\n	$titles__ .= \" DATE_FORMAT(worklog.started, \'%M, %Y\') AS `month`, \";\n	$group_by[] = \'month\';\n}\nif(@array_search(\'Author\', $titles) !== false){\n	$titles__ .= \" `worklog`.`author` AS `Author`,\";\n	$group_by[] = \'author\';\n}\nif(count($group_by)){\n	$group_by = \' GROUP BY \' . implode(\',\', $group_by);\n} else {\n	$group_by = \'\';\n}\n\n$sql = $this->_sql;\n$sql = str_replace(\'__TITLES__\', $titles__, $sql);\n$sql = str_replace(\'__WHERE__\', $where, $sql);\n$sql = str_replace(\'__GROUP_BY__\', $group_by, $sql);\n$this->_sql = $sql;';
	select last_insert_id() into @refid_plugin_reports_reports_1446477081;
	insert into `plugin_reports_parameters` set `report_id` = @refid_plugin_reports_reports_1446477081, `type` = 'dropdown', `name` = 'titles', `value` = 'Project;Author;Month', `delete` = '0', `is_multiselect` = 1;
	insert into `plugin_reports_parameters` set `report_id` = @refid_plugin_reports_reports_1446477081, `type` = 'custom', `name` = 'project_id', `value` = '(select id, title from plugin_extra_projects order by title)', `delete` = '0', `is_multiselect` = 0;
	insert into `plugin_reports_parameters` set `report_id` = @refid_plugin_reports_reports_1446477081, `type` = 'custom', `name` = 'author', `value` = '(select distinct author from plugin_extra_projects_worklog order by author)', `delete` = '0', `is_multiselect` = 0;
	insert into `plugin_reports_parameters` set `report_id` = @refid_plugin_reports_reports_1446477081, `type` = 'custom', `name` = 'month', `value` = '(SELECT DISTINCT(DATE_FORMAT(started,\'%Y-%m-01\')) AS `month` from plugin_extra_projects_worklog order by started desc)', `delete` = '0', `is_multiselect` = 0;

insert into `plugin_reports_reports` set `name` = 'Sprints(JIRA) Spent Time', `summary` = '', `sql` = 'SELECT \n    __TITLES__ ROUND(SUM(`worklog`.`time_spent`) / 3600, 2) AS `time_spent`\nFROM plugin_extra_projects_worklog worklog INNER JOIN plugin_extra_projects_issues issues ON worklog.issue_id = issues.id INNER JOIN plugin_extra_projects_rapidviews_sprints_has_issues has_issues ON issues.id = has_issues.issue_id INNER JOIN plugin_extra_projects_rapidviews_sprints sprints ON has_issues.sprint_id = sprints.id\n__WHERE__\n__GROUP_BY__', `widget_sql` = '', `category` = '0', `sub_category` = '0', `dashboard` = '0', `created_by` = null, `modified_by` = null, `date_created` = NOW(), `date_modified` = NOW(), `publish` = '1', `delete` = '0', `widget_id` = '20', `chart_id` = '15', `link_url` = '', `link_column` = '', `report_type` = 'sql', `autoload` = '0', `checkbox_column` = '0', `action_button_label` = '', `action_button` = '0', `action_event` = '																																																																																																																																																																																											', `checkbox_column_label` = '', `autosum` = '0', `column_value` = '', `autocheck` = '0', `custom_report_rules` = '', `bulk_message_sms_number_column` = '', `bulk_message_email_column` = '', `bulk_message_subject_column` = '', `bulk_message_subject` = '', `bulk_message_body_column` = '', `bulk_message_body` = '', `bulk_message_interval` = '', `rolledback_to_version` = null, `php_modifier` = '$titles = $this->get_parameter(\'titles\');\nif(count($titles) == 0){\n	$titles[] = \'Sprint\';\n}\n\n$titles__ = \'\';\n\n$where = array();\n$sprint_id = $this->get_parameter(\'sprint_id\');\nif(is_numeric($sprint_id)){\n	$where[] = \'sprints.id = \' . $sprint_id;\n}\n$author = $this->get_parameter(\'author\');\nif($author){\n	$where[] = \"worklog.author = \'\" . $author . \"\'\";\n}\n$month = $this->get_parameter(\'month\');\nif($month){\n	$where[] = \"DATE_FORMAT(worklog.started, \'%Y-%m-01\') = \'\" . $month . \"\'\";\n}\nif(count($where)){\n	$where = \' WHERE \' . implode(\' AND \', $where);\n} else {\n	$where = \'\';\n}\n$group_by = array();\nif(@array_search(\'Sprint\', $titles) !== false){\n	$titles__ .= \" `sprints`.`name` AS `Sprint`,\";\n	$group_by[] = \'sprints.id\';\n}\nif(@array_search(\'Month\', $titles) !== false){\n	$titles__ .= \" DATE_FORMAT(worklog.started, \'%M, %Y\') AS `month`, \";\n	$group_by[] = \'month\';\n}\nif(@array_search(\'Author\', $titles) !== false){\n	$titles__ .= \" `worklog`.`author` AS `Author`,\";\n	$group_by[] = \'author\';\n}\nif(count($group_by)){\n	$group_by = \' GROUP BY \' . implode(\',\', $group_by);\n} else {\n	$group_by = \'\';\n}\n\n$sql = $this->_sql;\n$sql = str_replace(\'__TITLES__\', $titles__, $sql);\n$sql = str_replace(\'__WHERE__\', $where, $sql);\n$sql = str_replace(\'__GROUP_BY__\', $group_by, $sql);\n$this->_sql = $sql;';
	select last_insert_id() into @refid_plugin_reports_reports_1446478781;
	insert into `plugin_reports_parameters` set `report_id` = @refid_plugin_reports_reports_1446478781, `type` = 'dropdown', `name` = 'titles', `value` = 'Sprint;Author;Month', `delete` = '0', `is_multiselect` = 1;
	insert into `plugin_reports_parameters` set `report_id` = @refid_plugin_reports_reports_1446478781, `type` = 'custom', `name` = 'sprint_id', `value` = '(SELECT sprints.id, CONCAT(rapidviews.name, \'->\', sprints.name) AS `title` from plugin_extra_projects_rapidviews_sprints sprints INNER JOIN plugin_extra_projects_rapidviews rapidviews ON sprints.rapidview_id = rapidviews.id ORDER BY title)', `delete` = '0', `is_multiselect` = 0;
	insert into `plugin_reports_parameters` set `report_id` = @refid_plugin_reports_reports_1446478781, `type` = 'custom', `name` = 'author', `value` = '(select distinct author from plugin_extra_projects_worklog order by author)', `delete` = '0', `is_multiselect` = 0;
	insert into `plugin_reports_parameters` set `report_id` = @refid_plugin_reports_reports_1446478781, `type` = 'custom', `name` = 'month', `value` = '(SELECT DISTINCT(DATE_FORMAT(started,\'%Y-%m-01\')) AS `month` from plugin_extra_projects_worklog order by started desc)', `delete` = '0', `is_multiselect` = 0;

UPDATE IGNORE plugin_extra_service_types SET friendly_name='Annual Domain' WHERE `name`='domain';

