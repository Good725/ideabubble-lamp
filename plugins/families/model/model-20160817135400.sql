/*
ts:2016-08-17 13:54:00
*/

INSERT IGNORE INTO `engine_plugins` (`name`, `friendly_name`, `show_on_dashboard`, `requires_media`) VALUES ('families', 'Families', 0, 0);

CREATE TABLE plugin_family_families
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  family  VARCHAR(50),
  primary_contact_id  INT,
  created DATETIME,
  updated DATETIME,
  created_by INT,
  updated_by INT,
  published TINYINT DEFAULT 1,
  deleted TINYINT DEFAULT 0,

  KEY (family)
)
ENGINE = INNODB
CHARSET = UTF8;

CREATE TABLE plugin_family_members
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  family_id INT,
  contact_id  INT,
  role  ENUM('Child', 'Parent', 'Mature'),

  KEY (family_id),
  KEY (contact_id)
)
ENGINE = INNODB
CHARSET = UTF8;
