/*
ts:2020-05-12 17:42:00
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
VALUES ('cart_prepay_heading_enabled',
        'Show Pre-Pay and PAYG headings in the cart',
        'bookings',
        '0',
        '0',
        '0',
        '0',
        '0',
        'Show Pre-Pay and PAYG headings in the cart',
        'toggle_button',
        'Bookings',
        'Model_Settings,on_or_off');
