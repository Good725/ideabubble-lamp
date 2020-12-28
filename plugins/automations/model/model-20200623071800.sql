/*
ts:2020-06-23 07:18:00
*/

ALTER TABLE plugin_automations_has_sequences_has_attachments ADD COLUMN share TINYINT NOT NULL DEFAULT 0;

ALTER TABLE plugin_automations_log ADD COLUMN do_not_repeat_key VARCHAR(50);
ALTER TABLE plugin_automations_log ADD KEY (do_not_repeat_key);

