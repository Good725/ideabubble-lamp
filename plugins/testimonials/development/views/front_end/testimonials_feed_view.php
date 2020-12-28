<?php
$feed = new Model_Feeds;
$display = $feed->display_feed(__FILE__);
?>

<?php if ($display): ?>
    <div id="latest-testimonials">
        <h1>Latest Testimonials</h1>
        <ul id="slider_testimonials">
            <?=$feed_items?>
        </ul>
    </div>
    <?php if ($animation_type != 'fixed'): ?>
        <script type="text/javascript" src="<?= URL::get_engine_plugin_assets_base('pages')?>sliders/bxslider/jquery.bxslider.js"></script>
        <script type="text/javascript">
			jQuery(function()
			{
				$('#slider_testimonials').bxSlider({
					mode: '<?= $animation_type ?>',
					speed: '<?= $timeout ?>',
					auto: true,
					<?php if (Settings::instance()->get('testimonials_feed_pagination') != 1): ?>
					controls: false,
					pager: false,
					<?php endif; ?>
				});
			});
        </script>
    <?php endif; ?>
<?php endif; ?>
