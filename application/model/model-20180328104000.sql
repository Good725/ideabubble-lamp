/*
ts:2018-03-28 10:40:00
*/


/* Add the "03" template "testimonials" layout, if it does not already exist */
DELIMITER ;;
INSERT INTO
  `plugin_pages_layouts` (`layout`, `template_id`, `use_db_source`, `date_created`, `date_modified`, `created_by`, `modified_by`, `source`)
  SELECT
    'testimonials',
    (SELECT IFNULL(`id`, '') FROM `engine_site_templates` WHERE `stub` = '03' LIMIT 1),
    1,
    CURRENT_TIMESTAMP,
    CURRENT_TIMESTAMP,
    (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
    (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
    '<?php
\n$side_panels = $panel_model->get_panels(\'content_right\', ($settings_instance->get(\'localisation_content_active\') == \'1\'));
\n$testimonials = Model_Testimonials::get_all_items_front_end(null, \'testimonials\');
\n?>
\n
\n<div class=\"row\">
\n    <div class=\"column-content columns medium-7 large-8">
\n        <article>
\n            <section class=\"entry-content\"><?= $page_data[\'content\'] ?><\/section>
\n        <\/article>
\n
\n        <section id=\"testimonials-section\">
\n            <?php if (count($testimonials)): ?>
\n                <?php foreach($testimonials as $testimonial): ?>
\n                    <div class=\"testimonial-block\">
\n                        <div class=\"panel testimonial-panel\"><?= $testimonial[\'content\'] ?><\/div>
\n
\n                        <h4><?= htmlentities($testimonial[\'item_signature\']) ?><\/h4>
\n
\n                        <p><?= htmlentities($testimonial[\'item_company\']) ?><\/p>
\n                    <\/div>
\n                <?php endforeach; ?>
\n            <?php endif; ?>
\n        <\/section>
\n    <\/div>
\n
\n    <div class=\"column-panels columns medium-5 large-4\">
\n        <?= Model_Panels::render(\'Get a Quick Quote\') ?>
\n    <\/div>
\n<\/div>
\n
\n'
  FROM `plugin_pages_layouts`
    WHERE NOT EXISTS (SELECT * FROM `plugin_pages_layouts` WHERE `layout` = 'testimonials' AND `template_id` = (SELECT `id` FROM `engine_site_templates` WHERE `stub` = '30' LIMIT 1) AND `deleted` = 0)
    LIMIT 1
;;