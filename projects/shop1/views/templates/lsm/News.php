<?php include 'template_views/header.php'; ?>

	<div class="row content-columns">
		<?php include 'template_views/sidebar.php'; ?>

		<div class="content_area">
			<div class="page-content"><?= trim($page_data['content']) ?></div>
			<div class="page-content page-content--news"><?= Model_News::get_plugin_items_front_end_list($page_data['current_item_identifier'], $page_data['current_item_category']) ?></div>
		</div>
	</div>

<?php include 'template_views/footer.php'; ?>
