/*
ts:2020-05-13 01:52:00
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
VALUES ('cart_special_requirements_enable',
        'Show Special Requirements in the cart',
        'bookings',
        '0',
        '0',
        '0',
        '0',
        '0',
        'Show Special Requirements in the cart',
        'toggle_button',
        'Checkout',
        'Model_Settings,on_or_off');