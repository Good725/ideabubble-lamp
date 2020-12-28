/*
ts: 2020-04-22 16:33:00
*/
UPDATE `plugin_reports_reports`
SET
    `php_modifier`= '\r\n
$date = $this->get_parameter(\'date\');\n
$scheduleId = $this->get_parameter(\'schedule_id\');\n
if (!is_numeric($scheduleId)){\n
  $scheduleId = 0;\n
}\n
$trainerId = $this->get_parameter(\'trainer_id\');\n
$timeslotId = $this->get_parameter(\'timeslot_id\');\n
$schedule = Model_Schedules::get_schedule($scheduleId);\n
\r\n
$eventsQuery = DB::select(\'e.*\')->from(array(\'plugin_courses_schedules_events\', \'e\'))\n
  ->join(array(\'plugin_courses_schedules\', \'s\'), \'inner\')->on(\'e.schedule_id\', \'=\', \'s.id\')\n
  ->where(\'e.schedule_id\', \'=\', $scheduleId)\n
  ->and_where(\'e.delete\', \'=\', 0);\n
\r\n
if($timeslotId){\n
$eventsQuery->and_where(\'e.id\', \'=\', $timeslotId);\n
}\n
\r\n
if ($schedule){\n
  if (@$schedule[\'payg_period\'] == \'week\') {\n
    $weekstart = date(\'Y-m-d\', strtotime("monday this week", strtotime($date)));\n
    $eventsQuery->and_where(\'e.datetime_start\', \'>=\', $weekstart);\n
    $eventsQuery->and_where(\'e.datetime_start\', \'<\', DB::expr(\'date_add("\' . $weekstart . \'", interval 1 week)\'));\n
  } else {\n
    $eventsQuery->and_where(\'e.datetime_start\', \'>=\', $date);\n
    $eventsQuery->and_where(\'e.datetime_start\', \'<\', DB::expr(\'date_add("\' . $date . \'", interval 1 day)\'));\n
  }\n
}\r\n
\r\n
if (is_numeric($trainerId)) {\n
  $eventsQuery->and_where(DB::expr(\'if(e.trainer_id > 0, e.trainer_id = \' . $trainerId . \', s.trainer_id = \' . $trainerId . \')\'), \'>\', 0);\n
}\r\n
$events = $eventsQuery->order_by(\'e.datetime_start\', \'asc\')\n
  ->execute()\n
  ->as_array();\n
\r\n
DB::query(null, \'DROP TEMPORARY TABLE IF EXISTS tmp_account_stat\')->execute();\n
DB::query(null, \'DROP TEMPORARY TABLE IF EXISTS tmp_tx_stat\')->execute();\n
DB::query(null, \'CREATE TEMPORARY TABLE tmp_tx_stat\n
(SELECT\n
    tx.id,\n
    tx.booking_id,\n
    tx.total,\n
    tt.credit,\n
    SUM(IFNULL(py.amount,0)) AS pyt,\n
    SUM(IFNULL(IF(ps.credit > 0, py.amount, -py.amount),0)) AS pyx,\n
    tx.total - SUM(IFNULL(IF(ps.credit > 0, py.amount, -py.amount),0)) AS outstanding\n
  FROM plugin_bookings_transactions tx\n
    INNER JOIN plugin_bookings_transactions_has_schedule has ON tx.id = has.transaction_id AND has.schedule_id = \' . $scheduleId . \'\n
    LEFT JOIN plugin_bookings_transactions_types tt ON tx.type = tt.id\n
    LEFT JOIN plugin_bookings_transactions_payments py ON tx.id = py.transaction_id AND py.deleted = 0\n
    LEFT JOIN plugin_bookings_transactions_payments_statuses ps ON py.`status` = ps.id\n
  GROUP BY tx.id\n
  HAVING outstanding <> 0)\')->execute();\n
DB::query(null, \'ALTER TABLE tmp_tx_stat ADD KEY (`booking_id`)\')->execute();\n
DB::query(null, \'DROP TEMPORARY TABLE IF EXISTS tmp_account_stat\')->execute();\n
DB::query(null, \'CREATE TEMPORARY TABLE tmp_account_stat\n
(SELECT\n
    bk.booking_id,\n
    SUM(IFNULL(IF(tx.credit > 0, tx.outstanding, -tx.outstanding), 0)) AS outstanding\n
  FROM plugin_ib_educate_bookings bk\n
    LEFT JOIN tmp_tx_stat tx ON bk.booking_id = tx.booking_id\n
  GROUP BY bk.booking_id)\')->execute();\n
DB::query(null, \'ALTER TABLE tmp_account_stat ADD KEY (`booking_id`)\')->execute();\n
\r\n
$replace1 = \'\';\n
$replace2 = \'\';\n
$replace3 = array();\n
if(count($events) == 0){\n
    $replace3 = \' and 0 \';\n
} else {\n
    $event_count = count($events);\n
    foreach($events as $i => $event){\n
        $event_time = strtotime($event[\'datetime_start\']);\n
    $event_end_time = strtotime($event[\'datetime_end\']);\n
        if(date(\'H\', $event_time) == 0){\n
            $colname = date(\'Y-m-d\', $event_time);\n
        } else {\n
            $time_format = (@$_POST[\'report_format\'] == \'csv\') ? \'H_i\' : \'H:i\';\n
            $colname = date($time_format, $event_time);\n
        }\n
    if (@$_POST[\'report_format\'] == \'csv\') {\n
      $replace1 .= \',IF(i\'. $i . \'.attending, i\'. $i . \'.timeslot_status, \\\'Not Attending\\\') as `Attendance \' . $colname . \'`\';\n
      $replace1 .= \',i\'. $i . \'.planned_arrival as `Planned Arrival \' . $colname . \'`\';\n
      $replace1 .= \',i\'. $i . \'.planned_leave as `Planned Leave \' . $colname . \'`\';\n
    } else {\n
      $replace1 .= \',concat(\\\' <input type=\"text\" name=\"timeslot_status[\\\',bk.booking_id,\\\'][\\\',i\'. $i . \'.booking_item_id,\\\']\" value=\"\\\',ifnull(i\'. $i . \'.timeslot_status, \\\'Present,Paid\\\'),\\\'\" data-attending=\"\\\',i\'. $i . \'.attending,\\\'\" data-payg_apply_fees_when_absent=\"\\\',IFNULL(sc.payg_apply_fees_when_absent, \\\'\\\'),\\\'\" style = \"width:100%\" />\\\') as `Attendance \' . $colname . \'`\';\n
      $replace1 .= \',concat(\\\' <input type=\"text\" name=\"planned_arrival[\\\',bk.booking_id,\\\'][\\\',i\'. $i . \'.booking_item_id,\\\']\" value=\"\\\',ifnull(date_format(i\'. $i . \'.planned_arrival, \\\'%H:%i\\\'), \\\'\' . date(\'H:i\', $event_time) . \'\\\'),\\\'\" style = \"width:100%\" />\\\') as `Planned Arrival \' . $colname . \'`\';\n
      $replace1 .= \',concat(\\\' <input type=\"text\" name=\"planned_leave[\\\',bk.booking_id,\\\'][\\\',i\'. $i . \'.booking_item_id,\\\']\" value=\"\\\',ifnull(date_format(i\'. $i . \'.planned_leave,\\\'%H:%i\\\'), \\\'\' .  date(\'H:i\', $event_end_time) . \'\\\'),\\\'\" style = \"width:100%\" />\\\') as `Planned Leave \' . $colname . \'`\';\n
    }\n
        $replace2 .= \'inner join plugin_courses_schedules_events e\'. $i . \' on e\'. $i . \'.id = \' . $event[\'id\'] . \' and e\'. $i . \'.delete = 0 \';\n
        $replace2 .= \'left join plugin_ib_educate_booking_items i\'. $i . \' on i\'. $i . \'.booking_id = bk.booking_id and i\'. $i . \'.period_id = \' . $event[\'id\'] . \' and i\'. $i . \'.delete = 0 and i\'. $i . \'.booking_status <>  3 \';\n
        $replace3[] = \'i\'. $i . \'.period_id is not null\';\n
    }\n
  if (@$_POST[\'report_format\'] == \'csv\') {\n
    $replace1 .= \',replace(group_concat(ifnull(n.note,\\\'\\\')),\\\'\"\\\',\\\'\"\\\') as `Notes`\';\n
  } else {\n
    $replace1 .= \',concat(\\\' <textarea type=\"text\" style=\"width: 100%\" name=\"note[\\\',bk.booking_id,\\\']\">\\\',replace(group_concat(ifnull(n.note,\\\'\\\')),\\\'\"\\\',\\\'\"\\\'),\\\'</textarea>\\\') as `Notes`\';\n
  }\n
\r\n
\r\n
\r\n
  $replace2 .= \' left join tmp_account_stat on bk.booking_id = tmp_account_stat.booking_id\';\n
  if($event_count > 0){\n
    $replace2 .= \' left join plugin_contacts3_notes n on i0.booking_item_id = n.link_id and n.table_link_id = 3 and n.`deleted` = 0\';\n
  }\n
    $replace3 = count($replace3) > 0 ? \' and (\' . implode(\' or \', $replace3) . \') \' : \'\';\n
}\n
\r\n
$sql = $this->_sql;\n
$sql = str_replace(\'__PHP1__\', $replace1, $sql);\n
$sql = str_replace(\'__PHP2__\', $replace2, $sql);\n
\r\n
$sql = str_replace(\'__PHP3__\', $replace3, $sql);\n
$this->_sql = $sql;\n'
WHERE (`name`='Roll Call Attendance');

UPDATE `plugin_reports_reports` SET `action_event`= '\r\n
var $reportRows = $(\"#report_table tbody > tr\");\n
var $timeslotStatuses = $(\"[name*=timeslot_status]\");\n
var rows = [];\n
for (var i = 0 ; i < $reportRows.length ; ++i) {\n
    var timeslotStatuses = $($reportRows[i]).find(\"select[name*=timeslot_status]\");\n
	var plannedArrivals = $($reportRows[i]).find(\"input[name*=planned_arrival]\");\n
	var plannedLeaves = $($reportRows[i]).find(\"input[name*=planned_leave]\");\n
    var booking = {id: null, items: [], note: null, amount: null};\n
    for (var j = 0 ; j < timeslotStatuses.length ; ++j) {\n
        var timeslotStatus = $(timeslotStatuses[j]);\n
        var params = /timeslot_status\\[(\\d+)\\]\\[(\\d+)\\]/.exec(timeslotStatus.attr(\'name\'));\n
        if (params) {\n
            booking.id = params[1];\n
            booking.items.push({id: params[2], status: timeslotStatus.val(), planned_arrival: $(plannedArrivals[j]).val(), planned_leave: $(plannedLeaves[j]).val()});\n
            booking.note = $($reportRows[i]).find(\"textarea[name*=note]\").val();\n
            booking.transactionId = $($reportRows[i]).find(\"select[name*=transaction]\").val();\n
            booking.amount = $($reportRows[i]).find(\"input[name*=amount]\").val();\n
            rows.push(booking);\n
        }\n
    }\n
}\n
\r\n
var data = {json: JSON.stringify(rows), dont_update_accounts: 1};\n
$.post(\'/admin/bookings/ajax_rollcall_update\', data, function(response){\n
    $(\"#generate_report\").click();alert(response);\n
});\n'
WHERE (`name`='Roll Call Attendance');



