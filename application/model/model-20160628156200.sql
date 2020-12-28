/*
ts:2016-06-28 16:20:00
*/

/* Update the CAPTCHA setting to just apply to the front end. */
UPDATE `engine_settings`
SET
    `name`    = 'Enable on Front End',
    `default` = '1',
    `note`    = 'Check this box to enable CAPTCHAs s on the front end of the site.',
    `type`    = 'toggle_button',
    `options` = 'Model_Settings,on_or_off'
WHERE `variable` = 'captcha_enabled';

/* type = 'checkbox' used the strings 'TRUE' and 'FALSE'. type = 'toggle_button' uses 1 and 0 */
UPDATE `engine_settings` SET `value_live`  = 1 WHERE `variable` = 'captcha_enabled' AND `value_live`  = 'TRUE';
UPDATE `engine_settings` SET `value_stage` = 1 WHERE `variable` = 'captcha_enabled' AND `value_stage` = 'TRUE';
UPDATE `engine_settings` SET `value_test`  = 1 WHERE `variable` = 'captcha_enabled' AND `value_test`  = 'TRUE';
UPDATE `engine_settings` SET `value_dev`   = 1 WHERE `variable` = 'captcha_enabled' AND `value_dev`   = 'TRUE';
UPDATE `engine_settings` SET `value_live`  = 0 WHERE `variable` = 'captcha_enabled' AND `value_live`  = 'FALSE';
UPDATE `engine_settings` SET `value_stage` = 0 WHERE `variable` = 'captcha_enabled' AND `value_stage` = 'FALSE';
UPDATE `engine_settings` SET `value_test`  = 0 WHERE `variable` = 'captcha_enabled' AND `value_test`  = 'FALSE';
UPDATE `engine_settings` SET `value_dev`   = 0 WHERE `variable` = 'captcha_enabled' AND `value_dev`   = 'FALSE';

/* Add a separate CAPTCHA setting for the backend */
INSERT INTO `engine_settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`) VALUES
(
  'cms_captcha_enabled',
  'Enable on Back End',
  '0',
  '0',
  '0',
  '0',
  '0',
  'Check this box to enable CAPTCHAs on the backend of the site.',
  'toggle_button',
  'Captcha',
  'Model_Settings,on_or_off'
);

/* Add modification date column to the users table */
ALTER IGNORE TABLE `engine_users`
ADD COLUMN `date_modified` TIMESTAMP NULL  AFTER `can_login` ;
