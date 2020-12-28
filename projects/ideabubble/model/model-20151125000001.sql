/*
ts:2015-11-25 00:00:01
*/

ALTER TABLE plugin_pages_layouts ADD UNIQUE KEY (`layout`);
INSERT IGNORE INTO plugin_pages_layouts (`layout`) VALUES ('ideabubble_layout1');
INSERT IGNORE INTO plugin_pages_layouts (`layout`) VALUES ('ideabubble_layout2');
INSERT IGNORE INTO plugin_pages_layouts (`layout`) VALUES ('ideabubble_layout3');
INSERT IGNORE INTO plugin_pages_layouts (`layout`) VALUES ('ideabubble_layout4');
INSERT IGNORE INTO plugin_pages_layouts (`layout`) VALUES ('ideabubble_home');

UPDATE plugin_pages_pages SET layout_id = (select id from plugin_pages_layouts where `layout` = 'ideabubble_home') WHERE name_tag = 'home.html';
