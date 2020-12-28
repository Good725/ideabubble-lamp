/*
ts:2017-02-12 22:20:00
*/

UPDATE plugin_reports_reports INNER JOIN plugin_reports_widgets on plugin_reports_reports.widget_id = plugin_reports_widgets.id
	SET plugin_reports_widgets.x_axis = 'teacher', plugin_reports_widgets.y_axis = 'qty'
	WHERE plugin_reports_reports.name = 'Attendance By Teacher';
