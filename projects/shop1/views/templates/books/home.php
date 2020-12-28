<?php include 'template_views/header.php' ?>
	<div class="featured-products-list">
		<?= Model_Product::render_featured_products_html() ?>
	</div>
	<form id="ProductDetailsForm"></form>
	<script type="text/javascript" src="/engine/plugins/products/js/front_end/product_details.js"></script>
<?php include 'template_views/footer.php' ?>