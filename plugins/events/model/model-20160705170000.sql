/*
ts:2016-07-05 17:00:00
*/

INSERT IGNORE INTO `plugin_messaging_notification_templates`
(`name`, `driver`, `type_id`, `subject`, `sender`, `message`, `overwrite_cms_message`, `date_created`, `created_by`, `date_updated`, `publish`, `deleted`) VALUES
(
  'ticket-purchased-seller',
  'EMAIL',
  (SELECT `id` FROM `plugin_messaging_notification_types` WHERE `title` = 'email' LIMIT 1),
  'A ticket has been purchased',
  'testing@websitecms.ie',
  '<p>An order has been made by $email.</p>\n<p>Name: $firstname $lastname</p>\n<p>Email: $email</p>\n\n<p>\nAddress:<br />\n$address_1<br />\n$address_2<br />\n$city<br />\n$country\n</p>\n\n<p>Eircode: $eircode</p>\n\n<p>Order</p>\n$orders_table\n<br />\n\n<p>\nCommission: $currency$commission_amount<br />\nFixed commission: $currency$commission_fixed_charge_amount<br />\nVat: $currency$vat_total<br />\nDiscount: $currency$discount<br />\n<strong>Total</strong>: $currency$total\n</p>',
  '0',
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  CURRENT_TIMESTAMP,
  '1',
  '0'
);

DELETE FROM plugin_messaging_notification_template_targets
  WHERE x_details = 'bcc' AND template_id in (SELECT `id` FROM `plugin_messaging_notification_templates` WHERE `name` = 'ticket-purchased-seller');

INSERT IGNORE INTO `plugin_messaging_notification_templates`
(`name`, `driver`, `type_id`, `sender`, `date_created`, `created_by`, `date_updated`, `publish`, `deleted`) VALUES
(
  'email-event-attendees',
  'EMAIL',
  (SELECT `id` FROM `plugin_messaging_notification_types` WHERE `title` = 'email' LIMIT 1),
  'testing@websitecms.ie',
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  CURRENT_TIMESTAMP,
  '1',
  '0'
);
