/*
ts:2015-01-01 00:01:00
*/

-- -------------------------------------
-- MCN-8 - Wire up contact form
-- -------------------------------------
INSERT IGNORE INTO `plugin_notifications_event`
(`name`,                          `description`,                   `subject`)
VALUES
('contact-form',                  'Contact Us',                    'Website Enquiry'),
('successful-payment-seller',     'Successful Payment (Seller)',   'You have received a new website sale'),
('successful-payment-customer',   'Successful Payment (Customer)', 'Order confirmation - Thank you for your purchase'),
('consultation-form',             'Online Consultation',           'Online Consultation'),
('enquiry_form',                  'Enquire',                       'Enquiry'),
('contact_notification_callback', 'Request a Callback',            'Request a Callback');

INSERT IGNORE INTO `engine_site_templates` (`title`, `stub`, `type`, `date_created`, `date_modified`) VALUES
('Content1 - 02',          '02',          'website', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Content1 - Home Wide',   'home_wide',   'website', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Content1 - Wide Banner', 'wide_banner', 'website', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Content1 - Dated',       'dated',       'website', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP)
;

INSERT IGNORE INTO `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT '02', '02', `id`, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP FROM `engine_site_templates`  WHERE `stub` = 'default';
INSERT IGNORE INTO `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT '03', '03', `id`, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP FROM `engine_site_templates`  WHERE `stub` = 'default';
INSERT IGNORE INTO `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT '04', '04', `id`, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP FROM `engine_site_templates`  WHERE `stub` = 'default';
INSERT IGNORE INTO `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT '05', '05', `id`, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP FROM `engine_site_templates`  WHERE `stub` = '02';
INSERT IGNORE INTO `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT '06', '06', `id`, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP FROM `engine_site_templates`  WHERE `stub` = 'default';
INSERT IGNORE INTO `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT '07', '07', `id`, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP FROM `engine_site_templates`  WHERE `stub` = 'default';
INSERT IGNORE INTO `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT '08', '08', `id`, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP FROM `engine_site_templates`  WHERE `stub` = 'default';
INSERT IGNORE INTO `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT '09', '09', `id`, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP FROM `engine_site_templates`  WHERE `stub` = 'default';
INSERT IGNORE INTO `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT '10', '10', `id`, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP FROM `engine_site_templates`  WHERE `stub` = 'default';
INSERT IGNORE INTO `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT '11', '11', `id`, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP FROM `engine_site_templates`  WHERE `stub` = 'default';
INSERT IGNORE INTO `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT '12', '12', `id`, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP FROM `engine_site_templates`  WHERE `stub` = 'wide_banner';
INSERT IGNORE INTO `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT '13', '13', `id`, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP FROM `engine_site_templates`  WHERE `stub` = 'home_wide';
INSERT IGNORE INTO `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT '14', '14', `id`, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP FROM `engine_site_templates`  WHERE `stub` = 'default';
INSERT IGNORE INTO `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT '15', '15', `id`, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP FROM `engine_site_templates`  WHERE `stub` = 'default';
INSERT IGNORE INTO `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT '16', '16', `id`, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP FROM `engine_site_templates`  WHERE `stub` = 'default';
INSERT IGNORE INTO `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT '17', '17', `id`, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP FROM `engine_site_templates`  WHERE `stub` = 'default';
INSERT IGNORE INTO `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT '18', '18', `id`, CURRENT_TIMESTAMP, CURRENT_TIMESTAMP FROM `engine_site_templates`  WHERE `stub` = 'default';
