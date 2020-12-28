/*
ts:2020-07-06 18:30:00
*/

-- Add learning and booking modes to the schedules table, which are linked to lookups

ALTER TABLE `plugin_courses_schedules`
ADD COLUMN `learning_mode_id` INT(11) NULL AFTER `study_mode_id`,
ADD COLUMN `delivery_mode_id` INT(11) NULL AFTER `learning_mode_id`;

INSERT INTO `engine_lookup_fields` (`name`) VALUES ('Learning mode');

INSERT INTO `engine_lookup_values` (`field_id`, `value`, `label`, `is_default`, `public`) VALUES
((SELECT id FROM `engine_lookup_fields` WHERE `name` = 'Learning mode' LIMIT 1), 'self_paced',  'Self-paced',  '0', '1'),
((SELECT id FROM `engine_lookup_fields` WHERE `name` = 'Learning mode' LIMIT 1), 'trainer_led', 'Trainer-led', '0', '1'),
((SELECT id FROM `engine_lookup_fields` WHERE `name` = 'Delivery mode' LIMIT 1), 'blended',     'Blended',     '0', '1'),
((SELECT id FROM `engine_lookup_fields` WHERE `name` = 'Delivery mode' LIMIT 1), 'classroom',   'Classroom',   '0', '1'),
((SELECT id FROM `engine_lookup_fields` WHERE `name` = 'Delivery mode' LIMIT 1), 'online',      'Online',      '0', '1');
