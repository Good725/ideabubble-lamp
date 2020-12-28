/*
ts:2016-08-18 14:25:00
*/


UPDATE `engine_settings` SET `value_live`='1', `value_stage`='1', `value_test`='1', `value_dev`='1' WHERE `variable`='view_website';

INSERT IGNORE INTO `engine_role_permissions` (`role_id`, `resource_id`)
  SELECT `engine_project_role`.`id`, `engine_resources`.`id` FROM `engine_project_role` JOIN `engine_resources`
  WHERE `engine_project_role`.`role` IN ('Administrator', 'Super User') AND `engine_resources`.`alias` = 'view_website_frontend';


UPDATE IGNORE `engine_settings` SET
  `value_live`='/admin/bookings',
  `value_stage`='/admin/bookings',
  `value_test`='/admin/bookings',
  `value_dev`='/admin/bookings'
WHERE `variable`='cms_heading_button_link';

UPDATE IGNORE `engine_settings` SET
  `value_live`='Create Booking',
  `value_stage`='Create Booking',
  `value_test`='Create Booking',
  `value_dev`='Create Booking'
WHERE `variable`='cms_heading_button_text';

INSERT IGNORE INTO `plugin_formbuilder_forms` (`form_name`, `action`, `method`, `fields`, `publish`, `date_modified`, `captcha_enabled`, `form_id`) VALUES (
 'ContactUs',
 'frontend/formprocessor/',
 'POST',
 '<input name=\"subject\" value=\"Contact form\" type=\"hidden\"><input name=\"business_name\" value=\"Navan Ballet\" type=\"hidden\"><input name=\"redirect\" value=\"thank-you.html\" type=\"hidden\"><input name=\"event\" value=\"contact-form\" type=\"hidden\"><input name=\"trigger\" value=\"custom_form\" type=\"hidden\" id=\"trigger\"><input name=\"form_type\" value=\"Contact Form\" id=\"form_type\" type=\"hidden\"><input name=\"form_identifier\" value=\"contact_\" type=\"hidden\"><input type=\"hidden\" name=\"email_template\" id=\"email_template\" value=\"contactformmail\"><li><label for=\"contact_form_name\">Name</label><input type=\"text\" name=\"contact_form_name\" class=\"validate[required]\" id=\"contact_form_name\" placeholder=\"Enter name\"></li><li><label for=\"contact_form_address\">Address</label><textarea name=\"contact_form_address\" id=\"contact_form_address\" class=\"validate[required]\" placeholder=\"Enter address\"></textarea></li><li><label for=\"contact_form_email_address\">Email</label><input type=\"text\" class=\"validate[required]\" name=\"contact_form_email_address\" id=\"contact_form_email_address\" placeholder=\"Enter email address\"></li><li><label for=\"contact_form_tel\">Phone</label><input type=\"text\" name=\"contact_form_tel\" id=\"contact_form_tel\" class=\"validate[required]\" placeholder=\"Enter phone number\"></li><li><label for=\"contact_form_message\">Message</label><textarea name=\"contact_form_message\" class=\"validate[required]\" id=\"contact_form_message\" placeholder=\"Type your message here\"></textarea></li>\n<li><label for=\"subscribe\" style=\"\n    float: none;\n\">tick this box to let us get in touch with you</label><input id=\"subscribe\" name=\"contact_form_add_to_list\" type=\"checkbox\"></li>                <li><label></label><button name=\"submit1\" id=\"submit1\" value=\"Send Email\">Submit</button></li>',
 '1',
 CURRENT_TIMESTAMP(),
 '0',
 'ContactUs'
 );

UPDATE `plugin_pages_pages` SET `content`=CONCAT(`content`, "\n\n<div class=\"formrt\">{form-ContactUs}</div>") WHERE `name_tag` in ('contact-us.html', 'contact-us');


INSERT IGNORE INTO `plugin_pages_pages` (`name_tag`, `title`, `content`, `date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `layout_id`, `category_id`) VALUES
(
  'booking-confirmed',
  'Booking Confirmed',
  '<h2 style=\"text-align: center;\"><span style=\"color:#D79CA1\">BOOKING CONFIRMED</span></h2>
\n\n<p style=\"text-align: center;\"><strong><span style=\"color:#304495\">Thank you for booking with us today.<br />
\nWe have sent your booking confirmation to your email address.</span></strong></p>
\n\n<hr style=\"border: none; border-top: 1px solid #EDCFD0; max-width: 350px; margin: 5em auto 0;\" />
\n\n<p style=\"text-align: center;\"><span style=\"color:#304495\"><strong>You can also log in to your account here to:</strong></span></p>
\n\n<p style=\"text-align: center;\"><span style=\"color:#D79CA1\" class=\"flaticon-calendar\">&zwnj;</span></p>
\n\n<p style=\"text-align: center;\">Review all your bookings with us</p>
\n\n<p style=\"text-align: center;\"><span style=\"color:#D79CA1\" class=\"flaticon-smile\">&zwnj;</span></p>
\n\n<p style=\"text-align: center;\">Add more details about yourself and your family</p>\n\n<p style=\"text-align: center;\"><span style=\"color:#D79CA1\" class=\"flaticon-time\">&zwnj;</span></p>
\n\n<p style=\"text-align: center;\">Track payments and balances</p>
\n\n<p style=\"text-align: center;\"><a class=\"login_button\" href=\"/admin/login\">Log in</a></p>',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  '1',
  '0',
  (SELECT `id` FROM `plugin_pages_layouts`   WHERE `layout`   = 'content' LIMIT 1),
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'Default' LIMIT 1)
);


UPDATE IGNORE `engine_settings` SET
  `value_live`='/course-list.html',
  `value_stage`='/course-list.html',
  `value_test`='/course-list.html',
  `value_dev`='/course-list.html'
WHERE `variable`='cms_heading_button_link';
