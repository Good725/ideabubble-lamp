<?
$address1        = Settings::instance()->get('addres_line_1');
$address2        = Settings::instance()->get('addres_line_2');
$address3        = Settings::instance()->get('addres_line_3');
$telephone       = Settings::instance()->get('telephone');
$email           = Settings::instance()->get('email');
$company_slogan  = Settings::instance()->get('company_slogan');
?>
<div id="header-right">
	<?php if ($assets_folder_path == '18'): // todo: replace with setting or layout builder ?>
		<div class="company_slogan"><?= Settings::instance()->get('company_slogan'); ?></div>
	<?php else: ?>
		<div id="top">
			<?php if (Settings::instance()->get('company_title')): ?>
				<p class="header-company-title"><?= Settings::instance()->get('company_title').(($address1 != '') ? '<span class="header-address-line">, '.$address1.'.</span>' : '') ?></p>
			<?php endif; ?>
			<?php if ($telephone.$email != ''): ?>
				<p>
					<?= ($telephone != '') ? '<span class="header_telephone_label">TEL:</span>'.$telephone : '' ?><i class="contact-divider"></i>
					<?= ($email     != '') ? '<span class="header_email_label">E-Mail: </span>'.$email : '' ?>
				</p>
			<?php endif; ?>

			<?php include 'social_media_group.php'; ?>
		</div>

		<div id="bottom">
			<?php if (Kohana::$config->load('config')->get('db_id') == 'lsomusic'): ?>
				<a id="book_now" href="https://vecweb.vecnet.ie/web_musiclimerickcity/webmusic/webbookmusic.html?loccode=lsom" target="_blank">
					<img alt="Apply Now" src="<?= URL::get_skin_urlpath(TRUE) ?>/images/applynow.png" />
				</a>
			<?php elseif(file_exists(DOCROOT.'/assets/'.$assets_folder_path.'/images/applynow.png')): ?>
				<a id="book_now" href="contact-us.html">
					<img alt="Make a Booking" src="<?= URL::get_skin_urlpath(TRUE) ?>/images/applynow.png" />
				</a>
			<?php endif; ?>
		</div>
	<?php endif; ?>
</div>

