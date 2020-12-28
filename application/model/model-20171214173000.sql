/*
ts:2017-12-14 17:30:00
*/

INSERT INTO `engine_settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `type`, `group`, `options`, `note`) VALUES
  ('facebook_pixel_enabled_sitewide', 'Enable Facebook Pixel sitewide', '0', '0', '0', '0', '0', 'toggle_button', 'Facebook Pixel', 'Model_Settings,on_or_off', 'Set up a Facebook Pixel campaign across the entire site'),
  ('facebook_pixel_enabled_per_user', 'Enable Facebook Pixel per user', '0', '0', '0', '0', '0', 'toggle_button', 'Facebook Pixel', 'Model_Settings,on_or_off', 'Allow individual users to set up their own Facebook Pixel campaigns'),
  ('facebook_pixel_code',             'Facebook Pixel code',            '',  '',  '',  '',  '',  'text',          'Facebook Pixel', '',                         '')
;

ALTER TABLE `engine_users`
ADD COLUMN `facebook_pixel_enabled` INT(1)      NOT NULL DEFAULT 0  AFTER `register_source` ,
ADD COLUMN `facebook_pixel_code`    VARCHAR(45) NULL                AFTER `facebook_pixel_enabled` ;
