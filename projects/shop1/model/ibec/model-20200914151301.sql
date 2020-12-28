/*
ts:2020-09-14 15:13:01
*/

UPDATE engine_settings
  SET value_live=1,value_stage=1,value_test=1,value_dev=1
  WHERE `variable`='booking_invoice_number_is_mandatory';
