<?php if ($settings->get('facebook_url').$settings->get('twitter_url').$settings->get('linkedin_url').$settings->get('flickr_url').$settings->get('pinterest_button') != ''): ?>
	<?php
	$facebook_url = $settings->get('facebook_url');
	$twitter_url  = $settings->get('twitter_url');
	$flickr_url   = $settings->get('flickr_url');
	$linkedin_url = $settings->get('linkedin_url');

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

	<div class="social_icons">
		<h3>Follow Us</h3>
		<ul>
			<?php if ($facebook_url != ''): ?>
				<li><a href="<?= $facebook_url ?>"><img src="<?=URL::site()?>assets/<?= $assets_folder_path ?>/images/fb.png" width="27" height="25" alt="fb_icon"></a></li>
			<?php endif; ?>
			<?php if ($twitter_url != ''): ?>
				<li><a href="<?= $twitter_url ?>"><img src="<?=URL::site()?>assets/<?= $assets_folder_path ?>/images/twitter.png" width="27" height="25" alt="twitter"></a></li>
			<?php endif; ?>
			<?php if ($linkedin_url != ''): ?>
				<li><a href="<?= $linkedin_url ?>"><img src="<?=URL::site()?>assets/<?= $assets_folder_path ?>/images/in.png" width="23" height="25"  alt="in_img"></a></li>
			<?php endif; ?>
		</ul>
	</div>
<?php endif; ?>