/*
ts:2016-03-21 15:22:00
*/
INSERT IGNORE INTO `plugin_formbuilder_forms` (`form_name`, `action`, `method`, `fields`, `options`, `deleted`, `publish`, `date_created`, `date_modified`, `captcha_enabled`, `use_stripe`, `form_id`) VALUES
(
  'Call Me Back',
  'frontend/formprocessor/',
  'POST',
  '<input name=\"subject\" value=\"Contact form\" type=\"hidden\"><input name=\"redirect\" value=\"thank-you.html\" type=\"hidden\" id=\"\"><input name=\"event\" value=\"contact-form\" type=\"hidden\" id=\"\"><input name=\"trigger\" value=\"custom_form\" type=\"hidden\" id=\"trigger\"><input name=\"form_type\" value=\"Contact Form\" id=\"form_type\" type=\"hidden\"><input name=\"form_identifier\" value=\"contact_\" type=\"hidden\"><input type=\"hidden\" name=\"email_template\" id=\"email_template\" value=\"contactformmail\"><li><label for=\"contact_form_name\">Name</label><input type=\"text\" name=\"contact_form_name\" class=\"validate[required]\" id=\"contact_form_name\" placeholder=\"Enter name\"></li><li><label for=\"contact_form_tel\">Phone</label><input type=\"text\" name=\"contact_form_tel\" id=\"contact_form_tel\" class=\"validate[required]\" placeholder=\"Enter phone number\"></li> <li><label for=\"subscribe\" style=\"     float: none; \">tick this box to let us get in touch with you</label><input id=\"subscribe\" name=\"contact_form_add_to_list\" type=\"checkbox\"></li>                <li><label></label><button value=\"Send Email\" type=\"submit\">Submit</button></li>',
  'redirect:|failpage:',
  '0',
  '1',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  'CallMeBack'
);

INSERT IGNORE INTO `plugin_pages_pages` (`name_tag`, `title`, `content`, `date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`) VALUES
(
  'call-me-back.html',
  'Call Me Back',
  '<h1>Call Me Back</h1>  <div class=\"formrt\">{form-Call Me Back}</div> ',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0',
  '1',
  (SELECT `id` FROM `plugin_pages_layouts`   WHERE `layout`   = 'content' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'DEFAULT' LIMIT 1)
);
