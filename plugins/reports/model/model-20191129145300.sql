/*
ts:2019-11-29 14:53:00
*/
DELIMITER ;;
INSERT INTO `plugin_reports_reports`
SET `name`          = 'Print Roll Call - General',
    `summary`       = '',
    `sql`           = "",
    `category`      = 0,
    `sub_category`  = 0,
    `dashboard`     = 0,
    `date_created`  = NOW(),
    `date_modified` = NOW(),
    `publish`       = 1,
    `delete`        = 0,
    `report_type`   = 'sql',
    `php_modifier`  = '';;

INSERT INTO plugin_reports_parameters
SET `report_id`      = (SELECT `id`
                        FROM `plugin_reports_reports`
                        WHERE `name` = 'Print Roll Call - General'
                        ORDER BY ID DESC
                        LIMIT 1),
    `type`           = 'date',
    `name`           = 'date',
    `value`          = '',
    `delete`         = 0,
    `is_multiselect` = 0;;

INSERT INTO plugin_reports_parameters
SET `report_id`      = (SELECT `id`
                        FROM `plugin_reports_reports`
                        WHERE `name` = 'Print Roll Call - General'
                        ORDER BY ID DESC
                        LIMIT 1),
    `type`           = 'custom',
    `name`           = 'location',
    `value`          = "(SELECT 
\n DISTINCT buildings.id, 
\n buildings.`name` AS `name`
\n FROM plugin_courses_schedules s
\n INNER JOIN plugin_courses_schedules_events e ON s.id = e.schedule_id AND e.`delete` = 0 AND s.`delete` = 0
\n INNER JOIN plugin_ib_educate_booking_items i ON e.id = i.period_id AND i.`delete` = 0
\n INNER JOIN plugin_ib_educate_bookings b ON i.booking_id = b.booking_id AND b.`delete` = 0
\n INNER JOIN plugin_courses_courses c ON s.course_id = c.id
\n INNER JOIN plugin_courses_locations  locations ON s.location_id = locations.id
\n INNER JOIN plugin_courses_locations buildings ON locations.parent_id = buildings.id
\n GROUP BY s.id
\n ORDER BY buildings.`name`, locations.`name`)",
    `delete`         = 0,
    `is_multiselect` = 0;;

INSERT INTO plugin_reports_parameters
SET `report_id`      = (SELECT `id`
                        FROM `plugin_reports_reports`
                        WHERE `name` = 'Print Roll Call - General'
                        ORDER BY ID DESC
                        LIMIT 1),
    `type`           = 'custom',
    `name`           = 'trainer_id',
    `value`          = "(SELECT DISTINCT t.id, CONCAT_WS(' ', t.title, ifnull(t.first_name, 'No Trainer'), t.last_name)
\n FROM plugin_courses_schedules s
\n INNER JOIN plugin_courses_schedules_events e ON s.id = e.schedule_id AND s.delete=0 AND e.delete=0
\n INNER JOIN plugin_ib_educate_booking_items i ON e.id = i.period_id AND i.`delete` = 0
\n INNER JOIN plugin_ib_educate_bookings b ON i.booking_id = b.booking_id AND b.`delete` = 0
\n LEFT JOIN plugin_contacts3_contacts t ON t.id = IF(e.trainer_id > 0, e.trainer_id, s.trainer_id)
\n LEFT JOIN plugin_courses_locations  locations ON s.location_id = locations.id
\n LEFT JOIN plugin_courses_locations buildings ON locations.parent_id = buildings.id
\n WHERE e.datetime_start >= '{!date!}' AND e.datetime_start < DATE_ADD('{!date!}',INTERVAL 1 DAY) AND (buildings.id = '{!location!}' or buildings.id is null)
\n ORDER BY t.first_name, t.last_name)",
    `delete`         = 0,
    `is_multiselect` = 0;;

INSERT INTO plugin_reports_parameters
SET `report_id`      = (SELECT `id`
                        FROM `plugin_reports_reports`
                        WHERE `name` = 'Print Roll Call - General'
                        ORDER BY ID DESC
                        LIMIT 1),
    `type`           = 'custom',
    `name`           = 'schedule_id',
    `value`          = "(SELECT 
\n DISTINCT s.id, 
\n CONCAT( s.`name`, ' ', IF(s.payment_type = 1, 'PrePAY', 'PAYG')) AS `name`
\n FROM plugin_courses_schedules s
\n INNER JOIN plugin_courses_schedules_events e ON s.id = e.schedule_id AND e.`delete` = 0 AND s.`delete` = 0
\n INNER JOIN plugin_ib_educate_booking_items i ON e.id = i.period_id AND i.`delete` = 0 and i.booking_status <> 3
\n INNER JOIN plugin_ib_educate_bookings b ON i.booking_id = b.booking_id AND b.`delete` = 0 and b.booking_status <> 3
\n INNER JOIN plugin_courses_courses c ON s.course_id = c.id
\n LEFT JOIN plugin_courses_locations  locations ON s.location_id = locations.id
\n LEFT JOIN plugin_courses_locations buildings ON locations.parent_id = buildings.id
\n WHERE e.datetime_start >= '{!date!}' AND e.datetime_start < DATE_ADD('{!date!}',INTERVAL 1 DAY) AND (s.trainer_id = '{!trainer_id!}' OR e.trainer_id = '{!trainer_id!}') AND s.publish=1
\n GROUP BY s.id
\n ORDER BY buildings.`name`, locations.`name`, s.`name`)",
    `delete`         = 0,
    `is_multiselect` = 0;;

INSERT INTO plugin_reports_parameters
SET `report_id`      = (SELECT `id`
                        FROM `plugin_reports_reports`
                        WHERE `name` = 'Print Roll Call - General'
                        ORDER BY ID DESC
                        LIMIT 1),
    `type`           = 'custom',
    `name`           = 'timeslot_id',
    `value`          = "(SELECT DISTINCT e.id, date_format(e.datetime_start, '%H:%i') as timeslot
\n FROM plugin_courses_schedules s
\n INNER JOIN plugin_courses_schedules_events e ON s.id = e.schedule_id
\n WHERE e.datetime_start >= '{!date!}' AND e.datetime_start < DATE_ADD('{!date!}',INTERVAL 1 DAY) AND s.id = '{!schedule_id!}' AND e.delete=0
\n ORDER BY e.datetime_start)",
    `delete`         = 0,
    `is_multiselect` = 0;;

UPDATE
    `plugin_reports_reports`
SET `sql`          = "SELECT
\n    st.id AS `Student ID`,
\n    bk.booking_id AS `Booking ID`,
\n    CONCAT_WS(' ', st.first_name, st.last_name)  AS `Student`
\n
\n     __PHP1__,
\n
\n    \'          \' AS `Student Signature`
\n FROM plugin_courses_schedules sc
\n    INNER JOIN plugin_courses_courses co ON sc.course_id = co.id
\n    INNER JOIN plugin_courses_categories cg ON co.category_id = cg.id
\n    INNER JOIN plugin_ib_educate_booking_has_schedules bs ON sc.id = bs.schedule_id
\n    INNER JOIN plugin_ib_educate_bookings bk ON bs.booking_id = bk.booking_id AND bk.`delete` = 0 AND bk.booking_status <> 3
\n    INNER JOIN plugin_contacts3_contacts st ON bk.contact_id = st.id
\n    INNER JOIN plugin_contacts3_residences re ON st.residence = re.address_id
\n
\n    __PHP2__
\n
\n WHERE sc.id = '\{!schedule_id!}\' __PHP3__
\n GROUP BY `Booking ID`
\n ORDER BY st.first_name,st.last_name
",


    `php_modifier` = "$date = $this->get_parameter('date');
\n$scheduleId = $this->get_parameter('schedule_id');
\nif (!is_numeric($scheduleId)){
\n    $scheduleId = 0;
\n}
\n$trainerId = $this->get_parameter('trainer_id');
\n$timeslotId = $this->get_parameter('timeslot_id');
\n$schedule = Model_Schedules::get_schedule($scheduleId);
\n
\n$eventsQuery = DB::select('e.*')->from(array('plugin_courses_schedules_events', 'e'))
\n    ->join(array('plugin_courses_schedules', 's'), 'inner')->on('e.schedule_id', '=', 's.id')
\n    ->where('e.schedule_id', '=', $scheduleId)
\n    ->and_where('e.delete', '=', 0);
\n
\nif ($schedule) {
\n    if (@$schedule['payg_period'] == 'week') {
\n        $weekstart = date('Y-m-d', strtotime(\"monday this week\", strtotime($date)));
\n        $eventsQuery->and_where('e.datetime_start', '>=', $weekstart);
\n        $eventsQuery->and_where('e.datetime_start', '<', DB::expr('date_add(\"' . $weekstart . '\", interval 1 week)'));
\n    } else {
\n        $eventsQuery->and_where('e.datetime_start', '>=', $date);
\n        $eventsQuery->and_where('e.datetime_start', '<', DB::expr('date_add(\"' . $date . '\", interval 1 day)'));
\n    }
\n}
\n
\nif (is_numeric($trainerId)) {
\n    $eventsQuery->and_where(DB::expr('if(e.trainer_id > 0, e.trainer_id = ' . $trainerId . ', s.trainer_id = ' . $trainerId . ')'), '>', 0);
\n}
\nif($timeslotId){
\n  $eventsQuery->and_where('e.id','=',$timeslotId);
\n}
\n$events = $eventsQuery->order_by('e.datetime_start', 'asc')
\n    ->execute()
\n    ->as_array();
\n
\nDB::query(null, 'DROP TEMPORARY TABLE IF EXISTS tmp_account_stat')->execute();
\nDB::query(null, 'DROP TEMPORARY TABLE IF EXISTS tmp_tx_stat')->execute();
\nDB::query(null, 'CREATE TEMPORARY TABLE tmp_tx_stat
\n(SELECT
\n        tx.id,
\n        tx.booking_id,
\n        tx.total,
\n        tt.credit,
\n        SUM(IFNULL(py.amount,0)) AS pyt,
\n        SUM(IFNULL(IF(ps.credit > 0, py.amount, -py.amount),0)) AS pyx,
\n        tx.total - SUM(IFNULL(IF(ps.credit > 0, py.amount, -py.amount),0)) AS outstanding
\n    FROM plugin_bookings_transactions tx
\n        INNER JOIN plugin_bookings_transactions_has_schedule has ON tx.id = has.transaction_id AND has.schedule_id = ' . $scheduleId . '
\n        LEFT JOIN plugin_bookings_transactions_types tt ON tx.type = tt.id
\n        LEFT JOIN plugin_bookings_transactions_payments py ON tx.id = py.transaction_id AND py.deleted = 0
\n        LEFT JOIN plugin_bookings_transactions_payments_statuses ps ON py.`status` = ps.id
\n    GROUP BY tx.id
\n    HAVING outstanding <> 0)')->execute();
\nDB::query(null, 'ALTER TABLE tmp_tx_stat ADD KEY (`booking_id`)')->execute();
\nDB::query(null, 'DROP TEMPORARY TABLE IF EXISTS tmp_account_stat')->execute();
\nDB::query(null, 'CREATE TEMPORARY TABLE tmp_account_stat
\n(SELECT
\n        bk.booking_id,
\n        SUM(IFNULL(IF(tx.credit > 0, tx.outstanding, -tx.outstanding), 0)) AS outstanding
\n    FROM plugin_ib_educate_bookings bk
\n        LEFT JOIN tmp_tx_stat tx ON bk.booking_id = tx.booking_id
\n    GROUP BY bk.booking_id)')->execute();
\nDB::query(null, 'ALTER TABLE tmp_account_stat ADD KEY (`booking_id`)')->execute();
\n
\n$replace1 = '';
\n$replace2 = '';
\n$replace3 = array();
\nif(count($events) == 0){
\n    $replace3 = ' and 0 ';
\n} else {
\n    $event_count = count($events);
\n    foreach($events as $i => $event){
\n        $event_time = strtotime($event['datetime_start']);
\n        if(date('H', $event_time) == 0){
\n            $colname = date('Y-m-d', $event_time);
\n        } else {
\n            $colname = date('H:i', $event_time);
\n        }
\n        if (@$_POST['report_format'] == 'csv') {
\n            $replace1 .= ',IF(i'. $i . '.attending, i'. $i . '.timeslot_status, \\'Not Attending\\') as `Time ' . $colname . '`';
\n        } else {
\n            $replace1 .= ',IF(i'. $i . '.attending = 1, concat(\\' <input type=\"checkbox\" name=\"timeslot_status[\\',bk.booking_id,\\'][\\',i'. $i . '.booking_item_id,\\']\" value=\"\\',ifnull(i'. $i . '.timeslot_status, \\'Present,Paid\\'),\\'\" data-attending=\"\\',i'. $i . '.attending,\\'\" style = \"width:100%\" />\\'), \\'Not Attending\\') as `Time ' . $colname . '`';
\n        }
\n        $replace2 .= 'inner join plugin_courses_schedules_events e'. $i . ' on e'. $i . '.id = ' . $event['id'] . ' and e'. $i . '.delete = 0 ';
\n        $replace2 .= 'left join plugin_ib_educate_booking_items i'. $i . ' on i'. $i . '.booking_id = bk.booking_id and i'. $i . '.period_id = ' . $event['id'] . ' and i'. $i . '.delete = 0 /*and i'. $i . '.booking_status <> 3 */';
\n        $replace3[] = 'i'. $i . '.period_id is not null';
\n    }
\n
\n    $replace2 .= ' left join tmp_account_stat on bk.booking_id = tmp_account_stat.booking_id';
\n    if($event_count > 0){
\n        $replace2 .= ' left join plugin_contacts3_notes n on i0.booking_item_id = n.link_id and n.table_link_id = 3 and n.`deleted` = 0';
\n    }
\n    $replace3 = count($replace3) > 0 ? ' and (' . implode(' or ', $replace3) . ') ' : '';
\n}
\n$sql = $this->_sql;
\n$sql = str_replace('__PHP1__', $replace1, $sql);
\n$sql = str_replace('__PHP2__', $replace2, $sql);
\n$sql = str_replace('__PHP3__', $replace3, $sql);
\n$this->_sql = $sql;
\n"
WHERE `name` = 'Print Roll Call - General'
;;


INSERT IGNORE INTO `plugin_reports_report_sharing` (`report_id`, `group_id`)
select rr.id, pr.id
from plugin_reports_reports `rr`
         left join engine_project_role `pr` on `pr`.role = 'Administrator'
order by rr.id desc
limit 1;;

INSERT IGNORE INTO `plugin_reports_report_sharing` (`report_id`, `group_id`)
select rr.id, pr.id
from plugin_reports_reports `rr`
         left join engine_project_role `pr` on `pr`.role = 'Super Users'
order by rr.id desc
limit 1;;

UPDATE `plugin_reports_reports`
SET `publish` = '0',
    `delete`  = '1'
WHERE (`name` = 'Print Roll Call - General');;

UPDATE
    `plugin_reports_reports`
SET `sql`          = "SELECT
\n    st.id AS `Student ID`,
\n    bk.booking_id AS `Booking ID`,
\n    CONCAT_WS(' ', st.first_name, st.last_name)  AS `Student`
\n
\n     __PHP1__,
\n
\n    \'          \' AS `Student Signature`
\n FROM plugin_courses_schedules sc
\n    INNER JOIN plugin_courses_courses co ON sc.course_id = co.id
\n    INNER JOIN plugin_courses_categories cg ON co.category_id = cg.id
\n    INNER JOIN plugin_ib_educate_booking_has_schedules bs ON sc.id = bs.schedule_id
\n    INNER JOIN plugin_ib_educate_bookings bk ON bs.booking_id = bk.booking_id AND bk.`delete` = 0 AND bk.booking_status <> 3
\n    INNER JOIN plugin_contacts3_contacts st ON bk.contact_id = st.id
\n    INNER JOIN plugin_contacts3_residences re ON st.residence = re.address_id
\n
\n    __PHP2__
\n
\n WHERE sc.id = '\{!schedule_id!}\' __PHP3__
\n GROUP BY `Booking ID`
\n ORDER BY st.first_name,st.last_name
",
`php_modifier` = "$date = $this->get_parameter('date');
\n$scheduleId = $this->get_parameter('schedule_id');
\nif (!is_numeric($scheduleId)){
\n    $scheduleId = 0;
\n}
\n$trainerId = $this->get_parameter('trainer_id');
\n$timeslotId = $this->get_parameter('timeslot_id');
\n$schedule = Model_Schedules::get_schedule($scheduleId);
\n
\n$eventsQuery = DB::select('e.*')->from(array('plugin_courses_schedules_events', 'e'))
\n    ->join(array('plugin_courses_schedules', 's'), 'inner')->on('e.schedule_id', '=', 's.id')
\n    ->where('e.schedule_id', '=', $scheduleId)
\n    ->and_where('e.delete', '=', 0);
\n
\nif ($schedule) {
\n    if (@$schedule['payg_period'] == 'week') {
\n        $weekstart = date('Y-m-d', strtotime(\"monday this week\", strtotime($date)));
\n        $eventsQuery->and_where('e.datetime_start', '>=', $weekstart);
\n        $eventsQuery->and_where('e.datetime_start', '<', DB::expr('date_add(\"' . $weekstart . '\", interval 1 week)'));
\n    } else {
\n        $eventsQuery->and_where('e.datetime_start', '>=', $date);
\n        $eventsQuery->and_where('e.datetime_start', '<', DB::expr('date_add(\"' . $date . '\", interval 1 day)'));
\n    }
\n}
\n
\nif (is_numeric($trainerId)) {
\n    $eventsQuery->and_where(DB::expr('if(e.trainer_id > 0, e.trainer_id = ' . $trainerId . ', s.trainer_id = ' . $trainerId . ')'), '>', 0);
\n}
\nif($timeslotId){
\n  $eventsQuery->and_where('e.id','=',$timeslotId);
\n}
\n$events = $eventsQuery->order_by('e.datetime_start', 'asc')
\n    ->execute()
\n    ->as_array();
\n
\nDB::query(null, 'DROP TEMPORARY TABLE IF EXISTS tmp_account_stat')->execute();
\nDB::query(null, 'DROP TEMPORARY TABLE IF EXISTS tmp_tx_stat')->execute();
\nDB::query(null, 'CREATE TEMPORARY TABLE tmp_tx_stat
\n(SELECT
\n        tx.id,
\n        tx.booking_id,
\n        tx.total,
\n        tt.credit,
\n        SUM(IFNULL(py.amount,0)) AS pyt,
\n        SUM(IFNULL(IF(ps.credit > 0, py.amount, -py.amount),0)) AS pyx,
\n        tx.total - SUM(IFNULL(IF(ps.credit > 0, py.amount, -py.amount),0)) AS outstanding
\n    FROM plugin_bookings_transactions tx
\n        INNER JOIN plugin_bookings_transactions_has_schedule has ON tx.id = has.transaction_id AND has.schedule_id = ' . $scheduleId . '
\n        LEFT JOIN plugin_bookings_transactions_types tt ON tx.type = tt.id
\n        LEFT JOIN plugin_bookings_transactions_payments py ON tx.id = py.transaction_id AND py.deleted = 0
\n        LEFT JOIN plugin_bookings_transactions_payments_statuses ps ON py.`status` = ps.id
\n    GROUP BY tx.id
\n    HAVING outstanding <> 0)')->execute();
\nDB::query(null, 'ALTER TABLE tmp_tx_stat ADD KEY (`booking_id`)')->execute();
\nDB::query(null, 'DROP TEMPORARY TABLE IF EXISTS tmp_account_stat')->execute();
\nDB::query(null, 'CREATE TEMPORARY TABLE tmp_account_stat
\n(SELECT
\n        bk.booking_id,
\n        SUM(IFNULL(IF(tx.credit > 0, tx.outstanding, -tx.outstanding), 0)) AS outstanding
\n    FROM plugin_ib_educate_bookings bk
\n        LEFT JOIN tmp_tx_stat tx ON bk.booking_id = tx.booking_id
\n    GROUP BY bk.booking_id)')->execute();
\nDB::query(null, 'ALTER TABLE tmp_account_stat ADD KEY (`booking_id`)')->execute();
\n
\n$replace1 = '';
\n$replace2 = '';
\n$replace3 = array();
\nif(count($events) == 0){
\n    $replace3 = ' and 0 ';
\n} else {
\n    $event_count = count($events);
\n    foreach($events as $i => $event){
\n        $event_time = strtotime($event['datetime_start']);
\n        if(date('H', $event_time) == 0){
\n            $colname = date('Y-m-d', $event_time);
\n        } else {
\n            $colname = date('H:i', $event_time);
\n        }
\n        if (@$_POST['report_format'] == 'csv') {
\n            $replace1 .= ',IF(i'. $i . '.attending, i'. $i . '.timeslot_status, \\'Not Attending\\') as `Time ' . $colname . '`';
\n        } else {
\n            $replace1 .= ',IF(i'. $i . '.attending = 1, concat(\\' <input type=\"checkbox\" name=\"timeslot_status[\\',bk.booking_id,\\'][\\',i'. $i . '.booking_item_id,\\']\" value=\"\\',ifnull(i'. $i . '.timeslot_status, \\'Present,Paid\\'),\\'\" data-attending=\"\\',i'. $i . '.attending,\\'\" style = \"width:100%\" />\\'), \\'Not Attending\\') as `Time ' . $colname . '`';
\n        }
\n        $replace2 .= 'inner join plugin_courses_schedules_events e'. $i . ' on e'. $i . '.id = ' . $event['id'] . ' and e'. $i . '.delete = 0 ';
\n        $replace2 .= 'left join plugin_ib_educate_booking_items i'. $i . ' on i'. $i . '.booking_id = bk.booking_id and i'. $i . '.period_id = ' . $event['id'] . ' and i'. $i . '.delete = 0 /*and i'. $i . '.booking_status <> 3 */';
\n        $replace3[] = 'i'. $i . '.period_id is not null';
\n    }
\n
\n    $replace2 .= ' left join tmp_account_stat on bk.booking_id = tmp_account_stat.booking_id';
\n    if($event_count > 0){
\n        $replace2 .= ' left join plugin_contacts3_notes n on i0.booking_item_id = n.link_id and n.table_link_id = 3 and n.`deleted` = 0';
\n    }
\n    $replace3 = count($replace3) > 0 ? ' and (' . implode(' or ', $replace3) . ') ' : '';
\n}
\n$sql = $this->_sql;
\n$sql = str_replace('__PHP1__', $replace1, $sql);
\n$sql = str_replace('__PHP2__', $replace2, $sql);
\n$sql = str_replace('__PHP3__', $replace3, $sql);
\n$this->_sql = $sql;
\n"
WHERE `name` = 'Print Roll Call'
;;