/*
ts:2016-10-05 08:08:00
*/

update plugin_currency_currencies set deleted = 1 where currency = 'USD';
update plugin_currency_currencies set `name` = 'Sterling' where currency = 'GBP';
