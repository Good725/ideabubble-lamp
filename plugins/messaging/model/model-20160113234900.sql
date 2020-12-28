/*
ts:2016-01-13 23:49:00
*/

CREATE TABLE `plugin_messaging_notification_template_attachments`
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  template_id INT NOT NULL,
  name VARCHAR(255),
  path VARCHAR(255),
  file_id INT,
  content MEDIUMBLOB,
  type VARCHAR(100),
  content_encoding VARCHAR(100),

  KEY (template_id)
)
ENGINE = InnoDB
CHARSET = UTF8;
