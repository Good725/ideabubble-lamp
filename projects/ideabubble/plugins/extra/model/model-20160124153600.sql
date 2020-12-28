/*
ts:2016-01-24 15:36:00
*/

UPDATE plugin_reports_reports SET `bulk_message_body` = '<p>Dear<br />\n{Company}<br />\n<br />\n, your domain<br />\n{Url}<br />\n<br />\nis due to expire on the<br />\n{Expired}\n</p>\n<br />\n<p>\nThe amount required to renew your service is :<br />\n{Price}<br />\n<br />\ninc VAT.<br />\nPlease pay by visiting the pay now link here : https://www.ideabubble.ie/pay-online.html\n</p>' WHERE `name` = 'Extra Services Payment Due';
UPDATE plugin_reports_reports SET `bulk_message_body` = '<p>Dear<br />\n{Company}<br />\n<br />\n, your domain<br />\n{Url}<br />\n<br />\nis due to expire on the<br />\n{Ended}\n</p>\n<br />\n<p>\nThe amount required to renew your service is :<br />\n{Price}<br />\n<br />\ninc VAT.<br />\nPlease pay by visiting the pay now link here : https://www.ideabubble.ie/pay-online.html\n</p>' WHERE `name` = 'Extra Services Expired';
