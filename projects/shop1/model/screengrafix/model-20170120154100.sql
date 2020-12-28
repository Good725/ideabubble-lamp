/*
ts:2017-01-20 15:41:00
*/

UPDATE
  `engine_settings`
SET
  `value_live`  = '0',
  `value_stage` = '0',
  `value_test`  = '0',
  `value_dev`   = '0'
WHERE
  `variable`='sign_builder_select_quantity_step'
;
