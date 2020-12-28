/*
ts:2015-12-14 20:31:00
*/

ALTER TABLE `plugin_pages_pages` ADD COLUMN `force_ssl` BIT(1) DEFAULT 0;
ALTER TABLE `plugin_pages_pages` ADD COLUMN `nocache` BIT(1) DEFAULT 0;
CREATE OR REPLACE SQL SECURITY INVOKER VIEW `view_ppages` AS SELECT
plugin_pages_pages.id AS id,
plugin_pages_pages.name_tag AS name_tag,
plugin_pages_pages.title AS title,
plugin_pages_pages.content AS content,
plugin_pages_pages.banner_photo AS banner_photo,
plugin_pages_pages.category_id AS category_id,
plugin_pages_pages.layout_id AS layout_id,
plugin_pages_pages.theme AS theme,
plugin_pages_pages.seo_keywords AS seo_keywords,
plugin_pages_pages.seo_description AS seo_description,
plugin_pages_pages.date_entered AS date_entered,
plugin_pages_pages.footer AS footer,
plugin_pages_pages.last_modified AS last_modified,
plugin_pages_pages.modified_by AS modified_by,
plugin_pages_pages.created_by AS created_by,
plugin_pages_pages.publish AS publish,
plugin_pages_pages.deleted AS deleted,
plugin_pages_categorys.category AS category,
plugin_pages_layouts.layout AS layout,
plugin_pages_pages.include_sitemap AS include_sitemap,
plugin_pages_pages.force_ssl,
plugin_pages_pages.nocache
from ((`plugin_pages_pages` left join `plugin_pages_layouts` on((`plugin_pages_pages`.`layout_id` = `plugin_pages_layouts`.`id`))) left join `plugin_pages_categorys` on((`plugin_pages_pages`.`category_id` = `plugin_pages_categorys`.`id`)));
