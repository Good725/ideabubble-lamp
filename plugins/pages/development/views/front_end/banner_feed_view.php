<div class="banners">
    <ul id="imageContainer" class="innerfade">
        <?=$feed_items?>
    </ul>
</div>

<?php
$bannerSpeed = 2000;
$bannerTimeout = 7000;
$banners = "";
$bannerHeight = 0;

?>
<?php if($banner_type == 2):?>
<script>window.jQuery || document.write('<script src="<?= URL::get_engine_assets_base(); ?>js/libs/jquery-2.1.4.min.js"><\/script>')</script>
<script src="<?=URL::get_engine_plugin_assets_base('pages')?>js/jquery.innerfade.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        $('#imageContainer').innerfade({
            speed: <?=$bannerSpeed?>,
            timeout: <?=$bannerTimeout?>,
            type: 'sequence',
            containerheight: <?=$bannerHeight?>
        });
    });
</script>
<?php endif;?>

<script type="text/javascript">
    $(document).ready(function(){
        //adjuts .banners height
        var banner_height = $('.banner_0 img').attr('height');
        $('.banners').css('height', banner_height + 'px');
    });
</script>