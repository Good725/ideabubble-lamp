/*
ts:2020-03-06 11:00:00
*/

-- Update to include "grade" column when there is only one result
UPDATE
 `plugin_reports_reports`
SET
  `date_modified` = CURRENT_TIMESTAMP,
  `sql`  = 'select
\n   bookings.booking_id as `Booking Id`,
\n   bookings.contact_id as `Student Id`,
\n   CONCAT_WS('' '', students.first_name, students.last_name) as `Student`,
\n   CONCAT_WS('' '', organisations.first_name, organisations.last_name) as `Organisation`,
\n   courses.title as `Course`,
\n   schedules.`name` as `Schedule`,
\n   sum(if(find_in_set(''Absent'', items.timeslot_status), 1 ,0)) as `Absent Count`,
\n   sum(if(find_in_set(''Absent'', items.timeslot_status), 0, 1)) as `Present Count`,
\n   count_results as `Number of results`,
\n   avg_result as `AVG Result`,
\n   results.grade as `Grade`
\n from plugin_ib_educate_bookings bookings
\n   inner join plugin_ib_educate_booking_items items on bookings.booking_id = items.booking_id
\n   inner join plugin_courses_schedules_events timeslots on items.period_id = timeslots.id
\n   inner join plugin_courses_schedules schedules on timeslots.schedule_id = schedules.id
\n   inner join plugin_courses_courses courses on schedules.course_id = courses.id
\n   inner join plugin_contacts3_contacts students on bookings.contact_id = students.id
\n   left join plugin_contacts3_relations rels on students.id = rels.child_id
\n   left join plugin_contacts3_contacts organisations on rels.parent_id = organisations.id
\n   left join 
\n     (
\n       select
\n           results.schedule_id, results.student_id, avg(results.result) as avg_result,
\n           count(results.result) as count_results,
\n           if(count(todos.id) = 1, grades.grade, null) as grade
\n         from plugin_todos_todos2 todos
\n           inner join plugin_todos_todos2_has_results results on todos.id = results.todo_id
\n           left join plugin_todos_grading_schema_points points
\n             on todos.grading_schema_id = points.schema_id
\n             and points.level_id = results.level_id
\n           inner join plugin_todos_grades grades
\n             on points.grade_id = grades.id
\n             and grades.percent_min <= results.result
\n             and grades.percent_max >= results.result
\n             and points.subject_id in (results.subject_id, '''', 0)
\n             and grades.deleted = 0
\n             where todos.deleted = 0
\n         group by results.schedule_id, results.student_id
\n     ) results on results.schedule_id = timeslots.schedule_id and results.student_id = students.id
\n where bookings.`delete` = 0 and items.`delete` = 0 and bookings.booking_status <> 3 and timeslots.`delete` = 0
\n   and items.timeslot_status is not null
\n   and (course_id = ''{!Course!}'' or '''' = ''{!Course!}'')
\n group by bookings.booking_id
\n having `Absent Count` > 0 or `Present Count` > 0 or `AVG Result` is not null
'
WHERE `name`='Attendance & Grade';
