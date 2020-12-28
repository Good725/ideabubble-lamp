/*
ts:2016-06-22 16:55:00
*/

UPDATE `plugin_formbuilder_forms`
SET `fields`='<input name=\"subject\" value=\"Contact form\" type=\"hidden\"><input name=\"redirect\" value=\"thank-you.html\" type=\"hidden\"><input name=\"event\" value=\"contact-form\" type=\"hidden\"><input name=\"trigger\" value=\"custom_form\" type=\"hidden\"><input name=\"form_type\" value=\"Contact Form\" type=\"hidden\"><input name=\"form_identifier\" value=\"contact_\" type=\"hidden\"><input type=\"hidden\" name=\"email_template\" value=\"contactformmail\"><li><label for=\"callback-request-form-name\">Name</label><input id=\"callback-request-form-name\" name=\"name\" type=\"text\"></li><li><label for=\"callback-request-form-email\" class=\"mandatory-label\">Email</label><input id=\"callback-request-form-email\" name=\"email\" type=\"text\" class=\"validate[required,custom[email]]\"></li><li><label for=\"callback-request-form-phone\" class=\"mandatory-label\">Phone</label><input id=\"callback-request-form-phone\" name=\"phone\" type=\"text\" class=\"validate[required]\"></li> <li><label for=\"callback-request-form-suitable_time_to_call\">Suitable Time to Call</label><input id=\"callback-request-form-suitable_time_to_call\" name=\"suitable_time_to_call\" type=\"text\"></li><li><label></label><button type=\"submit\">Submit</button></li>'
WHERE `form_name`='Callback Request';
