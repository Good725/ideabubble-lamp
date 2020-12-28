<div id="latest-news">
	<ul id="slider_home_news">
		<?=$feed_items?>
	</ul>
</div>
<script type="text/javascript" src="<?= URL::get_engine_plugin_assets_base('pages')?>sliders/bxslider/jquery.bxslider.js"></script>
<script type="text/javascript">

    var auto_slide;

    //Display news slider if there is more than one news
    jQuery(function(){
        if($('#slider_home_news li').size() > 1){
            auto_slide = true;
        }
        else{
            auto_slide = false;
        }
        $('#slider1').bxSlider({mode: 'fade', auto: auto_slide, controls: true});
    });

</script>

