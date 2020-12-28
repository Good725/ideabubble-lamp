-- Cash Flow Report
SELECT
	(SELECT
		SUM(
			plugin_bookings_transactions_payments.amount
		)
		FROM
			plugin_bookings_transactions
		INNER JOIN plugin_bookings_transactions_payments ON plugin_bookings_transactions.id = plugin_bookings_transactions_payments.transaction_id
		INNER JOIN plugin_bookings_transactions_payments_statuses ON plugin_bookings_transactions_payments.`status` = plugin_bookings_transactions_payments_statuses.id
		INNER JOIN plugin_contacts3_contacts ON plugin_bookings_transactions.contact_id = plugin_contacts3_contacts.id
		INNER JOIN plugin_contacts3_contact_type ON plugin_contacts3_contacts.type = plugin_contacts3_contact_type.contact_type_id
		WHERE
			plugin_contacts3_contact_type.contact_type_id = 1
		AND credit = 1
		AND plugin_bookings_transactions_payments.type != 'transfer'
	)AS 'Paid by students',
	(SELECT
		SUM(
			plugin_bookings_transactions_payments.amount
		)
		FROM
			plugin_bookings_transactions
		INNER JOIN plugin_bookings_transactions_payments ON plugin_bookings_transactions.id = plugin_bookings_transactions_payments.transaction_id
		INNER JOIN plugin_bookings_transactions_payments_statuses ON plugin_bookings_transactions_payments.`status` = plugin_bookings_transactions_payments_statuses.id
		INNER JOIN plugin_contacts3_contacts ON plugin_bookings_transactions.contact_id = plugin_contacts3_contacts.id
		INNER JOIN plugin_contacts3_contact_type ON plugin_contacts3_contacts.type = plugin_contacts3_contact_type.contact_type_id
		WHERE
			plugin_contacts3_contact_type.contact_type_id = 1
		AND credit = 0
		AND plugin_bookings_transactions_payments.type != 'transfer'
	)AS 'Returned to students',
	(SELECT
		SUM(
			plugin_bookings_transactions_payments.amount
		)
		FROM
			plugin_bookings_transactions
		INNER JOIN plugin_bookings_transactions_payments ON plugin_bookings_transactions.id = plugin_bookings_transactions_payments.transaction_id
		INNER JOIN plugin_bookings_transactions_payments_statuses ON plugin_bookings_transactions_payments.`status` = plugin_bookings_transactions_payments_statuses.id
		INNER JOIN plugin_contacts3_contacts ON plugin_bookings_transactions.contact_id = plugin_contacts3_contacts.id
		INNER JOIN plugin_contacts3_contact_type ON plugin_contacts3_contacts.type = plugin_contacts3_contact_type.contact_type_id
		WHERE
			plugin_contacts3_contact_type.contact_type_id = 4
		AND credit = 1
		AND plugin_bookings_transactions_payments.type != 'transfer'
	)AS 'Paid by Billing',
	(SELECT
		SUM(
			plugin_bookings_transactions_payments.amount
		)
		FROM
			plugin_bookings_transactions
		INNER JOIN plugin_bookings_transactions_payments ON plugin_bookings_transactions.id = plugin_bookings_transactions_payments.transaction_id
		INNER JOIN plugin_bookings_transactions_payments_statuses ON plugin_bookings_transactions_payments.`status` = plugin_bookings_transactions_payments_statuses.id
		INNER JOIN plugin_contacts3_contacts ON plugin_bookings_transactions.contact_id = plugin_contacts3_contacts.id
		INNER JOIN plugin_contacts3_contact_type ON plugin_contacts3_contacts.type = plugin_contacts3_contact_type.contact_type_id
		WHERE
			plugin_contacts3_contact_type.contact_type_id = 4
		AND credit = 0
		AND plugin_bookings_transactions_payments.type != 'transfer'
	)AS 'Returned to Billing',
(SELECT
		SUM(
			plugin_bookings_transactions_payments.amount
		)
		FROM
			plugin_bookings_transactions
		INNER JOIN plugin_bookings_transactions_payments ON plugin_bookings_transactions.id = plugin_bookings_transactions_payments.transaction_id
		INNER JOIN plugin_bookings_transactions_payments_statuses ON plugin_bookings_transactions_payments.`status` = plugin_bookings_transactions_payments_statuses.id
		INNER JOIN plugin_contacts3_contacts ON plugin_bookings_transactions.contact_id = plugin_contacts3_contacts.id
		INNER JOIN plugin_contacts3_contact_type ON plugin_contacts3_contacts.type = plugin_contacts3_contact_type.contact_type_id
		WHERE
			plugin_contacts3_contact_type.contact_type_id = 3
		AND credit = 1
		AND plugin_bookings_transactions_payments.type != 'transfer'
	)AS 'Paid by Teacher',
	(SELECT
		SUM(
			plugin_bookings_transactions_payments.amount
		)
		FROM
			plugin_bookings_transactions
		INNER JOIN plugin_bookings_transactions_payments ON plugin_bookings_transactions.id = plugin_bookings_transactions_payments.transaction_id
		INNER JOIN plugin_bookings_transactions_payments_statuses ON plugin_bookings_transactions_payments.`status` = plugin_bookings_transactions_payments_statuses.id
		INNER JOIN plugin_contacts3_contacts ON plugin_bookings_transactions.contact_id = plugin_contacts3_contacts.id
		INNER JOIN plugin_contacts3_contact_type ON plugin_contacts3_contacts.type = plugin_contacts3_contact_type.contact_type_id
		WHERE
			plugin_contacts3_contact_type.contact_type_id = 3
		AND credit = 0
		AND plugin_bookings_transactions_payments.type != 'transfer'
	)AS 'Returned to Teacher';

-- Top 10 Vacancies
SELECT
	plugin_courses_schedules.`name` AS `Schedule Name`,
	plugin_courses_courses.title AS `Course Title`,
	plugin_courses_subjects.`name` AS `Subject`,
	plugin_courses_locations.`name` AS `Location`,
	plugin_courses_schedules_events.datetime_start AS `Date Start`,
	plugin_courses_schedules_events.datetime_end AS `Date End`,
	plugin_courses_schedules_events.id AS `Schedule ID`,
	plugin_courses_schedules.max_capacity AS `Max Capacity`,
	(
		plugin_courses_schedules.max_capacity -(
			SELECT
				COUNT(attending)
			FROM
				plugin_ib_educate_booking_items
			WHERE
				booking_item_id = plugin_courses_schedules_events.id
			AND attending = 1
		)
	)AS 'Spaces'
FROM
	plugin_courses_courses
INNER JOIN plugin_courses_schedules ON plugin_courses_courses.id = plugin_courses_schedules.course_id
INNER JOIN plugin_courses_subjects ON plugin_courses_courses.subject_id = plugin_courses_subjects.id
INNER JOIN plugin_courses_schedules_events ON plugin_courses_schedules.id = plugin_courses_schedules_events.schedule_id
INNER JOIN plugin_courses_locations ON plugin_courses_schedules.location_id = plugin_courses_locations.id
WHERE
	plugin_courses_schedules_events.`delete` = 0
AND plugin_courses_schedules_events.datetime_start >= "{!From!}"
AND plugin_courses_schedules_events.datetime_end <= "{!To!}"
ORDER BY
	Spaces DESC
LIMIT 10

-- Pre Pay Dues
SELECT CONCAT(
		'Transaction #',
		t.id,
		' ',
		t4.type
	) AS `Transaction`,
	CONCAT(c1.`name`, ' ', c2.title) AS `Course`,
	CONCAT(
		'Next Class: ',
		DATE_FORMAT(
			c4.datetime_start,
			'%a %D %b - %H:%i'
		)
	) AS `Next Attending`,
	CONCAT(c3.first_name, ' ', c3.last_name) AS `Student`,
	CONCAT(
		c.title,
		' ',
		c.first_name,
		' ',
		c.last_name
	) AS `Payer`,
	(
		t.total - COALESCE(
			(
				SELECT
					SUM(t1.amount)
				FROM
					plugin_bookings_transactions_payments AS `t1`
				JOIN plugin_bookings_transactions_payments_statuses AS `t2` ON t1.`status` = t2.id
				WHERE
					t2.credit = 1
				AND t1.transaction_id = t.id
			),
			0
		)+ COALESCE(
			(
				SELECT
					SUM(t1.amount)
				FROM
					plugin_bookings_transactions_payments AS `t1`
				JOIN plugin_bookings_transactions_payments_statuses AS `t2` ON t1.`status` = t2.id
				WHERE
					t2.credit = 0
				AND t1.transaction_id = t.id
			),
			0
		)
	) AS `Balance`,
	DATE_FORMAT(
		t.payment_due_date,
		'%a %D %b '
	)AS `Due Date`
FROM plugin_bookings_transactions t INNER JOIN plugin_bookings_transactions_types t4 ON t4.id = t.type
	 INNER JOIN plugin_ib_educate_bookings b ON t.booking_id = b.booking_id
	 INNER JOIN plugin_bookings_transactions_has_schedule t5 ON t.id = t5.transaction_id
	 INNER JOIN plugin_contacts3_contacts c ON t.contact_id = c.id
	 INNER JOIN plugin_courses_schedules c1 ON t5.schedule_id = c1.id
	 INNER JOIN plugin_courses_schedules_events c4 ON c1.id = c4.id
	 INNER JOIN plugin_ib_educate_booking_items b1 ON c4.id = b1.period_id
	 INNER JOIN plugin_courses_courses c2 ON c1.course_id = c2.id
	 INNER JOIN plugin_contacts3_contacts c3 ON c3.id = b.contact_id
WHERE t4.type = 'Booking - Pay Now'
AND b1.attending = 1
AND c4.datetime_start >= CURDATE()
HAVING Balance > 0

-- Free Rooms
SELECT
	plugin_courses_locations.`name`,
	plugin_courses_locations.parent_id,
	plugin_courses_locations.capacity
FROM
	plugin_courses_locations
WHERE
	plugin_courses_locations.`delete` = 0
AND id NOT IN(
	SELECT DISTINCT
		plugin_courses_schedules.location_id
	FROM
		plugin_courses_schedules
	INNER JOIN plugin_courses_schedules_events ON plugin_courses_schedules.id = plugin_courses_schedules_events.schedule_id
	WHERE
		plugin_courses_schedules_events.datetime_start = CURDATE()
)
AND NOT isnull(parent_id)
ORDER BY parent_id;

-- Attendee Vs Absentee
SELECT
	count(*)AS 'count',
	CASE
WHEN attending = 1 THEN
	'Present'
ELSE
	'Absent'
END AS 'Present'
FROM
	plugin_ib_educate_booking_items

JOIN plugin_contacts3_notes ON plugin_ib_educate_booking_items.booking_item_id = plugin_contacts3_notes.link_id
WHERE
	plugin_contacts3_notes.note NOT LIKE '%Booked after start date.%'
AND `delete` = 0
GROUP BY
	attending;

-- List the absentee
SELECT
	CONCAT(
		plugin_contacts3_contacts.first_name,
		' ',
		plugin_contacts3_contacts.last_name
	)AS student,
	plugin_contacts3_notes.note
FROM
	plugin_contacts3_notes
INNER JOIN plugin_contacts3_notes_tables ON plugin_contacts3_notes.table_link_id = plugin_contacts3_notes_tables.id
INNER JOIN plugin_ib_educate_booking_items ON plugin_ib_educate_booking_items.period_id = plugin_contacts3_notes.link_id
INNER JOIN plugin_ib_educate_bookings ON plugin_ib_educate_booking_items.booking_id = plugin_ib_educate_bookings.booking_id
INNER JOIN plugin_courses_schedules_events ON plugin_ib_educate_booking_items.period_id = plugin_courses_schedules_events.id
INNER JOIN plugin_contacts3_contacts ON plugin_ib_educate_bookings.contact_id = plugin_contacts3_contacts.id
WHERE
	plugin_ib_educate_booking_items.`delete` = 0
AND plugin_courses_schedules_events.datetime_start = CURDATE()
AND plugin_ib_educate_booking_items.attending = 0

-- Pending Bookings
SELECT DISTINCT CONCAT(plugin_contacts3_contacts.first_name, ' ' ,plugin_contacts3_contacts.last_name) AS student,
	CONCAT('Booking #',plugin_ib_educate_bookings.booking_id, ': ', plugin_courses_schedules.`name`, ' - ',plugin_courses_courses.title) AS booking
FROM plugin_ib_educate_bookings INNER JOIN plugin_ib_educate_bookings_status ON plugin_ib_educate_bookings.booking_status = plugin_ib_educate_bookings_status.status_id
	 INNER JOIN plugin_contacts3_contacts ON plugin_contacts3_contacts.id = plugin_ib_educate_bookings.contact_id
	 INNER JOIN plugin_courses_schedules ON plugin_ib_educate_bookings.schedule_id = plugin_courses_schedules.id
	 INNER JOIN plugin_courses_courses ON plugin_courses_schedules.course_id = plugin_courses_courses.id
	 INNER JOIN plugin_courses_schedules_events ON plugin_ib_educate_bookings.schedule_id = plugin_courses_schedules_events.schedule_id
WHERE plugin_ib_educate_bookings.booking_status = 1
AND plugin_courses_schedules_events.datetime_start = CURDATE();

-- Cancelled Bookings
SELECT DISTINCT CONCAT(plugin_contacts3_contacts.first_name, ' ' ,plugin_contacts3_contacts.last_name) AS student,
	CONCAT('Booking #',plugin_ib_educate_bookings.booking_id, ': ', plugin_courses_schedules.`name`, ' - ',plugin_courses_courses.title,' -',plugin_courses_schedules_events.id)
AS booking
FROM plugin_ib_educate_bookings INNER JOIN plugin_ib_educate_bookings_status ON plugin_ib_educate_bookings.booking_status = plugin_ib_educate_bookings_status.status_id
	 INNER JOIN plugin_contacts3_contacts ON plugin_contacts3_contacts.id = plugin_ib_educate_bookings.contact_id
	 INNER JOIN plugin_courses_schedules ON plugin_ib_educate_bookings.schedule_id = plugin_courses_schedules.id
	 INNER JOIN plugin_courses_courses ON plugin_courses_schedules.course_id = plugin_courses_courses.id
	 INNER JOIN plugin_courses_schedules_events ON plugin_ib_educate_bookings.schedule_id = plugin_courses_schedules_events.schedule_id
WHERE plugin_ib_educate_bookings.booking_status = 3
AND plugin_courses_schedules_events.datetime_start = CURDATE();

-- PAYG Dues on Day
-- List the students owing for a PAYG course on selected day
SELECT
	CONCAT(c.first_name, ' ', c.last_name)AS `Student`,
	t.amount AS `Class Fee`,
	CONCAT(
		'Next Class: ',
		DATE_FORMAT(
			c4.datetime_start,
			'%a %D %b - %H:%i'
		)
	)AS `Next Attending`,
	CONCAT(c1.`name`, ' ', c2.title)AS `Course`,
	CONCAT(
		'Transaction #',
		t.id,
		' ',
		t4.type
	)AS `Transaction`,
	(
		t.total - COALESCE(
			(
				SELECT
					SUM(t1.amount)
				FROM
					plugin_bookings_transactions_payments AS `t1`
				JOIN plugin_bookings_transactions_payments_statuses AS `t2` ON t1.`status` = t2.id
				WHERE
					t2.credit = 1
				AND t1.transaction_id = t.id
			),
			0
		)+ COALESCE(
			(
				SELECT
					SUM(t1.amount)
				FROM
					plugin_bookings_transactions_payments AS `t1`
				JOIN plugin_bookings_transactions_payments_statuses AS `t2` ON t1.`status` = t2.id
				WHERE
					t2.credit = 0
				AND t1.transaction_id = t.id
			),
			0
		)
	)AS `Total Due`
FROM
	plugin_bookings_transactions t
INNER JOIN plugin_bookings_transactions_types t4 ON t4.id = t.type
INNER JOIN plugin_ib_educate_bookings b ON t.booking_id = b.booking_id
INNER JOIN plugin_contacts3_contacts c ON t.contact_id = c.id
INNER JOIN plugin_bookings_transactions_has_schedule t5 ON t.id = t5.transaction_id
INNER JOIN plugin_courses_schedules c1 ON t5.schedule_id = c1.id
INNER JOIN plugin_courses_schedules_events c4 ON c1.id = c4.id
INNER JOIN plugin_courses_courses c2 ON c1.course_id = c2.id
INNER JOIN plugin_contacts3_contacts c3 ON c3.id = b.contact_id
WHERE
	t4.type = 'Booking - PAYG'
HAVING
	`Total Due` > 0;

-- Report to see how many people that did a course at Christmas have/have not rebooked for the easter course
-- Select
SELECT DISTINCT
	CONCAT(
		cs.first_name,
		' ',
		cs.last_name,
		' - ',
		c3.`year`
	)AS `Student`,
	c1.category AS `Category`
FROM
	plugin_courses_courses AS `c`
INNER JOIN plugin_courses_categories AS `c1`
	ON c.category_id = c1.id
INNER JOIN plugin_courses_schedules AS `c2`
	ON c2.course_id = c.id
INNER JOIN plugin_courses_schedules_events AS `c4`
	ON c2.id = c4.schedule_id
INNER JOIN plugin_ib_educate_bookings AS `b`
	ON c2.id = b.schedule_id
INNER JOIN plugin_contacts3_contacts AS `cs`
	ON b.contact_id = cs.id
INNER JOIN plugin_courses_years AS `c3`
	ON cs.year_id = c3.id
WHERE
	b.booking_status = 2
AND(c1.category = 'Grinds/Tutorials' OR c1.id = 'Revision');
-- Options Custom Parameter
SELECT category
FROM plugin_courses_categories
WHERE publish=1;

-- Count the bookings by categories
SELECT COUNT(*) AS `Students`,
	c3.category AS `Category`
FROM plugin_ib_educate_bookings AS `b`
INNER JOIN plugin_courses_schedules AS `c0` ON b.schedule_id = c0.id
	 INNER JOIN plugin_courses_courses AS `c2` ON c0.course_id = c2.id
	 INNER JOIN plugin_courses_categories AS `c3` ON c2.category_id = c3.id
	 INNER JOIN plugin_bookings_transactions AS `t0` ON b.booking_id = t0.booking_id
	 INNER JOIN plugin_bookings_transactions_types AS `t1` ON t0.type = t1.id
WHERE b.booking_status = 2
AND t1.type = 'Booking - Pay Now'
GROUP BY `category`;
--Select the Transaction type from drop down
SELECT type
FROM plugin_bookings_transactions_types
WHERE publish=1 AND credit=1;


-- Get All Payments
-- List All the payments and transfer made
SELECT
	CONCAT(
		'Payment#:',
		t.id,
		' ',
		t1.`status`
	)AS `Payment detail`,
	CONCAT(
		t.currency,
		' ',
		(t.amount + t.bank_fee)
	)AS `Amount`,
	CONCAT(
		'Transaction#',
		t2.id,
		' ',
		t3.type
	)AS `Transaction`,
	CONCAT(c2.first_name, ' ', c2.last_name)AS `Student`,
	CONCAT(
		c1.title,
		' ',
		c1.first_name,
		' ',
		c1.last_name
	)AS `Payer`
FROM
	plugin_bookings_transactions_payments AS `t`
INNER JOIN plugin_bookings_transactions_payments_statuses AS `t1` ON t.`status` = t1.id
INNER JOIN plugin_bookings_transactions AS `t2` ON t.transaction_id = t2.id
INNER JOIN plugin_bookings_transactions_types AS `t3` ON t2.type = t3.id
INNER JOIN plugin_contacts3_contacts AS `c1` ON t2.contact_id = c1.id
INNER JOIN plugin_ib_educate_bookings AS `b` ON t2.booking_id = b.booking_id
INNER JOIN plugin_contacts3_contacts AS `c2` ON b.contact_id = c2.id
WHERE
	t.created >= CURDATE()
ORDER BY
	t.created DESC;

-- Room-Allocation Board (RAB)
SELECT
	CONCAT(
		TIME_FORMAT(`event`.`datetime_start`, '%H:%i'),
		'-',
		TIME_FORMAT(`event`.`datetime_end`,   '%H:%i')
	) AS `Time`,
	DATE_FORMAT(`event`.`datetime_start`, '%W') AS `Day`,
	CONCAT('<a href="/admin/courses/edit_schedule/?id=', `schedule`.`id`, '">', `schedule`.`name`, '</a>') AS `Class`,
	CONCAT('<a href="/admin/courses/edit_location/?id=', `location`.`id`, '">', `location`.`name`, '</a>') AS `Room`,
	CONCAT('<a href="/admin/contacts2/edit/', `trainer`.`id`, '">', `trainer`.`first_name`, ' ', `trainer`.`last_name`, '</a>') AS `Trainer`
FROM `plugin_courses_schedules_events` `event`
JOIN `plugin_courses_schedules`        `schedule` ON `event`.   `schedule_id` = `schedule`.`id`
JOIN `plugin_courses_courses`          `course`   ON `schedule`.`course_id`   = `course`  .`id`
LEFT JOIN (
	SELECT *
	FROM `plugin_courses_locations`
	WHERE `delete` = 0
	AND `location_type_id` = 2
) `location`  ON `schedule`.`location_id` = `location`.`id`

LEFT JOIN(
	SELECT
		*
	FROM
		`plugin_contacts3_contacts`
JOIN plugin_contacts3_contact_type ON plugin_contacts3_contacts.type = plugin_contacts3_contact_type.contact_type_id
WHERE
plugin_contacts3_contact_type.label = 'Teacher'
	AND
		`delete` = 0
)`trainer` ON `schedule`.`trainer_id` = `trainer`.`id`
WHERE
	    `event`   .`delete`  = 0
	AND `schedule`.`delete`  = 0
	AND `course`  .`deleted` = 0
	AND `event`.`datetime_start` >= "{!From!}"
	AND `event`.`datetime_start` <  "{!To!}"
	AND `location`.`id` IN ({!LocationIDs!})
	AND `trainer`.`id` IN ({!TrainerIDs!})
ORDER BY `datetime_start` ASC;


-- See students booked in 1 Category not booking in another Category
SELECT DISTINCT
	CONCAT(
		cs.first_name,
		' ',
		cs.last_name,
		' - ',
		c3.`year`
	)AS `Student`,
	c1.category AS `Category`
FROM
	plugin_courses_courses AS `c`
INNER JOIN plugin_courses_categories AS `c1`
	ON c.category_id = c1.id
INNER JOIN plugin_courses_schedules AS `c2`
	ON c2.course_id = c.id
INNER JOIN plugin_courses_schedules_events AS `c4`
	ON c2.id = c4.schedule_id
INNER JOIN plugin_ib_educate_bookings AS `b`
	ON c2.id = b.schedule_id
INNER JOIN plugin_contacts3_contacts AS `cs`
	ON b.contact_id = cs.id
INNER JOIN plugin_courses_years AS `c3`
	ON cs.year_id = c3.id
WHERE
	b.booking_status = 2
AND
(
c1.category = "{!First!}"
OR c1.category = "{!Second!}"
);

-- Yearly Prepay Students
SELECT
CONCAT(c.first_name,' ',c.last_name) AS `Students`
FROM
	plugin_contacts3_contacts `c`
WHERE
	c.id IN(
		SELECT DISTINCT
			b0.contact_id
		FROM
			plugin_ib_educate_bookings `b0`
		HAVING
			(
				SELECT
					COUNT(*)
				FROM
					plugin_ib_educate_bookings `b`
				INNER JOIN plugin_ib_educate_booking_items `b1` ON b.booking_id = b1.booking_id
				WHERE
					b1.attending = 1
				AND b.contact_id = b0.contact_id
			)> 250
	)
AND c.id IN(
	SELECT DISTINCT
		b.contact_id
	FROM
		plugin_ib_educate_bookings `b`
	INNER JOIN plugin_bookings_transactions `t`
	ON t.booking_id = b.booking_id
	INNER JOIN plugin_bookings_transactions_types `t1`
	ON t1.id = t.type
	WHERE
		b.schedule_id IN(
			SELECT
				c03.id
			FROM
				plugin_courses_categories `c01`
			INNER JOIN plugin_courses_courses `c02` ON c01.id = c02.category_id
			INNER JOIN plugin_courses_schedules `c03` ON c02.id = c03.course_id
			WHERE
				c01.category = 'August Preparation Course '
		)
	AND t1.type = 'Booking - Pay Now'
);


-- Unused Rooms
SELECT
	CONCAT(r1.`name`, ' - ', r.`name`) AS `Room`,
	r.capacity AS `Max Capacity`
FROM
	plugin_courses_locations `r`
JOIN plugin_courses_locations `r1` ON r.parent_id = r1.id
WHERE
	r.`delete` = 0
AND r.id NOT IN(
	SELECT
		`location`.`id`
	FROM
		`plugin_courses_schedules_events` `event`
	JOIN `plugin_courses_schedules` `schedule` ON `event`.`schedule_id` = `schedule`.`id`
	JOIN `plugin_courses_courses` `course` ON `schedule`.`course_id` = `course`.`id`
	JOIN(
		SELECT
			*
		FROM
			`plugin_courses_locations`
		WHERE
			`delete` = 0
		AND `location_type_id` = 2
	)`location` ON `schedule`.`location_id` = `location`.`id`
	WHERE
		`event`.`delete` = 0
	AND `schedule`.`delete` = 0
	AND `course`.`deleted` = 0
	AND `event`.`datetime_start` >= "{!From!}"
	AND `event`.`datetime_start` <  "{!To!}"
);


-- Free Rooms
SELECT
	CONCAT(l1.`name`,' - ',l.`name`)
	 AS `Name`,
	l.capacity AS `Capacity`
FROM
	plugin_courses_locations `l`
	JOIN plugin_courses_locations `l1` ON l.parent_id = l1.id
WHERE
	l.`delete` = 0
AND l.id NOT IN(
	SELECT DISTINCT
		plugin_courses_schedules.location_id
	FROM
		plugin_courses_schedules
	INNER JOIN plugin_courses_schedules_events ON plugin_courses_schedules.id = plugin_courses_schedules_events.schedule_id
	WHERE
		plugin_courses_schedules_events.datetime_start = CURDATE()
)
ORDER BY Name;