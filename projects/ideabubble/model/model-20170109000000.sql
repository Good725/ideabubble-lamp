/*
ts:2017-01-09 00:00:00
*/

UPDATE plugin_pages_pages SET layout_id = (select id from plugin_pages_layouts where `layout` = 'ideabubble_home') WHERE name_tag = 'home.html' /*1*/;
ALTER TABLE plugin_pages_layouts ADD UNIQUE KEY (`layout`);
