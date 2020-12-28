/*
ts:2020-07-23 02:17:00
*/

INSERT IGNORE INTO `engine_settings` (
    `variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`,
    `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`)
    values (
               'courses_discounts_image',
               'Discount Image',
                'courses',
                '1', '1', '0', '0', '0',
               'Show file updload and allow uploading images for discounts',
               'toggle_button', 'Courses', 'Model_Settings,on_or_off');
