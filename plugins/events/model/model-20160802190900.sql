/*
ts:2016-08-02 19:09:00
*/

UPDATE plugin_messaging_notification_templates
  SET `message` = '<p>An order has been made by $email.</p>\n<p>Name: $firstname $lastname</p>\n<p>Email: $email</p>\n\n<p>\nAddress:<br />\n$address_1<br />\n$address_2<br />\n$city<br />\n$country\n</p>\n\n<p>Eircode: $eircode</p>\n\n<p>Order</p>\n$orders_table\n<br />\n\n<p>\nCommission: $currency$commission_amount<br />\nFixed commission: $currency$commission_fixed_charge_amount<br />\nVat: $currency$vat_total<br />\nDiscount: $currency$discount<br />\n<strong>Total</strong>: $currency$total\n</p><p>Note: $note</p>',
    `date_updated` = NOW()
  WHERE `name` = 'ticket-purchased-seller';
