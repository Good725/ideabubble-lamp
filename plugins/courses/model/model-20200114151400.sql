/*
ts:2020-01-14 15:14:00
*/

INSERT IGNORE INTO `engine_settings` (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`,
                                      `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`)
VALUES ('only_show_primary_trainer_course_dropdown', 'Show primary trainer only for course schedule dropdowns on frontend.', 'courses', '0', '0', '0', '0', '0',
        'Only show the primary trainer for a schedule when displayed on the frontend',
        'toggle_button', 'Courses', 'Model_Settings,on_or_off');