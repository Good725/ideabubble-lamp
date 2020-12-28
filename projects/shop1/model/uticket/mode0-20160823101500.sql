/*
ts:2016-08-23 10:15:00
*/

UPDATE `plugin_reports_sparklines` SET
`chart_type_id`     = (SELECT `id` FROM `plugin_reports_chart_types` WHERE `stub` = 'total'),
`dashboard_link_id` = (SELECT `id` FROM `plugin_dashboards` WHERE `title` = 'Admin'),
`date_modified`     = CURRENT_TIMESTAMP
WHERE `title`='Total Traffic';
