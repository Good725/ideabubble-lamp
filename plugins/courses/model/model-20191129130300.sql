/*
ts:2019-11-29 13:03:00
*/

INSERT IGNORE INTO `engine_settings` (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`,
                                      `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`)
VALUES ('schedule_enable_invoice', 'Enable schedule invoice', 'courses', '0', '0', '0', '0', '0',
        'Disable the invoice option for courses, if turned on invoice will be enabled by default in the course as well.',
        'toggle_button', 'Courses', 'Model_Settings,on_or_off');