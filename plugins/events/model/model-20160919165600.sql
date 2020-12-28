/*
ts:2016-09-19 16:56:00
*/

UPDATE plugin_messaging_notification_templates
  SET `subject` = 'Order Received'
  WHERE `name` = 'ticket-purchased-seller';

UPDATE plugin_messaging_notification_templates
  SET `subject` = 'Order Confirmed'
  WHERE `name` = 'ticket-purchased-buyer';


