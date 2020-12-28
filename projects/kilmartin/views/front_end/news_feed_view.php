<div id="latest-news">
	<ul id="slider1">
		<?=$feed_items?>
	</ul>
</div>
<script type="text/javascript" src="<?= URL::get_engine_plugin_assets_base('pages')?>sliders/bxslider/jquery.bxslider.js"></script>
<script type="text/javascript">
    jQuery(function(){
        var auto_slide;
        //Display news slider if there is more than one news
        jQuery(function(){
            if($('#slider_home_news li').size() > 1){
                auto_slide = true;
            }
            else{
                auto_slide = false;
            }
            $('#slider1').bxSlider({mode: 'fade', auto: true, controls: false});
            $(".bx-has-pager").remove();
        });
    });
</script>

