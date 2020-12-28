/*
ts:2017-06-22 16:30:00
*/

INSERT INTO `engine_settings`(`variable`, `name`, `note`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `type`, `group`, `options`) VALUES
(
  'privacy_policy_page',
  'Privacy Policy Page',
  '',
  (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` IN ('privacy-policy.html', 'privacy-policy') LIMIT 1),
  (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` IN ('privacy-policy.html', 'privacy-policy') LIMIT 1),
  (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` IN ('privacy-policy.html', 'privacy-policy') LIMIT 1),
  (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` IN ('privacy-policy.html', 'privacy-policy') LIMIT 1),
  (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` IN ('privacy-policy.html', 'privacy-policy') LIMIT 1),
  'combobox',
  'Website',
  'Model_Pages,get_pages_as_options'
);

INSERT INTO `engine_settings`
  (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`)
VALUES
  ('newsletter_subscription_captcha', 'Newsletter form CAPTCHA', '0', '0', '0', '0', '0', 'Use a CAPTCHA in the newsletter subscription form', 'toggle_button', 'Forms', 'Model_Settings,on_or_off');
