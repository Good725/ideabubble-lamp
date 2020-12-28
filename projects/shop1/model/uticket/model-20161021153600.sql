/*
ts:2016-10-21 15:37:00
*/

/*do not share admin dashbord with external users*/
DELETE s
	FROM plugin_dashboards d
		INNER JOIN plugin_dashboards_sharing s ON d.id = s.dashboard_id
		INNER JOIN engine_project_role r ON s.group_id = r.id
	WHERE d.title = 'Admin' AND r.role = 'External User';

update plugin_dashboards d
	inner join plugin_dashboards_gadgets g on d.id = g.dashboard_id
	inner join plugin_reports_reports r ON g.gadget_id = r.id
	set g.deleted = 0
	where d.id = 6 and r.`name` in ('Admin Total Events', 'Admin Total Tickets', 'Admin Total Profit', 'Admin Booking Fee Total');

/*this is to prevent total profit report to be disabled on external users home*/
update plugin_reports_reports set dashboard=0 where `name` = 'Admin Total Profit';
