/*
ts:2017-12-07 17:40:00
*/

-- Insert the "Quick Quote" form, if it does not already exist
INSERT IGNORE INTO
  `plugin_formbuilder_forms` (`form_name`, `action`, `method`, `fields`, `publish`, `deleted`, `date_modified`, `form_id`)
SELECT
  'Quick Quote',
  'frontend/formprocessor/',
  'POST',
  '<input name=\"subject\" value=\"Contact form\" type=\"hidden\" />
<input name=\"redirect\" value=\"thank-you.html\" type=\"hidden\" />
<input name=\"event\" value=\"contact-form\" type=\"hidden\" />
<input name=\"trigger\" value=\"custom_form\" type=\"hidden\" id=\"trigger\" />
<input name=\"form_type\" value=\"Contact Form\" id=\"form_type\" type=\"hidden\" />
<input name=\"form_identifier\" value=\"contact_\" type=\"hidden\" />
<input type=\"hidden\" name=\"email_template\" id=\"email_template\" value=\"contactformmail\" />
<li><label for=\"contact_form_name\"></label><input type=\"text\" name=\"contact_form_name\" class=\"validate[required]\" id=\"enquiry_form_name\" placeholder=\"Enter Name*\"></li>
<li><label for=\"contact_form_tel\"></label><input type=\"text\" name=\"contact_form_tel\" id=\"enquiry_form_tel\" placeholder=\"Enter Phone No*\" class=\"validate[required,custom[phone]]\"></li>
<li><label for=\"contact_form_tel\"></label><input type=\"text\" name=\"contact_form_tel\" id=\"enquiry_form_tel\" placeholder=\"Enter E-mail*\" class=\"validate[required,custom[email]]\"></li>
<li><label for=\"contact_form_email_address\"></label><textarea name=\"contact_form_message\" id=\"enquiry_form_message\" placeholder=\"Message*\" class=\"validate[required]\"></textarea></li>
<li><label></label><button name=\"submit1\" id=\"enquiry_form_submit\" value=\"Send Email\" class=\"button\" type=\"submit\">Send Your Enquiry</button></li>',
  '1',
  '0',
  CURRENT_TIMESTAMP,
  'Quick Quote'
FROM
  `plugin_formbuilder_forms`
WHERE
  NOT EXISTS (SELECT `id` FROM `plugin_formbuilder_forms` WHERE `form_name` = 'Quick Quote' AND (`deleted` != 1 or `deleted` IS NULL))
LIMIT 1
;

-- Insert the "Request a Callback" form, if it does not already exist
INSERT IGNORE INTO
  `plugin_formbuilder_forms` (`form_name`, `action`, `method`, `fields`, `publish`, `deleted`, `date_modified`, `form_id`)
SELECT
  'Request a Callback',
  'frontend/formprocessor/',
  'POST',
  '<input name=\"subject\" value=\"Callback Request\" type=\"hidden\" />
<input name=\"redirect\" value=\"thank-you.html\" type=\"hidden\" />
<input name=\"event\" value=\"contact-form\" type=\"hidden\" />
<input name=\"trigger\" value=\"custom_form\" type=\"hidden\" id=\"trigger\" />
<input name=\"form_type\" value=\"Contact Form\" id=\"form_type\" type=\"hidden\" />
<input name=\"form_identifier\" value=\"contact_\" type=\"hidden\" />
<input type=\"hidden\" name=\"email_template\" id=\"email_template\" value=\"contactformmail\" />
<li><label for=\"contact_form_name\">Name:</label><input type=\"text\" name=\"contact_form_name\" class=\"validate[required]\" id=\"contact_form_name\"></li>
<li><label for=\"contact_form_tel\">Phone:</label><input type=\"text\" name=\"contact_form_tel\" id=\"contact_form_tel\" class=\"validate[required,custom[phone]]\"></li>
<li><label for=\"contact_form_submit\"></label><button id=\"formbuilder-preview-contact_form_submit\" class=\"button\" type=\"submit\">Request a Callback</button></li>',
  '1',
  '0',
  CURRENT_TIMESTAMP,
  'Request a Callback'
FROM
  `plugin_formbuilder_forms`
WHERE
  NOT EXISTS (SELECT `id` FROM `plugin_formbuilder_forms` WHERE `form_name` = 'Request a Callback' AND (`deleted` != 1 or `deleted` IS NULL))
LIMIT 1
;


