/*
ts:2019-05-14 10:01:00
*/

UPDATE
  `engine_settings`
SET
  `value_dev`   = 'sls',
  `value_test`  = 'sls',
  `value_stage` = 'sls',
  `value_live`  = 'sls'
WHERE
  `variable` = 'checkout_customization'
;