<!-- News Feed -->
<div id="latest-news" class="news_feed left">
	<h1><a href="<?=URL::base()?>news.html">Latest news</a></h1>
	<ul id="newsfeed_slider" class="left feed_slider">
		<?=$feed_items?>
	</ul>
</div>
<? /* @TODO: IF REQUIRED News SCROLLING FEED - UNCOMMENT BELOW CODE?>
<script type="text/javascript" src="<?= URL::get_engine_plugin_assets_base('pages')?>sliders/bxslider/jquery.bxslider.js"></script>
<script type="text/javascript">
    jQuery(function(){
        $('#newsfeed_slider').bxSlider({
			mode: 'fade',
			auto: true,
			pager: true,
			controls: true,
			speed: 3000
		});
    });
</script>
<? */?>
<!-- /News Feed -->