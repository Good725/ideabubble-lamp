/*
ts:2016-12-14 10:33:00
*/

UPDATE plugin_pages_pages SET layout_id = (select id from plugin_pages_layouts where `layout` = 'ideabubble_layout3') WHERE `name_tag` LIKE '%error404%';
