/*
ts:2016-11-11 09:51:00
*/

DELETE pr
	FROM engine_plugins_per_role pr
		INNER JOIN engine_plugins p ON pr.plugin_id = p.id
		INNER JOIN engine_project_role r ON pr.role_id = r.id
	WHERE r.role NOT IN ('Super User', 'Administrator', 'Manager') AND p.name IN ('contacts2', 'contacts3');
