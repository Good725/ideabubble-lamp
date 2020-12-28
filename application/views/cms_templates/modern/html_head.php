<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
<title><?php echo $title; ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="twitter:widgets:csp" content="on">
<link rel="stylesheet" href="<?= URL::overload_asset('css/cms.compiled.css') ?>" />
<link rel="stylesheet" href="<?= URL::overload_asset('js/bootstrap-toggle/bootstrap-toggle.min.css') ?>" />
<link rel="stylesheet" href="<?= URL::overload_asset('css/forms.css', ['cachebust' => true]) ?>" />
<link rel="stylesheet" href="<?= URL::overload_asset('css/stylish.css', ['cachebust' => true]) ?>" />
<link rel="stylesheet" href="<?= URL::overload_asset('css/jquery.datetimepicker.css') ?>">
<?php if (Settings::instance()->get('shared_footer')): ?>
    <link rel="stylesheet" href="<?= URL::get_engine_assets_base() . 'css/shared_footer.css' ?>"/>
<?php endif; ?>
<?php if (Settings::instance()->get('cms_skin') != ''): ?>
    <link rel="stylesheet" href="<?= URL::get_engine_theme_assets_base() . 'css/styles.css' ?>"/>
<?php endif; ?>
<link rel="stylesheet" href="<?php echo URL::overload_asset('css/custom.css') ?>">
<link rel="stylesheet" href="<?= URL::overload_asset('css/project.css') ?>">
<?php if (isset($styles) && is_array($styles)): ?>
	<?php foreach ($styles as $file => $type): ?>
		<link rel="stylesheet" type="text/css" href="<?= $file ?>" media="<?= $type ?>" />
	<?php endforeach; ?>
<?php endif; ?>
<!-- end CSS -->
<?php if (Model_Plugin::is_enabled_for_role('Administrator', 'Media')): ?>
	<!-- fonts -->
	<link rel="stylesheet" type="text/css" href="/admin/media/fonts" />
<?php endif; ?>

<!-- This is the only JS allowed in the head. All other JS files in the footer
====================================================================== -->
<script src="<?php echo URL::get_engine_assets_base(); ?>js/libs/modernizr-2.0.6.min.js"></script>
<!-- Load jQuery from Google or fall back to local copy -->
<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
<script>window.jQuery || document.write('<script src="<?php echo URL::get_engine_assets_base(); ?>js/libs/jquery-2.1.4.min.js"><\/script>')</script>
<script src="<?=URL::get_engine_assets_base() ?>js/highcharts/highcharts.js" type="text/javascript"></script>
<script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
<script>window.jQuery.ui || document.write('<script src="<?= URL::get_engine_assets_base(); ?>js/libs/jquery-ui-1.11.4.min.js"><\/script>')</script>
<script src="<?= URL::get_engine_assets_base(); ?>js/jquery.ui.touch-punch.min.js"></script>

<?php
if ((Settings::instance()->get('browser_sniffer_backend'))
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
    <link rel="stylesheet" href="<?= URL::get_engine_assets_base() ?>css/browserorg/style.css">
<?php $page_data['assets_implemented']['browser_sniffer'] = true;
    View::bind_global('page_data', $page_data);
endif; ?>

<?php
if (Settings::instance()->get('google_analytics_backend_tracking')) {
    echo Settings::get_google_analytics_script();
}
?>

<script src="<?php echo URL::get_engine_assets_base(); ?>js/daterangepicker/jquery.datetimepicker.js"></script>
<script src="<?= URL::get_engine_assets_base(); ?>js/moment.min.js"></script>

<?php if (Auth::instance()->has_access('messaging_access_own_mail') || Auth::instance()->has_access('messaging_view')) { ?>
    <link rel="stylesheet"
          href="<?php echo URL::get_engine_plugin_assets_base('messaging') . 'css/check_notifications.css' ?>">
    <script src="<?= URL::get_engine_plugin_assets_base('messaging') . 'js/check_notifications.js' ?>"></script>
<?php } ?>

<?php if(class_exists('Model_Keyboardshortcut')){ ?>
<script src="<?=URL::get_engine_assets_base() . 'js/keyboardshortcuts.js'?>"></script>
<?php } ?>

<?php if (file_exists(APPPATH.'assets/'.Settings::instance()->get('cms_skin').'/js/script.js')): ?>
    <script src="<?=URL::get_engine_theme_assets_base() . 'js/script.js'?>"></script>
<?php endif; ?>

<?= Settings::instance()->get('cms_head_html'); ?>

<?php echo View::factory('snippets/login_as_block'); ?>
