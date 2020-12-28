/*
ts:2020-01-30 11:12:00
*/

INSERT IGNORE INTO `engine_settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`,
                               `location`, `note`, `type`, `group`, `required`, `options`)
values ('twilio_account_sid', 'Twilio Account SID', '', '', '', '', '', 'both', '', 'text', 'Twilio Settings', 0, '');

INSERT IGNORE INTO `engine_settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`,
                               `location`, `note`, `type`, `group`, `required`, `options`)
values ('twilio_auth_token', 'Twilio Auth Token', '', '', '', '', '', 'both', '', 'text', 'Twilio Settings', 0, '');

INSERT IGNORE INTO `engine_settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`,
                               `location`, `note`, `type`, `group`, `required`, `options`)
values ('twilio_phone_number', 'Twilio Phone Number', '', '', '', '', '', 'both', '', 'text', 'Twilio Settings', 0, '');
