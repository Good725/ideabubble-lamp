/*
ts: 2020-06-15 10:51:01
*/

ALTER /*nodalmerror*/ TABLE `plugin_automations_has_sequences_has_conditions_has_values` DROP FOREIGN KEY `plugin_automations_has_sequences_has_conditions_has_values_ibfk_1`;
ALTER TABLE `plugin_automations_has_sequences_has_conditions_has_values` ADD CONSTRAINT `sequence_condition_id` FOREIGN KEY (`sequence_condition_id`) REFERENCES `plugin_automations_has_sequences_has_conditions` (`id`) ON DELETE CASCADE;
