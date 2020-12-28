<!-- News Feed -->
<div id="latest-news" class="news_feed">
	<h3 class="news_feed_heading"><a href="/news.html">Latest news</a></h3>
	<ul id="newsfeed_slider" class="left feed_slider">
		<?= $feed_items ?>
	</ul>
</div>
<script type="text/javascript" src="<?= URL::get_engine_plugin_assets_base('pages')?>sliders/bxslider/jquery.bxslider.js"></script>
<script type="text/javascript">
    jQuery(function()
	{
        $('#newsfeed_slider').bxSlider({
			mode: 'vertical',
			auto: true,
			pager: false,
			controls: false,
			speed: 8000,
			minSlides: 3,
			maxSlides: 3,
			slideMargin: 10,
			slideHeight: 91
		});
    });
</script>
<!-- /News Feed -->