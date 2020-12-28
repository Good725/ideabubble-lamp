/*
ts:2015-01-01 00:00:02
*/
CREATE TABLE IF NOT EXISTS `plugin_reports_reports` (
  `id` SMALLINT UNSIGNED AUTO_INCREMENT ,
  `name` VARCHAR(255),
  `summary` VARCHAR(255),
  `sql` TEXT,
  `category` INT,
  `sub_category` INT,
  `dashboard` bit DEFAULT 0,
  `created_by` INT NULL ,
  `modified_by` INT NULL ,
  `date_created` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `date_modified` TIMESTAMP NULL ,
  `publish` bit DEFAULT 1 ,
  `delete` bit DEFAULT 0 ,
  PRIMARY KEY (`id`) ) ENGINE = InnoDB;

  CREATE TABLE IF NOT EXISTS `plugin_reports_categories` (
  `id` SMALLINT UNSIGNED AUTO_INCREMENT ,
  `name` VARCHAR(255),
  `parent` INT,
  `summary` VARCHAR(255),
  `content` TEXT,
  `order` INT,
  `created_by` INT NULL ,
  `modified_by` INT NULL ,
  `date_created` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `date_modified` TIMESTAMP NULL ,
  `publish` bit DEFAULT 1 ,
  `delete` bit DEFAULT 0 ,
  PRIMARY KEY (`id`) ) ENGINE = InnoDB;

   CREATE TABLE IF NOT EXISTS `plugin_reports_saved_reports` (
  `id` SMALLINT UNSIGNED AUTO_INCREMENT ,
  `name` VARCHAR(255),
  `report_id` INT,
  `data` TEXT,
  `created_by` INT NULL ,
  `modified_by` INT NULL ,
  `date_created` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `date_modified` TIMESTAMP NULL ,
  `publish` bit DEFAULT 1 ,
  `delete` bit DEFAULT 0 ,
  PRIMARY KEY (`id`) ) ENGINE = InnoDB;

  CREATE TABLE IF NOT EXISTS `plugin_reports_widgets` (
  `id` INT UNSIGNED AUTO_INCREMENT ,
  `name` VARCHAR(255),
  `type` INT,
  `x_axis` VARCHAR(255),
  `y_axis` VARCHAR(255),
  `created_by` INT NULL ,
  `modified_by` INT NULL ,
  `date_created` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `date_modified` TIMESTAMP NULL ,
  `publish` bit DEFAULT 1 ,
  `delete` bit DEFAULT 0 ,
  PRIMARY KEY (`id`) ) ENGINE = InnoDB;

  ALTER IGNORE TABLE `plugin_reports_reports` ADD `widget_id` INT;
  ALTER IGNORE TABLE `plugin_reports_reports` ADD `chart_id` INT;

  CREATE TABLE IF NOT EXISTS `plugin_reports_charts` (
  `id` INT UNSIGNED AUTO_INCREMENT ,
  `title` VARCHAR(255),
  `type` INT,
  `x_axis` VARCHAR(255),
  `y_axis` VARCHAR(255),
  `created_by` INT NULL ,
  `modified_by` INT NULL ,
  `date_created` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `date_modified` TIMESTAMP NULL ,
  `publish` bit DEFAULT 1 ,
  `delete` bit DEFAULT 0 ,
  PRIMARY KEY (`id`) ) ENGINE = InnoDB;

-- Dummy Data for Reports --
INSERT IGNORE INTO `plugin_reports_categories` VALUES ('1', 'Sales', '0', '', '', null, null, null, '2014-04-22 17:27:10', '2014-04-22 17:27:10', b'1', b'0');
INSERT IGNORE INTO `plugin_reports_reports` (`id`,`name`,`summary`,`sql`,`category`,`sub_category`,`dashboard`,`created_by`,`modified_by`,`date_created`,`date_modified`,`publish`,`delete`,`widget_id`,`chart_id`) VALUES ('1', 'Sales 3 Months to Date', 'This report shows total sales from the last 3 months.', 'SELECT SUM(payment_amount) AS `amount`, DATE_FORMAT(purchase_time,\'%b\') AS `Date`,paid FROM `plugin_payments_log` WHERE `purchase_time` IS NOT NULL GROUP BY Month (`purchase_time`) ORDER BY `purchase_time` ASC LIMIT 3', '0', '0', '0', null, null, '2014-04-29 11:28:24', '2014-04-29 11:28:24', '1', '0', '7', '5'), ('3', 'Sales to date by month', 'Sales to date by month', 'SELECT SUM(payment_amount) AS `Money`, DATE_FORMAT(purchase_time,\'%b\') AS `Date`,paid FROM `plugin_payments_log` WHERE `purchase_time` IS NOT NULL GROUP BY MONTH (`purchase_time`)', '0', '0', '0', null, null, '2014-04-29 11:40:55', '2014-04-29 11:40:55', '1', '0', '9', '4'), ('4', 'Best Sales Days', 'This report shows the best selling days of the week.', 'SELECT SUM(payment_amount) AS `Money`, DATE_FORMAT(purchase_time,\'%a\') AS `Date`,paid FROM `plugin_payments_log` WHERE `purchase_time` IS NOT NULL GROUP BY WEEKDAY (`purchase_time`)', '0', '0', b'0', null, null, '2014-04-29 11:46:16', '2014-04-29 11:46:16', '1', '0', '10', '6');
INSERT IGNORE INTO `plugin_reports_charts` VALUES ('4', 'Sales to date by month', '1', 'Date', 'Money', null, null, '2014-04-27 21:18:42', null, b'1', b'0'), ('5', '3 Months Sales to Date', '1', 'Date', 'amount', null, null, '2014-04-27 22:16:17', null, b'1', b'0'), ('6', 'Best Selling Days', '2', 'Date', 'Money', null, null, '2014-04-28 19:52:45', null, b'1', b'0');
INSERT IGNORE INTO `plugin_reports_widgets` VALUES ('1', '', '1', '', '', null, null, '2014-04-13 20:16:01', null, b'1', b'0'), ('2', '', '1', '', '', null, null, '2014-04-13 20:16:12', null, b'1', b'0'), ('3', 'Sales Report Widget', '1', '\'Jan\',\'Feb\',\'Mar\',\'Apr\'', '', null, null, '2014-04-13 20:20:11', null, b'1', b'0'), ('4', 'Sales Report Widget', '1', 'Date', '', null, null, '2014-04-13 20:56:21', null, b'1', b'0'), ('5', 'Sales Report Widget', '1', '\'Date\'', '', null, null, '2014-04-13 20:56:42', null, b'1', b'0'), ('6', 'Sales Report Widget', '1', '\'Date\'', '', null, null, '2014-04-13 20:58:01', null, b'1', b'0'), ('7', 'Last 3 Months Total Sales', '2', 'Date', 'amount', null, null, '2014-04-13 20:58:37', null, b'1', b'0'), ('8', 'Sales Report Widget', '1', '\'Date\'', '\'amount\'', null, null, '2014-04-14 19:35:09', null, b'1', b'0'), ('9', 'Sales to date by month', '1', 'Date', 'Money', null, null, '2014-04-22 02:35:36', null, b'1', b'0'), ('10', 'Top Selling Day', '2', 'Date', 'Money', null, null, '2014-04-28 19:52:45', null, b'1', b'0');

CREATE TABLE IF NOT EXISTS `plugin_reports_parameters`(
`id` INT UNSIGNED AUTO_INCREMENT,
`report_id` INT UNSIGNED,
`type` VARCHAR(255),
`name` VARCHAR(255),
`value` VARCHAR(255),
`delete` INT DEFAULT 0,
PRIMARY KEY(`id`)
) ENGINE = InnoDB;

INSERT IGNORE INTO `plugin_reports_reports` VALUES ('5', 'Top Web Pages', 'These are the top 5 most visited pages.', 'SELECT dimensions.ga:pagePath AS `PageName`,metrics.ga:entrances AS `Entrances` FROM google_analytics LIMIT 5', '0', '0', b'0', null, null, '2014-05-16 14:51:43', '2014-05-16 14:51:43', b'1', b'0', '11', '7'), ('6', 'Website Traffic', 'These are the website hits on a month by month basis', 'SELECT dimensions.ga:month AS `Month`,metrics.ga:users AS `Hits` FROM google_analytics', '0', '0', b'0', null, null, '2014-05-16 14:52:54', '2014-05-16 14:52:54', b'1', b'0', '12', '8'), ('7', 'Top Referrals', 'This is the top 5 refferals', 'SELECT dimensions.ga:fullReferrer AS `Refferer`,metrics.ga:users AS `Users` FROM google_analytics LIMIT 5', '0', '0', b'0', null, null, '2014-05-16 14:52:11', '2014-05-16 14:52:11', b'1', b'0', '13', '9');
INSERT IGNORE INTO `plugin_reports_parameters` VALUES ('1', '5', 'text', 'date_from', '2014-01-01', '0'), ('2', '5', 'text', 'date_to', '2014-05-01', '0'), ('3', '5', 'text', 'sort-ireland-desc', '-ga:entrances', '0'), ('5', '7', 'text', 'date_from', '2014-01-01', '0'), ('6', '7', 'text', 'date_to', '2014-05-16', '0'), ('7', '7', 'text', 'sort-users', '-ga:users', '0');
INSERT IGNORE INTO `plugin_reports_charts` VALUES ('7', 'Top Web Pages', '2', 'PageName', 'Entrances', null, null, '2014-05-15 00:32:05', null, b'1', b'0'), ('8', 'Website Traffic', '1', 'Month', 'Hits', null, null, '2014-05-15 02:52:54', null, b'1', b'0'), ('9', 'Top Referrals', '3', 'Refferer', 'Users', null, null, '2014-05-15 22:12:12', null, b'1', b'0');
INSERT IGNORE INTO `plugin_reports_widgets` VALUES ('11', 'Top Web Pages', '2', 'PageName', 'Entrances', null, null, '2014-05-15 00:32:05', null, b'1', b'0'), ('12', 'Website Traffic', '1', 'Month', 'Hits', null, null, '2014-05-15 02:52:54', null, b'1', b'0'), ('13', 'Top Referrals', '3', 'Refferer', 'Users', null, null, '2014-05-15 22:12:12', null, b'1', b'0');

-- ----------------------------
-- WPPROD-300 CMS icons
-- ----------------------------
UPDATE `plugins` SET icon = 'reports2.png' WHERE name = 'reports';

CREATE TABLE IF NOT EXISTS `plugin_reports_keywords` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `url` varchar(255) DEFAULT NULL,
  `keyword` varchar(255) DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `last_position` varchar(255) DEFAULT NULL,
  `current_position` varchar(255) DEFAULT NULL,
  `delete` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

  ALTER IGNORE TABLE `plugin_reports_reports` ADD `link_url` VARCHAR(255);
  ALTER IGNORE TABLE `plugin_reports_reports` ADD `link_column` VARCHAR(255);
  ALTER IGNORE TABLE `plugin_reports_reports` ADD `report_type` VARCHAR(255) DEFAULT 'sql';
  ALTER IGNORE TABLE `plugin_reports_keywords` ADD `report_id` INT;

CREATE TABLE IF NOT EXISTS `plugin_reports_keyword_data` (
`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
`date_run` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
`keyword_id` INT,
`report_id` INT,
`grank` INT,
`brank` INT,
`yrank` INT,
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

ALTER IGNORE TABLE `plugin_reports_reports` ADD `autoload` INT DEFAULT '0';

ALTER IGNORE TABLE `plugin_reports_reports` MODIFY COLUMN `dashboard` INT;
ALTER IGNORE TABLE `plugin_reports_reports` MODIFY COLUMN `publish` INT;
ALTER IGNORE TABLE `plugin_reports_reports` MODIFY COLUMN `delete` INT;
ALTER IGNORE TABLE `plugin_reports_widgets` MODIFY COLUMN `publish` INT;
ALTER IGNORE TABLE `plugin_reports_widgets` MODIFY COLUMN `delete` INT;
ALTER IGNORE TABLE `plugin_reports_reports` MODIFY COLUMN `report_type` VARCHAR(255);

INSERT IGNORE INTO `plugin_reports_reports` (`name`,`summary`,`sql`,`category`,`sub_category`,`dashboard`,`created_by`,`modified_by`,`date_created`,`date_modified`,`publish`,`delete`,`widget_id`,`chart_id`,`report_type`,`link_url`,`link_column`,`autoload`) VALUES ('Recent Payments', 'SQL', 'SELECT\n	`customer_name` AS `Customer`,\n	`customer_telephone` AS `Telephone`,\n	`customer_address` AS `Address`,\n	`customer_email` AS `Email`,\n	(\n		CASE\n		WHEN paid = 1 THEN\n			\'Yes\'\n		ELSE\n			\'No\'\n		END\n	) AS `Paid`,\n	`payment_type`,\n	`payment_amount`\nFROM\n	plugin_payments_log\nORDER BY `purchase_time` DESC', '0', '0', '1', null, null, '2014-10-13 16:18:22', '2014-10-13 16:18:22', '1', '0', '14', '10', '0', '', '', '0'),
('Orders', 'Order', 'SELECT\n	`customer_name` AS `Customer`,\n	group_concat(case when t2.cart_id = plugin_payments_log.cart_id then t2.title end SEPARATOR \',\') AS `Items`,\n	(\n		CASE\n		WHEN paid = 1 THEN\n			\'Yes\'\n		ELSE\n			\'No\'\n		END\n	) AS `Paid`,\n	`payment_amount`\nFROM\n	plugin_payments_log\nLEFT JOIN plugin_products_cart_items AS t2 ON t2.cart_id = plugin_payments_log.cart_id\nGROUP BY plugin_payments_log.cart_id\nORDER BY `purchase_time` DESC', '0', '0', '0', null, null, '2014-10-13 16:23:06', '2014-10-13 16:23:06', '1', '0', '15', '11', '0', '', '', '0'),
('Recent Customers', 'Most Recent Customers', 'SELECT\n	`customer_name` AS `Customer`,\n	`customer_telephone` AS `Telephone`,\n	`customer_address` AS `Address`,\n	`customer_email` AS `Email`\nFROM\n	plugin_payments_log\nORDER BY `purchase_time` DESC', '0', '0', '0', null, null, '2014-10-13 16:27:33', '2014-10-13 16:27:33', '1', '0', '16', '12', '0', '', '', '0');

UPDATE `plugin_reports_reports` SET `sql` = 'SELECT
	SUM(payment_amount)AS `Money`,
	DATE_FORMAT(purchase_time, ''%b'')AS `Date`,
	paid
FROM
	`plugin_payments_log`
WHERE
	`purchase_time` IS NOT NULL AND
	MONTH(`purchase_time`) <> MONTH(now())
GROUP BY
	MONTH(`purchase_time`)' WHERE `name` = 'Sales to date by month';

	INSERT IGNORE INTO `plugin_reports_reports` (`name`,`summary`,`sql`,`category`,`sub_category`,`dashboard`,`created_by`,`modified_by`,`date_created`,`date_modified`,`publish`,`delete`,`widget_id`,`chart_id`,`report_type`,`link_url`,`link_column`,`autoload`) VALUES
	 ('Top Search Terms', '', 'SELECT dimensions.ga:pagePath AS `PageName`,metrics.ga:Keyword AS `Keyword` FROM google_analytics', '0', '0', '1', null, null, '2014-10-20 16:16:21', '2014-10-20 16:16:22', '1', '0', '16', '12', '', '', 'sql', '0'),
	 ('Yahoo Search Ranking', '', '', '0', '0', '1', null, null, '2014-10-20 17:11:42', '2014-10-20 17:11:42', '1', '0', '15', '11', 'serp', '', 'serp', '0'),
	 ('Bing Search Ranking', '', '', '0', '0', '1', null, null, '2014-10-20 17:11:35', '2014-10-20 17:11:35', '1', '0', '16', '12', 'serp', '', 'serp', '0');

ALTER IGNORE TABLE `plugin_reports_reports` ADD `checkbox_column` TINYINT NOT NULL DEFAULT '0';
ALTER IGNORE TABLE `plugin_reports_reports` ADD `action_button_label` varchar(64) DEFAULT NULL;
ALTER IGNORE TABLE `plugin_reports_reports` ADD `action_button` tinyint(4) NOT NULL DEFAULT '0';
ALTER IGNORE TABLE `plugin_reports_reports` ADD `action_event` varchar(128) DEFAULT NULL;
ALTER IGNORE TABLE `plugin_reports_reports` ADD `checkbox_column_label` varchar(64) DEFAULT NULL;
ALTER IGNORE TABLE `plugin_reports_reports` ADD `autosum` tinyint(4) NOT NULL DEFAULT '0';
ALTER IGNORE TABLE `plugin_reports_reports` ADD `column_value` varchar(64) DEFAULT NULL;

-- --------------------------------
-- IBIS-148 User Profiled Reports
-- --------------------------------
CREATE TABLE IF NOT EXISTS `plugin_reports_favorites` (
  `user_id`   INT(11) NOT NULL ,
  `report_id` INT(11) NOT NULL ,
  PRIMARY KEY (`user_id`, `report_id`) );

CREATE TABLE IF NOT EXISTS `plugin_reports_report_sharing` (
  `report_id` INT(11) NOT NULL ,
  `group_id`  INT(11) NOT NULL ,
  PRIMARY KEY (`report_id`, `group_id`) );

ALTER IGNORE TABLE `plugin_reports_reports` MODIFY COLUMN `action_event` TEXT;

ALTER IGNORE TABLE `plugin_reports_reports` ADD `autocheck` INT(1) DEFAULT 0;

ALTER IGNORE TABLE `plugin_reports_reports` ADD `custom_report_rules` TEXT;

CREATE TABLE IF NOT EXISTS `plugin_reports_widget_types` (
  `id`   INT          NOT NULL AUTO_INCREMENT ,
  `stub` VARCHAR(255) NULL ,
  `name` VARCHAR(255) NULL ,
  PRIMARY KEY (`id`) ,
  UNIQUE INDEX `stub_UNIQUE` (`stub` ASC) );

-- These IDs were used when this data was hardcoded
INSERT IGNORE INTO `plugin_reports_widget_types` (`id`, `stub`, `name`) VALUES
('1', 'line_graph',  'Line Graph' ),
('2', 'bar_chart',   'Bar Chart'  ),
('3', 'pie_chart',   'Pie Chart'  ),
('4', 'gannt_chart', 'Gannt Chart'),
('5', 'serp_table',  'SERP Table' ),
('6', 'serp_google', 'SERP GOOGLE'),
('7', 'serp_bing',   'SERP Bing'  ),
('8', 'serp_yahoo',  'SERP Yahoo' ),
('9', 'quick_stats', 'Quick Stats');

INSERT IGNORE INTO `plugin_reports_widget_types` (`stub`, `name`) VALUES ('table',  'Table' );

CREATE TABLE IF NOT EXISTS `plugin_reports_user_options` (
  `id`            INT       NOT NULL AUTO_INCREMENT ,
  `user_id`       INT       NULL ,
  `report_id`     INT       NULL ,
  `order`         INT       NULL ,
  `date_created`  TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP() ,
  `date_modified` TIMESTAMP NULL ,
  PRIMARY KEY (`id`)
 );

INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`) VALUES
('reports_widgets_per_row', 'Number of Report Widgets per row', '3', '3', '3', '3', '3', 'The number of report widgets to appear in a row on the dashboard', 'text', 'Dashboard');

ALTER IGNORE TABLE `plugin_reports_widgets` ADD COLUMN `html` BLOB NULL DEFAULT NULL AFTER `y_axis` ;

INSERT IGNORE INTO `plugin_reports_widget_types` (`stub`, `name`) VALUES ('raw_html', 'Raw HTML');

INSERT IGNORE INTO `plugin_reports_widgets` (`name`, `type`, `html`, `publish`, `delete`) VALUES (
'IB Twitter Feed',
(SELECT `id` FROM `plugin_reports_widget_types` where stub = 'raw_html'),
'<a class=\"twitter-timeline\" href=\"https://twitter.com/ideabubble\" data-widget-id=\"392616846836252672\">Tweets by @ideabubble</a>\n<script>\n	!function (d, s, id) {\n		var js, fjs = d.getElementsByTagName(s)[0], p = /^http:/.test(d.location) ? \'http\' : \'https\';\n		if (!d.getElementById(id)) {\n			js = d.createElement(s);\n			js.id = id;\n			js.src = p + \"://platform.twitter.com/widgets.js\";\n			fjs.parentNode.insertBefore(js, fjs);\n		}\n	}(document, \"script\", \"twitter-wjs\");\n</script>',
1,
0
);

INSERT IGNORE INTO `plugin_reports_reports` (`name`, `dashboard`, `publish`, `delete`, `widget_id`) VALUES
('IB Twitter Feed', '0', '1', '0', LAST_INSERT_ID());

UPDATE IGNORE `plugin_reports_widgets`
SET `html` = '<a class=\"twitter-timeline\" href=\"https://twitter.com/ideabubble\" data-widget-id=\"392616846836252672\">Tweets by @ideabubble</a>\n<script>\n	!function (d, s, id) {\n		var js, fjs = d.getElementsByTagName(s)[0], p = /^http:/.test(d.location) ? \'http\' : \'https\';\n		if (!d.getElementById(id)) {\n			js = d.createElement(s);\n			js.id = id;\n			js.src = p + \"://platform.twitter.com/widgets.js\";\n			fjs.parentNode.insertBefore(js, fjs);\n		}\n	}(document, \"script\", \"twitter-wjs\");\n	if (typeof twttr !== ''undefined'') twttr.widgets.load();\n</script>'
WHERE `name` = 'IB Twitter Feed';

ALTER TABLE plugin_reports_reports ADD COLUMN bulk_message_sms_number_column VARCHAR(255);
ALTER TABLE plugin_reports_reports ADD COLUMN bulk_message_email_column VARCHAR(255);
ALTER TABLE plugin_reports_reports ADD COLUMN bulk_message_subject_column VARCHAR(255);
ALTER TABLE plugin_reports_reports ADD COLUMN bulk_message_subject VARCHAR(255);
ALTER TABLE plugin_reports_reports ADD COLUMN bulk_message_body_column VARCHAR(255);
ALTER TABLE plugin_reports_reports ADD COLUMN bulk_message_body MEDIUMTEXT;
ALTER TABLE plugin_reports_reports ADD COLUMN bulk_message_interval VARCHAR(255);

-- IBCMS-489
CREATE TABLE IF NOT EXISTS plugin_reports_versions
(
	id int auto_increment primary key,
	report_id	int not null,
	created_date	datetime not null,
	created_by	int not null,
	data_json	mediumtext,

	key	(report_id)
) ENGINE = INNODB;

ALTER TABLE plugin_reports_reports ADD COLUMN rolledback_to_version INT;


CREATE TABLE IF NOT EXISTS `plugin_reports_sparklines` (
  `id`               INT          NOT NULL AUTO_INCREMENT ,
  `title`            VARCHAR(255) NULL ,
  `type_id`          INT(11)      NULL ,
  `x_axis`           VARCHAR(255) NULL ,
  `y_axis`           VARCHAR(255) NULL ,
  `total_field`      VARCHAR(255) NULL ,
  `total_type_id`    INT(11)      NULL ,
  `text_color`       VARCHAR(127) NULL ,
  `background_color` VARCHAR(127) NULL ,
  `width`            INT(2)       NULL ,
  `created_by`       INT(11)      NULL ,
  `modified_by`      INT(11)      NULL ,
  `date_created`     TIMESTAMP    NULL DEFAULT CURRENT_TIMESTAMP ,
  `date_modified`    TIMESTAMP    NULL ,
  `publish`          INT(1)       NOT NULL DEFAULT 1 ,
  `deleted`          INT(1)       NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`)
 );


CREATE TABLE IF NOT EXISTS `plugin_reports_chart_types` (
  `id`            INT          NOT NULL AUTO_INCREMENT ,
  `name`          VARCHAR(255) NOT NULL ,
  `stub`          VARCHAR(255) NOT NULL ,
  `created_by`    INT(11)      NULL ,
  `modified_by`   INT(11)      NULL ,
  `date_created`  TIMESTAMP    NULL DEFAULT CURRENT_TIMESTAMP ,
  `date_modified` TIMESTAMP    NULL ,
  `publish`       INT(1)       NOT NULL DEFAULT 1 ,
  `deleted`       INT(1)       NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC) ,
  UNIQUE INDEX `stub_UNIQUE` (`stub` ASC)
 );

INSERT IGNORE INTO `plugin_reports_chart_types` (`name`, `stub`) VALUES ('Bar', 'bar'), ('Line', 'line'), ('Pie', 'pie'),  ('Doughnut', 'donut');

CREATE TABLE IF NOT EXISTS `plugin_reports_total_types` (
  `id`            INT          NOT NULL AUTO_INCREMENT ,
  `name`          VARCHAR(255) NULL ,
  `stub`          VARCHAR(255) NULL ,
  `created_by`    INT(11)      NULL ,
  `modified_by`   INT(11)      NULL ,
  `date_created`  TIMESTAMP    NULL DEFAULT CURRENT_TIMESTAMP ,
  `date_modified` TIMESTAMP    NULL ,
  `publish`       INT(1)       NOT NULL DEFAULT 1 ,
  `deleted`       INT(1)       NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `name_UNIQUE` (`name` ASC) ,
  UNIQUE INDEX `stub_UNIQUE` (`stub` ASC)
 );

INSERT IGNORE INTO `plugin_reports_total_types` (`name`, `stub`) VALUES
  ('Average',        'avg'),
  ('Count',          'count'),
  ('Count Distinct', 'count-distinct'),
  ('Maximum',        'max'),
  ('Minimum',        'min'),
  ('Sum',            'sum')
 ;

ALTER IGNORE TABLE `plugin_reports_sparklines` ADD COLUMN `report_id` INT(11) NOT NULL AFTER `title` ;
ALTER IGNORE TABLE `plugin_reports_sparklines` ADD COLUMN `chart_type_id` INT(11) NOT NULL AFTER `report_id` ;

ALTER TABLE `plugin_reports_reports` ADD COLUMN `php_modifier` TEXT;

ALTER IGNORE TABLE `plugin_reports_reports` ADD COLUMN `widget_sql` TEXT NULL DEFAULT NULL  AFTER `sql` ;

UPDATE IGNORE `plugin_reports_reports` `report`
JOIN `plugin_reports_widgets` `widget` on `report`.`widget_id` = `widget`.`id`
SET `widget_id` = NULL
WHERE `widget`.`name` = 'IB Twitter Feed'
AND `report`.`name` not like '%IB Twitter Feed%';

UPDATE IGNORE `plugin_reports_reports` `report`
JOIN `plugin_reports_widgets` `widget` on `report`.`widget_id` = `widget`.`id`
SET `widget_id` = NULL
WHERE `widget`.`name` = 'Last 3 Months Total Sales'
AND `report`.`name` not like '%Sales 3 Months to Date%';

ALTER TABLE `plugin_reports_parameters` ADD COLUMN `is_multiselect` BIT(1);
ALTER TABLE `plugin_reports_parameters` MODIFY COLUMN `value` TEXT;

UPDATE IGNORE `plugin_reports_reports` SET `report_type`='sql' WHERE `name`='Recent Payments';
