/*
ts:2019-05-10 17:00:00
*/

CREATE TABLE plugin_messaging_roles_can_send
(
  role_id INT,
  can_send_to_role_id INT,
  driver VARCHAR(100),

  PRIMARY KEY (role_id, can_send_to_role_id)
)
ENGINE = INNODB
CHARSET = UTF8;


INSERT IGNORE INTO plugin_messaging_roles_can_send
  (role_id, can_send_to_role_id, driver)
  (select sender.id, send_to.id, 'dashboard' from engine_project_role sender, engine_project_role send_to where sender.role in ('Administrator', 'Teacher', 'Manager'));

INSERT IGNORE INTO plugin_messaging_roles_can_send
  (role_id, can_send_to_role_id, driver)
  (select sender.id, send_to.id, 'dashboard' from engine_project_role sender, engine_project_role send_to where sender.role in ('Student', 'Parent/Guardian', 'Mature Student') AND send_to.role in ('Teacher'));
