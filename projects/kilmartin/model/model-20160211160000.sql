/*
ts:2016-02-11 16:00:00
*/

/*------------------------------------*\
    "Airport"-style screen
\*------------------------------------*/

/* Page layout */
INSERT IGNORE INTO `plugin_pages_layouts` (`layout`, `source`, `use_db_source`, `publish`, `deleted`, `date_created`, `date_modified`, `created_by`, `modified_by`)
SELECT  'tvad', '', '0', '1', '0', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, `user`.`id`, `user`.`id`
FROM `users` `user`
WHERE `email` = 'super@ideabubble.ie'
LIMIT 1;

/* Pages */
INSERT IGNORE INTO `plugin_pages_pages` (`name_tag`, `title`, `content`, `banner_photo`, `seo_keywords`, `seo_description`, `footer`, `date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`, `theme`, `force_ssl`, `nocache`)
SELECT 'rab.html', 'RAB', '', '', '', '', '', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, `user`.`id`, `user`.`id`, '1', '0', '1', `layout`.`id`, '1', '', '0', '1'
FROM `users` `user`
JOIN `plugin_pages_layouts` `layout` ON `layout`.`layout` = 'tvad'
JOIN `plugin_pages_categorys` `category` ON `category`.`category` = 'Default'
WHERE `user`.`email` = 'super@ideabubble.ie'
LIMIT 1;

INSERT IGNORE INTO `plugin_pages_pages` (`name_tag`, `title`, `content`, `banner_photo`, `seo_keywords`, `seo_description`, `footer`, `date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`, `theme`, `force_ssl`, `nocache`)
SELECT 'rab-limerick.html', 'Limerick RAB', '', '', '', '', '', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, `user`.`id`, `user`.`id`, '1', '0', '1', `layout`.`id`, '1', '', '0', '1'
FROM `users` `user`
JOIN `plugin_pages_layouts` `layout` ON `layout`.`layout` = 'tvad'
JOIN `plugin_pages_categorys` `category` ON `category`.`category` = 'Default'
WHERE `user`.`email` = 'super@ideabubble.ie'
LIMIT 1;

INSERT IGNORE INTO `plugin_pages_pages` (`name_tag`, `title`, `content`, `banner_photo`, `seo_keywords`, `seo_description`, `footer`, `date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`, `theme`, `force_ssl`, `nocache`)
SELECT 'rab-ennis.html', 'Ennis RAB', '', '', '', '', '', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, `user`.`id`, `user`.`id`, '1', '0', '1', `layout`.`id`, '1', '', '0', '1'
FROM `users` `user`
JOIN `plugin_pages_layouts` `layout` ON `layout`.`layout` = 'tvad'
JOIN `plugin_pages_categorys` `category` ON `category`.`category` = 'Default'
WHERE `user`.`email` = 'super@ideabubble.ie'
LIMIT 1;

/* News categories */
INSERT IGNORE INTO `plugin_news_categories` (`category`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `delete`)
SELECT 'Ticker', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, `id`, `id`, '1', '0'
FROM `users`
WHERE `email` = 'super@ideabubble.ie';

INSERT IGNORE INTO `plugin_news_categories` (`category`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `delete`)
SELECT 'Ticker - Limerick', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, `id`, `id`, '1', '0'
FROM `users`
WHERE `email` = 'super@ideabubble.ie';

INSERT IGNORE INTO `plugin_news_categories` (`category`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `delete`)
SELECT 'Ticker - Ennis', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, `id`, `id`, '1', '0'
FROM `users`
WHERE `email` = 'super@ideabubble.ie';

/* Dummy news data */
INSERT INTO `plugin_news`
(`category_id`, `title`, `image`, `title_text`, `alt_text`, `seo_title`, `seo_keywords`, `seo_description`, `seo_footer`, `order`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `summary`, `content`)
SELECT `category`.`id`, 'Ticker 1', '0', '', '', '', '', '', '', '0', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, `user`.`id`, `user`.`id`, '1', '0', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>\n\n<p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>\n\n<p>Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>\n'
FROM `plugin_news_categories` `category`
JOIN `users` `user` ON `user`.`email` = 'super@ideabubble.ie'
WHERE `category` = 'Ticker';

INSERT INTO `plugin_news`
(`category_id`, `title`, `image`, `title_text`, `alt_text`, `seo_title`, `seo_keywords`, `seo_description`, `seo_footer`, `order`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `summary`, `content`)
SELECT `category`.`id`, 'Ticker 2', '0', '', '', '', '', '', '', '0', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, `user`.`id`, `user`.`id`, '1', '0',
'Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.',
'<p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.</p><p>Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur.</p>'
FROM `plugin_news_categories` `category`
JOIN `users` `user` ON `user`.`email` = 'super@ideabubble.ie'
WHERE `category` = 'Ticker';

INSERT INTO `plugin_news`
(`category_id`, `title`, `image`, `title_text`, `alt_text`, `seo_title`, `seo_keywords`, `seo_description`, `seo_footer`, `order`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `summary`, `content`)
SELECT `category`.`id`, 'Ticker 3', '0', '', '', '', '', '', '', '0', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, `user`.`id`, `user`.`id`, '1', '0',
'Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam.',
'<p>Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur?</p><p>Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?</p>'
FROM `plugin_news_categories` `category`
JOIN `users` `user` ON `user`.`email` = 'super@ideabubble.ie'
WHERE `category` = 'Ticker';

INSERT INTO `plugin_news`
  (`category_id`, `title`, `image`, `title_text`, `alt_text`, `seo_title`, `seo_keywords`, `seo_description`, `seo_footer`, `order`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `summary`, `content`)
  SELECT `category`.`id`, 'Ticker 1', '0', '', '', '', '', '', '', '0', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, `user`.`id`, `user`.`id`, '1', '0', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>\n\n<p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>\n\n<p>Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>\n'
  FROM `plugin_news_categories` `category`
  JOIN `users` `user` ON `user`.`email` = 'super@ideabubble.ie'
  WHERE `category` = 'Ticker - Limerick';
INSERT INTO `plugin_news`
  (`category_id`, `title`, `image`, `title_text`, `alt_text`, `seo_title`, `seo_keywords`, `seo_description`, `seo_footer`, `order`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `summary`, `content`)
  SELECT `category`.`id`, 'Ticker 2', '0', '', '', '', '', '', '', '0', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, `user`.`id`, `user`.`id`, '1', '0',
  'Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.',
  '<p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.</p><p>Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur.</p>'
  FROM `plugin_news_categories` `category`
  JOIN `users` `user` ON `user`.`email` = 'super@ideabubble.ie'
  WHERE `category` = 'Ticker - Limerick';
INSERT INTO `plugin_news`
  (`category_id`, `title`, `image`, `title_text`, `alt_text`, `seo_title`, `seo_keywords`, `seo_description`, `seo_footer`, `order`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `summary`, `content`)
  SELECT `category`.`id`, 'Ticker 3', '0', '', '', '', '', '', '', '0', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, `user`.`id`, `user`.`id`, '1', '0',
  'Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam.',
  '<p>Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur?</p><p>Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?</p>'
  FROM `plugin_news_categories` `category`
  JOIN `users` `user` ON `user`.`email` = 'super@ideabubble.ie'
  WHERE `category` = 'Ticker - Limerick';

INSERT INTO `plugin_news`
  (`category_id`, `title`, `image`, `title_text`, `alt_text`, `seo_title`, `seo_keywords`, `seo_description`, `seo_footer`, `order`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `summary`, `content`)
  SELECT `category`.`id`, 'Ticker 1', '0', '', '', '', '', '', '', '0', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, `user`.`id`, `user`.`id`, '1', '0', 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.', '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.</p>\n\n<p>Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur.</p>\n\n<p>Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum</p>\n'
  FROM `plugin_news_categories` `category`
  JOIN `users` `user` ON `user`.`email` = 'super@ideabubble.ie'
  WHERE `category` = 'Ticker - Ennis';
INSERT INTO `plugin_news`
  (`category_id`, `title`, `image`, `title_text`, `alt_text`, `seo_title`, `seo_keywords`, `seo_description`, `seo_footer`, `order`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `summary`, `content`)
  SELECT `category`.`id`, 'Ticker 2', '0', '', '', '', '', '', '', '0', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, `user`.`id`, `user`.`id`, '1', '0',
  'Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.',
  '<p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae vitae dicta sunt explicabo.</p><p>Nemo enim ipsam voluptatem quia voluptas sit aspernatur aut odit aut fugit, sed quia consequuntur.</p>'
  FROM `plugin_news_categories` `category`
  JOIN `users` `user` ON `user`.`email` = 'super@ideabubble.ie'
  WHERE `category` = 'Ticker - Ennis';
INSERT INTO `plugin_news`
  (`category_id`, `title`, `image`, `title_text`, `alt_text`, `seo_title`, `seo_keywords`, `seo_description`, `seo_footer`, `order`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `summary`, `content`)
  SELECT `category`.`id`, 'Ticker 3', '0', '', '', '', '', '', '', '0', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP, `user`.`id`, `user`.`id`, '1', '0',
  'Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam.',
  '<p>Neque porro quisquam est, qui dolorem ipsum quia dolor sit amet, consectetur, adipisci velit, sed quia non numquam eius modi tempora incidunt ut labore et dolore magnam aliquam quaerat voluptatem. Ut enim ad minima veniam, quis nostrum exercitationem ullam corporis suscipit laboriosam, nisi ut aliquid ex ea commodi consequatur?</p><p>Quis autem vel eum iure reprehenderit qui in ea voluptate velit esse quam nihil molestiae consequatur, vel illum qui dolorem eum fugiat quo voluptas nulla pariatur?</p>'
  FROM `plugin_news_categories` `category`
  JOIN `users` `user` ON `user`.`email` = 'super@ideabubble.ie'
  WHERE `category` = 'Ticker - Ennis';