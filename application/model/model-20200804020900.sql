/*
ts:2020-08-04 02:09:00
*/
INSERT IGNORE INTO `engine_settings`
(
    `variable`,
    `name`,
    `note`,
    `type`,
    `group`,
    `required`,
    `options`)
VALUES (
           'twilio_apply_code',
           'Apply Country Code for all numbers',
           'Apply Country Code for all numbers',
           'toggle_button',
           'Twilio Settings',
           0, 'Model_Settings,on_or_off');