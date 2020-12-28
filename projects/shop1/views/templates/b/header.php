<link href="<?=URL::site()?>assets/<?= $assets_folder_path ?>/css/bootstrap.css" rel="stylesheet" />
<?php $logged_in_user = Auth::instance()->get_user(); ?>
<div id="header">
	<!--<span class="head-links"><a href="#">LOGIN</a><a href="#">REGISTER</a></span>-->
    <?php if (Settings::instance()->get('product_enquiry') != 1): ?>
        <span id="mini_cart_wrapper">
			<?php include "mini_cart.php"; ?>
		</span>
		<?php if (class_exists('Model_Currency') AND count(Model_Currency::getRates()) > 0): ?>
			<script src="<?=Url::get_engine_plugin_assets_base('currency')?>js/currency.js"></script>
			<span class="change-currency">
                <span>Currency </span><?=Model_Currency::getCurrencySelector()?>
				<span class="glyphicon glyphicon-chevron-down"></span>
            </span>
		<?php endif; ?>
    <?php endif; ?>

	<div id="header_logo">
		<a href="/"><img src="<?= $page_data['logo'] ?>" alt="<?= Settings::instance()->get('company_title') ?>" /></a>
		<?php if(Kohana::$config->load('config')->get('db_id') == 'murphyenterprises'): ?>
			<a href="<?=URL::site();?>contact-us.html">
				<div class="header_get_a_quote"><img src="<?=URL::site()?>assets/<?= $assets_folder_path ?>/images/header_get_a_quote.png"></div>
			</a>
		<?php endif; ?>
		<div id="header_slogan"><?= Settings::instance()->get('company_slogan'); ?></div>
		<div class="nav-area">
            <div id="main_menu"><?php menuhelper::add_menu_editable_heading('main')?></div>
			<?php if (Settings::instance()->get('product_search_bar') == 'TRUE'): ?>
				<div id="product_searchbar_wrapper" class="product_searchbar_wrapper">
					<label for="product_searchbar">Search</label>
					<input id="product_searchbar"class="product_searchbar" type="text" placeholder="Search products ..."/>
				</div>
			<?php endif; ?>
			<?php 
				$facebook_url = Settings::instance()->get('facebook_url');
				if ($facebook_url.Settings::instance()->get('twitter_url') != '')
				{
					$social_media = TRUE;
				}
				else
				{
					$social_media = FALSE;
				}
            ?>
			 <?php if ($social_media): ?>
            <div class="connect_with_us_wrapper">
                <div>
                    <?php if ($facebook_url != '') : ?>
                        <span><a target="_blank" href="https://www.facebook.com/<?= $facebook_url ?>"><img src="/assets/<?= $assets_folder_path ?>/images/facebook_icon.png" /></a></span>
                    <?php endif; ?>
					<?php if (Settings::instance()->get('googleplus_url') != ''): ?>
						<span><a target="_blank" href="https://plus.google.com/<?= Settings::instance()->get('googleplus_url') ?>"><img src="/assets/<?= $assets_folder_path ?>/images/googleplus_icon.png" /></a></span>
					<?php endif; ?>
                    <?php if (Settings::instance()->get('twitter_url') != '') : ?>
                        <span><a target="_blank" href="https://twitter.com/<?= Settings::instance()->get('twitter_url') ?>"><img src="/assets/<?= $assets_folder_path ?>/images/twitter_icon.png" /></a></span>
                    <?php endif; ?>
                    <?php if (Settings::instance()->get('linkedin_url') != '') : ?>
                        <span><a target="_blank" href="https://linkedin.com/<?= Settings::instance()->get('linkedin_url') ?>"><img src="/assets/<?= $assets_folder_path ?>/images/linkedin_icon.png" /></a></span>
                    <?php endif; ?>
                    <?php if (Settings::instance()->get('flickr_url') != '') : ?>
                        <span><a target="_blank" href="https://www.flickr.com/photos/<?= Settings::instance()->get('flickr_url') ?>"><img src="/assets/<?= $assets_folder_path ?>/images/flickr_icon.png" /></a></span>
                    <?php endif; ?>
                    <?= Settings::instance()->get('pinterest_button'); ?>
                </div>
            </div>
        <?php endif; ?>
		</div>
	 </div>
	<?php if (Settings::instance()->get('header_contact_details') == 1): ?>
		<?php
		$telephone = Settings::instance()->get('telephone');
		$mobile    = Settings::instance()->get('mobile');
		$email     = Settings::instance()->get('email');
		$fax       = Settings::instance()->get('fax');

		$show_need_help_button = (Settings::instance()->get('show_need_help_button') == 1);
		$show_donate_button    = (Settings::instance()->get('show_donate_button')    == 1);
		?>
		<div class="header_contact">
			<p class="header-contact-companytitle"><?= Settings::instance()->get('company_title') ?></p>
			<?php if ($telephone.$email.$fax != ''): ?>
				<dl>
					<?php if ($telephone != ''): ?>
						<dt class="header-contact-telephone-label">Tel</dt><dd class="header-contact-telephone"> <?= $telephone ?></dd>
					<?php endif; ?>
					<?php if ($mobile != ''): ?>
						<dt class="header-contact-mobile-label" hidden>Tel</dt><dd class="header-contact-mobile" hidden> <?= $mobile ?></dd>
					<?php endif; ?>
					<?php if ($fax != ''): ?>
						<dt class="header-contact-fax-label">Fax</dt><dd class="header-contact-fax"> <?= $fax ?></dd>
					<?php endif; ?>
					<?php if ($email != ''): ?>
						<dt class="header-contact-email-label">E-Mail</dt><dd class="header-contact-email"> <?= $email ?></dd>
					<?php endif; ?>
				</dl>
			<?php endif; ?>
			<a href="/contact-us.html" class="secondary_button header_callback_button" id="header_callback_button">Contact Us</a>
			<?php if ($show_need_help_button OR $show_donate_button): ?>
				<div class="header-buttons">
					<?php if ($show_need_help_button): ?>
						<?php $help_link = Settings::instance()->get('need_help_page'); ?>
						<a href="/<?= Model_Pages::get_page_by_id($help_link) ?>" class="header-button need-help-button">Need Help?</a>
					<?php endif; ?>
					<?php if ($show_donate_button): ?>
						<?php $donation_link = Settings::instance()->get('donation_page'); ?>
						<a href="/<?= Model_Pages::get_page_by_id($donation_link) ?>" class="header-button donation-button">Donate Now</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	<?php endif; ?>
   
	
</div>

