/*
ts:2019-09-06 11:30:00
*/

DELIMITER ;;

-- Total active students report 1
INSERT INTO `plugin_reports_reports` (`name`, `sql`, `publish`, `delete`, `action_button_label`, `action_button`,
                                      `action_event`, `date_created`, `dashboard`)
SELECT 'Total active students',
       '',
       '1',
       '0',
       '',
       '0',
       '',
       CURRENT_TIMESTAMP,
       '1'
FROM `plugin_reports_reports`
WHERE NOT EXISTS(SELECT * FROM `plugin_reports_reports` WHERE `name` = 'Total active students')
LIMIT 1 ;;

-- Update report to use the latest version of the SQL.

UPDATE
    `plugin_reports_reports`
SET `sql`           = 'select count(contacts.id) as ''Total''
             from plugin_contacts3_contact_has_roles `chr`
             inner join plugin_contacts3_contacts `contacts` on (chr.contact_id = contacts.id)
                      inner join plugin_contacts3_roles `c3r` on (`chr`.role_id = `c3r`.id)
             where  (`c3r`.name = ''Child''  or `c3r`.name = ''Mature'') and contacts.is_inactive = 0 and contacts.delete = 0;',
    `date_modified` = CURRENT_TIMESTAMP
WHERE `name` = 'Total active students'
  AND `delete` = 0
;;

INSERT IGNORE INTO `plugin_reports_sparklines` (`title`, `report_id`, `chart_type_id`, `total_type_id`, `total_field`,
                                                `text_color`, `background_color`,
                                                `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`,
                                                `deleted`)
VALUES ('Total active students',
        (SELECT `id`
         FROM `plugin_reports_reports`
         WHERE `name` = 'Total active students'
           AND `delete` = 0
         ORDER BY `id` DESC
         LIMIT 1),
        (SELECT `id` FROM `plugin_reports_chart_types` WHERE `stub` = 'total' AND `deleted` = 0 LIMIT 1),
        '0',
        'Total',
        ' ',
        ' ',
        (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
        (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
        CURRENT_TIMESTAMP,
        CURRENT_TIMESTAMP,
        '1',
        '0');;

-- Summer Active students report 2
INSERT INTO `plugin_reports_widgets` (`name`, `type`, `x_axis`, `y_axis`, `date_created`, `date_modified`,
                                      `publish`, `delete`, `created_by`, `modified_by`)
VALUES ('Summer - Active students', (select id from plugin_reports_chart_types where name = 'Pie' limit 1), 'title',
        'total', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '1', '0',
        (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
        (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1));;

INSERT INTO `plugin_reports_charts` (`title`, `type`, `x_axis`, `y_axis`, `date_created`,
                                     `date_modified`, `publish`, `delete`)
VALUES ('Summer - Active students', (select id from plugin_reports_chart_types where name = 'Pie' limit 1), 'title',
        'total', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '1', '0');;


INSERT INTO `plugin_reports_reports` (`name`, `sql`, `publish`, `delete`, `action_button_label`, `action_button`,
                                      `action_event`, `date_created`, `dashboard`, widget_id)
SELECT 'Summer - Active students',
       '',
       '1',
       '0',
       '',
       '0',
       '',
       CURRENT_TIMESTAMP,
       '1',
       (select id from plugin_reports_widgets where name = 'Summer - Active students' limit 1)
FROM `plugin_reports_reports`
WHERE NOT EXISTS(SELECT * FROM `plugin_reports_reports` WHERE `name` = 'Summer - Active students')
LIMIT 1
;;

-- Update report to use the latest version of the SQL.

UPDATE
    `plugin_reports_reports`
SET `sql`           = 'SELECT courses.title, count(*) as `total`
from plugin_ib_educate_bookings `bookings`
         inner join plugin_ib_educate_bookings_has_courses `booking_courses`
                    on bookings.booking_id = booking_courses.booking_id
         inner join plugin_courses_courses `courses` on booking_courses.course_id = courses.id
         INNER JOIN  plugin_courses_categories categories on courses.category_id = categories.id
        inner join plugin_contacts3_contacts `contacts` on bookings.contact_id = contacts.id
where categories.category = ''Junior Summer School'' and contacts.is_inactive = 0
group by courses.id;',
    `date_modified` = CURRENT_TIMESTAMP
WHERE `name` = 'Summer - Active students'
  AND `delete` = 0
;;

-- Work experience report 3
INSERT INTO `plugin_reports_widgets` (`name`, `type`, `x_axis`, `y_axis`, `date_created`, `date_modified`,
                                      `publish`, `delete`, `created_by`, `modified_by`)
VALUES ('Work experience - Active students', (select id from plugin_reports_chart_types where name = 'Pie' limit 1),
        'title',
        'total', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '1', '0',
        (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
        (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1));;

INSERT INTO `plugin_reports_charts` (`title`, `type`, `x_axis`, `y_axis`, `date_created`,
                                     `date_modified`, `publish`, `delete`)
VALUES ('Work experience - Active students', (select id from plugin_reports_chart_types where name = 'Pie' limit 1),
        'title',
        'total', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '1', '0');;


INSERT INTO `plugin_reports_reports` (`name`, `sql`, `publish`, `delete`, `action_button_label`, `action_button`,
                                      `action_event`, `date_created`, `dashboard`, widget_id)
SELECT 'Work experience - Active students',
       '',
       '1',
       '0',
       '',
       '0',
       '',
       CURRENT_TIMESTAMP,
       '1',
       (select id from plugin_reports_widgets where name = 'Work experience - Active students' limit 1)
FROM `plugin_reports_reports`
WHERE NOT EXISTS(SELECT * FROM `plugin_reports_reports` WHERE `name` = 'Work experience - Active students')
LIMIT 1
;;

UPDATE
    `plugin_reports_reports`
SET `sql`           = 'SELECT courses.title, count(*) as `total`
from plugin_ib_educate_bookings `bookings`
         inner join plugin_ib_educate_bookings_has_courses `booking_courses`
                    on bookings.booking_id = booking_courses.booking_id
         inner join plugin_courses_courses `courses` on booking_courses.course_id = courses.id
         inner join plugin_courses_categories categories ON courses.category_id = categories.id
        inner join plugin_contacts3_contacts `contacts` on bookings.contact_id = contacts.id
where categories.category = ''Work experience Programme'' and contacts.is_inactive = 0
group by courses.id;',
    `date_modified` = CURRENT_TIMESTAMP
WHERE `name` = 'Work experience - Active students'
  AND `delete` = 0
;;

-- High School report 4
INSERT INTO `plugin_reports_widgets` (`name`, `type`, `x_axis`, `y_axis`, `date_created`, `date_modified`,
                                      `publish`, `delete`, `created_by`, `modified_by`)
VALUES ('High school - Active students', (select id from plugin_reports_chart_types where name = 'Pie' limit 1),
        'title',
        'total', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '1', '0',
        (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
        (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1));;

INSERT INTO `plugin_reports_charts` (`title`, `type`, `x_axis`, `y_axis`, `date_created`,
                                     `date_modified`, `publish`, `delete`)
VALUES ('High school - Active students', (select id from plugin_reports_chart_types where name = 'Pie' limit 1),
        'title',
        'total', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '1', '0');;


INSERT INTO `plugin_reports_reports` (`name`, `sql`, `publish`, `delete`, `action_button_label`, `action_button`,
                                      `action_event`, `date_created`, `dashboard`, widget_id)
SELECT 'High school - Active students',
       '',
       '1',
       '0',
       '',
       '0',
       '',
       CURRENT_TIMESTAMP,
       '1',
       (select id from plugin_reports_widgets where name = 'High school - Active students' limit 1)
FROM `plugin_reports_reports`
WHERE NOT EXISTS(SELECT * FROM `plugin_reports_reports` WHERE `name` = 'High school - Active students')
LIMIT 1
;;

UPDATE
    `plugin_reports_reports`
SET `sql`           = 'SELECT courses.title, count(*) as `total`
from plugin_ib_educate_bookings `bookings`
         inner join plugin_ib_educate_bookings_has_courses `booking_courses`
                    on bookings.booking_id = booking_courses.booking_id
         inner join plugin_courses_courses `courses` on booking_courses.course_id = courses.id
         inner join plugin_courses_categories categories ON courses.category_id = categories.id
        inner join plugin_contacts3_contacts `contacts` on bookings.contact_id = contacts.id
where categories.category = ''High School Programmes'' and contacts.is_inactive = 0
group by courses.id;',
    `date_modified` = CURRENT_TIMESTAMP
WHERE `name` = 'High school - Active students'
  AND `delete` = 0
;;

-- Personalised programmes report 4.5
INSERT INTO `plugin_reports_widgets` (`name`, `type`, `x_axis`, `y_axis`, `date_created`, `date_modified`,
                                      `publish`, `delete`, `created_by`, `modified_by`)
VALUES ('Personalised - Active students', (select id from plugin_reports_chart_types where name = 'Pie' limit 1),
        'title',
        'total', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '1', '0',
        (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
        (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1));;

INSERT INTO `plugin_reports_charts` (`title`, `type`, `x_axis`, `y_axis`, `date_created`,
                                     `date_modified`, `publish`, `delete`)
VALUES ('Personalised - Active students', (select id from plugin_reports_chart_types where name = 'Pie' limit 1),
        'title',
        'total', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '1', '0');;


INSERT INTO `plugin_reports_reports` (`name`, `sql`, `publish`, `delete`, `action_button_label`, `action_button`,
                                      `action_event`, `date_created`, `dashboard`, widget_id)
SELECT 'Personalised - Active students',
       '',
       '1',
       '0',
       '',
       '0',
       '',
       CURRENT_TIMESTAMP,
       '1',
       (select id from plugin_reports_widgets where name = 'Personalised - Active students' limit 1)
FROM `plugin_reports_reports`
WHERE NOT EXISTS(SELECT * FROM `plugin_reports_reports` WHERE `name` = 'Personalised - Active students')
LIMIT 1
;;

UPDATE
    `plugin_reports_reports`
SET `sql`           = 'SELECT courses.title, count(*) as `total`
from plugin_ib_educate_bookings `bookings`
         inner join plugin_ib_educate_bookings_has_courses `booking_courses`
                    on bookings.booking_id = booking_courses.booking_id
         inner join plugin_courses_courses `courses` on booking_courses.course_id = courses.id
         inner join plugin_courses_categories categories ON courses.category_id = categories.id
        inner join plugin_contacts3_contacts `contacts` on bookings.contact_id = contacts.id
where categories.category = ''Personalised Programmes'' and contacts.is_inactive = 0
group by courses.id;',
    `date_modified` = CURRENT_TIMESTAMP
WHERE `name` = 'Personalised - Active students'
  AND `delete` = 0
;;

-- Total student enquiries report 5
INSERT INTO `plugin_reports_reports` (`name`, `sql`, `publish`, `delete`, `action_button_label`, `action_button`,
                                      `action_event`, `date_created`, `dashboard`)
SELECT 'Total student enquiries',
       '',
       '1',
       '0',
       '',
       '0',
       '',
       CURRENT_TIMESTAMP,
       '1'
FROM `plugin_reports_reports`
WHERE NOT EXISTS(SELECT * FROM `plugin_reports_reports` WHERE `name` = 'Total student enquiries')
LIMIT 1 ;;

UPDATE
    `plugin_reports_reports`
SET `sql`           = 'select count(booking.booking_id) as ''Total''
            from plugin_ib_educate_bookings `booking`
            inner join plugin_contacts3_contacts `contacts` on booking.contact_id = contacts.id
            inner join plugin_ib_educate_bookings_has_applications `booking_application` on booking.booking_id = booking_application.booking_id
            inner join plugin_ib_educate_bookings_status `application_status`
                    on booking_application.status_id = application_status.status_id
            where application_status.title = ''Enquiry'' and contacts.is_inactive = 0',
    `date_modified` = CURRENT_TIMESTAMP
WHERE `name` = 'Total student enquiries'
  AND `delete` = 0
;;

INSERT IGNORE INTO `plugin_reports_sparklines` (`title`, `report_id`, `chart_type_id`, `total_type_id`, `total_field`,
                                                `text_color`, `background_color`,
                                                `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`,
                                                `deleted`)
VALUES ('Total student enquiries',
        (SELECT `id`
         FROM `plugin_reports_reports`
         WHERE `name` = 'Total student enquiries'
           AND `delete` = 0
         ORDER BY `id` DESC
         LIMIT 1),
        (SELECT `id` FROM `plugin_reports_chart_types` WHERE `stub` = 'total' AND `deleted` = 0 LIMIT 1),
        '0',
        'Total',
        ' ',
        ' ',
        (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
        (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
        CURRENT_TIMESTAMP,
        CURRENT_TIMESTAMP,
        '1',
        '0');;

-- Total active students report 6
INSERT INTO `plugin_reports_reports` (`name`, `sql`, `publish`, `delete`, `action_button_label`, `action_button`,
                                      `action_event`, `date_created`, `dashboard`)
SELECT 'Total in progress bookings',
       '',
       '1',
       '0',
       '',
       '0',
       '',
       CURRENT_TIMESTAMP,
       '1'
FROM `plugin_reports_reports`
WHERE NOT EXISTS(SELECT * FROM `plugin_reports_reports` WHERE `name` = 'Total in progress bookings')
LIMIT 1 ;;

UPDATE
    `plugin_reports_reports`
SET `sql`           = "SELECT
                    count(booking.booking_id) as 'Total'
                FROM
                    plugin_ib_educate_bookings `booking`
                        LEFT JOIN
                    plugin_contacts3_contacts `contacts` ON booking.contact_id = contacts.id
                        LEFT JOIN
                    plugin_ib_educate_bookings_status `booking_status` ON booking.booking_status = booking_status.status_id
                        LEFT JOIN
                    plugin_ib_educate_bookings_has_applications `booking_application` ON booking.booking_id = booking_application.booking_id
                        LEFT JOIN
                    plugin_ib_educate_bookings_status `application_status` ON booking_application.status_id = application_status.status_id
                WHERE
                    booking_status.title = 'In Progress'
                        AND (application_status.title <> 'Enquiry'
                        OR application_status.title IS NULL)
                        AND contacts.is_inactive = 0;",
    `date_modified` = CURRENT_TIMESTAMP
WHERE `name` = 'Total in progress bookings'
  AND `delete` = 0
;;

INSERT IGNORE INTO `plugin_reports_sparklines` (`title`, `report_id`, `chart_type_id`, `total_type_id`, `total_field`,
                                                `text_color`, `background_color`,
                                                `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`,
                                                `deleted`)
VALUES ('Total in progress bookings',
        (SELECT `id`
         FROM `plugin_reports_reports`
         WHERE `name` = 'Total in progress bookings'
           AND `delete` = 0
         ORDER BY `id` DESC
         LIMIT 1),
        (SELECT `id` FROM `plugin_reports_chart_types` WHERE `stub` = 'total' AND `deleted` = 0 LIMIT 1),
        '0',
        'Total',
        ' ',
        ' ',
        (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
        (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
        CURRENT_TIMESTAMP,
        CURRENT_TIMESTAMP,
        '1',
        '0');;

-- Insert the dashboard
INSERT INTO plugin_dashboards
(title, description, columns, date_filter, date_created, date_modified, created_by, modified_by, publish, deleted)
VALUES ('Students', 'Students dashboard', 3, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,
        (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
        (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
        1, 0);;

-- Then insert the dashboard gadgets
INSERT INTO `plugin_dashboards_gadgets` (`dashboard_id`, `gadget_id`, `type_id`, `column`, `order`, `date_created`,
                                         `date_modified`, `publish`, `deleted`)
VALUES ((SELECT `id` FROM `plugin_dashboards` WHERE `title` = 'Students' ORDER BY `date_created` DESC LIMIT 1),
        (SELECT `id`
         FROM `plugin_reports_reports`
         WHERE `name` = 'Total active students'
         ORDER BY `date_created` DESC
         LIMIT 1),
        (SELECT `id` FROM `plugin_dashboards_gadget_types` WHERE `stub` = 'sparkline' LIMIT 1),
        '1',
        '1',
        CURRENT_TIMESTAMP,
        CURRENT_TIMESTAMP,
        '1',
        '0');;

INSERT INTO `plugin_dashboards_gadgets` (`dashboard_id`, `gadget_id`, `type_id`, `column`, `order`, `date_created`,
                                         `date_modified`, `publish`, `deleted`)
VALUES ((SELECT `id` FROM `plugin_dashboards` WHERE `title` = 'Students' ORDER BY `date_created` DESC LIMIT 1),
        (SELECT `id`
         FROM `plugin_reports_reports`
         WHERE `name` = 'High school - Active students'
         ORDER BY `date_created` DESC
         LIMIT 1),
        (SELECT `id` FROM `plugin_dashboards_gadget_types` WHERE `stub` = 'widget' LIMIT 1),
        '1',
        '2',
        CURRENT_TIMESTAMP,
        CURRENT_TIMESTAMP,
        '1',
        '0');;

INSERT INTO `plugin_dashboards_gadgets` (`dashboard_id`, `gadget_id`, `type_id`, `column`, `order`, `date_created`,
                                         `date_modified`, `publish`, `deleted`)
VALUES ((SELECT `id` FROM `plugin_dashboards` WHERE `title` = 'Students' ORDER BY `date_created` DESC LIMIT 1),
        (SELECT `id`
         FROM `plugin_reports_reports`
         WHERE `name` = 'Personalised - Active students'
         ORDER BY `date_created` DESC
         LIMIT 1),
        (SELECT `id` FROM `plugin_dashboards_gadget_types` WHERE `stub` = 'widget' LIMIT 1),
        '1',
        '3',
        CURRENT_TIMESTAMP,
        CURRENT_TIMESTAMP,
        '1',
        '0');;

INSERT INTO `plugin_dashboards_gadgets` (`dashboard_id`, `gadget_id`, `type_id`, `column`, `order`, `date_created`,
                                         `date_modified`, `publish`, `deleted`)
VALUES ((SELECT `id` FROM `plugin_dashboards` WHERE `title` = 'Students' ORDER BY `date_created` DESC LIMIT 1),
        (SELECT `id`
         FROM `plugin_reports_reports`
         WHERE `name` = 'Total in progress bookings'
         ORDER BY `date_created` DESC
         LIMIT 1),
        (SELECT `id` FROM `plugin_dashboards_gadget_types` WHERE `stub` = 'sparkline' LIMIT 1),
        '2',
        '7',
        CURRENT_TIMESTAMP,
        CURRENT_TIMESTAMP,
        '1',
        '0');;

INSERT INTO `plugin_dashboards_gadgets` (`dashboard_id`, `gadget_id`, `type_id`, `column`, `order`, `date_created`,
                                         `date_modified`, `publish`, `deleted`)
VALUES ((SELECT `id` FROM `plugin_dashboards` WHERE `title` = 'Students' ORDER BY `date_created` DESC LIMIT 1),
        (SELECT `id`
         FROM `plugin_reports_reports`
         WHERE `name` = 'Summer - Active students'
         ORDER BY `date_created` DESC
         LIMIT 1),
        (SELECT `id` FROM `plugin_dashboards_gadget_types` WHERE `stub` = 'widget' LIMIT 1),
        '2',
        '7',
        CURRENT_TIMESTAMP,
        CURRENT_TIMESTAMP,
        '1',
        '0');;

INSERT INTO `plugin_dashboards_gadgets` (`dashboard_id`, `gadget_id`, `type_id`, `column`, `order`, `date_created`,
                                         `date_modified`, `publish`, `deleted`)
VALUES ((SELECT `id` FROM `plugin_dashboards` WHERE `title` = 'Students' ORDER BY `date_created` DESC LIMIT 1),
        (SELECT `id`
         FROM `plugin_reports_reports`
         WHERE `name` = 'Total student enquiries'
         ORDER BY `date_created` DESC
         LIMIT 1),
        (SELECT `id` FROM `plugin_dashboards_gadget_types` WHERE `stub` = 'sparkline' LIMIT 1),
        '3',
        '13',
        CURRENT_TIMESTAMP,
        CURRENT_TIMESTAMP,
        '1',
        '0');;

INSERT INTO `plugin_dashboards_gadgets` (`dashboard_id`, `gadget_id`, `type_id`, `column`, `order`, `date_created`,
                                         `date_modified`, `publish`, `deleted`)
VALUES ((SELECT `id` FROM `plugin_dashboards` WHERE `title` = 'Students' ORDER BY `date_created` DESC LIMIT 1),
        (SELECT `id`
         FROM `plugin_reports_reports`
         WHERE `name` = 'Work experience - Active students'
         ORDER BY `date_created` DESC
         LIMIT 1),
        (SELECT `id` FROM `plugin_dashboards_gadget_types` WHERE `stub` = 'widget' LIMIT 1),
        '3',
        '14',
        CURRENT_TIMESTAMP,
        CURRENT_TIMESTAMP,
        '1',
        '0');;

-- Total active host families report 7
INSERT INTO `plugin_reports_reports` (`name`, `sql`, `publish`, `delete`, `action_button_label`, `action_button`,
                                      `action_event`, `date_created`, `dashboard`)
SELECT 'Total active host families',
       '',
       '1',
       '0',
       '',
       '0',
       '',
       CURRENT_TIMESTAMP,
       '1'
FROM `plugin_reports_reports`
WHERE NOT EXISTS(SELECT * FROM `plugin_reports_reports` WHERE `name` = 'Total active host families')
LIMIT 1 ;;

-- Update report to use the latest version of the SQL.

UPDATE
    `plugin_reports_reports`
SET `sql`           = 'SELECT COUNT(contacts.id) AS ''Total''
             from  plugin_contacts3_contacts `contacts`
             inner join plugin_contacts3_contact_type `contact_types` ON (contacts.type = contact_types.contact_type_id)
                      inner join plugin_contacts3_contacts_subtypes `contact_subtypes` ON (`contacts`.subtype_id = `contact_subtypes`.id)
             where  (`contact_subtypes`.subtype = ''Host Family''   OR `contact_types`.label = ''Host Family'') and contacts.is_inactive = 0 and contacts.delete = 0;',
    `date_modified` = CURRENT_TIMESTAMP
WHERE `name` = 'Total active host families'
  AND `delete` = 0
;;

INSERT IGNORE INTO `plugin_reports_sparklines` (`title`, `report_id`, `chart_type_id`, `total_type_id`, `total_field`,
                                                `text_color`, `background_color`,
                                                `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`,
                                                `deleted`)
VALUES ('Total active host families',
        (SELECT `id`
         FROM `plugin_reports_reports`
         WHERE `name` = 'Total active host families'
           AND `delete` = 0
         ORDER BY `id` DESC
         LIMIT 1),
        (SELECT `id` FROM `plugin_reports_chart_types` WHERE `stub` = 'total' AND `deleted` = 0 LIMIT 1),
        '0',
        'Total',
        ' ',
        ' ',
        (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
        (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
        CURRENT_TIMESTAMP,
        CURRENT_TIMESTAMP,
        '1',
        '0');;

-- Location Active host families students report 8
INSERT INTO `plugin_reports_widgets` (`name`, `type`, `x_axis`, `y_axis`, `date_created`, `date_modified`,
                                      `publish`, `delete`, `created_by`, `modified_by`)
VALUES ('Location - Active host families', (select id from plugin_reports_chart_types where name = 'Pie' limit 1), 'title',
        'total', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '1', '0',
        (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
        (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1));;

INSERT INTO `plugin_reports_charts` (`title`, `type`, `x_axis`, `y_axis`, `date_created`,
                                     `date_modified`, `publish`, `delete`)
VALUES ('Location - Active host families', (select id from plugin_reports_chart_types where name = 'Pie' limit 1), 'title',
        'total', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '1', '0');;


INSERT INTO `plugin_reports_reports` (`name`, `sql`, `publish`, `delete`, `action_button_label`, `action_button`,
                                      `action_event`, `date_created`, `dashboard`, widget_id)
SELECT 'Location - Active host families',
       '',
       '1',
       '0',
       '',
       '0',
       '',
       CURRENT_TIMESTAMP,
       '1',
       (select id from plugin_reports_widgets where name = 'Location - Active host families' limit 1)
FROM `plugin_reports_reports`
WHERE NOT EXISTS(SELECT * FROM `plugin_reports_reports` WHERE `name` = 'Location - Active host families')
LIMIT 1
;;

-- Update report to use the latest version of the SQL.

UPDATE
    `plugin_reports_reports`
SET `sql`           = 'SELECT
    counties.name as `title`, COUNT(counties.id) AS `total`
FROM
    plugin_contacts3_contacts `contacts`
        INNER JOIN
    plugin_contacts3_contact_type `contact_types` ON (contacts.type = contact_types.contact_type_id)
        INNER JOIN
    plugin_contacts3_contacts_subtypes `contact_subtypes` ON (`contacts`.subtype_id = `contact_subtypes`.id)
        INNER JOIN
    plugin_contacts3_residences `residence` ON contacts.residence = residence.address_id
        INNER JOIN
    engine_counties `counties` ON residence.county = counties.id
WHERE
    (`contact_subtypes`.subtype = ''Host Family''
        OR `contact_types`.label = ''Host Family'')
        AND contacts.is_inactive = 0
GROUP BY counties.id;',
    `date_modified` = CURRENT_TIMESTAMP
WHERE `name` = 'Location - Active host families'
  AND `delete` = 0
;;

-- Total host_families with students report 9
INSERT INTO `plugin_reports_reports` (`name`, `sql`, `publish`, `delete`, `action_button_label`, `action_button`,
                                      `action_event`, `date_created`, `dashboard`)
SELECT 'Total host families with students',
       '',
       '1',
       '0',
       '',
       '0',
       '',
       CURRENT_TIMESTAMP,
       '1'
FROM `plugin_reports_reports`
WHERE NOT EXISTS(SELECT * FROM `plugin_reports_reports` WHERE `name` = 'Total host families with students')
LIMIT 1 ;;

-- Update report to use the latest version of the SQL.

UPDATE
    `plugin_reports_reports`
SET `sql`           = 'SELECT
   count(distinct contacts.id) as `Total`
FROM
    plugin_contacts3_contacts `contacts`
        INNER JOIN
    plugin_contacts3_contact_type `contact_types` ON (contacts.type = contact_types.contact_type_id)
        INNER JOIN
    plugin_contacts3_contacts_subtypes `contact_subtypes` ON (`contacts`.subtype_id = `contact_subtypes`.id)
    inner join plugin_ib_educate_bookings_has_linked_contacts `linked_contacts` on contacts.id = linked_contacts.contact_id
WHERE
    (`contact_subtypes`.subtype = ''Host Family''
        OR `contact_types`.label = ''Host Family'')
        AND contacts.is_inactive = 0;',
    `date_modified` = CURRENT_TIMESTAMP
WHERE `name` = 'Total host families with students'
  AND `delete` = 0;;

INSERT IGNORE INTO `plugin_reports_sparklines` (`title`, `report_id`, `chart_type_id`, `total_type_id`, `total_field`,
                                                `text_color`, `background_color`,
                                                `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`,
                                                `deleted`)
VALUES ('Total host families with students',
        (SELECT `id`
         FROM `plugin_reports_reports`
         WHERE `name` = 'Total host families with students'
           AND `delete` = 0
         ORDER BY `id` DESC
         LIMIT 1),
        (SELECT `id` FROM `plugin_reports_chart_types` WHERE `stub` = 'total' AND `deleted` = 0 LIMIT 1),
        '0',
        'Total',
        ' ',
        ' ',
        (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
        (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
        CURRENT_TIMESTAMP,
        CURRENT_TIMESTAMP,
        '1',
        '0');;

-- Location host families with students students report 10
INSERT INTO `plugin_reports_widgets` (`name`, `type`, `x_axis`, `y_axis`, `date_created`, `date_modified`,
                                      `publish`, `delete`, `created_by`, `modified_by`)
VALUES ('Location - host families with students', (select id from plugin_reports_chart_types where name = 'Pie' limit 1),
        'title',
        'total', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '1', '0',
        (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
        (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1));;

INSERT INTO `plugin_reports_charts` (`title`, `type`, `x_axis`, `y_axis`, `date_created`,
                                     `date_modified`, `publish`, `delete`)
VALUES ('Location - host families with students', (select id from plugin_reports_chart_types where name = 'Pie' limit 1),
        'title',
        'total', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '1', '0');;


INSERT INTO `plugin_reports_reports` (`name`, `sql`, `publish`, `delete`, `action_button_label`, `action_button`,
                                      `action_event`, `date_created`, `dashboard`, widget_id)
SELECT 'Location - host families with students',
       '',
       '1',
       '0',
       '',
       '0',
       '',
       CURRENT_TIMESTAMP,
       '1',
       (select id from plugin_reports_widgets where name = 'Location - host families with students' limit 1)
FROM `plugin_reports_reports`
WHERE NOT EXISTS(SELECT * FROM `plugin_reports_reports` WHERE `name` = 'Location - host families with students')
LIMIT 1
;;

-- Update report to use the latest version of the SQL.

UPDATE
    `plugin_reports_reports`
SET `sql`           = 'SELECT counties.name as `title`, count(counties.id) as ''total''
    FROM
    plugin_contacts3_contacts `contacts`
        INNER JOIN
    plugin_contacts3_contact_type `contact_types` ON (contacts.type = contact_types.contact_type_id)
        INNER JOIN
    plugin_contacts3_contacts_subtypes `contact_subtypes` ON (`contacts`.subtype_id = `contact_subtypes`.id)
        INNER JOIN
    plugin_ib_educate_bookings_has_linked_contacts `linked_contacts` ON contacts.id = linked_contacts.contact_id
        INNER JOIN
    plugin_contacts3_residences `residence` ON contacts.residence = residence.address_id
        INNER JOIN
    engine_counties `counties` ON residence.county = counties.id
WHERE
    (`contact_subtypes`.subtype = ''Host Family''
        OR `contact_types`.label = ''Host Family'')
        AND contacts.is_inactive = 0 group by counties.id;',
    `date_modified` = CURRENT_TIMESTAMP
WHERE `name` = 'Location - host families with students'
  AND `delete` = 0
;;

-- Insert the dashboard
INSERT INTO plugin_dashboards
(title, description, columns, date_filter, date_created, date_modified, created_by, modified_by, publish, deleted)
VALUES ('Host families', 'Host families dashboard', 3, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,
        (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
        (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
        1, 0);;

-- Then insert the dashboard gadgets
INSERT INTO `plugin_dashboards_gadgets` (`dashboard_id`, `gadget_id`, `type_id`, `column`, `order`, `date_created`,
                                         `date_modified`, `publish`, `deleted`)
VALUES ((SELECT `id` FROM `plugin_dashboards` WHERE `title` = 'Host families' ORDER BY `date_created` DESC LIMIT 1),
        (SELECT `id`
         FROM `plugin_reports_reports`
         WHERE `name` = 'Total active host families'
         ORDER BY `date_created` DESC
         LIMIT 1),
        (SELECT `id` FROM `plugin_dashboards_gadget_types` WHERE `stub` = 'sparkline' LIMIT 1),
        '1',
        '1',
        CURRENT_TIMESTAMP,
        CURRENT_TIMESTAMP,
        '1',
        '0');;

INSERT INTO `plugin_dashboards_gadgets` (`dashboard_id`, `gadget_id`, `type_id`, `column`, `order`, `date_created`,
                                         `date_modified`, `publish`, `deleted`)
VALUES ((SELECT `id` FROM `plugin_dashboards` WHERE `title` = 'Host families' ORDER BY `date_created` DESC LIMIT 1),
        (SELECT `id`
         FROM `plugin_reports_reports`
         WHERE `name` = 'Location - Active host families'
         ORDER BY `date_created` DESC
         LIMIT 1),
        (SELECT `id` FROM `plugin_dashboards_gadget_types` WHERE `stub` = 'widget' LIMIT 1),
        '1',
        '2',
        CURRENT_TIMESTAMP,
        CURRENT_TIMESTAMP,
        '1',
        '0');;

INSERT INTO `plugin_dashboards_gadgets` (`dashboard_id`, `gadget_id`, `type_id`, `column`, `order`, `date_created`,
                                         `date_modified`, `publish`, `deleted`)
VALUES ((SELECT `id` FROM `plugin_dashboards` WHERE `title` = 'Host families' ORDER BY `date_created` DESC LIMIT 1),
        (SELECT `id`
         FROM `plugin_reports_reports`
         WHERE `name` = 'Total host families with students'
         ORDER BY `date_created` DESC
         LIMIT 1),
        (SELECT `id` FROM `plugin_dashboards_gadget_types` WHERE `stub` = 'sparkline' LIMIT 1),
        '2',
        '7',
        CURRENT_TIMESTAMP,
        CURRENT_TIMESTAMP,
        '1',
        '0');;

INSERT INTO `plugin_dashboards_gadgets` (`dashboard_id`, `gadget_id`, `type_id`, `column`, `order`, `date_created`,
                                         `date_modified`, `publish`, `deleted`)
VALUES ((SELECT `id` FROM `plugin_dashboards` WHERE `title` = 'Host families' ORDER BY `date_created` DESC LIMIT 1),
        (SELECT `id`
         FROM `plugin_reports_reports`
         WHERE `name` = 'Location - host families with students'
         ORDER BY `date_created` DESC
         LIMIT 1),
        (SELECT `id` FROM `plugin_dashboards_gadget_types` WHERE `stub` = 'widget' LIMIT 1),
        '2',
        '8',
        CURRENT_TIMESTAMP,
        CURRENT_TIMESTAMP,
        '1',
        '0');;

-- Total student departures this week report 12
INSERT INTO `plugin_reports_reports` (`name`, `sql`, `publish`, `delete`, `action_button_label`, `action_button`,
                                      `action_event`, `date_created`, `dashboard`)
SELECT 'Total student departures this week',
       '',
       '1',
       '0',
       '',
       '0',
       '',
       CURRENT_TIMESTAMP,
       '1'
FROM `plugin_reports_reports`
WHERE NOT EXISTS(SELECT * FROM `plugin_reports_reports` WHERE `name` = 'Total student departures this week')
LIMIT 1 ;;

-- Update report to use the latest version of the SQL.

UPDATE
    `plugin_reports_reports`
SET `sql`           = 'SELECT  count(id) as `Total`
FROM
    plugin_logistics_transfers
WHERE
    `type` = ''Departure''
        AND YEARWEEK(`scheduled_date`) = YEARWEEK(NOW());',
    `date_modified` = CURRENT_TIMESTAMP
WHERE `name` = 'Total student departures this week'
  AND `delete` = 0
;;

INSERT IGNORE INTO `plugin_reports_sparklines` (`title`, `report_id`, `chart_type_id`, `total_type_id`, `total_field`,
                                                `text_color`, `background_color`,
                                                `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`,
                                                `deleted`)
VALUES ('Total student departures this week',
        (SELECT `id`
         FROM `plugin_reports_reports`
         WHERE `name` = 'Total student departures this week'
           AND `delete` = 0
         ORDER BY `id` DESC
         LIMIT 1),
        (SELECT `id` FROM `plugin_reports_chart_types` WHERE `stub` = 'total' AND `deleted` = 0 LIMIT 1),
        '0',
        'Total',
        ' ',
        ' ',
        (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
        (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
        CURRENT_TIMESTAMP,
        CURRENT_TIMESTAMP,
        '1',
        '0');;

-- Total student arrivals this week report 13
INSERT INTO `plugin_reports_reports` (`name`, `sql`, `publish`, `delete`, `action_button_label`, `action_button`,
                                      `action_event`, `date_created`, `dashboard`)
SELECT 'Total student arrivals this week',
       '',
       '1',
       '0',
       '',
       '0',
       '',
       CURRENT_TIMESTAMP,
       '1'
FROM `plugin_reports_reports`
WHERE NOT EXISTS(SELECT * FROM `plugin_reports_reports` WHERE `name` = 'Total student arrivals this week')
LIMIT 1 ;;

-- Update report to use the latest version of the SQL.

UPDATE
    `plugin_reports_reports`
SET `sql`           = 'SELECT  count(id) as `Total`
FROM
    plugin_logistics_transfers
WHERE
    `type` = ''Arrival''
        AND YEARWEEK(`scheduled_date`) = YEARWEEK(NOW());',
    `date_modified` = CURRENT_TIMESTAMP
WHERE `name` = 'Total student arrivals this week'
  AND `delete` = 0
;;

INSERT IGNORE INTO `plugin_reports_sparklines` (`title`, `report_id`, `chart_type_id`, `total_type_id`, `total_field`,
                                                `text_color`, `background_color`,
                                                `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`,
                                                `deleted`)
VALUES ('Total student arrivals this week',
        (SELECT `id`
         FROM `plugin_reports_reports`
         WHERE `name` = 'Total student arrivals this week'
           AND `delete` = 0
         ORDER BY `id` DESC
         LIMIT 1),
        (SELECT `id` FROM `plugin_reports_chart_types` WHERE `stub` = 'total' AND `deleted` = 0 LIMIT 1),
        '0',
        'Total',
        ' ',
        ' ',
        (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
        (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
        CURRENT_TIMESTAMP,
        CURRENT_TIMESTAMP,
        '1',
        '0');;

-- Insert the dashboards
INSERT INTO plugin_dashboards
(title, description, columns, date_filter, date_created, date_modified, created_by, modified_by, publish, deleted)
VALUES ('Transfers', 'Transfers dashboard', 3, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,
        (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
        (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
        1, 0);;

-- Then insert the dashboard gadgets
INSERT INTO `plugin_dashboards_gadgets` (`dashboard_id`, `gadget_id`, `type_id`, `column`, `order`, `date_created`,
                                         `date_modified`, `publish`, `deleted`)
VALUES ((SELECT `id` FROM `plugin_dashboards` WHERE `title` = 'Transfers' ORDER BY `date_created` DESC LIMIT 1),
        (SELECT `id`
         FROM `plugin_reports_reports`
         WHERE `name` = 'Total student departures this week'
         ORDER BY `date_created` DESC
         LIMIT 1),
        (SELECT `id` FROM `plugin_dashboards_gadget_types` WHERE `stub` = 'sparkline' LIMIT 1),
        '1',
        '1',
        CURRENT_TIMESTAMP,
        CURRENT_TIMESTAMP,
        '1',
        '0');;

INSERT INTO `plugin_dashboards_gadgets` (`dashboard_id`, `gadget_id`, `type_id`, `column`, `order`, `date_created`,
                                         `date_modified`, `publish`, `deleted`)
VALUES ((SELECT `id` FROM `plugin_dashboards` WHERE `title` = 'Transfers' ORDER BY `date_created` DESC LIMIT 1),
        (SELECT `id`
         FROM `plugin_reports_reports`
         WHERE `name` = 'Total student arrivals this week'
         ORDER BY `date_created` DESC
         LIMIT 1),
        (SELECT `id` FROM `plugin_dashboards_gadget_types` WHERE `stub` = 'sparkline' LIMIT 1),
        '2',
        '1',
        CURRENT_TIMESTAMP,
        CURRENT_TIMESTAMP,
        '1',
        '0');;