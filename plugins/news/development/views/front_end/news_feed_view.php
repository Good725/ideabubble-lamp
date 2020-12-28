<?php
$feed = new Model_Feeds;
$display = $feed->display_feed(__FILE__);
?>

<?php if ($display): ?>
    <div id="latest-news">
        <h1>Latest News</h1>
        <ul id="slider1">
            <?= $feed_items ?>
        </ul>
    </div>
    <?php if ($animation_type != 'fixed'): ?>
        <script type="text/javascript" src="<?= URL::get_engine_plugin_assets_base('pages')?>sliders/bxslider/jquery.bxslider.js"></script>
        <script type="text/javascript">
            jQuery(function(){
                $('#slider1').bxSlider({mode: '<?= $animation_type ?>', speed: '<?= $timeout ?>', auto: true, controls: true});
            });
        </script>
    <?php endif; ?>
<?php endif; ?>
