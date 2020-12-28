<?php
$assets_folder_path      = Kohana::$config->load('config')->assets_folder_path;
$assets_folder_code_path = PROJECTPATH.'www/assets/'.$assets_folder_path;
$project_media_folder    = Kohana::$config->load('config')->project_media_folder;
$media_model             = new Model_Media();
$media_path              = Model_Media::get_path_to_media_item_admin($project_media_folder, '', '');
$settings_instance       = Settings::instance();
$panel_model             = new Model_Panels();
?><!DOCTYPE html>
<html class="no-js" lang="en" >
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= $page_data['title'] ?></title>

        <meta name="description" content="<?= $page_data['seo_description'] ?>"/>
        <link rel="canonical" href="<?= str_replace('.html.html', '.html', URL::base().$page_data['name_tag'].'.html') ?>" />
        <meta property="og:locale" content="en_GB" />
        <meta property="og:type" content="website" />
        <meta property="og:title" content="<?= $page_data['title'] ?>" />
        <meta property="og:description" content="<?= $page_data['seo_description'] ?>" />
        <meta property="og:url" content="<?= str_replace('.html.html', '.html', URL::base().'/'.$page_data['name_tag'].'.html') ?>" />
        <meta property="og:site_name" content="<?= $settings_instance->get('company_name') ?>" />

        <link rel="dns-prefetch" href="//fonts.googleapis.com" />

        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="<?= URL::get_engine_assets_base() ?>js/libs/jquery-1.12.4.min.js"><\/script>')</script>

        <?php if ($media_model->is_filename_used('favicon.ico', 'content')): ?>
            <link rel="shortcut icon" href="<?= $media_path ?>content/favicon.ico" type="image/ico" />
        <?php endif; ?>

        <link rel="stylesheet" href="/assets/shared/css/browserorg/style.css" />
        <link rel="stylesheet" href="/assets/<?= $assets_folder_path ?>/css/styles.css<?= file_exists($assets_folder_code_path.'/css/styles.css') ? '?ts='.filemtime($assets_folder_code_path.'/css/styles.css') : '' ?>" type="text/css" media="all" id="main-css" />
        <?php if (trim($theme->styles)): ?>
            <link rel="stylesheet" href="<?= $theme->get_url() ?>" />
        <?php endif; ?>
        <?php if (Settings::instance()->get('cookie_enabled') === 'TRUE'): ?>
            <!-- Cookie consent plugin by Silktide - http://silktide.com/cookieconsent -->
            <script type="text/javascript">
                <?php
                $cookie_message      = Settings::instance()->get('cookie_text');
                $cookie_dismiss_text = Settings::instance()->get('hide_notice_message');
                $cookie_link         = Settings::instance()->get('cookie_page');
                $cookie_link_text    = Settings::instance()->get('link_text');
                $cookie_message      = $cookie_message      ? $cookie_message      : 'This website uses cookies to ensure you get the best experience on our website';
                $cookie_dismiss_text = $cookie_dismiss_text ? $cookie_dismiss_text : 'Got it!';
                $cookie_link_text    = $cookie_link_text    ? $cookie_link_text    : 'More info';
                $cookie_consent_options = array(
                    "message" => $cookie_message,
                    "dismiss" => $cookie_dismiss_text,
                    "learnMore" => $cookie_link_text,
                    "link" => $cookie_link ? Model_Pages::get_page_by_id($cookie_link) : null,
                    "theme" => "dark-bottom"
                );
                ?>
                window.cookieconsent_options = <?=json_encode($cookie_consent_options)?>; // use proper js encoding to handle special characters ' " \ / etc...
            </script>
            <script src="<?= URL::site() ?>assets/shared/js/cookieconsent/cookieconsent.min.js"></script>
        <?php endif; ?>
    </head>

    <body class="layout-<?= $page_data['layout'] ?><?= ( ! empty($page_data['banner_slides'])) ? 'has_banner' : '' ?>">
        <div class="wrapper">
            <div class="header-top-desktop-block show-for-large-up">
                <div class="row">
                    <div class="columns medium-4">
                        <a href="/">
                            <img class="desktop-logo" src="<?= $page_data['logo'] ?>" class="logo-image" alt="" title="Homepage" />
                        </a>
                    </div>

                    <div class="columns medium-8">
                        <ul class="inline-list right header-top-contact-list">
                            <?php if (trim($settings_instance->get('telephone'))): ?>
                                <li class="number-li">
                                    <span>Tel:</span>
                                    <a href="tel:<?= trim($settings_instance->get('telephone')) ?>"><?= $settings_instance->get('telephone') ?></a>
                                </li>
                            <?php endif; ?>

                            <?php if (trim($settings_instance->get('email'))): ?>
                                <li class="number-li">
                                    <span>Email:</span>
                                    <a href="mailto:<?= trim($settings_instance->get('email')) ?>"><?= $settings_instance->get('email') ?></a>
                                </li>
                            <?php endif; ?>

                            <?php if ($settings_instance->get('show_need_help_button')): ?>
                                <li class="header-contact-button">
                                    <a class="button primary" href="/<?= Model_Pages::get_page_by_id($settings_instance->get('need_help_page')) ?>">
                                        <span class="fa fa-phone"></span>
                                        Request a callback
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
            </div>

            <header class="block-header top-bar-container contain-to-grid">
                <nav class="top-bar" data-topbar role="navigation" data-options="is_hover: true; mobile_show_parent_link: true;">
                    <ul class="title-area hide-for-large-up">
                        <li class="name">
                            <a href="/">
                                <img class="desktop-logo" src="<?= $page_data['logo'] ?>" alt="" />
                            </a>
                        </li>

                        <li class="toggle-topbar menu-icon">
                            <a href=""><span class="fa fa-bars"></span></a>
                        </li>
                    </ul>

                    <section class="top-bar-section main-navigation">
                        <?php $main_menu = Menuhelper::get_all_published_menus('main'); ?>

                        <ul id="menu-main-menu" class="top-bar-menu left">
                            <?php foreach ($main_menu as $level1): ?>
                                <?php if ($level1['level'] == 1): ?>
                                    <?php
                                    $link = menuhelper::get_link($level1);
                                    $active_link = ($page_data['name_tag'] == substr($link, strrpos($link, '/') + 1));
                                    ?>
                                    <li class="divider"></li>
                                    <li class="menu-item menu-item-type-post_type menu-item-object-page<?= ($level1['has_sub']) ? ' has-dropdown' : '' ?><?= $active_link ? ' active' : '' ?>">
                                        <a href="<?= $link ?>">
                                            <?= $level1['title'] ?>
                                        </a>

                                        <?php if ($level1['has_sub']): ?>
                                            <ul class="sub-menu dropdown">
                                                <?php foreach ($main_menu as $level2): ?>
                                                    <?php if ($level2['level'] == 2 && $level2['parent_id'] == $level1['id']): ?>
                                                        <?php
                                                        $link = menuhelper::get_link($level2);
                                                        $active_link = ($page_data['name_tag'] == substr($link, strrpos($link, '/') + 1));
                                                        ?>
                                                        <li class="menu-item menu-item-type-post_type menu-item-object-page<?= $active_link ? ' active' : '' ?>">
                                                            <a href="<?= $link ?>">
                                                                <?= $level2['title'] ?>
                                                            </a>
                                                        </li>
                                                    <?php endif; ?>
                                                <?php endforeach; ?>
                                            </ul>
                                        <?php endif; ?>
                                    </li>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        </ul>
                    </section>
                </nav>
            </header>

            <div class="quick_contact hide-for-medium-up">
                <ul class="no-bullet">
                    <?php if (trim($settings_instance->get('telephone'))): ?>
                        <li class="quick_contact-item">
                            <a onclick="__gaTracker('send', 'event', 'Quick contact', 'Call Us');"  href="tel:<?= str_replace(' ', '', $settings_instance->get('telephone')) ?>">
                                <span class="sr-only"><?= __('Phone') ?></span>
                                <span class="fa fa-phone"></span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <?php if (trim($settings_instance->get('email'))): ?>
                        <li onclick="__gaTracker('send', 'event', 'Quick contact', 'Email');"  class="quick_contact-item">
                            <a href="mailto:<?= str_replace(' ', '', $settings_instance->get('email')) ?>">
                                <span class="sr-only"><?= __('Email') ?></span>
                                <span class="fa fa-envelope"></span>
                            </a>
                        </li>
                    <?php endif; ?>
                    <li class="quick_contact-item">
                        <a href="/contact-us.html#content_start">
                            <span class="sr-only"><?= __('Location') ?></span>
                            <span class="fa fa-map-marker"></span>
                        </a>
                    </li>
                </ul>
            </div>

            <main class="block-main" role="main">
                <?php if (trim($settings_instance->get('telephone')) || trim($settings_instance->get('email'))): ?>
                    <div class="chat-sticky show-for-medium-up">
                        <ul class="no-bullet">
                            <?php if (trim($settings_instance->get('telephone'))): ?>
                                <li>
                                    <a onclick="__gaTracker('send', 'event', 'Sidebar', 'Call Us');" href="tel:<?= trim($settings_instance->get('telephone')) ?>" class="tooltip-left" data-tooltip="Call us">
                                        <span class="fa fa-phone"></span>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if (trim($settings_instance->get('email'))): ?>
                                <li>
                                    <a onclick="__gaTracker('send', 'event', 'Sidebar', 'Email');" href="mailto:<?= trim($settings_instance->get('email')) ?>" class="tooltip-left" data-tooltip="Email us">
                                        <span class="fa fa-envelope"></span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                <?php endif;?>

                <?php if ( ! empty($page_data['banner_slides']) OR ( ! empty($page_data['banner_image']))): ?>
                    <div class="frontpage-banner">

                        <?php if ( ! empty($page_data['banner_slides'])): ?>
                            <?php // dynamic / custom banner ?>

                            <?php
                            $slides = isset($banner_items) ? $banner_items : (isset($page_data['banner_slides']) ? $page_data['banner_slides'] : array());
                            $number_of_slides = count($slides);
                            ?>

                            <div class="swiper-container" id="page-banner-swiper"
                                 data-autoplay="<?= ! empty($page_data['banner_sequence_data']['timeout']) ? $page_data['banner_sequence_data']['timeout'] : 5000 ?>"
                                 data-direction="<?= ( ! empty($page_data['banner_sequence_data']['animation_type']) && $page_data['banner_sequence_data']['animation_type'] == 'vertical') ? 'vertical' : 'horizontal' ?>"
                                 data-effect="<?= ( ! empty($page_data['banner_sequence_data']['animation_type']) && $page_data['banner_sequence_data']['animation_type'] == 'fade') ? 'fade' : 'slide' ?>"
                                 data-slides="<?= $number_of_slides ?>"
                                 data-speed="<?= ! empty($page_data['banner_sequence_data']['rotating_speed']) ? $page_data['banner_sequence_data']['rotating_speed'] : 300 ?>"
                                >
                                <div class="swiper-wrapper">
                                    <?php foreach ($page_data['banner_slides'] as $slide): ?>
                                        <div class="swiper-slide banner-slide">
                                            <div class="banner-image attachment-banner size-banner" style="background-image: url('/shared_media/<?= $project_media_folder ?>/media/photos/banners/<?= $slide['image'] ?>')"></div>

                                            <div class="banner-shadow"></div>
                                            <?php
                                            // Check if "banner-left.png" and "banner-right.png" have been uploaded to the media plugin.
                                            // If so, add them before and/or after the banner
                                            ?>

                                            <?php if ($media_model->is_filename_used('banner-left.png', 'content')): ?>
                                                <div class="banner-left" style="background-image: url('<?= $media_path ?>content/banner-left.png');"></div>
                                            <?php endif; ?>

                                            <?php if ($media_model->is_filename_used('banner-right.png', 'content')): ?>
                                                <div class="banner-right" style="background-image: url('<?= $media_path ?>content/banner-right.png');"></div>
                                            <?php endif; ?>

                                            <?php if (!empty($slide['html'])): ?>
                                                <div class="frontpage-banner-caption">
                                                    <div class="row">
                                                        <div class="columns large-6 small-12"><?= $slide['html'] ?></div>
                                                    </div>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>

                                <?php if ( ! empty($page_data['banner_sequence_data']['controls'])): ?>
                                    <div class="swiper-button-next"></div>
                                    <div class="swiper-button-prev"></div>
                                <?php endif; ?>

                                <?php if ( ! empty($page_data['banner_sequence_data']['pagination'])): ?>
                                    <div class="swiper-pagination"></div>
                                <?php endif; ?>
                            </div>
                        <?php elseif ( ! empty ($page_data['banner_image'])): ?>
                            <?php // static banner ?>

                            <img
                                src="/shared_media/<?= $project_media_folder ?>/media/photos/banners/<?= $page_data['banner_image'] ?>"
                                class="attachment-banner size-banner"
                                alt=""
                                />
                            <div class="banner-left"></div>
                            <div class="banner-right"></div>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>