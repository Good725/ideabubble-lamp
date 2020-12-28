/*
ts:2019-11-20 12:43:00
*/

UPDATE `plugin_formbuilder_forms` SET `fields` = '<input type=\"hidden\" name=\"subject\" value=\"Contact form\" />
\n<input type=\"hidden\" name=\"business_name\" value=\"\" />
\n<input type=\"hidden\" name=\"redirect\" value=\"thank-you.html\" />
\n<input type=\"hidden\" name=\"event\" value=\"contact-form\" />
\n<input type=\"hidden\" name=\"trigger\" value=\"custom_form\" />
\n<input type=\"hidden\" name=\"form_type\" value=\"Contact Form\" />
\n<input type=\"hidden\" name=\"form_identifier\" value=\"contact_\" />
\n<input type=\"hidden\" name=\"email_template\" value=\"contactformmail\" />
\n<li>
\n    <label for=\"contact_form_name\">Name</label>
\n    <input type=\"text\" name=\"contact_form_name\" class=\"validate[required]\" id=\"contact_form_name\" placeholder=\"Enter name\" />
\n</li>
\n<li>
\n    <label for=\"contact_form_address\">Address</label>
\n    <textarea name=\"contact_form_address\" id=\"contact_form_address\" class=\"validate[required]\" placeholder=\"Enter address\"></textarea>
\n</li>
\n<li>
\n    <label for=\"contact_form_email_address\">Email</label>
\n    <input type=\"text\" class=\"validate[required]\" name=\"contact_form_email_address\" id=\"contact_form_email_address\" placeholder=\"Enter email address\">
\n</li>
\n<li>
\n    <label for=\"contact_form_tel\">Phone</label>
\n    <input type=\"text\" name=\"contact_form_tel\" id=\"contact_form_tel\" class=\"validate[required]\" placeholder=\"Enter phone number\">
\n</li>
\n<li>
\n    <label for=\"contact_form_message\">Message</label>
\n    <textarea name=\"contact_form_message\" class=\"validate[required]\" id=\"contact_form_message\" placeholder=\"Type your message here\"></textarea>
\n</li>
\n<li style=\"display: flex; flex-direction: row-reverse;\">
\n    <label for=\"subscribe\" style=\"flex: 1; width: auto;\">Tick this box to let us get in touch with you</label>
\n    <input type=\"checkbox\" id=\"subscribe\" name=\"contact_form_add_to_list\" />
\n</li>
\n<li><span>[CAPTCHA]</span></li>
\n<li>
\n    <label></label>
\n    <button type=\"submit\" name=\"submit1\" id=\"submit1\" value=\"Send Email\">Submit</button>
\n</li>' where `form_name` = 'Contact Us';