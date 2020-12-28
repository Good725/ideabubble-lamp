/*
ts:2016-10-09 15:58:00
*/

INSERT IGNORE INTO `engine_plugins` (`name`, `friendly_name`, `show_on_dashboard`, `requires_media`) VALUES ('donations', 'Donations', '1', '0');

CREATE TABLE plugin_donations_products
(
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  `value` DECIMAL(10, 2) NOT NULL,
  deleted TINYINT NOT NULL DEFAULT 0
)
ENGINE=INNODB
CHARSET=UTF8;

CREATE TABLE plugin_donations_donations
(
  id INT AUTO_INCREMENT PRIMARY KEY,
  contact_id  INT NOT NULL,
  message_id  INT NOT NULL,
  product_id  INT,
  status  ENUM('Processing', 'Confirmed', 'Completed', 'Rejected'),
  note  TEXT,
  created DATETIME,
  updated DATETIME,
  created_by INT,
  updated_by INT,
  deleted TINYINT NOT NULL DEFAULT 0,

  KEY (contact_id),
  KEY (product_id)
)
ENGINE=INNODB
CHARSET=UTF8;
