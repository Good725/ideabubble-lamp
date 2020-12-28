/*
ts:2016-08-22 14:00:00
*/

INSERT IGNORE INTO `plugin_formbuilder_forms` (`form_name`, `action`, `method`, `fields`, `publish`, `date_modified`, `captcha_enabled`, `form_id`) VALUES (
 'ContactUs',
 'frontend/formprocessor/',
 'POST',
 '<input name=\"subject\" value=\"Contact form\" type=\"hidden\"><input name=\"business_name\" value=\"Navan Ballet\" type=\"hidden\"><input name=\"redirect\" value=\"thank-you.html\" type=\"hidden\"><input name=\"event\" value=\"contact-form\" type=\"hidden\"><input name=\"trigger\" value=\"custom_form\" type=\"hidden\" id=\"trigger\"><input name=\"form_type\" value=\"Contact Form\" id=\"form_type\" type=\"hidden\"><input name=\"form_identifier\" value=\"contact_\" type=\"hidden\"><input type=\"hidden\" name=\"email_template\" id=\"email_template\" value=\"contactformmail\"><li><label for=\"contact_form_name\">Name</label><input type=\"text\" name=\"contact_form_name\" class=\"validate[required]\" id=\"contact_form_name\" placeholder=\"Enter name\"></li><li><label for=\"contact_form_address\">Address</label><textarea name=\"contact_form_address\" id=\"contact_form_address\" class=\"validate[required]\" placeholder=\"Enter address\"></textarea></li><li><label for=\"contact_form_email_address\">Email</label><input type=\"text\" class=\"validate[required]\" name=\"contact_form_email_address\" id=\"contact_form_email_address\" placeholder=\"Enter email address\"></li><li><label for=\"contact_form_tel\">Phone</label><input type=\"text\" name=\"contact_form_tel\" id=\"contact_form_tel\" class=\"validate[required]\" placeholder=\"Enter phone number\"></li><li><label for=\"contact_form_message\">Message</label><textarea name=\"contact_form_message\" class=\"validate[required]\" id=\"contact_form_message\" placeholder=\"Type your message here\"></textarea></li>\n<li><label for=\"subscribe\" style=\"\n    float: none;\n\">tick this box to let us get in touch with you</label><input id=\"subscribe\" name=\"contact_form_add_to_list\" type=\"checkbox\"></li>                <li><label></label><button type="submit" name=\"submit1\" id=\"submit1\" value=\"Send Email\">Submit</button></li>',
 '1',
 CURRENT_TIMESTAMP(),
 '0',
 'ContactUs'
 );

INSERT IGNORE INTO `plugin_pages_pages` (`name_tag`, `title`, `content`, `date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `layout_id`, `category_id`) VALUES
(
  'contact-us',
  'Contact Us',
  '<h1>Contact Us</h1>',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  '1',
  '0',
  (SELECT `id` FROM `plugin_pages_layouts`   WHERE `layout`   = 'content' LIMIT 1),
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'Default' LIMIT 1)
);

UPDATE IGNORE `plugin_pages_pages` SET `content`=CONCAT(`content`, "\n\n<div class=\"formrt\">{form-ContactUs}</div>") WHERE `name_tag` IN ('contact-us.html', 'contact-us');
