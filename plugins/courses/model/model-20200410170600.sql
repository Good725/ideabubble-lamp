/*
ts:2020-04-10  17:06:00
*/
UPDATE

    `plugin_reports_reports`

SET

    `sql` = 'select
\n     bookings.booking_id as `Booking ID`,
\n     courses.title as `Course`,
\n     schedules.`name` as `Schedule`,
\n     outstandings.organisation_name as `Organisation`,
\n     date_format(schedules.start_date, \'%d-%M-%Y\') as `Starts`,
\n     outstandings.transaction_id as `Transaction ID`,
\n     outstandings.total as `Total`
\n     from plugin_ib_educate_bookings bookings
\n         inner join plugin_ib_educate_booking_items items on bookings.booking_id = items.booking_id
\n         inner join plugin_courses_schedules_events timeslots on items.period_id = timeslots.id
\n         inner join plugin_courses_schedules schedules on timeslots.schedule_id = schedules.id
\n         inner join plugin_courses_courses courses on schedules.course_id = courses.id
\n         inner join plugin_contacts3_contacts contacts on bookings.contact_id = contacts.id
\n         left join
\n            (select
\n                transactions.booking_id,
\n                transactions.contact_id,
\n                transactions.id as transaction_id,
\n                transactions.total as total,
\n                sum(payments.amount) as paid,
\n                transactions.total - ifnull(sum(payments.amount), 0) as outstanding,
\n                organisations.first_name,
\n                contact_type.name,
\n                if (contact_type.name = \'organisation\', payers.first_name, organisations.first_name) as organisation_name
\n                   from plugin_bookings_transactions transactions
\n                     inner join plugin_bookings_transactions_types ttypes on transactions.type = ttypes.id
\n                        left join plugin_bookings_transactions_payments payments
\n                            on transactions.id = payments.transaction_id
\n                            and payments.deleted = 0
\n                            and payments.status = 2
\n                        left join plugin_contacts3_contacts payers on transactions.contact_id = payers.id
\n                        left join plugin_contacts3_contact_type contact_type on payers.`type` = contact_type.contact_type_id
\n                        left join plugin_contacts3_relations relations on payers.id = relations.child_id
\n                        left join plugin_contacts3_contacts organisations on relations.parent_id = organisations.id
\n
\n                   where transactions.deleted = 0
\n                       and ttypes.credit = 1
\n                   group by transactions.id
\n             ) outstandings on bookings.booking_id = outstandings.booking_id
\n         left join plugin_contacts3_contacts payers on outstandings.contact_id = payers.id
\n             where bookings.`delete` = 0 and items.`delete` = 0 and timeslots.`delete` = 0
\n             and (schedules.start_date < date_add(\'{!Before!}\', interval 1 day) or \'\' = \'{!Before!}\') and (schedules.start_date >= \'{!After!}\' or \'\' = \'{!After!}\') and (course_id = \'{!Course!}\' or \'\' = \'{!Course!}\')
\n             and (courses.category_id = \'{!Category!}\' or \'\' = \'{!Category!}\')
\n         group by schedules.id, `Transaction ID`, `Organisation`, bookings.booking_id
\n         order by schedules.start_date desc'
where `name` = 'Deferred Revenue' or `name` LIKE '%Deferred Revenue%';