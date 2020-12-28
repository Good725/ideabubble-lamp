/*
ts:2020-05-12 18:00:00
*/

ALTER TABLE `plugin_testimonials` ADD COLUMN `item_position` VARCHAR(255) NULL AFTER `item_signature`;

INSERT INTO `engine_settings` (`variable`, `name`, `linked_plugin_name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `note`, `type`, `group`, `required`, `options`)
VALUES (
  'enable_testimonial_filters',
  'Enable filters',
  'testimonials',
  '0', '0', '0', '0',
  'Enable filters on the testimonials page',
  'toggle_button',
  'Testimonials',
  '0',
  'Model_Settings,on_or_off'
);
