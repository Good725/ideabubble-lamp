<ul id=gallery_slider>
    <?=$elements?>
</ul>

<?php echo '<script type="text/javascript" src="'. URL::get_engine_plugin_assets_base('pages') . 'sliders/bxslider/jquery.bxslider.js"></script>'; ?>

<script type="text/javascript">
    jQuery(function(){
        $('#gallery_slider').bxSlider({
            mode: 'fade',
            auto: true,
            controls: false
        });
    });
</script>