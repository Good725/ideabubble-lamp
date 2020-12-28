/*
ts:2020-02-27 10:32:00
*/

INSERT IGNORE INTO `engine_settings` (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`,
                                      `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`)
VALUES ('default_schedule_group_bookings', 'Default schedule group booking option', 'courses', '0', '0', '0', '0', '0',
        'When set to on, new schedules will have the group booking option enabled, otherwise it\'ll be off.',
        'toggle_button', 'Courses', 'Model_Settings,on_or_off');