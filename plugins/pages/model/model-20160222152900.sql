/*
ts:2016-02-22 15:29:00
*/

UPDATE `plugin_reports_reports` set `sql` = 'SELECT name_tag, COUNT(*) AS `duplicates`, GROUP_CONCAT(plugin_pages_pages.id) AS `page_ids` FROM plugin_pages_pages GROUP BY name_tag HAVING duplicates > 1 ORDER BY name_tag' WHERE `name` = 'Duplicate Pages';

