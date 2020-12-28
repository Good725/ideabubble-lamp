/*
ts:2016-07-12 08:57:00

*/

INSERT INTO plugin_todos_related_list
  (title, related_table_name, related_table_id_column, related_table_title_column, related_table_deleted_column, related_open_link_url)
  VALUES
  ('Booking', 'plugin_ib_educate_bookings', 'booking_id', null, 'delete', '/admin/bookings/add_edit_booking/#ID#');

INSERT INTO plugin_todos_related_list
  (title, related_table_name, related_table_id_column, related_table_title_column, related_table_deleted_column, related_open_link_url)
  VALUES
  ('Accounts', 'plugin_bookings_transactions', 'id', null, 'deleted', null);

INSERT INTO plugin_todos_related_list
  (title, related_table_name, related_table_id_column, related_table_title_column, related_table_deleted_column, related_open_link_url)
  VALUES
  ('Attendance', null, null, null, null, null);

INSERT INTO plugin_todos_related_list
  (title, related_table_name, related_table_id_column, related_table_title_column, related_table_deleted_column, related_open_link_url)
  VALUES
  ('Payroll', null, null, null, null, null);

INSERT INTO plugin_todos_related_list
  (title, related_table_name, related_table_id_column, related_table_title_column, related_table_deleted_column, related_open_link_url)
  VALUES
  ('Building', 'plugin_courses_locations', 'id', 'name', 'delete', '/admin/courses/edit_location/?id=#IDI#');

INSERT INTO plugin_todos_related_list
  (title, related_table_name, related_table_id_column, related_table_title_column, related_table_deleted_column, related_open_link_url)
  VALUES
  ('Teacher', 'plugin_contacts3_contacts', 'id', "DB::expr(CONCAT_WS(' ', first_name, last_name))", 'delete', '/admin/contacts3/add_edit_contact/#ID#');

INSERT INTO plugin_todos_related_list
  (title, related_table_name, related_table_id_column, related_table_title_column, related_table_deleted_column, related_open_link_url)
  VALUES
  ('Task', null, null, null, null, null);

INSERT INTO plugin_todos_related_list
  (title, related_table_name, related_table_id_column, related_table_title_column, related_table_deleted_column, related_open_link_url)
  VALUES
  ('Safety', null, null, null, null, null);

INSERT INTO plugin_todos_related_list
  (title, related_table_name, related_table_id_column, related_table_title_column, related_table_deleted_column, related_open_link_url)
  VALUES
  ('Ennis', null, null, null, null, null);
