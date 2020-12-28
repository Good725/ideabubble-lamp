<?php $show_sidebar = isset($show_sidebar) ? $show_sidebar : TRUE ?>
<?php include 'template_views/html_document_header.php' ?>
<body id="Page-<?= str_replace('.html', '', $page_data['name_tag']) ?>" class="<?= $page_data['layout'] ?>_layout">
	<div id="wrapper">
		<div id="page">
			<div id="container">
				<?php include 'template_views/header.php' ?>

				<div id="banner"><?= trim(Model_PageBanner::render_frontend_banners($page_data['banner_photo'])) ?></div>

				<!-- main area -->
				<div id="iner-main">
					<div class="ct<?= $show_sidebar ? '' : ' no_right'?>">
						<?= trim($page_data['content']) ?>
						<?php switch ($page_data['name_tag'])
						{
							case 'news.html':
								echo Model_News::get_plugin_items_front_end_list($page_data['current_item_identifier'], $page_data['current_item_category']);
								break;
							case 'testimonials.html':
								echo Model_Testimonials::get_plugin_items_front_end_list($page_data['current_item_identifier'], $page_data['current_item_category']);
								break;
						}
						?>
					</div>

					<?php if ($show_sidebar): ?>
						<div class="sidert">
							<div id="content_panels" class="content_panels"><?= Model_Panels::get_panels_feed('content_right'); ?></div>
						</div>
					<?php endif; ?>
				</div>
				<!-- /main area -->

				<?php include 'template_views/footer.php' ?>
			</div>
		</div>
	</div>
</body>
<?php include 'template_views/html_document_footer.php' ?>