/*
ts:2020-04-22 20:00:00
*/
DELIMITER ;;

-- Create the email notification
INSERT IGNORE INTO `plugin_messaging_notification_templates` (`name`, `description`, `driver`, `type_id`, `subject`, `message`, `overwrite_cms_message`, `date_created`, `created_by`)
VALUES (
  'course_accreditation_application',
  'Email sent to a student, inviting them to register for accreditation ',
  'EMAIL',
  (SELECT `id` FROM `plugin_messaging_notification_types` WHERE `title` = 'email'),
  'TU Dublin accreditation application',
  '',
  '1',
  CURRENT_TIMESTAMP,
  (SELECT IFNULL((SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1), 1))
);;


-- Create the form
INSERT IGNORE INTO `plugin_formbuilder_forms` (`form_name`, `action`, `method`, `deleted`, `publish`, `date_created`,
    `date_modified`, `captcha_enabled`, `captcha_version`, `form_id`)
VALUES (
  'accreditation_application',
  'frontend/formprocessor/',
  'POST',
  '0',
  '',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '0',
  '2',
  'accreditation_application'
);;


-- Add the "accreditation application" page, if it does not already exist.
-- Unpublished by default. Sites that need it can publish.
INSERT IGNORE INTO
  `plugin_pages_pages` (`name_tag`, `title`, `content`,`date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`)
SELECT
  'accreditation-application',
  'Accreditation application',
  '<h1>Accreditation application</h1>\n\n<div>{form-accreditation_application}</div>',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  IFNULL((SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1), 1),
  IFNULL((SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1), 1),
  '0',
  '0',
  '1',
  (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'content' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'DEFAULT' LIMIT 1)
FROM `plugin_pages_pages`
WHERE NOT EXISTS (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` = 'accreditation-application' AND `deleted` = 0)
LIMIT 1;;


-- Create the setting for the page to be used for the accreditation application form. Page not set by default
INSERT IGNORE INTO `engine_settings`(`variable`, `name`, `note`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `type`, `group`, `options`) VALUES
(
  'accreditation_application_page',
  'Accreditation-application page',
  'Page the user uses to complete the course-accreditation application',
  '',
  '',
  '',
  '',
  '',
  'combobox',
  'Bookings',
  'Model_Pages,get_pages_as_options'
);;

-- Add the "application thank you" page, if it does not already exist.
-- Unpublished by default. Sites that need it can publish.
INSERT IGNORE INTO
    `plugin_pages_pages` (`name_tag`, `title`, `content`,`date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`)
SELECT
    'application-thank-you',
    'Thank you for submitting application ',
    '<h1>Thank you for submitting application</h1>',
    CURRENT_TIMESTAMP,
    CURRENT_TIMESTAMP,
    (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
    (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
    '0',
    '0',
    '1',
    (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'thankyou' AND `deleted` = 0 LIMIT 1),
    (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'DEFAULT' LIMIT 1)
FROM `plugin_pages_pages`
WHERE NOT EXISTS (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` = 'application-thank-you' AND `deleted` = 0)
LIMIT 1;;

