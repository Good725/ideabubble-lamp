/*
ts:2016-08-17 13:54:00
*/

INSERT IGNORE INTO `engine_plugins` (`name`, `friendly_name`, `show_on_dashboard`, `requires_media`) VALUES ('transactions', 'Transactions', 0, 0);

CREATE TABLE plugin_transactions_types
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  transaction_type  VARCHAR(100) NOT NULL,
  income  TINYINT(1) NOT NULL,
  exchange ENUM('Real', 'Virtual', 'Correction') NOT NULL,
  deleted TINYINT(1) NOT NULL DEFAULT 0
)
ENGINE = INNODB
CHARSET = UTF8;

INSERT INTO plugin_transactions_types
  (transaction_type, income, exchange)
  VALUES
  ('Business', 1, 'Real'),
  ('Credit', -1, 'Virtual'),
  ('Journal Cancel', -1, 'Correction'),
  ('Journal Add', 1, 'Correction'),
  ('Return', -1, 'Real');

CREATE TABLE plugin_transactions_transactions
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  type_id INT NOT NULL,
  journalled_transaction_id INT,
  currency VARCHAR(3) NOT NULL,
  amount  DECIMAL(10, 2) NOT NULL,
  discount  DECIMAL(10, 2) NOT NULL,
  total DECIMAL(10, 2) NOT NULL,
  due DATE NOT NULL,
  contact_id  INT,
  family_id INT,
  user_id INT,
  status  ENUM('Outstanding', 'Completed', 'Cancelled') NOT NULL,
  created DATETIME NOT NULL,
  updated DATETIME NOT NULL,
  created_by  INT,
  updated_by  INT,
  deleted TINYINT(1) NOT NULL DEFAULT 0,

  KEY (contact_id),
  KEY (family_id),
  KEY (user_id)
)
ENGINE = INNODB
CHARSET = UTF8;

CREATE TABLE plugin_transactions_transactions_outstanding
(
  transaction_id INT PRIMARY KEY NOT NULL,
  outstanding DECIMAL(10, 2) NOT NULL DEFAULT 0
)
ENGINE = INNODB
CHARSET = UTF8;

CREATE TABLE plugin_transactions_gateways
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  gateway VARCHAR(50) NOT NULL,
  deleted TINYINT(1) NOT NULL DEFAULT 0
)
ENGINE = INNODB
CHARSET = UTF8;

INSERT INTO plugin_transactions_gateways
  (gateway)
  VALUES
  ('Cash'), ('Cheque'), ('Bank Transfer'), ('Realex'), ('Paypal'), ('Stripe'), ('Sagepay'), ('BOIPA');

CREATE TABLE plugin_transactions_paymenttypes
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  payment_type  VARCHAR(100) NOT NULL,
  income  TINYINT(1) NOT NULL,
  exchange ENUM('Real', 'Transfer', 'Correction') NOT NULL,
  deleted TINYINT(1) NOT NULL DEFAULT 0
)
ENGINE = INNODB
CHARSET = UTF8;

INSERT INTO plugin_transactions_paymenttypes
  (payment_type, income, exchange)
  VALUES
  ('Payment', 1, 'Real'),
  ('Credit', 0, 'Transfer'),
  ('Refund', -1, 'Real'),
  ('Journal Cancel', -1, 'Correction'),
  ('Journal Add', 1, 'Correction');

CREATE TABLE plugin_transactions_payments
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  to_transaction_id  INT NOT NULL,
  from_transaction_id  INT,
  paymenttype_id  INT NOT NULL,
  journalled_payment_id INT,
  currency VARCHAR(3) NOT NULL DEFAULT 'EUR',
  currency_rate DECIMAL(10, 2) NOT NULL DEFAULT 1.0,
  amount  DECIMAL(10, 2) NOT NULL,
  gateway_id INT NOT NULL,
  gateway_tx_reference VARCHAR(100) NOT NULL DEFAULT '',
  gateway_fee DECIMAL(10, 2) NOT NULL DEFAULT 0,
  gateway_fee_included TINYINT(1) NOT NULL DEFAULT 0,
  status ENUM('Processing', 'Completed', 'Cancelled') NOT NULL,
  created DATETIME NOT NULL,
  updated DATETIME NOT NULL,
  created_by  INT,
  updated_by  INT,
  deleted TINYINT(1) NOT NULL DEFAULT 0,

  KEY (to_transaction_id),
  KEY (from_transaction_id)
)
ENGINE = INNODB
CHARSET = UTF8;

CREATE TABLE plugin_transactions_history
(
  id INT AUTO_INCREMENT PRIMARY KEY,
  transaction_id INT NOT NULL,
  saved DATETIME NOT NULL,
  data MEDIUMTEXT NOT NULL,

  KEY (transaction_id)
)
ENGINE=INNODB
CHARSET=UTF8;
