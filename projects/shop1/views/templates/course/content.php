	<?php include 'template_views/html_document_header.php'; ?>
	<body id="<?= $page_data['layout'] ?>" class="content_layout <?= $page_data['category'] ?>">
		<div class="wrapper">
			<div class="container">

				<?php include 'header.php' ?>

				<div class="main_content_wrapper">
					<div class="main_content">
						<?php $alerts = Session::instance()->get('messages'); ?>
						<section class="banner_section">
							<?= Model_PageBanner::render_frontend_banners($page_data['banner_photo']); ?>
						</section>

						<section class="content_section">
							<?php if ($page_data['name_tag'] == 'contact-us.html' AND isset($_GET['course_id']) AND class_exists('Model_Courses'))
							{
								$course_id = Kohana::sanitize($_GET['course_id']);
								$course = @Model_Courses::get_course($course_id);
								if (isset($course['id']))
								{
									$page_data['content'] = str_replace('_message"></textarea>','_message">Course #'.$course['id'].': '.$course['title']."\n".'</textarea>', $page_data['content']);
								}
							}
							?>
							<?= $page_data['content'] ?>
							<?php
							switch($page_data['name_tag'])
							{
								case 'news.html'         : echo '<h1>News</h1>', Model_News::get_plugin_items_front_end_list($page_data['current_item_identifier'], $page_data['current_item_category']); break;
								case 'testimonials.html' : echo '<div class="content"><h1>Testimonials</h1>'.Model_Testimonials::get_plugin_items_front_end_list($page_data['current_item_identifier'], $page_data['current_item_category']).'</div>'; break;
								case 'checkout.html'     : include 'template_views/checkout.php'; break;
							}
							?>
						</section>
					</div>

					<?php include 'sidebar.php' ?>
				</div>

				<?php include 'footer.php' ?>
			</div>
		</div>
	</body>
</html>