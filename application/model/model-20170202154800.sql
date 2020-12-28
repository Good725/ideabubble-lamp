/*
ts:2017-02-02 15:48:00
*/

DELETE FROM `engine_settings` WHERE `variable` = 'website_frontend_login_url';

INSERT INTO `engine_settings`
  (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`)
  VALUES
  ('website_frontend_register_role', 'Front End Registration Role', 'External User', 'External User', 'External User',  'External User',  'External User', 'External User', 'text', 'User Registration');
