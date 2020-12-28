/*
ts:2018-03-13 09:13:00
*/

INSERT IGNORE INTO `engine_plugins`
  (`name`, `friendly_name`, `show_on_dashboard`, `requires_media`, `media_folder`)
  VALUES
  ('contacts3', 'Contacts 3', '0', '0', NULL);

UPDATE engine_plugins SET show_on_dashboard = 0 WHERE `name` = 'contacts3';

CREATE TABLE IF NOT EXISTS `plugin_contacts3_contact_has_course_subject_preferences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contact_id` varchar(45) NOT NULL,
  `course_subject_id` varchar(45) NOT NULL,
  `value` tinyint(1) DEFAULT NULL,
  `date_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `publish` tinyint(1) NOT NULL DEFAULT '1',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `contact_id` (`contact_id`,`course_subject_id`)
) ENGINE=INNODB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `plugin_contacts3_contact_has_course_type_preferences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contact_id` varchar(45) NOT NULL,
  `course_type_id` varchar(45) NOT NULL,
  `value` tinyint(1) DEFAULT NULL,
  `date_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `publish` tinyint(1) NOT NULL DEFAULT '1',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `contact_id` (`contact_id`,`course_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `plugin_contacts3_contact_has_notifications` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `contact_id` int(11) unsigned NOT NULL,
  `group_id` int(11) unsigned NOT NULL,
  `value` varchar(127) NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `preferred` tinyint(1) DEFAULT NULL,
  `date_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `publish` tinyint(1) NOT NULL DEFAULT '1',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `notification_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `contact_id` (`contact_id`),
  KEY `group_id` (`group_id`),
  KEY `value` (`value`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `plugin_contacts3_contact_has_preferences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contact_id` varchar(45) NOT NULL,
  `preference_id` varchar(45) NOT NULL,
  `value` tinyint(1) DEFAULT NULL,
  `date_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `publish` tinyint(1) NOT NULL DEFAULT '1',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `contact_id` (`contact_id`,`preference_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `plugin_contacts3_contact_has_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contact_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `contact_id_2` (`contact_id`,`role_id`),
  KEY `contact_id` (`contact_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `plugin_contacts3_contact_has_subject_preferences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contact_id` varchar(45) NOT NULL,
  `subject_id` varchar(45) NOT NULL,
  `value` tinyint(1) DEFAULT NULL,
  `date_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `publish` tinyint(1) NOT NULL DEFAULT '1',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `contact_id` (`contact_id`,`subject_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `plugin_contacts3_contact_type` (
  `contact_type_id` int(11) NOT NULL AUTO_INCREMENT,
  `label` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`contact_type_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `plugin_contacts3_contact_type` (`contact_type_id`, `label`) values (1, 'General');
INSERT IGNORE INTO `plugin_contacts3_contact_type` (`contact_type_id`, `label`) values (2, 'Billed Organization');

CREATE TABLE IF NOT EXISTS `plugin_contacts3_contacts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) DEFAULT NULL,
  `type` int(11) DEFAULT NULL,
  `first_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) DEFAULT NULL,
  `date_of_birth` datetime DEFAULT NULL,
  `family_id` int(11) DEFAULT NULL,
  `school_id` int(11) DEFAULT NULL,
  `academic_year_id` int(11) DEFAULT NULL,
  `year_id` int(11) DEFAULT NULL,
  `points_required` int(5) DEFAULT '0',
  `residence` int(11) DEFAULT NULL,
  `notifications_group_id` int(11) DEFAULT NULL,
  `is_primary` tinyint(1) DEFAULT '0',
  `pps_number` varchar(10) DEFAULT NULL,
  `publish` tinyint(1) DEFAULT '1',
  `delete` tinyint(1) DEFAULT '0',
  `date_created` datetime DEFAULT NULL,
  `date_modified` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_flexi_student` tinyint(1) DEFAULT '0',
  `subtype_id` tinyint(4) NOT NULL,
  `is_inactive` tinyint(4) NOT NULL DEFAULT '0',
  `nationality` varchar(100) DEFAULT NULL,
  `gender` varchar(3) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `family_id` (`family_id`),
  KEY `school_id` (`school_id`),
  KEY `notifications_group_id` (`notifications_group_id`),
  KEY `residence` (`residence`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `plugin_contacts3_contacts_subtypes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type_id` int(11) NOT NULL,
  `subtype` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `plugin_contacts3_contacts_subtypes` (`id`, `type_id`, `subtype`) values (1, 1, 'Family');
INSERT IGNORE INTO `plugin_contacts3_contacts_subtypes` (`id`, `type_id`, `subtype`) values (2, 1, 'Staff');
INSERT IGNORE INTO `plugin_contacts3_contacts_subtypes` (`id`, `type_id`, `subtype`) values (3, 2, 'Billed Organization');

CREATE TABLE IF NOT EXISTS `plugin_contacts3_family` (
  `family_id` int(11) NOT NULL AUTO_INCREMENT,
  `family_name` varchar(255) DEFAULT NULL,
  `primary_contact_id` int(11) DEFAULT NULL,
  `notes` text,
  `residence` int(11) DEFAULT NULL,
  `notifications_group_id` int(11) DEFAULT NULL,
  `publish` tinyint(1) DEFAULT '1',
  `delete` tinyint(1) DEFAULT '0',
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `date_modified` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `is_inactive` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`family_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `plugin_contacts3_mytime` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contact_id` int(11) NOT NULL,
  `description` text,
  `start_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_date` date NOT NULL,
  `end_time` time NOT NULL,
  `color` varchar(8) DEFAULT NULL,
  `days` set('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday') DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created` datetime DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated` datetime DEFAULT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  `availability` enum('YES','NO') DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `contact_id` (`contact_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `plugin_contacts3_mytime_subjects` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mytime_id` int(11) NOT NULL,
  `subject_id` int(11) NOT NULL,
  `deleted` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `mytime_id` (`mytime_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `plugin_contacts3_notes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `link_id` int(11) NOT NULL,
  `table_link_id` int(11) NOT NULL,
  `note` blob,
  `date_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `publish` tinyint(1) NOT NULL DEFAULT '1',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `link_id` (`link_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `plugin_contacts3_notes_tables` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `table` varchar(127) NOT NULL,
  `description` varchar(255) NOT NULL,
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `table` (`table`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `plugin_contacts3_notes_tables`
  (`id`, `table`)
  VALUES
 (1, 'plugin_contacts3_contacts'),
 (2, 'plugin_contacts3_family'),
 (3, 'plugin_ib_educate_booking_items'),
 (4, 'plugin_ib_educate_bookings');

CREATE TABLE IF NOT EXISTS `plugin_contacts3_notification_groups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `publish` tinyint(1) NOT NULL DEFAULT '1',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `plugin_contacts3_notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(127) NOT NULL,
  `stub` varchar(127) NOT NULL,
  `date_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `publish` tinyint(1) NOT NULL DEFAULT '1',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `stub_UNIQUE` (`stub`),
  UNIQUE KEY `name_UNIQUE` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `plugin_contacts3_notifications` (`stub`, `name`) VALUES
 ('email',    'Email'),
 ('mobile',   'Mobile'),
 ('landline', 'Landline'),
 ('web',      'Web'),
 ('skype',    'Skype'),
 ('facebook', 'Facebook'),
 ('twitter',  'Twitter');

CREATE TABLE IF NOT EXISTS `plugin_contacts3_preferences` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(127) NOT NULL,
  `stub` varchar(127) NOT NULL,
  `group` varchar(127) NOT NULL,
  `date_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `publish` tinyint(1) NOT NULL DEFAULT '1',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  `required` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `stub` (`stub`,`group`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `plugin_contacts3_preferences` (`id`, `label`, `stub`, `group`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `required`) VALUES (1, 'Emergency', 'emergency', 'notification', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0, 0);
INSERT IGNORE INTO `plugin_contacts3_preferences` (`id`, `label`, `stub`, `group`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `required`) VALUES (2, 'Accounts', 'accounts', 'notification', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0, 0);
INSERT IGNORE INTO `plugin_contacts3_preferences` (`id`, `label`, `stub`, `group`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `required`) VALUES (3, 'Absentee SMS + CALLS', 'absentee', 'notification', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0, 1);
INSERT IGNORE INTO `plugin_contacts3_preferences` (`id`, `label`, `stub`, `group`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `required`) VALUES (4, 'Reminders', 'reminders', 'notification', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0, 0);
INSERT IGNORE INTO `plugin_contacts3_preferences` (`id`, `label`, `stub`, `group`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `required`) VALUES (5, 'SMS Marketing', 'sms_marketing', 'notification', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0, 0);
INSERT IGNORE INTO `plugin_contacts3_preferences` (`id`, `label`, `stub`, `group`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `required`) VALUES (6, 'Email Marketing', 'email_marketing', 'notification', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0, 0);
INSERT IGNORE INTO `plugin_contacts3_preferences` (`id`, `label`, `stub`, `group`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `required`) VALUES (7, 'Time Sheet Alerts', 'time_sheet_alerts', 'notification', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0, 0);
INSERT IGNORE INTO `plugin_contacts3_preferences` (`id`, `label`, `stub`, `group`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `required`) VALUES (8, 'Fainting', 'fainting', 'special', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0, 0);
INSERT IGNORE INTO `plugin_contacts3_preferences` (`id`, `label`, `stub`, `group`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `required`) VALUES (9, 'Diabetes', 'diabetes', 'special', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0, 0);
INSERT IGNORE INTO `plugin_contacts3_preferences` (`id`, `label`, `stub`, `group`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `required`) VALUES (10, 'Asthma', 'asthma', 'special', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0, 0);
INSERT IGNORE INTO `plugin_contacts3_preferences` (`id`, `label`, `stub`, `group`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `required`) VALUES (11, 'Allergy (see notes)', 'allergy', 'special', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0, 0);
INSERT IGNORE INTO `plugin_contacts3_preferences` (`id`, `label`, `stub`, `group`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `required`) VALUES (12, 'Learning disability', 'learning_disability', 'special', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0, 0);
INSERT IGNORE INTO `plugin_contacts3_preferences` (`id`, `label`, `stub`, `group`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `required`) VALUES (13, 'Wheelchair requirements', 'wheelchair_requirements', 'special', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0, 0);
INSERT IGNORE INTO `plugin_contacts3_preferences` (`id`, `label`, `stub`, `group`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `required`) VALUES (14, 'Other', 'other', 'special', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0, 0);
INSERT IGNORE INTO `plugin_contacts3_preferences` (`id`, `label`, `stub`, `group`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `required`) VALUES (15, 'Text Messaging', 'text_messaging', 'contact', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0, 0);
INSERT IGNORE INTO `plugin_contacts3_preferences` (`id`, `label`, `stub`, `group`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `required`) VALUES (16, 'Phone Call', 'phone_call', 'contact', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0, 0);
INSERT IGNORE INTO `plugin_contacts3_preferences` (`id`, `label`, `stub`, `group`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `required`) VALUES (17, 'Email', 'email', 'contact', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0, 0);
INSERT IGNORE INTO `plugin_contacts3_preferences` (`id`, `label`, `stub`, `group`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `required`) VALUES (18, 'Dyslexia', 'dyslexia', 'special', '2016-11-3 21:23:42', NULL, NULL, NULL, 1, 0, 0);
INSERT IGNORE INTO `plugin_contacts3_preferences` (`id`, `label`, `stub`, `group`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `required`) VALUES (19, 'Dyspraxia', 'dyspraxia', 'special', '2016-11-3 21:23:42', NULL, NULL, NULL, 1, 0, 0);
INSERT IGNORE INTO `plugin_contacts3_preferences` (`id`, `label`, `stub`, `group`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `required`) VALUES (20, 'Anxiety', 'anxiety', 'special', '2016-11-3 21:23:42', NULL, NULL, NULL, 1, 0, 0);
INSERT IGNORE INTO `plugin_contacts3_preferences` (`id`, `label`, `stub`, `group`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `required`) VALUES (21, 'ADHD', 'ADHD', 'special', '2016-11-3 21:23:42', NULL, NULL, NULL, 1, 0, 0);
INSERT IGNORE INTO `plugin_contacts3_preferences` (`id`, `label`, `stub`, `group`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `required`) VALUES (22, 'Autism', 'autism', 'special', '2016-11-3 21:23:42', NULL, NULL, NULL, 1, 0, 0);
INSERT IGNORE INTO `plugin_contacts3_preferences` (`id`, `label`, `stub`, `group`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `required`) VALUES (23, 'Aspergers', 'aspergers', 'special', '2016-11-3 21:23:42', NULL, NULL, NULL, 1, 0, 0);
INSERT IGNORE INTO `plugin_contacts3_preferences` (`id`, `label`, `stub`, `group`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `required`) VALUES (24, 'View other profiles', 'db-otr-pf', 'family_permission', '2017-2-21 18:43:47', NULL, NULL, NULL, 1, 0, 0);
INSERT IGNORE INTO `plugin_contacts3_preferences` (`id`, `label`, `stub`, `group`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `required`) VALUES (25, 'Create bookings', 'db-cbs-gu', 'family_permission', '2017-2-21 18:45:09', NULL, NULL, NULL, 1, 0, 0);
INSERT IGNORE INTO `plugin_contacts3_preferences` (`id`, `label`, `stub`, `group`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `required`) VALUES (26, 'Manage attendance', 'db-mn-ads-gu', 'family_permission', '2017-2-21 18:47:24', NULL, NULL, NULL, 1, 0, 0);
INSERT IGNORE INTO `plugin_contacts3_preferences` (`id`, `label`, `stub`, `group`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `required`) VALUES (27, 'Bookings', 'bookings', 'notification', '2017-7-3 10:51:13', NULL, NULL, NULL, 1, 0, 0);

CREATE TABLE IF NOT EXISTS `plugin_contacts3_residences` (
  `address_id` int(11) NOT NULL AUTO_INCREMENT,
  `address1` varchar(255) DEFAULT NULL,
  `address2` varchar(255) DEFAULT NULL,
  `address3` varchar(255) DEFAULT NULL,
  `country` varchar(3) DEFAULT NULL,
  `county` int(11) DEFAULT NULL,
  `postcode` varchar(20) DEFAULT NULL,
  `town` varchar(255) DEFAULT NULL,
  `coordinates` varchar(127) DEFAULT NULL,
  `publish` tinyint(1) DEFAULT '1',
  `delete` tinyint(1) DEFAULT '0',
  PRIMARY KEY (`address_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `plugin_contacts3_roles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(127) DEFAULT NULL,
  `stub` varchar(127) DEFAULT NULL,
  `date_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `publish` tinyint(1) NOT NULL DEFAULT '1',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_UNIQUE` (`name`),
  UNIQUE KEY `stub_UNIQUE` (`stub`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT IGNORE INTO plugin_contacts3_roles (`id`, `name`, `stub`, `publish`, `deleted`) values (1, 'Guardian', 'guardian', 1, 0);
INSERT IGNORE INTO plugin_contacts3_roles (`id`, `name`, `stub`, `publish`, `deleted`) values (2, 'Child', 'child', 1, 0);
INSERT IGNORE INTO plugin_contacts3_roles (`id`, `name`, `stub`, `publish`, `deleted`) values (3, 'Mature', 'mature', 1, 0);
INSERT IGNORE INTO plugin_contacts3_roles (`id`, `name`, `stub`, `publish`, `deleted`) values (4, 'Teacher', 'teacher', 1, 0);
INSERT IGNORE INTO plugin_contacts3_roles (`id`, `name`, `stub`, `publish`, `deleted`) values (5, 'Supervisor', 'supervisor', 1, 0);
INSERT IGNORE INTO plugin_contacts3_roles (`id`, `name`, `stub`, `publish`, `deleted`) values (6, 'Admin', 'admin', 1, 0);

CREATE TABLE IF NOT EXISTS `plugin_contacts3_roles_has_preferences` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) NOT NULL,
  `group` varchar(100) DEFAULT NULL,
  `preference` varchar(100) DEFAULT NULL,
  `allowed` tinyint(4) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `plugin_contacts3_todos_relation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `family_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `todo_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `plugin_contacts3_users_has_permission` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contact3_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `contact3_id` (`contact3_id`),
  KEY `user_id` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE plugin_contacts3_sync
(
  contact_id  INT NOT NULL,
  contact3_id INT NOT NULL,

  UNIQUE KEY(contact_id),
  UNIQUE KEY(contact3_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


CREATE TABLE IF NOT EXISTS `countries` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(2) NOT NULL,
  `call_code` varchar(4) DEFAULT NULL,
  `name` varchar(127) NOT NULL,
  `date_created` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `date_modified` timestamp NULL DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL,
  `publish` tinyint(1) NOT NULL DEFAULT '1',
  `deleted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (1, 'AD', '376', 'Andorra', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (2, 'AE', '971', 'United Arab Emirates', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (3, 'AF', '93', 'Afghanistan', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (4, 'AG', '1268', 'Antigua and Barbuda', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (5, 'AI', '1264', 'Anguilla', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (6, 'AL', '355', 'Albania', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (7, 'AM', '374', 'Armenia', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (8, 'AN', '599', 'Netherlands Antilles', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (9, 'AO', '244', 'Angola', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (10, 'AQ', '672', 'Antarctica', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (11, 'AR', '54', 'Argentina', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (12, 'AS', '1684', 'American Samoa', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (13, 'AT', '43', 'Austria', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (14, 'AU', '61', 'Australia', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (15, 'AW', '297', 'Aruba', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (16, 'AZ', '994', 'Azerbaijan', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (17, 'BA', '387', 'Bosnia and Herzegovina', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (18, 'BB', '1246', 'Barbados', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (19, 'BD', '880', 'Bangladesh', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (20, 'BE', '32', 'Belgium', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (21, 'BF', '226', 'Burkina Faso', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (22, 'BG', '359', 'Bulgaria', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (23, 'BH', '973', 'Bahrain', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (24, 'BI', '257', 'Burundi', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (25, 'BJ', '229', 'Benin', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (26, 'BL', '590', 'Saint Barthelemy', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (27, 'BM', '1441', 'Bermuda', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (28, 'BN', '673', 'Brunei Darussalam', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (29, 'BO', '591', 'Bolivia', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (30, 'BR', '55', 'Brazil', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (31, 'BS', '1242', 'Bahamas', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (32, 'BT', '975', 'Bhutan', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (33, 'BV', NULL, 'Bouvet Island', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (34, 'BW', '267', 'Botswana', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (35, 'BY', '375', 'Belarus', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (36, 'BZ', '501', 'Belize', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (37, 'CA', '1', 'Canada', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (38, 'CC', '61', 'Cocos (Keeling) Islands', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (39, 'CD', '243', 'Congo, The Democratic Republic of the', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (40, 'CF', '236', 'Central African Republic', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (41, 'CG', '242', 'Congo', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (42, 'CH', '41', 'Switzerland', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (43, 'CI', '225', 'Cote D Ivoire', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (44, 'CK', '682', 'Cook Islands', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (45, 'CL', '56', 'Chile', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (46, 'CM', '237', 'Cameroon', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (47, 'CN', '86', 'China', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (48, 'CO', '57', 'Colombia', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (49, 'CR', '506', 'Costa Rica', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (50, 'CU', '53', 'Cuba', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (51, 'CV', '238', 'Cape Verde', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (52, 'CX', '61', 'Christmas Island', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (53, 'CY', '357', 'Cyprus', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (54, 'CZ', '420', 'Czech Republic', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (55, 'DE', '49', 'Germany', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (56, 'DJ', '253', 'Djibouti', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (57, 'DK', '45', 'Denmark', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (58, 'DM', '1767', 'Dominica', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (59, 'DO', '1809', 'Dominican Republic', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (60, 'DZ', '213', 'Algeria', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (61, 'EC', '593', 'Ecuador', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (62, 'EE', '372', 'Estonia', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (63, 'EG', '20', 'Egypt', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (64, 'EH', '212', 'Western Sahara', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (65, 'ER', '291', 'Eritrea', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (66, 'ES', '34', 'Spain', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (67, 'ET', '251', 'Ethiopia', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (68, 'FI', '358', 'Finland', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (69, 'FJ', '679', 'Fiji', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (70, 'FK', '500', 'Falkland Islands (Malvinas)', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (71, 'FM', '691', 'Micronesia, Federated States of', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (72, 'FO', '298', 'Faroe Islands', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (73, 'FR', '33', 'France', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (74, 'GA', '241', 'Gabon', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (75, 'GB', '44', 'United Kingdom', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (76, 'GD', '1473', 'Grenada', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (77, 'GE', '995', 'Georgia', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (78, 'GF', '594', 'French Guiana', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (79, 'GH', '233', 'Ghana', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (80, 'GI', '350', 'Gibraltar', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (81, 'GL', '299', 'Greenland', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (82, 'GM', '220', 'Gambia', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (83, 'GN', '224', 'Guinea', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (84, 'GP', '590', 'Guadeloupe', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (85, 'GQ', '240', 'Equatorial Guinea', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (86, 'GR', '30', 'Greece', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (87, 'GS', '500', 'South Georgia South Sandwich Islands', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (88, 'GT', '502', 'Guatemala', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (89, 'GU', '1671', 'Guam', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (90, 'GW', '245', 'Guinea-bissau', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (91, 'GY', '592', 'Guyana', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (92, 'HK', '852', 'Hong Kong', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (93, 'HM', NULL, 'Heard and McDonald Islands', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (94, 'HN', '504', 'Honduras', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (95, 'HR', '385', 'Croatia', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (96, 'HT', '509', 'Haiti', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (97, 'HU', '36', 'Hungary', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (98, 'ID', '62', 'Indonesia', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (99, 'IE', '353', 'Ireland', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (100, 'IL', '972', 'Israel', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (101, 'IM', '44', 'Isle of Man', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (102, 'IN', '91', 'India', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (103, 'IO', '246', 'British Indian Ocean Territory', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (104, 'IQ', '964', 'Iraq', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (105, 'IR', '98', 'Iran, Islamic Republic of', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (106, 'IS', '354', 'Iceland', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (107, 'IT', '39', 'Italy', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (108, 'JM', '1876', 'Jamaica', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (109, 'JO', '962', 'Jordan', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (110, 'JP', '81', 'Japan', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (111, 'KE', '254', 'Kenya', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (112, 'KG', '996', 'Kyrgyzstan', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (113, 'KH', '855', 'Cambodia', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (114, 'KI', '686', 'Kiribati', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (115, 'KM', '269', 'Comoros', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (116, 'KN', '1869', 'Saint Kitts and Nevis', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (117, 'KP', '850', 'Korea Democratic People\'s Republic of', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (118, 'KR', '82', 'Korea Republic of', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (119, 'KW', '965', 'Kuwait', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (120, 'KY', '1345', 'Cayman Islands', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (121, 'KZ', '7', 'Kazakstan', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (122, 'LA', '856', 'Lao People\'s Democratic Republic', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (123, 'LB', '961', 'Lebanon', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (124, 'LC', '1758', 'Saint Lucia', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (125, 'LI', '423', 'Liechtenstein', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (126, 'LK', '94', 'Sri Lanka', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (127, 'LR', '231', 'Liberia', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (128, 'LS', '266', 'Lesotho', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (129, 'LT', '370', 'Lithuania', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (130, 'LU', '352', 'Luxembourg', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (131, 'LV', '371', 'Latvia', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (132, 'LY', '218', 'Libyan Arab Jamahiriya', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (133, 'MA', '212', 'Morocco', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (134, 'MC', '377', 'Monaco', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (135, 'MD', '373', 'Moldova, Republic of', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (136, 'ME', '382', 'Montenegro', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (137, 'MF', '1599', 'Saint Martin', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (138, 'MG', '261', 'Madagascar', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (139, 'MH', '692', 'Marshall Islands', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (140, 'MK', '389', 'Macedonia, The Former Yugoslav Republic of', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (141, 'ML', '223', 'Mali', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (142, 'MM', '95', 'Myanmar', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (143, 'MN', '976', 'Mongolia', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (144, 'MO', '853', 'Macau', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (145, 'MP', '1670', 'Northern Mariana Islands', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (146, 'MQ', '596', 'Martinique', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (147, 'MR', '222', 'Mauritania', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (148, 'MS', '1664', 'Montserrat', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (149, 'MT', '356', 'Malta', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (150, 'MU', '230', 'Mauritius', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (151, 'MV', '960', 'Maldives', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (152, 'MW', '265', 'Malawi', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (153, 'MX', '52', 'Mexico', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (154, 'MY', '60', 'Malaysia', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (155, 'MZ', '258', 'Mozambique', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (156, 'NA', '264', 'Namibia', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (157, 'NC', '687', 'New Caledonia', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (158, 'NE', '227', 'Niger', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (159, 'NF', '672', 'Norfork Island', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (160, 'NG', '234', 'Nigeria', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (161, 'NI', '505', 'Nicaragua', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (162, 'NL', '31', 'Netherlands', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (163, 'NO', '47', 'Norway', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (164, 'NP', '977', 'Nepal', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (165, 'NR', '674', 'Nauru', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (166, 'NU', '683', 'Niue', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (167, 'NZ', '64', 'New Zealand', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (168, 'OM', '968', 'Oman', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (169, 'PA', '507', 'Panama', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (170, 'PE', '51', 'Peru', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (171, 'PF', '689', 'French Polynesia', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (172, 'PG', '675', 'Papua New Guinea', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (173, 'PH', '63', 'Philippines', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (174, 'PK', '92', 'Pakistan', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (175, 'PL', '48', 'Poland', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (176, 'PM', '508', 'Saint Pierre and Miquelon', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (177, 'PN', '870', 'Pitcairn', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (178, 'PR', '1', 'Puerto Rico', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (179, 'PT', '351', 'Portugal', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (180, 'PW', '680', 'Palau', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (181, 'PY', '595', 'Paraguay', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (182, 'QA', '974', 'Qatar', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (183, 'RE', '262', 'RÃ©union', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (184, 'RO', '40', 'Romania', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (185, 'RS', '381', 'Serbia', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (186, 'RU', '7', 'Russian Federation', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (187, 'RW', '250', 'Rwanda', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (188, 'SA', '966', 'Saudi Arabia', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (189, 'SB', '677', 'Solomon Islands', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (190, 'SC', '248', 'Seychelles', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (191, 'SD', '249', 'Sudan', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (192, 'SE', '46', 'Sweden', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (193, 'SG', '65', 'Singapore', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (194, 'SH', '290', 'Saint Helena', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (195, 'SI', '386', 'Slovenia', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (196, 'SJ', '47', 'Svalbard and Jan Mayen', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (197, 'SK', '421', 'Slovakia', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (198, 'SL', '232', 'Sierra Leone', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (199, 'SM', '378', 'San Marino', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (200, 'SN', '221', 'Senegal', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (201, 'SO', '252', 'Somalia', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (202, 'SR', '597', 'Suriname', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (203, 'ST', '239', 'Sao Tome and Principe', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (204, 'SV', '503', 'El Salvador', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (205, 'SY', '963', 'Syrian Arab Republic', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (206, 'SZ', '268', 'Swaziland', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (207, 'TC', '1649', 'Turks and Caicos Islands', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (208, 'TD', '235', 'Chad', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (209, 'TF', '689', 'French Southern Territories', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (210, 'TG', '228', 'Togo', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (211, 'TH', '66', 'Thailand', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (212, 'TJ', '992', 'Tajikistan', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (213, 'TK', '690', 'Tokelau', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (214, 'TL', '670', 'Timor-leste', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (215, 'TM', '993', 'Turkmenistan', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (216, 'TN', '216', 'Tunisia', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (217, 'TO', '676', 'Tonga', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (218, 'TP', '670', 'East Timor', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (219, 'TR', '90', 'Turkey', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (220, 'TT', '1868', 'Trinidad and Tobago', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (221, 'TV', '688', 'Tuvalu', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (222, 'TW', '886', 'Taiwan, Province of China', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (223, 'TY', '262', 'Mayotte', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (224, 'TZ', '255', 'Tanzania, United Republic of', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (225, 'UA', '380', 'Ukraine', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (226, 'UG', '256', 'Uganda', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (227, 'UM', '1', 'United States Minor Outlying Islands', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (228, 'US', '1', 'United States', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (229, 'UY', '598', 'Uruguay', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (230, 'UZ', '998', 'Uzbekistan', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (231, 'VA', '39', 'Holy See (Vatican City State)', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (232, 'VC', '1784', 'Saint Vincent and The Grenadines', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (233, 'VE', '58', 'Venezuela', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (234, 'VG', '1284', 'Virgin Islands, British', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (235, 'VI', '1340', 'Virgin Islands, U.S.', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (236, 'VN', '84', 'Vietnam', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (237, 'VU', '678', 'Vanuatu', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (238, 'WF', '681', 'Wallis and Futuna', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (239, 'WS', '685', 'Samoa', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (240, 'YE', '967', 'Yemen', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (241, 'ZA', '27', 'South Africa', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (242, 'ZM', '260', 'Zambia', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
INSERT IGNORE INTO `countries` (`id`, `code`, `call_code`, `name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES (243, 'ZW', '263', 'Zimbabwe', '2014-8-7 19:38:39', NULL, NULL, NULL, 1, 0);
