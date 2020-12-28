/*
ts:2016-11-07 14:00:00
*/

-- Create messaging template
INSERT INTO `plugin_messaging_notification_templates` (`name`, `description`, `driver`, `type_id`, `subject`, `sender`, `message`, `overwrite_cms_message`, `usable_parameters_in_template`, `date_created`, `created_by`, `date_updated`, `publish`, `deleted`) VALUES
(
  'price-list-request',
  'Email sent to the administration, when a user requests a price list',
  'EMAIL',
  (SELECT IFNULL(`id`, '') FROM `plugin_messaging_notification_types` WHERE `title` = 'email' LIMIT 1),
  'Price List',
  'premierhairrestoration@gmail.com',
  '<table style=\"text-align: left;\">
\n    <tbody>
\n        <tr>
\n            <th scope=\"row\" style=\"padding-right: 1em;\">Name</th>
\n            <td>$name</td>
\n        </tr>
\n        <tr>
\n            <th scope=\"row\" style=\"padding-right: 1em;\">Phone</th>
\n            <td>$phone</td>
\n        </tr>
\n        <tr>
\n            <th scope=\"row\" style=\"padding-right: 1em;\">Email</th>
\n            <td>$email</td>
\n        </tr>
\n        <tr>
\n            <th scope=\"row\" style=\"padding-right: 1em;\">Comments</th>
\n            <td>$comments</td>
\n        </tr>
\n    </tbody>
\n</table>',
  '1',
  '$name,$phone,$email,$comments',
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`,'') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  CURRENT_TIMESTAMP,
  '1',
  '0'
);

-- Recipients for the created template
INSERT INTO `plugin_messaging_notification_template_targets` (`template_id`, `target_type`, `target`, `x_details`, `date_created`) VALUES (
  (SELECT `id` FROM `plugin_messaging_notification_templates` WHERE `name` = 'price-list-request' AND `deleted` = 0 ORDER BY `id` DESC LIMIT 1),
  'EMAIL',
  'premierhairrestoration@gmail.com',
  'to',
  CURRENT_TIMESTAMP
);

-- Form, which will send an email, using the template
INSERT INTO `plugin_formbuilder_forms` (`form_name`, `form_id`, `action`, `method`, `fields`, `options`, `deleted`, `publish`, `date_created`, `date_modified`, `summary`, `captcha_enabled`, `use_stripe`) VALUES
(
  'Request a Price List',
  'Request a Price List',
  'frontend/formprocessor/',
  'POST',
  '
  <input type=\"hidden\" name=\"subject\"        value=\"Price List\" />
  <input type=\"hidden\" name=\"redirect\"       value=\"thank-you.html\" />
  <input type=\"hidden\" name=\"event\"          value=\"price-list-request\" />
  <input type=\"hidden\" name=\"trigger\"        value=\"custom_form\" />
  <input type=\"hidden\" name=\"form_type\"      value=\"Contact Form\" />
  <input type=\"hidden\" name=\"email_template\" value=\"contactformmail\" />
  <li><label for=\"price_list_form_name\" ></label><input type=\"text\" name=\"name\"  class=\"validate[required]\"      id=\"price_list_form_name\"     placeholder=\"Name*\"  /></li>
  <li><label for=\"price_list_form_phone\"></label><input type=\"text\" name=\"phone\" class=\"validate[custom[phone]]\" id=\"price_list_form_phone\"    placeholder=\"Phone\"  /></li>
  <li><label for=\"price_list_form_email\"></label><input type=\"text\" name=\"email\" class=\"validate[required]\"      id=\"price_list_form_email\"    placeholder=\"Email*\" /></li>
  <li><label for=\"price_list_form_comments\"></label><textarea name=\"comments\" class=\"validate[required]\"           id=\"price_list_form_comments\" placeholder=\"Comments*\" rows=\"5\"></textarea></li>
  <li><label></label><button type=\"submit\" class=\"button\">Submit</button></li>',
  '',
  '0',
  '1',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '',
  '0',
  '0'
);

-- Page, which includes the form
-- Create the page, if it doesn't already exist.
INSERT INTO `plugin_pages_pages` (`name_tag`, `title`, `content`,`date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`)
SELECT
  'price-list',
  'Price List',
  '',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0',
  '1',
  `id`,
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'DEFAULT' LIMIT 1)
FROM `plugin_pages_layouts`
WHERE `layout` = 'content'
AND NOT EXISTS (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` IN ('price-list', 'price-list.html') AND `deleted` = 0);

-- Update the page, to include the form
UPDATE `plugin_pages_pages`
SET `content`='<p>Please fill in your details below and you will receive an email from us</p> <div class="formrt">{form-Request a Price List}</div>'
WHERE `name_tag` IN ('price-list', 'price-list.html') AND `deleted` = 0;

-- Remove "tick this box to let us get in touch with you" checkbox
UPDATE `plugin_formbuilder_forms`
SET `fields`='
\n<input type=\"hidden\" name=\"subject\"         value=\"Contact form\" />
\n<input type=\"hidden\" name=\"business_name\"   value=\"Premiere Hair Restoration Clinic\" />
\n<input type=\"hidden\" name=\"redirect\"        value=\"thank-you.html\" />
\n<input type=\"hidden\" name=\"event\"           value=\"contact-form\" />
\n<input type=\"hidden\" name=\"trigger\"         value=\"custom_form\" id=\"trigger\" />
\n<input type=\"hidden\" name=\"form_type\"       value=\"Contact Form\" id=\"form_type\" />
\n<input type=\"hidden\" name=\"form_identifier\" value=\"contact_\" />
\n<input type=\"hidden\" name=\"email_template\"  value=\"contactformmail\" id=\"email_template\" />
\n<li><label for=\"contact_form_name\"></label><input type=\"text\" name=\"contact_form_name\" class=\"validate[required]\" id=\"contact_form_name\" placeholder=\"Name*\"></li>
\n<li style=\"\"><label for=\"contact_form_address\"></label><textarea name=\"contact_form_address\" id=\"contact_form_address\" placeholder=\"Address\" rows=\"5\"></textarea></li>
\n<li><label for=\"contact_form_tel\"></label><input type=\"text\" name=\"contact_form_tel\" id=\"contact_form_tel\" placeholder=\"Phone\"></li>
\n<li><label for=\"contact_form_email_address\"></label><input type=\"text\" name=\"contact_form_email_address\" id=\"contact_form_email_address\" placeholder=\"Email*\" class=\"validate[required]\"></li>
\n<li><label for=\"contact_form_message\"></label><textarea name=\"contact_form_message\" class=\"validate[required]\" id=\"contact_form_message\" placeholder=\"Message*\" rows=\"5\"></textarea></li>
\n<li><label></label><button type=\"submit\" class=\"button\">Submit</button></li>'
WHERE `form_id`='ContactUs';

-- Add new content layout
INSERT INTO `plugin_pages_layouts` (`layout`, `use_db_source`, `publish`, `deleted`, `date_created`, `date_modified`, `created_by`, `modified_by`) VALUES
(
  'content2',
  '0',
  '1',
  '0',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1)
);

-- Panel for the new layout
INSERT INTO `plugin_panels` (`title`, `position`, `order_no`, `type_id`, `image`, `text`, `link_id`, `link_url`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`) VALUES
(
  'Redefining Hair Care',
  'content_left',
  '1',
  (SELECT `id` FROM `plugin_panels_types` WHERE `name` = 'static'),
  '0',
  '<p style=\"text-align:center\"><img alt=\"\" src=\"/shared_media/premierhairgroup/media/photos/content/harp.png\" style=\"height:164px; width:128px\" /></p>\n\n<h2 style=\"text-align:center\"><strong><span style=\"font-size:75px\">PREMIER HAIR</span></strong><br />\n<span style=\"font-size:30px\">RESTORATION CLINIC</span><br />\n<span style=\"font-size:24px\">Ireland</span></h2>\n\n<h3 style=\"text-align:center\"><strong><span style=\"font-size:36px\">REDEFINING&nbsp;HAIR CARE</span></strong></h3>\n',
  '0',
  '',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0'
);

-- Set pages to use the new layout
UPDATE `plugin_pages_pages`
SET
  `layout_id`     = (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'content2' AND `deleted` = 0),
  `last_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1)
WHERE
  `name_tag` IN ('contact-us', 'book-consultation', 'request-a-callback', 'price-list')
AND
  `deleted` = 0;
