/*
ts:2018-08-28 11:01:00
*/

-- Insert the make-a-donation page, if it doesn't exist
INSERT INTO
  `plugin_pages_pages` (`name_tag`, `title`, `content`, `date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`)
SELECT
  'make-a-donation',
  'Make a donation',
  '<h1>Make a donation</h1>
\n<p>Choose a donation type.</p>
\n<div class="formrt">{form-donations}</div>',
   CURRENT_TIMESTAMP,
   CURRENT_TIMESTAMP,
   (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
   (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
   1,
   0,
   1,
   (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'Content' AND `deleted` = 0 LIMIT 1),
   (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'Default')
FROM
  `plugin_pages_pages`
WHERE NOT EXISTS
  (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` IN ('make-a-donation', 'make-a-donation.html') AND `deleted` = 0)
LIMIT 1;


UPDATE
  `plugin_pages_pages`
SET
  `content` = CONCAT(`content`, '<p>Choose a donation type.</p>\n<div class="formrt">{form-donations}</div>')
WHERE
  `name_tag` = 'make-a-donation'
AND
  `content` NOT LIKE '%{form-donations}%'
AND
  `deleted` = 0
;


UPDATE
  `plugin_formbuilder_forms`
SET
  `use_stripe` = 1
WHERE
  `form_name` = 'donations'
;