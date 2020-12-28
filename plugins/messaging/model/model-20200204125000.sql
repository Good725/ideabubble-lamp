/*
ts:2020-02-04 12:50:00
*/


INSERT IGNORE INTO `engine_settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`,
                               `location`, `note`, `type`, `group`, `required`, `options`)
values ('phpmail_from_email', 'PHPmail From Email', 'testing@websitecms.ie', 'testing@websitecms.ie',
        'testing@websitecms.ie', 'testing@websitecms.ie', 'testing@websitecms.ie', 'both', '', 'text',
        'Phpmail Settings', 0, '');