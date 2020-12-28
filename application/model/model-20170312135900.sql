/*
ts:2017-03-12 13:59:00
*/

CREATE TABLE engine_eprinters
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  location  VARCHAR(100),
  tray  VARCHAR(100),
  email VARCHAR(100),
  published TINYINT(1) NOT NULL DEFAULT 1,
  deleted TINYINT(1) NOT NULL DEFAULT 0
)
ENGINE = INNODB
CHARSET = UTF8;

ALTER TABLE plugin_reports_reports MODIFY COLUMN generate_documents_tray VARCHAR(255);

DELETE FROM engine_settings WHERE variable IN ('print_tray1_email', 'print_tray2_email', 'print_tray1_paper_type', 'print_tray2_paper_type');
