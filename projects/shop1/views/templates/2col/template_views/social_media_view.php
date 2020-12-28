<!-- Social Media -->
<?php
$addthis_id   = Settings::instance()->get('addthis_id');
$facebook_url = Settings::instance()->get('facebook_url');
$twitter_url  = Settings::instance()->get('twitter_url');
$flickr_url   = Settings::instance()->get('flickr_url');
$linkedin_url = Settings::instance()->get('linkedin_url');

$addthis_url  = 'https://www.addthis.com/bookmark.php?v=250&amp;username='.$addthis_id;

if ($facebook_url != '' AND strpos($facebook_url, 'facebook.com/') == FALSE) {
    $facebook_url = 'https://www.facebook.com/'.$facebook_url;
}
if ($twitter_url != '' AND strpos($twitter_url, 'twitter.com/') == FALSE) {
    $twitter_url = 'http://twitter.com/'.$twitter_url;
}
if ($flickr_url != '' AND strpos($flickr_url, 'flickr.com/') == FALSE) {
    $flickr_url = 'http://flickr.com/photos/'.$flickr_url;
}
?>
<div class="socialmedia">
    <div class="sharethis">
        <?php if ($addthis_id != ''): ?>
            <!-- AddThis Button BEGIN -->
            <div class="addthis_toolbox addthis_default_style">
                <a href="<?=$addthis_url?>" class="addthis_button_compact">Share</a>
                <span class="addthis_separator">&#124;</span>
                <!--<a id="flickr_www" href="">www.<strong style="color: rgb(57, 147, 255);">flick <span style="color: rgb(255, 28, 146);">r</span></strong>.com</a><br/>-->
                <a class="addthis_button_preferred_2"></a>
                <a class="addthis_button_preferred_3"></a>
                <a class="addthis_button_preferred_4"></a>
            </div>
            <script type="text/javascript" src="https://s7.addthis.com/js/250/addthis_widget.js#username=<?=$addthis_url?>"></script>
            <!-- AddThis Button END -->
        <?php endif; ?>
        <?php if ($facebook_url != ''):?>
            <a name="fb_link" type="button_count" href="<?=$facebook_url?>"><img src='<?=URL::site()?>assets/default/images/facebook-icon.png' alt='' width='18' height='18'/></a>
        <?php endif; ?>
        <?php if ($twitter_url != ''):?>
            <a name="twitter_link" type="button_count" href="<?=$twitter_url?>"><img src='<?=URL::site()?>assets/default/images/twitter-icon.png' alt='' width='18' height='18'/></a>
        <?php endif; ?>
        <?php if ($linkedin_url != ''):?>
            <a name="linkedin_link" type="button_count" href="<?=$linkedin_url?>"><img src='<?=URL::site()?>assets/default/images/linkedin-icon.png' alt='' width='18' height='18'/></a>
        <?php endif; ?>
        <?php if ($flickr_url != ''):?>
            <a name="twitter_link" type="button_count" href="<?=$flickr_url?>"><img src='<?=URL::site()?>assets/default/images/flickr-icon.png' alt='' width='18' height='18'/></a>
        <?php endif; ?>

    </div>
</div>
<!-- /Social Media -->