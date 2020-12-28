/*
ts:2016-08-21 01:58:00
*/

INSERT INTO `engine_settings`
  (`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
  VALUES
  ('courses', 'courses_deposit_percent', 'Booking Deposit Percent', '25', '25', '25',  '25',  '25',  'both', '', 'text', 'Courses', 0, '');

ALTER TABLE plugin_courses_schedules ADD COLUMN booking_type ENUM('One Timeslot', 'Whole Schedule') DEFAULT 'One Timeslot';

ALTER TABLE `plugin_courses_bookings` RENAME `plugin_courses_bookings_migrate`;

CREATE TABLE plugin_courses_bookings
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  student_id  INT NOT NULL,
  payer_id  INT NOT NULL,
  currency VARCHAR(3),
  fee DECIMAL(10, 2),
  discount DECIMAL(10, 2),
  total DECIMAL(10, 2),
  status  ENUM('Enquiry', 'Confirmed', 'Cancelled', 'Processing') NOT NULL,
  created DATETIME NOT NULL,
  updated DATETIME NOT NULL,
  created_by  INT,
  updated_by  INT,
  deleted TINYINT(1) NOT NULL DEFAULT 0,

  KEY (student_id),
  KEY (payer_id)
)
ENGINE=INNODB
CHARSET=UTF8;

CREATE TABLE plugin_courses_bookings_has_schedules
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  booking_id  INT NOT NULL,
  schedule_id INT NOT NULL,
  currency VARCHAR(3),
  fee DECIMAL(10, 2),
  discount DECIMAL(10, 2),
  total DECIMAL(10, 2),
  created DATETIME NOT NULL,
  updated DATETIME NOT NULL,
  created_by  INT,
  updated_by  INT,
  deleted TINYINT(1) NOT NULL DEFAULT 0,
  status  ENUM('Enquiry', 'Confirmed', 'Cancelled', 'Processing') NOT NULL,

  KEY (booking_id),
  KEY (schedule_id)
)
ENGINE=INNODB
CHARSET=UTF8;

CREATE TABLE plugin_courses_bookings_has_schedules_has_timeslots
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  booking_has_schedule_id INT NOT NULL,
  timeslot_id INT NOT NULL,
  currency VARCHAR(3),
  fee DECIMAL(10, 2),
  discount DECIMAL(10, 2),
  total DECIMAL(10, 2),
  attend TINYINT(1) NOT NULL DEFAULT 0,
  created DATETIME NOT NULL,
  updated DATETIME NOT NULL,
  created_by  INT,
  updated_by  INT,
  deleted TINYINT(1) NOT NULL DEFAULT 0,

  KEY (booking_has_schedule_id),
  KEY (timeslot_id)
)
ENGINE=INNODB
CHARSET=UTF8;

CREATE TABLE plugin_courses_rollcall
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  timeslot_id INT NOT NULL,
  student_id  INT NOT NULL,
  status SET ('Absent', 'Present', 'Late', 'Early Departures', 'Unpaid', 'Paid'),
  created DATETIME,
  updated DATETIME,
  created_by  INT,
  updated_by  INT,
  deleted TINYINT(1) DEFAULT 0,

  KEY (timeslot_id),
  KEY (student_id)
)
ENGINE=INNODB
CHARSET=UTF8;

CREATE TABLE plugin_courses_bookings_has_transactions
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  booking_id  INT NOT NULL,
  booking_has_schedule_id INT,
  transaction_id  INT NOT NULL,
  created DATETIME,
  updated DATETIME,
  created_by  INT,
  updated_by  INT,
  deleted TINYINT(1) DEFAULT 0,

  KEY (booking_id),
  KEY (transaction_id)
)
ENGINE=INNODB
CHARSET=UTF8;

CREATE TABLE plugin_courses_bookings_history
(
  id INT AUTO_INCREMENT PRIMARY KEY,
  booking_id INT NOT NULL,
  saved DATETIME NOT NULL,
  data MEDIUMTEXT NOT NULL,

  KEY (booking_id)
)
ENGINE=INNODB
CHARSET=UTF8;
