/*
ts:2020-05-18 14:49:00
*/

INSERT INTO `engine_plugins`
  (`name`, `friendly_name`, `icon`, `flaticon`, `svg`, `show_on_dashboard`)
  VALUES
  ('navapi', 'NAV API', 'bookings', 'receipt', 'bookings', 0);

INSERT INTO `engine_resources`
  (`type_id`, `alias`, `name`, `description`)
  VALUES
  ('0', 'navapi', 'Navision API', 'Navision API');

CREATE TABLE plugin_navapi_events
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  schedule_id INT,
  remote_event_no VARCHAR(20) NOT NULL,
  remote_event_title  VARCHAR(50),
  remote_venue  VARCHAR(100),
  remote_cost_centre  VARCHAR(20),
  remote_description  TEXT,
  remote_start_date DATETIME,
  remote_end_date DATETIME,
  remote_status VARCHAR(10),

  KEY (schedule_id)
)
ENGINE=INNODB
CHARSET=UTF8;
