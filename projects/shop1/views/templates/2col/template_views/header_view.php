<!-- Header -->
<?php
$telephone = Settings::instance()->get('telephone');
$product_enquiry = Settings::instance()->get('product_enquiry');
$logo = Settings::instance()->get('site_logo');
$logo = ($logo != '') ? $logo : '';
?>
<div id="header" class="left<?= ($product_enquiry == 1) ? ' enquiry' : ''?>">
     <div class="logo left">
		 <a href="<?=URL::site();?>">
			 <img src="<?= $page_data['logo'] ?>" alt="<?= Settings::instance()->get('company_title') ?>" title="<?= Settings::instance()->get('company_title') ?>" />
		 </a>
	 </div>
     <div class="ct left"></div>
     <?php if ($product_enquiry == 'TRUE'): ?>
         <div class="rt right" id="call_options_wrapper">
             <div id="call_options">
                <a href="/request-a-callback.html"><div id="callback_btn">Request a call back</div></a>
                <?php if ($telephone != ''): ?>
                    <a href="/contact-us.html"><div id="call_us_btn">Call us: <?=$telephone?></div></a>
                <?php endif; ?>
             </div>
         </div>
     <?php else: ?>
        <div class="rt left" id="mini_cart_wrapper">
		    <?php echo Model_Product::render_minicart_view(); ?>
        </div>
    <?php endif; ?>
    <div class="right margin-left-10"><?=(strpos($_SERVER['HTTP_HOST'],'quinlanireland') !== false) ? '<img src="'.URL::site().'assets/02/images/header_right_badge.png"/>':'';?></div>
</div>
<!-- /Header -->
