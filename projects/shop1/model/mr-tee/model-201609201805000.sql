/*
ts:2016-09-20 18:05:00
*/
UPDATE `plugin_products_postage_zone` SET publish = 0 where title <> 'Worldwide (Standard Post)';
UPDATE `plugin_products_postage_zone` SET publish = 1 where title = 'Worldwide (Standard Post)';