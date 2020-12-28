/*
ts:2015-01-01 00:01:00
*/
-- only need project edits here note system and plugins will update themselves
-- ---------------------------------------
-- QI-67 - x3 Contact Forms Not Working
-- ---------------------------------------
INSERT IGNORE INTO `plugin_notifications_event` (`name`, `description`, `subject`) VALUES ('enquiry_form', 'Enquire', 'Enquiry');
INSERT IGNORE INTO `plugin_notifications_event` (`name`, `description`, `subject`) VALUES ('contact_notification_callback', 'Request a Callback', 'Request a Callback');

-- ---------------------------------------
-- NBS-13 Notifications to be setup
-- ---------------------------------------
INSERT IGNORE INTO `plugin_notifications_event`
 (`name`,                        `description`,                   `from`, `subject`) VALUES
 ('contact-form',                'Contact Us',                    '',     'Website Enquiry'),
 ('successful-payment-seller',   'Successful Payment (Seller)',   '',     'You have received a new website sale'),
 ('successful-payment-customer', 'Successful Payment (Customer)', '',     'Order confirmation - Thank you for your purchase');

-- MHI-6 Top Header Contact Info
INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `options`)
VALUES ('header_contact_details', 'Header Contact Details', '0', '0', '0', '0', '0', 'both', 'Display contact details in the site header', 'toggle_button', 'Contact Us', 'Model_Settings,on_or_off');

-- ---------------------------------------
-- GP-23 Checkout Options
-- ---------------------------------------
INSERT IGNORE INTO `settings` (`variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `note`, `type`, `group`, `options`)
VALUES ('checkout_delivery_options', 'Reserve and Collect Options', '0', '0', '0', '0', '0', 'Add extra delivery options for reserving and collecting products at the checkout.', 'toggle_button', 'Shop Checkout', 'Model_Settings,on_or_off');

UPDATE IGNORE `settings` SET `name` = 'Reserve and Collect Options', `note` = 'Add extra delivery options for reserving and collecting products at the checkout.', `group` = 'Shop Checkout' WHERE `variable` = 'checkout_delivery_options';


INSERT IGNORE INTO `engine_site_templates` (`title`, `stub`, `type`, `date_created`, `date_modified`) VALUES
('Content1 - 2 Column',    '2col',        'website', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Content1 - Home Wide',   'home_wide',   'website', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Content1 - A',           'a',           'website', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Content1 - Course',      'course',      'website', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Content1 - Systems',     'systems',     'website', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Content1 - Books',       'books',       'website', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
;

INSERT IGNORE INTO `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT '01', '01', `id`, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP FROM `engine_site_templates`  WHERE `stub` = 'default';
INSERT IGNORE INTO `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT '02', '02', `id`, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP FROM `engine_site_templates`  WHERE `stub` = '2col';
INSERT IGNORE INTO `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT '03', '03', `id`, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP FROM `engine_site_templates`  WHERE `stub` = 'home_wide';
INSERT IGNORE INTO `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT '04', '04', `id`, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP FROM `engine_site_templates`  WHERE `stub` = 'a';
INSERT IGNORE INTO `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT '05', '05', `id`, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP FROM `engine_site_templates`  WHERE `stub` = 'home_wide';
INSERT IGNORE INTO `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT '06', '06', `id`, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP FROM `engine_site_templates`  WHERE `stub` = 'home_wide';
INSERT IGNORE INTO `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT '07', '07', `id`, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP FROM `engine_site_templates`  WHERE `stub` = 'home_wide';
INSERT IGNORE INTO `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT '08', '08', `id`, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP FROM `engine_site_templates`  WHERE `stub` = 'home_wide';
INSERT IGNORE INTO `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT '09', '09', `id`, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP FROM `engine_site_templates`  WHERE `stub` = 'a';
INSERT IGNORE INTO `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT '10', '10', `id`, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP FROM `engine_site_templates`  WHERE `stub` = 'home_wide';
INSERT IGNORE INTO `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT '11', '11', `id`, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP FROM `engine_site_templates`  WHERE `stub` = 'a';
INSERT IGNORE INTO `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT '12', '12', `id`, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP FROM `engine_site_templates`  WHERE `stub` = 'a';
INSERT IGNORE INTO `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT '13', '13', `id`, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP FROM `engine_site_templates`  WHERE `stub` = 'home_wide';
INSERT IGNORE INTO `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT '14', '14', `id`, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP FROM `engine_site_templates`  WHERE `stub` = 'a';
INSERT IGNORE INTO `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT '15', '15', `id`, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP FROM `engine_site_templates`  WHERE `stub` = 'home_wide';
INSERT IGNORE INTO `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT '16', '16', `id`, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP FROM `engine_site_templates`  WHERE `stub` = 'course';
INSERT IGNORE INTO `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT '17', '17', `id`, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP FROM `engine_site_templates`  WHERE `stub` = 'systems';
INSERT IGNORE INTO `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT '19', '19', `id`, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP FROM `engine_site_templates`  WHERE `stub` = 'a';
INSERT IGNORE INTO `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT '20', '20', `id`, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP FROM `engine_site_templates`  WHERE `stub` = 'books';
INSERT IGNORE INTO `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT '21', '21', `id`, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP FROM `engine_site_templates`  WHERE `stub` = 'books';

-- Rape Crisis theme
INSERT IGNORE INTO `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT '22', '22', `id`, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP FROM `engine_site_templates`  WHERE `stub` = 'home_wide';
