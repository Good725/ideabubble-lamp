/*
ts:2016-03-03 17:55:00
*/
UPDATE IGNORE `settings` SET `value_dev` = 'ahc', `value_test` = 'ahc', `value_stage` = 'ahc', `value_live` = 'ahc'
WHERE `variable` = 'template_folder_path';

UPDATE IGNORE `settings` SET `value_dev` = '19', `value_test` = '19', `value_stage` = '19', `value_live` = '19'
WHERE `variable` = 'assets_folder_path';

UPDATE IGNORE `settings` SET `value_dev` = 0, `value_test` = 0, `value_stage` = 0, `value_live` = 0
WHERE `variable` = 'use_config_file';


INSERT IGNORE INTO `plugin_formbuilder_forms` (`form_name`, `action`, `method`, `fields`, `options`, `deleted`, `publish`, `date_created`, `date_modified`, `captcha_enabled`, `use_stripe`, `form_id`) VALUES
(
 'ContactUs',
 'frontend/formprocessor/',
 'POST',
 '<input name=\"subject\" value=\"Contact form\" type=\"hidden\"><input name=\"business_name\" value=\"Ailesbury Hair Clinic\" type=\"hidden\" id=\"\"><input name=\"redirect\" value=\"thank-you.html\" type=\"hidden\" id=\"\"><input name=\"event\" value=\"contact-form\" type=\"hidden\" id=\"\"><input name=\"trigger\" value=\"custom_form\" type=\"hidden\" id=\"trigger\"><input name=\"form_type\" value=\"Contact Form\" id=\"form_type\" type=\"hidden\"><input name=\"form_identifier\" value=\"contact_\" type=\"hidden\"><input type=\"hidden\" name=\"email_template\" id=\"email_template\" value=\"contactformmail\"><li><label for=\"contact_form_name\"></label><input type=\"text\" name=\"contact_form_name\" class=\"validate[required]\" id=\"contact_form_name\" placeholder=\"Name*\"></li><li style=\"\"><label for=\"contact_form_address\"></label><textarea name=\"contact_form_address\" id=\"contact_form_address\" placeholder=\"Address\" rows=\"10\"></textarea></li><li><label for=\"contact_form_tel\"></label><input type=\"text\" name=\"contact_form_tel\" id=\"contact_form_tel\" placeholder=\"Phone\"></li><li><label for=\"contact_form_email_address\"></label><input type=\"text\" name=\"contact_form_email_address\" id=\"contact_form_email_address\" placeholder=\"Email*\" class=\"validate[required]\"></li><li><label for=\"contact_form_message\"></label><textarea name=\"contact_form_message\" class=\"validate[required]\" id=\"contact_form_message\" placeholder=\"Message*\" rows=\"10\"></textarea></li>\n<li><label for=\"subscribe\" style=\"\n    float: none;\n\">tick this box to let us get in touch with you</label><input id=\"subscribe\" name=\"contact_form_add_to_list\" type=\"checkbox\"></li>                <li><label></label><button name=\"submit1\" id=\"submit1\" value=\"Send Email\">Submit</button></li>',
 'redirect:|failpage:',
 '0',
 '1',
 CURRENT_TIMESTAMP,
 CURRENT_TIMESTAMP,
 '0',
 '0',
 'ContactUs'
 ),
(
 'AskExpert',
 'frontend/formprocessor/',
 'POST',
 '<input name=\"subject\" value=\"Contact form\" type=\"hidden\"><input name=\"business_name\" value=\"Ailesbury Hair Clinic\" type=\"hidden\" id=\"\"><input name=\"redirect\" value=\"thank-you.html\" type=\"hidden\" id=\"\"><input name=\"event\" value=\"contact-form\" type=\"hidden\" id=\"\"><input name=\"trigger\" value=\"custom_form\" type=\"hidden\" id=\"trigger\"><input name=\"form_type\" value=\"Contact Form\" id=\"form_type\" type=\"hidden\"><input name=\"form_identifier\" value=\"contact_\" type=\"hidden\"><input type=\"hidden\" name=\"email_template\" id=\"email_template\" value=\"contactformmail\"><li><label for=\"ask_expert_form_name\"></label><input type=\"text\" name=\"ask_expert_form_name\" class=\"validate[required]\" id=\"ask_expert_form_name\" placeholder=\"Name*\"></li><li><label for=\"contact_form_email_address\"></label><input type=\"text\" name=\"contact_form_email_address\" id=\"ask_expert_email_address\" placeholder=\"Email\"></li>\n<li><label for=\"contact_form_phone\"></label><input type=\"text\" name=\"contact_form_email_address\" id=\"ask_expert_phone\" placeholder=\"Telephone*\" class=\"validate[required]\"></li>\n                                <li><label for=\"expert_form_request_for\"></label><select name=\"contact_form_request_for\" id=\"expert_form_request_for\">\n  <option value=\"\">Request For</option>\n  <option value=\"Friend\">Friend</option>\n  <option value=\"Google\">Google</option>\n  <option value=\"TV\">TV</option>\n  <option value=\"Radio\">Radio</option>\n  <option value=\"Newspaper\">Newspaper</option>\n  <option value=\"Other\">Other</option>\n  </select></li>\n<li><label for=\"expert_form_request_for\"></label><select name=\"contact_form_request_for\" id=\"expert_form_request_for\">\n  <option value=\"\">Where did you hear about us?</option>\n  <option value=\"More Information\">More Information</option><option value=\"More Information\">Call Me Back</option>\n  </select></li><li><label></label><button name=\"submit1\" id=\"submit1\" value=\"Send Email\">Submit</button></li>',
 'redirect:|failpage:',
 '0',
 '1',
 CURRENT_TIMESTAMP,
 CURRENT_TIMESTAMP,
 '0',
 '0',
 'AskExpert'
 );

INSERT IGNORE INTO `plugin_formbuilder_forms` (`form_name`, `action`, `method`, `fields`, `options`, `deleted`, `publish`, `date_created`, `date_modified`, `captcha_enabled`, `use_stripe`, `form_id`) VALUES
(
  'EnquiryForm',
  'frontend/formprocessor/',
  'POST',
  '<input name=\"subject\" value=\"Contact form\" type=\"hidden\"><input name=\"business_name\" value=\"Ailesbury Hair Clinic\" type=\"hidden\" id=\"\"><input name=\"redirect\" value=\"thank-you.html\" type=\"hidden\" id=\"\"><input name=\"event\" value=\"contact-form\" type=\"hidden\" id=\"\"><input name=\"trigger\" value=\"custom_form\" type=\"hidden\" id=\"trigger\"><input name=\"form_type\" value=\"Contact Form\" id=\"form_type\" type=\"hidden\"><input name=\"form_identifier\" value=\"contact_\" type=\"hidden\"><input type=\"hidden\" name=\"email_template\" id=\"email_template\" value=\"contactformmail\"><li><label for=\"contact_form_name\"></label><input type=\"text\" name=\"contact_form_name\" class=\"validate[required]\" id=\"enquiry_form_name\" placeholder=\"Name*\"></li><li><label for=\"contact_form_tel\"></label><input type=\"text\" name=\"contact_form_tel\" id=\"enquiry_form_tel\" placeholder=\"Phone\"></li><li><label for=\"contact_form_email_address\"></label><input type=\"text\" name=\"contact_form_message\" id=\"enquiry_form_message\" placeholder=\"Message*\" class=\"validate[required]\"></li>\n                <li><label></label><button name=\"submit1\" id=\"enquiry_form_submit\" value=\"Send Email\">Go</button></li>',
  'redirect:|failpage:',
  '0',
  '1',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '0',
  '0',
  'EnquiryForm'
);
