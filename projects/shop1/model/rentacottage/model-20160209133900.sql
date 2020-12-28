/*
ts:2016-02-09 13:39:00
*/

SELECT id INTO @content_id_20160209133900 FROM plugin_pages_layouts WHERE `layout` = 'content';
insert into `plugin_pages_pages` set
  `name_tag` = 'thank-you-for-booking.html',
  `title` = 'thank-you-for-booking',
  `content` = '<p><strong>Thank you for booking.</strong></p>\n',
  `banner_photo` = '',
  `seo_keywords` = '',
  `seo_description` = '',
  `footer` = '',
  `date_entered` = NOW(),
  `last_modified` = NOW(),
  `created_by` = 1,
  `modified_by` = '15',
  `publish` = '1',
  `deleted` = '0',
  `include_sitemap` = '0',
  `layout_id` = @content_id_20160209133900,
  `category_id` = '1',
  `theme` = '',
  `force_ssl` = 0,
  `nocache` = 1;
