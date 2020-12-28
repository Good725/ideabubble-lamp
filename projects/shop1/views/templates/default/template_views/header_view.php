<!-- Header -->
<div id="header" class="left">
     <div class="logo left">
		 <a href="<?=URL::site();?>">
			 <img src="<?= $page_data['logo'] ?>" alt="<?= Settings::instance()->get('company_title') ?>" title="<?= Settings::instance()->get('company_title') ?>" />
		 </a>
	 </div>
     <div class="ct left"></div>
     <div class="rt left" id="mini_cart_wrapper">
		 <?php echo Model_Product::render_minicart_view(); ?>
     </div>
</div>
<!-- /Header -->
