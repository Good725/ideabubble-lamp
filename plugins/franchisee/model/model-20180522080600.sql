/*
ts:2018-05-22 08:06:00
*/

INSERT INTO `engine_plugins` (`name`, `friendly_name`, `show_on_dashboard`, `requires_media`) VALUES ('franchisee', 'Franchisee', '1', '0');

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'courses_booking_edit', 'Courses / Booking Edit', 'Courses / Booking Edit', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'courses'));

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'courses_course_edit', 'Courses / Course Edit', 'Courses / Course Edit', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'courses'));

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'courses_schedule_edit', 'Courses / Schedule Edit', 'Courses / Schedule Edit', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'courses'));

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'courses_schedule_edit_limited', 'Courses / Schedule Edit Limited', 'Courses / Schedule Edit Limited', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'courses'));

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'courses_timetable_edit', 'Courses / Timetable Edit', 'Courses / Timetable Edit', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'courses'));

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'courses_category_edit', 'Courses / Category Edit', 'Courses / Category Edit', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'courses'));

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'courses_subject_edit', 'Courses / Subject Edit', 'Courses / Subject Edit', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'courses'));

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'courses_location_edit', 'Courses / Location Edit', 'Courses / Location Edit', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'courses'));

  INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'courses_location_edit_limited', 'Courses / Location Edit Limited', 'Courses / Location Edit Limited', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'courses'));

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'courses_provider_edit', 'Courses / Provider Edit', 'Courses / Provider Edit', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'courses'));

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'courses_studymode_edit', 'Courses / Study Mode Edit', 'Courses / Study Mode Edit', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'courses'));

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'courses_type_edit', 'Courses / Type Edit', 'Courses / Type Edit', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'courses'));

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'courses_level_edit', 'Courses / Level Edit', 'Courses / Level Edit', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'courses'));

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'courses_year_edit', 'Courses / Year Edit', 'Courses / Year Edit', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'courses'));

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'courses_academicyear_edit', 'Courses / Academic Year Edit', 'Courses / Academic Year Edit', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'courses'));

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'courses_registration_edit', 'Courses / Registration Edit', 'Courses / Registration Edit', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'courses'));

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'courses_topic_edit', 'Courses / Topic Edit', 'Courses / Topic Edit', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'courses'));

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'courses_zone_edit', 'Courses / Zone Edit', 'Courses / Zone Edit', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'courses'));

INSERT INTO engine_role_permissions
  (role_id, resource_id)
  (SELECT
      r.id, e.id
    FROM `engine_project_role` r JOIN engine_resources e
    WHERE
      r.role in ('Administrator', 'Super User') AND
      e.alias in (
                  'courses_course_edit',
                  'courses_schedule_edit',
                  'courses_timetable_edit',
                  'courses_category_edit',
                  'courses_subject_edit',
                  'courses_location_edit',
                  'courses_provider_edit',
                  'courses_studymode_edit',
                  'courses_type_edit',
                  'courses_level_edit',
                  'courses_year_edit',
                  'courses_academicyear_edit',
                  'courses_registration_edit',
                  'courses_topic_edit',
                  'courses_zone_edit'
                  )
  );

  INSERT INTO engine_role_permissions
  (role_id, resource_id)
  (SELECT
      r.id, e.id
    FROM `engine_project_role` r JOIN engine_resources e
    WHERE
      r.role in ('Administrator', 'Super User') AND
      e.alias in (
                  'courses_booking_edit'
                  )
  );

INSERT INTO engine_project_role (`role`, `description`) VALUES ('Franchisee', 'Franchisee');

INSERT INTO engine_role_permissions
  (role_id, resource_id)
  (SELECT r.id, e.id FROM `engine_project_role` r JOIN engine_resources e WHERE r.role = 'Franchisee' AND e.alias = 'courses');

INSERT INTO engine_role_permissions
  (role_id, resource_id)
  (SELECT r.id, e.id FROM `engine_project_role` r JOIN engine_resources e WHERE r.role = 'Franchisee' AND e.alias = 'courses_schedule_edit_limited');

INSERT INTO engine_role_permissions
  (role_id, resource_id)
  (SELECT r.id, e.id FROM `engine_project_role` r JOIN engine_resources e WHERE r.role = 'Franchisee' AND e.alias = 'courses_location_edit_limited');

ALTER TABLE plugin_courses_schedules ADD COLUMN owned_by INT;

INSERT INTO plugin_courses_providers_types (`type`) VALUES ('Franchisee');
ALTER TABLE plugin_courses_providers ADD COLUMN franchisee_id INT;

CREATE TABLE plugin_courses_courses_has_providers
(
  course_id INT NOT NULL,
  provider_id INT NOT NULL,

  KEY (course_id),
  KEY (provider_id)
)
ENGINE=INNODB
CHARSET=UTF8;

INSERT INTO plugin_courses_courses_has_providers
  (course_id, provider_id)
  (select id, provider_id from plugin_courses_courses where provider_id is not NULL);

ALTER TABLE `plugin_courses_courses` DROP FOREIGN KEY `fk_plugin_courses_courses_plugin_courses_providers`;

ALTER TABLE plugin_courses_courses DROP COLUMN provider_id;

ALTER TABLE plugin_courses_courses ADD COLUMN schedule_is_fee_required TINYINT DEFAULT 0;
ALTER TABLE plugin_courses_courses ADD COLUMN schedule_fee_amount DECIMAL(10, 2);
ALTER TABLE plugin_courses_courses ADD COLUMN schedule_fee_per ENUM('Timeslot', 'Day', 'Schedule');
ALTER TABLE plugin_courses_courses ADD COLUMN schedule_allow_price_override TINYINT DEFAULT 0;

ALTER TABLE plugin_courses_locations ADD COLUMN owned_by INT;

INSERT INTO `plugin_dashboards` (`title`, `description`) VALUES ('Franchisee', 'Franchisee');
INSERT INTO plugin_dashboards_sharing
  (dashboard_id, group_id)
  (select dashboards.id, roles.id from plugin_dashboards dashboards, engine_project_role roles where dashboards.title = 'Franchisee' and roles.role in ('Administrator', 'Franchisee'));

UPDATE engine_project_role SET default_dashboard_id = (select dashboards.id from plugin_dashboards dashboards where dashboards.title = 'Franchisee' limit 1) WHERE role='Franchisee';

INSERT INTO `plugin_reports_reports` (`name`, `sql`) VALUES ('Franchisee Schedules Counts', 'SELECT \r\n	CONCAT( \r\n		\'<div class=\"text-center\"><h3>Total Schedules</h3><span style=\"font-size: 2em;\">\', \r\n		IFNULL(`count`, 0), \r\n		\'</span></div>\' \r\n	) AS `cnt` \r\nFROM ( \r\n	select \r\n		count(*) as count\r\n	from plugin_courses_schedules schedules\r\n		inner join engine_users users on schedules.owned_by = users.id\r\n	where users.id = @user_id\r\n) AS `counter`');
INSERT INTO `plugin_reports_sparklines` (`title`, `report_id`, `chart_type_id`, `x_axis`, `total_field`, `text_color`, `background_color`) VALUES ('Total Schedules', (select id from plugin_reports_reports where name='Franchisee Schedules Counts' order by id desc limit 1), '6', 'cnt', 'cnt', 'rgb(255, 255, 255)', 'rgb(56, 231, 202)');
INSERT INTO `plugin_dashboards_gadgets` (`dashboard_id`, `gadget_id`, `type_id`, `column`, `order`) VALUES ((select dashboards.id from plugin_dashboards dashboards where dashboards.title = 'Franchisee'), (select id from plugin_reports_reports where name='Franchisee Schedules Counts' order by id desc limit 1), '2', '1', '1');

INSERT INTO `plugin_reports_reports` (`name`, `sql`) VALUES ('Franchisee Bookings Counts', 'SELECT \r\n	CONCAT( \r\n		\'<div class=\"text-center\"><h3>Total Bookings</h3><span style=\"font-size: 2em;\">\', \r\n		IFNULL(`count`, 0), \r\n		\'</span></div>\' \r\n	) AS `cnt` \r\nFROM ( \r\n	select \r\n		count(*) as count\r\n	from plugin_courses_bookings bookings \r\n	inner join plugin_courses_bookings_has_schedules has_schedules on bookings.id = has_schedules.booking_id \r\n	inner join plugin_courses_schedules schedules on has_schedules.schedule_id = schedules.id \r\n	where bookings.deleted = 0 and has_schedules.deleted = 0 and schedules.owned_by=@user_id\r\n) AS `counter`\r\n');
INSERT INTO `plugin_reports_sparklines` (`title`, `report_id`, `chart_type_id`, `x_axis`, `total_field`, `text_color`, `background_color`) VALUES ('Total Bookings', (select id from plugin_reports_reports where name='Franchisee Bookings Counts' order by id desc limit 1), '6', 'cnt', 'cnt', 'rgb(255, 255, 255)', 'rgb(56, 231, 202)');
INSERT INTO `plugin_dashboards_gadgets` (`dashboard_id`, `gadget_id`, `type_id`, `column`, `order`) VALUES ((select dashboards.id from plugin_dashboards dashboards where dashboards.title = 'Franchisee'), (select id from plugin_reports_reports where name='Franchisee Bookings Counts' order by id desc limit 1), '2', '2', '1');

INSERT INTO `plugin_reports_reports` (`name`, `sql`) VALUES ('Franchisee Bookings Revenue', 'SELECT \r\n	CONCAT( \r\n		\'<div class=\"text-center\"><h3>Total Revenue</h3><span style=\"font-size: 2em;\">\', \r\n		IFNULL(`sum`, 0), \r\n		\'</span></div>\' \r\n	) AS `cnt` \r\nFROM ( \r\n	select \r\n		sum(has_schedules.total) as sum\r\n	from plugin_courses_bookings bookings \r\n	inner join plugin_courses_bookings_has_schedules has_schedules on bookings.id = has_schedules.booking_id \r\n	inner join plugin_courses_schedules schedules on has_schedules.schedule_id = schedules.id \r\n	where bookings.deleted = 0 and has_schedules.deleted = 0 and schedules.owned_by=@user_id\r\n) AS `counter`\r\n');
INSERT INTO `plugin_reports_sparklines` (`title`, `report_id`, `chart_type_id`, `x_axis`, `total_field`, `text_color`, `background_color`) VALUES ('Total Revenue', (select id from plugin_reports_reports where name='Franchisee Bookings Revenue' order by id desc limit 1), '6', 'cnt', 'cnt', 'rgb(255, 255, 255)', 'rgb(56, 231, 202)');
INSERT INTO `plugin_dashboards_gadgets` (`dashboard_id`, `gadget_id`, `type_id`, `column`, `order`) VALUES ((select dashboards.id from plugin_dashboards dashboards where dashboards.title = 'Franchisee'), (select id from plugin_reports_reports where name='Franchisee Bookings Revenue' order by id desc limit 1), '2', '3', '1');
