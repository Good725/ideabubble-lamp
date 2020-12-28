/*
ts:2019-09-26 17:00:02
*/

DELIMITER ;;

INSERT IGNORE INTO `plugin_dashboards` (`title`, `description`, `columns`, `date_filter`, `date_created`,
                                        `date_modified`,
                                        `publish`, `deleted`)
VALUES ('My dashboard', 'My dashboard', '3', '0', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '1', '0');;

INSERT IGNORE INTO engine_project_role (`role`, `description`, `default_dashboard_id`)
VALUES ('Org rep', 'Org rep', (select `id` from plugin_dashboards where `title` = 'Org rep' LIMIT 1));;

INSERT IGNORE INTO engine_role_permissions (role_id, resource_id)
SELECT (SELECT id
        FROM engine_project_role
        WHERE role = 'Org rep'),
       rp.resource_id
FROM engine_role_permissions `rp`
         INNER JOIN
     engine_project_role `pr` ON rp.role_id = pr.id
WHERE pr.role = 'Parent/Guardian';;

INSERT IGNORE INTO plugin_contacts3_contact_type (`name`, `display_name`, `label`, `publish`, `deletable`)
VALUES ('org_rep', 'Org reps', 'Org rep', 1, 0);;

INSERT INTO `plugin_reports_reports` (`name`, `sql`, `publish`, `delete`, `action_button_label`, `action_button`,
                                      `action_event`, `date_created`, `dashboard`)
SELECT 'My total booked courses',
       '',
       '1',
       '0',
       '',
       '0',
       '',
       CURRENT_TIMESTAMP,
       '1'
FROM `plugin_reports_reports`
WHERE NOT EXISTS(SELECT * FROM `plugin_reports_reports` WHERE `name` = 'My total booked courses')
LIMIT 1 ;;

UPDATE
    `plugin_reports_reports`
SET `sql`           = 'SELECT DISTINCT
    count(`bookings`.booking_id) as `total`
FROM
    `plugin_ib_educate_bookings` AS `bookings`
        INNER JOIN
    plugin_contacts3_contacts `contacts` ON bookings.contact_id = contacts.id
        INNER JOIN
    engine_users `users` ON contacts.linked_user_id = users.id
WHERE
    `bookings`.`delete` = 0
    and users.id = @user_id
ORDER BY `bookings`.`booking_id` DESC;',
    `date_modified` = CURRENT_TIMESTAMP
WHERE `name` = 'My total booked courses'
  AND `delete` = 0 ;;

INSERT IGNORE INTO `plugin_reports_sparklines` (`title`, `report_id`, `chart_type_id`, `total_type_id`, `total_field`,
                                                `text_color`, `background_color`,
                                                `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`,
                                                `deleted`)
VALUES ('Total booked courses',
        (SELECT `id`
         FROM `plugin_reports_reports`
         WHERE `name` = 'My total booked courses'
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

-- My total profiles created report 2
INSERT INTO `plugin_reports_reports` (`name`, `sql`, `publish`, `delete`, `action_button_label`, `action_button`,
                                      `action_event`, `date_created`, `dashboard`)
SELECT 'My total profiles created',
       '',
       '1',
       '0',
       '',
       '0',
       '',
       CURRENT_TIMESTAMP,
       '1'
FROM `plugin_reports_reports`
WHERE NOT EXISTS(SELECT * FROM `plugin_reports_reports` WHERE `name` = 'My total profiles created')
LIMIT 1 ;;

-- Update report to use the latest version of the SQL.

UPDATE
    `plugin_reports_reports`
SET `sql`           = 'SELECT DISTINCT
    count(DISTINCT `users`.`id`) as `Total`
FROM
    `plugin_ib_educate_bookings` AS `bookings`
        INNER JOIN
    plugin_contacts3_contacts `contacts` ON bookings.contact_id = contacts.id
        INNER JOIN
    engine_users `users` ON contacts.linked_user_id = users.id
WHERE
    `bookings`.`delete` = 0
    and users.id = @user_id
ORDER BY `bookings`.`booking_id` DESC;',
    `date_modified` = CURRENT_TIMESTAMP
WHERE `name` = 'My total profiles created'
  AND `delete` = 0 ;;

INSERT IGNORE INTO `plugin_reports_sparklines` (`title`, `report_id`, `chart_type_id`, `total_type_id`, `total_field`,
                                                `text_color`, `background_color`,
                                                `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`,
                                                `deleted`)
VALUES ('Total profiles created',
        (SELECT `id`
         FROM `plugin_reports_reports`
         WHERE `name` = 'My total profiles created'
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

-- My total attended courses report 3
INSERT INTO `plugin_reports_reports` (`name`, `sql`, `publish`, `delete`, `action_button_label`, `action_button`,
                                      `action_event`, `date_created`, `dashboard`)
SELECT 'My total attended courses',
       '',
       '1',
       '0',
       '',
       '0',
       '',
       CURRENT_TIMESTAMP,
       '1'
FROM `plugin_reports_reports`
WHERE NOT EXISTS(SELECT * FROM `plugin_reports_reports` WHERE `name` = 'My total attended courses')
LIMIT 1 ;;

-- Update report to use the latest version of the SQL.

UPDATE
    `plugin_reports_reports`
SET `sql`           = 'SELECT
    count(bookings.booking_id) as `Total`
FROM
    plugin_ib_educate_bookings `bookings`
        INNER JOIN
    plugin_contacts3_contacts `contacts` ON bookings.contact_id = contacts.id
        INNER JOIN
    engine_users `users` ON contacts.linked_user_id = users.id
        INNER JOIN
    plugin_ib_educate_bookings_status `booking_status` ON bookings.booking_status = booking_status.status_id
WHERE
    users.id = @user_id
        AND booking_status.title = ''Completed''',
    `date_modified` = CURRENT_TIMESTAMP
WHERE `name` = 'My total attended courses'
  AND `delete` = 0 ;;

INSERT IGNORE INTO `plugin_reports_sparklines` (`title`, `report_id`, `chart_type_id`, `total_type_id`, `total_field`,
                                                `text_color`, `background_color`,
                                                `created_by`, `modified_by`, `date_created`, `date_modified`, `publish`,
                                                `deleted`)
VALUES ('Total attended courses',
        (SELECT `id`
         FROM `plugin_reports_reports`
         WHERE `name` = 'My total attended courses'
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

-- Then insert the dashboard gadgets
INSERT INTO `plugin_dashboards_gadgets` (`dashboard_id`, `gadget_id`, `type_id`, `column`, `order`, `date_created`,
                                         `date_modified`, `publish`, `deleted`)
VALUES ((SELECT `id` FROM `plugin_dashboards` WHERE `title` = 'My dashboard' ORDER BY `date_created` DESC LIMIT 1),
        (SELECT `id`
         FROM `plugin_reports_reports`
         WHERE `name` = 'My total booked courses'
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
VALUES ((SELECT `id` FROM `plugin_dashboards` WHERE `title` = 'My dashboard' ORDER BY `date_created` DESC LIMIT 1),
        (SELECT `id`
         FROM `plugin_reports_reports`
         WHERE `name` = 'My total profiles created'
         ORDER BY `date_created` DESC
         LIMIT 1),
        (SELECT `id` FROM `plugin_dashboards_gadget_types` WHERE `stub` = 'sparkline' LIMIT 1),
        '2',
        '1',
        CURRENT_TIMESTAMP,
        CURRENT_TIMESTAMP,
        '1',
        '0');;

INSERT INTO `plugin_dashboards_gadgets` (`dashboard_id`, `gadget_id`, `type_id`, `column`, `order`, `date_created`,
                                         `date_modified`, `publish`, `deleted`)
VALUES ((SELECT `id` FROM `plugin_dashboards` WHERE `title` = 'My dashboard' ORDER BY `date_created` DESC LIMIT 1),
        (SELECT `id`
         FROM `plugin_reports_reports`
         WHERE `name` = 'My total attended courses'
         ORDER BY `date_created` DESC
         LIMIT 1),
        (SELECT `id` FROM `plugin_dashboards_gadget_types` WHERE `stub` = 'sparkline' LIMIT 1),
        '3',
        '1',
        CURRENT_TIMESTAMP,
        CURRENT_TIMESTAMP,
        '1',
        '0');;

UPDATE engine_project_role
SET `default_dashboard_id` = (SELECT id
                              FROM plugin_dashboards
                              WHERE title = 'My dashboard')
WHERE engine_project_role.role = 'Org rep';;

UPDATE engine_project_role
SET `default_dashboard_id` = (SELECT id
                              FROM plugin_dashboards
                              WHERE title = 'My dashboard')
WHERE engine_project_role.role = 'Parent/Guardian';;