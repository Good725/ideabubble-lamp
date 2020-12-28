<!doctype html><!--[if IE 7]>
<html class="no-js ie7 oldie" lang="en"> <![endif]--><!--[if IE 8]>
<html class="no-js ie8 oldie" lang="en"> <![endif]--><!--[if gt IE 8]><!-->
<html class="no-js" lang="en"> <!--<![endif]-->
<head>
	<script>
		if (window.top.location.host != window.location.host) {
			window.top.location.href = window.location.href;
		}
	</script>
	<script>
    window.ibcms = {};
    <?php
    $loggedUser = Auth::instance()->get_user();
    $userModel = new Model_Users();
    $loggedUserData = $userModel->get_user($loggedUser['id']);
    ?>
	window.ibcms.settings = {}
	window.ibcms.settings.contacts_create_family = <?=Settings::instance()->get('contacts_create_family')?>;
    window.ibcms.user = <?=json_encode($loggedUserData);?>;
	window.ibcms.date_format = '<?php
                    switch(\Settings::instance()->get('date_format')){
                        case 'Y-m-d': echo 'yyyy-mm-dd';break;
                        case 'm/d/Y': echo 'mm/dd/yyyy';break;
                        case 'd/m/Y': echo 'dd/mm/yyyy';break;
                        case 'd-m-Y': echo 'dd-mm-yyyy';break;
                        default:      echo 'dd-mm-yyyy';break;
                    } ?>';
	window.ibcms.max_attachment_size_mb = <?=\Settings::instance()->get('messaging_max_attachment_size_mb')?>;
    </script>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="twitter:widgets:csp" content="on">
    <title><?php echo $title; ?></title>
    <meta name="description" content="">
    <meta name="author" content="">

    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSS concatenated and minified from less
    ====================================================================== -->

	<link rel="stylesheet" href="<?= URL::overload_asset('css/cms.compiled.css') ?>" />
	<?php
	$current_url = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . "{$_SERVER['HTTP_HOST']}/{$_SERVER['REQUEST_URI']}";
	if ( preg_match("~\bkilmartin\b~",$current_url) )
	{
	?>
		<link rel="stylesheet" href="<?= '//'.URL::get_static_domain().'/engine/kes/css/styles.css'; ?>" />
		<link rel="stylesheet" href="<?= '//'.URL::get_static_domain().'/engine/kes/css/header-footer.css'; ?>" />
	<?php
	}
	?>
	
	<link rel="stylesheet" href="<?= URL::overload_asset('js/bootstrap-toggle/bootstrap-toggle.min.css') ?>" />
    <link rel="stylesheet" href="<?= URL::overload_asset('css/forms.css', ['cachebust' => true]) ?>" />
    <link rel="stylesheet" href="<?= URL::overload_asset('css/stylish.css', ['cachebust' => true]) ?>">
	<link rel="stylesheet" href="<?= URL::overload_asset('css/jquery.datetimepicker.css') ?>">
    <link rel="stylesheet" href="<?= URL::overload_asset('css/custom.css') ?>">
    <link rel="stylesheet" href="<?= URL::overload_asset('css/project.css') ?>">
    <?php
    if (isset($styles) && is_array($styles)) {
        foreach ($styles as $file => $type) {
            //echo HTML::style($file, array('media' => $type)), "\n";
            echo '<link rel="stylesheet" type="text/css" href="' . $file . '" media="' . $type . '">';
        }
    }
    ?>
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
    <script>window.jQuery || document.write('<script src="<?= URL::get_engine_assets_base(); ?>js/libs/jquery-2.1.4.min.js"><\/script>')</script>
    <script src="<?=URL::get_engine_assets_base() .'js/highcharts/highcharts.js';?>" type="text/javascript"></script>
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
	<!-- fallback to local copy -->
    <script>window.jQuery.ui || document.write('<script src="<?= URL::get_engine_assets_base(); ?>js/libs/jquery-ui-1.11.4.min.js"><\/script>')</script>
	<script src="<?= URL::get_engine_assets_base(); ?>js/jquery.ui.touch-punch.min.js"></script>

	<script src="<?php echo URL::get_engine_assets_base(); ?>js/daterangepicker/jquery.datetimepicker.js"></script>
	<script src="<?= URL::get_engine_assets_base(); ?>js/moment.min.js"></script>

    <?php
    if (Settings::instance()->get('google_analytics_backend_tracking')) {
        echo Settings::get_google_analytics_script();
    }
    ?>

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
		<link rel="stylesheet" href="<?= URL::overload_asset('css/browserorg/style.css') ?>">
	<?php endif; ?>

    <?php if (Auth::instance()->has_access('messaging_access_own_mail') || Auth::instance()->has_access('messaging_view')) { ?>
	<link rel="stylesheet" href="<?php echo URL::get_engine_plugin_assets_base('messaging') . 'css/check_notifications.css' ?>">
	<script src="<?=URL::get_engine_plugin_assets_base('messaging') . 'js/check_notifications.js'?>"></script>
	<?php
	}
	?>
	
	<?php
	if(class_exists('Model_Keyboardshortcut')){ ?>
	<script src="<?=URL::get_engine_assets_base() . 'js/keyboardshortcuts.js'?>"></script>
	<?php
	}
	?>

    <?= Settings::instance()->get('cms_head_html'); ?>
	
</head>

<body<?= (isset($current_controller)) ? ' id="'.$current_controller.'"' : '' ?>  class="<?= 'env-mode' . Kohana::$environment ?>">

<!-- Header -->
<?php echo $header; ?>
<!-- End Header -->
<input type="hidden" id="datatable_default_length" value="<?= ( ! empty($user_details->datatable_length_preference)) ? $user_details->datatable_length_preference : 10 ?>" />

<?php
$display_on_dashboard = TRUE; // (strpos($body, 'pages.png') == TRUE);
$current_page         = str_replace('/', '', $_SERVER["REQUEST_URI"]);
$is_dashboard_page    = (URL::site('admin') == URL::site() . $current_page OR $current_page == 'admindashboard');
?>
<!-- Container -->
<div id="main" class="container">
	<?= (isset($alert)) ? $alert : '' ?>
	<?php if ($display_on_dashboard AND $is_dashboard_page AND Settings::instance()->get('ib_twitter_feed') == 1): ?>
		<div id="ibTwitterFeed">
			<a class="twitter-timeline" href="https://twitter.com/ideabubble" data-widget-id="392616846836252672">Tweets by @ideabubble</a>
			<script>
				!function (d, s, id) {
					var js, fjs = d.getElementsByTagName(s)[0], p = /^http:/.test(d.location) ? 'http' : 'https';
					if (!d.getElementById(id)) {
						js = d.createElement(s);
						js.id = id;
						js.src = p + "://platform.twitter.com/widgets.js";
						fjs.parentNode.insertBefore(js, fjs);
					}
				}(document, "script", "twitter-wjs");
			</script>
		</div>
	<?php endif; ?>

    <div class="row content-inner">
        <?php if (0 && isset($sidebar)): ?>
            <div class="span3">
                <!-- Main Sidebar -->
                <?php echo $sidebar; ?>
                <!-- End Sidebar -->
            </div>
        <?php endif; ?>

		<?= View::factory('html_content_area')->set(array(
			'show_welcome_text' => isset($show_welcome_text) ? $show_welcome_text : TRUE,
			'body' => $body,
			'on' => isset($on) ? $on : '',
			'off' => isset($off) ? $off : '',
			'jira' => isset($jira) ? $jira : ''))
		?>

	</div>
</div>
<!-- End Container -->

<!-- Footer -->
<?php echo $footer; ?>
<!-- End Footer -->

<!-- Begin Footer JS section
	====================================================================== -->

<!-- Bootstrap style JS script-->
<script src="<?= URL::get_engine_assets_base() ?>js/bootstrap-3.3.5.min.js"></script>

<!-- Scripts concatenated and minified via ant build script-->
<script src="<?= URL::get_engine_assets_base() ?>js/jquery.dataTables.min.js"></script>
<script src="<?= URL::get_engine_assets_base() ?>js/ckeditor/ckeditor.js"></script>
<script src="<?= URL::get_engine_assets_base() ?>js/plugins.js"></script>
<script src="<?= URL::get_engine_assets_base() ?>js/bootstrap-datepicker.js"></script>
<script src="<?= URL::get_engine_assets_base() ?>js/combobox.js"></script>
<script src="<?= URL::get_engine_assets_base() ?>js/bootstrap-toggle/bootstrap-toggle.min.js"></script>
<script src="<?= URL::get_engine_assets_base() ?>js/bootstrap-multiselect.js"></script>
<script src="<?= URL::overload_asset('js/forms.js', ['cachebust' => true]) ?>"></script>

<!-- 3rd party plugins -->
<?php
// Add in any plugin specific scripts...
if (isset($scripts) && is_array($scripts)) {
	$scripts_echoed = array();
	foreach ($scripts as $script) {
		if (array_search($script, $scripts_echoed) === false) {
			echo $script . "\n";
			$scripts_echoed[] = $script;
		}
	}
}
?>
<script src="<?php echo URL::get_engine_assets_base(); ?>js/script.js"></script>

<script src="<?php echo URL::get_engine_assets_base(); ?>js/codes-import.js"></script>

<?php if (Settings::instance()->get('slaask_api_access_cms')): ?>
	<?php $slaask_api_key = trim(Settings::instance()->get('slaask_api_key')); ?>
	<?php if ($slaask_api_key): ?>
		<script src='https://cdn.slaask.com/chat.js'></script>
		<script>
			_slaask.identify('<?=$loggedUserData['name'] . ' ' . $loggedUserData['surname']?>', {
				user_id: <?=$loggedUserData['id']?>,
				email: '<?=$loggedUserData['email']?>'
			});

			_slaask.init('<?= $slaask_api_key ?>');

			document.addEventListener('slaask.ready', function (e) {
				console.log(e.detail);
			}, false);

			document.addEventListener('slaask.open', function (e) {
				console.log(e.detail);
			}, false);

			document.addEventListener('slaask.close', function (e) {
				console.log(e.detail);
			}, false);

			document.addEventListener('slaask.sent_message', function (e) {
				console.log(e.detail);
			}, false);

			document.addEventListener('slaask.received_message', function (e) {
				console.log(e.detail);
			}, false);
		</script>
	<?php endif; ?>
<?php endif; ?>

<!-- Our scripts don't put inline if possible -->
<!-- end scripts-->

<!-- End JS section -->

<div id="profiler" style="display:none;">
    <?php echo View::factory('profiler/stats') ?>
</div>
<?php echo IbHelpers::t_tag(Settings::instance()->get('cms_footer_html')); ?>

<div class="modal fade" tabindex="-1" role="dialog" id="auto-logout-modal">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<h4 class="modal-title"><?= __('Inactive for too long') ?></h4>
			</div>
			<div class="modal-body">
				<p><?= __('For your protection, we have logged you out as you have been inactive for more than ' .
                        $loggedUserData['auto_logout_minutes'] .
							' minutes. You need to login again to continue') ?></p>
			</div>
			<div class="modal-footer">
				<a href="/admin/login" class="btn"><?= __('Log in') ?></a>
			</div>
		</div>
	</div>
</div>

</body>
</html>
