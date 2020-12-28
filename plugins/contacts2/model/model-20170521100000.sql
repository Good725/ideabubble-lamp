/*
ts:2017-05-21 10:00:00
*/

DELETE FROM plugin_todos_related_list WHERE `title` = 'contacts2' /*2*/;
INSERT INTO `plugin_todos_related_list` (`title`, `related_table_name`, `related_table_id_column`, `related_table_title_column`, `related_table_deleted_column`, `related_open_link_url`) VALUES ('contacts2', 'plugin_contacts_contact', 'id', 'DB::expr(CONCAT(first_name, \' \', last_name))', 'deleted', '/admin/contacts2/edit/') /*typo fix*/;
