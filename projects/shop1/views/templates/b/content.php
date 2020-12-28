<?php include 'template_views/html_document_header.php'; ?>
<body id="<?=$page_data['layout']?>" class="<?=$page_data['category']?>">
<div id="wrap">
    <div id="container">
        <?php include 'header.php' ?>
        <div id="main">
            <div id="sideLt">
				<?php if (Settings::instance()->get('main_menu_products') == 1): ?>
					<div class="panels_lt">
						<?= Model_Panels::get_panels_feed('home_left'); ?>
					</div>
				<?php else: ?>
					<div class="panels_lt">
						<?php if (Settings::instance()->get('show_submenu_in_sidebar') == 1): ?>
							<?php $submenu = Menuhelper::get_submenus_for_page($page_data['id'], 'main'); ?>
						<?php endif; ?>
						<?php if (isset($submenu) AND count($submenu) > 0): ?>
							<div class="products_menu sidebar-content-menu">
								<h3><?= $page_data['title'] ?></h3>
								<ul class="ul_level_1">
									<?php foreach ($submenu as $menu_item): ?>
										<li class="li_level_1<?= ($page_data['id'] == $menu_item['page_id']) ? ' current' : '' ?>">
											<a href="<?= ($menu_item['page_url'] != '') ? '/'.$menu_item['page_url'] : $menu_item['link_url'] ?>"><?= $menu_item['title'] ?></a>
										</li>
									<?php endforeach; ?>
								</ul>
							</div>
						<?php else: ?>
							<?php $show_products = (Settings::instance()->get('products_menu') === FALSE OR Settings::instance()->get('products_menu') == 1) ?>
							<?php if ($show_products): ?>
								<div class="specials_offers"><h1>PRODUCTS</h1></div>
							<?php endif; ?>
							<div class="products_menu">
								<?php if ( ! $show_products): ?>
									<div>
										<?= menuhelper::add_menu_editable_heading('left','ul_level_1'); ?>
									</div>
								<?php else: ?>
									<?= Model_Product::render_products_menu(); ?>
								<?php endif; ?>
							</div>
						<?php endif; ?>
						<?=Model_Panels::get_panels_feed('content_left');?>
					</div>

				<?php endif; ?>
            </div>
            <div id="ct">
                <div id="banner">
                    <?php echo Model_PageBanner::render_frontend_banners($page_data['banner_photo']);  //Helper banners ?>
                </div>

                <div id="ct_left" class="column">
                    <div id="checkout_messages"></div>
                    <div class="content">
						<?= $page_data['content']; ?>
                        <?php
                        /* Some Plugin Specific Content Will be called Here */
                        //Load News - Data for the News Page
                        if($page_data['name_tag'] == 'news.html') echo Model_News::get_plugin_items_front_end_list($page_data['current_item_identifier'], $page_data['current_item_category']);
                        ?>
						<?php if (trim($page_data['footer'])): ?>
							<div class="page-footer"><?= $page_data['footer'] ?></div>
						<?php endif; ?>
                    </div>
                    <?php if ($page_data['name_tag'] == 'contact-us.html' AND strpos($page_data['content'], '<form ') == FALSE) Model_Formprocessor::contactus(); ?>
					<?php if ($page_data['name_tag'] == 'online-returns.html') echo View::factory('/front_end/online_returns_form'); ?>
                    <?php if ($page_data['name_tag'] == 'testimonials.html') echo '<div class="content"><h1>Testimonials</h1>' . Model_Testimonials::get_plugin_items_front_end_list($page_data['current_item_identifier'], $page_data['current_item_category']) . '</div>';?>
                </div>

               <?php
				$panels      = Model_Panels::get_panels_feed('content_right');
				$news        = (Settings::instance()->get('sidebar_news_feed') == 1) ? Model_News::get_plugin_items_front_end_feed('News') : '';
				$show_panels = (strpos($panels, '<ul') != FALSE);
				$show_news   = (strpos($news, '<ul') != FALSE AND $page_data['name_tag'] != 'news.html');
				?>
				<?php if ($show_panels OR $show_news): ?>
					<div id="ct_right" class="column side-column">
						<div>
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