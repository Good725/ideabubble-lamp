/*
ts:2018-04-29 18:35:00
*/

ALTER TABLE plugin_courses_courses ADD COLUMN is_fulltime ENUM('NO', 'YES') NOT NULL DEFAULT 'NO';
ALTER TABLE plugin_courses_courses ADD COLUMN fulltime_price DECIMAL(10, 2);
CREATE TABLE plugin_courses_courses_has_paymentoptions
(
  id  INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  course_id INT NOT NULL,
  deposit DECIMAL(10, 2),
  months  TINYINT,
  interest_rate DECIMAL(10, 2),
  published TINYINT NOT NULL DEFAULT 1,
  deleted TINYINT NOT NULL DEFAULT 0,

  KEY (course_id)
)
ENGINE=INNODB
CHARSET=UTF8;

CREATE TABLE plugin_bookings_transactions_has_courses
(
  id INT AUTO_INCREMENT PRIMARY KEY,
  transaction_id INT NOT NULL,
  course_id INT NOT NULL,
  deleted TINYINT NOT NULL DEFAULT 0,

  KEY (transaction_id),
  KEY (course_id)
)
ENGINE=INNODB
CHARSET=UTF8;

CREATE TABLE plugin_courses_schedules_has_paymentoptions
(
  id  INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
  schedule_id INT NOT NULL,
  deposit DECIMAL(10, 2),
  months  TINYINT,
  interest_rate DECIMAL(10, 2),
  published TINYINT NOT NULL DEFAULT 1,
  deleted TINYINT NOT NULL DEFAULT 0,

  KEY (schedule_id)
)
ENGINE=INNODB
CHARSET=UTF8;
