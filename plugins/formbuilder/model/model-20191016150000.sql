/*
ts:2019-10-16 15:00:00
*/

-- Add CAPTCHA to forms that don't have a CAPTCHA
-- Enable CAPTCHA
-- Set version to 2
-- Prepend the last <li></li> in the form with another <li></li> containing the CAPTCHA object
UPDATE
  `plugin_formbuilder_forms`
SET
  `date_modified`   = CURRENT_TIMESTAMP,
  `captcha_enabled` = 1,
  `captcha_version` = 2,
  `fields`          = REPLACE(
    `fields`,
     SUBSTRING_INDEX(`fields`, '<li>', -1),
     CONCAT('<span>[CAPTCHA]</span></li><li>', SUBSTRING_INDEX(`fields`, '<li>', -1))
  )
WHERE
  `fields` not like '%[CAPTCHA%'
AND
  /* Excluded forms that are not be given a CAPTCHA */
  `form_name` NOT IN ('new_project_enquiry_trigger', 'Newsletter subscription', 'Subscribe to Newsletter')
;

UPDATE `engine_settings` SET `value_live` = 1, `value_stage` = 1, `value_test` = '1', `value_dev` = '1' WHERE `variable` = 'newsletter_subscription_captcha';
