/*
ts:2019-11-22 12:31:00
*/
UPDATE
  `engine_settings`
SET
  `value_dev`   = 'Registration fee: $1',
  `value_test`  = 'Registration fee: $1',
  `value_stage` = 'Registration fee: $1',
  `value_live`  = 'Registration fee: $1'
WHERE
  `variable`    = 'checkout_deposit_text'
;