/*
ts:2016-03-21 09:16:00
*/

CREATE TABLE plugin_messaging_notification_categories
(
  id INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL
)
ENGINE = INNODB
CHARSET = UTF8;

INSERT INTO plugin_messaging_notification_categories (`name`) VALUES ('Website');
INSERT INTO plugin_messaging_notification_categories (`name`) VALUES ('Accounts');
INSERT INTO plugin_messaging_notification_categories (`name`) VALUES ('News');

ALTER TABLE plugin_messaging_notification_templates ADD COLUMN category_id INT;
