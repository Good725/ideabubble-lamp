/*
ts:2020-04-08 13:11:00
*/
UPDATE
    `plugin_reports_reports`
SET
    `sql` = 'select
\n        bookings.booking_id as `Booking Id`,
\n        bookings.contact_id as `Student Id`,
\n        CONCAT_WS(\' \', students.first_name, students.last_name) as `Student`,
\n        ifnull(bookings.bill_payer, bookings.contact_id) as `Payer Id`,
\n        CONCAT_WS(\' \', ifnull(payers.first_name, students.first_name), ifnull(payers.last_name,students.last_name)) as `Payer`,
\n        courses.title as `Course`,
\n        schedules.`name` as `Schedule`,
\n        bookings.created_date as `Booked Date`,
\n        min(timeslots.datetime_start) as `Start Date`,
\n        booking_status.title as `Status`,
\n        outstandings.outstanding as `Tx Outstanding`,
\n        outstandings.transaction_id as `Tx ID`,
\n        outstandings.transaction_type as `Tx Type`,
\n        outstandings.total as `Tx Total`,
\n        outstandings.transaction_payment_status as `Tx Status`,
\n        outstandings.transaction_updated as `Tx Updated`
\n            from plugin_ib_educate_bookings bookings
\n                inner join plugin_ib_educate_bookings_status booking_status on bookings.booking_status = booking_status.status_id
\n                inner join plugin_ib_educate_booking_items items on bookings.booking_id = items.booking_id
\n                inner join plugin_courses_schedules_events timeslots on items.period_id = timeslots.id
\n                inner join plugin_courses_schedules schedules on timeslots.schedule_id = schedules.id
\n                inner join plugin_courses_courses courses on schedules.course_id = courses.id
\n                inner join plugin_contacts3_contacts students on bookings.contact_id = students.id
\n                left join plugin_contacts3_contacts payers on bookings.bill_payer = payers.id
\n                inner join
\n                     (select
\n                         transactions.booking_id,
\n                         transactions.id as transaction_id,
\n                         ttypes.type as transaction_type,
\n                         payment_statuses.status as transaction_payment_status,
\n                         transactions.updated as transaction_updated,
\n                         transactions.total as total,
\n                         sum(payments.amount) as paid,
\n                         transactions.total - ifnull(sum(payments.amount), 0) as outstanding
\n                            from plugin_bookings_transactions transactions
\n                            inner join plugin_bookings_transactions_types ttypes on transactions.type = ttypes.id
\n                            left join plugin_bookings_transactions_payments payments on transactions.id = payments.transaction_id and payments.deleted = 0  and payments.`status` = 2
\n                            left join plugin_bookings_transactions_payments_statuses payment_statuses on payments.`status` = payment_statuses.id
\n                              where transactions.deleted = 0 and ttypes.credit = 1
\n                              group by transactions.id
\n                              having outstanding > 0
\n                      ) outstandings on bookings.booking_id = outstandings.booking_id
\n
\n              where bookings.`delete` = 0 and items.`delete` = 0 and timeslots.`delete` = 0
\n              and (timeslots.datetime_start < date_add(\'{!Before!}\', interval 1 day) or \'\' = \'{!Before!}\')  and (course_id = \'{!Course!}\' or \'\' = \'{!Course!}\')  and  (outstandings.outstanding >= \'{!Minimum Outstanding!}\' or \'\' = \'{!Minimum Outstanding!}\')
\n               group by bookings.booking_id'
WHERE
        `name` = 'Bookings ALL' or `name` = 'Bookings All' or `name` = 'BOOKINGS ALL';