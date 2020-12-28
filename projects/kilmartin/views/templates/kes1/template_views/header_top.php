<?php
$assets_folder_path = Kohana::$config->load('config')->assets_folder_path;
$assets_folder_code_path = PROJECTPATH.'www/assets/'.$assets_folder_path;
$settings = Settings::instance()->get();
$user = Auth::instance()->get_user();
$has_banner = ( ! empty($page_data['banner_slides']) || ! empty($page_data['banner_image']) || ! empty($page_data['banner_map']));
$banner_search = ($has_banner && !empty($banner_search));
$banner_search = Settings::instance()->get('course_finder_mode') != 'none' && (strtolower($page_data['layout']) == 'home' || !empty($banner_search));
?><!doctype html>
<html lang="en">
	<head>
        <?php
        if (@$settings['onetrust_cookie_policy_enable'] == 1) {
        ?>
        <!-- OneTrust Cookies Consent Notice start -->
        <script src="https://cdn.cookielaw.org/scripttemplates/otSDKStub.js"  type="text/javascript" charset="UTF-8" data-domain-script="<?=$settings['onetrust_cookie_policy_domain_script']?>" ></script>
        <script type="text/javascript">
            function OptanonWrapper() { }
        </script>
        <!-- OneTrust Cookies Consent Notice end -->
        <?php
        }
        ?>
        <?=@$settings['template_head_first'] ?>
        <script>
            if (window.top.location.host != window.location.host) {
                window.top.location.href = window.location.href;
            }
        </script>
        <?=(Settings::instance()->get('search_engine_indexing') == 'FALSE') ? '<meta name="robots" content="noindex">' : '' ;?>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title><?= htmlentities($page_data['title']) ?></title>
        <meta property="og:title" content="<?= htmlentities($page_data['title']) ?>" />

        <?= i18n::get_canonical_link(); ?>
        <?= i18n::get_alternate_links(); ?>

		<meta name="description" content="<?= trim($page_data['seo_description']) ?>" />
		<meta name="keywords" content="<?= trim($page_data['seo_keywords'])?>" />
		<meta name="google-site-verification" content="<?= settings::instance()->get('google_webmaster_code') ?>"/>
		<meta name="msvalidate.01" content="<?= settings::instance()->get('bing_webmaster_code') ?>"/>
		<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no"/>
        <?php if (Settings::instance()->get('site_favicon')): ?>
            <link rel="shortcut icon" href="<?= Model_Media::get_image_path(Settings::instance()->get('site_favicon'), 'favicons', array('cachebust' => true)); ?>" type="image/ico" />
        <?php else: ?>
            <link rel="shortcut icon" href="/assets/<?= $assets_folder_path ?>/images/favicon.ico" type="image/ico" />
        <?php endif; ?>
		<?=settings::get_google_analytics_script();?>
		<meta name="google-site-verification" content="<?= settings::instance()->get('google_webmaster_code') ?>" />
		<meta name="msvalidate.01" content="<?= settings::instance()->get('bing_webmaster_code') ?>" />
        <link rel="stylesheet" type="text/css" href="<?= URL::get_engine_assets_base() ?>/css/bootstrap-multiselect.css" />
        <link rel="stylesheet" type="text/css" href="<?= URL::get_engine_assets_base() ?>/css/validation.css" />
        <?php if (!empty($page_object) && $page_object->has_media_player()): ?>
            <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/plyr/3.5.6/plyr.css" media="screen" type="text/css" />
            <script src="https://cdnjs.cloudflare.com/ajax/libs/plyr/3.5.6/plyr.min.js"></script>
        <?php endif; ?>

		<link rel="stylesheet" type="text/css" href="<?= URL::get_engine_assets_base() ?>/css/jquery.datetimepicker.css" media="screen" />
		<link rel="stylesheet" type="text/css" href="<?= URL::get_engine_plugin_assets_base('courses') ?>css/eventCalendar.css" media="screen" />
		<link rel="stylesheet" type="text/css" href="<?= URL::get_engine_assets_base() ?>/css/swiper.min.css" />
        <link rel="stylesheet" href="<?= URL::overload_asset('css/forms.css', ['cachebust' => true]) ?>" />

        <?php if (isset($theme) && trim($theme->styles)): ?>
             <link rel="stylesheet" href="<?=$theme->get_url() ?>" />
         <?php else: ?>
             <link rel="stylesheet" type="text/css" href="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/css/styles.css?ts=<?= @filemtime($assets_folder_code_path.'/css/styles.css') ?>" />
             <link rel="stylesheet" type="text/css" href="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/css/print.css" media="print" />
         <?php endif; ?>

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>

		<?php if(Model_Plugin::is_enabled_for_role('Administrator', 'Messaging')){ ?>
			<link rel="stylesheet" href="<?php echo URL::get_engine_plugin_assets_base('messaging') . 'css/check_notifications.css' ?>">
		<?php } ?>

        <?php if (Settings::instance()->get('stripe_enabled') == 'TRUE'): ?>
            <script src="https://checkout.stripe.com/checkout.js"></script>
            <script src="https://js.stripe.com/v3/"></script>
        <?php endif; ?>

        <?php if (isset($_GET['og_data'])): ?>
            <?php $og_data = Model_Pages::get_page($_GET['og_data']) ?>
            <?php if ($og_data): ?>
                <?php $og_data = isset($og_data[0]) ? $og_data[0] : $og_data; ?>

                <meta property="og:title" content="<?= Settings::instance()->get('company_title') ?>" />
                <meta property="og:type" content="website" />
                <meta property="og:description" content="<?= $og_data['seo_description'] ?>" />
                <meta property="og:url" content="<?= (isset($_SERVER['HTTPS']) ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'].Request::detect_uri().'?og_data='.$_GET['og_data'] ?>" />
                <meta property="og:image" content="<?= URL::site() ?>assets/kes1/img/thanks.jpg" />
                <meta property="og:image:type" content="image/jpeg" />
                <meta property="og:image:width" content="265" />
                <meta property="og:image:height" content="84" />
            <?php endif; ?>
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
		<?= Settings::instance()->get('head_html'); ?>
	</head>

    <?php
    if (!isset($mobile_footer_menu)) {
        $mobile_footer_menu = Settings::instance()->get('sticky_mobile_footer_menu');
    }
    ?>

    <body
        class="template-educate layout-<?= $page_data['layout'] ?><?php
            echo $user                    ? ' body-logged_in' : ' body-logged_out';
            echo $has_banner              ? ' has_banner' : '';
            echo $banner_search           ? ' has_banner_search' : '';
            echo !empty($has_breadcrumbs) ? ' has_breadcrumbs' : '';
            echo $mobile_footer_menu      ? ' has_mobile_footer_menu' : '';
            echo Settings::instance()->get('sticky_header') ? ' has_sticky_header' : '';
            echo (!empty($page_object) && $page_object->subject->id)  ? ' has_linked_subject' : '';
            echo (!empty($page_object) && $page_object->get_color()) ? ' has_category_color' : '';
            ?>"
        data-page="<?= $page_data['name_tag'] ?>"
        <?= (!empty($page_object) && $page_object->get_color()) ? ' style="--category-color: '.$page_object->get_color().'"' : '' ?>
    >
        <?= Settings::instance()->get('body_html'); ?>

        <div class="hidden">
            <?= IbHelpers::get_spritesheet() ?>
        </div>

        <svg class="sr-only">
            <defs>
                <clipPath id="vertical-clippath">
                    <path d="M69.09,10c0,0 -65.1,250.77 -33.34,431.13c31.76,180.36 107.01,344.77 67.45,529.75c-39.55,184.98 131.03,443.12 131.03,443.12l895.76,-28l8,-1337z"></path>                </clipPath>
            </defs>
        </svg>

        <?= View::factory('snippets/login_as_block'); ?>
        <div class="wrapper">
            <?= (isset($body) && isset($body->alert)) ? $body->alert : IbHelpers::get_messages() ?>
