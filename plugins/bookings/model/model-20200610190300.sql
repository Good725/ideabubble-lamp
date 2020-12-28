/*
ts: 2020-06-10 19:03:00
*/
UPDATE `plugin_reports_reports` SET `custom_report_rules`='init();\r\n
function init() {\r\n
   $(\"#report_table tbody > tr\").each(function () {\r\n
      var tr = this;\r\n
       var bookingId = null;\r\n
       var $timeslotStatuses = $(tr).find(\"[name*=timeslot_status]\");\r\n
        var transactions = $(tr).find(\"var.transactions\");\r\n
\r\n
		$(tr).find(\"input[name*=planned_arrival], input[name*=planned_leave]\").datetimepicker({\r\n
			datepicker : false,\r\n
			format: \'H:i\',\r\n
			formatTime: \'H:i\',\r\n
			step: 5\r\n
		});\r\n
\r\n
\r\n
        $timeslotStatuses.each(function () {\r\n
           if ($(this).data(\"attending\") == \"1\" || $(this).data(\"payg_apply_fees_when_absent\") == 1) {\r\n
                var val = this.value;\r\n
                var select = \'\';\r\n
                bookingId = /timeslot_status\\[(\\d+)\\]\\[(\\d+)\\]/.exec(this.name)[1];\r\n
                select =\r\n
                   \'<select name=\"\' + this.name + \'\" style=\"width:100%;min-width:80px;height:1.5em;\">\' +\r\n
                       \'<option value=\"Present\" \' + (val.indexOf(\'Present\') != -1 ? \'selected=\"selected\"\' : \'\') + \'>Present<\\/option>\' +\r\n
                       \'<option value=\"\" \' + (val == \'\' || val.indexOf(\'Absent\') != -1  ? \'selected=\"selected\"\' : \'\') + \'>Absent<\\/option>\' +\r\n
                       \'<option value=\"Late\" \' + (val.indexOf(\'Late\') != -1 ? \'selected=\"selected\"\' : \'\') + \'>Late<\\/option>\' +\r\n
                       \'<option value=\"Early Departures\" \' + (val.indexOf(\'Early Departures\') != -1 ? \'selected=\"selected\"\' : \'\') + \'>Early Departures<\\/option>\' +\r\n
                 \'<\\/select>\';\r\n
                $(this).replaceWith(select);\r\n
          } else if ($(this).data(\'attending\') === undefined && $(this).data(\'payg_apply_fees_when_absent\') === undefined) {\r\n
                return false;\r\n
           } else {\r\n
              $(this).replaceWith(\"Not Attending\");\r\n
            }\r\n
      });\r\n
\r\n
       var tselect = \'\';\r\n
       if (transactions.length > 0 && transactions.html() != \'\') {\r\n
           var transactionsData = transactions.html();\r\n
           transactionsData = transactionsData.split(\'||\');\r\n
           var defaultTransaction = -1;\r\n
          for (var j = 0; j < transactionsData.length; ++j) {\r\n
               transactionsData[j] = transactionsData[j].split(\'@@\');\r\n
               if (transactionsData[j][1] == \'Booking - PAYG\') {\r\n
                    defaultTransaction = j;\r\n
               }\r\n
            }\r\n
           tselect = \'<select name=\"transaction[\' + bookingId + \']\" style=\"width:100%;min-width:80px;height:1.5em;\">\';\r\n
            for (var j = 0; j < transactionsData.length; ++j) {\r\n
                tselect += \'<option value=\"\' + transactionsData[j][0] + \'\">\' + transactionsData[j][0] + \' \' + transactionsData[j][1] + \' \' + transactionsData[j][2] + \'</option>\';\r\n
           }\r\n
            tselect += \'</select>\';\r\n
        }\r\n
       transactions.replaceWith(tselect);\r\n
   });\r\n
}\r\n
\r\n
$(\'#report_table_wrapper\').find(\'.dataTables_paginate > a\').on(\"click\", function () {\r\n
    init();\r\n
});\r\n
\r\n' WHERE (`name`='Roll Call Attendance');
