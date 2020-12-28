<?php $logged_in_user = Auth::instance()->get_user(); ?>
<div class="header" id="header">
	<div class="header_logo" id="header_logo">
		<a href="/"><img src="<?= $page_data['logo'] ?>" alt="<?= Settings::instance()->get('company_title') ?>" /></a>
		<?php if(Kohana::$config->load('config')->get('db_id') == 'murphyenterprises'): ?>
			<a href="<?=URL::site();?>contact-us.html">
				<div class="header_get_a_quote"><img src="<?=URL::site()?>assets/<?= $assets_folder_path ?>/images/header_get_a_quote.png"></div>
			</a>
		<?php endif; ?>
	</div>
	<div class="header_contact">
		<p class="header_contact-phone"><?= Settings::instance()->get('telephone'); ?></p>
	</div>
    <p class="header_slogan" id="header_slogan"><?= Settings::instance()->get('company_slogan'); ?></p>
	<div class="main_menu" id="main_menu"><?php menuhelper::add_menu_editable_heading('main')?></div>
</div>

