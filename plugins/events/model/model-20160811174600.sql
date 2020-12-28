/*
ts:2016-08-11 17:46:00
*/
INSERT INTO `engine_lookup_fields` (`name`) VALUES ('Event Category');
INSERT INTO `engine_lookup_fields` (`name`) VALUES ('Event Topic');

SET  @cat_id = (SELECT id FROM engine_lookup_fields WHERE `name` = 'Event Category');
SET  @topic_id = (SELECT id FROM engine_lookup_fields WHERE `name` = 'Event Topic');

INSERT INTO engine_lookup_values
(`label`, `value`,`field_id`,`autor`) SELECT plugin_events_topics.`name`,plugin_events_topics.id,@topic_id,plugin_events_topics.created_by FROM `plugin_events_topics`;

INSERT INTO engine_lookup_values
(`label`, `value`,`field_id`) SELECT `category`,id,@cat_id FROM `plugin_events_categories`;
