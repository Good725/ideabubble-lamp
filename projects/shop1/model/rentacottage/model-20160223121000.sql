/*
ts:2016-02-23 12:10:00
*/
INSERT IGNORE INTO `plugin_pages_layouts` (`layout`, `source`, `use_db_source`, `publish`, `deleted`, `date_created`, `date_modified`, `created_by`, `modified_by`) VALUES
(
  'favourites',
  '<?php\n// Include the top portion of the site\ninclude Kohana::find_file(\'template_views\', \'header\');\n\n// Get wishlist items\n$property_ids = (array) json_decode(Cookie::get(\'propman_wishlist\', \'[]\'));\n$properties   = array();\nforeach ($property_ids as $id)\n{\n	$properties[] = ORM::factory(\'Propman\')->where(\'id\', \'=\', $id)->find_published();\n}\n?>\n\n<h1>Favourites</h1>\n\n<?php if (count($properties) > 0): ?>\n	<p>View your favourites below.</p>\n	<div class="view-wishlist-listing">\n		<?php include Kohana::find_file(\'views\', \'view_wishlist\'); ?>\n	</div>\n<?php else: ?>\n	<p>You currently have no favourite properties. Why not <a href="/search-results.html">search</a> for some? Click the heart icon to add a favourite.</p>\n<?php endif; ?>\n\n<?php\n// Include the bottom portion of the site\ninclude Kohana::find_file(\'template_views\', \'footer\');\n?>',
  '1',
  '1',
  '0',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1)
);
