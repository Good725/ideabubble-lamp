/*
ts:2016-05-17 12:25:00
*/
UPDATE IGNORE `engine_settings` SET `value_dev` = 'phrc', `value_test` = 'phrc', `value_stage` = 'phrc', `value_live` = 'phrc'
WHERE `variable` = 'template_folder_path';

UPDATE IGNORE `engine_settings` SET `value_dev` = '20', `value_test` = '20', `value_stage` = '20', `value_live` = '20'
WHERE `variable` = 'assets_folder_path';

UPDATE IGNORE `engine_settings` SET `value_dev` = 0, `value_test` = 0, `value_stage` = 0, `value_live` = 0
WHERE `variable` = 'use_config_file';

INSERT IGNORE INTO `plugin_formbuilder_forms` (`form_name`, `action`, `method`, `class`, `fields`, `deleted`, `publish`, `date_created`, `date_modified`, `summary`, `captcha_enabled`, `use_stripe`, `form_id`) VALUES
(
  'EnquiryForm',
  'frontend/formprocessor/',
  'POST',
  '',
  '<input name=\"subject\" value=\"Contact form\" type=\"hidden\"><input name=\"business_name\" value=\"Premier Hair Restoration Clinic\" type=\"hidden\" id=\"\"><input name=\"redirect\" value=\"thank-you.html\" type=\"hidden\" id=\"\"><input name=\"event\" value=\"contact-form\" type=\"hidden\" id=\"\"><input name=\"trigger\" value=\"custom_form\" type=\"hidden\" id=\"trigger\"><input name=\"form_type\" value=\"Contact Form\" id=\"form_type\" type=\"hidden\"><input name=\"form_identifier\" value=\"contact_\" type=\"hidden\"><input type=\"hidden\" name=\"email_template\" id=\"email_template\" value=\"contactformmail\"><li><label for=\"enquiry_form_name\"></label><input type=\"text\" name=\"contact_form_name\" class=\"validate[required]\" id=\"enquiry_form_name\" placeholder=\"ENTER YOUR NAME\"></li><li><label for=\"enquiry_form_phone\"></label><input type=\"text\" name=\"contact_form_tel\" id=\"enquiry_form_phone\" placeholder=\"ENTER YOUR PHONE NUMBER\" class=\"validate[required]\"></li><li><label for=\"enquiry_form_email_address\"></label><input type=\"text\" name=\"contact_form_email_address\" id=\"enquiry_form_email_address\" placeholder=\"ENTER YOUR EMAIL ADDRESS\"></li> <li><label for=\"expert_form_request_for\"></label><select name=\"contact_form_request_for\" id=\"expert_form_request_for\">   <option value=\"\">Request for</option>   <option value=\"More Information\">More Information</option><option value=\"Call Me Back\">Call Me Back</option>  </select></li>                                 <li><label for=\"enquiry_form_hear_about\"></label><select name=\"contact_form_hear_about\" id=\"enquiry_form_hear_about\">   <option value=\"\">You heard about us from...</option>   <option value=\"Friend\">Friend</option>   <option value=\"Google\">Google</option>   <option value=\"TV\">TV</option>   <option value=\"Radio\">Radio</option>   <option value=\"Newspaper\">Newspaper</option>   <option value=\"Other\">Other</option>   </select></li> <li><label></label><button id=\"enquiry_form_submit\" value=\"Send Email\" class=\"button\" type=\"submit\">ENQUIRE NOW</button></li>',
  '0',
  '1',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '',
  '0',
  '0',
  'EnquiryForm'
);


INSERT IGNORE INTO `engine_feeds` (`name`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `short_tag`, `function_call`) VALUES
(
  'news feed',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie'),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie'),
  '1',
  '0',
  'newsfeed',
  'Model_News,get_plugin_items_front_end_feed'
),
(
  'testimonials feed',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie'),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie'),
  '1',
  '0',
  'testimonialsfeed',
  'Model_Testimonials,get_plugin_items_front_end_feed'
);

INSERT IGNORE INTO `plugin_formbuilder_forms` (`form_name`, `action`, `method`, `fields`, `options`, `deleted`, `publish`, `date_created`, `date_modified`, `summary`, `captcha_enabled`, `use_stripe`, `form_id`) VALUES
(
  'ContactUs',
  'frontend/formprocessor/',
  'POST',
  '<input name=\"subject\" value=\"Contact form\" type=\"hidden\"><input name=\"business_name\" value=\"Premiere Hair Restoration Clinic\" type=\"hidden\" id=\"\"><input name=\"redirect\" value=\"thank-you.html\" type=\"hidden\" id=\"\"><input name=\"event\" value=\"contact-form\" type=\"hidden\" id=\"\"><input name=\"trigger\" value=\"custom_form\" type=\"hidden\" id=\"trigger\"><input name=\"form_type\" value=\"Contact Form\" id=\"form_type\" type=\"hidden\"><input name=\"form_identifier\" value=\"contact_\" type=\"hidden\"><input type=\"hidden\" name=\"email_template\" id=\"email_template\" value=\"contactformmail\"><li><label for=\"contact_form_name\"></label><input type=\"text\" name=\"contact_form_name\" class=\"validate[required]\" id=\"contact_form_name\" placeholder=\"Name*\"></li><li style=\"\"><label for=\"contact_form_address\"></label><textarea name=\"contact_form_address\" id=\"contact_form_address\" placeholder=\"Address\" rows=\"10\"></textarea></li><li><label for=\"contact_form_tel\"></label><input type=\"text\" name=\"contact_form_tel\" id=\"contact_form_tel\" placeholder=\"Phone\"></li><li><label for=\"contact_form_email_address\"></label><input type=\"text\" name=\"contact_form_email_address\" id=\"contact_form_email_address\" placeholder=\"Email*\" class=\"validate[required]\"></li><li><label for=\"contact_form_message\"></label><textarea name=\"contact_form_message\" class=\"validate[required]\" id=\"contact_form_message\" placeholder=\"Message*\" rows=\"10\"></textarea></li> <li><label for=\"subscribe\" style=\"     float: none; \">tick this box to let us get in touch with you </label><input id=\"subscribe\" name=\"contact_form_add_to_list\" type=\"checkbox\"></li>                <li><label></label><button type=\"submit\" class=\"button\">Submit</button></li>',
  'redirect:|failpage:',
  '0',
  '1',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '',
  '0',
  '0',
  'ContactUs'
);

INSERT IGNORE INTO `plugin_formbuilder_forms` (`form_name`, `action`, `method`, `fields`, `options`, `deleted`, `publish`, `date_created`, `date_modified`, `summary`, `captcha_enabled`, `use_stripe`, `form_id`) VALUES
(
  'RequestCallback',
  'frontend/formprocessor/',
  'POST',
  '<input name=\"subject\" value=\"Contact form\" type=\"hidden\"><input name=\"redirect\" value=\"thank-you.html\" type=\"hidden\" id=\"\"><input name=\"event\" value=\"contact-form\" type=\"hidden\" id=\"\"><input name=\"trigger\" value=\"custom_form\" type=\"hidden\" id=\"\"><input name=\"form_type\" value=\"Contact Form\" id=\"\" type=\"hidden\"><input name=\"form_identifier\" value=\"contact_\" type=\"hidden\"><input type=\"hidden\" name=\"email_template\" id=\"\" value=\"contactformmail\"><li><label for=\"callback_form_name\"></label><input type=\"text\" name=\"contact_form_name\" class=\"validate[required]\" id=\"callback_form_name\" placeholder=\"Name*\"></li><li><label for=\"contact_form_tel\"></label><input type=\"text\" name=\"contact_form_email\" id=\"callback_form_name_email\" placeholder=\"Email*\" class=\"validate[required]\"></li><li><label for=\"contact_form_time\"></label><select name=\"contact_form_time\" id=\"callback_form_name_time\" placeholder=\"What item is suitable\">\n  <option value=\"\">Please select</option>\n  <option value=\"08:00\">8:00</option>\n  <option value=\"09:00\">9:00</option>\n  <option value=\"10:00\">10:00</option>\n  <option value=\"11:00\">11:00</option>\n  <option value=\"12:00\">12:00</option>\n  <option value=\"13:00\">13:00</option>\n  <option value=\"14:00\">14:00</option>\n  <option value=\"15:00\">15:00</option>\n  <option value=\"16:00\">16:00</option>\n  <option value=\"17:00\">17:00</option>\n</select>\n</li>                 <li><label></label><button id=\"callback_form_submit\" type=\"submit\" class=\"button\">Submit</button></li>',
  'redirect:|failpage:',
  '0',
  '1',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '',
  '0',
  '0',
  'RequestCallback'
);

INSERT INTO `plugin_pages_pages` (`name_tag`, `title`, `content`, `date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`) VALUES
(
  'contact-us',
  'Contact Us',
  '<h1>Contact Us</h1><div class=\"formrt\">{form-ContactUs}</div> ',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0',
  '1',
  (SELECT `id` FROM `plugin_pages_layouts`   WHERE `layout`   = 'content' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'DEFAULT' LIMIT 1)
  );


INSERT IGNORE INTO `plugin_pages_pages` (`name_tag`, `title`, `content`, `date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`) VALUES
(
  'request-a-callback',
  'Request a Callback',
  '<h1>Request a Callback</h1><div class=\"formrt\">{form-RequestCallback}</div> ',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0',
  '1',
  (SELECT `id` FROM `plugin_pages_layouts`   WHERE `layout`   = 'content' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'DEFAULT' LIMIT 1)
);

UPDATE `plugin_formbuilder_forms`
SET   `fields`    = '<input name=\"subject\" value=\"Contact form\" type=\"hidden\"><input name=\"redirect\" value=\"thank-you.html\" type=\"hidden\" id=\"\"><input name=\"event\" value=\"contact-form\" type=\"hidden\" id=\"\"><input name=\"trigger\" value=\"custom_form\" type=\"hidden\" id=\"\"><input name=\"form_type\" value=\"Contact Form\" id=\"\" type=\"hidden\"><input name=\"form_identifier\" value=\"contact_\" type=\"hidden\"><input type=\"hidden\" name=\"email_template\" id=\"\" value=\"contactformmail\"><li><label for=\"callback_form_name\"></label><input type=\"text\" name=\"contact_form_name\" class=\"validate[required]\" id=\"callback_form_name\" placeholder=\"Name*\"></li><li><label for=\"contact_form_tel\"></label><input type=\"text\" name=\"contact_form_email\" id=\"callback_form_name_email\" placeholder=\"Email*\" class=\"validate[required]\"></li><li><label for=\"contact_form_time\"></label><input type="text" name=\"contact_form_time\" id=\"callback_form_name_time\" placeholder=\"What time is suitable?\" />\n</li>                 <li><label></label><button id=\"callback_form_submit\" type=\"submit\" class=\"button\">Submit</button></li>'
WHERE `form_name` = 'RequestCallback';

INSERT IGNORE INTO `plugin_formbuilder_forms` (`form_name`, `action`, `method`, `fields`, `deleted`, `publish`, `date_created`, `date_modified`, `captcha_enabled`, `use_stripe`, `form_id`) VALUES
(
  'Online Consultation',
  'frontend/formprocessor/',
  'POST',
  '<input name=\"subject\" value=\"Contact form\" type=\"hidden\"><input name=\"redirect\" value=\"thank-you.html\" type=\"hidden\"><input name=\"event\" value=\"contact-form\" type=\"hidden\"><input name=\"trigger\" value=\"custom_form\" type=\"hidden\"><input name=\"form_type\" value=\"Contact Form\" type=\"hidden\"><input name=\"form_identifier\" value=\"contact_\" type=\"hidden\"><input type=\"hidden\" name=\"email_template\" value=\"consultationmail\"><input name=\"business_name\" value=\"Premier Hair Restoration Clinic\" type=\"hidden\"><li><label for=\"consultation_form_name\"></label><input type=\"text\" name=\"name\" id=\"consultation_form_name\" placeholder=\"Name*\" class=\"validate[required]\"></li><li><label for=\"consultation_form_telephone\"></label><input type=\"text\" name=\"telephone\" id=\"consultation_form_telephone\" placeholder=\"Telephone Number*\" class=\"validate[required]\"></li><li><label for=\"consultation_form_email\"></label><input type=\"text\" name=\"email\" id=\"consultation_form_email\" placeholder=\"Email\"></li><li><label for=\"\"></label><select name=\"country\" id=\"formbuilder-preview-consultation_form_country\" class=\"validate[required]\">   <option value=\"\">Country</option>   <option value=\"UK\">UK</option>   <option value=\"IRE\">IRE</option>   <option value=\"Rest of World\">Rest of World</option> </select> </li><li>   <h2>Some Medical History</h2> </li> <li>   <p>Are you currently taking any medication?</p> </li><li>      <label for=\"consultation_form_taking_medication_yes\"></label>   <input type=\"radio\" name=\"taking_medication\" id=\"consultation_form_taking_medication_yes\" value=\"1\" class=\"validate[required]\">   <label for=\"consultation_form_taking_medication_yes\"> Yes</label> </li><li>      <label for=\"consultation_form_taking_medication_no\"></label>   <input type=\"radio\" name=\"taking_medication\" id=\"consultation_form_taking_medication_no\" value=\"0\" class=\"validate[required]\">   <label for=\"consultation_form_taking_medication_no\"> No</label> </li> <li>   <h2>Hair Condition</h2> </li><li><label for=\"consultation_form_age\"></label><input type=\"text\" name=\"hair_loss_age\" id=\"consultation_form_age\" placeholder=\"Approximately at what age did your hair loss begin?\"></li>  <li>   <p>Have you had hair restoration before?</p> </li> <li>      <label for=\"consultation_form_had_restoration_before_yes\"></label>   <input type=\"radio\" name=\"had_restoration_before\" id=\"consultation_form_had_restoration_before_yes\" value=\"1\" class=\"validate[required]\">   <label for=\"consultation_form_had_restoration_before_yes\"> Yes</label> </li> <li>        <label for=\"consultation_form_had_restoration_before_no\"></label>   <input type=\"radio\" name=\"had_restoration_before\" id=\"consultation_form_had_restoration_before_no\" value=\"0\" class=\"validate[required]\">   <label for=\"consultation_form_had_restoration_before_no\"> No</label> </li> <li>      <p>At what rate has your hair loss developed?</p> </li>   <li>      <label for=\"consultation_form_hair_loss_development_slow\"></label>   <input type=\"radio\" name=\"hair_loss_development\" id=\"consultation_form_hair_loss_development_slow\" value=\"Slow\" class=\"validate[required]\">   <label for=\"consultation_form_hair_loss_development_slow\"> Slow</label> </li><li>      <label for=\"consultation_form_hair_loss_development_gradual\"></label>   <input type=\"radio\" name=\"hair_loss_development\" id=\"consultation_form_hair_loss_development_gradual\" value=\"Gradual\" class=\"validate[required]\">   <label for=\"consultation_form_hair_loss_development_gradual\"> Gradual</label> </li><li>      <label for=\"consultation_form_hair_loss_development_fast\"></label>   <input type=\"radio\" name=\"hair_loss_development\" id=\"consultation_form_hair_loss_development_fast\" value=\"Fast\" class=\"validate[required]\">   <label for=\"consultation_form_hair_loss_development_fast\"> Fast</label> </li>                <li><label for=\"consultation_form_desired_date\"></label><input type=\"text\" name=\"desired_date\" id=\"consultation_form_desired_date\" placeholder=\"When would you like to have a procedure?*\" class=\"datepicker validate[required]\"></li><li><label for=\"consultation_form_other_information\"></label><textarea name=\"other_information\" id=\"consultation_form_other_information\" placeholder=\"Please leave any extra information you think is important\" rows=\"10\"></textarea></li>                <li><label for=\"\"></label><button type=\"submit\" class=\"button\">Submit</button></li>',
  '0',
  '1',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '0',
  '0',
  'online-consultation-form'
);


INSERT IGNORE INTO `plugin_pages_pages` (`name_tag`, `title`, `content`, `date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`) VALUES
(
  'book-consultation',
  'Book Consultation',
  '<h1>Book Consultation</h1><div class=\"formrt\">{form-Online Consultation}</div> ',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0',
  '1',
  (SELECT `id` FROM `plugin_pages_layouts`   WHERE `layout`   = 'content' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'DEFAULT' LIMIT 1)
);
