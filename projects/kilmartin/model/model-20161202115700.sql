/*
ts:2016-12-02 11:57:00
*/

UPDATE `plugin_reports_reports` SET `sql`='SELECT\n	t4.type AS `Transaction Type`,\n	b.booking_id AS `Booking Id`,\n	CONCAT(c3.first_name, \' \', c3.last_name) AS `Booking Name`,\n	emails.`value` AS `Email`,\n	mobiles.`value` AS `Mobile`,\n	CONCAT(c.title,\' \',c.first_name,\' \',c.last_name) AS `Payee`,\n	s.`name` AS `Schedule`,\n	t.payment_due_date AS `Due Date`,\n	SUM(t.amount) AS `Amount`,\n	SUM((SELECT\n					IFNULL(SUM(IF(t2.credit, t1.amount, -t1.amount)), 0)\n				FROM\n					plugin_bookings_transactions_payments AS `t1`\n					JOIN plugin_bookings_transactions_payments_statuses AS `t2` ON t1.`status` = t2.id\n				WHERE\n			t1.transaction_id = t.id)) AS `Paid`,\n	SUM((t.amount - \n		IFNULL(\n			(SELECT\n					SUM(IF(t2.credit, t1.amount, -t1.amount))\n				FROM\n					plugin_bookings_transactions_payments AS `t1`\n					JOIN plugin_bookings_transactions_payments_statuses AS `t2` ON t1.`status` = t2.id\n				WHERE\n			t1.transaction_id = t.id),\n			0\n		))) AS `Amount Due`\n	\nFROM plugin_bookings_transactions `t`\n	INNER JOIN plugin_bookings_transactions_types `t4` ON t4.id = t.type\n	INNER JOIN plugin_ib_educate_bookings `b` ON t.booking_id = b.booking_id\n	INNER JOIN plugin_contacts3_contacts `c` ON t.contact_id = c.id\n	INNER JOIN plugin_bookings_transactions_has_schedule ts ON t.id = ts.transaction_id AND ts.deleted = 0\n	INNER JOIN plugin_ib_educate_booking_has_schedules bs ON b.booking_id = bs.booking_id AND bs.schedule_id = ts.schedule_id AND bs.deleted = 0\n	INNER JOIN plugin_courses_schedules s ON bs.schedule_id = s.id AND s.`delete` = 0\n	INNER JOIN plugin_courses_courses `c2` ON s.course_id = c2.id\n	INNER JOIN plugin_contacts3_contacts `c3` ON c3.id = b.contact_id\n	LEFT JOIN plugin_contacts3_contact_has_notifications emails ON c3.notifications_group_id = emails.group_id AND emails.notification_id = 1\n	LEFT JOIN plugin_contacts3_contact_has_notifications mobiles ON c3.notifications_group_id = mobiles.group_id AND mobiles.notification_id = 2\n\nWHERE t.payment_due_date >= \'{!From!}\' AND t.payment_due_date < DATE_ADD(\'{!To!}\', INTERVAL 1 DAY) __PHP1__\n\nGROUP BY `Booking Id`, `Schedule`\n\nHAVING `Amount Due` > 0', `php_modifier`='$scheduleIds = $this->get_parameter(\'Schedule\');\n$sql = $this->_sql;\nif ($scheduleIds)$sql = str_replace(\'__PHP1__\', \'AND s.id IN (\' . implode(\',\', $scheduleIds) . \')\', $sql);\nelse $sql = str_replace(\'__PHP1__\', \'\', $sql);\n$this->_sql = $sql;\n\r\n' WHERE (`name`='Payment Due');

INSERT INTO `plugin_reports_parameters`
  SET
    report_id = (select id from plugin_reports_reports where `name`='Payment Due'),
    `type` = 'custom',
    name = 'Schedule',
    `delete` = 0,
    is_multiselect = 1,
    `value`='SELECT\n	s.id,\n	s.`name` AS `Schedule`\n	\nFROM plugin_bookings_transactions `t`\n	INNER JOIN plugin_bookings_transactions_types `t4` ON t4.id = t.type\n	INNER JOIN plugin_ib_educate_bookings `b` ON t.booking_id = b.booking_id\n	INNER JOIN plugin_contacts3_contacts `c` ON t.contact_id = c.id\n	INNER JOIN plugin_bookings_transactions_has_schedule ts ON t.id = ts.transaction_id AND ts.deleted = 0\n	INNER JOIN plugin_ib_educate_booking_has_schedules bs ON b.booking_id = bs.booking_id AND bs.schedule_id = ts.schedule_id AND bs.deleted = 0\n	INNER JOIN plugin_courses_schedules s ON bs.schedule_id = s.id AND s.`delete` = 0\n	INNER JOIN plugin_courses_courses `c2` ON s.course_id = c2.id\n	INNER JOIN plugin_contacts3_contacts `c3` ON c3.id = b.contact_id\n	LEFT JOIN plugin_contacts3_contact_has_notifications emails ON c3.notifications_group_id = emails.group_id AND emails.notification_id = 1\n	LEFT JOIN plugin_contacts3_contact_has_notifications mobiles ON c3.notifications_group_id = mobiles.group_id AND mobiles.notification_id = 2\n\nGROUP BY `Schedule`\n\nHAVING SUM((t.amount - \n		IFNULL(\n			(SELECT\n					SUM(IF(t2.credit, t1.amount, -t1.amount))\n				FROM\n					plugin_bookings_transactions_payments AS `t1`\n					JOIN plugin_bookings_transactions_payments_statuses AS `t2` ON t1.`status` = t2.id\n				WHERE\n			t1.transaction_id = t.id),\n			0\n		))) > 0';