/*
ts:2018-05-09 09:30:00
*/

DELIMITER ;;
UPDATE
  `engine_site_templates`
SET
  `date_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  `header`        = '<?php
\n$assets_folder_path      = Kohana::$config->load(\'config\')->assets_folder_path;
\n$assets_folder_code_path = PROJECTPATH.\'www\/assets\/\'.$assets_folder_path;
\n$project_media_folder    = Kohana::$config->load(\'config\')->project_media_folder;
\n$media_model             = new Model_Media();
\n$media_path              = Model_Media::get_path_to_media_item_admin($project_media_folder, \'\', \'\');
\n$settings_instance       = Settings::instance();
\n$panel_model             = new Model_Panels();
\n?><!DOCTYPE html>
\n<html class=\"no-js\" lang=\"en\">
\n    <head>
\n        <meta charset=\"utf-8\" \/>
\n        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
\n        <title><?= $page_data[\'title\'] ?><\/title>
\n
\n        <meta name=\"description\" content=\"<?= $page_data[\'seo_description\'] ?>\"\/>
\n        <link rel=\"canonical\" href=\"<?= str_replace(\'.html.html\', \'.html\', URL::base().$page_data[\'name_tag\'].\'.html\') ?>\" \/>
\n        <meta property=\"og:locale\" content=\"en_GB\" \/>
\n        <meta property=\"og:type\" content=\"website\" \/>
\n        <meta property=\"og:title\" content=\"<?= $page_data[\'title\'] ?>\" \/>
\n        <meta property=\"og:description\" content=\"<?= $page_data[\'seo_description\'] ?>\" \/>
\n        <meta property=\"og:url\" content=\"<?= str_replace(\'.html.html\', \'.html\', URL::base().$page_data[\'name_tag\'].\'.html\') ?>\" \/>
\n        <meta property=\"og:site_name\" content=\"<?= $settings_instance->get(\'company_name\') ?>\" \/>
\n
\n        <meta name=\"google-site-verification\" content=\"<?= Settings::instance()->get(\'google_webmaster_code\') ?>\" />
\n        <meta name=\"msvalidate.01\" content=\"<?= Settings::instance()->get(\'bing_webmaster_code\') ?>\" />
\n
\n        <link rel=\"dns-prefetch\" href=\"\/\/fonts.googleapis.com\" \/>
\n
\n        <script src=\"\/\/ajax.googleapis.com\/ajax\/libs\/jquery\/1.12.4\/jquery.min.js\"><\/script>
\n        <script>window.jQuery || document.write(\'<script src=\"<?= URL::get_engine_assets_base() ?>js\/libs\/jquery-1.12.4.min.js\"><\\/script>\')<\/script>
\n
\n        <?php if ($media_model->is_filename_used(\'favicon.ico\', \'content\')): ?>
\n            <link rel=\"shortcut icon\" href=\"<?= $media_path ?>content\/favicon.ico\" type=\"image\/ico\" \/>
\n        <?php endif; ?>
\n
\n        <?= settings::get_google_analytics_script(); ?>
\n
\n        <link rel=\"stylesheet\" href=\"\/assets\/shared\/css\/browserorg\/style.css\" \/>
\n        <?php if (trim($theme->styles)): ?>
\n            <link rel=\"stylesheet\" href=\"<?= $theme->get_url() ?>\" \/>
\n        <?php endif; ?>
\n        <link href=\"\/engine\/shared\/css\/jquery.datetimepicker.css\" rel=\"stylesheet\" \/>
\n        <script src=\"\/engine\/shared\/js\/daterangepicker\/jquery.datetimepicker.js\"><\/script>
\n        <script>$(document).ready(function(){    $(\".datepicker\").datetimepicker({format:\"d\/m\/Y\",timepicker:false});});<\/script>
\n
\n        <?php if (Settings::instance()->get(\'stripe_enabled\') == \'TRUE\'): ?>
\n            <script src=\"https:\/\/checkout.stripe.com\/checkout.js\"><\/script>
\n        <?php endif; ?>
\n
\n        <script type=\"text\/javascript\" src=\"<?= URL::get_engine_plugin_assets_base(\'payments\') ?>js\/front_end\/payments.js\"></script>
\n    <\/head>
\n
\n    <body class=\"layout-<?= $page_data[\'layout\'] ?><?= ( ! empty($page_data[\'banner_slides\'])) ? \' has_banner\' : \'\' ?>\">
\n        <div class=\"wrapper\">
\n            <div class=\"header-top-desktop-block show-for-large-up\">
\n                <div class=\"row\">
\n                    <div class=\"columns medium-4\">
\n                        <a href=\"\/\">
\n                            <img class=\"desktop-logo\" src=\"<?= $page_data[\'logo\'] ?>\" class=\"logo-image\" alt=\"\" title=\"Homepage\" \/>
\n                        <\/a>
\n                    <\/div>
\n
\n                    <div class=\"columns medium-8\">
\n                        <ul class=\"inline-list right header-top-contact-list\">
\n                            <?php if (trim($settings_instance->get(\'telephone\'))): ?>
\n                                <li class=\"number-li\">
\n                                    <span>Tel:<\/span>
\n                                    <a href=\"tel:<?= preg_replace(\'\/[^0-9]\/\', \'\', $settings_instance->get(\'telephone\')) ?>\"><?= $settings_instance->get(\'telephone\') ?><\/a>
\n                                <\/li>
\n                            <?php endif; ?>
\n
\n                            <?php if (trim($settings_instance->get(\'email\'))): ?>
\n                                <li class=\"number-li\">
\n                                    <span>Email:<\/span>
\n                                    <a href=\"mailto:<?= trim($settings_instance->get(\'email\')) ?>\"><?= $settings_instance->get(\'email\') ?><\/a>
\n                                <\/li>
\n                            <?php endif; ?>
\n
\n                            <?php if ($settings_instance->get(\'show_need_help_button\')): ?>
\n                                <li class=\"header-contact-button\">
\n                                    <a class=\"button primary\" href=\"\/<?= Model_Pages::get_page_by_id($settings_instance->get(\'need_help_page\')) ?>\">
\n                                        <span class=\"fa fa-phone\"><\/span>
\n                                        Request a callback
\n                                    <\/a>
\n                                <\/li>
\n                            <?php endif; ?>
\n                        <\/ul>
\n                    <\/div>
\n                <\/div>
\n            <\/div>
\n
\n            <header class=\"block-header top-bar-container contain-to-grid\">
\n                <nav class=\"top-bar\" data-topbar role=\"navigation\" data-options=\"is_hover: true; mobile_show_parent_link: true;\">
\n                    <ul class=\"title-area hide-for-large-up\">
\n                        <li class=\"name\">
\n                            <a href=\"\/\">
\n                                <img class=\"desktop-logo\" src=\"<?= $page_data[\'logo\'] ?>\" alt=\"\" \/>
\n                            <\/a>
\n                        <\/li>
\n
\n                        <li class=\"toggle-topbar menu-icon\">
\n                            <a href=\"\"><span class=\"fa fa-bars\"><\/span><\/a>
\n                        <\/li>
\n                    <\/ul>
\n
\n                    <section class=\"top-bar-section main-navigation\">
\n                        <?php $main_menu = Menuhelper::get_all_published_menus(\'main\'); ?>
\n
\n                        <ul id=\"menu-main-menu\" class=\"top-bar-menu left\">
\n                            <?php foreach ($main_menu as $level1): ?>
\n                                <?php if ($level1[\'level\'] == 1): ?>
\n                                    <?php
\n                                    $link = menuhelper::get_link($level1);
\n                                    $active_link = ($page_data[\'name_tag\'] == substr($link, strrpos($link, \'\/\') + 1));
\n                                    ?>
\n                                    <li class=\"divider\"><\/li>
\n                                    <li class=\"menu-item menu-item-type-post_type menu-item-object-page<?= ($level1[\'has_sub\']) ? \' has-dropdown\' : \'\' ?><?= $active_link ? \' active\' : \'\' ?>\">
\n                                        <a href=\"<?= $link ?>\">
\n                                            <?= $level1[\'title\'] ?>
\n                                        <\/a>
\n
\n                                        <?php if ($level1[\'has_sub\']): ?>
\n                                            <ul class=\"sub-menu dropdown\">
\n                                                <?php foreach ($main_menu as $level2): ?>
\n                                                    <?php if ($level2[\'level\'] == 2 && $level2[\'parent_id\'] == $level1[\'id\']): ?>
\n                                                        <?php
\n                                                        $link = menuhelper::get_link($level2);
\n                                                        $active_link = ($page_data[\'name_tag\'] == substr($link, strrpos($link, \'\/\') + 1));
\n                                                        ?>
\n                                                        <li class=\"menu-item menu-item-type-post_type menu-item-object-page<?= $active_link ? \' active\' : \'\' ?>\">
\n                                                            <a href=\"<?= $link ?>\">
\n                                                                <?= $level2[\'title\'] ?>
\n                                                            <\/a>
\n                                                        <\/li>
\n                                                    <?php endif; ?>
\n                                                <?php endforeach; ?>
\n                                            <\/ul>
\n                                        <?php endif; ?>
\n                                    <\/li>
\n                                <?php endif; ?>
\n                            <?php endforeach; ?>
\n                        <\/ul>
\n                    <\/section>
\n                <\/nav>
\n            <\/header>
\n
\n            <div class=\"quick_contact hide-for-medium-up\">
\n                <ul class=\"no-bullet\">
\n                    <?php if (trim($settings_instance->get(\'telephone\'))): ?>
\n                        <li class=\"quick_contact-item\">
\n                            <a onclick=\"__gaTracker(\'send\', \'event\', \'Quick contact\', \'Call Us\');\"  href=\"tel:<?= preg_replace(\'\/[^0-9]\/\', \'\', $settings_instance->get(\'telephone\')) ?>\">
\n                                <span class=\"sr-only\"><?= __(\'Phone\') ?><\/span>
\n                                <span class=\"fa fa-phone\"><\/span>
\n                            <\/a>
\n                        <\/li>
\n                    <?php endif; ?>
\n                    <?php if (trim($settings_instance->get(\'email\'))): ?>
\n                        <li onclick=\"__gaTracker(\'send\', \'event\', \'Quick contact\', \'Email\');\"  class=\"quick_contact-item\">
\n                            <a href=\"mailto:<?= str_replace(\' \', \'\', $settings_instance->get(\'email\')) ?>\">
\n                                <span class=\"sr-only\"><?= __(\'Email\') ?><\/span>
\n                                <span class=\"fa fa-envelope\"><\/span>
\n                            <\/a>
\n                        <\/li>
\n                    <?php endif; ?>
\n                    <li class=\"quick_contact-item\">
\n                        <a href=\"\/contact-us.html#content_start\">
\n                            <span class=\"sr-only\"><?= __(\'Location\') ?><\/span>
\n                            <span class=\"fa fa-map-marker\"><\/span>
\n                        <\/a>
\n                    <\/li>
\n                <\/ul>
\n            <\/div>
\n
\n            <main class=\"block-main\" role=\"main\">
\n                <?php if (trim($settings_instance->get(\'telephone\')) || trim($settings_instance->get(\'email\'))): ?>
\n                    <div class=\"chat-sticky show-for-medium-up\">
\n                        <ul class=\"no-bullet\">
\n                            <?php if (trim($settings_instance->get(\'telephone\'))): ?>
\n                                <li>
\n                                    <a onclick=\"__gaTracker(\'send\', \'event\', \'Sidebar\', \'Call Us\');\" href=\"tel:<?= preg_replace(\'\/[^0-9]\/\', \'\', $settings_instance->get(\'telephone\')) ?>\" class=\"tooltip-left\" data-tooltip=\"Call us\">
\n                                        <span class=\"fa fa-phone\"><\/span>
\n                                    <\/a>
\n                                <\/li>
\n                            <?php endif; ?>
\n
\n                            <?php if (trim($settings_instance->get(\'email\'))): ?>
\n                                <li>
\n                                    <a onclick=\"__gaTracker(\'send\', \'event\', \'Sidebar\', \'Email\');\" href=\"mailto:<?= trim($settings_instance->get(\'email\')) ?>\" class=\"tooltip-left\" data-tooltip=\"Email us\">
\n                                        <span class=\"fa fa-envelope\"><\/span>
\n                                    <\/a>
\n                                <\/li>
\n                            <?php endif; ?>
\n                        <\/ul>
\n                    <\/div>
\n                <?php endif;?>
\n
\n                <?php $banner_data = Model_Pagebanner::get_banner_data($page_data[\'banner_photo\']); ?>
\n
\n                <?php if ( ! empty($banner_data[\'map_data\']) || ! empty($page_data[\'banner_slides\']) || ! empty($page_data[\'banner_image\'])): ?>
\n                    <div class=\"frontpage-banner\">
\n                        <?php if ( ! empty($banner_data[\'map_data\']) && $banner_data[\'map_data\'][\'publish\']): ?>
\n                            <?php \/\/ map banner ?>
\n                            <?= $banner_data[\'map_data\'][\'html\']; ?>
\n                        <?php elseif ( ! empty($page_data[\'banner_slides\'])): ?>
\n                            <?php \/\/ dynamic \/ custom banner ?>
\n
\n                            <?php foreach ($page_data[\'banner_slides\'] as $slide): ?>
\n                                <div class=\"banner-slide banner-slide\-\-<?= $slide[\'overlay_position\'] ?>\" data-overlay_position=\"<?= trim($slide[\'overlay_position\']) ?>\">
\n                                    <div class=\"banner-image attachment-banner size-banner\" style=\"background-image: url(\'\/shared_media\/<?= $project_media_folder ?>\/media\/photos\/banners\/<?= $slide[\'image\'] ?>\')\">
\n                                        <img src=\"\/shared_media\/<?= $project_media_folder ?>\/media\/photos\/banners\/<?= $slide[\'image\'] ?>\" alt=\"\" \/>
\n                                    <\/div>
\n
\n                                    <div class=\"banner-shadow\"><\/div>
\n                                    <?php
\n                                    \/\/ Check if \"banner-left.png\" and \"banner-right.png\" have been uploaded to the media plugin.
\n                                    \/\/ If so, add them before and\/or after the banner
\n                                    ?>
\n
\n                                    <?php if ($media_model->is_filename_used(\'banner-left.png\', \'content\')): ?>
\n                                        <div class=\"banner-left\" style=\"background-image: url(\'<?= $media_path ?>content\/banner-left.png\');\"><\/div>
\n                                    <?php endif; ?>
\n
\n                                    <?php if ($media_model->is_filename_used(\'banner-right.png\', \'content\')): ?>
\n                                        <div class=\"banner-right\" style=\"background-image: url(\'<?= $media_path ?>content\/banner-right.png\');\"><\/div>
\n                                    <?php endif; ?>
\n
\n                                    <?php if (!empty($slide[\'html\'])): ?>
\n                                        <div class=\"frontpage-banner-caption\">
\n                                            <div class=\"row\">
\n                                                <div class=\"banner-caption-content columns large-6 small-12\"><?= $slide[\'html\'] ?><\/div>
\n                                            <\/div>
\n                                        <\/div>
\n                                    <?php endif; ?>
\n                                <\/div>
\n                            <?php endforeach; ?>
\n                        <?php elseif ( ! empty ($page_data[\'banner_image\'])): ?>
\n                            <?php \/\/ static banner ?>
\n
\n                            <img
\n                                src=\"\/shared_media\/<?= $project_media_folder ?>\/media\/photos\/banners\/<?= $page_data[\'banner_image\'] ?>\"
\n                                class=\"attachment-banner size-banner\"
\n                                alt=\"\"
\n                                \/>
\n                            <div class=\"banner-left\"><\/div>
\n                            <div class=\"banner-right\"><\/div>
\n                        <?php endif; ?>
\n                    <\/div>
\n                <?php endif; ?>'
WHERE
  `stub` = '03';;

UPDATE
  `engine_site_templates`
SET
  `styles` = REPLACE(`styles`, 'shared/validation.css', 'shared/css/validation.css')
WHERE
  `stub` = '03'
;;