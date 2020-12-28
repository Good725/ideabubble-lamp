/*
ts:2017-05-22 15:29:00
*/

INSERT INTO `engine_settings`
(`variable`,                        `name`,                     `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`,                                                             `type`,          `options`,                  `group`  ) VALUES
('account_managed_course_bookings', 'Account-managed bookings', 'courses',            '0',          '0',           '0',          '0',         '0',       'both',     'Enable bookings, which users can manage by logging into the site', 'toggle_button', 'Model_Settings,on_or_off', 'Courses');
