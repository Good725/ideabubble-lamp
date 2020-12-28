/*
ts:2020-05-12 18:05:00
*/

/** Emerging trends **/

DELIMITER ;;

UPDATE `engine_settings`
SET    `value_live`='1', `value_stage`='1', `value_test`='1', `value_dev`='1'
WHERE  `variable`='enable_news_filters';;


-- Insert the "emerging trends" (news) page, if it doesn't already exist
INSERT IGNORE INTO `plugin_pages_pages` (`name_tag`, `title`, `content`,`date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`)
SELECT
  'news',
  'news',
  '<h1>Emerging trends</h1>',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  '1',
  '1',
  '1',
  '0',
  '1',
  (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'news3' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'DEFAULT' LIMIT 1)
FROM `plugin_pages_pages`
WHERE NOT EXISTS (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` IN ('news', 'news.html') AND `deleted` = 0)
LIMIT 1;;

-- Update the page
UPDATE
  `plugin_pages_pages`
SET
  `name_tag`      = 'news',
  `title`         = 'Emerging trends',
  `layout_id`     = (SELECT `id` FROM `plugin_pages_layouts` WHERE `layout` = 'news3' AND `template_id` = (SELECT `id` FROM `engine_site_templates` WHERE `stub` = '04') AND `deleted` = 0 LIMIT 1),
  `last_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = 1,
  `content`       = '',
  `footer`        = '<p>{spotlights-}</p>'
WHERE
  `name_tag` IN ('news', 'news.html');;

-- Add more news items
INSERT INTO `plugin_news`
  (`category_id`, `title`, `image`, `event_date`, `order`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`)
VALUES
  ((SELECT `id` FROM `plugin_news_categories` WHERE `category` = 'Emerging trends' LIMIT 1), 'Title of article. Emerging trend 4', 'emerging-trends-4.png', '2020-05-24 00:00:00', '4', CURRENT_TIMESTAMP , CURRENT_TIMESTAMP, 1, 1, '1', '0'),
  ((SELECT `id` FROM `plugin_news_categories` WHERE `category` = 'Emerging trends' LIMIT 1), 'Title of article. Emerging trend 5', 'emerging-trends-5.png', '2020-05-22 00:00:00', '5', CURRENT_TIMESTAMP , CURRENT_TIMESTAMP, 1, 1, '1', '0'),
  ((SELECT `id` FROM `plugin_news_categories` WHERE `category` = 'Emerging trends' LIMIT 1), 'Title of article. Emerging trend 6', 'emerging-trends-6.png', '2020-05-20 00:00:00', '6', CURRENT_TIMESTAMP , CURRENT_TIMESTAMP, 1, 1, '1', '0'),
  ((SELECT `id` FROM `plugin_news_categories` WHERE `category` = 'Emerging trends' LIMIT 1), 'Title of video. Video item 1. Lorem ipsum',      'video.png', '2020-05-24 00:00:00', '1', CURRENT_TIMESTAMP , CURRENT_TIMESTAMP, 1, 1, '1', '0'),
  ((SELECT `id` FROM `plugin_news_categories` WHERE `category` = 'Emerging trends' LIMIT 1), 'Title of video. Video item 2. Lorem ipsum',      'video.png', '2020-05-22 00:00:00', '2', CURRENT_TIMESTAMP , CURRENT_TIMESTAMP, 1, 1, '1', '0'),
  ((SELECT `id` FROM `plugin_news_categories` WHERE `category` = 'Emerging trends' LIMIT 1), 'Title of video. Video item 3. Lorem ipsum',      'video.png', '2020-05-20 00:00:00', '3', CURRENT_TIMESTAMP , CURRENT_TIMESTAMP, 1, 1, '1', '0');;

INSERT INTO `plugin_news`
  (`category_id`, `title`, `image`, `event_date`, `order`, `date_created`, `date_modified`, `created_by`, `modified_by`, `publish`, `deleted`)
VALUES
  ((SELECT `id` FROM `plugin_news_categories` WHERE `category` = 'Emerging trends' LIMIT 1), 'Title of podcast 1. Lorem ispum dolot sit', 'podcast-1.png', '2020-05-24 00:00:00', '1', CURRENT_TIMESTAMP , CURRENT_TIMESTAMP, 1, 1, '1', '0'),
  ((SELECT `id` FROM `plugin_news_categories` WHERE `category` = 'Emerging trends' LIMIT 1), 'Title of podcast 2. Lorem ispum dolot sit', 'podcast-2.png', '2020-05-22 00:00:00', '2', CURRENT_TIMESTAMP , CURRENT_TIMESTAMP, 1, 1, '1', '0'),
  ((SELECT `id` FROM `plugin_news_categories` WHERE `category` = 'Emerging trends' LIMIT 1), 'Title of podcast 3. Lorem ispum dolot sit', 'podcast-3.png', '2020-05-20 00:00:00', '3', CURRENT_TIMESTAMP , CURRENT_TIMESTAMP, 1, 1, '1', '0'),
  ((SELECT `id` FROM `plugin_news_categories` WHERE `category` = 'Emerging trends' LIMIT 1), 'Title of blog 1. Lorem ipsum dolor sit',    'blog-1.jpg', '2020-05-24 00:00:00', '1', CURRENT_TIMESTAMP , CURRENT_TIMESTAMP, 1, 1, '1', '0'),
  ((SELECT `id` FROM `plugin_news_categories` WHERE `category` = 'Emerging trends' LIMIT 1), 'Title of blog 2. Lorem ipsum dolor sit',    'blog-2.png', '2020-05-22 00:00:00', '2', CURRENT_TIMESTAMP , CURRENT_TIMESTAMP, 1, 1, '1', '0'),
  ((SELECT `id` FROM `plugin_news_categories` WHERE `category` = 'Emerging trends' LIMIT 1), 'Title of blog 3. Lorem ipsum dolor sit',    'blog-3.png', '2020-05-20 00:00:00', '3', CURRENT_TIMESTAMP , CURRENT_TIMESTAMP, 1, 1, '1', '0');;

-- Set the media type of existing news items
UPDATE `plugin_news` SET `media_type` = 'Article' WHERE `title` LIKE 'Title of article%';;
UPDATE `plugin_news` SET `media_type` = 'Video'   WHERE `title` LIKE 'Title of video%';;
UPDATE `plugin_news` SET `media_type` = 'Podcast' WHERE `title` LIKE 'Title of podcast%';;
UPDATE `plugin_news` SET `media_type` = 'Blog'    WHERE `title` LIKE 'Title of blog%';;

UPDATE `plugin_news` SET `image` = 'podcast-3.png' WHERE `image` = 'podcast-3.jpg';;
UPDATE `plugin_news` SET `image` = 'blog-2.jpg'    WHERE `image` = 'blog-2.png';;