/*
ts:2015-01-01 00:01:00
*/

-- only need project edits here note system and plugins will update themselves
-- ---------------------------------
-- PCSYS-2 Develop Frontend
-- ---------------------------------
UPDATE `ppages_layouts` SET `layout` = 'home' WHERE `ppages_layouts`.`id` =1;
UPDATE `ppages_layouts` SET `layout` = 'content' WHERE `ppages_layouts`.`id` =2;
UPDATE `ppages_layouts` SET `layout` = 'products' WHERE `ppages_layouts`.`id` =3;
UPDATE `ppages_layouts` SET `layout` = 'checkout' WHERE `ppages_layouts`.`id` =4;
UPDATE `ppages_layouts` SET `layout` = 'register' WHERE `ppages_layouts`.`id` =5;
INSERT IGNORE INTO `ppages_layouts` ( `id` , `layout` ) VALUES ( 6 , 'search' );

INSERT IGNORE INTO `ppages` (
`id` ,
`name_tag` ,
`title` ,
`content` ,
`banner_photo` ,
`seo_keywords` ,
`seo_description` ,
`footer` ,
`date_entered` ,
`last_modified` ,
`created_by` ,
`modified_by` ,
`publish` ,
`deleted` ,
`include_sitemap` ,
`layout_id` ,
`category_id`
)
VALUES ( NULL , 'search.html', 'search', '', NULL , '', '', '', NULL, NULL, '2', NULL , '1', '0', '1', '6', '2' );

INSERT IGNORE INTO `ppages` (
`id` ,
`name_tag` ,
`title` ,
`content` ,
`banner_photo` ,
`seo_keywords` ,
`seo_description` ,
`footer` ,
`date_entered` ,
`last_modified` ,
`created_by` ,
`modified_by` ,
`publish` ,
`deleted` ,
`include_sitemap` ,
`layout_id` ,
`category_id`
)
VALUES ( NULL , 'products.html', 'products', '', '', '', '', '', NULL, NULL, '2', '2', '1', '0', '1', '3', '2' );

-- -----------------------------------------------
-- PCSYS-27 Content 1 Layout: Styling required
-- -----------------------------------------------
INSERT IGNORE INTO `ppages_layouts` (`layout`) VALUES ('content-1');


-- -----------------------------------------------
-- PCSYS-39 Add brand logos panel above footer
-- -----------------------------------------------
INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`) VALUES ('footer_logos_text', 'Footer Logos text', '<h2>Top Brands</h2>\n<p>We stock 1000s of your favourite top brands.</p>\n', '<h2>Top Brands</h2>\n<p>We stock 1000s of your favourite top brands.</p>\n', '<h2>Top Brands</h2>\n<p>We stock 1000s of your favourite top brands.</p>\n', '<h2>Top Brands</h2>\n<p>We stock 1000s of your favourite top brands.</p>\n', '<h2>Top Brands</h2>\n<p>We stock 1000s of your favourite top brands.</p>\n', 'Text to display before the footer logos', 'wysiwyg', 'General Settings');
INSERT IGNORE INTO `shared_media_photo_presets` (`title`, `directory`, `height_large`, `width_large`, `action_large`, `thumb`) VALUES ('Company Logos', 'menus', '75', '130', 'fith', '0');

-- -----------------------------------------------
-- PCSYS-72 - registration form
-- -----------------------------------------------

insert into `plugin_notifications_event` set `name` = 'register-account', `description` = 'register-account', `from` = '', `subject` = 'Register Account', `header` = '', `footer` = '';
	select last_insert_id() into @refid_plugin_notifications_event;
	insert into `plugin_notifications_to` set `id_event` = @refid_plugin_notifications_event, `id_contact` = '1';
	insert into `plugin_notifications_cc` set `id_event` = @refid_plugin_notifications_event, `id_contact` = '3';

insert into `plugin_formbuilder_forms` set `form_name` = 'Register Account', `action` = 'frontend/formprocessor/', `method` = 'POST', `class` = null, `fields` = '<input value=\"Registration Form\" name=\"subject\" type=\"hidden\"><input value=\"pcsystems\" name=\"business_name\" type=\"hidden\"><input id=\"\" value=\"registration-awaiting-approval.html\" name=\"redirect\" type=\"hidden\"><input id=\"\" value=\"register-account\" name=\"event\" type=\"hidden\"><input id=\"trigger\" value=\"register_account\" name=\"trigger\" type=\"hidden\"><input id=\"form_type\" value=\"Registration Account\" name=\"form_type\" type=\"hidden\"><input id=\"\" value=\"register_account_\" name=\"form_identifier\" type=\"hidden\"><input id=\"email_template\" value=\"registeraccountmail\" name=\"email_template\" type=\"hidden\"><li><label for=\"register_email\">Email</label><input class=\"validate[required,email]\" id=\"register_email\" name=\"email\" type=\"text\"></li><li><label for=\"register_firstname\">First Name</label><input class=\"validate[required]\" id=\"register_firstname\" name=\"firstname\" type=\"text\"></li><li><label for=\"register_surname\">Last Name</label><input class=\"validate[required]\" id=\"register_surname\" name=\"surname\" type=\"text\"></li><li><label for=\"register_phone\">Phone</label><input class=\"validate[required]\" id=\"register_phone\" name=\"phone\" type=\"text\"></li><li><label for=\"register_address_line1\">Address Line 1</label><input class=\"validate[required]\" id=\"register_address_line1\" name=\"address_line1\" type=\"text\"></li><li><label for=\"register_address_line2\">Address Line 2</label><input class=\"validate[required]\" id=\"register_address_line2\" name=\"address_line2\" type=\"text\"></li><li><label for=\"register_county\">County</label><input class=\"validate[required]\" id=\"register_county\" name=\"county\" type=\"text\"></li><li><label for=\"register_company\">Company</label><input class=\"validate[required]\" id=\"register_company\" name=\"company\" type=\"text\"></li><li><label for=\"register_country\">Country</label><input class=\"validate[required]\" id=\"register_country\" name=\"country\" type=\"text\"></li><li><label for=\"register_password\">Password</label><input class=\"validate[required]\" id=\"register_password\" name=\"password\" type=\"password\"></li><li><label for=\"register_repassword\">Reenter Password</label><input class=\"validate[required], validate[required,equals[register_password]]\" id=\"register_repassword\" name=\"repassword\" type=\"password\"></li><li><label for=\"register_account_type\"> Registration Type</label><select id=\"register_account_type\" name=\"account_type\"><option value=\"Industrial\">Industrial</option><option value=\"Pharmaceutical\">Pharmaceutical</option><option value=\"Education\">Education</option><option value=\"Trade\">Trade</option><option value=\"IT Manager\">IT Manager</option><option value=\"Other\">Other</option></select></li><li><label for=\"register_account_type_other\"></label><input id=\"register_account_type_other\" name=\"account_type_other\" type=\"text\"></li><li><label for=\"register_heard_from\">Where did you hear about us?</label><select id=\"register_heard_from\" name=\"heard_from\"><option value=\"Already Did Business\">Already Did Business</option><option value=\"Newspaper\">Newspaper</option><option value=\"Radio\">Radio</option><option value=\"TV\">TV</option><option value=\"A Friend\">A Friend</option><option value=\"Google\">Google</option></select></li>                <li><label for=\"\"></label><input type=\"submit\"></li>', `options` = 'redirect:3|failpage:3', `deleted` = null, `publish` = '1', `date_created` = null, `date_modified` = '2015-04-12 20:01:18', `summary` = '', `captcha_enabled` = null, `form_id` = 'register-account';

delete from `ppages` where `name_tag` = 'register-account.html';
insert into `ppages` set `name_tag` = 'register-account.html', `title` = 'Register Account', `content` = '<div class=\"registration\">\n<h2>Register Account</h2>\n\n<p class=\"register-note\">If you already have an account with us, please login at the <a href=\"http://pcsystems.websitecms.ie/login.html\">login page</a>.</p>\n\n<form action=\"frontend/formprocessor/\" class=\"formrt\" id=\"account_registration_form\" method=\"post\">{form-2}</form>\n\n<p><strong>Please note you will receive an email to verify your account upon registration.</strong></p>\n</div>\n', `banner_photo` = '', `seo_keywords` = '', `seo_description` = '', `footer` = '', `date_entered` = '2014-11-26 14:16:47', `last_modified` = '2015-04-14 08:34:59', `created_by` = '2', `modified_by` = '2', `publish` = '1', `deleted` = '0', `include_sitemap` = '1', `layout_id` = '5', `category_id` = '1';


-- -----------------------------------------------
-- PCSYS-61 Content 2 Layout please create
-- -----------------------------------------------
INSERT IGNORE INTO `ppages_layouts` (`layout`) VALUES ('content-n-p'), ('content-n-p-c');

-- -----------------------------------------------
-- PCSYS-59 subscribe form
-- -----------------------------------------------

insert IGNORE into `plugin_notifications_event` set `name` = 'subscribe-to-newsletter', `description` = 'subscribe-to-newsletter', `from` = '', `subject` = 'Newsletter Subscription', `header` = '', `footer` = '';
insert IGNORE into `plugin_formbuilder_forms` set `form_name` = 'Subscribe to Newsletter', `action` = 'frontend/formprocessor/', `method` = 'POST', `class` = null, `fields` = '<fieldset><legend>Subscribe to our Newsletter</legend><input value=\"Newsletter Sign Up\" name=\"subject\" type=\"hidden\"><input value=\"PC Systems\" name=\"business_name\" type=\"hidden\"><input value=\"thank-you-subscribing.html\" name=\"redirect\" type=\"hidden\"><input id=\"\" value=\"subscribe-to-newsletter\" name=\"event\" type=\"hidden\"><input value=\"subscribe\" id=\"trigger\" name=\"trigger\" type=\"hidden\"><input id=\"form_type\" value=\"Newsletter Form\" name=\"form_type\" type=\"hidden\"><input value=\"newsletter_\" name=\"form_identifier\" type=\"hidden\"><input id=\"email_template\" value=\"subscribeformmail\" name=\"email_template\" type=\"hidden\"><li><label for=\"form_name\">Name</label><input class=\"validate[required]\" id=\"form_name\" name=\"form_name\" type=\"text\"></li><li><label for=\"form_email_address\">Email</label><input class=\"validate[required]\" id=\"form_email_address\" name=\"form_email_address\" type=\"text\"></li><li><label for=\"submit\"></label><button id=\"submit\" name=\"submit\">Subscribe to Newsletter</button></li></fieldset>', `options` = 'redirect:3|failpage:3', `deleted` = null, `publish` = '1', `date_created` = null, `date_modified` = '2015-04-23 20:10:18', `summary` = '', `captcha_enabled` = null, `form_id` = 'newsletter';

-- -----------------------------------------------
-- PCSYS-142 new field entry for existing customer
-- -----------------------------------------------
Update plugin_formbuilder_forms set fields = '<input value=\"Registration Form\" name=\"subject\" type=\"hidden\"><input value=\"pcsystems\" name=\"business_name\" type=\"hidden\"><input id=\"\" value=\"registration-awaiting-approval.html\" name=\"redirect\" type=\"hidden\"><input id=\"\" value=\"register-account\" name=\"event\" type=\"hidden\"><input id=\"trigger\" value=\"register_account\" name=\"trigger\" type=\"hidden\"><input id=\"form_type\" value=\"Registration Account\" name=\"form_type\" type=\"hidden\"><input id=\"\" value=\"register_account_\" name=\"form_identifier\" type=\"hidden\"><input id=\"email_template\" value=\"registeraccountmail\" name=\"email_template\" type=\"hidden\"><li><label for=\"register_email\">Email</label><input class=\"validate[required,email]\" id=\"register_email\" name=\"email\" type=\"text\"></li><li><label for=\"register_firstname\">First Name</label><input class=\"validate[required]\" id=\"register_firstname\" name=\"firstname\" type=\"text\"></li><li><label for=\"register_surname\">Last Name</label><input class=\"validate[required]\" id=\"register_surname\" name=\"surname\" type=\"text\"></li><li><label for=\"register_phone\">Phone</label><input class=\"validate[required]\" id=\"register_phone\" name=\"phone\" type=\"text\"></li><li><label for=\"register_address_line1\">Address Line 1</label><input class=\"validate[required]\" id=\"register_address_line1\" name=\"address_line1\" type=\"text\"></li><li><label for=\"register_address_line2\">Address Line 2</label><input class=\"validate[required]\" id=\"register_address_line2\" name=\"address_line2\" type=\"text\"></li><li><label for=\"register_county\">County</label><input class=\"validate[required]\" id=\"register_county\" name=\"county\" type=\"text\"></li><li><label for=\"register_company\">Company</label><input class=\"validate[required]\" id=\"register_company\" name=\"company\" type=\"text\"></li><li><label for=\"register_country\">Country</label><input class=\"validate[required]\" id=\"register_country\" name=\"country\" type=\"text\"></li><li><label for=\"register_password\">Password</label><input class=\"validate[required]\" id=\"register_password\" name=\"password\" type=\"password\"></li><li><label for=\"register_repassword\">Reenter Password</label><input class=\"validate[required], validate[required,equals[register_password]]\" id=\"register_repassword\" name=\"repassword\" type=\"password\"></li><li><label for=\"register_account_type\"> Registration Type</label><select id=\"register_account_type\" name=\"account_type\"><option value=\"IT Manager\">IT Manager</option><option value=\"Trade\">Trade</option><option value=\"Existing Customer\">Existing Customer</option><option value=\"Education\">Education</option><option value=\"Pharmaceutical\">Pharmaceutical</option><option value=\"Industrial\">Industrial</option><option value=\"Other\">Other</option></select></li><li><label for=\"register_account_type_other\"></label><input id=\"register_account_type_other\" name=\"account_type_other\" type=\"text\"></li><li><label for=\"register_heard_from\">Where did you hear about us?</label><select id=\"register_heard_from\" name=\"heard_from\"><option value=\"Already Did Business\">Already Did Business</option><option value=\"Newspaper\">Newspaper</option><option value=\"Radio\">Radio</option><option value=\"TV\">TV</option><option value=\"A Friend\">A Friend</option><option value=\"Google\">Google</option></select></li><li><label for=\"\"></label><input type=\"submit\" class=\"button button-primary\" value=\"Register\"></li>' where `form_id` = 'register-account';

-- -----------------------------------------------
-- PCSYS-121 Footer Styling from Designer
-- -----------------------------------------------
UPDATE `plugin_formbuilder_forms` SET `fields` = '<input value="Newsletter Sign Up" name="subject" type="hidden"><input value="PC Systems" name="business_name" type="hidden"><input value="thank-you-subscribing.html" name="redirect" type="hidden"><input id="" value="subscribe-to-newsletter" name="event" type="hidden"><input value="subscribe" id="trigger" name="trigger" type="hidden"><input id="form_type" value="Newsletter Form" name="form_type" type="hidden"><input value="newsletter_" name="form_identifier" type="hidden"><input id="email_template" value="subscribeformmail" name="email_template" type="hidden"><li><fieldset id="newsletter_fieldset"><legend><header> 	<h3>Sign up to our Newsletter</h3> 	<p>Exclusive Deals &amp; Offers!</p> </header></legend><ul><li><label for="form_name">Name</label><input class="validate[required]" id="form_name" name="form_name" type="text"></li><li><label for="form_email_address">Email</label><input class="validate[required]" id="form_email_address" name="form_email_address" type="text"></li><li><label for="submit"></label><button id="submit" name="submit">Submit</button></li></ul></fieldset></li>' WHERE `form_name` = 'Subscribe to Newsletter';

UPDATE IGNORE `plugin_formbuilder_forms` SET `fields` = '<input value="Newsletter Sign Up" name="subject" type="hidden"><input value="PC Systems" name="business_name" type="hidden"><input value="thank-you-subscribing.html" name="redirect" type="hidden"><input id="" value="subscribe-to-newsletter" name="event" type="hidden"><input value="subscribe" id="trigger" name="trigger" type="hidden"><input id="form_type" value="Newsletter Form" name="form_type" type="hidden"><input value="newsletter_" name="form_identifier" type="hidden"><input id="email_template" value="subscribeformmail" name="email_template" type="hidden"><li><fieldset id="newsletter_fieldset"><legend><header> 	<h3>Sign up to our Newsletter</h3> 	<p>Exclusive Deals &amp; Offers!</p> </header></legend><ul><li><label for="newsletter_form_name">Name</label><input class="validate[required]" id="newsletter_form_name" name="newsletter_form_name" type="text"></li><li><label for="newsletter_form_email_address">Email</label><input class="validate[required]" id="newsletter_form_email_address" name="newsletter_form_email_address" type="text"></li><li><label for="submit"></label><button id="submit" name="submit">Submit</button></li></ul></fieldset></li>' WHERE `form_name` = 'Subscribe to Newsletter';

