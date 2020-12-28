/*
ts:2017-01-02 10:20:00
*/

UPDATE `plugin_reports_reports` SET `sql`='SELECT \r\n        dt, ip, referer, url, host, details \r\n    FROM engine_errorlog\r\n    WHERE `type`=\'HTTP\'\r\n    ORDER BY `dt` DESC', `widget_sql`='SELECT \r\n        dt AS `Date`, referer, url, details \r\n    FROM engine_errorlog\r\n    WHERE `type`=\'HTTP\'\r\n    ORDER BY `dt` DESC' WHERE (`name`='HTTP Errors');

