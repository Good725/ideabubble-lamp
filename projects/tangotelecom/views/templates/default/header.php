<div class="header">
	<div class="header_img">
		<div class="nav_bg">
			<div class="menu_icon"><a href="#"><img src="<?=URL::site()?>assets/<?= $assets_folder_path ?>/images/menu-img.png" width="30" height="20" alt="menu"></a></div>
			<div class="navigation nav">
				<?php menuhelper::add_menu_editable_heading('main') ?>
			</div>
		</div>

		<?= Model_PageBanner::render_frontend_banners($page_data['banner_photo'], FALSE) ?>
	</div>
</div>