/*
ts:2020-09-09 13:46:00
*/

INSERT INTO `plugin_reports_reports` (`name`, `sql`, `date_modified`, `publish`, `delete`, `report_type`, `autoload`) VALUES ('Transactions', 'select \r\n	transactions.id as `Transaction ID`, transactions.booking_id as `Booking ID`, transactions.amount as `Amount`, transactions.fee as `Fee`, transactions.discount as `Discount`, transactions.total as `Total`, transactions.created as `Date Created`, count(*) as `Delegate Count`, contacts.first_name as `First Name`, contacts.last_name as `Last Name`, orgs.first_name as `Organization`\r\nfrom plugin_bookings_transactions transactions\r\ninner join plugin_contacts3_contacts contacts on transactions.contact_id = contacts.id\r\ninner join plugin_ib_educate_bookings_has_delegates has_delegates on transactions.booking_id = has_delegates.booking_id\r\nleft join plugin_contacts3_relations rels on contacts.id = rels.child_id\r\ninner join plugin_contacts3_contacts orgs on rels.parent_id = orgs.id\r\nwhere transactions.deleted = 0\r\ngroup by transactions.id\r\norder by transactions.updated desc;\r\n', '2020-09-09 15:52:06', '1', '0', 'sql', '1');

UPDATE `plugin_reports_reports` SET `sql`='\n
\n select
\n  transactions.id as `Transaction ID`,
\n  transaction_types.`type` as `Transaction Type`,
\n  transactions.booking_id as `Booking ID`,
\n  applied_booking_discounts. `Unit Discounts` as `Unit Discounts`,
\n  applied_booking_discounts. `Unit Discount Amount` as `Unit Discount Amount`,
\n  applied_booking_discounts. `Member Discounts` as `Member Discounts`,
\n  applied_booking_discounts. `Member Discount Amount` as `Member Discount Amount`,
\n  transactions.amount as `Transaction Amount`,
\n  transactions.fee as `Fee`,
\n  transactions.discount as `Discount`,
\n  transactions.total as `Total`,
\n  bookings.invoice_details as `Purchase Order No`,
\n  payments.`type` as `Payment Type`,
\n  transactions.created as `Date Created`,
\n  GROUP_CONCAT(has_delegates.cancel_reason_code) as `Reason Code`,
\n  count(*) as `Quantity	Delegate Count`,
\n contacts.first_name as `First Name`,
\n contacts.last_name as `Last Name`,
\n orgs.first_name as `Organization`
\n from
\n   plugin_bookings_transactions transactions
\n   left join plugin_bookings_transactions_types transaction_types ON transactions.`type` = transaction_types.id
\n   inner join plugin_contacts3_contacts contacts on transactions.contact_id = contacts.id
\n   left join  plugin_ib_educate_bookings bookings ON transactions.booking_id = bookings.booking_id
\n   left join (
\n      SELECT booking_id,
\n        discount_id,
\n        GROUP_CONCAT(nonmember_discounts.title) as `Unit Discounts`,
\n        SUM(nonmember_discounts.amount) as `Unit Discount Amount`,
\n        GROUP_CONCAT(member_discounts.title) as `Member Discounts`,
\n        SUM(member_discounts.amount) as `Member Discount Amount`
\n            FROM plugin_ib_educate_bookings_discounts booking_discounts
\n         		left join plugin_bookings_discounts member_discounts
\n 				  ON member_discounts.id = booking_discounts.discount_id
\n                AND member_discounts.member_only = 1
\n      		left join plugin_bookings_discounts nonmember_discounts
\n  				ON nonmember_discounts.id = booking_discounts.discount_id
\n                 AND nonmember_discounts.member_only = 0
\n 	 GROUP BY booking_discounts.booking_id
\n  ) as applied_booking_discounts ON bookings.booking_id = applied_booking_discounts.booking_id
\n  inner join plugin_ib_educate_bookings_has_delegates has_delegates on transactions.booking_id = has_delegates.booking_id
\n  left join plugin_bookings_transactions_payments payments ON payments.transaction_id = transactions.id
\n  left join plugin_contacts3_relations rels on contacts.id = rels.child_id
\n  left join plugin_contacts3_contacts orgs on rels.parent_id = orgs.id
\n where
\n  transactions.deleted = 0
\ngroup by
\n  transactions.id
\norder by
\n  transactions.updated desc' 
WHERE (`name`='Transactions');


