/*
ts:2016-08-25 09:41:00
*/

CREATE TABLE engine_notes_types
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  `type` VARCHAR(100),
  `referenced_table` VARCHAR(100) NOT NULL,
  `referenced_table_id` VARCHAR(100) NOT NULL,
  `referenced_table_deleted` VARCHAR(100) NOT NULL DEFAULT 'deleted',
  `deleted` TINYINT NOT NULL DEFAULT 0,

  KEY (`type`)
)
ENGINE = INNODB
CHARSET = UTF8;

CREATE TABLE engine_notes_notes
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  type_id INT NOT NULL,
  reference_id  INT NOT NULL,
  note  TEXT,
  created DATETIME NOT NULL,
  created_by INT,
  deleted TINYINT NOT NULL DEFAULT 0,

  KEY (type_id, reference_id)
)
ENGINE=INNODB
CHARSET=UTF8;
