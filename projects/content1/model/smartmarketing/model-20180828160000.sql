/*
ts:2018-08-28 16:00:00
*/

INSERT IGNORE INTO
  `plugin_formbuilder_forms` (`form_name`, `action`, `method`, `fields`, `publish`, `deleted`, `date_modified`, `captcha_enabled`, `form_id`)
VALUES
(
 'pay_online',
 'frontend/formprocessor/',
 'POST',
 '',
 '1',
 '0',
 CURRENT_TIMESTAMP,
 '0',
 'payment_form'
);

UPDATE
  `plugin_formbuilder_forms`
SET
  `use_stripe` = 1,
  `fields`     = '<input type=\"hidden\" name=\"item_name\" value=\"Quick Order\">
<input type=\"hidden\" name=\"event\" value=\"contact-form\">
<input type=\"hidden\" name=\"trigger\" value=\"custom_form\" id=\"trigger\">
<input type=\"hidden\" name=\"payment_method\" value=\"Stripe\" />
<li>
    <label for=\"payment_total\">Amount (&euro;)</label>
    <input type=\"text\" name=\"payment_total\" class=\"validate[required]\" id=\"payment_total\" />
</li>
<li>
    <label for=\"payment_form_email\">Email</label>
    <input type=\"text\" name=\"email\" class=\"validate[required]\" id=\"email\" />
</li>
<li>
    <label for=\"payment_form_name\">Name</label>
    <input type=\"text\" name=\"payment_form_name\" class=\"validate[required]\" id=\"payment_form_name\" />
</li>
<li>
    <label for=\"payment_form_phone\">Phone</label>
    <input type=\"text\" name=\"phone\" id=\"payment_form_phone\" />
</li>
<li>
    <label for=\"payment_form_address\">Address</label>
    <textarea name=\"address\" id=\"payment_form_address\"></textarea>
</li>
<li>
    <label for=\"payment_form_terms\">I have read and agree to the <a href=\"/terms-and-conditions.html\" target=\"_blank\">terms and conditions</a>.</label>
    <input type=\"checkbox\" name=\"terms\" class=\"validate[required]\" id=\"payment_form_terms\" />
</li>
'
WHERE
  `form_name` = 'pay_online'
;

-- Insert the make-a-donation page, if it doesn't exist
INSERT INTO
  `plugin_pages_pages` (`name_tag`, `title`, `content`, `date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`)
SELECT
  'pay-online',
  'Pay online',
  '<h1>Make a payment</h1>
\n<div class="formrt">{form-pay_online}</div>',
   CURRENT_TIMESTAMP,
   CURRENT_TIMESTAMP,
   (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
   (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
   1,
   0,
   1,
   (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'Content' AND `deleted` = 0 AND `template_id` = (SELECT `id` FROM `engine_site_templates` WHERE `stub` = '03') LIMIT 1),
   (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'Default')
FROM
  `plugin_pages_pages`
WHERE NOT EXISTS
  (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` IN ('pay-online', 'pay-online.html') AND `deleted` = 0)
LIMIT 1;

-- If the pay-online page already exists, update it to include the form.
UPDATE
  `plugin_pages_pages`
SET
  `content` = CONCAT(`content`, '\n<div class="formrt">{form-pay_online}</div>')
WHERE
  `name_tag` = 'pay-online'
AND
  `content` NOT LIKE '%{form-pay_online}%'
AND
  `deleted` = 0
;
