/*
ts:2016-08-12 10:10:00
*/

INSERT INTO `plugin_messaging_notification_templates`
(`name`, `driver`, `type_id`, `subject`, `sender`, `message`, `overwrite_cms_message`, `date_created`, `created_by`, `date_updated`, `publish`, `deleted`) VALUES
(
  'ticket-purchased-buyer',
  'EMAIL',
  (SELECT `id` FROM `plugin_messaging_notification_types` WHERE `title` = 'email' LIMIT 1),
  'Ticket Order Confirmation',
  'testing@websitecms.ie',
  '<p>Hello $firstname $lastname</p>
\n\n<p>Thank you for making an order though our website.</p>
\n\n<h2>Your details</h2>
\n<p>Name: $firstname $lastname</p>
\n<p>Email: $email</p>
\n\n<p>\nAddress:<br />\n$address_1<br />\n$address_2<br />\n$city<br />\n$country\n</p>
\n\n<p>Eircode: $eircode</p>
\n\n<h2>Order</h2>
\n$orders_table\n<br />
\n\n<p>\nCommission: $currency$commission_amount<br />
\nFixed commission: $currency$commission_fixed_charge_amount<br />
\nVat: $currency$vat_total<br />
\nDiscount: $currency$discount<br />
\n<strong>Total</strong>: $currency$total
\n</p>',
  '0',
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  CURRENT_TIMESTAMP,
  '1',
  '0'
);
