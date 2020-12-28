/*
ts:2017-07-20 12:00:00
*/

UPDATE
  `plugin_formbuilder_forms`
SET
  `fields` =
'<input type=\"hidden\" name=\"subject\"         value=\"Contact form\" />
\n<input type=\"hidden\" name=\"business_name\"   value=\"STAC, Sinnott Training & Certification Ltd\" />
\n<input type=\"hidden\" name=\"redirect\"        value=\"thank-you.html\" />
\n<input type=\"hidden\" name=\"event\"           value=\"contact-form\" />
\n<input type=\"hidden\" name=\"trigger\"         value=\"custom_form\" id=\"trigger\" />
\n<input type=\"hidden\" name=\"form_type\"       value=\"Contact Form\" id=\"form_type\" />
\n<input type=\"hidden\" name=\"form_identifier\" value=\"contact_\" />
\n<input type=\"hidden\" name=\"email_template\"  value=\"contactformmail\" id=\"email_template\" />
\n
\n<li>
\n	<label for=\"contact_form_name\">Name</label>
\n	<input type=\"text\" name=\"contact_form_name\" class=\"validate[required]\" id=\"contact_form_name\" />
\n</li>
\n
\n<li>
\n	<label>Phone</label>
\n	<input name=\"contact_form_tel\" id=\"contact_form_tel\" type=\"text\" class=\"validate[required]\" />
\n</li>
\n
\n<li>
\n	<label>Address</label>
\n	<textarea name=\"contact_form_address\" id=\"contact_form_address\"></textarea>
\n</li>
\n
\n<li>
\n	<label for=\"contact_form_email_address\">Email</label>
\n	<input type=\"text\" name=\"contact_form_email_address\" class=\"validate[required,custom[email]]\" id=\"contact_form_email_address\" />
\n</li>
\n
\n<li>
\n	<label>Message</label>
\n	<textarea name=\"contact_form_message\" class=\"validate[required]\" id=\"contact_form_message\"></textarea>
\n</li>
\n
\n<li>
\n	<label for=\"subscribe\" style=\"float: none; \">tick this box to let us get in touch with you</label>
\n	<input id=\"subscribe\" name=\"contact_form_add_to_list\" type=\"checkbox\" />
\n</li>
\n
\n<li>
\n	<label></label>
\n	<button name=\"submit1\" class=\"primary_button\" id=\"submit1\" value=\"Send Email\">Submit</button>
\n</li>',
  `form_id` = 'contact-us'
WHERE
  `form_name` = 'Contact Us'
;