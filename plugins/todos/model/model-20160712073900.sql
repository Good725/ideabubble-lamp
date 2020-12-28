/*
ts:2016-07-12 07:39:00
*/

CREATE TABLE plugin_todos_to_users
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  todo_id INT NOT NULL,
  to_user_id INT NOT NULL,

  KEY (todo_id)
)
ENGINE = INNODB
CHARSET = UTF8;

INSERT INTO plugin_todos_to_users (todo_id, to_user_id) (SELECT todo_id, to_user_id FROM plugin_todos WHERE deleted = 0);

ALTER TABLE plugin_todos DROP COLUMN to_user_id;

CREATE TABLE plugin_todos_related_list
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(100),
  related_table_name VARCHAR(100),
  related_table_id_column VARCHAR(100),
  related_table_title_column VARCHAR(100),
  related_table_deleted_column VARCHAR(100),
  related_open_link_url VARCHAR(100),
  deleted TINYINT NOT NULL DEFAULT 0
)
ENGINE = INNODB
CHARSET = UTF8;

ALTER TABLE `plugin_todos` CHANGE COLUMN `related_to_plugin` `related_to` VARCHAR(45);

