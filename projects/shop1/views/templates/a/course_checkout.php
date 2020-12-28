<?php include 'template_views/html_document_header.php'; ?>
<body id="<?= $page_data['layout'] ?>" class="<?= $page_data['category'] ?> layout-<?= $page_data['layout'] ?>">
	<div id="container">
		<?php include 'header.php' ?>
		<div id="main">
			<?php if (Settings::instance()->get('column_menu') == TRUE AND Settings::instance()->get('column_menu') == 1): ?>
				<div id="sideLt">
					<div class="panels_lt">
						<?php $products_menu_enabled = (Settings::instance()->get('products_menu') === FALSE OR Settings::instance()->get('products_menu') == 1); ?>
						<?php if ($products_menu_enabled): ?>
							<div class="specials_offers">
								<h1>Products</h1>
							</div>
						<?php endif; ?>
						<div class="products_menu">
							<?php if ( ! $products_menu_enabled): ?>
								<div>
									<?= menuhelper::add_menu_editable_heading('left', 'ul_level_1'); ?>
								</div>
							<?php else: ?>
								<?= Model_Product::render_products_menu(); ?>
							<?php endif; ?>
						</div>
						<?= Model_Panels::get_panels_feed('content_left'); ?>
					</div>
				</div>
			<?php endif; ?>
			<div id="ct">
				<div id="banner">
					<?php echo Model_PageBanner::render_frontend_banners($page_data['banner_photo']); //Helper banners ?>
				</div>

				<div id="ct_left" class="column">
					<?php $alerts = Session::instance()->get('messages'); ?>
					<div id="checkout_messages">
						<?php if ( ! is_null($alerts)): ?>
							<?php foreach( $alerts as $alert): ?>
								<div class="alert">
									<a class="close" data-dismiss="alert">&times;</a>
									<strong><?= ucfirst($alert['type']) ?>:</strong> <?= $alert['content'] ?></div>
							<?php endforeach; ?>
						<?php endif; ?>
						<?php Session::instance()->delete('messages') ?>
					</div>
					<div class="content">
						<?php include 'template_views/course_checkout.php'; ?>
						<?= $page_data['content'] ?>
					</div>
				</div>

			</div>
		</div>
		<div id="footer">
			<?php include 'footer.php' ?>
		</div>
		<?= Settings::instance()->get('footer_html'); ?>
	</div>
</body>
</html>