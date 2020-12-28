/*
ts:2019-11-22 07:52:00
*/

UPDATE `plugin_reports_reports` SET `php_modifier`='$date = $this->get_parameter(\'date\');\r\n$scheduleId = $this->get_parameter(\'schedule_id\');\r\nif (!is_numeric($scheduleId)){\r\n    $scheduleId = 0;\r\n}\r\n$trainerId = $this->get_parameter(\'trainer_id\');\r\n$timeslotId = $this->get_parameter(\'timeslot_id\');\r\n$schedule = Model_Schedules::get_schedule($scheduleId);\r\n\r\n$eventsQuery = DB::select(\'e.*\')->from(array(\'plugin_courses_schedules_events\', \'e\'))\r\n    ->join(array(\'plugin_courses_schedules\', \'s\'), \'inner\')->on(\'e.schedule_id\', \'=\', \'s.id\')\r\n    ->where(\'e.schedule_id\', \'=\', $scheduleId)\r\n    ->and_where(\'e.delete\', \'=\', 0);\r\n\r\nif($timeslotId){\r\n$eventsQuery->and_where(\'e.id\', \'=\', $timeslotId);\r\n}\r\n\r\nif ($schedule){\r\n    if (@$schedule[\'payg_period\'] == \'week\') {\r\n        $weekstart = date(\'Y-m-d\', strtotime(\"monday this week\", strtotime($date)));\r\n        $eventsQuery->and_where(\'e.datetime_start\', \'>=\', $weekstart);\r\n        $eventsQuery->and_where(\'e.datetime_start\', \'<\', DB::expr(\'date_add(\"\' . $weekstart . \'\", interval 1 week)\'));\r\n    } else {\r\n        $eventsQuery->and_where(\'e.datetime_start\', \'>=\', $date);\r\n        $eventsQuery->and_where(\'e.datetime_start\', \'<\', DB::expr(\'date_add(\"\' . $date . \'\", interval 1 day)\'));\r\n    }\r\n}\r\n\r\nif (is_numeric($trainerId)) {\r\n    $eventsQuery->and_where(DB::expr(\'if(e.trainer_id > 0, e.trainer_id = \' . $trainerId . \', s.trainer_id = \' . $trainerId . \')\'), \'>\', 0);\r\n}\r\n$events = $eventsQuery->order_by(\'e.datetime_start\', \'asc\')\r\n    ->execute()\r\n    ->as_array();\r\n\r\nDB::query(null, \'DROP TEMPORARY TABLE IF EXISTS tmp_account_stat\')->execute();\r\nDB::query(null, \'DROP TEMPORARY TABLE IF EXISTS tmp_tx_stat\')->execute();\r\nDB::query(null, \'CREATE TEMPORARY TABLE tmp_tx_stat\r\n(SELECT \r\n        tx.id,\r\n        tx.booking_id,\r\n        tx.total,\r\n        tt.credit,\r\n        SUM(IFNULL(py.amount,0)) AS pyt,\r\n        SUM(IFNULL(IF(ps.credit > 0, py.amount, -py.amount),0)) AS pyx,\r\n        tx.total - SUM(IFNULL(IF(ps.credit > 0, py.amount, -py.amount),0)) AS outstanding\r\n    FROM plugin_bookings_transactions tx\r\n        INNER JOIN plugin_bookings_transactions_has_schedule has ON tx.id = has.transaction_id AND has.schedule_id = \' . $scheduleId . \'\r\n        LEFT JOIN plugin_bookings_transactions_types tt ON tx.type = tt.id\r\n        LEFT JOIN plugin_bookings_transactions_payments py ON tx.id = py.transaction_id AND py.deleted = 0\r\n        LEFT JOIN plugin_bookings_transactions_payments_statuses ps ON py.`status` = ps.id\r\n    GROUP BY tx.id\r\n    HAVING outstanding <> 0)\')->execute();\r\nDB::query(null, \'ALTER TABLE tmp_tx_stat ADD KEY (`booking_id`)\')->execute();\r\nDB::query(null, \'DROP TEMPORARY TABLE IF EXISTS tmp_account_stat\')->execute();\r\nDB::query(null, \'CREATE TEMPORARY TABLE tmp_account_stat\r\n(SELECT \r\n        bk.booking_id, \r\n        SUM(IFNULL(IF(tx.credit > 0, tx.outstanding, -tx.outstanding), 0)) AS outstanding\r\n    FROM plugin_ib_educate_bookings bk\r\n        LEFT JOIN tmp_tx_stat tx ON bk.booking_id = tx.booking_id\r\n    GROUP BY bk.booking_id)\')->execute();\r\nDB::query(null, \'ALTER TABLE tmp_account_stat ADD KEY (`booking_id`)\')->execute();\r\n\r\n$replace1 = \'\';\r\n$replace2 = \'\';\r\n$replace3 = array();\r\nif(count($events) == 0){\r\n    $replace3 = \' and 0 \';\r\n} else {\r\n    $event_count = count($events);\r\n    foreach($events as $i => $event){\r\n        $event_time = strtotime($event[\'datetime_start\']);\r\n        $event_end_time = strtotime($event[\'datetime_end\']);\r\n        if(date(\'H\', $event_time) == 0){\r\n            $colname = date(\'Y-m-d\', $event_time);\r\n        } else {\r\n            $colname = date(\'H_i\', $event_time);\r\n        }\r\n        if (@$_POST[\'report_format\'] == \'csv\') {\r\n            $replace1 .= \',IF(i\'. $i . \'.attending, i\'. $i . \'.timeslot_status, \\\'Not Attending\\\') as `Time \' . $colname . \'`\';\r\n            $replace1 .= \',i\'. $i . \'.planned_arrival as `Planned Arrival \' . $colname . \'`\';\r\n            $replace1 .= \',i\'. $i . \'.planned_leave as `Planned Leave \' . $colname . \'`\';\r\n        } else {\r\n            $replace1 .= \',concat(\\\' <input type=\"text\" name=\"timeslot_status[\\\',bk.booking_id,\\\'][\\\',i\'. $i . \'.booking_item_id,\\\']\" value=\"\\\',ifnull(i\'. $i . \'.timeslot_status, \\\'Present,Paid\\\'),\\\'\" data-attending=\"\\\',i\'. $i . \'.attending,\\\'\" data-payg_apply_fees_when_absent=\"\\\',sc.payg_apply_fees_when_absent,\\\'\" data-payg_absent_fee=\"\\\',ifnull(sc.payg_absent_fee,sc.fee_amount),\\\'\" style = \"width:100%\" />\\\') as `Time \' . $colname . \'`\';\r\n            $replace1 .= \',concat(\\\' <input type=\"text\" name=\"planned_arrival[\\\',bk.booking_id,\\\'][\\\',i\'. $i . \'.booking_item_id,\\\']\" value=\"\\\',ifnull(date_format(i\'. $i . \'.planned_arrival, \\\'%H:%i\\\'), \\\'\' . date(\'H:i\', $event_time) . \'\\\'),\\\'\" style = \"width:100%\" />\\\') as `Planned Arrival \' . $colname . \'`\';\r\n            $replace1 .= \',concat(\\\' <input type=\"text\" name=\"planned_leave[\\\',bk.booking_id,\\\'][\\\',i\'. $i . \'.booking_item_id,\\\']\" value=\"\\\',ifnull(date_format(i\'. $i . \'.planned_leave,\\\'%H:%i\\\'), \\\'\' .  date(\'H:i\', $event_end_time) . \'\\\'),\\\'\" style = \"width:100%\" />\\\') as `Planned Leave \' . $colname . \'`\';\r\n        }\r\n        $replace2 .= \'inner join plugin_courses_schedules_events e\'. $i . \' on e\'. $i . \'.id = \' . $event[\'id\'] . \' and e\'. $i . \'.delete = 0 \';\r\n        $replace2 .= \'left join plugin_ib_educate_booking_items i\'. $i . \' on i\'. $i . \'.booking_id = bk.booking_id and i\'. $i . \'.period_id = \' . $event[\'id\'] . \' and i\'. $i . \'.delete = 0 and i\'. $i . \'.booking_status <>  3 \';\r\n        $replace3[] = \'i\'. $i . \'.period_id is not null\';\r\n    }\r\n	\r\n	$replace1 .= \',concat(\\\' <input \' . ($schedule[\'payg_apply_fees_when_absent\'] ? \'checkedx=checkedx\' : \'\') . \' type=\"checkbox\" name=\"apply_absent_fee[\\\',bk.booking_id,\\\'][\\\',i\'. $i . \'.booking_item_id,\\\']\" value=\"\\\',ifnull(sc.payg_absent_fee,sc.fee_amount),\\\'\" style = \"width:100%\" />\\\') as `Apply Absent Fee` \';\r\n	\r\n    if (@$_POST[\'report_format\'] == \'csv\') {\r\n        $replace1 .= \',replace(group_concat(ifnull(n.note,\\\'\\\')),\\\'\"\\\',\\\'\"\\\') as `Notes`\';\r\n    } else {\r\n        $replace1 .= \',concat(\\\' <input type=\"text\" name=\"note[\\\',bk.booking_id,\\\']\" value=\"\\\',replace(group_concat(ifnull(n.note,\\\'\\\')),\\\'\"\\\',\\\'\"\\\'),\\\'\" style=\"width:100%\" />\\\') as `Notes`\';\r\n    }\r\n    \r\n    //$replace1 .= \',concat(\\\'<var class=\"transactions\">\\\',GROUP_CONCAT(CONCAT_WS(\\\'@@\\\', tx.id, tt.type, tx.total, tx.amount) SEPARATOR \\\'||\\\'), \\\'</var>\\\') AS `Transaction`\';\r\n    $replace1 .= \', IF(i\'. $i . \'.attending = 1 OR sc.payg_apply_fees_when_absent = 1, tmp_account_stat.outstanding, \\\' - \\\') AS `Outstanding`\';\r\n    if (@$_POST[\'report_format\'] == \'csv\') {\r\n        //$replace1 .= \',sc.fee_amount * \' . ($schedule[\'fee_per\'] == \'Schedule\' ? 1 : $event_count) . \' as `Amount Received`\';\r\n    } else {\r\n        $replace1 .= \', IF(i\'. $i . \'.attending = 1 OR sc.payg_apply_fees_when_absent = 1,concat(\\\'<input type=\"text\" name=\"amount[\\\',bk.booking_id,\\\']\" value=\"\\\', IF(sc.payment_type = 1, 0, ifnull(e\'. $i . \'.fee_amount, sc.fee_amount) * \' . ($schedule[\'fee_per\'] == \'Schedule\' ? 1 : $event_count) . \'),\\\'\" style=\"width:100%\"/>\\\'), \\\' - \\\') as `Amount Received`\';\r\n    }\r\n\r\n    $replace2 .= \' left join tmp_account_stat on bk.booking_id = tmp_account_stat.booking_id\';\r\n    if($event_count > 0){\r\n        $replace2 .= \' left join plugin_contacts3_notes n on i0.booking_item_id = n.link_id and n.table_link_id = 3 and n.`deleted` = 0\';\r\n    }\r\n    $replace3 = count($replace3) > 0 ? \' and (\' . implode(\' or \', $replace3) . \') \' : \'\';\r\n}\r\n\r\n$sql = $this->_sql;\r\n$sql = str_replace(\'__PHP1__\', $replace1, $sql);\r\n$sql = str_replace(\'__PHP2__\', $replace2, $sql);\r\n$sql = str_replace(\'__PHP3__\', $replace3, $sql);\r\n$this->_sql = $sql;\r\n' WHERE (`name`='Master Roll Call');
UPDATE `plugin_reports_reports` SET `action_event`='var $reportRows = $(\"#report_table tbody > tr\");\r\nvar $timeslotStatuses = $(\"[name*=timeslot_status]\");\r\nvar rows = [];\r\nfor (var i = 0 ; i < $reportRows.length ; ++i) {\r\n    var timeslotStatuses = $($reportRows[i]).find(\"select[name*=timeslot_status]\");\r\n	var plannedArrivals = $($reportRows[i]).find(\"input[name*=planned_arrival]\");\r\n	var plannedLeaves = $($reportRows[i]).find(\"input[name*=planned_leave]\");\r\n    var booking = {id: null, items: [], note: null, amount: null};\r\n    for (var j = 0 ; j < timeslotStatuses.length ; ++j) {\r\n        var timeslotStatus = $(timeslotStatuses[j]);\r\n        var params = /timeslot_status\\[(\\d+)\\]\\[(\\d+)\\]/.exec(timeslotStatus.attr(\'name\'));\r\n		var apply_absent_fee = $($reportRows[i]).find(\"input[name*=apply_absent_fee]\").prop(\"checked\") ? $($reportRows[i]).find(\"input[name*=apply_absent_fee]\").val() : 0;\r\n        if (params) {\r\n            booking.id = params[1];\r\n            booking.items.push({id: params[2], status: timeslotStatus.val(), planned_arrival: $(plannedArrivals[j]).val(), planned_leave: $(plannedLeaves[j]).val(), apply_absent_fee: apply_absent_fee});\r\n            booking.note = $($reportRows[i]).find(\"input[name*=note]\").val();\r\n            booking.transactionId = $($reportRows[i]).find(\"select[name*=transaction]\").val();\r\n            booking.amount = $($reportRows[i]).find(\"input[name*=amount]\").val();\r\n            rows.push(booking);\r\n        }\r\n    }\r\n}\r\n\r\nvar data = {json: JSON.stringify(rows)};\r\n$.post(\'/admin/bookings/ajax_rollcall_update\', data, function(response){\r\n    $(\"#generate_report\").click();alert(response);\r\n});' WHERE (`name`='Master Roll Call');
UPDATE `plugin_reports_reports` SET `custom_report_rules`='init();\r\nfunction init() {\r\n    $(\"#report_table tbody > tr\").each(function () {\r\n        var tr = this;\r\n        var bookingId = null;\r\n        var $timeslotStatuses = $(tr).find(\"[name*=timeslot_status]\");\r\n        var transactions = $(tr).find(\"var.transactions\");\r\n\r\n		$(tr).find(\"input[name*=planned_arrival], input[name*=planned_leave]\").datetimepicker({\r\n			datepicker : false,\r\n			format: \'H:i\',\r\n			formatTime: \'H:i\',\r\n			step: 5\r\n		});\r\n	\r\n		$(tr).find(\"input[name*=apply_absent_fee]\").on(\"change\", function(){\r\n			if (this.checked) {\r\n				$(tr).find(\"input[name*=amount]\").val(this.value);\r\n			} else {\r\n				$(tr).find(\"input[name*=amount]\").val($(tr).find(\"input[name*=amount]\").prop(\"defaultValue\"));\r\n			}\r\n		});\r\n   $(tr).find(\"input[name*=apply_absent_fee]\").each(function(){\r\n        if (this.checked) {\r\n            $(tr).find(\"input[name*=amount]\").val(this.value);\r\n        }\r\n   });\r\n	\r\n        $timeslotStatuses.each(function () {\r\n            if ($(this).data(\"attending\") == \"1\" || $(this).data(\"payg_apply_fees_when_absent\") == 1) {\r\n                var val = this.value;\r\n                var select = \'\';\r\n                bookingId = /timeslot_status\\[(\\d+)\\]\\[(\\d+)\\]/.exec(this.name)[1];\r\n                select =\r\n                    //(val != \'\' ? val : \'Absent\') +\r\n                    \'<select name=\"\' + this.name + \'\" style=\"width:100%;min-width:80px;height:1.5em;\">\' +\r\n                    \'<option value=\"plan\">Plan</option>\' +\r\n                    \'<option value=\"Present,Paid\" \' + (val.indexOf(\'Present\') != -1 && val.indexOf(\'Paid\') != -1 ? \'selected=\"selected\"\' : \'\') + \'>Present + Paid<\\/option>\' +\r\n                    \'<option value=\"Present\" \' + (val.indexOf(\'Present\') != -1 && val.indexOf(\'Paid\') == -1 ? \'selected=\"selected\"\' : \'\') + \'>Present + Unpaid<\\/option>\';\r\n                if ($(this).data(\"payg_apply_fees_when_absent\") == 1) {\r\n                    select += \'<option value=\"Paid\" \' + (val == \'Paid\' ? \'selected=\"selected\"\' : \'\') + \'>Absent<\\/option>\';\r\n                    select += \'<option value=\"\" \' + (val == \'\' ? \'selected=\"selected\"\' : \'\') + \'>Absent + Unpaid<\\/option>\';\r\n                } else {\r\n                    select += \'<option value=\"\" \' + (val == \'\' ? \'selected=\"selected\"\' : \'\') + \'>Absent<\\/option>\';\r\n                }\r\n                select += \'<option value=\"Late,Paid\" \' + (val.indexOf(\'Late\') != -1 && val.indexOf(\'Paid\') != -1 ? \'selected=\"selected\"\' : \'\') + \'>Late + Paid<\\/option>\' +\r\n                    \'<option value=\"Late\" \' + (val.indexOf(\'Late\') != -1 && val.indexOf(\'Paid\') == -1 ? \'selected=\"selected\"\' : \'\') + \'>Late + Unpaid<\\/option>\' +\r\n                    \'<option value=\"Early Departures,Paid\" \' + (val.indexOf(\'Early Departures\') != -1 && val.indexOf(\'Paid\') != -1 ? \'selected=\"selected\"\' : \'\') + \'>Early Departures + Paid<\\/option>\' +\r\n                    \'<option value=\"Early Departures\" \' + (val.indexOf(\'Early Departures\') != -1 && val.indexOf(\'Paid\') == -1 ? \'selected=\"selected\"\' : \'\') + \'>Early Departures + Unpaid<\\/option>\'\r\n                \'<\\/option>\';\r\n                $(this).replaceWith(select);\r\n            } else if ($(this).data(\'attending\') === undefined && $(this).data(\'payg_apply_fees_when_absent\') === undefined) {\r\n                return false;\r\n            } else {\r\n                $(this).replaceWith(\"Not Attending\");\r\n            }\r\n        });\r\n\r\n        var tselect = \'\';\r\n        if (transactions.length > 0 && transactions.html() != \'\') {\r\n            var transactionsData = transactions.html();\r\n            transactionsData = transactionsData.split(\'||\');\r\n            var defaultTransaction = -1;\r\n            for (var j = 0; j < transactionsData.length; ++j) {\r\n                transactionsData[j] = transactionsData[j].split(\'@@\');\r\n                if (transactionsData[j][1] == \'Booking - PAYG\') {\r\n                    defaultTransaction = j;\r\n                }\r\n            }\r\n            tselect = \'<select name=\"transaction[\' + bookingId + \']\" style=\"width:100%;min-width:80px;height:1.5em;\">\';\r\n            for (var j = 0; j < transactionsData.length; ++j) {\r\n                tselect += \'<option value=\"\' + transactionsData[j][0] + \'\">\' + transactionsData[j][0] + \' \' + transactionsData[j][1] + \' \' + transactionsData[j][2] + \'</option>\';\r\n            }\r\n            tselect += \'</select>\';\r\n        }\r\n        transactions.replaceWith(tselect);\r\n    });\r\n}\r\n\r\n$(\'#report_table_wrapper\').find(\'.dataTables_paginate > a\').on(\"click\", function () {\r\n    init();\r\n});\r\n' WHERE (`name`='Master roll Call');

