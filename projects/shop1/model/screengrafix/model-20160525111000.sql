/*
ts:2015-05-25 11:10:00
*/

INSERT IGNORE INTO `plugin_formbuilder_forms` (`form_name`, `action`, `method`, `fields`, `options`, `deleted`, `publish`, `date_created`, `date_modified`, `captcha_enabled`, `use_stripe`, `form_id`) VALUES
(
  'Callback Request',
  'frontend/formprocessor/',
  'POST',
  '<input name=\"subject\" value=\"Contact form\" type=\"hidden\"><input name=\"redirect\" value=\"thank-you.html\" type=\"hidden\"><input name=\"event\" value=\"contact-form\" type=\"hidden\"><input name=\"trigger\" value=\"custom_form\" type=\"hidden\"><input name=\"form_type\" value=\"Contact Form\" type=\"hidden\"><input name=\"form_identifier\" value=\"contact_\" type=\"hidden\"><input type=\"hidden\" name=\"email_template\" value=\"contactformmail\"><li><label for=\"callback-request-form-name\">Name</label><input id=\"callback-request-form-name\" name=\"name\" type=\"text\"></li><li><label for=\"callback-request-form-email\" class=\"mandatory-label\">Email</label><input id=\"callback-request-form-email\" name=\"email\" type=\"text\" class=\"validate[required,custom[email]]\"></li><li><label for=\"callback-request-form-suitable_time_to_call\">Suitable Time to Call</label><input id=\"callback-request-form-suitable_time_to_call\" name=\"suitable_time_to_call\" type=\"text\"></li><li><label></label><button type=\"submit\">Submit</button></li>',
  'redirect:|failpage:',
  '0',
  '1',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '0',
  '0',
  'callback-request-form'
);

INSERT IGNORE INTO `plugin_pages_pages` (`name_tag`, `title`, `content`, `date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`) VALUES
(
  'request-a-callback',
  'Request a Callback',
  '<h1>Request a Callback</h1>  <div class=\"formrt\">{form-Callback Request}</div>   <p>&nbsp;</p>',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0',
  '1',
  (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'content' LIMIT 1),
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'Default' LIMIT 1)
);