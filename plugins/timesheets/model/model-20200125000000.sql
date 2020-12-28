/*
ts:2020-01-15 00:00:00
*/

INSERT INTO `plugin_reports_reports` (`name`, `report_type`) VALUES ('Staff Time', 'sql');
INSERT INTO `plugin_reports_parameters` (`report_id`, `type`, `name`) VALUES ((select id from plugin_reports_reports where `name`='Staff Time'), 'date', 'After');
INSERT INTO `plugin_reports_parameters` (`report_id`, `type`, `name`) VALUES ((select id from plugin_reports_reports where name='Staff Time'), 'date', 'Before');
INSERT INTO
  `plugin_reports_parameters` (`report_id`, `type`, `name`, `value`)
  VALUES
  ((select id from plugin_reports_reports where name='Staff Time'), 'custom', 'Department', 'select distinct departments.id as `depatment_id`, concat_ws(\' - \', organisations.first_name, departments.first_name) as `Department`\r\n	from plugin_contacts3_contacts organisations\r\n	inner join plugin_contacts3_relations ro on organisations.id = ro.parent_id\r\n	inner join plugin_contacts3_contacts departments on ro.child_id = departments.id\r\n	inner join plugin_contacts3_relations rd on departments.id = rd.parent_id\r\n	inner join plugin_contacts3_contacts staff on rd.child_id = staff.id\r\n	where organisations.`delete` = 0 and departments.`delete` = 0\r\n	order by `Department`');

DELIMITER ;;
-- Add category parameter
INSERT INTO
  `plugin_reports_parameters` (`report_id`, `type`, `name`, `value`)
VALUES
(
  (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'Staff Time'),
  'custom',
  'Category',
  'SELECT `id`, `category`\nFROM `plugin_courses_categories`\nWHERE `delete` = 0\nORDER BY `category`'
);;

-- Add task parameter
INSERT INTO
  `plugin_reports_parameters` (`report_id`, `type`, `name`, `value`)
VALUES
(
  (SELECT `id` FROM `plugin_reports_reports` WHERE `name` = 'Staff Time'),
  'custom',
  'Task',
  'SELECT `id`, `title`\nFROM `plugin_todos_todos2`\nWHERE `deleted` = 0\nORDER BY `title`'
);;


UPDATE `plugin_reports_reports`
  SET
    `php_post_filter` = '        $after = date(''Y-m-d'', strtotime($this->get_parameter(''After'')));
\n        $before = date(''Y-m-d'', strtotime(''+1 day'', strtotime($this->get_parameter(''Before'')))); /* +1 to ensure it is before and including this date */
\n        $department = $this->get_parameter(''Department'');
\n        $category = $this->get_parameter(''Category'');
\n        $task = $this->get_parameter(''Task'');
\n
\n        $y1 = (int)date(''Y'', strtotime($after));
\n        $y2 = (int)date(''Y'', strtotime($before));
\n        $years = array();
\n        for ($year = $y1 ; $year <= $y2 ; ++$year) {
\n            $years[] = $year;
\n        }
\n
\n        DB::query(null, "drop temporary table if exists tmp_staff_ids")->execute();
\n        DB::query(null, "create temporary table tmp_staff_ids (id int primary key)")->execute();
\n
\n        $timesheets_staff_id_query = ''select distinct staff_id from plugin_timesheets_requests r inner join plugin_contacts3_relations rd on r.staff_id = rd.child_id'';
\n        $timeoff_staff_id_query = ''select distinct staff_id from plugin_timeoff_requests r inner join plugin_contacts3_relations rd on r.staff_id = rd.child_id'';
\n
\n        if ($department) {
\n            $timesheets_staff_id_query .= '' and rd.parent_id = ''.$department;
\n            $timeoff_staff_id_query    .= '' and rd.parent_id = ''.$department;
\n        }
\n
\n        $category_where = (!empty($category)) ? '' AND `course`.`category_id` = ''.$category : '''';
\n        $task_where     = (!empty($task))     ? '' AND `r`.`todo_id` = ''.$task : '''';
\n
\n        DB::query(null, "insert ignore into tmp_staff_ids ($timesheets_staff_id_query)")->execute();
\n        DB::query(null, "insert ignore into tmp_staff_ids ($timeoff_staff_id_query)")->execute();
\n
\n        $select = "select
\n            staffs.id as `Staff Id`, concat_ws('' '', staffs.first_name, staffs.last_name) as `Staff`,
\n            days_at_work.days_at_work as `Days at Work`,
\n            days_annual.days_annual as `Annual Leave`,
\n            days_sick.days_sick as `Sick Leave / Bereavement Leave`,
\n            '''' as `Total`,";
\n
\n        foreach ($years as $year) {
\n            $select .= "year_" . $year . ".hours as `" . $year . " Hours`,";
\n        }
\n
\n        $select .= "'''' as `Total Hours`";
\n        $select .= " from tmp_staff_ids
\n          inner join plugin_contacts3_contacts as staffs on tmp_staff_ids.id = staffs.id
\n    left join
\n    (
\n        SELECT
\n          `r`.`staff_id`, SUM(DATEDIFF(`r`.`period_end_date`, `r`.`period_start_date`) + 1) AS `days_at_work`
\n        FROM `plugin_timesheets_requests` `r`
\n        INNER JOIN `plugin_timesheets_timesheets` `s`    ON `r`.`timesheet_id`     = `s`.`id`
\n        LEFT JOIN  `plugin_courses_schedules` `schedule` ON `r`.`schedule_id`      = `schedule`.`id`
\n        LEFT JOIN  `plugin_courses_courses`   `course`   ON `schedule`.`course_id` = `course`.`id`
\n        WHERE `r`.`period_start_date` >= ''$after''
\n        AND   `r`.`period_start_date` < ''$before''
\n        AND   `status` = ''approved''
\n        ".$category_where."
\n        ".$task_where."
\n        GROUP BY `r`.`staff_id`
\n      )
\n      as days_at_work on tmp_staff_ids.id = days_at_work.staff_id
\n    left join
\n    (
\n        SELECT
\n          `r`.`staff_id`, SUM(DATEDIFF(`r`.`period_end_date`, `r`.`period_start_date`) + 1) AS `days_annual`
\n        FROM `plugin_timeoff_requests` `r`
\n        WHERE `r`.`type` = ''annual''
\n        AND   `r`.`period_start_date` >= ''$after''
\n        AND   `r`.`period_start_date` < ''$before''
\n        AND   `status` = ''approved''
\n        GROUP BY `r`.`staff_id`
\n      )
\n     as days_annual on tmp_staff_ids.id = days_annual.staff_id
\n     left join
\n    (
\n        SELECT
\n          `r`.`staff_id`, SUM(DATEDIFF(`r`.`period_end_date`, `r`.`period_start_date`) + 1) AS `days_sick`
\n        FROM `plugin_timeoff_requests` `r`
\n        WHERE (`r`.`type` = ''bereavement'' OR `r`.`type` = ''sick'')
\n        AND `r`.`period_start_date` >= ''$after''
\n        AND `r`.`period_start_date` < ''$before''
\n        AND `status` = ''approved''
\n        GROUP BY `r`.`staff_id`
\n    )
\n    as days_sick on tmp_staff_ids.id = days_sick.staff_id
\n
\n        ";
\n
\n        foreach ($years as $year) {
\n            $year = (int)$year;
\n            $next_year = $year + 1;
\n            $select .= " left join
\n            (
\n                SELECT
\n                    r.staff_id, round(SUM(r.duration * (DATEDIFF(r.period_end_date, r.period_start_date) + 1)) / 60) as hours, DATE_FORMAT(r.period_start_date, ''%Y'') as year
\n                FROM plugin_timesheets_requests r
\n                INNER JOIN plugin_timesheets_timesheets s on r.timesheet_id = s.id
\n                LEFT JOIN  `plugin_courses_schedules` `schedule` ON `r`.`schedule_id`  = `schedule`.`id`
\n                LEFT JOIN  `plugin_courses_courses`   `course`   ON `schedule`.`course_id` = `course`.`id`
\n                WHERE r.period_start_date >= ''$year-01-01''
\n                AND r.period_start_date < ''$next_year-01-01''
\n                AND r.period_start_date >= ''$after''
\n                AND r.period_start_date < ''$before''
\n                AND status=''approved''
\n                ".$category_where."
\n                ".$task_where."
\n                GROUP BY r.staff_id, year
\n            ) as year_$year on tmp_staff_ids.id = year_$year.staff_id ";
\n        }
\n
\n        $result = DB::query(Database::SELECT, $select)->execute()->as_array();
\n        foreach ($result as $i => $row) {
\n            $result[$i][''Total''] = (int)$row[''Days at Work''] + (int)$row[''Annual Leave''] + (int)$row[''Sick Leave / Bereavement Leave''];
\n            $result[$i][''Total Hours''] = 0;
\n
\n            foreach ($years as $year) {
\n                //$result[$i][$year[''year''] . '' Hours''] = (int)$row[$year[''year''] . '' Hours''];
\n                $result[$i][''Total Hours''] += (int)$row[$year . '' Hours''];
\n            }
\n
\n            // Only show people with logged time/time off
\n            $has_time = ($result[$i][''Total''] != 0 || $result[$i][''Total Hours''] != 0);
\n
\n            // If a task is selected, the user must have logged time to the task
\n            $has_time_logged_to_task = (!$task || $result[$i][''Days at Work''] > 0);
\n
\n            // If a category is selected, the user must have logged time to the category
\n            $has_time_logged_to_category = (!$category || $result[$i][''Days at Work''] > 0);
\n
\n            if (!$has_time || !$has_time_logged_to_task || !$has_time_logged_to_category) {
\n                unset($result[$i]);
\n            }
\n        }
\n$data = $result;'
  WHERE (name='Staff Time');;

