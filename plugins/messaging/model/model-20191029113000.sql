/*
ts:2019-10-29 11:30:00
*/

INSERT INTO `engine_settings`
    (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`)
VALUES (
    'twilio_default_country_code',
    'Default country code',
    'messaging',
    '+353',
    '+353',
    '+353',
    '+353',
    '+353',
    'If an SMS number does not contain a country code, automatically use this one.',
    'text',
    'Twilio Settings'
);
