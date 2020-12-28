/*
ts:2020-05-12 21:51:00
*/

INSERT INTO `engine_lookup_fields` (`name`) VALUES ('How did you hear');
INSERT INTO `engine_lookup_values` (`field_id`, `label`, `value`, `is_default`, `public`) VALUES ((SELECT id FROM `engine_lookup_fields` WHERE `name` = 'How did you hear'), 'Advertisement', '1', '0', '0');
INSERT INTO `engine_lookup_values` (`field_id`, `label`, `value`, `is_default`, `public`) VALUES ((SELECT id FROM `engine_lookup_fields` WHERE `name` = 'How did you hear'), 'Booklet', '2', '0', '0');
INSERT INTO `engine_lookup_values` (`field_id`, `label`, `value`, `is_default`, `public`) VALUES ((SELECT id FROM `engine_lookup_fields` WHERE `name` = 'How did you hear'), 'Email/Newsletter', '3', '0', '0');
INSERT INTO `engine_lookup_values` (`field_id`, `label`, `value`, `is_default`, `public`) VALUES ((SELECT id FROM `engine_lookup_fields` WHERE `name` = 'How did you hear'), 'Facebook', '4', '0', '0');
INSERT INTO `engine_lookup_values` (`field_id`, `label`, `value`, `is_default`, `public`) VALUES ((SELECT id FROM `engine_lookup_fields` WHERE `name` = 'How did you hear'), 'Family or Friend', '5', '0', '0');
INSERT INTO `engine_lookup_values` (`field_id`, `label`, `value`, `is_default`, `public`) VALUES ((SELECT id FROM `engine_lookup_fields` WHERE `name` = 'How did you hear'), 'Magazine Article', '6', '0', '0');
INSERT INTO `engine_lookup_values` (`field_id`, `label`, `value`, `is_default`, `public`) VALUES ((SELECT id FROM `engine_lookup_fields` WHERE `name` = 'How did you hear'), 'Newspaper', '7', '0', '0');
INSERT INTO `engine_lookup_values` (`field_id`, `label`, `value`, `is_default`, `public`) VALUES ((SELECT id FROM `engine_lookup_fields` WHERE `name` = 'How did you hear'), 'Twitter', '8', '0', '0');
INSERT INTO `engine_lookup_values` (`field_id`, `label`, `value`, `is_default`, `public`) VALUES ((SELECT id FROM `engine_lookup_fields` WHERE `name` = 'How did you hear'), 'YouTube', '9', '0', '0');
INSERT INTO `engine_lookup_values` (`field_id`, `label`, `value`, `is_default`, `public`) VALUES ((SELECT id FROM `engine_lookup_fields` WHERE `name` = 'How did you hear'), 'Search Engine', '10', '0', '0');
INSERT INTO `engine_lookup_values` (`field_id`, `label`, `value`, `is_default`, `public`) VALUES ((SELECT id FROM `engine_lookup_fields` WHERE `name` = 'How did you hear'), 'Website', '11', '0', '0');

INSERT INTO `engine_settings` (
                                   `variable`,
                                   `name`,
                                   `linked_plugin_name`,
                                   `value_live`,
                                   `value_stage`,
                                   `value_test`,
                                   `value_dev`,
                                   `default`,
                                   `note`,
                                   `type`,
                                   `group`,
                                   `options`)
     VALUES ('how_did_you_hear_enabled',
            'Show How did you hear about us? in the cart',
            'bookings',
            '0',
            '0',
            '0',
            '0',
            '0',
            'Show Pre-Pay and PAYG headings in the cart',
            'toggle_button',
            'Checkout',
            'Model_Settings,on_or_off');