/*
ts:2016-06-14 13:02:00
*/

ALTER TABLE plugin_events_orders ADD COLUMN commission_fixed_charge_amount DECIMAL(10, 2);
ALTER TABLE plugin_events_orders ADD COLUMN commission_total DECIMAL(10, 2);
ALTER TABLE plugin_events_orders ADD COLUMN vat_total DECIMAL(10, 2);
ALTER TABLE plugin_events_orders ADD COLUMN vat_rate DECIMAL(10, 2);

ALTER TABLE plugin_events_orders_items ADD COLUMN vat DECIMAL(10, 2);
ALTER TABLE plugin_events_orders_items ADD COLUMN commission DECIMAL(10, 2);
ALTER TABLE plugin_events_orders_items ADD COLUMN discount_type ENUM('Fixed', 'Percent');
ALTER TABLE plugin_events_orders_items ADD COLUMN discount_amount DECIMAL(10, 2);
ALTER TABLE plugin_events_orders_items ADD COLUMN discount DECIMAL(10, 2);
ALTER TABLE plugin_events_orders_items ADD COLUMN total DECIMAL(10, 2);

ALTER TABLE plugin_events_events_ticket_types_sold ADD COLUMN event_date_id INT;
ALTER TABLE plugin_events_events_ticket_types_sold DROP PRIMARY KEY, ADD PRIMARY KEY (`ticket_type_id`, `event_date_id`);

ALTER TABLE plugin_events_events_sold ADD COLUMN event_date_id INT;
ALTER TABLE plugin_events_events_sold DROP PRIMARY KEY, ADD PRIMARY KEY (`event_id`, `event_date_id`);

CREATE TABLE plugin_events_seller_invoices
(
  id  INT AUTO_INCREMENT PRIMARY KEY,
  event_id  INT,
  file_id INT,
  amount  DECIMAL(10, 2),
  currency VARCHAR(3),
  due DATE,
  completed DATETIME,
  created DATETIME,
  deleted TINYINT DEFAULT 0,

  KEY (event_id)
)
  ENGINE=INNODB
  CHARSET=UTF8;

INSERT INTO `plugin_messaging_notification_templates`
  SET `send_interval` = null, `name` = 'event-invoice', `description` = '', `driver` = 'EMAIL', `type_id` = '5', `subject` = 'Event Invoice', `sender` = '', `message` = 'Invoice details have been attached', `overwrite_cms_message` = '0', `page_id` = '0', `header` = '', `footer` = '', `schedule` = null, `date_created` = NOW(), `date_updated` = NOW(), `last_sent` = null, `publish` = '1', `deleted` = '0', `create_via_code` = 1, `usable_parameters_in_template` = '', `doc_generate` = null, `doc_helper` = null, `doc_template_path` = null, `doc_type` = null, `category_id` = '0';

INSERT INTO `engine_settings`
(`linked_plugin_name`, `variable`, `name`, `value_live`, `value_stage`, `value_test`, `value_dev`, `default`, `location`, `note`, `type`, `group`, `required`, `options`)
VALUES
  ('events', 'events_invoice_email', 'Invoice To Email', '', 'testing@ideabubble.ie', 'testing@ideabubble.ie',  'testing@ideabubble.ie',  'testing@ideabubble.ie',  'both', '', 'text', 'Events', 0, '');

SELECT id INTO @events_resource_id FROM `engine_resources` o WHERE o.`alias` = 'events'/*0615*/;
  INSERT INTO `engine_resources` (`type_id`, `alias`, `name`, `description`, parent_controller) VALUES (1, 'events_invoice_update', 'Events / Invoices Update', 'Events Invoice Update', @events_resource_id);
