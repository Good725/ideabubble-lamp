/*
ts:2016-04-11 19:46:00
*/

ALTER TABLE engine_cron_tasks ADD COLUMN `send_email_on_complete` TINYINT DEFAULT 0;
INSERT INTO `engine_settings`
  (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('cron_email', 'Cron Email', 'support@ideabubble.ie', 'staging@websitecms.ie', 'staging@websitecms.ie', '', '', 'both', '', 'text', 'Cron', 0, '');

