/*
ts:2020-06-12 00:35:00
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
        'engine_enable_organisation_signup_flow',
        'Enable organisation sign up verification',
        'Enables verification flow on Organizations sign up ',
        'toggle_button',
        'User Registration',
        0, 'Model_Settings,on_or_off');

