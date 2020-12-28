/*
ts:2016-03-04 15:20:00
*/
INSERT INTO `plugin_pages_layouts` (`layout`, `source`, `use_db_source`, `publish`, `deleted`, `date_created`, `date_modified`, `created_by`, `modified_by`)
VALUES
(
  'content-wide',
  '<?php $template_folder = Kohana::$config->load(\'config\')->template_folder_path; ?>\n<?php include Kohana::find_file(\'template_views\', \'html_document_header\'); ?>\n<body id=\"<?= $page_data[\'layout\'] ?>\" class=\"<?= $page_data[\'category\'] ?>\">\n    <div id=\"container\">\n        <?php include PROJECTPATH.\'views/templates/\'.$template_folder.\'/header.php\'; ?>\n        <div id=\"main\">\n            <div id=\"ct\">\n                <div id=\"banner\"><?= Model_PageBanner::render_frontend_banners($page_data[\'banner_photo\']) ?></div>\n                <div id=\"ct_left\" class=\"column\">\n                    <div class=\"content\"><?= $page_data[\'content\'] ?></div>\n                </div>\n            </div>\n        </div>\n        <div id=\"footer\">\n            <?php include PROJECTPATH.\'views/templates/\'.$template_folder.\'/footer.php\'; ?>\n        </div>\n    </div>\n    <?= Settings::instance()->get(\'footer_html\'); ?>\n</body>\n</html>',
  '1',
  '1',
  '0',
  CURRENT_TIMESTAMP,
  CURRENT_TIMESTAMP,
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  (SELECT `id` FROM `users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1)
);