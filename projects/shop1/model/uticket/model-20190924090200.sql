/*
ts:2019-09-24 09:02:00
*/
DELIMITER ;;
-- Insert the dashboard

INSERT INTO plugin_dashboards
(title, description, columns, date_filter, date_created, date_modified, created_by, modified_by, publish, deleted)
SELECT 'My total sales', 'My total sales dashboard', 3, 0, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP,
        (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
        (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
        1, 0
FROM plugin_dashboards WHERE NOT EXISTS(select `id` from plugin_dashboards where title = 'My total sales') LIMIT 1;;

-- Then insert the dashboard gadgets
INSERT INTO `plugin_dashboards_gadgets` (`dashboard_id`, `gadget_id`, `type_id`, `column`, `order`, `date_created`,
                                         `date_modified`, `publish`, `deleted`)
SELECT (SELECT `id` FROM `plugin_dashboards` WHERE `title` = 'My total sales' ORDER BY `date_created` DESC LIMIT 1),
        (SELECT `id`
         FROM `plugin_reports_reports`
         WHERE `name` = 'Total Orders'
         ORDER BY `date_created` DESC
         LIMIT 1),
        (SELECT `id` FROM `plugin_dashboards_gadget_types` WHERE `stub` = 'sparkline' LIMIT 1),
        '1',
        '1',
        CURRENT_TIMESTAMP,
        CURRENT_TIMESTAMP,
        '1',
        '0'
FROM plugin_dashboards_gadgets
WHERE NOT EXISTS(select `dashboard_id` from plugin_dashboards_gadgets where `dashboard_id` = (SELECT `id`
                                                                                              FROM `plugin_dashboards`
                                                                                              WHERE `title` = 'My total sales'
                                                                                              ORDER BY `date_created` DESC
                                                                                              LIMIT 1) and  `column` = '1') LIMIT 1;;


INSERT INTO `plugin_dashboards_gadgets` (`dashboard_id`, `gadget_id`, `type_id`, `column`, `order`, `date_created`,
                                         `date_modified`, `publish`, `deleted`)
SELECT (SELECT `id`
         FROM `plugin_dashboards`
         WHERE `title` = 'My total sales'
         ORDER BY `date_created` DESC
         LIMIT 1),
        (SELECT `id`
         FROM `plugin_reports_reports`
         WHERE `name` = 'Total Revenue'
         ORDER BY `date_created` DESC
         LIMIT 1),
        (SELECT `id` FROM `plugin_dashboards_gadget_types` WHERE `stub` = 'sparkline' LIMIT 1),
        '2',
        '1',
        CURRENT_TIMESTAMP,
        CURRENT_TIMESTAMP,
        '1',
        '0'
FROM plugin_dashboards_gadgets
WHERE NOT EXISTS(select `dashboard_id`
                 from plugin_dashboards_gadgets
                 where `dashboard_id` = (SELECT `id`
                                         FROM `plugin_dashboards`
                                         WHERE `title` = 'My total sales'
                                         ORDER BY `date_created` DESC
                                         LIMIT 1)
                   and `column` = '2') LIMIT 1;;

INSERT INTO `plugin_dashboards_gadgets` (`dashboard_id`, `gadget_id`, `type_id`, `column`, `order`, `date_created`,
                                         `date_modified`, `publish`, `deleted`)
SELECT (SELECT `id`
         FROM `plugin_dashboards`
         WHERE `title` = 'My total sales'
         ORDER BY `date_created` DESC
         LIMIT 1),
        (SELECT `id`
         FROM `plugin_reports_reports`
         WHERE `name` = 'My Live Events'
         ORDER BY `date_created` DESC
         LIMIT 1),
        (SELECT `id` FROM `plugin_dashboards_gadget_types` WHERE `stub` = 'sparkline' LIMIT 1),
        '3',
        '1',
        CURRENT_TIMESTAMP,
        CURRENT_TIMESTAMP,
        '1',
        '0'
FROM plugin_dashboards_gadgets
WHERE NOT EXISTS(select `dashboard_id`
                 from plugin_dashboards_gadgets
                 where `dashboard_id` = (SELECT `id`
                                         FROM `plugin_dashboards`
                                         WHERE `title` = 'My total sales'
                                         ORDER BY `date_created` DESC
                                         LIMIT 1)
                   and `column` = '3') LIMIT 1;;

UPDATE `engine_project_role`
SET `default_dashboard_id` = (select id
                              from plugin_dashboards
                              where `title` = 'My Orders'
                              order by `date_created` desc
                              limit 1)
WHERE `role` = 'External User';;

UPDATE engine_users
SET default_dashboard_id = (select id
                            from plugin_dashboards
                            where `title` = 'My total sales'
                            order by `date_created` desc
                            limit 1)
WHERE `id` IN (SELECT DISTINCT e.owned_by
               FROM plugin_events_events e);;