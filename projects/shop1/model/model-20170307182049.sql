/*
ts:2017-03-07 18:20:49
*/

INSERT IGNORE INTO `plugin_pages_pages` (`name_tag`, `title`, `content`,`date_entered`, `last_modified`, `created_by`, `modified_by`, `publish`, `deleted`, `include_sitemap`, `layout_id`, `category_id`)
SELECT
  'contact-us',
  'Contact Us',
  '<div class="page-content"><h1>Contact Us</h1><p>If you have any questions or would like more information about a course, please feel free to contact us today.</p>
  <h2>Limerick School of Music</h2><p>
  <i class="fa fa-map-marker" aria-hidden="true"></i>
  Mulgrave Street
  <br>
  Limerick, V94 HV02
  </p>
  <p>
  <i class="fa fa-phone" aria-hidden="true"></i>
  061 - 417348
  </p>
  <p>
  <i class="fa fa-envelope" aria-hidden="true"></i>
  info@lsom.ie
  </p></div>',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  (SELECT `id` FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' AND `deleted` = 0 LIMIT 1),
  '1',
  '0',
  '1',
  `id`,
  (SELECT `id` FROM `plugin_pages_categorys` WHERE `category` = 'DEFAULT' LIMIT 1)
FROM `plugin_pages_layouts`
WHERE `layout` = 'contactform'
AND NOT EXISTS (SELECT `id` FROM `plugin_pages_pages` WHERE `name_tag` IN ('contact-us.html', 'contact-us') AND `deleted` = 0);