<?php include 'template_views/header.php' ?>
	<div class="products">
		<?php
		if ($page_data['name_tag'] == 'checkout.html')
		{
			echo Model_Product::render_checkout_html();
		}
		else
		{
			if ( ! isset($page_data['current_item_category']) OR (isset($page_data['current_item_category']) AND trim($page_data['current_item_category']) == ''))
			{
				echo Model_Product::render_products_category_html();
			}
			// Render Product Category
			if (isset($page_data['current_item_category']) AND $page_data['current_item_category'] != 'product_details')
			{
				echo Model_Product::render_products_list_html(8);
			}
		}
		?>

	</div>
<?php include 'template_views/footer.php' ?>