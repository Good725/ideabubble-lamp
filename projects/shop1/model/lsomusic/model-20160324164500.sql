/*
ts:2016-03-24 16:45:00
*/
/*
ts:2016-03-24 15:27:00
*/

-- tea time recital form and page
INSERT IGNORE INTO `plugin_formbuilder_forms` (`form_name`, `action`, `method`, `fields`, `deleted`, `publish`, `date_created`, `date_modified`, `captcha_enabled`, `use_stripe`, `form_id`) VALUES
(
  'Tea Time Recital',
  'frontend/formprocessor',
  'POST',
  '<input value=\"custom_form\" name=\"trigger\" type=\"hidden\"><input name=\"event\" value=\"contact-form\" type=\"hidden\"><input name=\"email_template\" value=\"recital_form_mail\" type=\"hidden\"><input value=\"thank-you\" id=\"formbuilder-preview-\" name=\"redirect\" type=\"hidden\"><li><label for=\"date_id\">Date</label><input id=\"date_id\" name=\"date\" type=\"text\" class=\"validate[required] datepicker\"></li><li><label for=\"name_id\">Name</label><input id=\"name_id\" name=\"name\" type=\"text\" class=\"validate[required]\"></li><li><label for=\"grade_id\">Grade</label><input class=\"validate[required]\" id=\"grade_id\" name=\"grade\" type=\"text\"></li><li><label for=\"instrument_id\">Instrument</label><input class=\"validate[required]\" id=\"instrument_id\" name=\"instrument\" type=\"text\"></li><li><label for=\"teacher_id\">Teacher</label><input class=\"validate[required]\" id=\"teacher_id\" name=\"teacher\" type=\"text\"></li><li><label for=\"fpd_id\">Full Program details (Include Composer&apos;s details)</label><textarea id=\"fpd_id\" name=\"fpd\"></textarea></li>                <li><label for=\"submit_btn\"></label><button type=\"submit\" id=\"submit_btn\">Submit</button></li>',
  '0',
  '1',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '0',
  '0',
  'tea-time-recital'
);

INSERT INTO `plugin_pages_pages` (`name_tag`, `title`, `content`, `date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`) VALUES
(
  'tea-time-recital',
  'Tea-Time Recital',
  '<div class=\"lsc\"> <h2><span style=\"color:#274F97;\"><strong>Limerick School of Music</strong></span><strong><span style=\"color:#274F97;\"> </span>| <span style=\"color:#000000;\">Tea Time Recital Form &nbsp;&nbsp; </span></strong></h2>  <h2><span style=\"color:#000000;\"><span style=\"font-size: 14px;\"><strong>Please complete the form below:</strong></span></span></h2> <div class = \"formbuilder-form\">{form-Tea Time Recital}</div>  <p><strong><span style=\"font-size:14px;\"><span style=\"color:#000000;\">Teachers, please submit this form to AJ Ryan&rsquo;s mail slot, together with any piano accompaniment parts, by the relevant closing date.</span></span></strong></p>  <p><br /> <span style=\"font-size:14px;\"><span style=\"color: rgb(39, 79, 151);\"><strong>Please note, Tea Time Recitals take place in LSOM hall at 7pm.</strong></span></span></p> </div> ',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  '1',
  '0',
  '1',
   (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'Content' AND `deleted` = 0 LIMIT 1),
   (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'Default')
);


-- school concert form and page
INSERT IGNORE INTO `plugin_formbuilder_forms` (`form_name`, `action`, `method`, `fields`, `deleted`, `publish`, `date_created`, `date_modified`, `captcha_enabled`, `use_stripe`, `form_id`) VALUES
(
  'School Concert',
  'frontend/formprocessor',
  'POST',
  '<input value="custom_form" name="trigger" type="hidden"><input name="event" value="contact-form" type="hidden"><input name="email_template" value="concert_form_mail" type="hidden"><input value="thank-you" id="formbuilder-preview-" name="redirect" type="hidden"><li><label for="doc_input">Date of Concert</label><input id="doc_input" name="date" type="text" class="validate[required] datepicker"></li><li><label for="name_id">Name</label><input id="name_id" name="name" type="text" class="validate[required]"></li><li><label for="grade_id">Grade</label><input id="grade_id" name="grade" type="text" class="validate[required]"></li><li><label for="instrument_id">Instrument</label><input id="instrument_id" name="instrument" type="text" class="validate[required]"></li><li><label for="teacher_id">Teacher</label><input id="teacher_id" name="teacher" type="text" class="validate[required]"></li><li><label for="fulltitleofpiece_id">Full Title of Piece</label><input id="fulltitleofpiece_id" name="fulltitleofpiece" type="text" class="validate[required]"></li><li><label for="composer_id">Composer Surname</label><input id="composer_id" name="composer_surname" type="text" class="validate[required]"></li><li><label for="firstname_id">First Name</label><input id="firstname_id" name="composer_firstname" type="text"></li><li><label for="thrusday_id">You are performing at a school concert on Thursday</label><input id="thrusday_id" name="performing_thursday" type="checkbox"></li>                <li><label for="submit_btn"></label><button id="submit_btn" type="submit">Submit</button></li>',
  '0',
  '1',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '0',
  '0',
  'school-concert-form'
);

INSERT INTO `plugin_pages_pages` (`name_tag`, `title`, `content`, `date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`) VALUES
(
  'school-concert',
  'School Concert',
  '<div class=\"lsc\">\n<h2><span style=\"color:#274F97;\"><strong>Limerick School of Music</strong></span><strong><span style=\"color:#274F97;\"> </span>| <span style=\"color:#000000;\">School Concert Form &nbsp;&nbsp; </span></strong></h2>\n\n<h2><span style=\"color:#000000;\"><span style=\"font-size: 14px;\"><strong>Please complete the form below:</strong></span></span></h2>\n\n<div class=\"formbuilder-form\">{form-School Concert}</div>\n\n<h3><span style=\"color:#000000;font-weight:normal\"><span style=\"font-size: 14px;\">Teachers, please remember it is your responsibility to leave the piano parts in Mr J Davis mail slot by 6.00pm on the Friday before the concert.</span></span></h3>\n\n<p><span style=\"color:#274F97;\"><span style=\"font-size: 14px;\"><strong>Please be at the hall in LSOM for 7.30pm.</strong></span></span></p>\n\n<p><strong><span style=\"color:#000000;\"><span style=\"font-size: 14px;\">Your family and friends are very welcome to come along to listen. Remember to dress appropriately for a concert performance.</span></span></strong></p>\n</div>\n',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  '1',
  '0',
  '1',
   (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'Content' AND `deleted` = 0 LIMIT 1),
   (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'Default')
);
