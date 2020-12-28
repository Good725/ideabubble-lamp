/*
ts:2016-04-06 13:59:00
*/

INSERT INTO `plugin_pages_layouts` SET `layout` = 'deals', `source` = ' ', `use_db_source` = 0, `publish` = 1, `deleted` = 0 /* 003*/;
	SELECT last_insert_id() INTO @refid_plugin_pages_layouts_20160406155912_003;

INSERT INTO `plugin_pages_pages`
  SET
    `name_tag` = 'deals',
    `title` = 'deals',
    `content` = '',
    `banner_photo` = null,
    `seo_keywords` = '',
    `seo_description` = '',
    `footer` = '',
    `date_entered` = NOW(),
    `last_modified` = NOW(),
    `publish` = 1,
    `deleted` = 0,
    `include_sitemap` = '1',
    `layout_id` = @refid_plugin_pages_layouts_20160406155912_003,
    `category_id` = '1', `parent_id` = '0',
    `theme` = null,
    `force_ssl` = 0,
    `nocache` = 1;

