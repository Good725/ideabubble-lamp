<!--<div id="banner">
<?php /*echo Model_PageBanner::render_frontend_banners($page_data['banner_photo']); */?>
</div>
<?php /*include 'template_views/products_featured_view.php'; */?>
<div class="left successful message_area" style="display:none;"></div>
<div class="clear left"></div>-->
<div class="products">
	<div class="product-title">FEATURED PRODUCT &#187;</div>
	<? for($i=0; $i<12; $i++) { ?>
		<div class="product">
			<div class="product-img"></div>
			<span class="product-info">Product Description to go in here, enough  room for approx 3/4 lines of text.</span>
			<span class="product-price orange">$000.00</span>
			<span class="product-excl">Excl. VAT: <span class="orange">$000.00</span></span>
			<a href="" class="product-buy">Buy now</a>
		</div>
	<? } ?>
</div>