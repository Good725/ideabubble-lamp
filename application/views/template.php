<!doctype html>
<!--[if IE 7]><html class="no-js ie7 oldie" lang="en"> <![endif]-->
<!--[if IE 8]><html class="no-js ie8 oldie" lang="en"> <![endif]-->
<!--[if gt IE 8]><!-->
<html class="no-js" lang="en"> <!--<![endif]-->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">

	<title><?php echo $title; ?></title>
	<meta name="description" content="">
	<meta name="author" content="">

	<meta name="viewport" content="width=device-width, initial-scale=1">

	<!-- CSS concatenated and minified from less
	====================================================================== -->
	<link rel="stylesheet" href="<?php echo URL::get_engine_assets_base(); ?>css/bootstrap.css">
	<!-- end CSS -->

	<!-- This is the only JS allowed in the head. All other JS files in the footer
	====================================================================== -->
	<script src="<?php echo URL::get_engine_assets_base(); ?>js/libs/modernizr-2.0.6.min.js"></script>
	<!-- Load jQuery from Google or fall back to local copy -->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
	<script>window.jQuery || document.write('<script src="<?= URL::get_engine_assets_base(); ?>js/libs/jquery-2.1.4.min.js"><\/script>')</script>

    <?php
    if (Settings::instance()->get('google_analytics_backend_tracking')) {
        echo Settings::get_google_analytics_script();
    }
    ?>
	<!-- end header JS -->

</head>

<body<?php if (isset($current_controller))
{
	echo ' id="' . $current_controller . '" ';
} ?> class='<?='env-mode'.Kohana::$environment ?>'>

<!-- Header -->
<?php echo $header; ?>
<!-- End Header -->

<!-- Container -->
<div class="container-fluid">

	<div class="row">

		<?php if (isset($sidebar))
	{
		?>
		<div class="span3">

			<!-- Main Sidebar -->
			<?php echo $sidebar; ?>
			<!-- End Sidebar -->

		</div>
		<? } ?>

		<div class="span<?php echo (isset($sidebar)) ? "9" : "12"; ?>">

			<!-- Main Content -->
			<?php echo $body; ?>
			<!-- End Main Content -->

		</div>

	</div>

	<!-- Footer -->
	<?php echo $footer; ?>
	<!-- End Footer -->

</div>
<!-- End Container -->


<!-- Begin Footer JS section
	====================================================================== -->

<!-- Bootstrap style JS script-->
<script src="<?= URL::get_engine_assets_base(); ?>js/bootstrap-3.3.5.min.js"></script>

<!-- Scripts concatenated and minified via ant build script-->
<script src="<?= URL::get_engine_assets_base(); ?>js/jquery.dataTables.min.js"></script>
<script src="<?= URL::get_engine_assets_base() ?>js/ckeditor/ckeditor.js"></script>
<script src="<?= URL::get_engine_assets_base(); ?>js/plugins.js"></script>
<script src="<?= URL::get_engine_assets_base(); ?>js/forms.js"></script>
<!-- 3rd party plugins -->
<?php
// Add in any plugin specific scripts...
if (isset($scripts) && is_array($scripts))
{
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
<!-- Our scripts don't put inline if possible -->
<!-- end scripts-->

<!-- End JS section -->

<div id="profiler" style="display:none;">
	<?php echo View::factory('profiler/stats') ?>
</div>
<?php echo Settings::instance()->get('cms_footer_html'); ?>
</body>
</html>
