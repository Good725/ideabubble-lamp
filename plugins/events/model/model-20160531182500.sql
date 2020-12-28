/*
ts:2016-05-31 18:25:00
*/

ALTER TABLE plugin_events_orders_items DROP COLUMN event_date_id;

CREATE TABLE plugin_events_orders_items_has_dates
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  order_item_id INT,
  date_id INT,

  KEY (order_item_id)
)
ENGINE = INNODB
CHARSET = UTF8;
