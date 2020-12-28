/*
ts:2015-11-25 00:00:00
engine.plugin.pages:20151125000000
*/

-- IBCMS-729
ALTER TABLE `ppages` RENAME TO `plugin_pages_pages`;
ALTER TABLE `ppages_categorys` RENAME TO `plugin_pages_categorys`;
ALTER TABLE `ppages_layouts` RENAME TO `plugin_pages_layouts`;
CREATE OR REPLACE VIEW `view_ppages` AS SELECT
	plugin_pages_pages.id,
	plugin_pages_pages.name_tag,
	plugin_pages_pages.title,
	plugin_pages_pages.content,
	plugin_pages_pages.banner_photo,
	plugin_pages_pages.category_id,
	plugin_pages_pages.layout_id,
	plugin_pages_pages.seo_keywords,
	plugin_pages_pages.seo_description,
	plugin_pages_pages.date_entered,
	plugin_pages_pages.footer,
	plugin_pages_pages.last_modified,
	plugin_pages_pages.modified_by AS modified_by,
	plugin_pages_pages.created_by,
	plugin_pages_pages.publish,
	plugin_pages_pages.deleted,
	plugin_pages_categorys.category,
	plugin_pages_layouts.layout,
	plugin_pages_pages.include_sitemap
FROM
	plugin_pages_pages LEFT
OUTER JOIN plugin_pages_layouts ON plugin_pages_pages.layout_id = plugin_pages_layouts.id LEFT
OUTER JOIN plugin_pages_categorys ON plugin_pages_pages.category_id = plugin_pages_categorys.id;
