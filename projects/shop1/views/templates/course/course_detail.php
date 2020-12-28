	<?php include 'template_views/html_document_header.php'; ?>
	<body id="<?= $page_data['layout'] ?>" class="course_layout <?= $page_data['category'] ?>">
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
							<?= $page_data['content'] ?>
							<?php include 'template_views/course_detail_snippet.php'; ?>
							<?= $page_data['content'] ?>
						</section>
					</div>

					<?php include 'sidebar.php' ?>
				</div>

				<?php include 'footer.php' ?>
			</div>
		</div>
	</body>
</html>