<?php $logged_in_user = Auth::instance()->get_user(); ?>
<div id="header">
    <?php if (Settings::instance()->get('product_enquiry') != 1): ?>
        <span id="mini_cart_wrapper"><?php include "mini_cart.php"; ?></span>
    <?php endif; ?>
	<div id="header_logo">
		<a href="/"><img src="<?= $page_data['logo'] ?>" alt="<?= Settings::instance()->get('company_title') ?>" /></a>
		<?php if(Kohana::$config->load('config')->get('db_id') == 'murphyenterprises'): ?>
			<a href="<?=URL::site();?>contact-us.html">
				<div class="header_get_a_quote"><img src="<?=URL::site()?>assets/<?= $assets_folder_path ?>/images/header_get_a_quote.png"></div>
			</a>
		<?php endif; ?>
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
    <div id="header_slogan"><?= Settings::instance()->get('company_slogan'); ?></div>
    <?php if (Settings::instance()->get('product_search_bar') == 'TRUE'): ?>
        <div id="product_searchbar_wrapper" class="product_searchbar_wrapper">
            <label for="product_searchbar">Search</label>
            <input id="product_searchbar"class="product_searchbar" type="text" placeholder="Search products ..."/>
        </div>
    <?php endif; ?>
	<div id="main_menu"><?php menuhelper::add_menu_editable_heading('main')?></div>
</div>

