/*
ts:2017-12-19 16:11:00
*/

UPDATE engine_project_role SET default_dashboard_id=(select id from plugin_dashboards where title='My Orders') WHERE `role` = 'External User';
UPDATE engine_users SET default_dashboard_id=-1 WHERE `id` IN (SELECT DISTINCT e.owned_by FROM plugin_events_events e);

