/*
ts:2019-04-29 16:00:00
*/

-- Add the "donation thank you" page, if it does not already exist.
-- Unpublished by default. Sites that need it can publish.
INSERT IGNORE INTO
  `plugin_pages_pages` (`name_tag`, `title`, `content`,`date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`)
SELECT
  'donation-thank-you',
  'Thank you for your donation',
  '<h1>Thank you for your donation</h1>',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '0',
  '0',
  '1',
  (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'pay_online' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'DEFAULT' LIMIT 1)
FROM `plugin_pages_pages`
WHERE NOT EXISTS (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` = 'donation-thank-you' AND `deleted` = 0)
LIMIT 1;

-- Setting to select which "thank you" page to use for donations
INSERT INTO `engine_settings`(`variable`, `name`, `note`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `type`, `group`, `options`) VALUES
(
  'donation_thank_you_page',
  'Donation thank you Page',
  'Page the user is sent to after completing a purchase',
  (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` = 'donation-thank-you' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` = 'donation-thank-you' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` = 'donation-thank-you' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` = 'donation-thank-you' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` = 'donation-thank-you' AND `deleted` = 0 LIMIT 1),
  'combobox',
  'Donations',
  'Model_Pages,get_pages_as_options'
);

-- Flag forms as being for donations, so that the code knows which "thank you" page setting to look at.
UPDATE
  `plugin_formbuilder_forms`
SET
  `fields` = CONCAT('<input type="hidden" name="is_donation" value="1" />\n', `fields`)
WHERE
  (`form_name` = 'donations' OR (`form_name` = 'PaymentFormQuickOrder' AND `fields` LIKE '%Donation%'))
AND
  `fields` NOT LIKE '%name="is_donation"%'
;