/*
ts:2020-06-02 15:30:00
*/

DELIMITER ;;

-- Insert the "subscribe" page, if it doesn't already exist
INSERT IGNORE INTO `plugin_pages_pages` (`name_tag`, `title`, `content`,`date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`)
SELECT
  'subscribe',
  'Newsletter signup',
  '<h1>Newsletter signup</h1>',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '1',
  '1',
  '0',
  '1',
  (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'content_wide' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'DEFAULT' LIMIT 1)
FROM `plugin_pages_pages`
WHERE NOT EXISTS (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` IN ('subscribe', 'subscribe.html') AND `deleted` = 0)
LIMIT 1;;

-- Update the "subscribe" page content.
UPDATE
  `plugin_pages_pages`
SET
  `name_tag`      = 'subscribe',
  `title`         = 'Mailing list signup',
  `layout_id`     = (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'content_wide' AND `deleted` = 0 LIMIT 1),
  `last_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = 1,
  `content`       = '<div class="formrt formrt-vertical formrt-raised form-contact_us">{form-Newsletter subscription}</div>

'
WHERE
  `name_tag` IN ('subscribe', 'subscribe.html');;


-- Update the form.
UPDATE
  `plugin_formbuilder_forms`
SET
  `date_modified` = CURRENT_TIMESTAMP,
  `form_id` = 'newsletter_subscription',
  `fields` = '
\n<input type=\"hidden\" name=\"subject\"         value=\"Newsletter Sign Up\">
\n<input type=\"hidden\" name=\"business_name\"   value=\"Ibec\" />
\n<input type=\"hidden\" name=\"redirect\"        value=\"thank-you-for-subscribing\" />
\n<input type=\"hidden\" name=\"trigger\"         value=\"add_to_list\" />
\n<input type=\"hidden\" name=\"form_type\"       value=\"Newsletter Form\" />
\n<input type=\"hidden\" name=\"form_identifier\" value=\"newsletter_signup_\" />
\n<input type=\"hidden\" name=\"email_template\"  value=\"subscribeformmail\" />
\n<li class="contact_form-li\-\-first_name">
\n    <label class=\"sr-only\" for=\"newsletter_signup_form_first_name\">First name*</label>
\n    <input type=\"text\" name=\"newsletter_signup_form_first_name\" id=\"newsletter_signup_form_first_name\" class=\"validate[required]\" placeholder=\"First name*\">
\n</li>
\n
\n<li class="contact_form-li\-\-first_name">
\n    <label class=\"sr-only\" for=\"newsletter_signup_form_last_name\">Last name*</label>
\n    <input type=\"text\" name=\"newsletter_signup_form_last_name\" id=\"newsletter_signup_form_last_name\" style=\"width:px;\" class=\"validate[required]\" placeholder=\"Last name*\">
\n</li>
\n
\n<li>
\n    <label class=\"sr-only\" for=\"newsletter_signup_form_email_address\">Email*</label>
\n    <input type=\"text\" class=\"validate[required,custom[email]]\" name=\"newsletter_signup_form_email_address\" id=\"newsletter_signup_form_email_address\" placeholder=\"Email*\">
\n</li>
\n
\n<li class="d-flex">
\n    <label for=\"subscribe\" style=\"float: none; font-size: .6875em;\"> I agree to allow my details to be used to sign up to the Ibec Academy mailing list.<br />See the <a href="/privacy-policy">Privacy Policy</a> for full details*</label>
\n    <label class=\"form-checkbox\" style=\"float: left; margin-right: .8rem; order: -1;\">
\n        <input type=\"checkbox\" class="validate[required]" id=\"subscribe\" name=\"contact_form_add_to_list\" />
\n        <span class=\"form-checkbox-helper\"></span>
\n    </label>
\n</li>
\n
\n<li>
\n    <label></label>
\n    <button type=\"submit\" class=\"button inverse\">Submit</button>
\n</li>'
WHERE
  `form_name` = 'Newsletter subscription'
;;



