/*
ts: 2019-08-20 17:30:00
*/

UPDATE
  `engine_settings`
SET
  `value_live`  = '$message_body',
  `value_stage` = '$message_body',
  `value_test`  = '$message_body',
  `value_dev`   = '$message_body'
WHERE
  `variable` = 'email_wrapper_html'
;