/*
ts:2018-08-14 14:00:00
*/

INSERT INTO `engine_settings` (`variable`, `name`, `note`, `type`, `group`, `options`) VALUES
('login_background_image', 'log-in form background image', 'Image to be used in the background of the log-in form.', 'select', 'Engine', 'Model_Media,get_background_images_as_options');

UPDATE `engine_settings` SET `default`     = '<h3>Log in to your account</h3>' WHERE `variable` = 'login_form_intro_text';
UPDATE `engine_settings` SET `value_dev`   = '<h3>Log in to your account</h3>' WHERE `variable` = 'login_form_intro_text' AND (TRIM(`value_dev`)   = '' OR `value_dev`   IS NULL);
UPDATE `engine_settings` SET `value_test`  = '<h3>Log in to your account</h3>' WHERE `variable` = 'login_form_intro_text' AND (TRIM(`value_test`)  = '' OR `value_test`  IS NULL);
UPDATE `engine_settings` SET `value_stage` = '<h3>Log in to your account</h3>' WHERE `variable` = 'login_form_intro_text' AND (TRIM(`value_stage`) = '' OR `value_stage` IS NULL);
UPDATE `engine_settings` SET `value_live`  = '<h3>Log in to your account</h3>' WHERE `variable` = 'login_form_intro_text' AND (TRIM(`value_live`)  = '' OR `value_live`  IS NULL);

UPDATE `engine_settings` SET `default`     = '<h3>Sign up to your account</h3>' WHERE `variable` = 'signup_form_intro_text';
UPDATE `engine_settings` SET `value_dev`   = '<h3>Sign up to your account</h3>' WHERE `variable` = 'signup_form_intro_text' AND (TRIM(`value_dev`)   = '' OR `value_dev`   IS NULL);
UPDATE `engine_settings` SET `value_test`  = '<h3>Sign up to your account</h3>' WHERE `variable` = 'signup_form_intro_text' AND (TRIM(`value_test`)  = '' OR `value_test`  IS NULL);
UPDATE `engine_settings` SET `value_stage` = '<h3>Sign up to your account</h3>' WHERE `variable` = 'signup_form_intro_text' AND (TRIM(`value_stage`) = '' OR `value_stage` IS NULL);
UPDATE `engine_settings` SET `value_live`  = '<h3>Sign up to your account</h3>' WHERE `variable` = 'signup_form_intro_text' AND (TRIM(`value_live`)  = '' OR `value_live`  IS NULL);

UPDATE `engine_settings` SET `default`     = '<p>By signing up, you agree to the <a href="/privacy-policy.html"><strong>privacy policy</strong></a> and <a href="/terms-of-use.html"><strong>terms of use</strong></a>.</p>' WHERE `variable` = 'sign_up_disclaimer_text';
UPDATE `engine_settings` SET `value_dev`   = '<p>By signing up, you agree to the <a href="/privacy-policy.html"><strong>privacy policy</strong></a> and <a href="/terms-of-use.html"><strong>terms of use</strong></a>.</p>' WHERE `variable` = 'sign_up_disclaimer_text' AND (TRIM(`value_dev`)   = '' OR `value_dev`   IS NULL);
UPDATE `engine_settings` SET `value_test`  = '<p>By signing up, you agree to the <a href="/privacy-policy.html"><strong>privacy policy</strong></a> and <a href="/terms-of-use.html"><strong>terms of use</strong></a>.</p>' WHERE `variable` = 'sign_up_disclaimer_text' AND (TRIM(`value_test`)  = '' OR `value_test`  IS NULL);
UPDATE `engine_settings` SET `value_stage` = '<p>By signing up, you agree to the <a href="/privacy-policy.html"><strong>privacy policy</strong></a> and <a href="/terms-of-use.html"><strong>terms of use</strong></a>.</p>' WHERE `variable` = 'sign_up_disclaimer_text' AND (TRIM(`value_stage`) = '' OR `value_stage` IS NULL);
UPDATE `engine_settings` SET `value_live`  = '<p>By signing up, you agree to the <a href="/privacy-policy.html"><strong>privacy policy</strong></a> and <a href="/terms-of-use.html"><strong>terms of use</strong></a>.</p>' WHERE `variable` = 'sign_up_disclaimer_text' AND (TRIM(`value_live`)  = '' OR `value_live`  IS NULL);

/* Add the "privacy policy" and "terms of use" pages, if they do not already exist */
INSERT IGNORE INTO `plugin_pages_pages` (`name_tag`, `title`, `content`,`date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`)
SELECT
  'privacy-policy',
  'Privacy policy',
  '<h1>Privacy policy</h1><p>Please put your content here.</p>',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0',
  '1',
  (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'content' LIMIT 1),
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'DEFAULT' LIMIT 1)
FROM `plugin_pages_pages`
WHERE NOT EXISTS (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` IN ('privacy-policy', 'privacy-policy.html') AND `deleted` = 0)
LIMIT 1;

INSERT IGNORE INTO `plugin_pages_pages` (`name_tag`, `title`, `content`,`date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`)
SELECT
  'terms-of-use',
  'Terms of use',
  '<h1>Terms of use</h1><p>Please put your content here.</p>',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0',
  '1',
  (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'content' LIMIT 1),
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'DEFAULT' LIMIT 1)
FROM `plugin_pages_pages`
WHERE NOT EXISTS (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` IN ('terms-of-use', 'terms-of-use.html') AND `deleted` = 0)
LIMIT 1;
