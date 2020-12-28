/*
ts:2016-03-05 17:51:00
*/

INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) values ('ipwatcher_treshold_day_url', 'Threshold (Requests/Day) Same Url', '1000', '1000', '1000', '1000', '1000', 'both', '', 'text', 'IP Watcher Settings', 0, '');
INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) values ('ipwatcher_treshold_hour_url', 'Threshold (Requests/Hour) Same Url', '100', '100', '100', '100', '100', 'both', '', 'text', 'IP Watcher Settings', 0, '');
INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`) values ('ipwatcher_treshold_minute_url', 'Threshold (Requests/Minute) Same Url', '20', '20', '20', '20', '20', 'both', '', 'text', 'IP Watcher Settings', 0, '');
