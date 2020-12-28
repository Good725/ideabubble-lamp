/*
ts:2020-05-25 17:04:00
*/

INSERT INTO `engine_settings` (
    `variable`,
    `name`,
    `linked_plugin_name`,
    `value_live`,
    `value_stage`,
    `value_test`,
    `value_dev`,
    `default`,
    `note`,
    `type`,
    `group`,
    `options`)
VALUES ('duration_in_checkout',
        'Show Course Duration in the checkout sidebar',
        'bookings',
        '0',
        '0',
        '0',
        '0',
        '0',
        'Show Course Duration in the checkout sidebar',
        'toggle_button',
        'Checkout',
        'Model_Settings,on_or_off');