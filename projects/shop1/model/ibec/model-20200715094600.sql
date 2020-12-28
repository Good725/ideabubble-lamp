/*
ts: 2020-07-15 09:46:00
*/

INSERT INTO `plugin_reports_reports` (`autoload`, `name`, `sql`, `category`, `sub_category`, `dashboard`, `publish`, `delete`, `report_type`) VALUES (1, 'Navision Bookings Sync Queue', 'select bookings.booking_id as `Booking ID`, s.title as `Booking Status`, DATE_FORMAT(bookings.created_date, \'%d/%m/%Y %H:%i\') as `Date Created`, CONCAT_WS(\' \', contacts.first_name, contacts.last_name) as `Lead Booker`\r\nfrom plugin_ib_educate_bookings bookings\r\nleft join engine_remote_sync rs on bookings.booking_id = rs.cms_id and rs.type=\'Navision-booking\' and bookings.`delete` = 0\r\nleft join plugin_ib_educate_bookings_status s on bookings.booking_status = s.status_id\r\nleft join plugin_contacts3_contacts contacts on bookings.contact_id = contacts.id\r\nwhere rs.remote_id is null and bookings.booking_status <> 3\r\norder by bookings.created_date desc;\r\n\r\n', '0', '0', '0', '1', '0', 'sql');

INSERT INTO `plugin_reports_reports` (`autoload`, `name`, `sql`, `publish`, `delete`, `report_type`) VALUES (1, 'Navision Transactions Sync Queue', 'select tx.id as `Transaction ID`, tx.booking_id as `Booking ID`, tx.total as `Total`, DATE_FORMAT(tx.created, \'%d/%m/%Y %H:%i\') as `Date Created`, CONCAT_WS(\' \', contacts.first_name, contacts.last_name) as `Payer` from plugin_bookings_transactions tx\r\nleft join engine_remote_sync rs on tx.id = rs.cms_id and rs.type=\'Navision-Transaction\' and tx.deleted = 0\r\nleft join plugin_contacts3_contacts contacts on tx.contact_id = contacts.id\r\nwhere rs.remote_id is null\r\norder by tx.created desc;\r\n', '1', '0', 'sql');

INSERT INTO `plugin_reports_reports` (`autoload`, `name`, `sql`, `publish`, `delete`, `report_type`) VALUES (1, 'Navision Payments Sync Queue', 'select pays.id as `Payment Id`, tx.id as `Transaction ID`, tx.booking_id as `Booking ID`, tx.total as `Total`, DATE_FORMAT(pays.created, \'%d/%m/%Y %H:%i\') as `Date Created`, CONCAT_WS(\' \', contacts.first_name, contacts.last_name) as `Payer` from plugin_bookings_transactions_payments pays\r\nleft join engine_remote_sync rs on pays.id = rs.cms_id and rs.type=\'Navision-Payment\' and pays.deleted = 0\r\nleft join plugin_bookings_transactions tx on pays.transaction_id = tx.id\r\nleft join plugin_contacts3_contacts contacts on tx.contact_id = contacts.id\r\nwhere rs.remote_id is null\r\norder by pays.created desc;\r\n', '1', '0', 'sql');


