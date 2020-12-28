<?php include 'template_views/html_document_header.php'; ?>
<body id="<?=$page_data['layout']?>" class="<?=$page_data['category']?>">
<div id="wrap">
    <div id="container">
        <?php include 'header.php' ?>
        <div id="main">
            <div id="sideLt">
				<div class="panels_lt">
					<div class="products_menu">
						<?= menuhelper::add_menu_editable_heading('side_menu','ul_level_1'); ?>
					</div>
					<?=Model_Panels::get_panels_feed('content_left');?>
				</div>
            </div>
            <div id="ct">
                <div id="banner">
                    <?php echo Model_PageBanner::render_frontend_banners($page_data['banner_photo']); ?>
                </div>

                <div id="ct_left" class="column">
                    <div id="checkout_messages"></div>
                    <div class="content">

						<?= $page_data['content']; ?>
                        <?php if($page_data['name_tag'] == 'news.html') echo Model_News::get_plugin_items_front_end_list($page_data['current_item_identifier'], $page_data['current_item_category']); ?>
                    </div>
                    <?php if ($page_data['name_tag'] == 'contact-us.html' AND strpos($page_data['content'], '<form ') == FALSE) Model_Formprocessor::contactus(); ?>
                    <?php if ($page_data['name_tag'] == 'testimonials.html') echo '<div class="content"><h1>Testimonials</h1>' . Model_Testimonials::get_plugin_items_front_end_list($page_data['current_item_identifier'], $page_data['current_item_category']) . '</div>';?>
                </div>

               <?php
				$panels      = Model_Panels::get_panels_feed('content_right');
				$news        = Model_News::get_plugin_items_front_end_feed('News');
				$show_panels = (strpos($panels, '<ul') != FALSE);
				$show_news   = ((strpos($news, '<ul') != FALSE AND $page_data['name_tag'] != 'news.html'));
				?>
				<?php if ($show_panels OR $show_news): ?>
					<div id="ct_right" class="column'">
						<div class="panels_lt">
							<?= $show_news   ? $news   : '' ?>
							<?= $show_panels ? $panels : '' ?>
						</div>
					</div>
				<?php endif; ?>

            </div>
        </div>
        <div id="footer">
            <?php include 'footer.php' ?>
        </div>
    </div>
</div>
<?= Settings::instance()->get('footer_html'); ?>
</body>
</html>