/*
ts: 2019-05-01 18:00:00
*/


UPDATE
  `plugin_messaging_notification_templates`
SET
  `message` = "A new time off request has been submitted.<br />
\n<br />
\nName: $name<br />
\nDepartment: $department<br />
\nType: $type<br />
\nPeriod: $period<br />
\nStart time: $start_date_time<br />
\nEnd time: $end_date_time<br />
\nDuration: $duration<br />
\nNotes: $note"
WHERE
  `name` = 'timeoff-request-created'
;
