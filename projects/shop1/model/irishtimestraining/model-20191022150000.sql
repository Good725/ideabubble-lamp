/*
ts:2019-10-22 15:00:00
*/

DELIMITER ;;

UPDATE
  `plugin_pages_pages`
SET
  `content` = '<h1><span style="font-size:48px">Checkout</span></h1><p class="my-2" style="font-size:22px">Secondary text. Secondary text.</p>',
  `footer`  = '<div class="get_in_touch simplebox mb-0" style="background-color: #eee;">
	<div class="simplebox-columns">
		<div class="simplebox-column simplebox-column-1">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<div><img alt="" src="/shared_media/irishtimestraining/media/photos/content/get_in_touch_guy.png" /></div>
			</div>
		</div>

		<div class="simplebox-column simplebox-column-2">
			<div class="simplebox-content">
				<div class="simplebox-content-toolbar"><button><img src="/engine/shared/js/ckeditor/plugins/simplebox/icons/wrench.svg" style="height:12px; width:12px" /></button></div>

				<h2>Get in touch</h2>

				<p>Contact us to discuss <span class="nowrap">tailor-made</span> courses for your team.</p>

				<p><a class="button bg-success" href="/contact-us">Contact us</a>
				   <a class="button bg-primary" href="/request-a-callback">Request a callback</a></p>
			</div>
		</div>
	</div>
</div>
'
WHERE
  `name_tag` IN ('checkout', 'checkout.html')
;;

-- Add the "terms-and-privacy" page, if it does not already exist.
INSERT IGNORE INTO
  `plugin_pages_pages` (`name_tag`, `title`, `content`,`date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`)
SELECT
  'terms-and-privacy',
  'Terms & Privacy',
  '',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '0',
  '0',
  '1',
  (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'content_wide' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'DEFAULT' LIMIT 1)
FROM `plugin_pages_pages`
WHERE NOT EXISTS (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` = 'terms-and-privacy' AND `deleted` = 0)
LIMIT 1;;

-- Add its content
UPDATE
  `plugin_pages_pages`
SET
  `publish`       = 1,
  `content`       = '<p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Sed ut perspiciatis inde omnis iste natus error sit voluptatem.</p>',
  `modified_by`   = (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  `last_modified` = CURRENT_TIMESTAMP
WHERE
  `name_tag` = 'terms-and-privacy'
;;

-- Set it as the privacy policy page in the settings
UPDATE
  `engine_settings`
SET
  `value_live`  = (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` = 'terms-and-privacy' LIMIT 1),
  `value_stage` = (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` = 'terms-and-privacy' LIMIT 1),
  `value_test`  = (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` = 'terms-and-privacy' LIMIT 1),
  `value_dev`   = (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` = 'terms-and-privacy' LIMIT 1)
WHERE
  `variable`    = 'privacy_policy_page'
;;


-- Add the "request a callback" page, if it does not already exist.
INSERT IGNORE INTO
  `plugin_pages_pages` (`name_tag`, `title`, `content`,`date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`)
SELECT
  'request-a-callback',
  'Request a callback',
  '',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0',
  '1',
  (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'content_wide' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'DEFAULT' LIMIT 1)
FROM `plugin_pages_pages`
WHERE NOT EXISTS (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` = 'request-a-callback' AND `deleted` = 0)
LIMIT 1;;

-- Add its content
UPDATE
  `plugin_pages_pages`
SET
  `layout_id`     = (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'content_wide' AND `deleted` = 0 LIMIT 1),
  `content`       = '<h1>Request a callback</h1>
\n
\n<p>Get exactly what your team needs with a tailor-made course. Co-created with you to help tackle your team&#39;s unique goals and challenges and delivered to your team on-site.</p>
\n
\n<p>Just enter your details below and a member of our expert team will get in touch as soon as possible!</p>
\n
\n<div class=\"formrt formrt-vertical\" style=\"max-width: 400px;\">{form-Request a Callback}</div>',
  `modified_by`   = (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  `last_modified` = CURRENT_TIMESTAMP
WHERE
  `name_tag` = 'request-a-callback'
;;

-- Update the form
UPDATE `plugin_formbuilder_forms`
SET `fields` =
 '<input type=\"hidden\" name=\"subject\"         value=\"Callback Request\" />
\n<input type=\"hidden\" name=\"redirect\"        value=\"thank-you.html\"   />
\n<input type=\"hidden\" name=\"event\"           value=\"contact-form\" /> 
\n<input type=\"hidden\" name=\"trigger\"         value=\"custom_form\" id=\"trigger\" /> 
\n<input type=\"hidden\" name=\"form_type\"       value=\"Contact Form\" id=\"form_type\" /> 
\n<input type=\"hidden\" name=\"form_identifier\" value=\"contact_\" /> 
\n<input type=\"hidden\" name=\"email_template\"  value=\"contactformmail\" id=\"email_template\" /> 
\n<li><label for=\"contact_form_name\">Name:</label><input type=\"text\" name=\"contact_form_name\" class=\"validate[required]\" id=\"contact_form_name\"></li> 
\n<li><label for=\"contact_form_tel\">Phone:</label><input type=\"text\" name=\"contact_form_tel\" class=\"validate[groupRequired[contact_method],custom[phone]]\" id=\"contact_form_tel\"></li>
\n<li><label for=\"contact_form_email\">Email:</label><input type=\"text\" name=\"contact_form_email\" class=\"validate[groupRequired[contact_method],custom[email]]\" id=\"contact_form_email\"></li>
\n<li style=\"display: flex; flex-direction: row-reverse;\">
\n    <label for=\"request_a_callback-terms-and-conditions\" style=\"font-size: 14px;\">
\n        By submitting this form, you agree that may use the data you provide to contact you with information related to your request.
\n        You can unsubscribe at any time by clicking the unsubscribe link in any email communication.
\n        To learn more, see our <a href="/privacy-policy" target="_blank">privacy policy</a>.
\n    </label>
\n    <input type=\"checkbox\" id=\"request_a_callback-terms-and-conditions\" class=\"validate[required]\" name=\"contact_form_terms_and_conditions_accepted\" style=\"margin-right: .5em;\" />
\n</li>
\n<li><span>[CAPTCHA]</span></li>
\n<li><label for=\"contact_form_submit\"></label><button id=\"formbuilder-preview-contact_form_submit\" class=\"button\" type=\"submit\">Request a Callback</button></li>'
WHERE `form_name` = 'Request a Callback';;
