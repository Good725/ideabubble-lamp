/*
ts:2020-02-18 12:31:00
*/

CREATE TABLE engine_ipwatcher_ignore_actions
(
  id INT AUTO_INCREMENT PRIMARY KEY,
  action VARCHAR(200),

  UNIQUE KEY (action)
)
ENGINE=INNODB
CHARSET=UTF8;

INSERT INTO engine_ipwatcher_ignore_actions (action) VALUES ('/admin/chat/get_data');
INSERT INTO engine_ipwatcher_ignore_actions (action) VALUES ('/admin/api/poll');
INSERT INTO engine_ipwatcher_ignore_actions (action) VALUES ('/admin/api/message_list');
INSERT INTO engine_ipwatcher_ignore_actions (action) VALUES ('/admin/api/room_list');
INSERT INTO engine_ipwatcher_ignore_actions (action) VALUES ('/admin/media/ajax_get_fonts');
INSERT INTO engine_ipwatcher_ignore_actions (action) VALUES ('/admin/messaging/check_notifications');
INSERT INTO engine_ipwatcher_ignore_actions (action) VALUES ('/admin/profile/keyboardshortcuts_load');
INSERT INTO engine_ipwatcher_ignore_actions (action) VALUES ('/admin/media/fonts');

