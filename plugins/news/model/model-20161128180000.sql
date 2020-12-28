/*
ts:2016-11-28 18:00:00
*/

CREATE TABLE `plugin_news_shared`
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  news_id INT,
  role_id INT,
  deleted TINYINT NOT NULL DEFAULT 0,

  KEY (news_id),
  KEY (role_id)
)
ENGINE = INNODB
CHARSET = UTF8;
