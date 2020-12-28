
<?php include 'template_views/html_document_header.php'; ?>
<?php
switch ($page_data['name_tag'])
{
	case 'news.html':
		$extra_content = Model_News::get_plugin_items_front_end_list($page_data['current_item_identifier'], $page_data['current_item_category']);
		break;
	case 'products.html':
		$extra_content = '';
		break;
	case 'checkout.html':
		$extra_content = Model_Product::render_checkout_html();
		break;
	case 'contact-us.html':
		$extra_content = Model_Formprocessor::contactus();
		break;
	case 'loyalty-registration-form.html':
		$extra_content = request::factory('/frontend/shop1/render_registration_html')->execute();
		break;
	case 'members-area.html':
		$extra_content = request::factory('/frontend/shop1/render_members_area_html')->execute();
		break;
	case 'login.html':
		$extra_content = request::factory('/frontend/shop1/login_html')->execute();
		break;
	case 'online-returns.html':
		$extra_content = View::factory('/front_end/online_returns_form');
		break;
	default:
		$extra_content = '';
}
?>

<body id="<?= $page_data['layout'] ?>"
	  class="<?= $page_data['category'] ?> pagename-<?= str_replace('.html', '', $page_data['name_tag']) ?>">
<div id="wrap">
	<div id="container">
		<?php include 'header.php' ?>
		<div class="banner">
				<img src="<?= URL::site() ?>assets/<?= $assets_folder_path ?>/images/header-bg.png" alt="banner-img"/>
		</div>
		<div id="main">

			<?php if (Settings::instance()->get('main_menu_products') == 1): ?>
				
					<div class="content">
						<?= $page_data['content'] ?>
					</div> 

					<?php
					if ($page_data['id'] == Settings::instance()->get('products_plugin_page'))
					{
						if (!empty($page_data['current_item_category']) AND !($page_data['current_item_category'] == 'product_details'))
						{
							$extra_content .= '<div class="products">'.Model_Product::render_products_list_html().'</div>';
						}
						if (empty($page_data['current_item_category']))
						{
							$extra_content .= '<div class="products">'.Model_Product::render_products_category_html(FALSE).'</div>';
						}
					}
					?>
					<?= $extra_content ?>
			
			<?php else: ?>
				
					<div class="clear"></div>
					<div class="content">
						<?= $page_data['content'] ?>
					</div>
					<?php
					if ($page_data['id'] == Settings::instance()->get('products_plugin_page'))
					{
						if (!empty($page_data['current_item_category']) AND !($page_data['current_item_category'] == 'product_details'))
						{
							$extra_content .= '<div class="products">'.Model_Product::render_products_list_html().'</div>';
						}
						if (empty($page_data['current_item_category']))
						{
							$extra_content .= '<div class="products">'.Model_Product::render_products_category_html(FALSE).'</div>';
						}
					}
					?>
					<?= $extra_content ?>

					<?php if (trim($page_data['footer'])): ?>
						<div class="page-footer"><?= $page_data['footer'] ?></div>
					<?php endif; ?>
		
			<?php endif; ?>

		</div>
		<div id="footer">
			<?php include 'footer.php' ?>
		</div>
	</div>
</div>

<?= Settings::instance()->get('footer_html'); ?>
</body>
</html>
