/*
ts:2016-03-29 15:00:00
*/

INSERT IGNORE INTO `plugin_formbuilder_forms` (`form_name`, `action`, `method`, `fields`, `deleted`, `publish`, `date_created`, `date_modified`, `captcha_enabled`, `use_stripe`, `form_id`) VALUES
(
  'Callback',
  'frontend/formprocessor/',
  'POST',
  '<input name=\"subject\" value=\"Contact form\" type=\"hidden\"><input name=\"redirect\" value=\"thank-you.html\" type=\"hidden\" id=\"\"><input name=\"event\" value=\"contact-form\" type=\"hidden\" id=\"\"><input name=\"trigger\" value=\"custom_form\" type=\"hidden\" id=\"\"><input name=\"form_type\" value=\"Contact Form\" id=\"\" type=\"hidden\"><input name=\"form_identifier\" value=\"contact_\" type=\"hidden\"><input type=\"hidden\" name=\"email_template\" id=\"\" value=\"contactformmail\"><li><label for=\"callback_form_name\"></label><input type=\"text\" name=\"contact_form_name\" class=\"validate[required]\" id=\"callback_form_name\" placeholder=\"Name*\"></li><li><label for=\"contact_form_tel\"></label><input type=\"text\" name=\"contact_form_tel\" id=\"callback_form_name_tel\" placeholder=\"Telephone*\" class=\"validate[required]\"></li>                 <li><label></label><button id=\"callback_form_submit\" type=\"submit\">Submit</button></li>',
  '0',
  '1',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '0',
  '0',
  'callback-form'
);

INSERT IGNORE INTO `plugin_formbuilder_forms` (`form_name`, `action`, `method`, `fields`, `deleted`, `publish`, `date_created`, `date_modified`, `captcha_enabled`, `use_stripe`, `form_id`) VALUES
(
  'Question',
  'frontend/formprocessor/',
  'POST',
  '<input name=\"subject\" value=\"Contact form\" type=\"hidden\"><input name=\"redirect\" value=\"thank-you.html\" type=\"hidden\" id=\"\"><input name=\"event\" value=\"contact-form\" type=\"hidden\" id=\"\"><input name=\"trigger\" value=\"custom_form\" type=\"hidden\" id=\"\"><input name=\"form_type\" value=\"Contact Form\" id=\"\" type=\"hidden\"><input name=\"form_identifier\" value=\"contact_\" type=\"hidden\"><input type=\"hidden\" name=\"email_template\" id=\"\" value=\"contactformmail\"><li><label for=\"question_form_name\"></label><input type=\"text\" name=\"contact_form_name\" class=\"validate[required]\" id=\"question_form_name\" placeholder=\"Name*\"></li><li><label for=\"contact_form_email_address\"></label><input type=\"text\" name=\"contact_form_email_address\" id=\"question_form_email_address\" placeholder=\"Email*\" class=\"validate[required]\"></li><li><label for=\"contact_form_tel\"></label><input type=\"text\" name=\"contact_form_tel\" id=\"question_form_tel\" placeholder=\"Telephone*\" class=\"validate[required]\"></li><li><label for=\"contact_form_message\"></label><textarea name=\"contact_form_message\" class=\"validate[required]\" id=\"contact_form_message\" placeholder=\"Your Question*\" rows=\"10\"></textarea></li>                 <li><label></label><button type=\"submit\">Submit</button></li>',
  '0',
  '1',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '0',
  '0',
  'question-form'
);

INSERT IGNORE INTO `plugin_pages_layouts` (`layout`, `use_db_source`, `publish`, `deleted`, `date_created`, `date_modified`, `created_by`, `modified_by`) VALUES (
  'testimonials',
  '0',
  '1',
  '0',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1)
);
