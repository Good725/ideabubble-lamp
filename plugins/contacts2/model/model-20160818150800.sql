/*
ts:2016-08-18 15:08:00
*/

CREATE TABLE plugin_contacts_communication_types
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100),
  deleted TINYINT DEFAULT 0,

  UNIQUE KEY (`name`)
)
ENGINE = INNODB
CHARSET = UTF8;

INSERT INTO plugin_contacts_communication_types
  (`name`) VALUES ('Email'), ('Mobile'), ('Phone'), ('Web'), ('Skype'), ('Facebook'), ('Twitter');

CREATE TABLE plugin_contacts_communications
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  contact_id INT NOT NULL,
  type_id INT NOT NULL,
  `value` VARCHAR(255),
  deleted TINYINT DEFAULT 0,

  KEY (contact_id)
)
ENGINE = INNODB
CHARSET = UTF8;

CREATE TABLE plugin_contacts_preference_types
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100),
  `section` VARCHAR(100),
  deleted TINYINT DEFAULT 0,

  UNIQUE KEY (`name`)
)
ENGINE = INNODB
CHARSET = UTF8;

INSERT INTO plugin_contacts_preference_types
  (`name`, `section`)
  VALUES
  ('Text Messaging', 'communication'),
  ('Phone Call', 'communication'),
  ('Post', 'communication'),
  ('Photo/Video Permission', 'privacy');

CREATE TABLE plugin_contacts_preferences
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  contact_id INT NOT NULL,
  type_id INT NOT NULL,
  `value` VARCHAR(255),
  deleted TINYINT DEFAULT 0,

  KEY (contact_id)
)
ENGINE = INNODB
CHARSET = UTF8;

INSERT INTO plugin_contacts_communications
  (contact_id, type_id, `value`)
  (select id, 1, email FROM plugin_contacts_contact where email != '');

INSERT INTO plugin_contacts_communications
  (contact_id, type_id, `value`)
  (select id, 2, mobile FROM plugin_contacts_contact where mobile != '');

INSERT INTO plugin_contacts_communications
  (contact_id, type_id, `value`)
  (select id, 3, phone FROM plugin_contacts_contact where phone != '');

