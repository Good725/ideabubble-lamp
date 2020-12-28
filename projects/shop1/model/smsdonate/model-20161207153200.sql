/*
ts:2016-12-07 15:32:00
*/

UPDATE plugin_messaging_notification_templates
  SET publish = 0, deleted = 1
  WHERE `name` IN ('course-booking-admin', 'course-booking-parent', 'ticket-purchased-buyer', 'email-event-attendees', 'ticket-purchased-seller', 'event-invoice', 'event-ticket', 'outstanding-bookings-8w-reminder', 'booking-balance-payment-admin', 'booking-balance-payment-customer', 'new_booking_customer', 'new_booking_admin', 'successful_payment_seller', 'successful_payment_customer');

UPDATE plugin_messaging_notification_templates
  SET `name` = 'donation-sms-status-approve', `message` = 'Your request has been approved'
  WHERE `name` = 'donation-sms-status-confirm';

UPDATE plugin_messaging_notification_templates
  SET publish = 0, deleted = 1
  WHERE `name` IN ('donation-sms-status-complete');


UPDATE plugin_reports_reports
	INNER JOIN plugin_reports_widgets ON plugin_reports_reports.widget_id = plugin_reports_widgets.id
	SET
	  plugin_reports_reports.name = 'Monthly Number Of Rejected Requests',
		plugin_reports_reports.sql = 'SELECT \n		DATE_FORMAT(d.created, \'%Y-%m\') AS `Period`,\n		COUNT(*) AS `Quantity`\n	FROM plugin_donations_donations d\n		LEFT JOIN plugin_donations_products p ON d.product_id = p.id\n		LEFT JOIN plugin_contacts_contact c ON d.contact_id = c.id\n	WHERE d.status = \'Rejected\' AND \'{!DASHBOARD-FROM!}\' <= d.created AND DATE_ADD(\'{!DASHBOARD-TO!}\', INTERVAL 1 DAY) > d.created\n	GROUP BY `Period`',
		plugin_reports_widgets.name = 'Number Of Rejected'
	WHERE plugin_reports_reports.name = 'Monthly Number Of Valid Requests Awaiting Decision';

UPDATE plugin_reports_reports
	INNER JOIN plugin_reports_widgets ON plugin_reports_reports.widget_id = plugin_reports_widgets.id
	SET
		plugin_reports_reports.name = 'Monthly Value Of Rejected Requests',
		plugin_reports_reports.sql = 'SELECT \n		DATE_FORMAT(d.created, \'%Y-%m\') AS `Period`,\n		SUM(IFNULL(p.value, 0)) AS `Value`\n	FROM plugin_donations_donations d\n		LEFT JOIN plugin_donations_products p ON d.product_id = p.id\n		LEFT JOIN plugin_contacts_contact c ON d.contact_id = c.id\n	WHERE d.status = \'Rejected\' AND \'{!DASHBOARD-FROM!}\' <= d.created AND DATE_ADD(\'{!DASHBOARD-TO!}\', INTERVAL 1 DAY) > d.created\n	GROUP BY `Period`',
		plugin_reports_widgets.name = 'Value Of Rejected'
	WHERE plugin_reports_reports.name = 'Monthly Value Of Valid Requests Awaiting Decision';

UPDATE plugin_reports_reports
	INNER JOIN plugin_reports_widgets ON plugin_reports_reports.widget_id = plugin_reports_widgets.id
	SET
		plugin_reports_reports.name = 'Weekly Number Of Rejected Requests',
		plugin_reports_reports.sql = 'SELECT \n		DATE_FORMAT(ADDDATE(d.created, INTERVAL 1 - DAYOFWEEK(d.created) DAY), \'%Y-%m-%d\') AS `Period`,\n		COUNT(*) AS `Quantity`\n	FROM plugin_donations_donations d\n		LEFT JOIN plugin_donations_products p ON d.product_id = p.id\n		LEFT JOIN plugin_contacts_contact c ON d.contact_id = c.id\n	WHERE d.status = \'Rejected\' AND \'{!DASHBOARD-FROM!}\' <= d.created AND DATE_ADD(\'{!DASHBOARD-TO!}\', INTERVAL 1 DAY) > d.created\n	GROUP BY `Period`',
		plugin_reports_widgets.name = 'Number Of Rejected'
	WHERE plugin_reports_reports.name = 'Weekly Number Of Valid Requests Awaiting Decision';

UPDATE plugin_reports_reports
	INNER JOIN plugin_reports_widgets ON plugin_reports_reports.widget_id = plugin_reports_widgets.id
	SET
		plugin_reports_reports.name = 'Weekly Value Of Rejected Requests',
		plugin_reports_reports.sql = 'SELECT \n		DATE_FORMAT(ADDDATE(d.created, INTERVAL 1 - DAYOFWEEK(d.created) DAY), \'%Y-%m-%d\') AS `Period`,\n		SUM(IFNULL(p.value, 0)) AS `Value`\n	FROM plugin_donations_donations d\n		LEFT JOIN plugin_donations_products p ON d.product_id = p.id\n		LEFT JOIN plugin_contacts_contact c ON d.contact_id = c.id\n	WHERE d.status = \'Rejected\' AND \'{!DASHBOARD-FROM!}\' <= d.created AND DATE_ADD(\'{!DASHBOARD-TO!}\', INTERVAL 1 DAY) > d.created\n	GROUP BY `Period`',
		plugin_reports_widgets.name = 'Value Of Rejected'
	WHERE plugin_reports_reports.name = 'Weekly Value Of Valid Requests Awaiting Decision';

UPDATE plugin_reports_reports
	INNER JOIN plugin_reports_widgets ON plugin_reports_reports.widget_id = plugin_reports_widgets.id
	SET
	  plugin_reports_reports.name = 'Daily Number Of Requests in Received',
		plugin_reports_widgets.name = 'Number in Received'
	WHERE plugin_reports_reports.name = 'Daily Number Of Valid Requests Awaiting Decision';

UPDATE plugin_reports_reports
	INNER JOIN plugin_reports_widgets ON plugin_reports_reports.widget_id = plugin_reports_widgets.id
	SET
	  plugin_reports_reports.name = 'Daily Value Of Requests in Received',
		plugin_reports_widgets.name = 'Value in Received'
	WHERE plugin_reports_reports.name = 'Daily Value Of Valid Requests Awaiting Decision';

