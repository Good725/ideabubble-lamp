/*
ts:2017-04-03 11:00:00
*/

INSERT INTO `plugin_pages_layouts` (`layout`, `source`, `use_db_source`, `publish`, `deleted`, `date_created`, `date_modified`, `created_by`, `modified_by`) values('checkout_summary',NULL,'0','1','0','2017-04-03 11:00:41',NULL,NULL,NULL);

SELECT LAST_INSERT_ID() INTO @checkout_summary_20170403110000_001;

insert into `plugin_pages_pages` (`name_tag`, `title`, `content`, `banner_photo`, `seo_keywords`, `seo_description`, `footer`, `date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`, `parent_id`, `theme`, `force_ssl`, `nocache`, `x_robots_tag`, `data_helper_call`)
  values('checkout-summary','Checkout Summary',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','0','1',@checkout_summary_20170403110000_001,'1','0',NULL,'0','1',NULL,NULL);
