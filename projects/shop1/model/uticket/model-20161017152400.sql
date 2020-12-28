/*
ts:2016-10-17 15:24:00
*/

UPDATE IGNORE `engine_plugins` SET `show_on_dashboard` = 0 WHERE `name` = 'events/orders';
UPDATE IGNORE `engine_plugins` SET `show_on_dashboard` = 0 WHERE `name` = 'events/invoices';