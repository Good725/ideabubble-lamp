/*
ts:2020-07-28 17:36:00
*/

INSERT IGNORE INTO `plugin_reports_reports` (`name`, `report_type`, `publish`, `delete`)
VALUES ('Bookings revenue', 'sql', '1', '0');

INSERT INTO `plugin_reports_parameters` (`report_id`, `type`, `name`, `value`) VALUES ((select id from plugin_reports_reports where name='Bookings revenue' and `delete`=0 limit 1), 'custom', 'Category', 'select id, category from plugin_courses_categories where `delete`=0 order by category');
INSERT INTO `plugin_reports_parameters` (`report_id`, `type`, `name`, `value`) VALUES ((select id from plugin_reports_reports where name='Bookings revenue' and `delete`=0 limit 1), 'custom', 'Course', 'select id, CONCAT_WS(\' - \' , `code`, title) as course from plugin_courses_courses where deleted=0 order by code');
INSERT INTO `plugin_reports_parameters` (`report_id`, `type`, `name`) VALUES ((select id from plugin_reports_reports where name='Bookings revenue' and `delete`=0 limit 1), 'date', 'After');
INSERT INTO `plugin_reports_parameters` (`report_id`, `type`, `name`) VALUES ((select id from plugin_reports_reports where name='Bookings revenue' and `delete`=0 limit 1), 'date', 'Before');

UPDATE
    `plugin_reports_reports`
SET
    `date_modified` = CURRENT_TIMESTAMP,
    `sql` = '\n
\nselect
\n     `bookings`.`booking_id` as `Booking ID`,
\n     bookings.created_date as `Booking Date`,
\n     min(schedules.start_date) as `Course Start Date`,
\n     courses.title as `Course Title`,
\n     outstandings.organisation_name as `Company Name`,
\n     IF(COUNT(DISTINCT delegates.id) > 0, group_concat(DISTINCT CONCAT_WS(\' \', delegates.first_name, delegates.last_name)), \'\') as `Delegates Names`,
\n     COUNT(DISTINCT delegates.id) as `Number of Delegates`,
\n     synced.remote_id as `AIQ A/C No`,
\n     outstandings.transaction_id as `Transaction ID`,
\n     bookings.payment_method as `Payment Method`,
\n     categories.category as `Vertical Type`,
\n     outstandings.total as `Price/Income`
\n     from plugin_ib_educate_bookings bookings
\n         inner join plugin_ib_educate_booking_items items on bookings.booking_id = items.booking_id
\n         left join plugin_ib_educate_bookings_has_delegates has_delegates on has_delegates.booking_id = bookings.booking_id
\n         left join plugin_contacts3_contacts delegates on has_delegates.contact_id = delegates.id
\n         inner join plugin_courses_schedules_events timeslots on items.period_id = timeslots.id
\n         inner join plugin_courses_schedules schedules on timeslots.schedule_id = schedules.id
\n         inner join plugin_courses_courses courses on schedules.course_id = courses.id
\n         left join plugin_courses_types course_types on courses.type_id = course_types.id
\n         inner join plugin_contacts3_contacts contacts on bookings.contact_id = contacts.id
\n         left join
\n            (select
\n                transactions.booking_id,
\n               transactions.contact_id,
\n                transactions.id as transaction_id,
\n                transactions.total as total,
\n               sum(payments.amount) as paid,
\n               transactions.total - ifnull(sum(payments.amount), 0) as outstanding,
\n               organisations.first_name,
\n               contact_type.name,
\n               if (contact_type.name = \'organisation\', payers.first_name, organisations.first_name) as organisation_name
\n                   from plugin_bookings_transactions transactions
\n                      inner join plugin_bookings_transactions_types ttypes on transactions.type = ttypes.id
\n                        left join plugin_bookings_transactions_payments payments
\n                            on transactions.id = payments.transaction_id
\n                            and payments.deleted = 0
\n                            and payments.status = 2
\n                        left join plugin_contacts3_contacts payers on transactions.contact_id = payers.id
\n                        left join plugin_contacts3_contact_type contact_type on payers.`type` = contact_type.contact_type_id
\n                        left join plugin_contacts3_relations relations on payers.id = relations.child_id
\n                        left join plugin_contacts3_contacts organisations on relations.parent_id = organisations.id
\n
\n                      where transactions.deleted = 0
\n                         and ttypes.credit = 1
\n                      group by transactions.id
\n             ) outstandings on bookings.booking_id = outstandings.booking_id
\n         left join plugin_contacts3_contacts payers on outstandings.contact_id = payers.id
\n         left join engine_remote_sync synced on contacts.id = synced.cms_id AND synced.`type` = \'AccountsIQ-Contact\'
\n         left join plugin_courses_categories categories on courses.category_id = categories.id
\n             where bookings.`delete` = 0
\n             and items.`delete` = 0
\n             and timeslots.`delete` = 0
\n             and (bookings.created_date < date_add(\'{!Before!}\', interval 1 day) or \'\' = \'{!Before!}\') and (bookings.created_date >= \'{!After!}\' or \'\' = \'{!After!}\') and (course_id = \'{!Course!}\' or \'\' = \'{!Course!}\')
\n             and (courses.category_id = \'{!Category!}\' or \'\' = \'{!Category!}\')
\n         group by schedules.id, `Transaction ID`, `Company Name`, `bookings`.`booking_id`
\n         order by min(schedules.start_date) desc'
WHERE
        `name` = 'Bookings revenue';