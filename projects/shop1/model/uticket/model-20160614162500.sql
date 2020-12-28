/*
ts:2016-06-07 14:40:00
*/

/* customer will need to provide their own settins on live */
UPDATE `engine_settings` SET `value_stage`='1', `value_test`='1', `value_dev` = '1' WHERE `variable`='slaask_api_access';
UPDATE `engine_settings` SET
  `value_stage`='602bc3d4c44443716cf0aa35747602f1',
  `value_test` ='602bc3d4c44443716cf0aa35747602f1',
  `value_dev`  ='602bc3d4c44443716cf0aa35747602f1'
WHERE `variable`='slaask_api_key';
