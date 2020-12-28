<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <? $assets_folder_path = Kohana::$config->load('config')->assets_folder_path; ?>
        <?php
        if (Settings::instance()->get('search_engine_indexing') === 'FALSE') {
            echo "<meta name='robots' content='NOINDEX, NOFOLLOW' />";
        }
        ?>

        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <meta name="description" content="<?= $page_data['seo_description']; ?>" />
        <meta name="keywords" content="<?= $page_data['seo_keywords']; ?>" />
        <meta name="author" content="//ideabubble.ie" />
        <meta name="viewport" content="width=device-width, initial-scale=1" />
        <meta name="google-site-verification" content="<?= @settings::instance()->get('google_webmaster_code') ?>" />
        <meta name="msvalidate.01" content="<?= @settings::instance()->get('bing_webmaster_code') ?>"/>
        <title><?= $page_data['title']; ?></title>
        <link href="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/images/favicon.ico" rel="shortcut icon" type="image/ico" />
        <link href="<?= URL::site() ?>assets/templates/02/css/normalize.css" rel="stylesheet" type="text/css" />
        <link href="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/css/validation.css" rel="stylesheet" type="text/css" />
        <link href="<?= URL::site() ?>assets/default/css/bootstrap_3.1.0.css" rel="stylesheet" type="text/css" />
        <link href="<?= URL::site() ?>assets/templates/02/css/structure.css" rel="stylesheet" type="text/css" />
        <link href="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/css/styles.css" rel="stylesheet" type="text/css"/>
        <link href="//fonts.googleapis.com/css?family=Open+Sans+Condensed:300,700,300italic" rel="stylesheet" type="text/css" />
        <?= settings::get_google_analitycs_script(); ?>
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
        <script type="text/javascript">window.jQuery || document.write('<script type="text/javascript" src="<?=URL::site()?>assets/<?= $assets_folder_path ?>/js/jquery-1.7.2.min.js"><\/script>')</script>
        <script type="text/javascript" src="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/js/jquery.validationEngine2.js"></script>
        <script type="text/javascript" src="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/js/jquery.validationEngine2-en.js"></script>
        <script type="text/javascript" src="<?= URL::site() ?>assets/default/js/bootstrap_3.1.0.js"></script>
        <script type="text/javascript" src="<?= URL::site() ?>assets/default/js/general.js"></script>
        <script type="text/javascript" src="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/js/general.js"></script>
        <script type="text/javascript" src="//assets.pinterest.com/js/pinit.js"></script>
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
        <?= $page_data['head_html'] ?>
    </head>