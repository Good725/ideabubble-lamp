/*
ts:2020-07-24 15:39:00
*/
INSERT INTO `engine_settings`
    (
       `linked_plugin_name`,
       `variable`,
       `name`,
       `value_live`,
       `value_stage`,
       `value_test`,
       `value_dev`,
       `default`,
       `location`,
       `note`,
       `type`,
       `group`,
       `required`,
       `options`) VALUES (
            'navapi',
            'navision_api_booking_outstanding',
            'Use Navision Booking Outstanding', 0, 0, 0,  0,  0,
            'both',
            'Use Navision Booking Outstanding',
            'toggle_button',
            'NAVISION API',
             0,
             'Model_Settings,on_or_off');
