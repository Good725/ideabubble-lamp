/*
ts:2016-12-26 10:45:00
*/

UPDATE `engine_settings`
  SET value_test = '0000000000', value_dev = '0000000000', value_stage = '0000000000'
  WHERE `variable` = 'messaging_override_recipients_sms';

UPDATE `engine_settings`
  SET value_test = '1', value_dev = '1', value_stage = '1'
  WHERE `variable` = 'messaging_override_recipients';

UPDATE `engine_settings`
  SET value_test = '', value_dev = '', value_stage = ''
  WHERE `variable` in ('twilio_account_sid', 'twilio_auth_token', 'twilio_phone_number');
