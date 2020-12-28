/*
ts:2019-05-14 10:01:00
*/

UPDATE
  `engine_settings`
SET
  `value_dev`   = 'bcfe',
  `value_test`  = 'bcfe',
  `value_stage` = 'bcfe',
  `value_live`  = 'bcfe'
WHERE
  `variable` = 'checkout_customization'
;