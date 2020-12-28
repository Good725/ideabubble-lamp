/*
ts:2017-07-26 17:04:00
*/

DELETE ds
	FROM plugin_dashboards_sharing ds
		INNER JOIN plugin_dashboards d ON ds.dashboard_id = d.id
	WHERE d.title IN ('Traffic', 'Sales');

INSERT INTO plugin_dashboards_sharing
	(dashboard_id, group_id)
	(SELECT d.id, r.id FROM plugin_dashboards d INNER JOIN engine_project_role r WHERE d.title IN ('Traffic', 'Sales') AND r.role IN ('Administrator', 'Super User', 'Manager'));
