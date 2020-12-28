/*
ts:2020-08-04 01:08:00
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
           'mandatory_two_factor_authorization',
           'Two Factor Authorization for New Users',
           'Two Factor Authorization for New Users and By Default',
           'toggle_button',
           'User Registration',
           0, 'Model_Settings,on_or_off');
 UPDATE `engine_settings` SET `name` = 'Two Step Authorization Enable',
                              `note` = 'Two Factor Authorization Enabled for users, for New Users it is be Default and forced to SMS',
                              `variable` = 'two_step_authorization',
                              `value_live` = 0,
                              `value_stage` = 0,
                              `value_test` = 0,
                              `value_dev` = 0,
                              `default` = 0
 WHERE `variable` = 'mandatory_two_factor_authorization';