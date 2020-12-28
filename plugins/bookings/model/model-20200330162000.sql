/*
ts:2020-03-03 16:20:00
*/

DELIMITER ;;
UPDATE
  `plugin_reports_reports`
SET
  `sql`='SELECT
\n    st.id as `Student ID`,
\n    CONCAT(\'<a href=\"/admin/contacts3?contact=\', st.id, \'\" target=\"_blank\">\', `st`.`first_name`, \' \', `st`.`last_name`, \'</a>\')  AS `Student`,
\n    re.town AS `Town`,
\n    bk.booking_id AS `Booking ID`,
\n    IF(st.is_flexi_student, \'Yes\', \'No\') AS `Flexi`,
\n    cg.category AS `Course Category`,
\n    co.title AS `Course Title`,
\n    sc.`name` AS `Schedule Title`,
\n    DATE_FORMAT(\'{!date!}\', \'%d/%m/%Y\') AS `Date`
\n        __PHP1__
/*\n    \'          \' AS `Student Signature`*/
\nFROM plugin_courses_schedules sc
\n    INNER JOIN plugin_courses_courses co ON sc.course_id = co.id
\n    INNER JOIN plugin_courses_categories cg ON co.category_id = cg.id
\n    INNER JOIN plugin_ib_educate_booking_has_schedules bs ON sc.id = bs.schedule_id
\n    INNER JOIN plugin_ib_educate_bookings bk ON bs.booking_id = bk.booking_id AND bk.`delete` = 0 AND bk.booking_status <> 3
\n    LEFT JOIN `plugin_ib_educate_bookings_has_delegates` `has_delegate` ON `has_delegate`.`booking_id` = `bk`.`booking_id`
\n    INNER JOIN plugin_contacts3_contacts st ON (
\n        \/\* No delegates => show the main booker \*\/
\n        (`bk`.`contact_id` = `st`.`id` AND has_delegate.contact_id IS NULL)
\n        OR
\n        \/\* Has delegates => show delegates \*\/
\n        (`has_delegate`.`contact_id` = `st`.`id`)
\n    )
\n    INNER JOIN plugin_contacts3_residences re ON st.residence = re.address_id
\n        
\n    __PHP2__
\n    
\n  WHERE sc.id = \'{!schedule_id!}\' __PHP3__
\n  GROUP BY `Booking ID`, `Date`, `sc`.`id`, `st`.`id`
\n  ORDER BY `Student`'
WHERE
  `name` = 'Roll Call Attendance';;


UPDATE
  `plugin_reports_reports`
SET
  `php_modifier`='$date = $this->get_parameter(\'date\');
\n$scheduleId = $this->get_parameter(\'schedule_id\');
\nif (!is_numeric($scheduleId)){
\n  $scheduleId = 0;
\n}
\n$trainerId = $this->get_parameter(\'trainer_id\');
\n$timeslotId = $this->get_parameter(\'timeslot_id\');
\n$schedule = Model_Schedules::get_schedule($scheduleId);
\n
\n$eventsQuery = DB::select(\'e.*\')->from(array(\'plugin_courses_schedules_events\', \'e\'))
\n  ->join(array(\'plugin_courses_schedules\', \'s\'), \'inner\')->on(\'e.schedule_id\', \'=\', \'s.id\')
\n  ->where(\'e.schedule_id\', \'=\', $scheduleId)
\n  ->and_where(\'e.delete\', \'=\', 0);
\n
\nif($timeslotId){
\n$eventsQuery->and_where(\'e.id\', \'=\', $timeslotId);
\n}
\n
\nif ($schedule){
\n  if (@$schedule[\'payg_period\'] == \'week\') {
\n    $weekstart = date(\'Y-m-d\', strtotime(\"monday this week\", strtotime($date)));
\n    $eventsQuery->and_where(\'e.datetime_start\', \'>=\', $weekstart);
\n    $eventsQuery->and_where(\'e.datetime_start\', \'<\', DB::expr(\'date_add(\"\' . $weekstart . \'\", interval 1 week)\'));
\n  } else {
\n    $eventsQuery->and_where(\'e.datetime_start\', \'>=\', $date);
\n    $eventsQuery->and_where(\'e.datetime_start\', \'<\', DB::expr(\'date_add(\"\' . $date . \'\", interval 1 day)\'));
\n  }
\n}
\n
\nif (is_numeric($trainerId)) {
\n  $eventsQuery->and_where(DB::expr(\'if(e.trainer_id > 0, e.trainer_id = \' . $trainerId . \', s.trainer_id = \' . $trainerId . \')\'), \'>\', 0);
\n}
\n$events = $eventsQuery->order_by(\'e.datetime_start\', \'asc\')
\n  ->execute()
\n  ->as_array();
\n
\nDB::query(null, \'DROP TEMPORARY TABLE IF EXISTS tmp_account_stat\')->execute();
\nDB::query(null, \'DROP TEMPORARY TABLE IF EXISTS tmp_tx_stat\')->execute();
\nDB::query(null, \'CREATE TEMPORARY TABLE tmp_tx_stat
\n(SELECT 
\n    tx.id,
\n    tx.booking_id,
\n    tx.total,
\n    tt.credit,
\n    SUM(IFNULL(py.amount,0)) AS pyt,
\n    SUM(IFNULL(IF(ps.credit > 0, py.amount, -py.amount),0)) AS pyx,
\n    tx.total - SUM(IFNULL(IF(ps.credit > 0, py.amount, -py.amount),0)) AS outstanding
\n  FROM plugin_bookings_transactions tx
\n    INNER JOIN plugin_bookings_transactions_has_schedule has ON tx.id = has.transaction_id AND has.schedule_id = \' . $scheduleId . \'
\n    LEFT JOIN plugin_bookings_transactions_types tt ON tx.type = tt.id
\n    LEFT JOIN plugin_bookings_transactions_payments py ON tx.id = py.transaction_id AND py.deleted = 0
\n    LEFT JOIN plugin_bookings_transactions_payments_statuses ps ON py.`status` = ps.id
\n  GROUP BY tx.id
\n  HAVING outstanding <> 0)\')->execute();
\nDB::query(null, \'ALTER TABLE tmp_tx_stat ADD KEY (`booking_id`)\')->execute();
\nDB::query(null, \'DROP TEMPORARY TABLE IF EXISTS tmp_account_stat\')->execute();
\nDB::query(null, \'CREATE TEMPORARY TABLE tmp_account_stat
\n(SELECT 
\n    bk.booking_id, 
\n    SUM(IFNULL(IF(tx.credit > 0, tx.outstanding, -tx.outstanding), 0)) AS outstanding
\n  FROM plugin_ib_educate_bookings bk
\n    LEFT JOIN tmp_tx_stat tx ON bk.booking_id = tx.booking_id
\n  GROUP BY bk.booking_id)\')->execute();
\nDB::query(null, \'ALTER TABLE tmp_account_stat ADD KEY (`booking_id`)\')->execute();
\n
\n$replace1 = \'\';
\n$replace2 = \'\';
\n$replace3 = array();
\nif(count($events) == 0){
\n    $replace3 = \' and 0 \';
\n} else {
\n    $event_count = count($events);
\n    foreach($events as $i => $event){
\n        $event_time = strtotime($event[\'datetime_start\']);
\n    $event_end_time = strtotime($event[\'datetime_end\']);
\n        if(date(\'H\', $event_time) == 0){
\n            $colname = date(\'Y-m-d\', $event_time);
\n        } else {
\n            $time_format = (@$_POST[\'report_format\'] == \'csv\') ? \'H_i\' : \'H:i\';
\n            $colname = date($time_format, $event_time);
\n        }
\n    if (@$_POST[\'report_format\'] == \'csv\') {
\n      $replace1 .= \',IF(i\'. $i . \'.attending, i\'. $i . \'.timeslot_status, \\\'Not Attending\\\') as `Attendance \' . $colname . \'`\';
\n      $replace1 .= \',i\'. $i . \'.planned_arrival as `Planned Arrival \' . $colname . \'`\';
\n      $replace1 .= \',i\'. $i . \'.planned_leave as `Planned Leave \' . $colname . \'`\';
\n    } else {
\n      $replace1 .= \',concat(\\\' <input type=\"text\" name=\"timeslot_status[\\\',bk.booking_id,\\\'][\\\',i\'. $i . \'.booking_item_id,\\\']\" value=\"\\\',ifnull(i\'. $i . \'.timeslot_status, \\\'Present,Paid\\\'),\\\'\" data-attending=\"\\\',i\'. $i . \'.attending,\\\'\" data-payg_apply_fees_when_absent=\"\\\',sc.payg_apply_fees_when_absent,\\\'\" style = \"width:100%\" />\\\') as `Attendance \' . $colname . \'`\';
\n      $replace1 .= \',concat(\\\' <input type=\"text\" name=\"planned_arrival[\\\',bk.booking_id,\\\'][\\\',i\'. $i . \'.booking_item_id,\\\']\" value=\"\\\',ifnull(date_format(i\'. $i . \'.planned_arrival, \\\'%H:%i\\\'), \\\'\' . date(\'H:i\', $event_time) . \'\\\'),\\\'\" style = \"width:100%\" />\\\') as `Planned Arrival \' . $colname . \'`\';
\n      $replace1 .= \',concat(\\\' <input type=\"text\" name=\"planned_leave[\\\',bk.booking_id,\\\'][\\\',i\'. $i . \'.booking_item_id,\\\']\" value=\"\\\',ifnull(date_format(i\'. $i . \'.planned_leave,\\\'%H:%i\\\'), \\\'\' .  date(\'H:i\', $event_end_time) . \'\\\'),\\\'\" style = \"width:100%\" />\\\') as `Planned Leave \' . $colname . \'`\';
\n    }
\n        $replace2 .= \'inner join plugin_courses_schedules_events e\'. $i . \' on e\'. $i . \'.id = \' . $event[\'id\'] . \' and e\'. $i . \'.delete = 0 \';
\n        $replace2 .= \'left join plugin_ib_educate_booking_items i\'. $i . \' on i\'. $i . \'.booking_id = bk.booking_id and i\'. $i . \'.period_id = \' . $event[\'id\'] . \' and i\'. $i . \'.delete = 0 and i\'. $i . \'.booking_status <>  3 \';
\n        $replace3[] = \'i\'. $i . \'.period_id is not null\';
\n    }
\n  if (@$_POST[\'report_format\'] == \'csv\') {
\n    $replace1 .= \',replace(group_concat(ifnull(n.note,\\\'\\\')),\\\'\"\\\',\\\'\"\\\') as `Notes`\';
\n  } else {
\n    $replace1 .= \',concat(\\\' <input type=\"text\" name=\"note[\\\',bk.booking_id,\\\']\" value=\"\\\',replace(group_concat(ifnull(n.note,\\\'\\\')),\\\'\"\\\',\\\'\"\\\'),\\\'\" style=\"min-width: 100px; width: 100%\" />\\\') as `Notes`\';
\n  }
\n    
\n  
\n  $replace2 .= \' left join tmp_account_stat on bk.booking_id = tmp_account_stat.booking_id\';
\n  if($event_count > 0){
\n    $replace2 .= \' left join plugin_contacts3_notes n on i0.booking_item_id = n.link_id and n.table_link_id = 3 and n.`deleted` = 0\';
\n  }
\n    $replace3 = count($replace3) > 0 ? \' and (\' . implode(\' or \', $replace3) . \') \' : \'\';
\n}
\n
\n$sql = $this->_sql;
\n$sql = str_replace(\'__PHP1__\', $replace1, $sql);
\n$sql = str_replace(\'__PHP2__\', $replace2, $sql);
\n$sql = str_replace(\'__PHP3__\', $replace3, $sql);
\n$this->_sql = $sql;
\n' 
WHERE
  `name` = 'Roll Call Attendance';;
