/*
ts:2020-10-07 17:50:00
*/

INSERT IGNORE INTO
    `plugin_messaging_notification_templates` (
    `name`,
    `driver`,
    `type_id`,
    `subject`,
    `sender`,
    `message`,
    `overwrite_cms_message`,
    `date_created`,
    `created_by`,
    `date_updated`,
    `publish`,
    `deleted`,
    `usable_parameters_in_template`,
    `category_id`
)VALUES ( 'thank_you_for_contacting_us',
          'EMAIL',
          (SELECT `id` FROM `plugin_messaging_notification_types` WHERE `title` = 'email' LIMIT 1),
          'Thank you for contacting us ',
          'noreply@websitecms.ie',
          '<p>Hi, $first_name ,</p> <p>Thank you for contacting us. We will be in touch at your $email</p>',
          '1',
          CURRENT_TIMESTAMP,
          (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
          CURRENT_TIMESTAMP,
          '1',
          '0',
          '$base_url, $email, $first_name, $validation',
          (SELECT `id` FROM `plugin_messaging_notification_categories` WHERE `name` = 'Auth' LIMIT 1)
        );

UPDATE `plugin_messaging_notification_templates` SET `message`  =
                                                         '<p>Hi, $first_name ,</p> <p>Thank you for contacting us. We will be in touch at your $email</p>',
                                                     `usable_parameters_in_template` = '$base_url, $email, $first_name'
WHERE `name` = 'thank_you_for_contacting_us';