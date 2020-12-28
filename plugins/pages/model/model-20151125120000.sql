/*
ts:2015-11-25 12:00:00
engine.plugin.pages:20151125120000
*/

-- IBCMS-791
CREATE OR REPLACE VIEW `view_ppages` AS
    SELECT
        `plugin_pages_pages`.`id` AS `id`,
        `plugin_pages_pages`.`name_tag` AS `name_tag`,
        `plugin_pages_pages`.`title` AS `title`,
        `plugin_pages_pages`.`content` AS `content`,
        `plugin_pages_pages`.`banner_photo` AS `banner_photo`,
        `plugin_pages_pages`.`category_id` AS `category_id`,
        `plugin_pages_pages`.`layout_id` AS `layout_id`,
        `plugin_pages_pages`.`theme` AS `theme`,
        `plugin_pages_pages`.`seo_keywords` AS `seo_keywords`,
        `plugin_pages_pages`.`seo_description` AS `seo_description`,
        `plugin_pages_pages`.`date_entered` AS `date_entered`,
        `plugin_pages_pages`.`footer` AS `footer`,
        `plugin_pages_pages`.`last_modified` AS `last_modified`,
        `plugin_pages_pages`.`modified_by` AS `modified_by`,
        `plugin_pages_pages`.`created_by` AS `created_by`,
        `plugin_pages_pages`.`publish` AS `publish`,
        `plugin_pages_pages`.`deleted` AS `deleted`,
        `plugin_pages_categorys`.`category` AS `category`,
        `plugin_pages_layouts`.`layout` AS `layout`,
        `plugin_pages_pages`.`include_sitemap` AS `include_sitemap`
    FROM
        ((`plugin_pages_pages`
        LEFT JOIN `plugin_pages_layouts` ON ((`plugin_pages_pages`.`layout_id` = `plugin_pages_layouts`.`id`)))
        LEFT JOIN `plugin_pages_categorys` ON ((`plugin_pages_pages`.`category_id` = `plugin_pages_categorys`.`id`)));

UPDATE IGNORE `plugin_pages_pages`
SET `layout_id` = CASE
    WHEN ((SELECT count(*) FROM `plugin_pages_layouts` WHERE `layout` = 'products') >= 1)
    THEN (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'products')
    ELSE `layout_id`
	END
WHERE `title` IN ('products', 'checkout');
