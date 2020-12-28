/*
ts:2016-11-10 15:00:00
*/

UPDATE `plugin_messaging_notification_templates` SET `subject`='Order received on uTicket | Order No. $order_id | $date' WHERE `name`='ticket-purchased-seller';
UPDATE `plugin_messaging_notification_templates` SET `subject`='Your uTicket Order No. $order_id | $date'                WHERE `name`='ticket-purchased-buyer';
