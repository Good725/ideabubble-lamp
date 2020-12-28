/*
ts:2017-01-20 12:55:00
*/


INSERT IGNORE INTO `engine_localisation_messages` (`message`, `created_on`, `updated_on`) VALUES
('Postcode',             CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Message for the card', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);

INSERT IGNORE INTO `engine_localisation_translations` (`language_id`, `message_id`, `translation`) VALUES (
  (SELECT `id` FROM `engine_localisation_languages` WHERE `code` = 'ga'),
  (SELECT `id` FROM `engine_localisation_messages` WHERE `message` = 'Postcode'),
  'Cód Postála'
);

INSERT IGNORE INTO `engine_localisation_translations` (`language_id`, `message_id`, `translation`) VALUES (
  (SELECT `id` FROM `engine_localisation_languages` WHERE `code` = 'en'),
  (SELECT `id` FROM `engine_localisation_messages` WHERE `message` = 'Postcode'),
  'Postcode'
);

INSERT IGNORE INTO `engine_localisation_translations` (`language_id`, `message_id`, `translation`) VALUES (
  (SELECT `id` FROM `engine_localisation_languages` WHERE `code` = 'en'),
  (SELECT `id` FROM `engine_localisation_messages` WHERE `message` = 'Message for the card'),
  'Message for the card'
);

INSERT IGNORE INTO `engine_localisation_translations` (`language_id`, `message_id`, `translation`) VALUES (
  (SELECT `id` FROM `engine_localisation_languages` WHERE `code` = 'ga'),
  (SELECT `id` FROM `engine_localisation_messages` WHERE `message` = 'Message for the card'),
  'Teachtaireacht don chárta'
);