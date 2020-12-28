/*
ts:2016-08-25 10:12:00
*/

ALTER TABLE plugin_transactions_transactions ADD COLUMN reason VARCHAR(100);

INSERT INTO engine_notes_types
  (`type`, `referenced_table`, `referenced_table_id`, `referenced_table_deleted`)
  VALUES
  ('Transaction', 'plugin_transactions_transactions', 'id', 'deleted');

INSERT INTO engine_notes_types
  (`type`, `referenced_table`, `referenced_table_id`, `referenced_table_deleted`)
  VALUES
  ('Payment', 'plugin_transactions_payments', 'id', 'deleted');
