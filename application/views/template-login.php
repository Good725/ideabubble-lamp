<!doctype html><!--[if IE 7]>
<html class="no-js ie7 oldie" lang="en"> <![endif]--><!--[if IE 8]>
<html class="no-js ie8 oldie" lang="en"> <![endif]--><!--[if gt IE 8]><!-->
<html class="no-js" lang="en"> <!--<![endif]-->
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

        <title><?php echo $title; ?></title>
        <meta name="description" content="">
        <meta name="author" content="">

        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSS concatenated and minified
        ====================================================================== -->
		<link rel="stylesheet" href="<?= URL::overload_asset('css/cms.compiled.css') ?>" />
        <link rel="stylesheet" href="<?= URL::overload_asset('css/forms.css', ['cachebust' => true]) ?>" />
        <link rel="stylesheet" href="<?= URL::overload_asset('css/stylish.css', ['cachebust' => true]) ?>" />
        <link rel="stylesheet" href="<?= URL::get_engine_assets_base().'css/elegant_icons.css' ?>" />

        <?php
        if (isset($styles) && is_array($styles)) {
            foreach ($styles as $link => $media) {
                echo  '<link rel="stylesheet" href="'.$link.'" media="'.$media.'" />'. PHP_EOL;
            }
        }
        ?>

        <link rel="stylesheet" href="<?= URL::overload_asset('css/login-signup.css') ?>" />
        <?php
        $skin = isset($_GET['usetheme']) ? $_GET['usetheme'] : '';
        if (!$skin) {
            $skin = Settings::instance()->get('cms_skin');
        }
        ?>

		<?php if ($skin != '' && file_exists(APPPATH.'assets/'.$skin.'/css/styles.css')): ?>
			<link rel="stylesheet" href="<?= URL::get_engine_theme_assets_base().'css/styles.css' ?>" />
		<?php endif; ?>
        <?php if (Settings::instance()->get('cms_skin') != '' AND file_exists(APPPATH.'assets/'.(Settings::instance()->get('cms_skin')).'/css/login-contact-duplicate-detection.css')): ?>
            <link rel="stylesheet" href="<?=URL::get_engine_theme_assets_base().'css/login-contact-duplicate-detection.css'; ?>" />
        <?php endif; ?>

        <link rel="stylesheet" href="<?= URL::overload_asset('css/project.css') ?>" />
        <!-- end CSS -->

        <!-- This is the only JS allowed in the head. All other JS files in the footer
        ====================================================================== -->
        <script src="<?php echo URL::get_engine_assets_base(); ?>js/libs/modernizr-2.0.6.min.js"></script>
        <!-- Load jQuery from Google or fall back to local copy -->
        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
        <script>window.jQuery || document.write('<script src="<?= URL::get_engine_assets_base(); ?>js/libs/jquery-2.1.4.min.js"><\/script>')</script>
        <!-- end header JS -->
        <!-- Begin Footer JS section
        ====================================================================== -->

		<?php if ((Settings::instance()->get('browser_sniffer_backend'))
            && (!isset($page_data['assets_implemented']['browser_sniffer']) || !$page_data['assets_implemented']['browser_sniffer'])): ?>
			<!-- Browser detection -->
            <script type="text/javascript">
                var test_mode = <?= (Settings::instance()->get('browser_sniffer_testmode') == 1) ? 'true' : 'false' ?>;
                var $buoop_unsupported_browsers = <?= json_encode(Settings::instance()->get('browser_sniffer_unsupported_options')) ?>;
                var $buoop_rcmd_browser = '<?= Settings::instance()->get('browser_sniffer_recommended_browser') ?>';
                var $buoop_vs = {
                    i: '<?= Settings::instance()->get('browser_sniffer_version_ie') ?>',
                    f: '<?= Settings::instance()->get('browser_sniffer_version_firefox') ?>',
                    o: '<?= Settings::instance()->get('browser_sniffer_version_opera') ?>',
                    c: '<?= Settings::instance()->get('browser_sniffer_version_chrome') ?>',
                    s: '<?= Settings::instance()->get('browser_sniffer_version_safari') ?>'
                }
            </script>
			<script src="<?= URL::get_engine_assets_base(); ?>js/browserorg/main.js"></script>

        <?php if (!file_exists (URL::overload_asset('css/browserorg/style.css'))): ?>
            <link rel="stylesheet" href="<?= URL::get_engine_theme_assets_base().'css/styles.css' ?>" />
        <?php endif; ?>

		<?php $page_data['assets_implemented']['browser_sniffer'] = true;
              View::bind_global('page_data', $page_data);
        endif; ?>

        <?php
        $settings_instance = Settings::instance();
        $pixel_enabled_sitewide = $settings_instance->get('facebook_pixel_enabled_sitewide');
        $facebook_pixel_code = trim($settings_instance->get('facebook_pixel_code'));
        ?>
        <?php if ($pixel_enabled_sitewide && !empty($facebook_pixel_code)): ?>
            <script>
                !function(f,b,e,v,n,t,s)
                {if(f.fbq)return;n=f.fbq=function(){n.callMethod?
                n.callMethod.apply(n,arguments):n.queue.push(arguments)};
                if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
                n.queue=[];t=b.createElement(e);t.async=!0;
                t.src=v;s=b.getElementsByTagName(e)[0];
                s.parentNode.insertBefore(t,s)}(window, document,'script',
                'https://connect.facebook.net/en_US/fbevents.js');
                fbq('init', '<?= $facebook_pixel_code ?>');

                <?php if (isset($_GET['registered']) && $_GET['registered'] == 'success'): ?>
                    fbq('track', 'CompleteRegistration');
                <?php endif; ?>
            </script>
        <?php endif; ?>

        <?php
        if (Settings::instance()->get('google_analytics_backend_tracking')) {
            echo Settings::get_google_analytics_script();
        }
        ?>
        <!-- End JS section -->
    </head>
<?php $referal  = @$_SERVER["HTTP_REFERER"];?>

    <?php
    $background_image = trim(Settings::instance()->get('login_background_image'));
    $background_image_path = Model_Media::get_image_path($background_image);
    ?>

    <body
        class="layout-login<?= (strrpos($referal,"/checkout_with_overlay")) ? "layout-login-back-white" : '' ?> theme-<?= Settings::instance()->get('cms_skin') ?>"
        <?= $background_image ? 'style="background-image: url(\''.$background_image_path.'\');"' : '' ?>
    >

        <!-- Container -->
        <div class="container-fluid"><?= $body ?></div>
        <!-- End Container -->

        <!-- Bootstrap style JS script-->
        <script src="<?= URL::get_engine_assets_base(); ?>js/bootstrap-3.3.5.min.js"></script>
        <script src="<?= URL::get_engine_assets_base(); ?>js/bootbox.js"></script>
        <script src="<?= URL::get_engine_assets_base(); ?>js/forms.js"></script>

        <!-- Scripts concatenated and minified via ant build script-->
        <?php
        // Add in any plugin specific scripts...
        if (isset($scripts) && is_array($scripts)) {
            foreach ($scripts as $script) {
                echo $script . PHP_EOL;
            }
        }
        ?>
        <!-- end scripts-->

        <!--[if lt IE 7 ]>
        <script src="//ajax.googleapis.com/ajax/libs/chrome-frame/1.0.3/CFInstall.min.js"></script>
        <script>window.attachEvent('onload', function () {
            CFInstall.check({mode: 'overlay'})
        })</script>
        <![endif]-->

    </body>
</html>
