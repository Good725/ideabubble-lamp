/*
ts:2017-03-26 15:49:00
*/

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'contacts3_frontend_bookings', 'KES Contacts / Frontend Bookings', 'KES Contacts / Frontend Bookings', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'contacts3'));

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'contacts3_frontend_accounts', 'KES Contacts / Frontend Accounts', 'KES Contacts / Frontend Accounts', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'contacts3'));

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'contacts3_frontend_homeworks', 'KES Contacts / Frontend Homeworks', 'KES Contacts / Frontend Homeworks', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'contacts3'));

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'contacts3_frontend_timesheets', 'KES Contacts / Frontend Timesheets', 'KES Contacts / Frontend Timesheets', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'contacts3'));

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'contacts3_frontend_attendance', 'KES Contacts / Frontend Attendance', 'KES Contacts / Frontend Attendance', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'contacts3'));

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'contacts3_frontend_timetables', 'KES Contacts / Frontend Timetables', 'KES Contacts / Frontend Timetables', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'contacts3'));

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`, parent_controller)
  VALUES
  (1, 'contacts3_frontend_wishlist', 'KES Contacts / Frontend Wishlist', 'KES Contacts / Frontend Wishlist', (SELECT id FROM `engine_resources` o WHERE o.`alias` = 'contacts3'));
