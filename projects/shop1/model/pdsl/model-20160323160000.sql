/*
ts:2015-03-23 16:00:00
*/
INSERT IGNORE INTO `plugin_formbuilder_forms` (`form_name`, `action`, `method`, `fields`, `deleted`, `publish`, `date_created`, `date_modified`, `captcha_enabled`, `use_stripe`, `form_id`) VALUES
(
  'Booking',
  'frontend/formprocessor',
  'POST',
  '<input name=\"subject\" value=\"Contact form\" type=\"hidden\"><input name=\"redirect\" value=\"thank-you.html\" type=\"hidden\" id=\"\"><input name=\"event\" value=\"contact-form\" type=\"hidden\" id=\"\"><input name=\"trigger\" value=\"custom_form\" type=\"hidden\" id=\"trigger\"><input name=\"form_type\" value=\"Contact Form\" id=\"form_type\" type=\"hidden\"><input name=\"form_identifier\" value=\"contact_\" type=\"hidden\"><input type=\"hidden\" name=\"email_template\" id=\"email_template\" value=\"contactformmail\"><li><label for=\"booking_form_name\">Name</label><input type=\"text\" name=\"booking_form_name\" class=\"validate[required]\" id=\"booking_form_name\" placeholder=\"Enter name\"></li><li style=\"\"><label for=\"booking_form_address\">Address</label><textarea name=\"address\" id=\"booking_form_address\" class=\"validate[required]\" placeholder=\"Enter address\"></textarea></li><li><label for=\"booking_form_email_address\">Email</label><input type=\"text\" class=\"validate[required]\" name=\"email\" id=\"booking_form_email_address\" placeholder=\"Enter email address\"></li><li><label for=\"booking_form_tel\">Phone</label><input type=\"text\" name=\"phone\" id=\"booking_form_tel\" class=\"validate[required]\" placeholder=\"Enter phone number\"></li><li><label for=\"booking_form_message\">Message</label><textarea name=\"message\" class=\"validate[required]\" id=\"booking_form_message\" placeholder=\"Type your message here\"></textarea></li><li><label for=\"booking_form_service\">Choose Service</label> <select id=\"booking_form_service\" name=\"service\">   <option value=\"\">— Please Select —</option><option>Fire</option>   <option>Security</option>   <option>Fire &amp; Security</option>      </select></li><li><label for=\"booking_form_requirements\">Service Requirements</label> <select id=\"booking_form_requirements\" name=\"requirements\">   <option value=\"\">— Please Select —</option><option>New Install</option>   <option>Existing System</option>      </select></li><li><label for=\"booking_form_service_type\">Service Type</label> <select id=\"booking_form_service_type\" name=\"service_type\">   <option value=\"\">— Please Select —</option>     <option value=\"Addressable\" data-service=\"fire\">Addressable</option>     <option value=\"Conventional\" data-service=\"fire\">Conventional</option>     <option value=\"CCTV\" data-service=\"security\">CCTV</option>     <option value=\"Access control\" data-service=\"security\">Access control</option>     <option value=\"Intruder\" data-service=\"security\">Intruder</option>   </select></li><li><label for=\"booking_form_industry_category\">Industry Category</label> <select id=\"booking_form_industry_category\" name=\"industry_category\">   <option value=\"\">— Please Select —</option>   <option>Industrial</option>   <option>Retail</option>   <option>Commercial</option>   <option>Educational</option>      </select></li><li><label for=\"booking_form_role\">Your Role</label> <select id=\"booking_form_role\" name=\"role\">   <option value=\"\">— Please Select —</option>   <option>IT Manager</option>   </select></li> <li><label for=\"subscribe\" style=\"     float: none; \">tick this box to let us get in touch with you</label><input id=\"subscribe\" name=\"add_to_list\" type=\"checkbox\"></li>                <li><label></label><button name=\"submit1\" id=\"make-booking-button\" value=\"Send Email\">Make Booking</button></li>',
  '0',
  '1',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '0',
  '0',
  'Booking'
);

UPDATE IGNORE `plugin_formbuilder_forms`
SET   `fields`    = '<input name=\"subject\" value=\"Booking form\" type=\"hidden\"><input name=\"redirect\" value=\"thank-you.html\" type=\"hidden\" id=\"\"><input name=\"event\" value=\"contact-form\" type=\"hidden\" id=\"\"><input name=\"trigger\" value=\"custom_form\" type=\"hidden\" id=\"trigger\"><input name=\"form_type\" value=\"Contact Form\" id=\"form_type\" type=\"hidden\"><input name=\"form_identifier\" value=\"contact_\" type=\"hidden\"><input type=\"hidden\" name=\"email_template\" value=\"consultationformmail\"><li><label for=\"booking_form_name\">Name</label><input type=\"text\" name=\"name\" class=\"validate[required]\" id=\"booking_form_name\" placeholder=\"Enter name\"></li><li style=\"\"><label for=\"booking_form_address\">Address</label><textarea name=\"address\" id=\"booking_form_address\" class=\"validate[required]\" placeholder=\"Enter address\"></textarea></li><li><label for=\"booking_form_email_address\">Email</label><input type=\"text\" class=\"validate[required]\" name=\"email\" id=\"booking_form_email_address\" placeholder=\"Enter email address\"></li><li><label for=\"booking_form_tel\">Phone</label><input type=\"text\" name=\"phone\" id=\"booking_form_tel\" class=\"validate[required]\" placeholder=\"Enter phone number\"></li><li><label for=\"booking_form_message\">Message</label><textarea name=\"message\" class=\"validate[required]\" id=\"booking_form_message\" placeholder=\"Type your message here\"></textarea></li><li><label for=\"booking_form_service\">Choose Service</label> <select id=\"booking_form_service\" name=\"service\">   <option value=\"\">— Please Select —</option><option>Fire</option>   <option>Security</option>   <option>Fire &amp; Security</option>      </select></li><li><label for=\"booking_form_requirements\">Service Requirements</label> <select id=\"booking_form_requirements\" name=\"requirements\">   <option value=\"\">— Please Select —</option><option>New Install</option>   <option>Existing System</option>      </select></li><li><label for=\"booking_form_service_type\">Service Type</label> <select id=\"booking_form_service_type\" name=\"service_type\">   <option value=\"\">— Please Select —</option>     <option value=\"Addressable\" data-service=\"fire\">Addressable</option>     <option value=\"Conventional\" data-service=\"fire\">Conventional</option>     <option value=\"CCTV\" data-service=\"security\">CCTV</option>     <option value=\"Access control\" data-service=\"security\">Access control</option>     <option value=\"Intruder\" data-service=\"security\">Intruder</option>   </select></li><li><label for=\"booking_form_industry_category\">Industry Category</label> <select id=\"booking_form_industry_category\" name=\"industry_category\">   <option value=\"\">— Please Select —</option>   <option>Industrial</option>   <option>Retail</option>   <option>Commercial</option>   <option>Educational</option>      </select></li><li><label for=\"booking_form_role\">Your Role</label> <select id=\"booking_form_role\" name=\"role\">   <option value=\"\">— Please Select —</option>   <option>IT Manager</option>   </select></li> <li><label for=\"subscribe\" style=\"     float: none; \">tick this box to let us get in touch with you</label><input id=\"subscribe\" name=\"add_to_list\" type=\"checkbox\"></li>                <li><label></label><button name=\"submit1\" id=\"make-booking-button\" value=\"Send Email\">Make Booking</button></li>'
WHERE `form_name` = 'Booking';

-- Insert the book-now page, if it doesn't exist
INSERT INTO `plugin_pages_pages` (`name_tag`, `title`, `content`, `date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`)
SELECT
  `page_name`,
  'Book Now',
  '<h1>Book a Consultation</h1>  <div class="formrt">{form-Booking}</div> ',
   CURRENT_TIMESTAMP,
   CURRENT_TIMESTAMP,
   (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
   (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
   1,
   0,
   1,
   (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'Content' AND `deleted` = 0 LIMIT 1),
   (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'Default')
FROM (SELECT 'book-now.html' AS `page_name`) `temp`
WHERE NOT EXISTS(SELECT 1 FROM `plugin_pages_pages` WHERE `name_tag` IN ('book-now', 'book-now.html') AND `deleted` = 0);