/*
ts:2019-05-27 14:00:00
*/

-- Add the "About Us" and "Terms and Conditions" footer items, if they don't already exist
INSERT INTO `plugin_menus`
  (`category`, `title`, `link_tag`, `link_url`, `has_sub`, `parent_id`, `menu_order`, `publish`, `deleted`, `date_modified`, `date_entered`, `created_by`, `modified_by`, `menus_target`, `image_id`)
SELECT
  'footer',
  'About Us',
  '0',
  '',
  '1',
  '0',
  '1',
  '1',
  '0',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '_self',
  '0'
FROM `plugin_menus`
WHERE NOT EXISTS
  (SELECT `id` FROM `plugin_menus` WHERE `category` = 'footer' AND `title` = 'About Us' AND `has_sub` = '1' AND `deleted` = 0)
LIMIT 1
;

INSERT INTO `plugin_menus`
  (`category`, `title`, `link_tag`, `link_url`, `has_sub`, `parent_id`, `menu_order`, `publish`, `deleted`, `date_modified`, `date_entered`, `created_by`, `modified_by`, `menus_target`, `image_id`)
SELECT
  'footer',
  'Terms & Conditions',
  '0',
  '',
  '1',
  '0',
  '1',
  '1',
  '0',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '_self',
  '0'
FROM `plugin_menus`
WHERE NOT EXISTS
  (SELECT `id` FROM `plugin_menus` WHERE `category` = 'footer' AND `title` = 'Terms & Conditions' AND `has_sub` = '1' AND `deleted` = 0)
LIMIT 1
;

-- Update the other footer items to be children of "About Us" and "Terms & Conditions"
UPDATE
  `plugin_menus`
SET
  `parent_id` = (SELECT `id` FROM (SELECT * FROM `plugin_menus`) `pm` WHERE `category` = 'footer' AND `title` = 'About Us' AND `has_sub` = 1 AND `deleted` = 0)
WHERE
  `category` = 'footer'
AND
  `deleted` = 0
AND
  `has_sub` = 0
AND
  `title` IN ('Home', 'About', 'News', 'Create Event', 'Support')
;

UPDATE
  `plugin_menus`
SET
  `parent_id` = (SELECT `id` FROM (SELECT * FROM `plugin_menus`) `pm` WHERE `category` = 'footer' AND `title` = 'Terms & Conditions' AND `has_sub` = 1 AND `deleted` = 0)
WHERE
  `category` = 'footer'
AND
  `deleted` = 0
AND
  `has_sub` = 0
AND
  `title` IN ('Pricing & Payment', 'Terms & Conditions', 'How It Works', 'Sell Tickets Online', 'Ticket Buyer Help', 'Event Organiser Help', 'Event Organiser Tips')
;