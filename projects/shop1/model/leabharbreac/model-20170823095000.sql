/*
ts:2017-08-23 09:50:00
*/

UPDATE
  `engine_settings`
SET
  `value_live`  = 1,
  `value_stage` = 1,
  `value_test`  = 1,
  `value_dev`   = 1
WHERE
  `variable` = 'checkout_gift_option'
;
