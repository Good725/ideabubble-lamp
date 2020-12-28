# ************************************************************
# Sequel Pro SQL dump
# Version 3408
#
# http://www.sequelpro.com/
# http://code.google.com/p/sequel-pro/
#
# Host: 192.168.2.14 (MySQL 5.1.49-3-log)
# Database: wpp_test_windenergy
# Generation Time: 2012-07-03 08:51:54 +0100
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table loginlogs
# ------------------------------------------------------------

DROP TABLE IF EXISTS `loginlogs`;

CREATE TABLE `loginlogs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ip_address` int(10) unsigned DEFAULT NULL,
  `email` varchar(254) NOT NULL,
  `time` int(11) NOT NULL,
  `success` tinyint(1) NOT NULL,
  `user_agent` varchar(254) NOT NULL,
  `session` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table logs
# ------------------------------------------------------------

DROP TABLE IF EXISTS `logs`;

CREATE TABLE `logs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `type` tinytext NOT NULL,
  `ip_address` int(11) DEFAULT NULL,
  `session` varchar(32) NOT NULL,
  `time` int(11) NOT NULL,
  `page` varchar(254) NOT NULL,
  `message` text NOT NULL,
  PRIMARY KEY (`id`,`time`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table permissions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `permissions`;

CREATE TABLE `permissions` (
  `id` int(16) NOT NULL AUTO_INCREMENT,
  `role_id` int(16) NOT NULL,
  `plugin_name` varchar(64) DEFAULT NULL,
  `permission_code` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table plugin_projects
# ------------------------------------------------------------

DROP TABLE IF EXISTS `plugin_projects`;

CREATE TABLE `plugin_projects` (
  `project_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `project_type` int(8) unsigned DEFAULT NULL,
  `client_id` int(11) NOT NULL,
  `manager_id` int(11) NOT NULL,
  `publish` tinyint(1) NOT NULL,
  `timeline_template` int(11) DEFAULT NULL,
  `coordinates` varchar(100) DEFAULT NULL,
  `planned_install` date DEFAULT NULL,
  `planning_consultant` varchar(100) DEFAULT NULL,
  `planned_mw` float(24,4) DEFAULT NULL,
  `deleted` tinyint(1) NOT NULL,
  PRIMARY KEY (`project_id`),
  UNIQUE KEY `id` (`project_id`),
  KEY `manager` (`manager_id`),
  KEY `deleted` (`deleted`),
  KEY `publish` (`publish`),
  KEY `pd` (`publish`,`deleted`),
  KEY `types` (`project_type`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table plugin_projects_attributes
# ------------------------------------------------------------

DROP TABLE IF EXISTS `plugin_projects_attributes`;

CREATE TABLE `plugin_projects_attributes` (
  `attribute_id` int(11) NOT NULL AUTO_INCREMENT,
  `group` char(50) NOT NULL,
  `label` char(50) NOT NULL DEFAULT 'UNLABELED',
  `type` char(50) NOT NULL,
  `flag` varchar(32) DEFAULT NULL,
  `required` tinyint(1) NOT NULL,
  `publish` tinyint(1) NOT NULL,
  `deleted` tinyint(1) NOT NULL,
  `modified` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`attribute_id`),
  UNIQUE KEY `id` (`attribute_id`),
  KEY `required` (`required`),
  KEY `publish` (`publish`),
  KEY `deleted` (`deleted`),
  KEY `group` (`group`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table plugin_projects_attributes_inputs
# ------------------------------------------------------------

DROP TABLE IF EXISTS `plugin_projects_attributes_inputs`;

CREATE TABLE `plugin_projects_attributes_inputs` (
  `input_id` int(10) NOT NULL AUTO_INCREMENT,
  `input_type` char(50) NOT NULL,
  `available` tinyint(1) NOT NULL,
  PRIMARY KEY (`input_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table plugin_projects_attributes_join
# ------------------------------------------------------------

DROP TABLE IF EXISTS `plugin_projects_attributes_join`;

CREATE TABLE `plugin_projects_attributes_join` (
  `attributes_join_id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `attribute_id` int(11) NOT NULL,
  `value` longtext,
  `date_modified` int(10) DEFAULT NULL,
  `modified_by` int(10) DEFAULT NULL,
  PRIMARY KEY (`attributes_join_id`),
  KEY `id` (`attributes_join_id`),
  KEY `project` (`project_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table plugin_projects_clients
# ------------------------------------------------------------

DROP TABLE IF EXISTS `plugin_projects_clients`;

CREATE TABLE `plugin_projects_clients` (
  `client_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `address` text,
  PRIMARY KEY (`client_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table plugin_projects_comments
# ------------------------------------------------------------

DROP TABLE IF EXISTS `plugin_projects_comments`;

CREATE TABLE `plugin_projects_comments` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `comment` text NOT NULL,
  `author` int(11) NOT NULL,
  `date` int(24) NOT NULL,
  `deleted` tinyint(1) NOT NULL,
  PRIMARY KEY (`comment_id`,`project_id`),
  KEY `comment_id` (`comment_id`),
  KEY `project_id` (`project_id`),
  KEY `date` (`date`),
  KEY `deleted` (`deleted`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table plugin_projects_statuses
# ------------------------------------------------------------

DROP TABLE IF EXISTS `plugin_projects_statuses`;

CREATE TABLE `plugin_projects_statuses` (
  `status_id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(50) NOT NULL,
  PRIMARY KEY (`status_id`),
  KEY `id` (`status_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table plugin_projects_timeline_join
# ------------------------------------------------------------

DROP TABLE IF EXISTS `plugin_projects_timeline_join`;

CREATE TABLE `plugin_projects_timeline_join` (
  `timeline_join_id` int(11) NOT NULL AUTO_INCREMENT,
  `project_id` int(11) NOT NULL,
  `task_id` int(11) NOT NULL,
  `bp_start` date NOT NULL,
  `bp_duration` int(11) NOT NULL,
  `t_start` date DEFAULT NULL,
  `t_duration` int(11) DEFAULT NULL,
  `cf_start` date DEFAULT NULL,
  `cf_duration` int(11) DEFAULT NULL,
  `lf_start` date DEFAULT NULL,
  `lf_duration` int(11) DEFAULT NULL,
  `a_start` date DEFAULT NULL,
  `a_duration` int(11) DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `bp_cost` decimal(10,2) NOT NULL,
  `f_cost` decimal(10,2) DEFAULT NULL,
  `c_cost` decimal(10,2) DEFAULT NULL,
  `complete` int(3) NOT NULL,
  `success_rate` int(11) DEFAULT NULL,
  `status_id` int(1) NOT NULL,
  `reason` int(11) DEFAULT NULL,
  `comment` longtext,
  PRIMARY KEY (`timeline_join_id`),
  KEY `id` (`timeline_join_id`),
  KEY `project` (`project_id`),
  KEY `status` (`status_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table plugin_projects_timeline_tasks
# ------------------------------------------------------------

DROP TABLE IF EXISTS `plugin_projects_timeline_tasks`;

CREATE TABLE `plugin_projects_timeline_tasks` (
  `task_id` int(11) NOT NULL AUTO_INCREMENT,
  `template_id` int(11) NOT NULL,
  `task` varchar(50) NOT NULL,
  `milestone` tinyint(1) NOT NULL,
  `order` int(11) NOT NULL,
  `duration` int(11) NOT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `timeline_parent_id` int(11) DEFAULT NULL,
  `cost` decimal(10,2) NOT NULL DEFAULT '0.00',
  `discount_rate` decimal(4,2) NOT NULL,
  `success_rate` decimal(4,2) NOT NULL,
  `override_rate` text,
  `publish` tinyint(1) NOT NULL,
  `deleted` tinyint(1) NOT NULL,
  PRIMARY KEY (`task_id`),
  KEY `id` (`task_id`),
  KEY `template` (`template_id`),
  KEY `milestone` (`milestone`),
  KEY `order` (`order`),
  KEY `parent` (`parent_id`),
  KEY `imeline parent` (`timeline_parent_id`),
  KEY `publish` (`publish`),
  KEY `deleted` (`deleted`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table plugin_projects_timeline_template
# ------------------------------------------------------------

DROP TABLE IF EXISTS `plugin_projects_timeline_template`;

CREATE TABLE `plugin_projects_timeline_template` (
  `template_id` int(11) NOT NULL AUTO_INCREMENT,
  `template` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`template_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table plugin_projects_types
# ------------------------------------------------------------

DROP TABLE IF EXISTS `plugin_projects_types`;

CREATE TABLE `plugin_projects_types` (
  `type_id` int(16) unsigned NOT NULL AUTO_INCREMENT,
  `type` varchar(24) DEFAULT NULL,
  PRIMARY KEY (`type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table plugin_projects_types_join
# ------------------------------------------------------------

DROP TABLE IF EXISTS `plugin_projects_types_join`;

CREATE TABLE `plugin_projects_types_join` (
  `join_id` int(16) unsigned NOT NULL AUTO_INCREMENT,
  `group` varchar(24) DEFAULT NULL,
  `project_type_id` int(16) DEFAULT NULL,
  PRIMARY KEY (`join_id`),
  KEY `group` (`group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table plugin_todos
# ------------------------------------------------------------

DROP TABLE IF EXISTS `plugin_todos`;

CREATE TABLE `plugin_todos` (
  `todo_id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(200) COLLATE utf8_bin DEFAULT NULL,
  `details` varchar(2000) COLLATE utf8_bin DEFAULT NULL,
  `from_user_id` int(11) NOT NULL,
  `to_user_id` int(11) DEFAULT NULL,
  `status_id` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `priority_id` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `due_date` datetime DEFAULT NULL,
  `related_to_plugin` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  `related_to_id` varchar(45) COLLATE utf8_bin DEFAULT NULL,
  PRIMARY KEY (`todo_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_bin;



# Dump of table plugins
# ------------------------------------------------------------

DROP TABLE IF EXISTS `plugins`;

CREATE TABLE `plugins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `version` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `folder` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `menu` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `type` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `is_frontend` tinyint(1) NOT NULL,
  `is_backend` tinyint(1) NOT NULL,
  `enabled` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;



# Dump of table project_company
# ------------------------------------------------------------

DROP TABLE IF EXISTS `project_company`;

CREATE TABLE `project_company` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company` varchar(45) NOT NULL,
  `description` varchar(60) DEFAULT NULL,
  `publish` tinyint(1) NOT NULL,
  `deleted` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;



# Dump of table project_role
# ------------------------------------------------------------

DROP TABLE IF EXISTS `project_role`;

CREATE TABLE `project_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role` varchar(45) NOT NULL,
  `description` varchar(45) DEFAULT NULL,
  `publish` tinyint(1) NOT NULL,
  `deleted` tinyint(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=COMPACT;



# Dump of table roles
# ------------------------------------------------------------

DROP TABLE IF EXISTS `roles`;

CREATE TABLE `roles` (
  `id` int(16) unsigned NOT NULL AUTO_INCREMENT,
  `parent_id` int(16) NOT NULL,
  `name` varchar(32) NOT NULL,
  `description` varchar(255) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table settings
# ------------------------------------------------------------

DROP TABLE IF EXISTS `settings`;

CREATE TABLE `settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `variable` varchar(64) DEFAULT NULL,
  `name` varchar(64) DEFAULT NULL,
  `value_live` longtext,
  `value_stage` longtext,
  `value_test` longtext,
  `value_dev` longtext,
  `default` longtext,
  `location` varchar(64) NOT NULL DEFAULT 'both',
  `note` text,
  `type` varchar(64) NOT NULL,
  `group` varchar(64) NOT NULL,
  `required` tinyint(1) NOT NULL,
  `options` longtext NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table user_tokens
# ------------------------------------------------------------

DROP TABLE IF EXISTS `user_tokens`;

CREATE TABLE `user_tokens` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `user_agent` varchar(40) NOT NULL,
  `token` varchar(64) NOT NULL,
  `type` varchar(100) NOT NULL,
  `created` int(10) unsigned NOT NULL,
  `expires` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_token` (`token`),
  KEY `fk_user_id` (`user_id`),
  CONSTRAINT `user_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `role_id` int(16) NOT NULL,
  `email` varchar(254) NOT NULL,
  `password` varchar(64) NOT NULL,
  `logins` int(10) unsigned NOT NULL DEFAULT '0',
  `last_login` int(10) unsigned DEFAULT NULL,
  `logins_fail` int(10) NOT NULL DEFAULT '0',
  `last_fail` int(10) DEFAULT NULL,
  `name` varchar(50) DEFAULT NULL,
  `surname` varchar(50) DEFAULT NULL,
  `address` tinytext,
  `phone` varchar(50) DEFAULT NULL,
  `registered` datetime DEFAULT NULL,
  `can_login` bit(1) NOT NULL DEFAULT b'1',
  `deleted` bit(1) NOT NULL DEFAULT b'0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `uniq_email` (`email`),
  KEY `can_login` (`can_login`),
  KEY `deleted` (`deleted`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table users_req
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users_req`;

CREATE TABLE `users_req` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `e-mail` varchar(45) DEFAULT NULL,
  `forename` varchar(45) DEFAULT NULL,
  `surname` varchar(45) DEFAULT NULL,
  `tel` varchar(15) DEFAULT NULL,
  `mob` varchar(15) DEFAULT NULL,
  `status` varchar(1) NOT NULL DEFAULT 'N',
  `users_id` int(11) unsigned NOT NULL,
  `date_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `fk_users_req_users` (`users_id`),
  CONSTRAINT `fk_users_req_users` FOREIGN KEY (`users_id`) REFERENCES `users` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table view_plugin_attributes
# ------------------------------------------------------------

DROP VIEW IF EXISTS `view_plugin_attributes`;

CREATE TABLE `view_plugin_attributes` (
   `attribute_id` INT(11) NOT NULL DEFAULT '0',
   `group` CHAR(50) NOT NULL,
   `label` CHAR(50) NOT NULL DEFAULT 'UNLABELED',
   `type` CHAR(50) NOT NULL,
   `required` TINYINT(1) NOT NULL,
   `value` LONGTEXT DEFAULT NULL,
   `title` VARCHAR(100) NOT NULL,
   `project_id` INT(11) NOT NULL
) ENGINE=MyISAM;



# Dump of table view_plugin_projects
# ------------------------------------------------------------

DROP VIEW IF EXISTS `view_plugin_projects`;

CREATE TABLE `view_plugin_projects` (
   `project_id` INT(11) NOT NULL DEFAULT '0',
   `title` VARCHAR(100) NOT NULL,
   `type` VARCHAR(24) DEFAULT NULL,
   `client_id` INT(11) NOT NULL,
   `planned_mw` FLOAT(24) DEFAULT NULL,
   `publish` TINYINT(1) NOT NULL,
   `timeline_template` INT(11) DEFAULT NULL,
   `planned_install` DATE DEFAULT NULL,
   `coordinates` VARCHAR(100) DEFAULT NULL,
   `deleted` TINYINT(1) NOT NULL,
   `client_name` VARCHAR(100) NOT NULL
) ENGINE=MyISAM;



# Dump of table view_plugin_projects_attribute_forms
# ------------------------------------------------------------

DROP VIEW IF EXISTS `view_plugin_projects_attribute_forms`;

CREATE TABLE `view_plugin_projects_attribute_forms` (
   `group` CHAR(50) NOT NULL,
   `type` CHAR(50) NOT NULL,
   `project_id` INT(11) NOT NULL,
   `value` LONGTEXT DEFAULT NULL,
   `label` CHAR(50) NOT NULL DEFAULT 'UNLABELED',
   `attribute_id` INT(11) NOT NULL DEFAULT '0',
   `flag` VARCHAR(32) DEFAULT NULL
) ENGINE=MyISAM;



# Dump of table view_plugin_projects_categories
# ------------------------------------------------------------

DROP VIEW IF EXISTS `view_plugin_projects_categories`;

CREATE TABLE `view_plugin_projects_categories` (
   `group` CHAR(50) NOT NULL
) ENGINE=MyISAM;



# Dump of table view_plugin_projects_link_timelines
# ------------------------------------------------------------

DROP VIEW IF EXISTS `view_plugin_projects_link_timelines`;

CREATE TABLE `view_plugin_projects_link_timelines` (
   `project_id` INT(11) NOT NULL DEFAULT '0',
   `timeline_template` INT(11) DEFAULT NULL,
   `template` VARCHAR(50) DEFAULT NULL
) ENGINE=MyISAM;



# Dump of table view_plugin_projects_list
# ------------------------------------------------------------

DROP VIEW IF EXISTS `view_plugin_projects_list`;

CREATE TABLE `view_plugin_projects_list` (
   `p_id` INT(11) NOT NULL DEFAULT '0',
   `project_type` INT(8) UNSIGNED DEFAULT NULL,
   `p_title` VARCHAR(100) NOT NULL,
   `cli_name` VARCHAR(100) NOT NULL
) ENGINE=MyISAM;



# Dump of table view_plugin_projects_task_join
# ------------------------------------------------------------

DROP VIEW IF EXISTS `view_plugin_projects_task_join`;

CREATE TABLE `view_plugin_projects_task_join` (
   `timeline_join_id` INT(11) NOT NULL DEFAULT '0',
   `project_id` INT(11) NOT NULL,
   `task_id` INT(11) NOT NULL,
   `bp_start` DATE NOT NULL,
   `bp_duration` INT(11) NOT NULL,
   `t_start` DATE DEFAULT NULL,
   `t_duration` INT(11) DEFAULT NULL,
   `cf_start` DATE DEFAULT NULL,
   `cf_duration` INT(11) DEFAULT NULL,
   `lf_start` DATE DEFAULT NULL,
   `lf_duration` INT(11) DEFAULT NULL,
   `a_start` DATE DEFAULT NULL,
   `a_duration` INT(11) DEFAULT NULL,
   `end_date` DATE DEFAULT NULL,
   `bp_cost` DECIMAL(10) NOT NULL,
   `f_cost` DECIMAL(10) DEFAULT NULL,
   `c_cost` DECIMAL(10) DEFAULT NULL,
   `complete` INT(3) NOT NULL,
   `success_rate` INT(11) DEFAULT NULL,
   `status_id` INT(1) NOT NULL,
   `status` VARCHAR(50) DEFAULT NULL,
   `reason` INT(11) DEFAULT NULL,
   `comment` LONGTEXT DEFAULT NULL,
   `cost` DECIMAL(10) NOT NULL DEFAULT '0.00',
   `deleted` TINYINT(1) NOT NULL,
   `discount_rate` DECIMAL(4) NOT NULL,
   `duration` INT(11) NOT NULL,
   `milestone` TINYINT(1) NOT NULL,
   `order` INT(11) NOT NULL,
   `override_rate` TEXT DEFAULT NULL,
   `parent_id` INT(11) DEFAULT NULL,
   `publish` TINYINT(1) NOT NULL,
   `task` VARCHAR(50) NOT NULL,
   `template_id` INT(11) NOT NULL,
   `timeline_parent_id` INT(11) DEFAULT NULL
) ENGINE=MyISAM;



# Dump of table view_plugin_projects_total_groups
# ------------------------------------------------------------

DROP VIEW IF EXISTS `view_plugin_projects_total_groups`;

CREATE TABLE `view_plugin_projects_total_groups` (
   `attribute_id` INT(11) NOT NULL DEFAULT '0',
   `total` BIGINT(21) NOT NULL DEFAULT '0',
   `group` CHAR(50) NOT NULL
) ENGINE=MyISAM;





# Replace placeholder table for view_plugin_attributes with correct view syntax
# ------------------------------------------------------------

DROP TABLE `view_plugin_attributes`;
CREATE ALGORITHM=UNDEFINED DEFINER=`ib_test`@`%` SQL SECURITY DEFINER VIEW `view_plugin_attributes`
AS select
   `plugin_projects_attributes`.`attribute_id` AS `attribute_id`,
   `plugin_projects_attributes`.`group` AS `group`,
   `plugin_projects_attributes`.`label` AS `label`,
   `plugin_projects_attributes`.`type` AS `type`,
   `plugin_projects_attributes`.`required` AS `required`,
   `plugin_projects_attributes_join`.`value` AS `value`,
   `plugin_projects`.`title` AS `title`,
   `plugin_projects_attributes_join`.`project_id` AS `project_id`
from ((`plugin_projects_attributes` join `plugin_projects_attributes_join` on((`plugin_projects_attributes`.`attribute_id` = `plugin_projects_attributes_join`.`attribute_id`))) join `plugin_projects` on((`plugin_projects_attributes_join`.`project_id` = `plugin_projects`.`project_id`)))
where ((`plugin_projects_attributes_join`.`attribute_id` = `plugin_projects_attributes`.`attribute_id`) and (`plugin_projects_attributes_join`.`project_id` = `plugin_projects`.`project_id`));


# Replace placeholder table for view_plugin_projects_total_groups with correct view syntax
# ------------------------------------------------------------

DROP TABLE `view_plugin_projects_total_groups`;
CREATE ALGORITHM=UNDEFINED DEFINER=`ib_test`@`%` SQL SECURITY DEFINER VIEW `view_plugin_projects_total_groups`
AS select
   `plugin_projects_attributes`.`attribute_id` AS `attribute_id`,count(`plugin_projects_attributes`.`group`) AS `total`,
   `plugin_projects_attributes`.`group` AS `group`
from `plugin_projects_attributes` group by `plugin_projects_attributes`.`group`;


# Replace placeholder table for view_plugin_projects with correct view syntax
# ------------------------------------------------------------

DROP TABLE `view_plugin_projects`;
CREATE ALGORITHM=UNDEFINED DEFINER=`ib_test`@`%` SQL SECURITY DEFINER VIEW `view_plugin_projects`
AS select
   `proj`.`project_id` AS `project_id`,
   `proj`.`title` AS `title`,
   `types`.`type` AS `type`,
   `proj`.`client_id` AS `client_id`,
   `proj`.`planned_mw` AS `planned_mw`,
   `proj`.`publish` AS `publish`,
   `proj`.`timeline_template` AS `timeline_template`,
   `proj`.`planned_install` AS `planned_install`,
   `proj`.`coordinates` AS `coordinates`,
   `proj`.`deleted` AS `deleted`,
   `cl`.`name` AS `client_name`
from ((`plugin_projects_types` `types` join `plugin_projects` `proj`) join `plugin_projects_clients` `cl`)
where ((`proj`.`client_id` = `cl`.`client_id`) and (`types`.`type_id` = `proj`.`project_type`)) order by `proj`.`project_id`;


# Replace placeholder table for view_plugin_projects_categories with correct view syntax
# ------------------------------------------------------------

DROP TABLE `view_plugin_projects_categories`;
CREATE ALGORITHM=UNDEFINED DEFINER=`ib_test`@`%` SQL SECURITY DEFINER VIEW `view_plugin_projects_categories`
AS select
   `plugin_projects_attributes`.`group` AS `group`
from `plugin_projects_attributes`
where ((`plugin_projects_attributes`.`deleted` = 0) and (`plugin_projects_attributes`.`publish` = 1)) group by `plugin_projects_attributes`.`group`;


# Replace placeholder table for view_plugin_projects_list with correct view syntax
# ------------------------------------------------------------

DROP TABLE `view_plugin_projects_list`;
CREATE ALGORITHM=UNDEFINED DEFINER=`ib_test`@`%` SQL SECURITY DEFINER VIEW `view_plugin_projects_list`
AS select
   `projects`.`project_id` AS `p_id`,
   `projects`.`project_type` AS `project_type`,
   `projects`.`title` AS `p_title`,
   `clients`.`name` AS `cli_name`
from (`plugin_projects` `projects` join `plugin_projects_clients` `clients` on((`projects`.`client_id` = `clients`.`client_id`)))
where ((`projects`.`deleted` = 0) and (`projects`.`publish` = 1));


# Replace placeholder table for view_plugin_projects_attribute_forms with correct view syntax
# ------------------------------------------------------------

DROP TABLE `view_plugin_projects_attribute_forms`;
CREATE ALGORITHM=UNDEFINED DEFINER=`ib_test`@`%` SQL SECURITY DEFINER VIEW `view_plugin_projects_attribute_forms`
AS select
   `plugin_projects_attributes`.`group` AS `group`,
   `plugin_projects_attributes`.`type` AS `type`,
   `plugin_projects_attributes_join`.`project_id` AS `project_id`,
   `plugin_projects_attributes_join`.`value` AS `value`,
   `plugin_projects_attributes`.`label` AS `label`,
   `plugin_projects_attributes`.`attribute_id` AS `attribute_id`,
   `plugin_projects_attributes`.`flag` AS `flag`
from (`plugin_projects_attributes` join `plugin_projects_attributes_join`)
where ((`plugin_projects_attributes`.`attribute_id` = `plugin_projects_attributes_join`.`attribute_id`) and (`plugin_projects_attributes`.`publish` = 1) and (`plugin_projects_attributes`.`deleted` = 0)) order by `plugin_projects_attributes_join`.`project_id`;


# Replace placeholder table for view_plugin_projects_task_join with correct view syntax
# ------------------------------------------------------------

DROP TABLE `view_plugin_projects_task_join`;
CREATE ALGORITHM=UNDEFINED DEFINER=`ib_test`@`%` SQL SECURITY DEFINER VIEW `view_plugin_projects_task_join`
AS select
   `plugin_projects_timeline_join`.`timeline_join_id` AS `timeline_join_id`,
   `plugin_projects_timeline_join`.`project_id` AS `project_id`,
   `plugin_projects_timeline_join`.`task_id` AS `task_id`,
   `plugin_projects_timeline_join`.`bp_start` AS `bp_start`,
   `plugin_projects_timeline_join`.`bp_duration` AS `bp_duration`,
   `plugin_projects_timeline_join`.`t_start` AS `t_start`,
   `plugin_projects_timeline_join`.`t_duration` AS `t_duration`,
   `plugin_projects_timeline_join`.`cf_start` AS `cf_start`,
   `plugin_projects_timeline_join`.`cf_duration` AS `cf_duration`,
   `plugin_projects_timeline_join`.`lf_start` AS `lf_start`,
   `plugin_projects_timeline_join`.`lf_duration` AS `lf_duration`,
   `plugin_projects_timeline_join`.`a_start` AS `a_start`,
   `plugin_projects_timeline_join`.`a_duration` AS `a_duration`,
   `plugin_projects_timeline_join`.`end_date` AS `end_date`,
   `plugin_projects_timeline_join`.`bp_cost` AS `bp_cost`,
   `plugin_projects_timeline_join`.`f_cost` AS `f_cost`,
   `plugin_projects_timeline_join`.`c_cost` AS `c_cost`,
   `plugin_projects_timeline_join`.`complete` AS `complete`,
   `plugin_projects_timeline_join`.`success_rate` AS `success_rate`,
   `plugin_projects_timeline_join`.`status_id` AS `status_id`,
   `plugin_projects_statuses`.`label` AS `status`,
   `plugin_projects_timeline_join`.`reason` AS `reason`,
   `plugin_projects_timeline_join`.`comment` AS `comment`,
   `plugin_projects_timeline_tasks`.`cost` AS `cost`,
   `plugin_projects_timeline_tasks`.`deleted` AS `deleted`,
   `plugin_projects_timeline_tasks`.`discount_rate` AS `discount_rate`,
   `plugin_projects_timeline_tasks`.`duration` AS `duration`,
   `plugin_projects_timeline_tasks`.`milestone` AS `milestone`,
   `plugin_projects_timeline_tasks`.`order` AS `order`,
   `plugin_projects_timeline_tasks`.`override_rate` AS `override_rate`,
   `plugin_projects_timeline_tasks`.`parent_id` AS `parent_id`,
   `plugin_projects_timeline_tasks`.`publish` AS `publish`,
   `plugin_projects_timeline_tasks`.`task` AS `task`,
   `plugin_projects_timeline_tasks`.`template_id` AS `template_id`,
   `plugin_projects_timeline_tasks`.`timeline_parent_id` AS `timeline_parent_id`
from (((`plugin_projects_timeline_join` join `plugin_projects_timeline_tasks`) join `plugin_projects`) left join `plugin_projects_statuses` on((`plugin_projects_timeline_join`.`status_id` = `plugin_projects_statuses`.`status_id`)))
where ((`plugin_projects_timeline_tasks`.`task_id` = `plugin_projects_timeline_join`.`task_id`) and (`plugin_projects_timeline_join`.`project_id` = `plugin_projects`.`project_id`) and (`plugin_projects_timeline_tasks`.`publish` = 1) and (`plugin_projects_timeline_tasks`.`deleted` = 0));


# Replace placeholder table for view_plugin_projects_link_timelines with correct view syntax
# ------------------------------------------------------------

DROP TABLE `view_plugin_projects_link_timelines`;
CREATE ALGORITHM=UNDEFINED DEFINER=`ib_test`@`%` SQL SECURITY DEFINER VIEW `view_plugin_projects_link_timelines`
AS select
   `plugin_projects`.`project_id` AS `project_id`,
   `plugin_projects`.`timeline_template` AS `timeline_template`,
   `plugin_projects_timeline_template`.`template` AS `template`
from (`plugin_projects` join `plugin_projects_timeline_template`)
where (`plugin_projects`.`timeline_template` = `plugin_projects_timeline_template`.`template_id`);

/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
