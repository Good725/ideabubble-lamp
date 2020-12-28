/*
ts:2015-01-01 00:00:13
*/
CREATE TABLE IF NOT EXISTS  `plugin_dashboards_dashboards` (
  `id`            INT          NOT NULL AUTO_INCREMENT ,
  `title`         VARCHAR(255) NOT NULL ,
  `description`   VARCHAR(255) NULL ,
  `layout_id`     INT(11)      NULL ,
  `date_filter`   INT(1)       NOT NULL DEFAULT 0 ,
  `date_created`  TIMESTAMP    NULL     DEFAULT CURRENT_TIMESTAMP ,
  `date_modified` TIMESTAMP    NULL ,
  `created_by`    INT(11)      NULL ,
  `modified_by`   INT(11)      NULL ,
  `publish`       INT(1)       NOT NULL DEFAULT 1 ,
  `deleted`       INT(1)       NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`)
);

ALTER IGNORE TABLE `plugin_dashboards_dashboards` RENAME TO `plugin_dashboards` ;

CREATE TABLE IF NOT EXISTS `plugin_dashboards_sharing` (
  `dashboard_id` INT(11) NOT NULL ,
  `group_id`  INT(11) NOT NULL ,
  PRIMARY KEY (`dashboard_id`, `group_id`)
);

ALTER IGNORE TABLE `plugin_dashboards`
  DROP COLUMN `layout_id` ,
  ADD COLUMN `columns` INT(2) NOT NULL DEFAULT 3 AFTER `description` ;

CREATE TABLE IF NOT EXISTS `plugin_dashboards_favorites` (
  `dashboard_id` INT(11) NOT NULL ,
  `user_id`      INT(11) NOT NULL ,
  PRIMARY KEY (`dashboard_id`, `user_id`)
);

CREATE TABLE IF NOT EXISTS `plugin_dashboards_gadgets` (
  `id`             INT(11)   NOT NULL AUTO_INCREMENT ,
  `dashboard_id`   INT(11)   NOT NULL ,
  `gadget_id`      INT(11)   NOT NULL ,
  `type_id`        INT(11)   NOT NULL ,
  `column`         INT(2)    NOT NULL ,
  `order`          INT(2)    NULL ,
  `created_by`     INT(11)   NULL ,
  `modified_by`    INT(11)   NULL ,
  `date_created`   TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ,
  `date_modified`  TIMESTAMP NULL ,
  `publish`        INT(1)    NOT NULL DEFAULT 1 ,
  `deleted`        INT(1)    NOT NULL DEFAULT 0 ,
  PRIMARY KEY (`id`)
 );

CREATE TABLE IF NOT EXISTS `plugin_dashboards_gadget_types` (
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

INSERT IGNORE INTO `plugin_dashboards_gadget_types` (`name`, `stub`)
 VALUES ('Widget', 'widget'), ('Sparkline', 'sparkline');


-- default dashboard
INSERT IGNORE INTO `plugin_dashboards` (`title`, `columns`, `date_filter`, `date_modified`) VALUES ('Default', 3, 0, CURRENT_TIMESTAMP);

INSERT IGNORE INTO `plugin_dashboards_gadgets` (`dashboard_id`, `gadget_id`, `type_id`, `column`, `order`)
  -- top web pages widget
  SELECT `dashboard`.`id` AS `dashboard_id`,  `gadget`.`id` AS `gadget_id`, `type`.`id`, 1, 1 FROM `plugin_dashboards` `dashboard`, `plugin_reports_reports` `gadget`, `plugin_dashboards_gadget_types` `type`
  WHERE `dashboard`.`title` = 'Default' AND `gadget`.`name`  = 'Top Web Pages'    AND `type`.`stub` = 'Widget'
  -- website traffic widget
  UNION SELECT `dashboard`.`id` AS `dashboard_id`,  `gadget`.`id` AS `gadget_id`, `type`.`id`, 2, 1 FROM `plugin_dashboards` `dashboard`, `plugin_reports_reports` `gadget`, `plugin_dashboards_gadget_types` `type`
  WHERE `dashboard`.`title` = 'Default' AND `gadget`.`name`  = 'Website Traffic'  AND `type`.`stub` = 'Widget'
  -- top referrals widget
  UNION SELECT `dashboard`.`id` AS `dashboard_id`,  `gadget`.`id` AS `gadget_id`, `type`.`id`, 3, 1 FROM `plugin_dashboards` `dashboard`, `plugin_reports_reports` `gadget`, `plugin_dashboards_gadget_types` `type`
  WHERE `dashboard`.`title` = 'Default' AND `gadget`.`name`  = 'Top Referrals'     AND `type`.`stub` = 'Widget'
  -- Twitter feed widget
  UNION SELECT `dashboard`.`id` AS `dashboard_id`,  `gadget`.`id` AS `gadget_id`, `type`.`id`, 1, 2 FROM `plugin_dashboards` `dashboard`, `plugin_reports_reports` `gadget`, `plugin_dashboards_gadget_types` `type`
  WHERE `dashboard`.`title` = 'Default' AND	`gadget`.`name`  = 'IB Twitter Feed' AND `type`.`stub` = 'Widget'
;
