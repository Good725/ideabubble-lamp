/*
ts:2019-05-22 07:43:00
*/

ALTER TABLE plugin_contacts3_contacts ADD COLUMN occupation VARCHAR(100);

CREATE TABLE plugin_contacts3_hosts
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  contact_id  INT NOT NULL,
  pets  TEXT,
  facilities_description  TEXT,
  student_profile SET('All Ages', 'Under 18', 'Group Leaders', 'Smokers', 'Male', 'Female', 'Vegetarian'),
  availability  ENUM('All Year', 'Summer', 'Winter'),
  facilities  SET('WI-FI', 'Computer', 'Breakfast Lunch and Dinner'),
  rules TEXT,
  other TEXT,
  status  ENUM('Pending', 'Approved', 'Declined'),
  published TINYINT NOT NULL DEFAULT 1,
  deleted TINYINT NOT NULL DEFAULT 0,
  created DATETIME,
  created_by  INT,
  updated DATETIME,
  updated_by  INT,

  KEY (contact_id)
)
ENGINE=INNODB
CHARSET=UTF8;
