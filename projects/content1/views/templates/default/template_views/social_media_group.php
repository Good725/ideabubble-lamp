<?php
$addthis_id      = Settings::instance()->get('addthis_id');
$facebook_url    = Settings::instance()->get('facebook_url');
$twitter_url     = Settings::instance()->get('twitter_url');
$flickr_url      = Settings::instance()->get('flickr_url');
$linkedin_url    = Settings::instance()->get('linkedin_url');
$googleplus_url  = Settings::instance()->get('googleplus_url');
$image_file_path = DOCROOT.'/assets/'.$assets_folder_path.'/images/';

if ($facebook_url != '' AND strpos($facebook_url, 'facebook.com/') == FALSE)
{
	$facebook_url = 'https://www.facebook.com/'.$facebook_url;
}
if ($twitter_url != '' AND strpos($twitter_url, 'twitter.com/') == FALSE)
{
	$twitter_url = 'http://twitter.com/'.$twitter_url;
}
if ($flickr_url != '' AND strpos($flickr_url, 'flickr.com/') == FALSE)
{
	$flickr_url = 'http://flickr.com/photos/'.$flickr_url;
}
if ($linkedin_url != '' AND strpos($linkedin_url, 'linkedin.com/') == FALSE)
{
	$linkedin_url = 'https://linkedin.com/in/'.$linkedin_url;
}
if ($googleplus_url != '' AND strpos($googleplus_url, 'plus.google.com/') == FALSE)
{
	$googleplus_url = 'https://plus.google.com/'.$googleplus_url;
}
?>
<?php if ($facebook_url.$twitter_url.$googleplus_url.$linkedin_url != ''): ?>
	<div class="social_media_group">
		<?php if (file_exists($image_file_path.'email_icon.png')): ?>
			<a href="/contact-us.html" class="social_media_group_icon social_media_group_icon-email">
				<img src="<?= URL::get_skin_urlpath(TRUE) ?>/images/email_icon.png" />
			</a>
		<?php endif; ?>

		<?php if ($facebook_url != '' AND file_exists($image_file_path.'facebook_icon.png')): ?>
			<a href="<?= $facebook_url ?>" class="social_media_group_icon social_media_group_icon-facebook">
				<img src="<?= URL::get_skin_urlpath(TRUE) ?>/images/facebook_icon.png" />
			</a>
		<?php endif; ?>

		<?php if ($twitter_url != '' AND file_exists($image_file_path.'twitter_icon.png')): ?>
			<a href="<?= $twitter_url ?>" class="social_media_group_icon social_media_group_icon-twitter">
				<img src="<?= URL::get_skin_urlpath(TRUE) ?>/images/twitter_icon.png" />
			</a>
		<?php endif; ?>

		<?php if ($googleplus_url != '' AND file_exists($image_file_path.'googleplus_icon.png')): ?>
			<a href="<?= $googleplus_url ?>" class="social_media_group_icon social_media_group_icon-googleplus">
				<img src="<?= URL::get_skin_urlpath(TRUE) ?>/images/googleplus_icon.png" />
			</a>
		<?php endif; ?>

		<?php if ($linkedin_url != '' AND file_exists($image_file_path.'linkedin_icon.png')): ?>
			<a href="<?= $linkedin_url ?>" class="social_media_group_icon social_media_group_icon-linkedin">
				<img src="<?= URL::get_skin_urlpath(TRUE) ?>/images/linkedin_icon.png" />
			</a>
		<?php endif; ?>

		<?php if ($flickr_url != '' AND file_exists($image_file_path.'flickr_icon.png')): ?>
			<a href="<?= $flickr_url ?>" class="social_media_group_icon social_media_group_icon-flickr">
				<img src="<?= URL::get_skin_urlpath(TRUE) ?>/images/flickr_icon.png" />
			</a>
		<?php endif; ?>
	</div>
<?php endif; ?>