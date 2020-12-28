/*
ts:2017-02-10 12:00:00
*/

INSERT IGNORE INTO `engine_resources` (`type_id`, `alias`, `name`, `description`) VALUES (0, 'timesheets_list', 'Timesheets Listing', 'Timesheets Listing');

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`) VALUES
(
  (SELECT `id` FROM `engine_project_role` WHERE `role` = 'Administrator'),
  (SELECT `id` FROM `engine_resources` WHERE `alias` = 'timesheets_list')
);

CREATE TABLE IF NOT EXISTS `plugin_timesheets_timesheet` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `contact_id` int(11) NOT NULL,
  `week` int(11) NOT NULL,
  `year` int(4) NOT NULL,
  `status` char(20) NOT NULL COMMENT 'pending, outstanding, late, submitted, approved',
  `date_created` datetime NOT NULL,
  `date_modified` datetime NOT NULL,
  `created_by` int(11) NOT NULL,
  `modified_by` int(11) NOT NULL,
  `deleted` tinyint(1) NOT NULL,
  `monday_start` int(4) NOT NULL,
  `monday_finish` int(4) NOT NULL,
  `monday_break` int(4) NOT NULL,
  `monday_holiday` int(4) NOT NULL,
  `tuesday_start` int(4) NOT NULL,
  `tuesday_finish` int(4) NOT NULL,
  `tuesday_break` int(4) NOT NULL,
  `tuesday_holiday` int(4) NOT NULL,
  `wednesday_start` int(4) NOT NULL,
  `wednesday_finish` int(4) NOT NULL,
  `wednesday_break` int(4) NOT NULL,
  `wednesday_holiday` int(4) NOT NULL,
  `thursday_start` int(4) NOT NULL,
  `thursday_finish` int(4) NOT NULL,
  `thursday_break` int(4) NOT NULL,
  `thursday_holiday` int(4) NOT NULL,
  `friday_start` int(4) NOT NULL,
  `friday_finish` int(4) NOT NULL,
  `friday_break` int(4) NOT NULL,
  `friday_holiday` int(4) NOT NULL,
  `saturday_start` int(4) NOT NULL,
  `saturday_finish` int(4) NOT NULL,
  `saturday_break` int(4) NOT NULL,
  `saturday_holiday` int(4) NOT NULL,
  `sunday_start` int(4) NOT NULL,
  `sunday_finish` int(4) NOT NULL,
  `sunday_break` int(4) NOT NULL,
  `sunday_holiday` int(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8;