/*
ts:2016-01-13 21:38:00
*/

CREATE TABLE `plugin_messaging_message_attachments`
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  message_id INT NOT NULL,
  name VARCHAR(255) NOT NULL,
  content MEDIUMBLOB,
  type VARCHAR(100) NOT NULL DEFAULT 'application/octet-stream',
  content_encoding VARCHAR(100) NOT NULL DEFAULT 'base64',

  KEY (message_id)
)
ENGINE = InnoDB
CHARSET = UTF8;

-- ROW_FORMAT=COMPRESSED is supported by barracuda, file_per_table
-- other engines wont support it so this query can be ignored safely
ALTER IGNORE TABLE `plugin_messaging_message_attachments` ROW_FORMAT=COMPRESSED;
