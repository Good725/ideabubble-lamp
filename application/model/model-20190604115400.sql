/*
ts:2019-06-04 11:54:00
*/

INSERT IGNORE INTO `plugin_dashboards` (`title`, `description`, `columns`, `date_filter`, `date_created`, `date_modified`,
                                                             `publish`, `deleted`)
VALUES ('Coordinator', 'Coordinator', '3', '0', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, '1', '0');

INSERT IGNORE INTO engine_project_role (`role`, `description`, `default_dashboard_id`)
VALUES ('Coordinator', 'Coordinator', (select `id` from plugin_dashboards where `title` = 'Coordinator' LIMIT 1));

INSERT IGNORE INTO `engine_resources`
    (`type_id`, `alias`, `name`, `description`, parent_controller)
VALUES (1, 'contacts3_limited_bookings_linked_contacts', 'KES Contacts / Limited Linked Contacts Bookings',
        'KES Contacts / Limited Linked Contacts Bookings',
        (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'contacts3' LIMIT 1));

INSERT IGNORE INTO engine_role_permissions (role_id, resource_id)
    (SELECT r.id, e.id
     FROM `engine_project_role` r
              JOIN engine_resources e
     WHERE r.role IN ('Coordinator')
       AND e.alias = 'contacts3_frontend_bookings');

INSERT IGNORE INTO engine_role_permissions (role_id, resource_id)
    (SELECT r.id, e.id
     FROM `engine_project_role` r
              JOIN engine_resources e
     WHERE r.role IN ('Coordinator')
       AND e.alias = 'contacts3_limited_bookings_linked_contacts');

INSERT IGNORE INTO `engine_resources`
    (`type_id`, `alias`, `name`, `description`, parent_controller)
VALUES (1, 'contacts3_limited_bookings_linked_contacts', 'KES Contacts / Limited Linked Contacts Bookings',
        'KES Contacts / Limited Linked Contacts Bookings',
        (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'contacts3' LIMIT 1));

INSERT IGNORE INTO engine_role_permissions
    (role_id, resource_id)
    (SELECT r.id, e.id
     FROM `engine_project_role` r
              JOIN engine_resources e
     WHERE r.role IN ('Administrator')
       AND e.alias = 'contacts3_settings');