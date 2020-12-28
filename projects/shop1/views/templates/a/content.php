<?php include 'template_views/html_document_header.php'; ?>
<body id="<?= $page_data['layout'] ?>" class="<?= $page_data['category'] ?>">
    <div id="container">
        <?php include 'header.php' ?>
        <div id="main">
            <?php if (Settings::instance()->get('column_menu') == TRUE AND Settings::instance()->get('column_menu') == 1): ?>
                <div id="sideLt">
                    <div class="panels_lt">
						<?php $products_menu_enabled = (Settings::instance()->get('products_menu') === FALSE OR Settings::instance()->get('products_menu') == 1); ?>
						<?php if ($products_menu_enabled OR Kohana::$config->load('config')->get('db_id') == 'lionprint'): ?>
							<div class="specials_offers">
								<?php if (Kohana::$config->load('config')->get('db_id') != 'lionprint'): ?>
									<h1>Products</h1>
								<?php else: ?>
									<h1>Quick Links</h1>
								<?php endif; ?>
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
						<?php if ($page_data['name_tag'] == 'contact-us.html' AND isset($_GET['course_id']) AND class_exists('Model_Courses'))
						{
							$course_id = Kohana::sanitize($_GET['course_id']);
							$course = @Model_Courses::get_course($course_id);
							if (isset($course['id']))
							{
								$page_data['content'] = preg_replace('/\<textarea(.*)name="(.*)message"(.*)\>(.*)\<\/textarea\>/','<textarea\1name="\2message"\3>I am interested in hearing more about Course #'.$course['id'].': '.$course['title']."\n".'\4</textarea>', $page_data['content']);
							}
						}
						?>

                        <?= $page_data['content'] ?>
                        <?php
                        /* Some Plugin Specific Content CWill be called Here */
                        //Load News - Data for the News Page
                        if ($page_data['name_tag'] == 'news.html') echo '<h1>News</h1>', Model_News::get_plugin_items_front_end_list($page_data['current_item_identifier'], $page_data['current_item_category']);
                        ?>
                    </div>
					<?php if ($page_data['name_tag'] == 'contact-us.html' AND ( ! strpos($page_data['content'], '<form')) AND  Kohana::$config->load('config')->get('db_id') != 'lionprint') Model_Formprocessor::contactus(); ?>
					<?php if ($page_data['name_tag'] == 'online-returns.html') echo View::factory('/front_end/online_returns_form'); ?>
                    <?php if ($page_data['name_tag'] == 'testimonials.html') echo '<div class="content"><h1>Testimonials</h1>' . Model_Testimonials::get_plugin_items_front_end_list($page_data['current_item_identifier'], $page_data['current_item_category']) . '</div>'; ?>
                </div>

                <?php
                $panels = Model_Panels::get_panels_feed('content_right');
                $news   = (Settings::instance()->get('sidebar_news_feed') == 1 ) ? Model_News::get_plugin_items_front_end_feed('News') : '';
                ?>
                <?php if (strpos($news, '<ul') != FALSE OR strpos($panels, '<ul') != FALSE): ?>
                    <div id="ct_right" class="column'">
                        <div class="panels_lt">
                            <?= $news ?>
                            <?= $panels ?>
                        </div>
                    </div>
                <?php endif; ?>

            </div>
        </div>
		<div id="footer">
			<?php include 'footer.php' ?>
		</div>
        <?= Settings::instance()->get('footer_html'); ?>
</body></html>