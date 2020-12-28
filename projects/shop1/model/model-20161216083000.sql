/*
ts:2016-12-16 08:30:00
*/

UPDATE `engine_settings` SET
  `value_live` = '30 days',
  `value_stage` = '30 days',
  `value_test` = '30 days',
  `value_dev` = '30 days',
  `default` = '30 days'
WHERE
  `variable` = 'login_lifetime';

UPDATE `engine_settings` SET
  `value_live` = '1 day',
  `value_stage` = '1 day',
  `value_test` = '1 day',
  `value_dev` = '1 day',
  `default` = '1 day'
WHERE
  `variable` = 'login_idle_time';
