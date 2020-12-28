/*
ts:2019-04-11 16:10:00
*/

INSERT INTO `engine_settings` (`variable`, `name`, `location`, `note`, `type`, `readonly`, `group`, `required`, `options`, `config_overwrite`)
VALUES ('disable_activity_tracking_items', 'Disable Activity Tracking Items', 'both', 'Disable the Activity Tracking Items that you do not wish to track on your site.', 'multiselect', '0', 'Activity Tracking', '0', 'Model_Activity,get_all_activity_item_types_for_settings', '0');

INSERT INTO `engine_settings` (`variable`, `name`, `location`, `note`, `type`, `readonly`, `group`, `options`, `config_overwrite`)
VALUES ('disable_activity_tracking_actions', 'Disable Activity Tracking Actions', 'both', 'Disable the Activity Tracking Actions that you do not wish to track on your site', 'multiselect', '0', 'Activity Tracking', 'Model_Activity,get_all_activity_actions_for_settings', '0');

UPDATE `engine_settings`
SET `group` = 'Activity Tracking'
WHERE `variable` = 'track_activities';

