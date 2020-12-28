/*
ts:2017-11-29 15:00:01
*/


/* Add the "03" template, if it does not already exist. */
INSERT INTO
  `engine_site_templates` (`title`, `stub`, `type`, `date_created`, `date_modified`)
  SELECT '03', '03', 'website', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
    FROM `engine_site_templates`
    WHERE NOT EXISTS (SELECT * FROM `engine_site_templates` WHERE `stub` = '03')
    LIMIT 1
;

/* Add the "30" theme, if it does not already exist */
INSERT INTO
  `engine_site_themes` (`title`, `stub`, `template_id`, `date_created`, `date_modified`)
  SELECT '30', '30', (SELECT `id`  FROM `engine_site_templates` WHERE `stub` = '03' LIMIT 1), CURRENT_TIMESTAMP, CURRENT_TIMESTAMP
    FROM `engine_site_themes`
    WHERE NOT EXISTS (SELECT * FROM `engine_site_themes` WHERE `stub` = '30')
    LIMIT 1
;

/* Update the template */
DELIMITER ;;
UPDATE
  `engine_site_templates`
SET
  `date_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  `header`        = '<?php
\n$assets_folder_path      = Kohana::$config->load(\'config\')->assets_folder_path;
\n$assets_folder_code_path = PROJECTPATH.\'www/assets/\'.$assets_folder_path;
\n$project_media_folder    = Kohana::$config->load(\'config\')->project_media_folder;
\n$media_model             = new Model_Media();
\n$media_path              = Model_Media::get_path_to_media_item_admin($project_media_folder, \'\', \'\');
\n$settings_instance       = Settings::instance();
\n$panel_model             = new Model_Panels();
\n?><!DOCTYPE html>
\n<html class=\"no-js\" lang=\"en\">
\n    <head>
\n        <meta charset=\"utf-8\" />
\n        <meta name=\"viewport\" content=\"width=device-width, initial-scale=1.0\">
\n        <title><?= $page_data[\'title\'] ?></title>
\n
\n        <meta name=\"description\" content=\"<?= $page_data[\'seo_description\'] ?>\"/>
\n        <link rel=\"canonical\" href=\"<?= str_replace(\'.html.html\', \'.html\', URL::base().$page_data[\'name_tag\'].\'.html\') ?>\" />
\n        <meta property=\"og:locale\" content=\"en_GB\" />
\n        <meta property=\"og:type\" content=\"website\" />
\n        <meta property=\"og:title\" content=\"<?= $page_data[\'title\'] ?>\" />
\n        <meta property=\"og:description\" content=\"<?= $page_data[\'seo_description\'] ?>\" />
\n        <meta property=\"og:url\" content=\"<?= str_replace(\'.html.html\', \'.html\', URL::base().$page_data[\'name_tag\'].\'.html\') ?>\" />
\n        <meta property=\"og:site_name\" content=\"<?= $settings_instance->get(\'company_name\') ?>\" />
\n
\n        <link rel=\"dns-prefetch\" href=\"//fonts.googleapis.com\" />
\n
\n        <script src=\"//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js\"></script>
\n        <script>window.jQuery || document.write(\'<script src=\"<?= URL::get_engine_assets_base() ?>js/libs/jquery-1.12.4.min.js\"><\\/script>\')</script>
\n
\n        <?php if ($media_model->is_filename_used(\'favicon.ico\', \'content\')): ?>
\n            <link rel=\"shortcut icon\" href=\"<?= $media_path ?>content/favicon.ico\" type=\"image/ico\" />
\n        <?php endif; ?>
\n
\n        <link rel=\"stylesheet\" href=\"/assets/shared/css/browserorg/style.css\" />
\n        <?php if (trim($theme->styles)): ?>
\n            <link rel=\"stylesheet\" href=\"<?= $theme->get_url() ?>\" />
\n        <?php endif; ?>
\n    </head>
\n
\n    <body class=\"layout-<?= $page_data[\'layout\'] ?><?= ( ! empty($page_data[\'banner_slides\'])) ? \'has_banner\' : \'\' ?>\">
\n        <div class=\"wrapper\">
\n            <div class=\"header-top-desktop-block show-for-large-up\">
\n                <div class=\"row\">
\n                    <div class=\"columns medium-4\">
\n                        <a href=\"/\">
\n                            <img class=\"desktop-logo\" src=\"<?= $page_data[\'logo\'] ?>\" class=\"logo-image\" alt=\"\" title=\"Homepage\" />
\n                        </a>
\n                    </div>
\n
\n                    <div class=\"columns medium-8\">
\n                        <ul class=\"inline-list right header-top-contact-list\">
\n                            <?php if (trim($settings_instance->get(\'telephone\'))): ?>
\n                                <li class=\"number-li\">
\n                                    <span>Tel:</span>
\n                                    <a href=\"tel:<?= trim($settings_instance->get(\'telephone\')) ?>\"><?= $settings_instance->get(\'telephone\') ?></a>
\n                                </li>
\n                            <?php endif; ?>
\n
\n                            <?php if (trim($settings_instance->get(\'email\'))): ?>
\n                                <li class=\"number-li\">
\n                                    <span>Email:</span>
\n                                    <a href=\"mailto:<?= trim($settings_instance->get(\'email\')) ?>\"><?= $settings_instance->get(\'email\') ?></a>
\n                                </li>
\n                            <?php endif; ?>
\n
\n                            <?php if ($settings_instance->get(\'show_need_help_button\')): ?>
\n                                <li class=\"header-contact-button\">
\n                                    <a class=\"button primary\" href=\"/<?= Model_Pages::get_page_by_id($settings_instance->get(\'need_help_page\')) ?>\">
\n                                        <span class=\"fa fa-phone\"></span>
\n                                        Request a callback
\n                                    </a>
\n                                </li>
\n                            <?php endif; ?>
\n                        </ul>
\n                    </div>
\n                </div>
\n            </div>
\n
\n            <header class=\"block-header top-bar-container contain-to-grid\">
\n                <nav class=\"top-bar\" data-topbar role=\"navigation\" data-options=\"is_hover: true; mobile_show_parent_link: true;\">
\n                    <ul class=\"title-area hide-for-large-up\">
\n                        <li class=\"name\">
\n                            <a href=\"/\">
\n                                <img class=\"desktop-logo\" src=\"<?= $page_data[\'logo\'] ?>\" alt=\"\" />
\n                            </a>
\n                        </li>
\n
\n                        <li class=\"toggle-topbar menu-icon\">
\n                            <a href=\"\"><span class=\"fa fa-bars\"></span></a>
\n                        </li>
\n                    </ul>
\n
\n                    <section class=\"top-bar-section main-navigation\">
\n                        <?php $main_menu = Menuhelper::get_all_published_menus(\'main\'); ?>
\n
\n                        <ul id=\"menu-main-menu\" class=\"top-bar-menu left\">
\n                            <?php foreach ($main_menu as $level1): ?>
\n                                <?php if ($level1[\'level\'] == 1): ?>
\n                                    <?php
\n                                    $link = menuhelper::get_link($level1);
\n                                    $active_link = ($page_data[\'name_tag\'] == substr($link, strrpos($link, \'/\') + 1));
\n                                    ?>
\n                                    <li class=\"divider\"></li>
\n                                    <li class=\"menu-item menu-item-type-post_type menu-item-object-page<?= ($level1[\'has_sub\']) ? \' has-dropdown\' : \'\' ?><?= $active_link ? \' active\' : \'\' ?>\">
\n                                        <a href=\"<?= $link ?>\">
\n                                            <?= $level1[\'title\'] ?>
\n                                        </a>
\n
\n                                        <?php if ($level1[\'has_sub\']): ?>
\n                                            <ul class=\"sub-menu dropdown\">
\n                                                <?php foreach ($main_menu as $level2): ?>
\n                                                    <?php if ($level2[\'level\'] == 2 && $level2[\'parent_id\'] == $level1[\'id\']): ?>
\n                                                        <?php
\n                                                        $link = menuhelper::get_link($level2);
\n                                                        $active_link = ($page_data[\'name_tag\'] == substr($link, strrpos($link, \'/\') + 1));
\n                                                        ?>
\n                                                        <li class=\"menu-item menu-item-type-post_type menu-item-object-page<?= $active_link ? \' active\' : \'\' ?>\">
\n                                                            <a href=\"<?= $link ?>\">
\n                                                                <?= $level2[\'title\'] ?>
\n                                                            </a>
\n                                                        </li>
\n                                                    <?php endif; ?>
\n                                                <?php endforeach; ?>
\n                                            </ul>
\n                                        <?php endif; ?>
\n                                    </li>
\n                                <?php endif; ?>
\n                            <?php endforeach; ?>
\n                        </ul>
\n                    </section>
\n                </nav>
\n            </header>
\n
\n            <div class=\"quick_contact hide-for-medium-up\">
\n                <ul class=\"no-bullet\">
\n                    <?php if (trim($settings_instance->get(\'telephone\'))): ?>
\n                        <li class=\"quick_contact-item\">
\n                            <a onclick=\"__gaTracker(\'send\', \'event\', \'Quick contact\', \'Call Us\');\"  href=\"tel:<?= str_replace(\' \', \'\', $settings_instance->get(\'telephone\')) ?>\">
\n                                <span class=\"sr-only\"><?= __(\'Phone\') ?></span>
\n                                <span class=\"fa fa-phone\"></span>
\n                            </a>
\n                        </li>
\n                    <?php endif; ?>
\n                    <?php if (trim($settings_instance->get(\'email\'))): ?>
\n                        <li onclick=\"__gaTracker(\'send\', \'event\', \'Quick contact\', \'Email\');\"  class=\"quick_contact-item\">
\n                            <a href=\"mailto:<?= str_replace(\' \', \'\', $settings_instance->get(\'email\')) ?>\">
\n                                <span class=\"sr-only\"><?= __(\'Email\') ?></span>
\n                                <span class=\"fa fa-envelope\"></span>
\n                            </a>
\n                        </li>
\n                    <?php endif; ?>
\n                    <li class=\"quick_contact-item\">
\n                        <a href=\"/contact-us.html#content_start\">
\n                            <span class=\"sr-only\"><?= __(\'Location\') ?></span>
\n                            <span class=\"fa fa-map-marker\"></span>
\n                        </a>
\n                    </li>
\n                </ul>
\n            </div>
\n
\n            <main class=\"block-main\" role=\"main\">
\n                <?php if (trim($settings_instance->get(\'telephone\')) || trim($settings_instance->get(\'email\'))): ?>
\n                    <div class=\"chat-sticky show-for-medium-up\">
\n                        <ul class=\"no-bullet\">
\n                            <?php if (trim($settings_instance->get(\'telephone\'))): ?>
\n                                <li>
\n                                    <a onclick=\"__gaTracker(\'send\', \'event\', \'Sidebar\', \'Call Us\');\" href=\"tel:<?= trim($settings_instance->get(\'telephone\')) ?>\" class=\"tooltip-left\" data-tooltip=\"Call us\">
\n                                        <span class=\"fa fa-phone\"></span>
\n                                    </a>
\n                                </li>
\n                            <?php endif; ?>
\n
\n                            <?php if (trim($settings_instance->get(\'email\'))): ?>
\n                                <li>
\n                                    <a onclick=\"__gaTracker(\'send\', \'event\', \'Sidebar\', \'Email\');\" href=\"mailto:<?= trim($settings_instance->get(\'email\')) ?>\" class=\"tooltip-left\" data-tooltip=\"Email us\">
\n                                        <span class=\"fa fa-envelope\"></span>
\n                                    </a>
\n                                </li>
\n                            <?php endif; ?>
\n                        </ul>
\n                    </div>
\n                <?php endif;?>
\n
\n                <?php if ( ! empty($page_data[\'banner_slides\']) OR ( ! empty($page_data[\'banner_image\']))): ?>
\n                    <div class=\"frontpage-banner\">
\n
\n                        <?php if ( ! empty($page_data[\'banner_slides\'])): ?>
\n                            <?php // dynamic / custom banner ?>
\n
\n                            <?php foreach ($page_data[\'banner_slides\'] as $slide): ?>
\n                                <div class=\"banner-slide\">
\n                                    <div class=\"banner-image attachment-banner size-banner\" style=\"background-image: url(\'/shared_media/<?= $project_media_folder ?>/media/photos/banners/<?= $slide[\'image\'] ?>\')\"></div>
\n
\n                                    <div class=\"banner-shadow\"></div>
\n                                    <?php
\n                                    // Check if \"banner-left.png\" and \"banner-right.png\" have been uploaded to the media plugin.
\n                                    // If so, add them before and/or after the banner
\n                                    ?>
\n
\n                                    <?php if ($media_model->is_filename_used(\'banner-left.png\', \'content\')): ?>
\n                                        <div class=\"banner-left\" style=\"background-image: url(\'<?= $media_path ?>content/banner-left.png\');\"></div>
\n                                    <?php endif; ?>
\n
\n                                    <?php if ($media_model->is_filename_used(\'banner-right.png\', \'content\')): ?>
\n                                        <div class=\"banner-right\" style=\"background-image: url(\'<?= $media_path ?>content/banner-right.png\');\"></div>
\n                                    <?php endif; ?>
\n
\n                                    <?php if (!empty($slide[\'html\'])): ?>
\n                                        <div class=\"frontpage-banner-caption\">
\n                                            <div class=\"row\">
\n                                                <div class=\"columns large-6 small-12\"><?= $slide[\'html\'] ?></div>
\n                                            </div>
\n                                        </div>
\n                                    <?php endif; ?>
\n                                </div>
\n                            <?php endforeach; ?>
\n                        <?php elseif ( ! empty ($page_data[\'banner_image\'])): ?>
\n                            <?php // static banner ?>
\n
\n                            <img
\n                                src=\"/shared_media/<?= $project_media_folder ?>/media/photos/banners/<?= $page_data[\'banner_image\'] ?>\"
\n                                class=\"attachment-banner size-banner\"
\n                                alt=\"\"
\n                                />
\n                            <div class=\"banner-left\"></div>
\n                            <div class=\"banner-right\"></div>
\n                        <?php endif; ?>
\n                    </div>
\n                <?php endif; ?>',
  `footer`        = '                <?php $partners = Menuhelper::get_all_published_menus(\'partners\'); ?>
\n                <?php if ( ! empty($partners)): ?>
\n                    <section class=\"client-block\">
\n                        <div class=\"row\">
\n                            <div class=\"small-12\">
\n                                <h2 class=\"text-center\"><?= __(\'Our Partners\') ?></h2>
\n
\n                                <ul class=\"partners\">
\n                                    <?php foreach ($partners as $partner): ?>
\n                                        <li>
\n                                            <?php $link = menuhelper::get_link($partner); ?>
\n
\n                                            <?php if ($link): ?><a href=\"<?= $link ?>\"><?php endif; ?>
\n                                                <img width=\"150\" height=\"150\"
\n                                                     src=\"/shared_media/<?= $project_media_folder ?>/media/photos/menus/<?= isset($partner[\'filename\']) ? $partner[\'filename\'] : \'\' ?>\"
\n                                                     alt=\"<?= $partner[\'title\'] ?>\"
\n                                                     class=\"attachment-thumbnail size-thumbnail\"
\n                                                    />
\n                                            <?php if ($link): ?></a><?php endif; ?>
\n                                        </li>
\n                                    <?php endforeach; ?>
\n                                </ul>
\n                            </div>
\n                        </div>
\n                    </section>
\n                <?php endif; ?>
\n            </main>
\n
\n            <footer class=\"block-footer\" role=\"contentinfo\">
\n                <section class=\"footer-menu\">
\n                    <div class=\"row footer-columns\">
\n                        <div class=\"columns medium-3 footer-column\">
\n                            <h5><?= __(\'Contact us\') ?></h5>
\n
\n                            <ul class=\"no-bullet\">
\n                                <?php if (trim($settings_instance->get(\'company_title\'))): ?>
\n                                    <li><?= $settings_instance->get(\'company_title\') ?></li>
\n                                <?php endif; ?>
\n
\n                                <?php if (trim($settings_instance->get(\'telephone\'))): ?>
\n                                    <li><span class=\"contact-label\">Tel</span> <?= $settings_instance->get(\'telephone\')?></li>
\n                                <?php endif; ?>
\n
\n                                <?php if (trim($settings_instance->get(\'fax\'))): ?>
\n                                    <li><span class=\"contact-label\">Fax</span> <?= $settings_instance->get(\'fax\')?></li>
\n                                <?php endif; ?>
\n
\n                                <?php if (trim($settings_instance->get(\'email\'))): ?>
\n                                    <li><span class=\"contact-label\">Email</span> <?= $settings_instance->get(\'email\')?></li>
\n                                <?php endif; ?>
\n                            </ul>
\n                        </div>
\n
\n                        <?php $footer_links = Menuhelper::get_all_published_menus(\'footer\'); ?>
\n
\n                        <?php foreach ($footer_links as $level1): ?>
\n                            <?php if ($level1[\'level\'] == 1): ?>
\n                                <div class=\"footer-column columns medium-3\">
\n                                    <?php $link = menuhelper::get_link($level1); ?>
\n                                    <h5><?= $link ? \'<a href=\"\'.$link.\'\">\'.$level1[\'title\'].\'</a>\' : $level1[\'title\'] ?></h5>
\n
\n                                    <?php if ($level1[\'has_sub\']): ?>
\n                                        <ul>
\n                                            <?php foreach ($footer_links as $level2): ?>
\n                                                <?php if ($level2[\'level\'] == 2 && $level2[\'parent_id\'] == $level1[\'id\']): ?>
\n                                                    <li class=\"menu-item\">
\n                                                        <a href=\"<?= menuhelper::get_link($level2) ?>\"><?= $level2[\'title\'] ?></a>
\n                                                    </li>
\n                                                <?php endif; ?>
\n                                            <?php endforeach; ?>
\n                                        </ul>
\n                                    <?php endif; ?>
\n                                </div>
\n                            <?php endif; ?>
\n                        <?php endforeach; ?>
\n                    </div>
\n
\n                    <div class=\"row footer-follow\">
\n                        <div class=\"small-12 large-3 columns\">
\n                            <?php
\n                            $social_media[\'facebook_url\']  = trim($settings_instance->get(\'facebook_url\'));
\n                            $social_media[\'twitter_url\']   = trim($settings_instance->get(\'twitter_url\'));
\n                            $social_media[\'linkedin_url\']  = trim($settings_instance->get(\'linkedin_url\'));
\n                            $social_media[\'instagram_url\'] = trim($settings_instance->get(\'instagram_url\'));
\n                            $social_media[\'snapchat_url\']  = trim($settings_instance->get(\'snapchat_url\'));
\n
\n                            if ($social_media[\'facebook_url\'] != \'\' && strpos($social_media[\'facebook_url\'], \'facebook.com/\') == false) {
\n                                $social_media[\'facebook_url\'] = \'https://www.facebook.com/\'.$social_media[\'facebook_url\'];
\n                            }
\n                            if ($social_media[\'twitter_url\'] != \'\' && strpos($social_media[\'twitter_url\'], \'twitter.com/\') == false) {
\n                                $social_media[\'twitter_url\'] = \'https://twitter.com/\'.$social_media[\'twitter_url\'];
\n                            }
\n                            if ($social_media[\'linkedin_url\'] != \'\' && strpos($social_media[\'linkedin_url\'], \'linkedin.com/\') == false) {
\n                                $social_media[\'linkedin_url\'] = \'https://linkedin.com/in/\'.$social_media[\'linkedin_url\'];
\n                            }
\n                            if ($social_media[\'instagram_url\'] != \'\' && strpos($social_media[\'instagram_url\'], \'instagram.com/\') == false) {
\n                                $social_media[\'instagram_url\'] = \'http://instagram.com/\'.$social_media[\'instagram_url\'];
\n                            }
\n                            if ($social_media[\'snapchat_url\'] != \'\' && strpos($social_media[\'snapchat_url\'], \'snapchat.com/\') == false) {
\n                                $social_media[\'snapchat_url\'] = \'http://snapchat.com/add/\'.$social_media[\'snapchat_url\'];
\n                            }
\n                            ?>
\n
\n                            <?php if (count($social_media)): ?>
\n                                <div class=\"footer-social\">
\n                                    <h5>Follow Us</h5>
\n
\n                                    <ul class=\"inline-list\" style=\"display: inline-block;\">
\n                                        <?php
\n                                        $name = $icon = \'facebook\';
\n                                        include Kohana::find_file(\'template_views\', \'footer_social_item\');
\n                                        $name = $icon = \'twitter\';
\n                                        include Kohana::find_file(\'template_views\', \'footer_social_item\');
\n                                        $name = $icon = \'linkedin\';
\n                                        include Kohana::find_file(\'template_views\', \'footer_social_item\');
\n                                        $name = $icon = \'instagram\';
\n                                        include Kohana::find_file(\'template_views\', \'footer_social_item\');
\n                                        $name = \'snapchat\';
\n                                        $icon = \'snapchat-ghost\';
\n                                        include Kohana::find_file(\'template_views\', \'footer_social_item\');
\n                                        ?>
\n                                    </ul>
\n                                </div>
\n                            <?php endif; ?>
\n                        </div>
\n
\n                        <div class=\"medium-12 large-6 columns small-only-text-center footer-slogan\"><?= htmlentities($settings_instance->get(\'company_slogan\')) ?></div>
\n
\n                        <?php if ($settings_instance->get(\'site_footer_logo\')): ?>
\n                            <div class=\"medium-12 large-3 columns small-only-text-center footer-logo\"><img src=\"<?= $media_path ?>logos/<?= $settings_instance->get(\'site_footer_logo\') ?>\" /></div>
\n                        <?php endif; ?>
\n                    </div>
\n
\n                    <div class=\"row footer-copyright\">
\n                        <div class=\"columns medium-6 small-text-center medium-text-left footer-copyright-company\"><?= $settings_instance->get(\'company_copyright\') ?></div>
\n                        <div class=\"columns medium-6 small-text-center medium-text-right footer-copyright-cms\"><?= (trim($settings_instance->get(\'cms_copyright\')) == \'\') ? \'Powered by <a href=\"https://ideabubble.ie\">Idea Bubble</a>\' : $settings_instance->get(\'cms_copyright\') ?></div>
\n                    </div>
\n                </section>
\n            </footer>
\n        </div><?php // .wrapper ?>
\n
\n        <script type=\"text/javascript\" src=\"/assets/<?= $assets_folder_path ?>/js/jquery.validationEngine2.js\"></script>
\n        <script type=\"text/javascript\" src=\"/assets/<?= $assets_folder_path ?>/js/jquery.validationEngine2-en.js\"></script>
\n        <script type=\"text/javascript\" src=\"/assets/<?= $assets_folder_path ?>/js/general.js<?= file_exists($assets_folder_code_path.\'/js/general.js\') ? \'?ts=\'.filemtime($assets_folder_code_path.\'/js/general.js\') : \'\' ?>\"></script>
\n    </body>
\n</html>',
  `styles`        = '@charset \"UTF-8\";
\n
\n@import url(\'/assets/shared/css/font-awesome.min.css\');
\n@import url(\'/assets/shared/css/foundation-5.5.3.css\');
\n@import url(\'/assets/shared/validation.css\');
\n
\np {
\n    font-size: inherit;
\n}
\n
\narticle a {
\n    text-decoration: underline;
\n}
\n
\n.widget {
\n    margin-bottom: 1rem;
\n}
\n.widget h3 {
\n    font-size: 1.2rem;
\n}
\n.widget ul {
\n    margin: 0;
\n    list-style: none;
\n}
\n
\n.post-featured-image {
\n    margin-bottom: 1rem;
\n}
\n
\n.alignleft {
\n    float: left;
\n    margin-right: 1rem;
\n}
\n
\n.alignright {
\n    float: right;
\n    margin-left: 1rem;
\n}
\n
\n.aligncenter {
\n    display: block;
\n    margin-left: auto !important;
\n    margin-right: auto !important;
\n}
\n
\n.social-icons li {
\n    margin-left: 0.5rem;
\n}
\n
\n.social-icons .webicon {
\n    box-shadow: none;
\n}
\n.social-icons .webicon:hover {
\n    box-shadow: none;
\n    margin: 0;
\n}
\n
\n.builder-block-grid > ul > li.item .featured-image {
\n    display: block;
\n    margin-bottom: 0.8rem;
\n}
\n
\n.column-content,
\n.column-panels {
\n    padding-top: 2.625rem;
\n}
\n
\n.block-main img {
\n    height: auto !important;
\n}
\n
\n.page-header {
\n    padding: 1rem 0;
\n    background: transparent;
\n    color: #212121;
\n}
\n.page-header h1 {
\n    margin: 0;
\n    color: #212121;
\n}
\n
\n.post-title {
\n    margin-bottom: 1rem;
\n}
\n.post-title h1 {
\n    margin-bottom: 0;
\n}
\n
\n.inline-list > li > * {
\n    display: inline-block;
\n}
\n
\n.no-margin {
\n    margin: 0;
\n}
\n
\n.no-margin-top {
\n    margin-top: 0;
\n}
\n
\n.no-margin-right {
\n    margin-right: 0;
\n}
\n
\n.no-margin-bottom {
\n    margin-bottom: 0;
\n}
\n
\n.no-margin-left {
\n    margin-left: 0;
\n}
\n
\n.text-muted {
\n    color: #555555;
\n}
\n
\nul.tick {
\n    list-style: none;
\n    margin-left: 0;
\n}
\nul.tick li:before {
\n    font-family: \'fontAwesome\';
\n    float: left;
\n}
\nul.tick li {
\n    padding-left: 1.3rem;
\n}
\nul.tick li:before {
\n    content: \"\\f00c\";
\n    margin-left: -1.3rem;
\n}
\n
\nul.chevron {
\n    list-style: none;
\n    margin-left: 0;
\n}
\nul.chevron li:before {
\n    font-family: \'fontAwesome\';
\n    float: left;
\n}
\nul.chevron li {
\n    padding-left: 0.7rem;
\n}
\nul.chevron li:before {
\n    content: \"\\f054\";
\n    margin-left: -0.6rem;
\n}
\n
\nul.caret {
\n    list-style: none;
\n    margin-left: 0;
\n}
\nul.caret li:before {
\n    font-family: \'fontAwesome\';
\n    float: left;
\n}
\nul.caret li {
\n    padding-left: 0.7rem;
\n}
\nul.caret li:before {
\n    content: \"\\f0da\";
\n    margin-left: -0.6rem;
\n}
\n
\n.styled-list ul {
\n    list-style: none;
\n    margin-left: 0;
\n}
\n.styled-list ul li:before {
\n    font-family: \'fontAwesome\';
\n    float: left;
\n}
\n.styled-list ul li:before {
\n    font-family: \'fontAwesome\';
\n    float: left;
\n}
\n
\n.styled-list.chevron li {
\n    padding-left: 0.9rem;
\n}
\n
\n.styled-list.chevron li:before {
\n    content: \"\\f054\";
\n    margin-left: -0.8rem;
\n}
\n
\n.styled-list.caret li {
\n    padding-left: 0.9rem;
\n}
\n
\n.styled-list.caret li:before {
\n    content: \"\\f0da\";
\n    margin-left: -0.8rem;
\n}
\n
\n.styled-list.tick li {
\n    padding-left: 1.4rem;
\n}
\n
\n.styled-list.tick li:before {
\n    content: \"\\f00c\";
\n    margin-left: -1.4rem;
\n}
\n
\n\/\* Forms \*\/
\n.formrt ul {
\n    list-style: none;
\n    margin: 0;
\n}
\n
\n.formrt ul > li:before {
\n    display: none;
\n}
\n
\n.formrt li {
\n    display: flex;
\n}
\n
\n.formrt li > label {
\n    min-width: 8em
\n}
\n.formrt li > [type=\"checkbox\"] {
\n    flex: -1;
\n    margin: .5em;
\n}
\n
\n.panel-item .formrt li {
\n    display: unset;
\n}
\n
\n.panel-item .formrt input:not([type=\"checkbox\"]):not([type=\"radio\"]),
\n.panel-item .formrt select,
\n.panel-item .formrt button {
\n    width: 100%;
\n}
\n
\n.formrt textarea {
\n    height: 11.5rem;
\n}
\n
\n\/\**
\n * Tooltips!
\n \*\/
\n\/\* Base styles for the element that has a tooltip \*\/
\n[data-tooltip],
\n.tooltip {
\n    position: relative;
\n    cursor: pointer;
\n}
\n
\n\/\* Base styles for the entire tooltip \*\/
\n[data-tooltip]:before,
\n[data-tooltip]:after,
\n.tooltip:before,
\n.tooltip:after {
\n    position: absolute;
\n    visibility: hidden;
\n    -ms-filter: \"progid:DXImageTransform.Microsoft.Alpha(Opacity=0)\";
\n    filter: progid:DXImageTransform.Microsoft.Alpha(Opacity=0);
\n    opacity: 0;
\n    -webkit-transition: opacity 0.2s ease-in-out, visibility 0.2s ease-in-out, -webkit-transform 0.2s cubic-bezier(0.71, 1.7, 0.77, 1.24);
\n    -moz-transition: opacity 0.2s ease-in-out, visibility 0.2s ease-in-out, -moz-transform 0.2s cubic-bezier(0.71, 1.7, 0.77, 1.24);
\n    transition: opacity 0.2s ease-in-out, visibility 0.2s ease-in-out, transform 0.2s cubic-bezier(0.71, 1.7, 0.77, 1.24);
\n    -webkit-transform: translate3d(0, 0, 0);
\n    -moz-transform: translate3d(0, 0, 0);
\n    transform: translate3d(0, 0, 0);
\n    pointer-events: none;
\n}
\n
\n\/\* Show the entire tooltip on hover and focus \*\/
\n[data-tooltip]:hover:before,
\n[data-tooltip]:hover:after,
\n[data-tooltip]:focus:before,
\n[data-tooltip]:focus:after,
\n.tooltip:hover:before,
\n.tooltip:hover:after,
\n.tooltip:focus:before,
\n.tooltip:focus:after {
\n    visibility: visible;
\n    -ms-filter: \"progid:DXImageTransform.Microsoft.Alpha(Opacity=100)\";
\n    filter: progid:DXImageTransform.Microsoft.Alpha(Opacity=100);
\n    opacity: 1;
\n}
\n
\n\/\* Base styles for the tooltip\'s directional arrow \*\/
\n.tooltip:before,
\n[data-tooltip]:before {
\n    z-index: 1001;
\n    border: 6px solid transparent;
\n    background: transparent;
\n    content: \"\";
\n}
\n
\n\/\* Base styles for the tooltip\'s content area \*\/
\n.tooltip:after,
\n[data-tooltip]:after {
\n    z-index: 1000;
\n    padding: 8px;
\n    width: 160px;
\n    background-color: #000;
\n    background-color: rgba(51, 51, 51, 0.9);
\n    color: #fff;
\n    content: attr(data-tooltip);
\n    font-size: 14px;
\n    line-height: 1.2;
\n}
\n
\n\/\* Directions \*\/
\n\/\* Top (default) \*\/
\n[data-tooltip]:before,
\n[data-tooltip]:after,
\n.tooltip:before,
\n.tooltip:after,
\n.tooltip-top:before,
\n.tooltip-top:after {
\n    bottom: 100%;
\n    left: 50%;
\n}
\n
\n[data-tooltip]:before,
\n.tooltip:before,
\n.tooltip-top:before {
\n    margin-left: -6px;
\n    margin-bottom: -12px;
\n    border-top-color: #000;
\n    border-top-color: rgba(51, 51, 51, 0.9);
\n}
\n
\n\/\* Horizontally align top/bottom tooltips \*\/
\n[data-tooltip]:after,
\n.tooltip:after,
\n.tooltip-top:after {
\n    margin-left: -80px;
\n}
\n
\n[data-tooltip]:hover:before,
\n[data-tooltip]:hover:after,
\n[data-tooltip]:focus:before,
\n[data-tooltip]:focus:after,
\n.tooltip:hover:before,
\n.tooltip:hover:after,
\n.tooltip:focus:before,
\n.tooltip:focus:after,
\n.tooltip-top:hover:before,
\n.tooltip-top:hover:after,
\n.tooltip-top:focus:before,
\n.tooltip-top:focus:after {
\n    -webkit-transform: translateY(-12px);
\n    -moz-transform: translateY(-12px);
\n    transform: translateY(-12px);
\n}
\n
\n\/\* Left \*\/
\n.tooltip-left:before,
\n.tooltip-left:after {
\n    right: 100%;
\n    bottom: 50%;
\n    left: auto;
\n}
\n
\n.tooltip-left:before {
\n    margin-left: 0;
\n    margin-right: -12px;
\n    margin-bottom: 0;
\n    border-top-color: transparent;
\n    border-left-color: #000;
\n    border-left-color: rgba(51, 51, 51, 0.9);
\n}
\n
\n.tooltip-left:hover:before,
\n.tooltip-left:hover:after,
\n.tooltip-left:focus:before,
\n.tooltip-left:focus:after {
\n    -webkit-transform: translateX(-12px);
\n    -moz-transform: translateX(-12px);
\n    transform: translateX(-12px);
\n}
\n
\n\/\* Bottom \*\/
\n.tooltip-bottom:before,
\n.tooltip-bottom:after {
\n    top: 100%;
\n    bottom: auto;
\n    left: 50%;
\n}
\n
\n.tooltip-bottom:before {
\n    margin-top: -12px;
\n    margin-bottom: 0;
\n    border-top-color: transparent;
\n    border-bottom-color: #000;
\n    border-bottom-color: rgba(51, 51, 51, 0.9);
\n}
\n
\n.tooltip-bottom:hover:before,
\n.tooltip-bottom:hover:after,
\n.tooltip-bottom:focus:before,
\n.tooltip-bottom:focus:after {
\n    -webkit-transform: translateY(12px);
\n    -moz-transform: translateY(12px);
\n    transform: translateY(12px);
\n}
\n
\n\/\* Right \*\/
\n.tooltip-right:before,
\n.tooltip-right:after {
\n    bottom: 50%;
\n    left: 100%;
\n}
\n
\n.tooltip-right:before {
\n    margin-bottom: 0;
\n    margin-left: -12px;
\n    border-top-color: transparent;
\n    border-right-color: #000;
\n    border-right-color: rgba(51, 51, 51, 0.9);
\n}
\n
\n.tooltip-right:hover:before,
\n.tooltip-right:hover:after,
\n.tooltip-right:focus:before,
\n.tooltip-right:focus:after {
\n    -webkit-transform: translateX(12px);
\n    -moz-transform: translateX(12px);
\n    transform: translateX(12px);
\n}
\n
\n\/\* Move directional arrows down a bit for left/right tooltips \*\/
\n.tooltip-left:before,
\n.tooltip-right:before {
\n    top: 3px;
\n}
\n
\n\/\* Vertically center tooltip content for left/right tooltips \*\/
\n.tooltip-left:after,
\n.tooltip-right:after {
\n    margin-left: 0;
\n    margin-bottom: -16px;
\n}
\n
\n.chat-sticky {
\n    position: fixed;
\n    right: 0;
\n    top: 250px;
\n    z-index: 9999;
\n    background: #000000;
\n    padding: 0.2rem 0.8rem 0.2rem 1rem;
\n    color: #FFFFFF;
\n}
\n.chat-sticky ul {
\n    margin-bottom: 0;
\n}
\n.chat-sticky ul li {
\n    margin: 0.5rem 0;
\n}
\n.chat-sticky ul li a {
\n    display: block;
\n    color: #FFFFFF;
\n    font-size: 1.5rem;
\n}
\n
\narticle ul > li,
\n.footer-column ul > li {
\n    list-style: none;
\n    position: relative;
\n}
\narticle ul > li:before,
\n.footer-column ul > li:before {
\n    content: \"\\f101\";
\n    font-family: FontAwesome;
\n    position: absolute;
\n    left: -1rem;
\n}
\narticle ul {
\n    margin-bottom: 2rem;
\n    margin-left: 4.125rem;
\n}
\narticle ul li {
\n    margin-bottom: .6667em;
\n}
\narticle ul > li:before {
\n    left: -1.5rem;
\n}
\n
\n.button {
\n    font-size: .875rem;
\n    padding: .822em 1.75em;
\n}
\n
\narticle .button {
\n    min-width: 8rem;
\n}
\n
\n
\n.gray-band {
\n    background: #EEEEEE;
\n    padding: 2.2rem 0 1rem;
\n    margin-bottom: 2rem;
\n}
\n
\n.csat-widget {
\n    width: 100% !important;
\n    max-width: 100% !important;
\n}
\n
\nh1 > strong, h2 > strong, h4 > strong {
\n    font-weight: 300;
\n}
\n
\n.page-header h1 {
\n    margin: 0.6rem 0 1rem;
\n}
\n
\nh2 {
\n    margin: 1rem 0 0.5rem;
\n}
\n
\nh3 {
\n    font-size: 1.4rem;
\n    font-weight: 500;
\n    margin-top: 1rem;
\n}
\nh3 > strong {
\n    font-weight: 500;
\n}
\n
\nh5 {
\n    font-size: 1.125rem;
\n    font-weight: 400;
\n}
\n
\np.lead, .lead p {
\n    font-size: 1.15rem;
\n}
\n@media only screen and (min-width: 40.063em) {
\n    p.lead, .lead p {
\n        font-size: 1.25rem;
\n    }
\n}
\n
\n.post-meta-list li {
\n    margin-bottom: 0.5rem;
\n}
\n
\nstrong {
\n    font-weight: 600;
\n}
\n
\n.block-breadcrumbs {
\n    padding: 0.2rem 0;
\n    background-color: #F5F5F5;
\n}
\n.block-breadcrumbs .breadcrumbs {
\n    margin-bottom: 0;
\n}
\n.block-breadcrumbs .breadcrumbs li.current_item {
\n    color: #212121;
\n}
\n
\n.builder-list {
\n    list-style: none;
\n    margin-left: 0;
\n}
\n.builder-list li {
\n    margin-bottom: 0.5rem;
\n}
\n.builder-list li a {
\n    display: block;
\n    border: 3px solid #EEEEEE;
\n    padding: 0.2rem 1rem;
\n}
\n.builder-list li a:after {
\n    content: \"\";
\n    font-family: \'fontAwesome\';
\n    float: right;
\n    color: #212121;
\n    font-size: 1.2rem;
\n    position: relative;
\n    top: -3px;
\n}
\n
\n.builder-block-grid > ul > li.item .featured-image {
\n    margin-bottom: 0;
\n}
\n.builder-block-grid > ul > li.item .featured-image img {
\n    width: 100%;
\n    border: 2px solid #EEEEEE;
\n    border-bottom: none;
\n}
\n
\n.builder-block-grid > ul > li.item .caption {
\n    border: 2px solid #EEEEEE;
\n    padding: 0.5rem 1rem 1rem;
\n}
\n
\ndiv.testimonial-panel {
\n    background: none repeat scroll 0 0 white;
\n    border: 3px solid #ededed;
\n    margin-bottom: 2rem;
\n    padding: 2rem 1.2rem;
\n    position: relative;
\n    color: #212121;
\n}
\ndiv.testimonial-panel p {
\n    font-size: 1.2rem;
\n}
\ndiv.testimonial-panel p, div.testimonial-panel cite {
\n    color: #212121;
\n}
\ndiv.testimonial-panel:after,
\ndiv.testimonial-panel:before {
\n    top: 100%;
\n    left: 2em;
\n    border: solid transparent;
\n    content: \" \";
\n    height: 0;
\n    width: 0;
\n    position: absolute;
\n    pointer-events: none;
\n}
\ndiv.testimonial-panel:after {
\n    border-color: rgba(255, 255, 255, 0);
\n    border-top-color: #fff;
\n    border-width: 20px;
\n    margin-left: -20px;
\n}
\ndiv.testimonial-panel:before {
\n    border-color: rgba(238, 238, 238, 0);
\n    border-top-color: inherit;
\n    border-width: 24px;
\n    margin-left: -24px;
\n}
\ndiv.testimonial-panel p {
\n    padding-left: 1.25rem;
\n    font-style: italic;
\n}
\ndiv.testimonial-panel p:before {
\n    content: \"\\f10d\";
\n    font-family: \'fontAwesome\';
\n    color: #EEE;
\n    margin-right: 1rem;
\n    float: left;
\n    position: absolute;
\n    left: 1rem;
\n}
\ndiv.testimonial-panel p:after {
\n    content: \"\\f10e\";
\n    font-family: \'fontAwesome\';
\n    color: #EEE;
\n    margin-left: 1rem;
\n    float: right;
\n}
\n
\n.testimonial-block h4 {
\n    font-size: 1.125rem;
\n    margin-bottom: 0.1rem;
\n}
\n
\nblockquote.testimonial-panel {
\n    position: relative;
\n    background: #ffffff;
\n    border: 3px solid #eeeeee;
\n    padding: 2rem 1.2rem;
\n    position: relative;
\n    margin-bottom: 2rem;
\n}
\n@media only screen and (min-width: 40.063em) {
\n    blockquote.testimonial-panel {
\n        padding-left: 5rem;
\n    }
\n}
\nblockquote.testimonial-panel p {
\n    font-size: 1.2rem;
\n    font-style: italic;
\n}
\nblockquote.testimonial-panel p:before {
\n    content: \"\\f10d\";
\n    font-family: \'fontAwesome\';
\n    float: left;
\n    position: absolute;
\n    top: 7px;
\n    color: #EEEEEE;
\n}
\n@media only screen and (min-width: 40.063em) {
\n    blockquote.testimonial-panel p:before {
\n        top: 1.8rem;
\n        left: 1.1rem;
\n        font-size: 2.4rem;
\n    }
\n}
\n
\n.testimonial-panel:after, .testimonial-panel:before {
\n    top: 100%;
\n    left: 20%;
\n    border: solid transparent;
\n    content: \" \";
\n    height: 0;
\n    width: 0;
\n    position: absolute;
\n    pointer-events: none;
\n}
\n
\n.testimonial-panel:after {
\n    border-color: rgba(255, 255, 255, 0);
\n    border-top-color: #ffffff;
\n    border-width: 20px;
\n    margin-left: -20px;
\n}
\n
\n.testimonial-panel:before {
\n    border-color: rgba(238, 238, 238, 0);
\n    border-top-color: #eeeeee;
\n    border-width: 24px;
\n    margin-left: -24px;
\n}
\n
\n.testimonial-snippet {
\n    margin: 1.7rem 0;
\n}
\n
\n.panel-item {
\n    margin-bottom: 1rem;
\n    position: relative;
\n}
\n.panel-item.has_form {
\n    padding: 1rem;
\n}
\n
\n.panel-item-image img {
\n    display: block;
\n    width: 100%;
\n}
\n.panel-item-image:before {
\n    position: absolute;
\n    right: 0;
\n    bottom: 0;
\n    left: 0;
\n    height: 4rem;
\n    content: \'\';
\n}
\n.panel-item-overlay {
\n    background: no-repeat bottom right;
\n    position: absolute;
\n    top: 0;
\n    right: 0;
\n    bottom: 0;
\n    left: 0;
\n}
\n.panel-item-image ~ .panel-item-text {
\n    color: #fff;
\n    font-size: .875em;
\n    position: absolute;
\n    right: 0;
\n    bottom: 0;
\n    left: 0;
\n    padding: .3rem 1rem;
\n}
\n.panel-item-image ~ .panel-item-text * {
\n    line-height: 1.5;
\n    margin: 0;
\n}
\n
\n@media screen and (min-width: 1028px) {
\n    .column-content:not(:only-child) {
\n        width: 68.4%;
\n    }
\n    .column-content + .column-panels {
\n        width: 31.6%;
\n    }
\n}
\n
\n.category-filter-button {
\n    margin-top: 0.7rem;
\n}
\n@media only screen and (min-width: 64.063em) {
\n    .category-filter-button {
\n        float: right;
\n    }
\n}
\n
\n.ajax-loader-gif {
\n    text-align: center;
\n    width: 100%;
\n}
\n
\ntable tr th, table tr td {
\n    text-align: center;
\n}
\ntable tr th:first-child, table tr td:first-child {
\n    text-align: left;
\n}
\n
\ntable tr th {
\n    background-color: #AAAAAA;
\n    color: #FFFFFF;
\n    font-size: 1rem;
\n}
\n
\ntable .fa-check {
\n    color: #6db850;
\n}
\n
\ntable .fa-times {
\n    color: #ca5a4b;
\n}
\n
\ndl.tabs dd.active a {
\n    background-color: #EEEEEE;
\n}
\n
\ndl.tabs dd a {
\n    border: 1px solid #EEEEEE;
\n    background-color: #fff;
\n    margin-right: 1px;
\n    padding: 0.5rem 1rem;
\n}
\n
\n.tabs-content {
\n    background-color: #EEEEEE;
\n    padding: 0 1rem;
\n}
\n
\n.monolith-accordion {
\n    margin: 2rem 0;
\n}
\n.monolith-accordion dl dd.accordion-navigation a {
\n    margin-bottom: 2px;
\n    font-size: 1.4rem;
\n    font-weight: 500;
\n}
\n.monolith-accordion dl dd.accordion-navigation a:after {
\n    content: \"\\f078\";
\n    font-family: \'fontAwesome\';
\n    float: right;
\n}
\n.monolith-accordion dl dd.accordion-navigation.active a:after {
\n    content: \"\\f077\";
\n}
\n.monolith-accordion dl dd.accordion-navigation .content a {
\n    font-size: 1rem;
\n}
\n.monolith-accordion dl dd.accordion-navigation .content a:after {
\n    content: none !important;
\n}
\n
\n.horizontal-block-row {
\n    margin-bottom: 2rem;
\n}
\n@media only screen and (min-width: 64.063em) {
\n    .horizontal-block-row .left-grid-block {
\n        padding-right: 0px !important;
\n    }
\n    .horizontal-block-row .right-grid-block {
\n        padding-left: 0px !important;
\n    }
\n    .horizontal-block-row .inner-content-padding {
\n        height: 272px;
\n    }
\n}
\n.horizontal-block-row .inner-content-padding {
\n    border: 5px solid #EEEEEE;
\n    padding: 1.5rem;
\n}
\n
\n.banner {
\n    background-size: cover;
\n    background-position: center center;
\n    background-repeat: no-repeat;
\n    height: 200px;
\n    position: relative;
\n    overflow-x: hidden;
\n}
\n@media only screen and (min-width: 40.063em) {
\n    .banner {
\n        height: 280px;
\n    }
\n
\n    .banner .banner-left {
\n        background-repeat: no-repeat;
\n        background-position: left center;
\n        position: absolute;
\n        left: -290px;
\n        top: 0;
\n        height: 280px;
\n        width: 100%;
\n    }
\n    .banner .banner-right {
\n        background-repeat: no-repeat;
\n        background-position: right center;
\n        position: absolute;
\n        right: -530px;
\n        top: 0;
\n        height: 280px;
\n        width: 100%;
\n    }
\n}
\n@media only screen and (min-width: 64.063em) {
\n    .banner .banner-left {
\n        left: -150px;
\n    }
\n    .banner .banner-right {
\n        right: -360px;
\n    }
\n}
\n@media screen and (min-width: 64rem) {
\n    .banner-shadow {
\n        position: absolute;
\n        top: 0;
\n        right: 0;
\n        bottom: 0;
\n        left: 0;
\n        background: linear-gradient(to right, #fff 0%, #fff 50%, transparent 55%, transparent 100%);
\n    }
\n}
\n
\n.frontpage-banner {
\n    position: relative;
\n    height: auto;
\n    overflow-x: hidden;
\n    text-align: center;
\n}
\n.frontpage-banner h1 {
\n    font-size: 2.5rem;
\n    font-weight: 400;
\n}
\n.frontpage-banner .banner-image {
\n    background-position-x: right;
\n    background-repeat: no-repeat;
\n    background-size: auto 100%;
\n    max-width: 100%;
\n    width: auto;
\n    margin: 0 auto;
\n    height: 182px;
\n}
\n.frontpage-banner-caption {
\n    text-align: center;
\n}
\n
\n@media screen and (min-width: 767px) {
\n    .frontpage-banner .banner-image {
\n        height: 359px;
\n    }
\n
\n}
\n
\n@media only screen and (min-width: 64em) {
\n    .frontpage-banner-caption {
\n        text-align: left;
\n        position: absolute;
\n        top: 1rem;
\n        width: 100%;
\n        margin-top: 5rem;
\n    }
\n}
\n
\n@media only screen and (min-width: 120.063em) and (max-width: 99999999em) {
\n    .banner-left {
\n        left: 0;
\n        height: 100%;
\n        width: 100%;
\n        background-repeat: no-repeat;
\n        background-position: left center;
\n        position: absolute;
\n        top: 0;
\n        display: block;
\n    }
\n    .banner-right {
\n        right: 0;
\n        height: 100%;
\n        width: 100%;
\n        background-repeat: no-repeat;
\n        background-position: right center;
\n        position: absolute;
\n        top: 0;
\n    }
\n}
\n
\n@media only screen and (min-width: 90.063em) and (max-width: 120em) {
\n    .banner-left {
\n        left: -200px;
\n        background-image: url(../images/banner-left.png);
\n        height: 100%;
\n        width: 100%;
\n        background-repeat: no-repeat;
\n        background-position: left center;
\n        position: absolute;
\n        top: 0;
\n        display: block;
\n    }
\n    .banner-right {
\n        right: -200px;
\n        background-image: url(../images/banner-right.png);
\n        height: 100%;
\n        width: 100%;
\n        background-repeat: no-repeat;
\n        background-position: right center;
\n        position: absolute;
\n        top: 0;
\n    }
\n}
\n
\n@media only screen and (min-width: 64em) and (max-width: 90em) {
\n    .banner-left {
\n        left: -350px;
\n        background-image: url(../images/banner-left.png);
\n        height: 100%;
\n        width: 100%;
\n        background-repeat: no-repeat;
\n        background-position: left center;
\n        position: absolute;
\n        top: 0;
\n        display: block;
\n    }
\n    .banner-right {
\n        right: -350px;
\n        background-image: url(../images/banner-right.png);
\n        height: 100%;
\n        width: 100%;
\n        background-repeat: no-repeat;
\n        background-position: right center;
\n        position: absolute;
\n        top: 0;
\n    }
\n}
\n
\n@media only screen and (min-width: 640px) and (max-width: 1100px) {
\n    .banner-left {
\n        left: -375px;
\n        height: 100%;
\n        width: 100%;
\n        background-repeat: no-repeat;
\n        background-position: left center;
\n        position: absolute;
\n        top: 0;
\n        display: block;
\n    }
\n    .banner-right {
\n        right: -400px;
\n        height: 100%;
\n        width: 100%;
\n        background-repeat: no-repeat;
\n        background-position: right center;
\n        position: absolute;
\n        top: 0;
\n    }
\n    .frontpage-banner-caption {
\n        padding-left: 5.5rem;
\n    }
\n}
\n
\n.header-top-desktop-block,
\n.block-header .top-bar {
\n    background-color: #EDEDED;
\n}
\n
\n@media only screen and (max-width: 63.9375rem) {
\n    .banner-left {
\n        display: none;
\n    }
\n    .banner-right {
\n        display: none;
\n    }
\n
\n    .block-header .top-bar,
\n    .block-header .top-bar .name {
\n        height: 4rem;
\n    }
\n    .top-bar .toggle-topbar.menu-icon a {
\n        font-size: 2rem;
\n        padding: 0 .9375rem;
\n    }
\n    .top-bar.expanded {
\n        height: auto;
\n    }
\n}
\n
\n.top-bar .toggle-topbar.menu-icon a span:after {
\n    display: none;
\n}
\n
\n.block-header .top-bar .name,
\n.header-top-desktop-block > .row {
\n    -webkit-box-align: center;
\n    -ms-flex-align: center;
\n    align-items: center;
\n    display: -webkit-box;
\n    display: -ms-flexbox;
\n    display: flex;
\n}
\n
\n.header-top-desktop-block .desktop-logo {
\n    margin: 0.7rem 0;
\n}
\n.header-top-desktop-block .header-top-contact-list {
\n    margin: 0;
\n    padding: 0;
\n    font-size: 1rem;
\n    font-weight: 400;
\n}
\n.header-top-desktop-block .header-top-contact-list li {
\n    height: 5.125rem;
\n    line-height: 5.125rem;
\n    position: relative;
\n}
\n.header-top-desktop-block .header-top-contact-list li span.divider {
\n    display: inline-block;
\n    position: relative;
\n    left: -0.55rem;
\n    color: #EEEEEE;
\n}
\n.header-top-contact-list a:not(.button) {
\n    text-decoration: underline;
\n}
\n.header-top-contact-list a:hover {
\n    text-decoration: none;
\n}
\n.header-top-desktop-block .header-top-contact-list button {
\n    margin-bottom: 0;
\n}
\n
\n.header-top-contact-list .number-li + .number-li:before {
\n    content: \'|\';
\n    margin-right: 1em
\n}
\n.header-contact-button .button  {
\n    padding-left: 2.5em;
\n    position: relative;
\n}
\n.header-contact-button .button > .fa {
\n    position: absolute;
\n    left: .5em;
\n    font-size: 1.5em;
\n}
\n
\n.name img {
\n    margin-left: 1rem;
\n    max-width: calc(100% - 4rem);
\n}
\n
\n.top-bar-section li.active:not(.has-form) a:not(.button) {
\n    font-weight: bold;
\n}
\n
\n.mobile-telephone-block {
\n    padding: 0.5rem 0;
\n    background-color: #333333;
\n}
\n.mobile-telephone-block span {
\n    color: #FFFFFF !important;
\n    margin-right: 0.2rem;
\n    display: inline-block;
\n}
\n
\n@media only screen and (min-width: 63.37em) {
\n    .top-bar-section > ul {
\n        display: -webkit-flex;
\n        display: -moz-flex;
\n        display: flex;
\n        -webkit-flex-direction: row;
\n        -moz-flex-direction: row;
\n        flex-direction: row;
\n        width: 100%;
\n    }
\n    .top-bar-section > ul > li:not(.divider) {
\n        float: none;
\n        -webkit-flex: 1;
\n        -moz-flex: 1;
\n        flex: 1;
\n    }
\n    .top-bar-section > ul > li > a {
\n        white-space: nowrap;
\n        text-overflow: ellipsis;
\n        text-align: center;
\n        overflow: hidden;
\n    }
\n}
\n
\n@media screen and (max-width: 40rem) {
\n    .quick_contact {
\n        background: #444;
\n        font-size: 2rem;
\n        position: fixed;
\n        bottom: 0;
\n        left: 0;
\n        width: 100%;
\n        z-index: 10;
\n    }
\n
\n    .quick_contact > ul {
\n        display: -webkit-box;
\n        display: -ms-flexbox;
\n        display: flex;
\n        margin: 0;
\n    }
\n
\n    .quick_contact-item {
\n        color: #fff;
\n        -webkit-box-flex: 1;
\n        -ms-flex: 1;
\n        flex: 1;
\n        text-align: center;
\n    }
\n
\n    .quick_contact-item + .quick_contact-item {
\n        border-left: 1px solid #fff;
\n    }
\n
\n    .quick_contact-item > a {
\n        color: #fff;
\n        display: block;
\n        padding: .05em .5em;
\n        text-decoration: none;
\n        width: 100%;
\n    }
\n
\n    \/\* Put some space at the bottom of the page, to ensure the \"quick contact\" section
\n       does not cover anything when the user scrolls to the bottom of the screen. \*\/
\n    .wrapper {
\n        padding-bottom: 3.375rem;
\n    }
\n}
\n
\n.footer-cta-block {
\n    background-color: #EEEEEE;
\n    padding: 1.6rem;
\n    margin-top: 1rem;
\n}
\n.footer-cta-block h3 {
\n    margin: 0;
\n}
\n.footer-cta-block .button {
\n    margin: 1rem 0 0;
\n    display: block;
\n}
\n@media only screen and (min-width: 40.063em) {
\n    .footer-cta-block .button {
\n        display: inline-block;
\n    }
\n}
\n
\n.block-footer {
\n    background: #000000;
\n    color: #FFFFFF;
\n    font-size: .875rem;
\n    padding: 1rem 0;
\n}
\n
\n.no-bullet > li:before {
\n    display: none;
\n}
\n.contact-label:after {
\n    content: \':\';
\n}
\n.block-footer .footer-logos {
\n    margin: 1rem 0 2rem;
\n}
\n.block-footer .footer-menu {
\n    color: #FFF;
\n}
\n.block-footer .footer-menu .footer-list {
\n    font-size: 80% !important;
\n}
\n.block-footer .footer-menu a {
\n    color: #FFF;
\n}
\n.footer-columns {
\n    margin-bottom: 1rem;
\n}
\n.footer-copyright {
\n    border-top: 1px solid #fff;
\n    font-size: .75rem;
\n    margin-top: 1rem;
\n    padding-top: 1rem;
\n}
\n
\n.footer-social ul {
\n    display: inline;
\n    margin: 0 0 0 1em;
\n}
\n.footer-social .footer-social-item {
\n    margin: 0 0 0 .5em;
\n}
\n.footer-social-item a {
\n    border-radius: .25em;
\n    font-size: 1.5em;
\n    width: 1.5em;
\n    height: 1.5em;
\n    text-align: center;
\n}
\n.footer-social-item-facebook a {
\n    background: #385495;
\n}
\n.footer-social-item-twitter a {
\n    background: #30b0e3;
\n}
\n
\n.social-icons {
\n    margin-left: -8px;
\n}
\n
\n@media screen and (max-width: 639px) {
\n    .footer-follow {
\n        display: -webkit-box;
\n        display: -ms-flexbox;
\n        display: flex;
\n        -webkit-box-orient: vertical;
\n        -webkit-box-direction: normal;
\n        -ms-flex-direction: column;
\n        flex-direction: column;
\n    }
\n    .footer-social ul {
\n        margin: 0;
\n    }
\n    .footer-social .footer-social-item {
\n        margin: 0 2em 0 0;
\n        font-size: 1.5rem;
\n    }
\n    .footer-logo {
\n        margin: 1rem 1rem 0;
\n    }
\n    .footer-slogan {
\n        -webkit-box-ordinal-group: 2;
\n        -ms-flex-order: 1;
\n        order: 1;
\n    }
\n
\n}
\n
\n@media screen and (min-width: 640px) {
\n    .footer-social {
\n        -webkit-box-align: center;
\n        -ms-flex-align: center;
\n        align-items: center;
\n        display: -webkit-box;
\n        display: -ms-flexbox;
\n        display: flex;
\n    }
\n}
\n
\n@media only screen and (min-width: 40.063em) {
\n    .social-icons {
\n        float: right;
\n    }
\n}
\n
\n.footer-slogan {
\n    font-size: .75rem;
\n    margin-top: .75rem;
\n}
\n
\n@media screen and (min-width: 1024px) {
\n
\n    .footer-slogan {
\n        padding-right: 0;
\n    }
\n
\n    .footer-logo {
\n        padding-left: 0;
\n        padding-right: 0;
\n    }
\n}
\n
\n.reg-logos {
\n    margin-top: 0.5rem;
\n    margin-left: 0;
\n}
\n.reg-logos li {
\n    margin-left: 0;
\n}
\n@media only screen and (min-width: 40.063em) {
\n    .reg-logos {
\n        float: right;
\n    }
\n}
\n
\n.footer-kpi-block {
\n    overflow: hidden;
\n    padding: 1rem 0;
\n}
\n.footer-kpi-block h2 {
\n    margin-bottom: 2rem;
\n}
\n.footer-kpi-block li h3 {
\n    font-size: 2rem;
\n}
\n.footer-kpi-block li img {
\n    max-width: 65%;
\n}
\n.footer-kpi-block li p {
\n    font-size: 0.875rem;
\n}
\n
\n.sidebar .widget {
\n    margin-bottom: 2.5rem;
\n}
\n.sidebar .widget ul li a {
\n    margin-bottom: 0.5rem;
\n    padding-bottom: 0.5rem;
\n    border-bottom: 2px solid #EEEEEE;
\n    display: block;
\n    color: #212121;
\n}
\n
\n.sidebar .widget ul li a:after {
\n    content: \"\";
\n    font-family: \'fontAwesome\';
\n    float: right;
\n}
\n
\n.sidebar .kpi-widget p {
\n    margin: 0.6rem 0 0;
\n    line-height: 1rem;
\n}
\n
\n.sidebar .kpi-widget li {
\n    margin-bottom: 0.8rem;
\n}
\n
\n.sidebar-contact-widget {
\n    margin-bottom: 0;
\n    padding-bottom: 0;
\n}
\n.sidebar-contact-widget .button {
\n    width: 100%;
\n    margin-bottom: 0;
\n}
\n
\n
\n.featured-services ul {
\n    display: -webkit-box;
\n    display: -ms-flexbox;
\n    display: flex;
\n    -ms-flex-wrap: wrap;
\n    flex-wrap: wrap;
\n}
\n
\n.feature-block a {
\n    height: 100%;
\n}
\n
\n.feature-block.feature-block {
\n    padding-left: .735rem;
\n    padding-right: .735rem;
\n}
\n.feature-block h3 {
\n    color: #212121;
\n    font-size: 1rem;
\n    min-height: 4em;
\n    padding: 0.7rem 0;
\n    margin: 0;
\n}
\n.feature-block h3:after {
\n    content: \"\";
\n    font-family: \'fontAwesome\';
\n    margin-left: 0.5rem;
\n    position: relative;
\n    top: 0.05rem;
\n}
\n
\n.feature-block > a {
\n    box-shadow: 2px 2px 2px #CCC;
\n    display: block;
\n    position: relative;
\n    overflow: hidden;
\n    background: #FFFFFF;
\n}
\n.feature-block > a .feature-block-description {
\n    position: absolute;
\n    display: none;
\n    transition: linear 0.2s;
\n}
\n.feature-block > a:hover .feature-block-description {
\n    display: block;
\n    top: 0;
\n    bottom: 0;
\n    left: 0;
\n    right: 0;
\n    color: white;
\n}
\n.feature-block > a:hover .feature-block-description .inner-content {
\n    position: relative;
\n    height: 100%;
\n    padding: 1rem;
\n}
\n.feature-block > a:hover .feature-block-description .inner-content span {
\n    display: block;
\n    background: #333333;
\n    color: #FFFFFF;
\n    position: absolute;
\n    bottom: 0;
\n    left: 0;
\n    right: 0;
\n    padding: 0.5rem;
\n}
\n
\n@media only screen and (min-width: 40.063rem) and (max-width: 57.5rem) {
\n    .feature-block-icon img {
\n        max-height: 130px;
\n    }
\n    .feature-block h3 {
\n        min-height: 5em;
\n    }
\n}
\n
\n.client-block {
\n    overflow: hidden;
\n    background: #EEE;
\n    padding: 2rem 0;
\n}
\n
\n.partners {
\n    display: -webkit-box;
\n    display: -ms-flexbox;
\n    display: flex;
\n    -ms-flex-wrap: wrap;
\n    flex-wrap: wrap;
\n    -webkit-box-pack: justify;
\n    -ms-flex-pack: justify;
\n    justify-content: space-between;
\n    list-style: none;
\n    margin: 0;
\n    padding: 0 1rem;
\n    width: 100%;
\n}
\n
\n.front-page-slider {
\n    margin: 2rem 0;
\n}
\n
\n.layout-home .orbit-slides-container li {
\n    background: #333333;
\n}
\n@media only screen and (min-width: 64.063em) {
\n    .layout-home .orbit-slides-container li img {
\n        position: relative;
\n        right: -1px;
\n    }
\n}
\n
\n.slide-caption {
\n    color: #FFFFFF;
\n    padding: 2rem;
\n}
\n@media only screen and (min-width: 64.063em) {
\n    .slide-caption {
\n        padding: 1.5rem 2rem 1.5rem 3rem;
\n    }
\n}
\n.slide-caption h3 {
\n    color: #FFFFFF;
\n    font-size: 1.875rem;
\n    margin-bottom: 1rem;
\n}
\n.slide-caption p {
\n    margin-bottom: 2rem;
\n    font-size: 1.1rem;
\n}
\n.slide-caption a.button {
\n    margin-bottom: 0;
\n}
\n
\n.orbit-container .orbit-prev, .orbit-container .orbit-next {
\n    background-color: rgba(0, 0, 0, 0.8);
\n    height: 50px;
\n    width: 30px;
\n    top: 40%;
\n}
\n
\n.orbit-bullets li {
\n    border-radius: 0;
\n    background: #AAAAAA;
\n}
\n
\n.contact-form {
\n    margin: 1.5rem 0;
\n}
\n.contact-form h3 {
\n    margin-bottom: 2rem;
\n}
\n.contact-form br {
\n    display: none;
\n}
\n
\n.contact-panel {
\n    padding: 0;
\n}
\n.contact-panel .location-address {
\n    padding: 1rem;
\n}
\n
\n\/\* Css Map Fix\*\/
\n.contact-panel .location-map img {
\n    max-width: none;
\n}
\n
\n\/\* Css Map Fix\*\/
\n.contact-panel .location-map label {
\n    width: auto;
\n    display: inline;
\n}
\n
\n.client-support-block .panel {
\n    padding-top: 0;
\n}
\n.client-support-block .panel .button {
\n    margin-bottom: 0;
\n}
\n.client-support-block .panel h2 {
\n    margin-bottom: 1rem;
\n}
\n.client-support-block .panel p {
\n    margin-bottom: 1rem;
\n}
\n@media only screen and (min-width: 64.063em) {
\n    .client-support-block .panel {
\n        min-height: 380px;
\n    }
\n}
\n
\n.single-team .webicon {
\n    margin-bottom: 0;
\n}
\n.single-team .webicon:hover {
\n    margin: 0;
\n}
\n
\n.single-team h2 {
\n    margin-top: 0;
\n}
\n
\n@media only screen and (min-width: 64.063em) {
\n    .single-team .panel-gray h2 {
\n        margin-top: 0;
\n    }
\n}
\n
\n.team-grid {
\n    margin: 0;
\n}
\n.team-grid li.feature-block {
\n    width: 100%;
\n}
\n
\n@media only screen and (min-width:40.063em) {
\n    .team-grid li.feature-block {
\n        width: 32%;
\n        float: left;
\n        margin: 0 1% 1% 0;
\n    }
\n}
\n.team-grid li.feature-block.mix {
\n    display: none;
\n}
\n.team-grid li.feature-block a h3 {
\n    font-size: 1.5rem;
\n    margin-bottom: 0;
\n    padding: 0;
\n}
\n.team-grid li.feature-block a p {
\n    margin-bottom: 0.5rem;
\n    color: #212121;
\n}
\n.team-grid li.feature-block a:hover .feature-block-description .inner-content span {
\n    padding: 4.35rem 0;
\n    text-align: center;
\n    height: 165px;
\n}
\n.team-grid li.feature-block a .feature-block-description .inner-content {
\n    padding-top: 3rem;
\n    color: #FFFFFF;
\n}
\n.team-grid li.feature-block a .feature-block-description .inner-content h3 {
\n    color: #FFFFFF;
\n}
\n.team-grid li.feature-block a .feature-block-content {
\n    border: 2px solid #EEEEEE;
\n    padding: 1rem;
\n    height: 165px;
\n}
\n@media only screen and (min-width: 64.063em) {
\n    .team-grid li.feature-block a .feature-block-content {
\n        height: 165px;
\n    }
\n}
\n
\n@media only screen and (min-width: 64.063em) {
\n    .team-thumbnail {
\n        float: right;
\n        margin: 0 0 1rem 1rem;
\n        width: 40%;
\n    }
\n}
\n
\n.team-social-list li p {
\n    margin-bottom: 0;
\n    margin-top: 10px;
\n}
\n
\n.panel.panel-advice {
\n    padding: 1rem;
\n}
\n.panel.panel-advice h2 {
\n    margin-top: 0;
\n}
\n
\n@media only screen and (min-width:64em) {
\n    .panel.panel-advice {
\n        padding: 2rem;
\n        background-position: 19.375rem top;
\n        background-size: 28.125rem;
\n        background-repeat: no-repeat;
\n    }
\n}
\n.panel.panel-advice ul.list-unstyled.chevron li {
\n    margin-bottom: 1rem;
\n}
\n
\n.panel.panel-advice ul.list-unstyled.chevron li a {
\n    font-weight: 500;
\n    font-size: 1.1rem;
\n    display: block;
\n}
\n
\n@media only screen and (min-width:40.063em) {
\n    .page-template-page-fullwidth .panel.panel-advice, .page-template-page-banner-fullwidth .panel.panel-advice {
\n        padding: 2rem;
\n        background-position: right top;
\n        background-size: 18.75rem;
\n        background-repeat: no-repeat;
\n    }
\n}
\n
\n@media only screen and (min-width:64em) {
\n    .page-template-page-fullwidth .panel.panel-advice, .page-template-page-banner-fullwidth .panel.panel-advice {
\n        background-size: 21.875rem;
\n    }
\n}'
WHERE
  `stub` = '03';;


UPDATE
  `engine_site_themes`
SET
  `date_modified` = CURRENT_TIMESTAMP,
  `modified_by`   = (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
  `styles` = '@import url(\'https://fonts.googleapis.com/css?family=Roboto:300,300i,400,400i,500,500i,700,700i\');
\nbody {
\n    font-family: Roboto, \"Helvetica Neue\", Helvetica, Arial, sans-serif;
\n}
\n
\n\/\* Error bubbles \*\/
\n.formError .formErrorContent,
\n.formError .formErrorArrow div  {
\n    background-color: $base_color_1;
\n}
\n
\n\/\* Links \*\/
\na,
\na:link {
\n    color: $link_color;
\n}
\n
\na:visited {
\n    color: $link_visited_color;
\n}
\n
\na:hover,
\na:focus {
\n    color: $link_hover_color;
\n}
\n
\n\/\* Alerts \*\/
\n.alert-box {
\n    background-color: $base_color_1;
\n    border-color: scale_color($base_color_1, -.3);
\n}
\n
\n.alert-box.success {
\n    background-color: #6db850;
\n    border-color: scale_color(#6db850, -.3);
\n}
\n
\n.alert-box.alert {
\n    background-color: #ca5a4b;
\n    border-color: scale_color(#ca5a4b, -.3);
\n}
\n
\n.alert-box.secondary {
\n    background-color: #000000;
\n    border-color: black;
\n}
\n
\n.alert-box.warning {
\n    background-color: #e5b13b;
\n    border-color: #dba11d;
\n}
\n
\n.alert-box.info {
\n    background-color: #677bde;
\n    border-color: #425bd6;
\n}
\n
\n\/\* Breadcrumbs \*\/
\n.breadcrumbs > *,
\n.breadcrumbs > * a,
\n.breadcrumbs > *:before {
\n    color: $base_color_1;
\n}
\n
\n\/\* Buttons \*\/
\nbutton,
\n.button {
\n    background-color: $base_color_1;
\n    border-color: #ff6f00;
\n    color: #fff;
\n}
\n
\na.button {
\n    color: #fff;
\n}
\n
\nbutton:hover, button:focus, .button:hover, .button:focus {
\n    background-color: #ff6f00;
\n}
\n
\nbutton.secondary, .button.secondary {
\n    background-color: #000000;
\n    border-color: #AAAAAA;
\n}
\n
\nbutton.secondary:hover, button.secondary:focus, .button.secondary:hover, .button.secondary:focus {
\n    background-color: black;
\n}
\n
\nbutton.success, .button.success {
\n    background-color: #6db850;
\n    border-color: #56963d;
\n}
\n
\nbutton.alert, .button.alert {
\n    background-color: #ca5a4b;
\n    border-color: #ab4132;
\n}
\n
\nbutton.alert:hover, button.alert:focus, .button.alert:hover, .button.alert:focus {
\n    background-color: #ab4132;
\n}
\n
\nbutton.warning, .button.warning {
\n    background-color: #e5b13b;
\n    border-color: #cb951b;
\n}
\n
\nbutton.warning:hover, button.warning:focus, .button.warning:hover, .button.warning:focus {
\n    background-color: #cb951b;
\n}
\n
\nbutton.info, .button.info {
\n    background-color: #677bde;
\n    border-color: #324dd2;
\n}
\n
\nbutton.info:hover, button.info:focus, .button.info:hover, .button.info:focus {
\n    background-color: #324dd2;
\n}
\n
\n\/\* Labels \*\/
\n.label {
\n    background-color: $base_color_1;
\n}
\n
\n.label.alert {
\n    background-color: #ca5a4b;
\n}
\n
\n.label.warning {
\n    background-color: #e5b13b;
\n}
\n
\n.label.success {
\n    background-color: #6db850;
\n}
\n
\n.label.secondary {
\n    background-color: #000000;
\n}
\n
\n.label.info {
\n    background-color: #677bde;
\n}
\n
\n\/\* Pagination \*\/
\nul.pagination li.current a,
\nul.pagination li.current button,
\nul.pagination li.current a:hover,
\nul.pagination li.current a:focus,
\nul.pagination li.current button:hover,
\nul.pagination li.current button:focus {
\n    background: $base_color_1;
\n}
\n
\n\/\* Top bar \*\/
\n.top-bar {
\n    background: #212121;
\n}
\n
\n.top-bar,
\n.top-bar .toggle-topbar.menu-icon a,
\n.header-top-contact-list a:not(.button) {
\n    color: #2D7B31;
\n}
\n
\n.top-bar .toggle-topbar.menu-icon a span::after {
\n    box-shadow: 0 0 0 1px #2D7B31, 0 7px 0 1px #2D7B31, 0 14px 0 1px #2D7B31;
\n}
\n
\n
\n.top-bar-section ul li.hide-for-large-up > a {
\n    background-color: $base_color_1;
\n}
\n
\n@media only screen and (min-width: 64em) {
\n    .top-bar-section li.active:not(.has-form) a:not(.button),
\n    .no-js .top-bar-section ul li:active > a{
\n        background: $base_color_1;
\n    }
\n
\n    .top-bar-section li.active:not(.has-form) a:not(.button):hover {
\n        background: #d18100;
\n
\n    }
\n}
\n
\n@media only screen and (min-width: 63.37em) {
\n    .top-bar-section > ul > li > a:hover {
\n        color: $base_color_1 !important;
\n    }
\n}
\n
\n.top-bar-section li.active:not(.has-form) a:not(.button) {
\n    background: #212121;
\n    color: $base_color_1;
\n}
\n
\n\/\* Lists \*\/
\nul.tick li:before {
\n    color: #6db850;
\n}
\n
\n.styled-list.chevron li:before,
\n.styled-list.caret li:before,
\n.styled-list.tick li:before {
\n    color: $base_color_1;
\n}
\n
\narticle ul > li:before {
\n    color: $base_color_2;
\n}
\n
\n\/\* Misc \*\/
\nblockquote {
\n    border-left-color: $base_color_1;
\n}
\n
\n.chat-sticky ul li a:hover {
\n    color: $base_color_1;
\n}
\n
\n.testimonial-block h4 {
\n    color: $base_color_1;
\n}
\n
\n.panel-item.has_form {
\n    background: #438329;
\n    color: #FFF;
\n}
\n
\n.panel-item-image:before {
\n    background: $base_color_2;
\n}
\n
\n@media screen and (max-width: 40rem) {
\n    .quick_contact {
\n        background: #448429;
\n    }
\n}
\n
\n.block-footer {
\n    background: #000000;
\n    color: #FFFFFF;
\n}
\n
\n.sidebar .widget ul li.current-cat a,
\n.sidebar .widget ul li.current_page_item a,
\n.sidebar .widget ul li a:hover {
\n    color: $base_color_1;
\n}
\n
\n.widget-widget_monolith_relative_pages_widget ul li.current_page_item a {
\n    color: $base_color_1;
\n}
\n
\n.feature-block > a .feature-block-description {
\n  color: $base_color_1;
\n}
\n
\n.orbit-bullets li.active {
\n    background: $base_color_1;
\n}
\n
\n.team-filtering li a.active {
\n    background-color: #c27800;
\n}
\n
\n.panel.panel-advice {
\n    border-color: $base_color_1;
\n}
\n
\n.panel.panel-advice ul.list-unstyled.chevron li:before {
\n    color: $base_color_1;
\n}'
WHERE
  `stub` = '30';;

/* Add the "03" template "content" layout, if it does not already exist */
INSERT INTO
  `plugin_pages_layouts` (`layout`, `template_id`, `use_db_source`, `date_created`, `date_modified`, `created_by`, `modified_by`, `source`)
  SELECT
    'content',
    (SELECT IFNULL(`id`, '') FROM `engine_site_templates` WHERE `stub` = '03' LIMIT 1),
    1,
    CURRENT_TIMESTAMP,
    CURRENT_TIMESTAMP,
    (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
    (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
    '<?php $side_panels = $panel_model->get_panels(\'content_right\', ($settings_instance->get(\'localisation_content_active\') == \'1\')); ?>
\n
\n<div class=\"row\">
\n    <div class=\"column-content columns <?= count($side_panels) ? \'medium-7 large-8\' : \'medium-12\' ?>\">
\n        <article>
\n            <section class=\"entry-content\"><?= $page_data[\'content\'] ?></section>
\n        </article>
\n    </div>
\n
\n    <div class=\"column-panels columns medium-5 large-4\">
\n        <?= Model_Panels::render(\'Get a Quick Quote\') ?>
\n    </div>
\n</div>'
  FROM `plugin_pages_layouts`
    WHERE NOT EXISTS (SELECT * FROM `plugin_pages_layouts` WHERE `layout` = 'content' AND `template_id` = (SELECT `id` FROM `engine_site_templates` WHERE `stub` = '30' LIMIT 1) AND `deleted` = 0)
    LIMIT 1
;;

/* Add the "03" template "home" layout, if it does not already exist */
INSERT INTO
  `plugin_pages_layouts` (`layout`, `template_id`, `use_db_source`, `date_created`, `date_modified`, `created_by`, `modified_by`, `source`)
  SELECT
    'home',
    (SELECT IFNULL(`id`, '') FROM `engine_site_templates` WHERE `stub` = '03' LIMIT 1),
    1,
    CURRENT_TIMESTAMP,
    CURRENT_TIMESTAMP,
    (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
    (SELECT IFNULL(`id`, '') FROM `engine_users` WHERE `email` = 'super@ideabubble.ie' LIMIT 1),
    '<?php $feature_panels = $panel_model->get_panels(\'home_content\', ($settings_instance->get(\'localisation_content_active\') == \'1\')); ?>
\n<?php if (count($feature_panels)): ?>
\n    <section class=\"gray-band\">
\n        <div class=\"featured-services\">
\n            <div class=\"row\">
\n                <div class=\"small-12 columns\">
\n                    <ul class=\"small-block-grid-2 medium-block-grid-4 large-block-grid-4 text-center\">
\n                        <?php foreach ($feature_panels as $feature): ?>
\n                            <li class=\"feature-block\">
\n                                <a<?= $feature[\'link_url\'] ? \' href=\"\'.$feature[\'link_url\'].\'\"\' : \'\' ?>>
\n                                    <div class=\"feature-block-icon\">
\n                                        <img height=\"150\" src=\"<?= $media_path ?>panels/<?= $feature[\'image\'] ?>\" class=\"attachment-thumbnail size-thumbnail\" alt=\"\" />
\n                                    </div>
\n
\n                                    <h3><?= $feature[\'title\'] ?></h3>
\n                                </a>
\n                            </li>
\n                        <?php endforeach; ?>
\n                    </ul>
\n                </div>
\n            </div>
\n        </div>
\n    </section>
\n<?php endif; ?>
\n
\n<div class=\"row\">
\n    <div class=\"columns medium-8 large-9\">
\n        <article>
\n            <section class=\"entry-content\"><?= $page_data[\'content\'] ?></section>
\n        </article>
\n    </div>
\n
\n    <?php $testimonials = Model_Testimonials::get_all_items_front_end(null, \'testimonials\'); ?>
\n
\n    <?php if (isset($testimonials[0])): ?>
\n        <?php $testimonial = $testimonials[0]; ?>
\n        <div class=\"columns medium-4 large-3\">
\n            <div class=\"testimonial-block\">
\n                <div class=\"panel testimonial-panel\"><i><?= $testimonial[\'summary\'] ?></i></div>
\n
\n                <h4><?= $testimonial[\'item_signature\'] ?></h4>
\n
\n                <p><?= $testimonial[\'item_company\'] ?></p>
\n
\n                <a class=\"button\" href=\"/testimonials\"><?= __(\'See more testimonials\') ?></a>
\n            </div>
\n        </div>
\n    <?php endif; ?>
\n</div>'
  FROM `plugin_pages_layouts`
    WHERE NOT EXISTS (SELECT * FROM `plugin_pages_layouts` WHERE `layout` = 'home' AND `template_id` = (SELECT `id` FROM `engine_site_templates` WHERE `stub` = '30' LIMIT 1) AND `deleted` = 0)
    LIMIT 1
;;


ALTER TABLE `engine_site_templates`
  CHANGE COLUMN `header` `header` MEDIUMBLOB NULL DEFAULT NULL,
  CHANGE COLUMN `styles` `styles` MEDIUMBLOB NULL DEFAULT NULL,
  CHANGE COLUMN `footer` `footer` MEDIUMBLOB NULL DEFAULT NULL;;
