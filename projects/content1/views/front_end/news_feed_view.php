<?php
$feed = new Model_Feeds;
$display = $feed->display_feed(__FILE__);
$min_slides = Settings::instance()->get('news_min_items_per_slide');
$max_slides = Settings::instance()->get('news_max_items_per_slide');
?>
<?php if (Kohana::$config->load('config')->get('template_folder_path') != 'wide_banner'): ?>
	<div class="latest_news_header" id="latest_news_header">Latest News</div>
<?php endif; ?>
<?php if ($display): ?>
	<div id="latest_news_content"<?= ($min_slides != '') ? ' style="height: '.($min_slides * 100).'px"' : '' ?>>
		<div id="latest-news" class="latest-news">
			<ul id="slider1">
				<?= $feed_items ?>
			</ul>
		</div>
        <?php if ($animation_type != 'fixed'): ?>
            <script type="text/javascript" src="<?= URL::get_engine_plugin_assets_base('pages') ?>sliders/bxslider/jquery.bxslider.js"></script>
            <script type="text/javascript">
				jQuery(function(){
					$('#slider1').bxSlider(
					{
						mode: '<?= $animation_type ?>',
						auto: true,
						pager: false,
						controls: false,
						speed: <?= $timeout ?>
						<?php if ((Settings::instance()->get('news_animation_type') == 'vertical') OR (Kohana::$config->load('config')->get('template_folder_path') == 'wide_banner')): ?>
						,
						minSlides: <?= ($min_slides != '') ? $min_slides : 2 ?>,
						maxSlides: <?= ($max_slides != '') ? $max_slides : 2 ?>,
						slideMargin: 10,
						slideHeight: 91
						<?php endif; ?>
					});
				});
			</script>
        <?php endif; ?>
    </div>

<?php endif; ?>
